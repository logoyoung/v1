<?php

ini_set('memory_limit', '512M');
require_once __DIR__ . '/../../include/init.php';

use system\Timer;
use service\live\helper\LiveLengthRedis;
use lib\live\LiveLength;
use lib\anchor\AnchorMostPopular;

/**
 *  算出主播上个月直播时长等并计入缓存
 * @date 2017-08-24 10:36:25
 * @author longgang <longgang@6.cn>
 * @copyright (c) 2017, longgang
 * @version 1.0.1
 */
class lastMonthAnchorLiveLength
{

    //每月直播任务时长  单位秒
    const PER_TASK_LIVE_TIME = 360000;
    //直播有效天数最小每天直播时间 单位秒
    const PER_MIN_EFFECTIVE_TIME = 3600;
    //缓存保存31天 单位秒
    const CACHE_31_DAYS = 2678400;
    //截取数据每份200条
    const PER_COUNT = 200;

    private $_logName = 'last_month_anchor_live_length_redis';

    private function _setAnchorLastLiveLengthRedis()
    {
        ####上个月####
        $dates = $this->getDates();

        $this->log("start|{$dates['last']}至{$dates['cur']} 直播时长信息写入缓存开始");
        $timer = new Timer();
        $timer->start();
        
        
        $liveLengthDao = $this->getLiveLengthDao();
          
        $anchorMostPopular = $this->getAnchorMostPopularDao();
        
        $liveLengthRedis = new LiveLengthRedis();

        $total = $liveLengthDao->getAllUidCount($dates['last'], $dates['cur']);
        
        if(!$total)
        {
            $this->log('error|从数据库获取时间内主播数异常');
            die(-1);
        }
        
        $totalPage = ceil($total / self::PER_COUNT);
        $page = 1;
        
        while ( $totalPage >= 0)
        {
            $luids = $liveLengthDao->getAllUid($dates['last'], $dates['cur'], $page, self::PER_COUNT);
            if(!$luids)
            {
                $this->log('error|从数据库获取主播id异常');
                die(-1);
            }
            
            $datas = [];

            foreach ($luids as $luid)
            {
                $data = [];
                $lengths = $liveLengthDao->getAnchorLiveLengths($luid, $dates['last'], $dates['cur']);
                
                if(!$lengths)
                {
                    $this->log("notice|从数据库获取主播{$luid}直播时长异常");
                    continue;
                }
                //时间内直播时长
                $data['monthLength'] = array_sum($lengths);
                
                //剩余直播时长
                $data['noLiveLength'] = self::PER_TASK_LIVE_TIME - $data['monthLength'] > 0 ? self::PER_TASK_LIVE_TIME - $data['monthLength'] : 0;
                
                //有效天数
                $effectDays = 0;
                foreach ($lengths as $length)
                {
                    if($length >= self::PER_MIN_EFFECTIVE_TIME)
                    {
                        $effectDays++;
                    }
                }
                $data['effectiveDays'] = $effectDays;
                
                //人气峰值
                $data['popuPeak'] = $anchorMostPopular->getLivePopularyPeak($luid, $dates['last'], $dates['cur']);
                
                $datas[$luid] = hp_json_encode(array_values_to_string($data));
            }
            
            if (!$datas)
            {
                $this->log('从数据库获取主播时长异常!');
                die(-1);
            }
            
            $liveLengthRedis->setAnchorLiveLength($dates['last'], $datas,self::CACHE_31_DAYS);
                        
            $this->log("{$dates['last']}至{$dates['cur']}主播时长记入缓存成功!luids:" . var_export($luids,true));

            $page ++;
            $totalPage --;
        }
    }

    //要存储数据的月份
    public function getDates()
    {
        $dates = [];
        $dates['cur'] = date('Y-m-01', time());
        $dates['today'] = date('Y-m-d', time());
        $lastMonthDays = getLastMonthDays($dates['cur']);
        $dates['last'] = $lastMonthDays[0];

        return $dates;
    }

    public function run()
    {
        $this->_setAnchorLastLiveLengthRedis();
    }

    public function getLiveLengthDao()
    {
        return new LiveLength();
    }
    
    public function getAnchorMostPopularDao()
    {
        return new AnchorMostPopular();
    }

    public function log($msg)
    {
        return write_log($msg, $this->_logName);
    }

}

$obj = new lastMonthAnchorLiveLength();

$obj->run();

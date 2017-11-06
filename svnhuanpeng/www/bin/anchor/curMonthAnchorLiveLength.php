<?php

ini_set('memory_limit', '512M');
require_once __DIR__ . '/../../include/init.php';

use system\Timer;
use service\live\helper\LiveLengthRedis;
use lib\live\LiveLength;
use lib\anchor\AnchorMostPopular;

/**
 *  算出主播当前月直播时长等并计入缓存
 * @date 2017-08-24 10:36:25
 * @author longgang <longgang@6.cn>
 * @copyright (c) 2017, longgang
 * @version 1.0.1
 */
class curMonthAnchorLiveLength
{

    //每月直播任务时长  单位秒
    const PER_TASK_LIVE_TIME = 360000;
    //直播有效天数最小每天直播时间 单位秒
    const PER_MIN_EFFECTIVE_TIME = 3600;
    //截取数据每份200条
    const PER_COUNT = 200;

    private $_logName = 'cur_month_anchor_live_length_redis';

    private function _setAnchorCurLiveLengthRedis()
    {
        ####本月####
        $dates = $this->getDates();

        $this->log("start|{$dates['today']} 直播时长信息写入缓存开始");
        $timer = new Timer();
        $timer->start();

        $cacheTime = strtotime(date('Y-m-t 23:59:59', time())) - time(); 
        
        $liveLengthDao = $this->getLiveLengthDao();
          
        $anchorMostPopular = $this->getAnchorMostPopularDao();
        
        $liveLengthRedis = new LiveLengthRedis();

        $total = $liveLengthDao->getAnchorDayLiveLengthsCount($dates['today']);
        
        if(!$total)
        {
            $this->log('error|从数据库获取时间内主播数异常');
            die(-1);
        }
        
        $totalPage = ceil($total / self::PER_COUNT);
        $page = 1;
        
        while ( $totalPage >= 0)
        {
            $luids = $liveLengthDao->getDayUid($dates['today'], $page, self::PER_COUNT);
            if(!$luids)
            {
                $this->log('error|从数据库获取主播id异常');
                die(-1);
            }
            
            $datas = [];

            foreach ($luids as $luid)
            {
                $data = [];
                $lengths = $liveLengthDao->getAnchorLiveLengths($luid, $dates['cur'],$dates['next']);
                
                if(!$lengths)
                {
                    $this->log("notice|从数据库获取主播{$luid}直播时长异常");
                    continue;
                }
                //时间内直播时长
                $data['monthLength'] = array_sum($lengths);
                
                //剩余直播时长
                $data['noLiveLength'] = self::PER_TASK_LIVE_TIME - $data['monthLength'] > 0 ? self::PER_TASK_LIVE_TIME - $data['monthLength'] : 0;
                
                //当天直播时长
                $data['dayLength'] = $liveLengthDao->getAnchorDayLiveLength($luid, $dates['today']);
                        
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
                $data['popuPeak'] = $anchorMostPopular->getLivePopularyPeak($luid, $dates['cur'], $dates['next']);
                
                $datas[$luid] = hp_json_encode(array_values_to_string($data));
            }
            
            if (!$datas)
            {
                $this->log('从数据库获取主播时长异常!');
                die(-1);
            }
            
            $liveLengthRedis->setAnchorLiveLength($dates['cur'], $datas,$cacheTime);
                        
            $this->log("{$dates['today']}主播时长记入缓存成功!luids:" . var_export($luids,true));

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
        $nextMonthDays = getNextMonthDays($dates['cur']);
        $dates['next'] = $nextMonthDays[0];
        
        return $dates;
    }

    public function run()
    {
        $this->_setAnchorCurLiveLengthRedis();
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

$obj = new curMonthAnchorLiveLength();

$obj->run();

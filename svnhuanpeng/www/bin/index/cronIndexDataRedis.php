<?php

ini_set('memory_limit', '1024M');
require_once __DIR__ . '/../../include/init.php';

use lib\game\AdminRecommendGame;
use system\Timer;
use service\game\helper\GameRedis;
use service\live\helper\LiveRedis;
use service\video\helper\VideoRedis;
use service\home\IndexService;
use lib\BaseInfo;
use lib\video\Video;

/**
 *  首页数据存入redis(直播推荐、公告信息、推荐游戏等)
 * @date 2017-07-17 14:17:06
 * @author longgang <longgang@6.cn>
 * @copyright (c) 2017, longgang
 * @version 1.0.0
 */
class cronIndexDataRedis
{
    private $_logName = 'cron_index_data_redis';
    
    public static $informationType =
    [
        BaseInfo::INFORMATION_TYPE_All,
        BaseInfo::INFORMATION_TYPE_PICTUR,
        BaseInfo::INFORMATION_TYPE_LIST,
    ];

    public static $informationClient =
    [
        BaseInfo::INFORMATION_CLIENT_APP,
        BaseInfo::INFORMATION_CLIENT_WEB,
    ];


    private function _setRecommGameList()
    {
        
        $adminRecommGameDao = new AdminRecommendGame();
        $gameRedis = new GameRedis();

        $recommGameIds = $adminRecommGameDao->getRecommendGameId();
        if (!$recommGameIds)
        {
            $this->log("error|从adminrecommendgame表里获取recommGameIds异常");
            return false;
        }
        
        return $gameRedis->setRecommendGameList($recommGameIds);
    }

    private function _setRecommLiveList()
    {
        $indexService = new IndexService();
        $indexService->setCaller('cron Script ' . __FILE__);
        $recommendLiveLists = $indexService->recommendLiveListsData();
        if(!$recommendLiveLists)
        {
            $this->log("error|获取推荐直播数据异常");
            return false;
        }
        $liveRedis = new LiveRedis();
        return $liveRedis->setRecommendLiveList($recommendLiveLists);
    }
    
    private function _setInformation()
    {
        $indexService = new IndexService();
        $indexService->setCaller('cron Script ' . __FILE__);
        $liveRedis = new LiveRedis();
        foreach (self::$informationClient as $client)
        {
            foreach (self::$informationType as $type)
            {
                $informationList = $indexService->getRecommendInformationListData($type, $client);
                if(!$informationList)
                {
                    $this->log("error|type:{$type};client:{$client};资讯信息获取异常");
                    continue;
                }
                $liveRedis->setInformationList($type, $client, $informationList);
            }
        }
        return true;

    }

    private function _setHotVideoForAppIndex()
    {
        $date = date('Y-m-d H:i:s', time() - 86400);

        $order = 'viewcount DESC';
        $videoDao = new Video();
        $res = $videoDao->getVideoListForApp($date, $order);
        if(!$res)
        {
            $this->log('error|APP首页热门视频数据获取异常');
        
            return false;
        }
        $videoRedis = new VideoRedis();
        return $videoRedis->setIndexHotVideo($res);
    }
    
    private function _setNewVideoForAppIndex()
    {
        $date = date('Y-m-d H:i:s', time() - 86400);

        $order = 'videoid DESC';
        $videoDao = new Video();
        $res = $videoDao->getVideoListForApp($date, $order);
        
        if(!$res)
        {
            $this->log('error|APP首页最新视频数据获取异常');
        
            return false;
        }
        $videoRedis = new VideoRedis();
        return $videoRedis->setIndexNewVideo($res);
    }

    public function run()
    {
        $this->log('start|首页数据种入缓存开始');
        $timer = new Timer();
        $timer->start();
        
        $this->_setRecommGameList();
        $this->_setRecommLiveList();
        $this->_setInformation();
        $this->_setHotVideoForAppIndex();
        $this->_setNewVideoForAppIndex();
        
        $timer->end();
        $t = $timer->getTime();
        $this->log("end |success| 脚本执行完成; 耗时:{$t}s;");

        return true;
    }

    public function log($msg)
    {
        return write_log($msg, $this->_logName);
    }
    
}

$obj = new cronIndexDataRedis();

$obj->run();

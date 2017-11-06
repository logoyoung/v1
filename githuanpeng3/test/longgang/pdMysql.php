<?php
require __DIR__.'/../../include/init.php';

use lib\video\Video;
use lib\video\VideoFollow;
use lib\live\LiveLength;
use lib\information\RecommendInformation;
use lib\game\AdminRecommendGame;
use lib\anchor\AnchorMostPopular;

/**
 * 数据库安全过滤预处理测试
 * @author longgang chen <longgang@6.cn>
 * @date 2017-09-07 15:40:03
 * @copyright (c) 2017, 6.cn
 * @version 1.0.0
 */

class pdMysql{
    
    public function f1()
    {
        $obj = new Video();
        $date = '2017-06-01 00:00:00';  
        $order = 'viewcount desc';
        $res = $obj->getVideoListForApp($date,$order);
        
        $uid = 50190;
        $status = 2;
        $res = $obj->getAnchorVideoid($uid, $status);
        
        $gameid = 150;
        $res = $obj->getVideoListByGameId($gameid);

        $videoId = ['33490','36255'];
        $res = $obj->getVideoInfoByVideoid($videoId);
        var_dump($res);
    }
    
    public function f2()
    {
        $obj = new VideoFollow();
        $uid = 50190;
        $res = $obj->getFollowVideoList($uid);
        var_dump($res);
    }
    
    public function f3()
    {
        $obj = new LiveLength();
        $smonth = '2017-09-01';
        $emonth = '2017-10-01';
        $res = $obj->getAllUid($smonth, $emonth);
        
        $res = $obj->getAllUidCount($smonth, $emonth);
        
        $uid = 2220;
        $res = $obj->getAnchorLiveLengths($uid, $smonth, $emonth);
        
        $res = $obj->getAnchorLiveLength($uid, $smonth, $emonth); 
        
        $mixLength = 3600;
        $res = $obj->getLiveEfeeDays($uid, $smonth, $emonth, $mixLength);
        
        $day = '2017-09-07';
        $res = $obj->getDayUid($day);
        
        $res = $obj->getAnchorDayLiveLengthsCount($day);
        
        $res = $obj->getAnchorDayLiveLength($uid, $day);
        var_dump($res);        
    }
    
    public function f4()
    {
        $obj = new RecommendInformation();
        $client = 2;
        $res = $obj->getCarouselListIds($client);
        var_dump($res);
    }
    
    public function f5()
    {
        $obj = new AdminRecommendGame();
        $type = 1;
        $res = $obj->getGameIdByRecommendType($type);
        var_dump($res);
    }
    
    public function f6()
    {
        $obj = new AnchorMostPopular();
        $uid = 2220;
        $smonth = '2017-09-01';
        $emonth = '2017-10-01';
        
        $res = $obj->getLivePopularyPeak($uid, $smonth, $emonth);
        var_dump($res);
                
    }
}

$obj =  new pdMysql();
$obj->f6();
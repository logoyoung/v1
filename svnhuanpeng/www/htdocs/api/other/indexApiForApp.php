<?php

include '../../../include/init.php';
require(INCLUDE_DIR . 'LiveRoom.class.php');

use service\room\LiveRoomService;
use service\user\UserDataService;
use service\live\LiveService;
use service\video\helper\VideoRedis;

/**
 * App首页
 * date 2016-4-27 17:50
 * author yandong@6rooms.com
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
$redisObj = new RedisHelp();

function getVideoListForApp($type, $db, $redisObj, $a, $black)
{
    if ($a != 10)
    {
        $data = date('Y-m-d H:i:s', time() - 86400);
        
        $videoRedis = new VideoRedis();

        if ($type == 1)
        {//热门
            $res = $videoRedis->getIndexHotVideo();
            $res = json_decode($res, TRUE);
            if (!$res)
            {
                $res = $db->field('videoid,uid,gameid,poster,title,ctime,gamename,viewcount,orientation,vfile')->where("status=" . VIDEO . " and  ctime >='$data'  GROUP BY uid")->order("viewcount DESC")->select('video');
            }
        }
        else {//最新
            $res = $videoRedis->getIndexNewVideo();
            $res = json_decode($res,true);
            if(!$res)
            {  
                $res = $db->field('videoid,uid,gameid,poster,title,ctime,gamename,viewcount,orientation,vfile')->where("status=" . VIDEO . " and  ctime >='$data' GROUP BY uid")->order("videoid DESC")->select('video');
            }
        }
    } else
    {
        if ($GLOBALS['env'] == "DEV")
        {
            $videoid = '14935,14980,15060,15065';
        } else
        {
            if ($black)
            {
                $array = array('2290' => 15520, '2625' => 17790, '2250' => 16335, '2140' => 16510);
                $bres = explode(",", $black);
                for ($i = 0, $k = count($bres); $i < $k; $i++)
                {
                    unset($array[$bres[$i]]);
                }
                if (empty($array))
                {
                    $videoid = 0;
                } else
                {
                    $videoid = implode(",", $array);
                }
            } else
            {
                $videoid = '15520,17790,16335,16510';
            }
        }

        $res = $db->field('videoid,uid,gameid,poster,title,ctime,gamename,viewcount,orientation,vfile')->where("videoid in ($videoid)")->select('video');
    }
    if ($res)
    {
        foreach ($res as $v)
        {
            $result[$v['uid']] = $v;
        }
    } else
    {
        $result = array();
    }
    return $result;
}

function makeRecommendorder($liveids, $db)
{
    if (empty($liveids))
    {
        return false;
    }
    $sql = "select liveid,poster,uid,`status`,stream,`server`,orientation from live where uid in ($uids) and `status`= " . LIVE . " order by liveid desc";
    $res = $db->doSql($sql);
    if (false !== $res && !empty($res))
    {
        foreach ($res as $v)
        {
            $lives[$v['uid']] = $v;
        }
        return $lives;
    } else
    {
        return array();
    }
}

function getVListForApp($type, $order, $page, $size, $conf, $db, $redisObj, $a, $black)
{
    $livelist = $videolist = array();

    //直播
    $live = getLiveListForApp($type,$size, $db, $redisObj, $a, $black);
    if ($live)
    {

        //批量获取用户信息
        $userDataService = new UserDataService();
        $userDataService->setCaller('api:' . __FILE__);
        $luids = array_keys($live);
        $userDataService->setUid($luids);
        $userInfo = $userDataService->getUserInfo();

        $liveRoomService = new LiveRoomService();
        $liveRoomService->setCaller('api:' . __FILE__);
        $liveRoomService->setLuid($luids);
        $viewCounts = $liveRoomService->batchGetLiveUserCountFictitious();

        foreach ($live as $v)
        {
            $ltmp['lvid'] = isset($v['liveid']) ? $v['liveid'] : 0;
            $ltmp['uid'] = $v['uid'];
            $ltmp['nick'] = array_key_exists($v['uid'], $userInfo) ? $userInfo[$v['uid']]['nick'] : '';
            $ltmp['head'] = isset($userInfo[$v['uid']]['pic']) ? $userInfo[$v['uid']]['pic'] : DEFAULT_PIC;
            $ltmp['gameID'] = isset($v['gameid']) ? $v['gameid'] : 0;
            $ltmp['poster'] = $v['poster'] ? $v['poster'] : '';
            $ltmp['title'] = $v['title'];
            $ltmp['stime'] = strtotime($v['ctime']);
            $ltmp['orientation'] = $v['orientation'];
            $ltmp['vtype'] = "1";
            if ($a == 10 && ($v['uid'] == 1815))
            {
                $ltmp['gameName'] = 'DemoLive';
            } else
            {
                $ltmp['gameName'] = $v['gamename'] ? $v['gamename'] : '';
            }
            $ltmp['userCount'] = isset($viewCounts[$v['uid']]) ? $viewCounts[$v['uid']] : 0;
            array_push($livelist, $ltmp);
        }

    } else
    {
        $livelist = array();
    }
    //录像
    $video = getVideoListForApp($type, $db, $redisObj, $a, $black);
    if ($video)
    {

        //批量获取用户信息
        $userDataService = new UserDataService();
        $userDataService->setCaller('api:' . __FILE__);
        $luids = array_keys($video);
        $userDataService->setUid($luids);
        $userInfo = $userDataService->getUserInfo();

        foreach ($video as $vl)
        {
            $vtmp['lvid'] = $vl['videoid'];
            $vtmp['uid'] = $vl['uid'];
            $vtmp['nick'] = array_key_exists($vl['uid'], $userInfo) ? $userInfo[$vl['uid']]['nick'] : '';
            $vtmp['head'] = (array_key_exists($vl['uid'], $userInfo) && !empty($userInfo[$vl['uid']]['pic'])) ? $userInfo[$vl['uid']]['pic'] : DEFAULT_PIC;
            $vtmp['gameID'] = $vl['gameid'];
            $vtmp['poster'] = sposter($vl['poster']);
            $vtmp['title'] = $vl['title'];
            $vtmp['stime'] = $vl['ctime'];
            $vtmp['gameName'] = $vl['gamename'];
            $vtmp['userCount'] = $vl['viewcount'];
            $vtmp['orientation'] = $vl['orientation'];
            $vtmp['vtype'] = "2";
            $vtmp['videoUrl'] = sfile($vl['vfile']);
            array_push($videolist, $vtmp);
        }
    } else
    {
        $videolist = array();
    }
    if ($livelist)
    {
        if ($a == 10)
        {
            $afterLiveSort = dyadicArray($livelist, 'lvid', SORT_ASC);
        } else
        {
            $afterLiveSort = dyadicArray($livelist, $order);
        }
        //前三十条直播随机排序
        $thirtyArray = array_slice($afterLiveSort, 0, 30);
        $aferThiryArray = array_slice($afterLiveSort, 30);
        shuffle($thirtyArray);
        $afterLiveSort = array_merge($thirtyArray, $aferThiryArray);
    } else
    {
        $afterLiveSort = array();
    }
    if ($videolist)
    {
        $afterVideoSort = dyadicArray($videolist, $order);
    } else
    {
        $afterVideoSort = array();
    }

    $newarray = array_merge($afterLiveSort, $afterVideoSort);
    $page = returnPage(count($newarray), $size, $page);
    if ($newarray)
    {
        $offset = ($page - 1) * $size;

        $list = array_slice($newarray, $offset, $size);
//        $list=$newarray;
    } else
    {
        $list = array();
    }
    return array('list' => $list, 'allCount' => count($newarray));
}

/**
 * 获取所有直播
 * @param object $db db对象
 * @param object $redisObj redis对象
 * @return array
 */
function getLiveListForApp($type,$size, $db, $redisObj, $a, $black)
{
    if ($a != 10)
    {
        $liveService = new LiveService();
        if ($type == 1)
        {//热门
            $liveService->setCaller('api:' . __FILE__);
            $liveService->setLiveType(1);
            $liveService->setSize($size);
            $res = $liveService->getLiveListByType();
            
            if (!$res)
            {
                $res = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')->where("status=" . LIVE)->select('live');
                if($res)
                {
                    foreach ($res as $k=>$v)
                    {
                        $res[$k]['poster'] = isset($v['poster']) ? $GLOBALS['env-def'][$GLOBALS['env']]['domain-lposter'] . $v['poster'] : '';
                    }
                }
            }
        }
        else {//最新
          
            $liveService->setCaller('api:' . __FILE__);
            $liveService->setLiveType(2);
            $liveService->setSize($size);
            $res = $liveService->getLiveListByType();
            
            if($res)
            {
                $res = multiArraySort($res, 'ctime','liveid');
            } else
            {
                $res = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')->where("status=" . LIVE)->order("liveid DESC")->select('live');
            }
             
        }
    } else
    {
        if ($GLOBALS['env'] == "DEV")
        {
            $liveid = '2045,2050';
        } else
        {
            if ($black)
            {
                $array = array('1815' => 5, '1870' => 10);
                $bres = explode(",", $black);
                for ($i = 0, $k = count($bres); $i < $k; $i++)
                {
                    unset($array[$bres[$i]]);
                }
                if (empty($array))
                {
                    $liveid = 0;
                } else
                {
                    $liveid = implode(",", $array);
                }
            } else
            {
                $liveid = '5,10';
            }
        }
        $res = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')->where("liveid in ($liveid)")->select('live');
    }
    
//    var_dump($res);
    if ($res)
    {
        foreach ($res as $v)
        {
            $result[$v['uid']] = $v;
        }
    } else
    {
        $result = array();
    }
    return $result;
}

/**
 * 获取列表数据
 * @param type $type
 * @param type $page
 * @param type $size
 * @param type $db
 * @param type $redisObj
 * @return type
 */
function getListForApp($type, $page, $size, $conf, $db, $redisObj, $a, $black)
{
    if (!in_array($type, array(1, 2)))
    {
        error(-4013);
    }
    if ($type == 1)
    {
        $order = 'userCount';
    }
    if ($type == 2)
    {
        $order = 'stime';
    }
    $res = getVListForApp($type, $order, $page, $size, $conf, $db, $redisObj, $a, $black);
    return $res ? $res : array();
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encapass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$size = isset($_POST['size']) ? trim($_POST['size']) : 10;
$page = isset($_POST['page']) ? trim($_POST['page']) : 1;
$type = isset($_POST['type']) ? trim($_POST['type']) : 1;
$a = isset($_POST['a']) ? trim($_POST['a']) : '';
$black = isset($_POST['black']) ? trim($_POST['black']) : '';

$result = getListForApp($type, $page, 500, $conf, $db, $redisObj, $a, $black);
if ($result)
{
    succ(array('list' => $result['list'], 'total' => $size));
} else
{
    succ(array('list' => array(), 'total' => '0'));
}
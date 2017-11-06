<?php

include '../../init.php';
require_once INCLUDE_DIR . 'redis.class.php';
/**
 * App首页
 * date 2016-4-27 17:50
 * author yandong@6rooms.com
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
$redisObj = new RedisHelp();

function getMoreUserInfo($uids, $db) {
    $ret = array();
    $res = $db->field('uid,nick,pic')->where('uid in (' . $uids . ')')->select('userstatic');
    foreach ($res as $key => $val) {
        $ret[$val['uid']] = $val;
    }
    return $ret;
}
function getVideoListForApp($type, $db, $redisObj) {
    if ($type == 1) {//热门
        $res = $db->field('videoid,uid,gameid,poster,title,ctime,gamename,viewcount,orientation,vfile,COUNT(DISTINCT uid)')->where("status=" . VIDEO. " GROUP BY uid")->order("viewcount DESC")->limit(20)->select('video');
    } else {//最新
        $res = $db->field('videoid,uid,gameid,poster,title,ctime,gamename,viewcount,orientation,vfile,COUNT(DISTINCT uid)')->where("status=" . VIDEO. " GROUP BY uid")->order("ctime DESC")->limit(20)->select('video');
    }
    if ($res) {
        foreach ($res as $v) {
            $result[$v['uid']] = $v;
        }
    }else{
        $result=array();
    }
    return $result;
}

function getVListForApp($type, $order, $page, $size, $conf, $db, $redisObj) {
    $livelist = $videolist = array();
    //直播
    $live = getLiveListForApp($type, $db, $redisObj);
    if ($live) {
        $liveids = array_keys($live);
        $userInfo = getMoreUserInfo(implode(',', $liveids), $db); //批量获取用户信息
        $liveuser = batchGetLiveRoomUserCount(implode(',', $liveids), $db); //获取在线用户数 
        foreach ($live as $v) {
            $ltmp['lvid'] = $v['liveid'];
            $ltmp['luid'] = $v['uid'];
            $ltmp['nick'] = array_key_exists($v['uid'], $userInfo) ? $userInfo[$v['uid']]['nick'] : '';
            $ltmp['pic'] = array_key_exists($v['uid'], $userInfo) ? "http://" . $conf['domain-img'] ."/". $userInfo[$v['uid']]['pic'] : '';
            $ltmp['gameid'] = $v['gameid'];
            $ltmp['poster'] = $v['poster'] ? "http://" . $conf['domain-img'] . "/" . $v['poster'] : '';
            $ltmp['title'] = $v['title'];
            $ltmp['ctime'] = strtotime($v['ctime']);
            $ltmp['angle'] = $v['orientation'];
            $ltmp['vtype'] = "1";
            $ltmp['gname'] = $v['gamename']?$v['gamename'] :'';
            $ltmp['vCount'] = array_key_exists($v['uid'], $liveuser) ? $liveuser[$v['uid']] : "0";
            array_push($livelist, $ltmp);
        }
    } else {
        $livelist = array();
    }
    //录像
    $video = getVideoListForApp($type, $db, $redisObj);
    if ($video) {
        $videoids = array_keys($video);
        $userInfo = getMoreUserInfo(implode(',', $videoids), $db); //批量获取用户信息
        foreach ($video as $vl) {
            $vtmp['lvid'] = $vl['videoid'];
            $vtmp['luid'] = $vl['uid'];
            $vtmp['nick'] = array_key_exists($vl['uid'], $userInfo) ? $userInfo[$vl['uid']]['nick'] : '';
            $vtmp['pic'] = array_key_exists($vl['uid'], $userInfo) ? "http://" . $conf['domain-img'] . "/" . $userInfo[$vl['uid']]['pic'] : '';
            $vtmp['gameid'] = $vl['gameid'];
            $vtmp['poster'] = $vl['poster'] ? "http://" . $conf['domain-img'] . "/" . $vl['poster'] : '';
            $vtmp['title'] = $vl['title'];
            $vtmp['ctime'] = $vl['ctime'];
            $vtmp['gname'] = $vl['gamename'];
            $vtmp['vCount'] = $vl['viewcount'];
            $vtmp['angle'] = $vl['orientation'];
            $vtmp['vtype'] = "2";
            $vtmp['vfile'] = $vl['vfile'] ? ($conf['domain-video'] . $vl['vfile']) : '';
            array_push($videolist, $vtmp);
        }
    } else {
        $videolist = array();
    }
    if($livelist){
        $afterLiveSort = dyadicArray($livelist, $order);  
    }else{
        $afterLiveSort =array();
    }
   if($videolist){
         $afterVideoSort = dyadicArray($videolist, $order);  
   }else{
          $afterVideoSort =array(); 
   }

    $newarray = array_merge($afterLiveSort, $afterVideoSort);
    $page=returnPage(count($newarray), $size, $page);
    if ($newarray) {
        $offset = ($page - 1) * $size;
        $list = array_slice($newarray, $offset, $size);
    } else {
        $list = array();
    }
    return array('list'=>$list,'allCount'=>count($newarray));
}



/**
 * 获取所有直播
 * @param object $db db对象
 * @param object $redisObj redis对象
 * @return array
 */
function getLiveListForApp($type, $db, $redisObj) {
    if ($type == 1) {//热门
        $res = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')->where("status=" . LIVE)->select('live');
    } else {//最新
        $res = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')->where("status=" . LIVE)->order("ctime DESC")->select('live');
    }
    if ($res) {
        foreach ($res as $v) {
            $result[$v['uid']] = $v;
        }
    }else{
      $result =array();
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
function getListForApp($type, $page, $size, $conf, $db, $redisObj) {
    if (!in_array($type, array(1, 2))) {
        error(-4013);
    }
    if ($type == 1) {
        $order = 'vCount';
    }
    if ($type == 2) {
        $order = 'ctime';
    }
    $res = getVListForApp($type, $order, $page, $size, $conf, $db, $redisObj);
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

$result = getListForApp($type, $page, $size, $conf, $db, $redisObj);
if ($result) {
    exit(json_encode(array('list' => $result['list'],'allCount'=>$result['allCount'])));
} else {
    exit(json_encode(array('list' => array())));
}

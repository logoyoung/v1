<?php

include '../../init.php';
/**
 * App端获取观看历史视频滑动列表
 * date 2016-11-03 11:28
 * anchor yandong@6rooms.com
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * 获取收藏的录像id
 * @param int $uid  主播id
 * @param type $conf
 * @param type $db
 * @return type
 */
function getFollowVideoList($uid, $db) {
    $res = $db->field('videoid')->where('uid=' . $uid . '')->order('tm DESC')->select('videofollow');
    if (false !== $res) {
        return $res;
    } else {
        return array();
    }
}

/**
 * 根据videoid获取录像详情
 * @param type $videoId
 * @param type $db
 * @return boolean
 */
function  getVideoInfoByVideoid($videoId,$db){
    if(empty($videoId)){
        return false;
    }
    $res=$db->field('videoid,uid,poster')->where("videoid in ($videoId)")->select('video');
    if(false !==$res){
        foreach($res as $v){
           $temp[$v['videoid']]=$v;
        }
        return $temp;
    }else{
        return array();
    }
}

function  getHistoryList($uid,$db){
    if(empty($uid)){
        return false;
    }
    $res=$db->field('luid')->where("uid=$uid")->order('stime  desc ')->limit(20)->select('history');
    if($res !==false && !empty($res)){
        return array_column($res,'luid');
    }else{
        return array();
    }
}

function  getLiveByHistory($luid,$conf,$db){
    if(empty($luid)){
        return false;
    }
    $list=array();
    $luid=implode(',',$luid);
    $res=$db->field('liveid,uid,poster')->where("uid  in ($luid)  and status=".LIVE)->order('ctime desc')->select('live');
    if($res !==false  && !empty($res)){
        foreach($res as $v){
            $temp['videoid']=$v['liveid'];
            $temp['uid']=$v['uid'];
            $temp['pic']=$v['poster'] ? "http://" . $conf['domain-img'] . "/".$v['poster'] : CROSS;
            array_push($list,$temp);
        }
        return $list;
    }else{
        return array();
    }
}


function getLiveList($uid,$conf,$db){
    $hlist=getHistoryList($uid,$db);
    if($hlist){
        $res=getLiveByHistory($hlist,$conf,$db);
        if($res){
            return $res;
        }else{
            return array();
        }
    }else{
        return array();
    }
}
/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
if (!$uid || !$encpass) {
    error('-1015');
}
//检查用户登陆状态
$userState = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $userState) {
    error($userState);
}
$res = getLiveList($uid,$conf,$db);
 exit(json_encode(array('list'=>$res)));
 

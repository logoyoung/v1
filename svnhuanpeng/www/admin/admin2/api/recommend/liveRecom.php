<?php

/**
 * 直播推荐
 * yandong@6rooms.com
 * date 2016-07-12 16:25
 * 
 */
require '../../includeAdmin/init.php';
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;


//if (empty($uid) || empty($encpass) || empty($type)) {
//    error(-4013);
//}
//$adminHelp = new AdminHelp($uid, $type);
//$err = $adminHelp->loginError($encpass);
//if ($err) {
//    error($err);
//}
/**
 * 获取推荐中的直播
 * @param type $db
 * @return array()
 */
function getRecommendList($db) {
    $res = $db->select('index_recommend_Live');
    if ($res !== false) {
        return $res;
    } else {
        return array();
    }
}

$result=getRecommendList($db);
if ($result) {
    $list=array();
    foreach ($result as $rk => $rv) {
        $arr['lid'] = $rv['liveid'];
        $arr['gid'] = $rv['gameid'];
        $arr['gtid'] = $rv['gametid'];
        $arr['gname'] = $rv['gamename'];
        $arr['uid'] = $rv['uid'];
        $arr['title'] = $rv['title'];
        $arr['ctime'] = $rv['ctime'];
        if($rv['poster']){
             $arr['poster'] = "http://" . $conf['domain-img'] .'/'. $rv['poster'];
             $arr['ispic'] ='1';
        }else{
             $arr['poster'] = CROSS;
             $arr['ispic'] ='0';
        }
        $arr['angle'] = $rv['orientation'];
        $autheInfo = getUserInfo($rv['uid'], $db);
        $arr['nick'] = $autheInfo[0]['nick'] ? $autheInfo[0]['nick'] : '';
        $arr['pic'] = $autheInfo[0]['pic'] ? "http://" . $conf['domain-img'] . "/" . $autheInfo[0]['pic'] : DEFAULT_PIC;
        array_push($list, $arr);
        }
        succ($list);
} else {
   succ(array());
}

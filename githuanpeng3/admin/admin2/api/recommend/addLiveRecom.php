<?php

/**
 * 添加首页推荐
 * yandong@6rooms.com
 * date 2016-07-14 10:25
 * 
 */
require '../../includeAdmin/init.php';
$db = new DBHelperi_admin();
/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
$liveid = isset($_POST['liveid']) ? (int) $_POST['liveid'] : 30545;

//if (empty($uid) || empty($encpass) || empty($type) || empty($liveid)) {
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
function addRecommendLive($liveid, $db) {
    $live = $db->field('liveid ,stream,uid,gametid ,gameid, gamename,title,ctime,poster,orientation')->where("liveid=$liveid   and stop_reason=0  and status=" . LIVE)->select("live");
    if ($live) {
        $data = array(
            'liveid' => $live[0]['liveid'],
            'stream' => $live[0]['stream'],
            'uid' => $live[0]['uid'],
            'gametid' => $live[0]['gametid'],
            'gameid' => $live[0]['gameid'],
            'gamename' => $live[0]['gamename'],
            'title' => $live[0]['title'],
            'ctime' => $live[0]['ctime'],
            'poster' => $live[0]['poster'],
            'orientation' => $live[0]['orientation']
        );
        $addres = $db->insert('index_recommend_Live', $data);
        if (false !== $addres) {
            return $addres;
        } else {
            return array();
        }
    } else {
        return array();
    }
}

$result = addRecommendLive($liveid, $db);
if (false !==$result) {
    succ($result);
} else {
    error(-1012);
}
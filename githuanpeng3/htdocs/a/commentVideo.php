<?php

include '../init.php';
/**
 * 评论录像
 * date 2015-12-31 15:42 pm
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
/**
 * 检测用户是否已评论过
 * @param int $uid
 * @param int $videoID
 * @param object $db
 * @return array
 */
function cheackUserIsComment($uid,$videoID,$db){
    $res=$db->where("uid=$uid and videoid=$videoID")->select('videocomment');
    return $res;
}
/**
 * 添加录像评论
 * @param array $data
 * @param object $db
 * @param object $conf
 * @return array
 */
function addComment($data, $db, $conf) {
    $commentback = array();
    $result = $db->insert('videocomment', $data);
    if (false !== $result) {
        $commentback['commentUserID'] = $data['uid'];
        $userinfo = getUserInfo($data['uid'], $db);
        $commentback['commentNickName'] = $userinfo[0]['nick'];
        $commentback['commentUserPicURL'] = $userinfo[0]['pic'] ? "http://" . $conf['domain-img'] . '/' . $userinfo[0]['pic'] : DEFAULT_PIC;
        $commentback['commentTimeStamp'] = time();
        $commentback['rate'] = $data['rate'];
        $commentback['comment'] = $data['comment'];
        $res = array('isSuccess' => 1, 'commentData' => $commentback);
    } else {
        $res = array('isSuccess' => 0, 'commentData' => '');
    }
    return $res;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$videoID = isset($_POST['videoID']) ? $_POST['videoID'] : '';
$comment = isset($_POST['comment']) ? $_POST['comment'] : '';
$rate = isset($_POST['rate']) ? $_POST['rate'] : 0;
if (empty($uid) || empty($encpass)) {
    error(-993);
}
if (empty($comment)) {
    error(-992);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$videoID = checkInt($videoID);
$comment = filterData($comment);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
//$checkres=cheackUserIsComment($uid,$videoID,$db);
//if($checkres){
//    exit(jsone(array('isSuccess' => 1, 'commentData' => '您已经评论过喽!')));
//}
$data = array(
    'uid' => $uid,
    'videoid' => $videoID,
    'rate' => $rate,
    'comment' => $comment,
    'ip' => ip2long(fetch_real_ip($port)),
    'port' => $port
);
$back = addComment($data, $db, $conf);
if ($back) {
    exit(jsone($back));
} else {
    exit(jsone(array('comm' => '')));
}


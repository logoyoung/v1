<?php

include '../../../include/init.php';
use service\rule\TextService;
use service\user\UserAuthService;

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
 * @param $data 添加的数组
 * @param $db
 * @return bool
 */
function addComment($data,$db) {
    $res = $db->insert('videocomment', $data);
    if (false !== $res) {
       return $res;
    } else {
      return false;
    }
}

function  back($data,$conf,$db){
    $commentback['uid'] = $data['uid'];
    $userinfo = getUserInfo($data['uid'], $db);
    $commentback['nick'] = $userinfo[0]['nick'];
    $commentback['head'] = $userinfo[0]['pic'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $userinfo[0]['pic'] : DEFAULT_PIC;
    $commentback['ctime'] = time();
    $commentback['rate'] = $data['rate'];
    $commentback['comment'] = $data['comment'];
    return $commentback;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$videoID = isset($_POST['videoID']) ? (int)$_POST['videoID'] : '';
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
$rate = isset($_POST['rate']) ? $_POST['rate'] : 0;
if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
if (empty($comment)) {
    error2(-5036,2);
}
if(empty($videoID)){
    error2(-4013,2);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$videoID = checkInt($videoID);
$comment = filterData($comment);
$commentLen = mb_strlen($comment, 'utf-8');
if ($commentLen > 120) {
    error2(-4084,2);
} else {
    if (mb_strlen($comment, 'latin1') > 120) {
        error2(-4084,2);
    }
}
$textService = new TextService();
$textService->setCaller('api:'.__FILE__.';line:'.__LINE__);
//$textService->setCallLevel(true);
$port = 0;
$textService->addText($comment,$uid,TextService::CHANNEL_DYNAMIC_COMMENT)->setIp(fetch_real_ip($port));
//反垃圾过滤
if(!$textService->checkStatus())
{
    write_log("error|肉容包含敏感内容,conntent:{$comment};uid:{$uid};videoID:{$videoID}",'comment_video_error');
    error2(-4084,2);
}

$auth = new UserAuthService();
$auth->setUid($uid);
$auth->setEnc($encpass);

//校验encpass、用户 登陆状态
if($auth->checkLoginStatus() !== true)
{
    //获取校验结果
    $result    = $auth->getResult();
    //错误码
    $errorCode = $result['error_code'];
    //错误消息
    $errorMsg  = $result['error_msg'];
    //假如是封禁的，可以获取禁时间
    $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
    write_log("notice|uid:{$uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|api:".__FILE__,'auth_access');
    error2(-4067,2);
}

$isBind=checkUserIsBindMobile($uid, $db);
if(false===$isBind){
    error2(-5026,2);
}
//$checkres=cheackUserIsComment($uid,$videoID,$db);
//if($checkres){
//    exit(jsone(array('isSuccess' => 1, 'commentData' => '您已经评论过喽!')));
//}
$cheakCommentMode=checkMode(CHECK_COMMENT,$db);//检查评论审核模式
if($cheakCommentMode){
    $status=COMMENT_AUTO_PASS;
}else{
    $status=COMMENT_WAIT;
}
$data = array(
    'uid' => $uid,
    'videoid' => $videoID,
    'rate' => $rate,
    'comment' => $comment,
    'status'=>$status,
    'ip' => ip2long(fetch_real_ip($port)),
    'port' => $port
);
addComment($data,$db);
if($cheakCommentMode){
    //先发后审
    succ(back($data,$conf,$db));
}else{
    //先审后发
    succ();
}




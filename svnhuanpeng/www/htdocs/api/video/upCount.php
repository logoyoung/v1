<?php

include '../../../include/init.php';
use service\user\UserAuthService;

/**
 * 点赞 && 取消点赞
 * date 2016-05-06 10:58
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 点赞&&取消点赞
 * @param type $uid  用户id
 * @param type $videoId  录像id
 * @param type $type  0取消点赞,1点赞
 * @param type $db
 * @return int
 */
function upCount($uid, $videoId, $type, $db) {
    if (empty($uid) || empty($videoId)) {
        return false;
    }
    $checkisup = $db->where("videoid=$videoId and  uid=$uid")->select('isupvideo');
    if ($type) {//点赞
        if ($checkisup) {
            return true;
        } else {
            $sql = "update video set upcount=upcount+1 where videoid=$videoId";
            $res = $db->doSql($sql);
            if ($res) {
                $data = array(
                    'videoid' => $videoId,
                    'uid' => $uid
                );
                $db->insert('isupvideo', $data);
                return true;
            } else {
                return false;
            }
        }
    } else {//取消点赞
        if ($checkisup) {
            $res = $db->where("videoid=$videoId and  uid=$uid")->delete('isupvideo');
            if ($res) {
                $sql = "update video set upcount=upcount-1 where videoid=$videoId";
                $result = $db->doSql($sql);
                if ($result) {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
    return $back;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$videoId = isset($_POST['videoID']) ? trim($_POST['videoID']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : 1;

if (empty($uid) || empty($encpass || empty($videoId))) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$videoId = checkInt($videoId);

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

$res = upCount($uid, $videoId, $type, $db);
if ($res === false) {
    error2(-5017);
} else {
   succ();
}


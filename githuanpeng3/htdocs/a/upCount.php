<?php

include '../init.php';
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
$videoId = isset($_POST['videoId']) ? trim($_POST['videoId']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : 1;

if (empty($uid) || empty($encpass || empty($videoId))) {
    error(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$videoId = checkInt($videoId);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$res = upCount($uid, $videoId, $type, $db);
if ($res === false) {
    error(-5017);
} else {
    exit(jsone(array('isSuccess' => 1)));
}


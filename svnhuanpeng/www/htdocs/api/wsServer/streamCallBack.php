<?php
/**
 * 推流鉴权
 * auchor: Dylan
 * date: 2017-01-05 17:00
 */
include '../../init.php';
$db = new DBHelperi_huanpeng();
function checkLiveIsExist($uid, $liveid, $db)
{
    if (empty($uid) || empty($liveid)) {
        return false;
    }
    $res = $db->field('uid')->where("uid=$uid  and  liveid=$liveid")->select('live');
    if (false !== $res) {
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return false;
    }
}

/**
 * start
 */
$uid = isset($_GET['uid']) ? (int)($_GET['uid']) : '';
$liveid = isset($_GET['liveid']) ? (int)($_GET['liveid']) : '';
$tm = isset($_GET['tm']) ? trim($_GET['tm']) : '';
$sign = isset($_GET['sign']) ? trim($_GET['sign']) : '';
if (empty($uid) || empty($liveid) || empty($tm) || empty($sign)) {
    Log_for_net('回源鉴权:缺少参数', $_GET, $db);
    echo 0;exit;
}

Log_for_net("回源鉴权:返回参数",$_GET,$db);
$Signer = buildSign(toString(array('uid'=>$uid,'liveid'=>$liveid,'tm'=>$tm)), STREAM_SECRET,false);
$checkSign = verifySign(toString(array('uid'=>$uid,'liveid'=>$liveid,'tm'=>$tm,'sign'=>$sign)), STREAM_SECRET);
if (true !== $checkSign) {
    Log_for_net('回源鉴权:秘钥不一致', array('net'=>$sign,'huan'=>$Signer), $db);
    echo 0;exit;
}
$res = checkLiveIsExist($uid, $liveid, $db);
if (false === $res) {
    echo 0;exit;
} else {
    if ($res == 1) {
        Log_for_net('回源鉴权成功!', array('res'=>$res,'uid'=>$uid), $db);
        echo 1;exit;
    } else {
        echo 0;exit;
    }
}



<?php
/**
 * 禁言设置   供后台使用，同时为前台提供接口（内网访问）
 * jiantao@6.cn
 * date 2016-10-12 13:55
 * 
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
require '../../includeAdmin/Redis.class.php';
$db = new DBHelperi_admin();

/**
 * 插入禁言记录
 * @param type $db
 * @param int $uid  管理员id
 * @param int $luid 用户id
 * @param int $roomid 禁言房间号
 * @param int $from 1后台 2前台
 * @param date $stime 禁言开始时间
 * @param date $etime 禁言结束时间
 * @param 1 $type 类型 1禁言操作 2已取消 
 * @return boolean
 */
function insertSilence($db, $uid, $luid, $roomid, $from, $reason, $stime, $etime, $type) 
{
    //把以前的记录更新  
    $where = '`luid`=' . $luid . ' and `roomid`=' . $roomid . ' and `type`=1';
    $data = array(
        'uuid' => $uid,
        'utime' => date('Y-m-d H:i:s'),
        'type' => 2
    );
    
    $db->where($where)->update('usersilence', $data);
    
    $data = array(
        'uid' => $uid,
        'luid' => $luid,
        'roomid' => $roomid,
        'fromto' => $from,
        'reason' => $reason,
        'stime' => date('Y-m-d H:i:s', $stime),
        'etime' => date('Y-m-d H:i:s', $etime),
        'type' => $type
    );
    
    return $db->insert('usersilence', $data);
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
if(verifySign($_POST, 'JR*&_+23d10~`9|9)diuy')) {
    $from = 2;
} else {
    $encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
    $type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
    if (empty($uid) || empty($encpass) || empty($type)) {
        error(-1007);
    }
    
    $adminHelp = new AdminHelp($uid, $type);
    $err = $adminHelp->loginError($encpass);
    if ($err) {
        error($err);
    }
    $from = 1;
}

$luid = isset($_POST['luid']) ? (int) $_POST['luid'] : 0;
if(!$uid || !$luid) {
    error(-1023);
}

$roomid = isset($_POST['roomid']) ? (int) $_POST['roomid'] : 0;
$reason = isset($_POST['reason']) ? $_POST['reason'] : '';
$timeLength = (isset($_POST['timeLength']) && is_numeric($_POST['timeLength'])) ? (int) $_POST['timeLength'] : '';
$stime = time();
$etime = $timeLength ? ($stime + $timeLength) : strtotime('+10 years');
$content = array('stime'=>$stime, 'etime'=>$etime);
$redis = new RedisHelp();
$redis->hset('silence_' . $roomid, $luid, json_encode($content));

$res = insertSilence($db, $uid, $luid, $roomid, $from, $reason, $stime, $etime, 1);
if ($res) {
    succ(array('etime'=>$etime));
} else {
    error(-1014);
}

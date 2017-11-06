<?php

include '../init.php';
/**
 * 添加房间管理员
 * date 2016-1-10 17:14
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * 根据昵称获取用户uid
 * @param string $adminNick
 * @param object $db
 * @return array
 */
function getUidByNick($adminNick, $db, $conf) {
    $res = $db->field('uid,nick,pic')->where("nick='$adminNick'")->select('userstatic');
    if ($res) {
        foreach ($res as $v) {
            $adminInfo['adminUserID'] = $v['uid'];
            $adminInfo['adminNick'] = ($v['nick']) ? $v['nick'] : '';
            $adminInfo['adminUserPicURL'] = $v['pic'] ? "http://" . $conf['domain-img'] . "/" . $v['pic'] : DEFAULT_PIC;
        }
    }

    return $adminInfo ? $adminInfo : array();
}

/**
 * 添加房间管理员
 * @param type $uid
 * @param type $adminId
 * @param type $db
 * @return type
 */
function addAdmin($uid, $adminId, $time, $db) {
    $checkIsExist = $db->where("luid=$uid and uid = $adminId")->select('roommanager');
    if (!empty($checkIsExist)) {
        return false;
    } else {
        $data=array(
            'luid'=>$uid,
            'uid'=>$adminId,
            'ctime'=>$time
        );
        $res = $db->insert('roommanager',$data);
    }
    return $res;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$adminNick = isset($_POST['adminNick']) ? trim($_POST['adminNick']) : '';
if (empty($uid) || empty($encpass) || empty($adminNick)) {
    error(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$adminNick = filterData(checkStr($adminNick));
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$rest = getUidByNick($adminNick, $db, $conf);
if ($rest) {
    if ($uid == $rest['adminUserID']) {
        error(-5021);
    } else {
        $addresult = addAdmin($uid, $rest['adminUserID'], date('Y-m-d H:i:s'), $db);
        if($addresult===false){
             error(-5022);
        }else{
             exit(jsone(array('isSuccess' => 1, 'adminer' => $rest))); 
        }
    }
} else {
    error(-5020);
}


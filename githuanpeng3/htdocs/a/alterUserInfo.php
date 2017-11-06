<?php

include '../init.php';
require(INCLUDE_DIR . 'User.class.php');
/**
 * 修改昵称信息
 * date 2016-1-5 14:58
 * author yandong@6rooms.com
 * version 0.1 update 2016-05-07
 */
$db = new DBHelperi_huanpeng();


/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$nick = isset($_POST['nick']) ? trim($_POST['nick']) : '';
if (empty($uid) || empty($encpass)) {
    error(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
if (!empty($nick)) {
    $nick = filterData($nick);
    $nickLen = mb_strlen($nick, 'utf-8');
    if ($nickLen < 3 || $nickLen > 10) {
        error(-1004);
    } else {
        if (mb_strlen($nick, 'latin1') < 3 || mb_strlen($nick, 'latin1') > 30) {
            error(-1004);
        }
    }
} else {
    error(-4064);
}
$userHelp = new UserHelp($uid, $db);

if ($loginError = $userHelp->checkStateError($encpass)) {
    error($loginError);
}
if ($userHelp->isUserNickExist($nick)) {
    error(-4035);
}

$coin = (int)$userHelp->getProperty()['hpcoin'];

if ($coin < MODIFY_NICK_COST) {
    error(-5023); //余额不足
}
$db->autocommit(false);
$db->query('begin');

$checkNickMode = checkMode(CHECK_NICK, $db);
$isfree = checkisFreeChangeNick($uid, $db);//是否有免费的改名机会
if ($isfree) {
    $spend = true;
} else {
    $spend = $userHelp->costHpCoin(MODIFY_NICK_COST, $coin);
}
if ($checkNickMode) {
    //先发后审
    setNickToAdmin($uid, $nick, $db, USER_NICK_AUTO_PASS);//同步到admin_user_nick表中
    if (!$userHelp->setNick($nick, $db) || !$spend) {
        $db->rollback();
        error(-5017); //系统错误
    } else {
        $db->query('commit');
        $db->autocommit(true);
        if ($isfree) {
            changeIsfreeStatus($uid, 0, $db);
        }
        $user = $userHelp->getProperty();
        succ(array('hpbean' => $user['hpbean'], 'hpcoin' => $user['hpcoin']));
    }
} else {
//先审后发
    $res = setNickToAdmin($uid, $nick, $db, USER_NICK_WAIT);//同步到admin_user_nick表中
    if (!$res || !spend) {
        $db->rollback();
        error(-5017); //系统错误
    } else {
        $db->query('commit');
        $db->autocommit(true);
        if ($isfree) {
            changeIsfreeStatus($uid, 0, $db);
        }
        $user = $userHelp->getProperty();
        succ(array('hpbean' => $user['hpbean'], 'hpcoin' => $user['hpcoin']));
    }

}



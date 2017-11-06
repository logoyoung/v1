<?php

include '../../init.php';
require(INCLUDE_DIR . 'User.class.php');
/**
 * 获取观众信息
 * date 2016-07-11 11:11
 * author yandong@6rooms.com
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();


/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass=isset($_POST['encpass']) ? trim(($_POST['encpass'])) : '';
$luid = isset($_POST['luid']) ? (int) ($_POST['luid']) : '';

if (empty($luid)) {
    error(-4013);
}
$userobj = new UserHelp($luid, $db);
$userRes = $userobj->getUsers(); //获取头像昵称
$info['luid'] = "$luid";
$info['picUrl'] = $userRes['pic'] ? $userRes['pic'] : '';
$info['anchorNickName'] = $userRes['nick'] ? $userRes['nick'] : '';
$info['level'] = $userobj->getLevelInfo()['level'];
$info['anchorLevel'] =  getAnchorLevel($luid, $db);
if ($uid && $encpass) {
    $s = CheckUserIsLogIn($uid, $encpass, $db);
    if (true !== $s) {
        error($s);
    }
    $isFollow =isOneFollowOne($uid, $luid, $db);
    if($isFollow){
       $info['isFollow']='1';  
    }else{
        $info['isFollow']="0";
    }
} else {
    $info['isFollow'] = '0';
}
$info['fansCount'] = $userobj->followCount();
$isCertify = $userobj->getCertifyInfo();
if ($isCertify['emailstatus'] == EMAIL_PASS && $isCertify['identstatus'] == RN_PASS && $isCertify['phonestatus'] == 1) {
    $info['isCertify'] = "1";
} else {
    $info['isCertify'] = "0";
}
exit(jsone($info));





<?php
include '../init.php';
function getUserInfo2($uid, $enc, $db, $conf)
{
    $userInfo = array(
        'loginStatus' => 0
    );
    if (CheckUserIsLogIn($uid, $enc, $db) !== true)
        return $userInfo;
    $row = getUserBaseInfo($uid, $db);
    $levelIntegral = getLevelIntegral($row['level'], $db);
    $userInfo['loginStatus'] = 1;
    $userInfo['_uid'] = $uid;
    $userInfo['_enc'] = $enc;
    $userInfo['nickName'] = $row['nick'];

    $url = "http://" . $conf['domain-img'] . '/';
    $userInfo['pic'] = $row['pic'] ? $url . $row['pic'] : 'http://dev.huanpeng.com/main/static/img/userface.png'; // 'http://i1.tietuku.com/596664c92b007bbb.jpg';

    $userInfo['level'] = $row['level'];
    $userInfo['integral'] = $row['integral'];
    $userInfo['readsign'] = $row['readsign'];
    $userInfo['hpbean'] = $row['hpbean'];
    $userInfo['hpcoin'] = $row['hpcoin'];
    $userInfo['levelIntegral'] = $levelIntegral;
    return $userInfo;
}

$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$uid = isset($_COOKIE['_uid']) ? (int) $_COOKIE['_uid'] : 0;
$enc = isset($_COOKIE['_enc']) ? trim($_COOKIE['_enc']) : '';
$userInfo = getUserInfo2($uid, $enc, $db, $conf);
$userInfo = json_encode($userInfo);
echo $userInfo;
exit;

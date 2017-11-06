<?php

include '../init.php';
$db = new DBHelperi_huanpeng();
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
if ($uid) {
    $userInfo = getUserInfo($uid, $db);
    $userInfo[0]['pic'] = $userInfo[0]['pic'] ? ('http://' . $conf['domain-img'] . $userInfo[0]['pic']) : DEFAULT_PIC;
    echo json_encode($userInfo);
}
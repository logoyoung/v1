<?php

include '../includeAdmin/init.php';
include INCLUDE_DIR . 'Admin.class.php';
if ((int) $_COOKIE['admin_uid'] && $_COOKIE['admin_enc'] && $_COOKIE['admin_type']) {
    $admin = New AdminHelp($_COOKIE['admin_uid'], $_COOKIE['admin_type']);
    if ($ret = $admin->loginError($_COOKIE['admin_enc'])) {
        header("Location:http://" . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . "/admin2/view/login.php");
        exit;
    }
}else{
     header("Location:http://" . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . "/admin2/view/login.php");
     exit;
}
?>
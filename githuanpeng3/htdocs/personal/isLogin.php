<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/5/11
 * Time: 下午3:48
 */

include_once INCLUDE_DIR."User.class.php";
$isUserLogin = true;
if(isset($_COOKIE['_uid']) && isset($_COOKIE['_enc']) && (int)$_COOKIE['_uid'] && trim($_COOKIE['_enc'])){
    $userHelp = new UserHelp($_COOKIE['_uid']);

    if($userHelp->checkStateError($_COOKIE['_enc'])){
        $isUserLogin = false;
    }
}else{
    $isUserLogin = false;
}

if(!$isUserLogin){
    include WEBSITE_TPL.'pleaseLogin.php';
//    echo file_get_contents(WEBSITE_TPL.'pleaseLogin.php');
    exit;
}
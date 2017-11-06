<?php

/**
 * 获取用户Uid
 * date 2017-03-09 09:45
 * author yandong@6rooms.com
 */
include '../../../include/init.php';
$db = new DBHelperi_huanpeng();
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
if (empty($mobile)) {
    error2(-4056, 2);
}
$mres = checkMobile($mobile);
if ($mres !== true) {
    error2(-4058, 2);
}
$res=checkMobileIsUsed($mobile, $db);
if($res){
  succ($res);
}else{
    error2(-5020, 2);
}
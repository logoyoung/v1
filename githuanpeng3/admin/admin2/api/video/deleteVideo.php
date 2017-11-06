<?php

/**
 * 删除一条已审核的录像
 * date 2016-09-20 5:20
 * anchor  yandong@6rooms.com
 */
require '../../includeAdmin/Video.class.php';
require '../../includeAdmin/Admin.class.php';

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : '';
$videoId = isset($_POST['videoId']) ? (int) $_POST['videoId'] : '2315';

//if (empty($uid) || empty($encpass) || empty($type)) {
//    error(-4013);
//}
//$adminHelp = new AdminHelp($uid, $type);
//$err = $adminHelp->loginError($encpass);
//if ($err) {
//    error($err);
//}
$videoObj = new Video();
$res=$videoObj->delVideo($videoId);
var_dump($res);
//if($res){
//    succ(array('isSuccess'=>1));
//}else{
//    error(array('isSuccess'=>0));
//}



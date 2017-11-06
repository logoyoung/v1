<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/16
 * Time: 下午2:41
 */



include '../../../include/init.php';
include INCLUDE_DIR . 'redis.class.php';
$redis=new RedisHelp();
$id = isset($_POST['versionid']) ? trim($_POST['versionid']) : '';
$name = isset($_POST['versionName']) ? trim($_POST['versionName']) : '';
$desc = isset($_POST['versionDesc']) ? trim($_POST['versionDesc']) : '';

if(!$id || !$name || !$desc){//!$_FILES['appFile']
    error(-4013);
}



//if($_FILES['appFile']['type'] != 'application/octet-stream'){
//    error(-4020);
//}

//if(!is_uploaded_file($_FILES['appFile']['tmp_name'])){
//    error('-1022');
//}


//$file = '/data/huanpeng-img/app/apk/huanpeng.apk';

//if(!move_uploaded_file($_FILES['appFile']['tmp_name'], $file)){
//    error(-6005);
//}

$data = array(
    'version' => $id,
    'versionName' => $name,
    'versionDesc' => $desc,
//    'fileName' => $file
);

setApkVersion($data, $redis);

exit(json_encode(array('isSuccess' => 1)));


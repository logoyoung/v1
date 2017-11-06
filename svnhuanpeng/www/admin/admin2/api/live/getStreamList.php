<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 17/3/6
 * Time: 上午11:33
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
//$db = new DBHelperi_admin();
//$conf = $GLOBALS['env-def'][$GLOBALS['env']];



$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
$luid = isset($_POST['luid']) ? (int) $_POST['luid'] : 0;

//if(empty($uid) || empty($uid) || empty($type) || empty($luid)){
//    return  error(-1007);
//}
//$adminHelp = new AdminHelp($uid, $type);
//$err = $adminHelp->loginError($encpass);
//if ($err) {
//    error($err);
//}
$url="http://www.huanpeng.com/api/live/getStreamList.php";
$data=array('luid'=>$luid);
$res=json_decode(CurlPost($url,$data),true);
if (!empty($res['content']['stream'])) {
    $orientation = $res['content']['orientation'];
    $stream = $res['content']['stream'];
    $liveId=$res['content']['liveID'];
    $streamList=$res['content']['streamList'];
} else {
    $orientation = '';
    $stream = '';
    $liveId='';
    $streamList=array();
}
echo json_encode(array('streamList' => array($streamList), 'orientation' => $orientation, 'stream' => $stream,'liveID'=>$liveId));

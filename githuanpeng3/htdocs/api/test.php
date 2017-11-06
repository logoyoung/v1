<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/10/24
 * Time: 下午5:07
 */

//include '../../../include2/init.php';
//
//use base\DBHelperi_huanpeng;
//
//$db = new DBHelperi_huanpeng(true);
//$sql = "select * from live where status=".LIVE;
//$res = $db->query($sql);
//
//while($row = $res->fetch_assoc()){
//    print_r($row);
//}
include ('../../include/lib/WcsHelper.class.php');
include ('../../include/lib/CDNHelper.class.php');

$server = 'rtmp://dev-urtmp.huanpeng.com/liverecord/';
$stream = 'Y-79014-5171292';
$cdn = new CDNHelper();
$publishRtmpUrl = $server . $stream;
$r = $cdn->stopCDNStream($publishRtmpUrl);
var_dump($r);
exit;

include_once  '../../include/init.php';
$db = new DBHelperi_huanpeng();
$sql = "select ctime,`keys` from liveStreamRecord  where  `keys` != ''";
$res = $db->query($sql);
$flvs = array();
while ($row = mysqli_fetch_assoc($res)){
    $flvs[] = $row;
}
echo json_encode($flvs);
exit;
include_once INCLUDE_DIR."mobileMessage.class.php";


sendMobileMsg::sendMsgCallBack($_GET['codeid']);
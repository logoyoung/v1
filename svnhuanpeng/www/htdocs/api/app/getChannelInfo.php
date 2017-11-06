<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/17
 * Time: 上午11:17
 */

include_once "../../../include/init.php";


$db = new DBHelperi_huanpeng();

$sql = "select * from admin_channel_version";
$res = $db->query($sql);
$channelBuildList = array();
$channelNameList = array();

while( $row = $res->fetch_assoc() )
{
	$channel = $row['channel'];
	$channelBuildList[$channel] = $row['build'];
	$channelNameList[$channel] = $row['channelName'];
}

$responseData = array(
	'channelBuildList'=> $channelBuildList,
	'channelNameList' => $channelNameList
);

exit(json_encode($responseData));


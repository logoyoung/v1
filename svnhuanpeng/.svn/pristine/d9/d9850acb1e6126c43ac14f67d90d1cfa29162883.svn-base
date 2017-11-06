<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/8
 * Time: 上午12:27
 */

include __DIR__.'/../../include/init.php';
include INCLUDE_DIR."LiveRoom.class.php";

$db = new DBHelperi_huanpeng(true);

use lib\LiveRoom as Roomobj;

$getLiveAuthor = function() use ( $db )
{
	$liveList = array();
	$sql = "select * from live where status=".LIVE;
	$res = $db->query($sql);
	while($row = $res->fetch_assoc())
	{
		array_push($liveList, $row['uid']);
	}
	return $liveList;
};

$upAuthorLiveRoomUserCount = function ( $luid, $viewer, $virtualViewer ) use ( $db )
{
	$date = date("Y-m-d H").":00:00";
	$utime = date("Y-m-d H:i:s");
	echo "$date===$luid===$utime===$viewer\n";
	$sql = "insert into popularoty_record (`date`,uid, popularoty,virtual, utime) VALUE('$date', $luid, $viewer, $virtualViewer,'$utime') on duplicate key 
update `count`=`count` + 1, utime='$utime', popularoty=popularoty+$viewer, virtual=virtual+$virtualViewer";
	return $db->query( $sql );
};

$liveList = $getLiveAuthor();

$result = LiveRoom::getRoomUserByLuid($liveList, $db);

$liveroom = new Roomobj(1,$db);

if( is_array( $result ) ){
	foreach ($result as $key => $value )
	{
		$luid = $value['luid'];
		$viewer = $value['total'];
		$virtualViewer = $liveroom->getLiveRoomUserCountFictitiousByLuid($luid);

		$upAuthorLiveRoomUserCount( $luid, $viewer, $virtualViewer );
	}
}



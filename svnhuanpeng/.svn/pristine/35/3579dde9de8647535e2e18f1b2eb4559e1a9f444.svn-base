<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/24
 * Time: 下午8:19
 */

include '../init.php';
$db = new DBHelperi_huanpeng();

$sql = "select gametid,name from gametype";
$res = $db->query($sql);


$arr = array();
while($row = $res->fetch_assoc()){
	$temp['gametid'] = $row['gametid'];
	$temp['gamename'] = $row['name'];

	array_push($arr, $temp);
}

exit(json_encode(array('gameTypeList' => $arr)));
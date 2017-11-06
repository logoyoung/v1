<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/30
 * Time: 下午1:22
 */


include '../../includeAdmin/init.php';
include_once INCLUDE_DIR.'AreaSearch.class.php';

$db = new DBHelperi_admin();
$area = new AreaSearch($db);


$sql = "select count(uid) as usercount, region_id from admin_user_position group by region_id";
$res = $db->query($sql);

$regionList = $area->getUserRegionList();

$ret = array();
while($row = $res->fetch_assoc()){

    array_push($ret, $row);
}

succ(array(
    'regionList' => $regionList,
    'distributed' => $ret
));
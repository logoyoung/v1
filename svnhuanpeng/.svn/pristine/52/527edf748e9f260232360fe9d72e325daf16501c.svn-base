<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/7/13
 * Time: 下午5:49
 */

include "../../includeAdmin/init.php";

$db = new DBHelperi_admin();

$size = (int)$_GET['size'] ? (int)$_GET['size'] : 10;
$page = (int)$_GET['page'] ? (int)$_GET['page'] : 1;

$last = ($page -1) *$size;
$sql = "select * from admin_app_break_report order by ctime desc limit $last,$size";
$res = $db->query($sql);

$list = array();
while($row = $res->fetch_assoc()){
    array_push($list, $row);
}

$sql = "select count(*) as total from admin_app_break_report";
$res = $db->query($sql);
$row = $res->fetch_assoc();


succ(array('list'=> $list, 'total'=>$row['total']));
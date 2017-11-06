<?php
/*  */

include '../init.php';

function myexit($id){
    echo $id;
    exit;
}
$liveid = isset($_GET['liveid'])?(int)$_GET['liveid']:0;
if(!$liveid)
    myexit(-1);
$db = new DBHelperi_huanpeng();
$sql = "SELECT `status` FROM `live` WHERE `liveid`={$liveid}";
$res = $db->query($sql);
$row = $res->fetch_row();
if(!$row)
    myexit(-1);
$status = $row[0];
if($status!=LIVE_SAVING)
    myexit(-1);
$sql = "UPDATE `live` SET `status`=".LIVE_STOP." WHERE `liveid`={$liveid} AND `status`=".LIVE_SAVING;
$res = $db->query($sql);

if(!$db->affectedRows)
    myexit(0);
myexit(1);



<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/9/22
 * Time: 上午11:41
 */

include "../../includeAdmin/init.php";
if(!$_GET['liveid']){
    error(-1013);
}

$db = new DBHelperi_admin();

$liveid = (int)$_GET['liveid'];

$sql = "select userstatic.nick as nick, live.title as title from userstatic, live where liveid=$liveid and userstatic.uid = live.uid";

$ret = $db->doSql($sql);

succ($ret[0]);
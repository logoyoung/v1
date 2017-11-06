<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/8/19
 * Time: 下午2:09
 */

include '../../init.php';
$db = new DBHelperi_huanpeng();
$sql = "select server, stream from live where status=".LIVE;

$res = $db->query($sql);
$ret = '';
while($row = $res->fetch_assoc()){
    $ret .= returnStreamText($row['server'], $row['stream']);
}

echo $ret;

function returnStreamText($server, $stream){
    $text = explode('/', $server);
    $server  = $text[0];
    return "$stream $server \r\n";
}

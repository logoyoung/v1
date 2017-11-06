<?php

include '../init.php';
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

/**
 * 获取socket server地址
 * date 2015-12-15
 * author yandong@6rooms.com
 * @param type $conf
 * @return array
 */
function getSocketServer($conf) {
    $serverList = $conf['socket'];
    shuffle($serverList);
    return jsone(array("serverList" => $serverList));
}

exit(getSocketServer($conf));

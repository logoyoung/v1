<?php

/**
 * 获取待审核,已审核,审核中,未通过录像条数
 * date  2016-9-12 11:32
 * yandong@6rooms.com
 */
require '../../includeAdmin/Video.class.php';

function getResult() {
    $videoObj = new Video();
    $wait_pass = $videoObj->waitPass();
    $pass = $videoObj->Pass();
    $pending = $videoObj->pending();
    $un_pass = $videoObj->unPass();
    return array('wait' => $wait_pass, 'pass' => $pass, 'pend' => $pending, 'un_pass' => $un_pass);
}

$data = getResult($db);
succ($data);

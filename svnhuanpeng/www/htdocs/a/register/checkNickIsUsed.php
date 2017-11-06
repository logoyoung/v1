<?php

include '../../init.php';
/**
 * 检测昵称是否被用过
 * date 2016-07-18 11:21
 * author yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();
$nick = isset($_POST['nick']) ? trim($_POST['nick']) : '';
if (empty($nick)) {
    error(-4064);
}
$nick = filterData($nick);
if (mb_strlen($nick, 'utf8') > 10 || mb_strlen($nick, 'utf8') < 2) {
    error(-4010);
}
$res = checkNickIsUsed($nick, $db);
if ($res) {
    exit(jsone(array('isSuccess' => '1')));//已被使用
} else {
    exit(jsone(array('isSuccess' => '0')));//0未被使用
}

<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/12
 * Time: 上午11:03
 */

include '../../../../include/init.php';

$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;

if(!$uid) exit(jsone(array('err'=>'-1')));

$baseInfo = getUserBaseInfo($uid, $db);


$r['info'] = get_userCertifyStatus($uid, $db);

$info = jsone($r);
exit($info);
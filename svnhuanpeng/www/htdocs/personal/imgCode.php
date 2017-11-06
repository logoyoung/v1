<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/8
 * Time: 下午4:27
 */
session_start();
include '../../include/Vcode.class.php';
$code = new Vcode(80, 35);

$_SESSION['vcode'] = implode('', $code->checkCode);
echo $code;

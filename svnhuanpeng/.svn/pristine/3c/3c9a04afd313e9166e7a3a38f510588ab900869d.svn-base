<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/30
 * Time: 下午9:47
 */

session_start();
include '../init.php';
$width = isset($_POST['w']) ? (int)($_POST['w']) : 76;
$height = isset($_POST['h']) ? (int)($_POST['h']) : 28;
$code = new Vcode($width, $height);
$_SESSION['receiveBean'] = implode('', $code->checkCode);
echo $code;
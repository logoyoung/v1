<?php
session_start();
include '../init.php';
include INCLUDE_DIR . 'Vcode.class.php';
$width = isset($_POST['w']) ? (int)($_POST['w']) : WIDTH;
$height = isset($_POST['h']) ? (int)($_POST['h']) : HEIGHT;
$code = new Vcode($width, $height);

$_SESSION['check_code'] = strtolower(implode('', $code->checkCode));
echo $code;
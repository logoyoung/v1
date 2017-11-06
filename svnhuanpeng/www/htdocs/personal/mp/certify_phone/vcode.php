<?php
session_start();
include '../../../../include/Vcode.class.php';
$code = new Vcode(80, 35);

$_SESSION['send_code'] = strtolower(implode('', $code->checkCode));
echo $code;

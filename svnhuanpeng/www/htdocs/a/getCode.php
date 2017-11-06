<?php
    session_start();
    include '../init.php';//var_dump($_SESSION['check_code']);exit;
    $code = isset($_POST['code'])?$_POST['code']:'';
    if($code!=$_SESSION['check_code'])  //var_dump($code);  var_dump($_SESSION['check_code']);exit;
        error(-4031);
    echo 0;
    exit;

<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/28
 * Time: 上午10:31
 */
include '../includeAdmin/init.php';
include_once INCLUDE_DIR."Admin.class.php";

$db = new DBHelperi_admin();
$req_int = array('group');
$req_str = array('username', 'password');

foreach($req_int as $key => $val){
    $$val = isset($_POST[$val]) ? (int)$_POST[$val] : 0;
    if(!$$val)
        error(-1007);
}

foreach($req_str as $key => $val){
    $$val = isset($_POST[$val]) ? trim($_POST[$val]) : '';
    if(!$$val)
        error(-1007);
}

if(!checkEmailFormat($username))
    error(-1008);

$ret = AdminHelp::toLoginError($username, $password, $group, $db);

if(is_array($ret))
    succ($ret);

else
    error($ret);


<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/3
 * Time: 上午11:49
 */

include '../init.php';
include INCLUDE_DIR . "User.class.php";
$db = new DBHelperi_huanpeng();
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$taskid = isset($_POST['taskID']) ? (int)$_POST['taskID'] : '';

if(!$uid || !$enc || !$taskid)
    error(-4013);

$user = new UserHelp($uid, $db);
$error = $user->checkStateError($enc);
if($error) error($error);

$bean = $user->getBeanByTask($taskid);

if($bean < 0){
    error($bean);
}

$property = $user->getProperty();

$ret = array(
    'isSuccess' => 1,
    'getNum' => $bean,
    'bean' => $property['hpbean'],
    'coin' => $property['hpcoin']
);

exit(json_encode(toString($ret)));
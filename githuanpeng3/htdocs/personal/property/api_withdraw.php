<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/22
 * Time: 上午11:33
 */

include '../../../include/init.php';

include INCLUDE_DIR.'Anchor.class.php';

$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$coin = isset($_POST['coin']) ? (int)$_POST['coin'] : 0;
$bean = isset($_POST['bean']) ? (int)$_POST['bean'] : 0;

$channel = isset($_POST['channel']) ? trim($_POST['channel']) : ''; //bank, Alipay, weChat

$anchor = new AnchorHelp($uid, $db);
if(!$uid || !$enc || !$channel)
	error(-4013);

if($loginStatus = $anchor->checkStateError($enc))
	error($loginStatus);

//检查银行卡认证状态
//检查主播认证状态

if($status = $anchor->withdraw($coin, $bean)){
	error($status);
}
exit(json_encode(array('isSuccess' => '1')));

?>

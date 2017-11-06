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

$bankid = isset($_POST['bankid']) ? (int)$_POST['bankid'] : 0;
$cardid = isset($_POST['cardid']) ? (int)$_POST['cardid'] : 0;

$channel = isset($_POST['channel']) ? trim($_POST['channel']) : ''; //bank, Alipay, weChat

$anchor = new AnchorHelp($uid, $db);
if(!$uid || !$enc || !$channel)
	error2(-4013);

if(!$cardid || !$bankid){
	error2(-4088,2);
}


if($loginStatus = $anchor->checkStateError($enc))
	error2(-4067,2);


//检查主播认证状态
if(!$anchor->isAnchor()){
	error2(-4057,2);
}

//检查银行卡认证状态
if(!$bankCardID = $anchor->checkBank($bankid,$cardid)){
	error2(-4087,2);//4086  没有绑定银行卡
}


if($status = $anchor->withdraw($coin, $bean, $bankCardID)){
	error2($status,2);
}
succ();

?>

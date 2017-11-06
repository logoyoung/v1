<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/21
 * Time: 上午10:38
 */

include '../../../include/init.php';
include INCLUDE_DIR.'Anchor.class.php';
use lib\User;
$db = new DBHelperi_huanpeng();
function getHpCoinByUid($uid,$db){
    if(empty($uid)){
        return false;
    }
    $userObj = new User($uid, $db);
    $data = $userObj->getUserProperty();
    $res = array();
    if (false !== $data) {
        $res[0]['hpbean'] = $data['bean'];
        $res[0]['hpcoin'] = $data['coin'];
        return $res;
    }else{
        return false;
    }

}
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : 0;

if(!$uid || !$enc){
	error(-4013);
}

$anchor = new AnchorHelp($uid);

if($loginStatus = $anchor->checkStateError($enc)){
	error($loginStatus);
}

$property = array();

$anchorObj = new Anchor($uid);
$anchorProperty  = $anchorObj->getAnchorProperty();
$property['coin'] = $anchorProperty['coin'];
$property['bean'] = $anchorProperty['bean'];

$property['todayCoin'] = $anchor->todayReceiveCoinCount();
$property['todayBean'] = $anchor->todayReceiveBeanCount();
$userAccount=getHpCoinByUid($uid,$db);
if(false !==$userAccount){
    $property['hpCoin'] =$userAccount[0]['hpcoin'];
}else{
    $property['hpCoin'] =0;
}

$property = toString($property);

exit(json_encode($property));
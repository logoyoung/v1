<?php
/**
 * 绑定银行卡
 * author  by Dylan
 * 2017-01-23 16:20
 */
include '../../../include/init.php';
require(INCLUDE_DIR . 'Anchor.class.php');
$db = new DBHelperi_huanpeng();


function checkBank($bankid, $db)
{
    if (empty($bankid)) {
        return false;
    }
    $res = $db->where("id=$bankid")->limit(1)->select('bank');
    if (false !== $res && !empty($res)) {
        return true;
    } else {
        return false;
    }
}

function addToBankCard($uid, $name, $bankid, $address, $mobile, $cardid,$db)
{
    if (empty($uid) || empty($name) || empty($bankid) || empty($address) || empty($mobile) || empty($cardid)) {
        return false;
    }
    $data = array(
        'uid' => $uid,
        'name' => $name,
        'bankid' => $bankid,
        'address' => $address,
        'phone' => $mobile,
        'cardid'=>$cardid
    );
    $res=$db->insert('bank_card',$data);
    if(false !==$res){
        return  true;
    }else{
        return false;
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$bankid = isset($_POST['bankID']) ? trim($_POST['bankID']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
$cardid = isset($_POST['cardID']) ? trim($_POST['cardID']) : '';
$cardid2 = isset($_POST['againCardID']) ? trim($_POST['againCardID']) : '';

if (empty($uid) || empty($enc) || empty($bankid)) {
    error2(-4013);
}
if (empty($name) || empty($address) || empty($bankid) || empty($mobile) || empty($cardid)|| empty($cardid2)) {
    error2(-4085, 2);
}
if($cardid !=$cardid2){
    error2(-4089,2);
}
$r = get_userCertifyStatus($uid, $db);
$mobile = $r['phone'];
$name = $r['identname'];

$data = filterData(array('name' => $name, 'bankid' => $bankid, 'mobile' => $mobile, 'cardid' => $cardid,'address'=>$address));
$checkMobile = checkMobile($data['mobile']);
if (true !== $checkMobile) {
    error2(-4058, 2);
}
if (mb_strlen($data['name'], 'utf8') > 10 || mb_strlen($data['name'], 'utf8') < 2) {
    error2(-4010, 2);
}
if (mb_strlen($data['address'], 'utf8') > 100 || mb_strlen($data['address'], 'utf8') < 6) {
    error2(-4081, 2);
}


$code = CheckUserIsLogIn($uid, $enc, $db);
if ($code !== true) {
    error2(-4067, 2);
}
$anchor = new AnchorHelp($uid);
$checkIsAnchor = $anchor->isAnchor(); //检测是不是主播
if (true !== $checkIsAnchor) {
    error2(-4057, 2);
}
$isExist=checkBank($bankid, $db);
if(!$isExist){
    error2(-5017,2);
}
$res=addToBankCard($uid, $name, $bankid,$address ,$mobile, $cardid,$db);
if($res){
    succ();
}else{
    error2(-5017,2);
}




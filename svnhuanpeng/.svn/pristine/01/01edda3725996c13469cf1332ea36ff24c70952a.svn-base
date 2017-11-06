<?php

include '../../../../include/init.php';
require(INCLUDE_DIR . 'Anchor.class.php');

error2(-4098,2);exit;

/**
 * 主播欢朋币兑换用户欢朋币
 * date 2016-12-15  12:09
 * author yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();

function getUserHp($uid, $db)
{
    if (empty($uid)) {
        return false;
    }
    $res = $db->field('hpbean,hpcoin')->where("uid =$uid")->select('useractive');
    if (false !== $res) {
        return $res;
    } else {
        return array();
    }
}

/*添加兑换纪录
 * @param int $uid  用户id
 * @param int $before  兑换数额
 * @param int $after   转化后数额
 * @param $db
 * @return bool
 */
function addConversion($uid, $before, $after, $db)
{
    if (empty($uid) || empty($before) || empty($after)) {
        return false;
    }
    $data = array(
        'uid' => $uid,
        'before' => $before,
        'after' => $after,
        'type' => 0
    );
    $res = $db->insert('conversion', $data);
    if (false !== $res) {
        return $res;
    } else {
        return false;
    }
}

/**添加到账单明细表
 * @param int $uid 用户 ｜ 收益人
 * @param int $cid 兑换记录表纪录id
 * @param int $before 消费
 * @param int $after  收益
 * @param $db
 * @return bool
 */
function addBilldetail($uid, $cid, $before, $after, $db)
{
    if (empty($uid) || empty($cid) || empty($before) || empty($after)) {
        return false;
    }
    $data = array(
        'customerid' => $uid,
        'purchase' => $before,
        'beneficiaryid' => $uid,
        'income' => $after,
        'type' => BILL_EXCHANGE,
        'info' =>$cid
    );
    $res = $db->insert('billdetail', $data);
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

function checkAnchorIsCompany($uid,$db){
    $res=$db->field("uid,cid")->where("uid=$uid")->limit(1)->select('anchor');
    if(false !==$res && !empty($res)){
        return $res[0]['cid'];
    }else{
        return false;
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$num = isset($_POST['number']) ? (int)($_POST['number']) : '';
if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
if (!empty($num)) {
    if (!is_numeric($num)) {
        error2(-4079, 2);
    }
    if (!is_int($num)) {
        error2(-4078, 2);
    }
} else {
    error2(-4077, 2);
}
//if (!in_array($num,array(10,20,30,100,500,1000))) {
//    error2(-4080, 2);
//}
$userHelp = new AnchorHelp($uid, $db);

if ($loginError = $userHelp->checkStateError($encpass)) {
    error2(-4067, 2);
}
$isCompanyAnchor=checkAnchorIsCompany($uid,$db);
if(!empty($isCompanyAnchor) && $isCompanyAnchor !=15){
    error2(-4097,2);
}
$coin = (int)$userHelp->getProperty()['coin'];//主播欢朋币
$CoinToBean=(int)$userHelp->exchangeToHpCoin($num);//金币转欢朋币
if ($coin < $CoinToBean) {
    error2(-5023, 2); //余额不足
}
$db->autocommit(false);
$db->query('begin');

$iscost = $userHelp->costHpCoin($CoinToBean, $coin); //减
$addcoin = intval($num * ((float)CONVERSION_RATIO));//金币换成欢朋币的数额
$add = addUserHpcoin($addcoin, $uid, $db);//加
if (!$iscost || !$add) {
    $db->rollback();
    error2(-5017); //系统错误
} else {
    $db->query('commit');
    $db->autocommit(true);
    $cid=addConversion($uid, $num, $addcoin, $db);//兑换纪录
    if($cid){
        addBilldetail($uid, $cid, $num, $addcoin, $db);//同步到账单明细表
    }
    $otherc = (int)$userHelp->getProperty()['coin'];
    $othercoin =  (int)$userHelp->exchangeToCoin($otherc);
    $otherhpcoin=getUserHp($uid, $db);
    if(!empty($othercoin)){
        $hpcoin= $otherhpcoin[0]['hpcoin'];
    }else{
        $hpcoin=0;
    }
    succ(array('coin'=>$othercoin, 'hpcoin'=>$hpcoin));
}



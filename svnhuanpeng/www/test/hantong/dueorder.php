<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/13
 * Time: 上午9:48
 */


include __DIR__ . "/../../include/init.php";

use lib\Finance;


$db    = new \DBHelperi_huanpeng();
$redis = new \RedisHelp();

$financeObj = new Finance( $db, $redis );


$uid     = 8560;
$tuid    = 8560;
$orderHB = 100;
$payHB   = $orderHB;
$desc    = jsone( [ 'desc test' ] );
$runtime = date( "Y-m-d H:i:s", time() + 20 );
$action  = Finance::GUARANTEE_CRON_ACTION_REFUND;
$otid    = 0;



//用户下单
$result = $financeObj->createDueOrder( $uid, $tuid, $orderHB,$payHB, $desc, $otid, $runtime, $action );
var_dump($result);

$orderid = $result['tid'];

//$result = $financeObj->freezeDueOrder($orderid, $desc,0,0);
//var_dump($result);


//锁定订单
$financeObj->lockDueOrder($orderid,$desc,0,0);
//冻结订单
$financeObj->freezeDueOrder($orderid,$desc,0,0);

$financeObj->unlockDueOrder($orderid,$desc,0,0);
sleep(21);

$financeObj->lockDueOrder($orderid,$desc,0,0);
//解除冻结
$financeObj->unFreezeDueOrder($orderid,$desc,0,0);

$result = $financeObj->finishDueOrder($orderid,date("Y-m-d H:i:s", time() +20),0,0);

$financeObj->unlockDueOrder($orderid,$desc,0,0);

//sleep(21);

//$result = $financeObj->unFreezeDueOrder($orderid, $desc, 0,0);
//var_dump($result);


//$result = $financeObj->unFreezeDueOrder($orderid,$desc,0,0);
//用户下单，被锁定
//$result = $financeObj->freezeDueOrder( $result['tid'], 0, 0 );

//用户下单，被冻结

//用户退单

//用户退单 被锁定

//用户退单被冻结

//用户确认订单

//用户确认订单，被锁定，

//用户确认订单，被冻结

//$runtime = date( "Y-m-d H:i:s", time() + 10 );
//$result  = $financeObj->refundDueOrder( $result['tid'], $runtime, 0, $result['uid'] );
//
//var_dump( $result );


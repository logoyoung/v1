<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/26
 * Time: 下午2:33
 */

include '../../../include/init.php';

//$db = new DBHelperi_huanpeng();
//
//$client = isset( $_POST['client'] ) ? (int)$_POST['client'] : 1;
//
//if( !in_array( $client, array( 0, 1 ) ) )
//{
//	error( -4013 );
//}
//
//function mark($time,$name){
//	return md5($time.$name);
//}
//
//if( $client == 1 )
//{
//	$URL = STATIC_IMG_PATH . 'gift/appGift/';
//	$list = array(
//		array( 'giftid'=>35,'name' => '飞船', 'color'=>'ff2676ed','bg_mark'=>mark(201706,'gifts_ufo_bg.png'),'compare'=>'','bg' => $URL . 'gifts_ufo_bg.png', 'poster' => $URL . 'gift_ufo.png', 'cost' => 6000,'pmark'=>mark(201707,'gift_ufo.png'), 'unit' => '欢朋币' ),
//		array( 'giftid'=>34,'name' => '汽车','color'=>'ff509323', 'bg_mark'=>mark(201706,'gift_car_bg.png'),'compare'=>'','bg' => $URL . 'gift_car_bg.png', 'poster' => $URL . 'gift_car.png', 'cost' => 1000,'pmark'=>mark(201707,'gift_car.png'), 'unit' => '欢朋币' ),
//		array( 'giftid'=>33,'name' => '滑板','color'=>'ffd84545', 'bg_mark'=>mark(201706,'gift_skateboarding_bg.png'),'compare'=>'','bg' => $URL . 'gift_skateboarding_bg.png', 'poster' => $URL . 'gift_skateboarding.png', 'cost' => 60,'pmark'=>mark(201707,'gift_skateboarding.png'), 'unit' => '欢朋币' ),
//		array( 'giftid'=>36,'name' => '比心','color'=>'ffd84545', 'bg_mark'=>mark(201706,'gift_skateboarding_bg.png'),'compare'=>'','bg' => $URL . 'gift_skateboarding_bg.png', 'poster' => $URL . 'gift_skateboarding.png', 'cost' => 20,'pmark'=>mark(201707,'gift_skateboarding.png'), 'unit' => '欢朋币' ),
//		array( 'giftid'=>32,'name' => '饮料', 'color'=>'fff07730','bg_mark'=>mark(201706,'gift_ufo_bg.png'),'compare'=>'','bg' => $URL . 'gift_drink_bg.png', 'poster' => $URL . 'gift_drink.png', 'cost' => 2, 'pmark'=>mark(201707,'gift_drink.png'),'unit' => '欢朋币' ),
//		array( 'giftid'=>31,'name' => '欢朋豆','color'=>'', 'bg_mark'=>'','bg' => '', 'poster' => $URL . 'gift_huandou_big.png', 'compare'=>'1','cost' => 520, 'pmark'=>mark(201707,'gift_huandou_big.png'),'unit' => '个' ),
//		array( 'giftid'=>31,'name' => '欢朋豆','color'=>'', 'bg_mark'=>'','bg' => '', 'poster' => $URL . 'gift_huandou_small.png', 'compare'=>'0','cost' => 100, 'pmark'=>mark(201707,'gift_huandou_small.png'),'unit' => '个' )
//	);
//	succ(array('list'=>$list) );
//}
//else
//{
//	succ( array() );
//}

use service\room\RoomGift;


$rule = [
	'client' => [
		'type' => 'int',
		'must' => true
	],
	'luid'   => [
		'type' => 'int',
		'must' => true
	]
];

$param = [];

if ( !checkParam( $rule, $_POST, $param ) )
{
	error2(-4013);
}

$uid    = $param['luid'];
$client = $param['client'];

$RoomGift = new RoomGift();

$RoomGift->setUid( $uid );


$giftInfo = $RoomGift->getSendGiftConfig();

$apiResponse = [];


$apiResponse['count'] = count( $giftInfo );
$apiResponse['list']  = $giftInfo;
$apiResponse['mark']  = md5( json_encode( $apiResponse ) );


//print_r(jsone($apiResponse));

succ( $apiResponse );
<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/7
 * Time: 上午11:31
 */

//include '../../../../include/init.php';

//$db = new DBHelperi_huanpeng();

//$info = file(__DIR__.'/info.php');
//$oldIntegral = json_decode( $info[1], true );
////print_r($oldIntegral);
//$calOldLevel = function( $exp ) use ( $oldIntegral )
//{
//      $level = 1;
//      foreach ($oldIntegral as $key => $val )
//      {
//              if( $exp > $val)
//              {
//                      $level = $key;
//              }
//      }
//
//      return $level;
//};
//
//var_dump($calOldLevel(1));
//
//exit();
//$levelGiftInfo = array(
//      31=>array('id'=>31,'money'=>1000,'exp'=>1),
//      32=>array('id'=>32,'money'=>2,'exp'=>2),
//      33=>array('id'=>33,'money'=>60,'exp'=>60),
//      34=>array('id'=>34,'money'=>1000,'exp'=>1000),
//      35=>array('id'=>35,'money'=>6000,'exp'=>6000)
//);

//$result = array();
//foreach ($array as $key => $value){
//      $index = $key + 1;
//      $result[$index] = $value;
//}

//echo json_encode($levelGiftInfo);
//exit;

$levelLsit = array(
	10,
	55,
	145,
	290,
	500,
	785,
	1155,
	1620,
	2285,
	3248,
	4607,
	6460,
	8905,
	12040,
	15963,
	20772,
	26565,
	33440,
	42315,
	53689,
	68061,
	85930,
	107795,
	134155,
	165509,
	202356,
	245195,
	294525,
	353855,

	425182,
	510503,
	611815,
	731115,
	870400,
	1031667,
	1216913,
	1428135,
	1667330,
	1936525,
	2245716,
	2604899,
	3024070,
	3513225,
	4082360,

	4741471,
	5500554,
	6369605,
	7358620,
	8477635,
	9743316,
	11172329,
	12781340,
	14587015,
	16606020,
	18855021,
	21350684,
	24109675,
	27148660,
	30477645,

	34116628,
	38087749,
	42406841,
	47088489,
	52145719,
	57589651,
	63429151,
	69670480,
	76316934,
	83368499

);

$result = array();
foreach ($levelLsit as $key => $val )
{
	$result[$key + 1] = $val;
}

echo json_encode($result);
exit;


$getLiveInfo = function () use ( $db )
{
	$sql = "select * from userlevel";
	$res = $db->query( $sql );

	$result = array();

	while( $row = $res->fetch_assoc() )
	{
		$result[$row['level']] = $row['integral'];
	}

	return $result;
};

$getGiftInfo = function () use ( $db )
{
	$sql = "select id,money,exp from gift";
	$res = $db->query( $sql );
	$result = array();

	while( $row = $res->fetch_assoc() )
	{
		$result[$row['id']] = $row;
	}

	return $result;
};

//print_r($getGiftInfo());
//print_r($getLiveInfo());

echo json_encode($getLiveInfo())."\n";
echo json_encode($getGiftInfo())."\n";

<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/22
 * Time: 上午10:01
 */

$path = realpath(__DIR__);
include_once $path.'/../../include/init.php';
include_once INCLUDE_DIR.'User.class.php';
include_once INCLUDE_DIR.'Anchor.class.php';

if(!$db) $db = new DBHelperi_huanpeng();

$uid = (int)$_COOKIE['_uid'];
$enc = trim($_COOKIE['_enc']);

$errno = 0;
$currentPage = '';

//登录错误
if(!$uid || !$enc){
	$errno = '-1';
} else{
	$userHelp = new UserHelp($uid, $db);
	if($userHelp->checkStateError($enc))
		$errno = '-1';
}

if(!$errno){
	$certInfo = $userHelp->getCertifyInfo();
	if(!$certInfo['phonestatus'])
		$currentPage = 'unCertPhone';
	else
		$currentPage = 'start';

	$applicationKey = "application:$uid";
	$redis = new RedisHelp();

	$isAnchor = false;

	if(RN_MODEL){
		$identstatus = (int)$certInfo['identstatus'];
		if($identstatus){
			//delete applicationKey;
			$pageList = ['1'=>'checkWait','100'=>'failed','101'=>'finish'];
			if($pageList[$identstatus])
				$currentPage = $pageList[$identstatus];

		}
	}else{
		$anchorHelp = new AnchorHelp( $uid, $db );
		if($anchorHelp->isAnchor()){
			$currentPage = 'finish';
		}
//		else
//		{
//			$currentPage = 'start';
//		}
	}


	$key = $redis->get($applicationKey);

	//检测RN_MODEL 是否为true 如果为true 开启实名认证
	if($_COOKIE['apply_anchor_mobile_code'] && $key == $_COOKIE['apply_anchor_mobile_code']){

		if( RN_MODEL )
		{
			$currentPage = 'realName';
		}
		else
		{
			hpdelCookie( 'apply_anchor_mobile_code' );
//			if( $anchorHelp->isAnchor() )
//			{
//				$currentPage = 'finish';
//			}
//			else
//			{
//				$currentPage = 'failed';
//			}
		}
	}

	$phoneNumber = $certInfo['phone'];



	if(($currentPage == 'start' || $currentPage == 'failed') && $_GET['page'] == 'phone')
		$currentPage = 'phone';


//pageList=['unCertPhone','phone','realName','checkWait','finish'];

//	$currentPage = 'phone';
//	$currentPage = 'checkWait';
	$varToJs = "currentPage = '$currentPage';phoneNumber='$phoneNumber';";

	$pageList = [
		'unCertPhone'=>'unCertPhone.php',
		'phone' => 'phone.php',
		'realName'=>'realName.php',
		'checkWait'=>'checkWait.php',
		'start'=>'start.php',
		'finish' => 'start.php',
		'failed' =>'start.php',
	];
	$include_view_path = 'view/'.$pageList[$currentPage];
}

$pageClassList = [
	'unCertPhone'=>'unCertPhone-page',
	'phone' => 'phone-page',
	'realName'=>'realName-page',
	'checkWait'=>'checkWait-page',
	'start'=>'start-page',
	'finish' => 'start-page',
	'failed' =>'start-page',
];

$pageClass = $pageClassList[$currentPage];

$loginError = !$errno ? 0 : 1;
$varErrorToJs = "loginError = $loginError;";

$isMobile = isMobile();


$mobilePage = [
    'start' => '我做主播',
    'finish' =>'我做主播',
    'failed' =>'我做主播',
    'phone' => '主播认证',
    'realName' => '主播认证',
    'checkWait' => '主播认证',
	'unCertPhone' => '认证错误'
];

$pageTitle = $mobilePage[$currentPage] ? $mobilePage[$currentPage]:'主播认证';
$varToJs .= "window.pageTitle='$pageTitle';";

?>
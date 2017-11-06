<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/11
 * Time: 下午4:45
 */
include_once "/usr/local/huanpeng/include/init.php";
include_once INCLUDE_DIR."User.class.php";

function getUNicks($uid, $db){
	$row = $db->field('nick')->where('uid=' . $uid)->select('userstatic');

	return $row[0]['nick'];
}

function get_username($uid, $db){
	$row =  $db->field('username')->where('uid=' . $uid)->select('userstatic');

	return $row[0]['username'];
}

function get_userLevel($uid, $db){
	$row = $db->field('level')->where('uid=' . $uid)->select('useractive');

	return $row[0]['level'];
}
function get_userIntegral($uid, $db){
	$row = $db->field('integral')->where('uid=' . $uid)->select('useractive');

	return $row[0]['integral'];
}

function get_userReadSign($uid, $db){
	$row = $db->field('readsign')->where('uid=' . $uid)->select('useractive');

	return $row[0]['readsign'];
}
function get_userSex($uid, $db){
	$row = $db->field('sex')->where('uid=' . $uid)->select('userstatic');

	return (int)$row[0]['sex'];
}
function get_userCertifyPhoneStatus($uid, $db){
	$row = $db->field('phone')->where('uid=' . $uid)->select('userstatic');

	if(!$row[0]['phone'])
		return 1;
	else
		return 0;
}
function get_userCertifyEmailStatus($uid, $db){
	return 0;
}
function get_userCertifyRealNameStatus($uid, $db){
	$row = $db->field('status')->where('uid=' . $uid)->select('userrealname');

	return (int)$row[0]['status'];
}
//$db = new DBHelperi_huanpeng();
//
//$uid = isset($_COOKIE['_uid']) ? (int)$_COOKIE['_uid'] : 0;
//$enc = isset($_COOKIE['_enc']) ? trim($_COOKIE['_enc']) : '';
//
//if($uid && $enc &&  checkUserState($uid, $enc, $db) === true){
//	//验证成功,判断cookie是否设置；
//	if(!isset($_COOKIE['_unick']) || $_COOKIE['_unick'] == ''){
//		$nick = getUNicks($uid, $db);
//		setcookie('_unick',urlencode($nick));
//	}
//	if(!isset($_COOKIE['_username']) || $_COOKIE['_username'] == ''){
//		$name = get_username($uid, $db);
//		setcookie('_username', urlencode($name));
//	}
//	if(!isset($_COOKIE['_uinfo']) || $_COOKIE['_uinfo'] == ''){
//		$uinfo[0] = get_userLevel($uid, $db);
//		$uinfo[1] = get_userIntegral($uid, $db);
//		$uinfo[2] = get_userReadsign($uid, $db);
//		$uinfo[3] = get_userSex($uid, $db);
//		$uinfo[4] = get_userCertifyPhoneStatus($uid, $db);
//		$uinfo[5] = get_userCertifyEmailStatus($uid, $db);
//		$uinfo[6] = get_userCertifyRealNameStatus($uid, $db);
//
//		$cookie_uinfo = implode(':', $uinfo);
//		setcookie('_uinfo', urlencode($cookie_uinfo));
//	}
//
//}


call_user_func(function(){
    $conf = $GLOBALS['env-def'][$GLOBALS['env']];
    $db = new DBHelperi_huanpeng();
    if(!isset($_COOKIE['_uid']))
    	hpsetCookie('_uid',LIVEROOM_ANONYMOUS + rand(200000000,299999999));
    if(!isset($_COOKIE['_enc']))
    	hpsetCookie('_enc','');
    if($_COOKIE['_uid'] && $_COOKIE['_enc']){
        $userHelp = new UserHelp($_COOKIE['_uid']);

        if(!$userHelp->checkStateError($_COOKIE['_enc'])){
            $info = $userHelp->getUsers();
            if(!isset($_COOKIE['_unick']) || !$_COOKIE['_unick']){
				hpsetCookie('_unick',$info['nick']);
            }
            if(!isset($_COOKIE['_uface']) || $_COOKIE['_uface'] != $info['pic']) {
				hpsetCookie('_uface', $info['pic']);
            }
            if(!isset($_COOKIE['_uproperty']) || !$_COOKIE['_uproperty']){
                $property = $userHelp->getProperty();
                $coin = $property['hpcoin'];
                $bean = $property['hpbean'];
				hpsetCookie('_uproperty',$bean.":".$coin);
            }
			if(!isset($_COOKIE['_phonestatus']))
				hpsetCookie('_phonestatus',$userHelp->getPhoneCertifyInfo()['status']);

            $baseInfo = getUserBaseInfo($_COOKIE['_uid'], $db);
            $userInfo = array($baseInfo['level'], $baseInfo['integral'],$baseInfo['readsign'],get_userSex($_COOKIE['_uid'], $db));
            $userinfo = implode(':', $userInfo);
			hpsetCookie('_uinfo',$userinfo);
        }
    }

//    print_r($_COOKIE);
});

if($_GET['login'] == 1){
	if($_GET['ref_url']){
		header("Location:".$_GET['ref_url']);
	}else{
		header("Location:".WEB_ROOT_URL);
	}
}
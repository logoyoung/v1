<?php

session_start();
include '../init.php';
include INCLUDE_DIR . 'PickBean.class.php';
include_once INCLUDE_DIR.'User.class.php';
require_once INCLUDE_DIR . 'class.geetestlib.php';

$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$luid = isset($_POST['luid'])  ? (int)$_POST['luid'] : 0;

$type = isset($_POST['event']) ? trim($_POST['event']) : '';

//exit(json_encode($_POST));

if(!$type){
	error(-4013);
}
if(!$uid || !$encpass || !$luid){
//	exit(json_encode(array('err' => -1)));
    error(-4013);
}

$code = checkUserState($uid, $encpass, $db);
if($code !== true){
	error($code);
}

$pick = new PickBean($uid, $db);

switch($type){
	case'enter':
		gb_enter($luid,$pick);
		break;
	case'uptime':
		gb_updateTime($luid, $pick);
		break;
	case'pick':
		$lvl = isset($_POST['lvl']) ? (int)$_POST['lvl'] : 0;
		$phone = get_userPhoneCertifyStatus($uid, $db);
//		if($phone['phonestatus'] == 0){
//			exit(json_encode(array('code' => -5, 'desc' => '请先认证手机')));
//		}
		gb_pick($luid,$pick,$lvl);
		break;
	default:
        error(-4013);
		break;
}

function gb_enter($luid,$pick){
	$result = $pick->enterRoom($luid);

	if(is_array($result)){
		exit(json_encode($result));
	}

	error($result);
}

function gb_updateTime($luid, $pick){
	$result = $pick->lockInTime($luid);
	if($result < 0){
		error($result);
	}
	exit(json_encode(array('isSuccess' => '1')));
}

function gb_pick($luid, $pick, $lvl){
	if(!$lvl){
        error(-4013);
	}

    if($_POST['type'] == 'gt'){
		$GtSdk = $_POST['client'] =='1' ? new GeetestLib(CAPTCHA_APP_ID, PRIVATE_APP_KEY) : new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);

        $user_id = $_SESSION['user_id'];
        if ($_SESSION['gtserver'] == 1) {
            $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $user_id);
            if (!$result) {
                error(-4031);
            }
        }else{
            if (!$GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
                error(-4031);
            }
        }
    }else{
        if(!$_POST['vcode'] || !$_SESSION['receiveBean'] || $_POST['vcode'] != $_SESSION['receiveBean']){
            error(-4031);
        }
    }

    $_SESSION['receiveBean'] = null;
	$result = $pick->pickTheBean($luid,$lvl);
	if($result > 0){
        $userHelp = new UserHelp($pick->uid);
        $propety = $userHelp->getProperty();
		$lvl = $lvl + 1;

		$arr = array(
			'isSuccess' => '1',
			'lvl' => "$lvl",
			'time' => ''.$pick->getPickRuleTime($lvl),
			'bean_count' => "$result",
			'isVip' => '0',
            'coin' => $propety['hpcoin'],
            'bean' => $propety['hpbean']
		);

		exit(json_encode($arr));
	}else{
		if($pick->isPick($lvl)){
			$curPick = $pick->getPickInfo();
			$arr = array(
				'isSuccess' => '0',
				'lvl' => "".$curPick['pickid'],
				'time' => ''.$curPick['time'],
				'isVip' => '0'
			);
			exit(json_encode($arr));
		}
	}

	error($result);
}

?>

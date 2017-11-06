<?php

include __DIR__."/../../../include/init.php";
require_once INCLUDE_DIR . 'class.geetestlib.php';
require_once INCLUDE_DIR.'bussiness_flow.fun.php';

use service\login\LoginService;
use service\event\EventManager;
use service\anchor\AnchorDataService;
use lib\Task;
use service\login\Error;
use hpBizFun;
class LoginApi
{

	const LOGIN_FAILED_MAX_LIMIT = 3;

	const ERROR_PARAM_NOT_VALID  = -4013;
	const ERROR_MOBILE_NOT_VALID = -4058;
	const ERROR_MULTIPLE_LOGIN   = -4061;
	const ERROR_SAFE_AUTH_FAILED = -4031;

	const CLIENT_ID_LIST = [ 1 ];

	private $loginService = [];

	private $_loginResult;

	private $_params = [
		'mobile'   => '',
		'password' => '',
		'client'   => 0,
		'type'     => 'gt'
	];

	private static $errorMsg = [
		self::ERROR_PARAM_NOT_VALID  => '缺少参数或者参数类型错误',
		self::ERROR_MOBILE_NOT_VALID => '请输入正确的手机号',
		self::ERROR_MULTIPLE_LOGIN   => '连续登录失败，请进行验证',
		self::ERROR_SAFE_AUTH_FAILED => '极验验证失败'
	];

	private function _getLoginServiceModel(): LoginService
	{
		if ( !$this->_params['mobile'] )
		{
			throw new Exception( 'mobile not define', -4058 );
		}

		if ( !isset( $this->loginService[$this->_params['mobile']] ) )
		{
			$this->loginService[$this->_params['mobile']] = new LoginService( $this->_params['mobile'], $this->_params['password'] );
		}

		return $this->loginService[$this->_params['mobile']];
	}

	private function _getParamsRule()
	{
		return [
			'mobile'            => [
				'must' => true,
				'type' => 'string'
			],
			'password'          => [
				'must' => true,
				'type' => 'string'
			],
			'client'            => 'int',
			'type'              => 'string',
			'geetest_challenge' => 'string',
			'geetest_validate'  => 'string',
			'geetest_seccode'   => 'string'
		];
	}

	private function _safeAuthentication()
	{
		$timer = $this->_getLoginServiceModel()->getFailedTimer();
		if ( $timer < self::LOGIN_FAILED_MAX_LIMIT )
		{
			return true;
		}


		if ( $this->_params['type'] != 'gt' )
		{
			$code = self::ERROR_MULTIPLE_LOGIN;
			render_error_json( self::$errorMsg[$code], $code, 2 );
		}

		$geeAuth = hpBizFun\checkGeetestCode( $this->_params['geetest_challenge'], $this->_params['geetest_validate'], $this->_params['geetest_seccode'], $this->_params['client'] );

		if ( !$geeAuth )
		{
			$code = self::ERROR_SAFE_AUTH_FAILED;
			render_error_json( self::$errorMsg[$code], $code, 2 );
		}

		return true;
	}

	private function _init()
	{
		$rule = $this->_getParamsRule();

		$param = $_POST;

		if ( !checkParam( $rule, $param, $this->_params ) )
		{
			$code = self::ERROR_PARAM_NOT_VALID;
			render_error_json( self::$errorMsg[$code], $code, 2 );
		}

		$userNameStatus = checkMobile( $this->_params['mobile'] );
		if ( $userNameStatus !== true )
		{
			$code = self::ERROR_MOBILE_NOT_VALID;
			render_error_json( self::$errorMsg[$code], $code, 2 );
		}
	}

	private function _isAppClient()
	{
		$client = intval( $this->_params['client'] );
		if ( in_array( $client, self::CLIENT_ID_LIST ) )
		{
			return true;
		}

		return false;
	}

	private function _doTask()
	{

		if ( false === $this->_isAppClient() )
		{
			return true;
		}

		$uid    = $this->_loginResult['uid'];
		$taskID = Task::TASK_APP_FIRST;

		$task = new Task( $uid );

		if ( $task->_isTaskFinish( $taskID ) )
		{
			return true;
		}

		return $task::synchroTask( $uid, $taskID );
	}

	private function _setClientCookie()
	{
//		$this->_getLoginServiceModel()->getFailedTimer();
		if($this->_loginResult['uid'] && $this->_loginResult['encpass'])
		{
			setUserLoginCookie($this->_loginResult['uid'], $this->_loginResult['encpass']);
		}
	}

	private function _syncMsg()
	{
		$data = [
			'uid' => $this->_loginResult['uid'],
			'encpass' => $this->_loginResult['encpass']
		];

		$url = WEB_ROOT_URL."api/other/checkUserExistNewMsg.php";

		curl_post($data,$url);
	}

	public function login()
	{
		$this->_init();

		$this->_safeAuthentication();

		$loginStatus        = $this->_getLoginServiceModel()->doLogin();
		$this->_loginResult =
		$result = $this->_getLoginServiceModel()->getResult();
		if ( true !== $loginStatus )
		{
			$timer = $this->_getLoginServiceModel()->getFailedTimer();

			if ( $result['error_code'] == Error::ERR_PASSWD && $timer >= self::LOGIN_FAILED_MAX_LIMIT )
			{
				$code = self::ERROR_MULTIPLE_LOGIN;
				//密码错误 但是需要提示 验证码错误 来解决提示问题
//				render_error_json( self::$errorMsg[$code], $code, 2 );
				render_error_json($result['error_desc'], $code,2);
			}
			else
			{
				render_error_json( $result['error_desc'], $result['error_code'],2 );
			}
		}

		$uid     = $this->_loginResult['uid'];
		$encpass = $this->_loginResult['encpass'];

		//todo failed log
		$this->_doTask();

		$this->_setClientCookie();

		$this->_syncMsg();

		$anchorObj = new AnchorDataService();
		$anchorObj->setUid( $uid );

		$isAnchor = $anchorObj->isAnchor() ? '1' : '0';

		$evnet = new EventManager();
		$evnet->trigger( EventManager::ACTION_USER_LOGIN, [ 'uid' => $uid ] );

		render_json( [ 'uid' => $uid, 'encpass' => $encpass, 'isAnchor' => $isAnchor ] );
	}

}


$loginApi = new LoginApi();

$loginApi->login();

exit();

session_start();
include '../../../include/init.php';
require_once INCLUDE_DIR . 'class.geetestlib.php';
require_once INCLUDE_DIR.'bussiness_flow.fun.php';
//use service\anchor\AnchorDataService;
//use hpBizFun;
//use service\event\EventManager;
//use service\user\UserAuthService;

$db = new DBHelperi_huanpeng();
$redisObj = new RedisHelp();

$passWord = isset($_POST['password']) ? trim($_POST['password']) : '';
$userName = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
$client = isset($_POST['client']) ? trim($_POST['client']) : '';
$type   = isset($_POST['type'])   ? trim($_POST['type']) : '';
if (empty($userName) || empty($passWord)) {
    error2(-4013, 2);
}
$userNameRes = checkMobile($userName);
if (true !== $userNameRes) {
    error2(-4058, 2);
}
$passWord = filterData($passWord);
$mkey = "LogInNumber:$userName";
if ($redisObj->get($mkey) >= 3) {//连续登录三次失败,开启验证码校验
    $conf = $GLOBALS['env-def'][$GLOBALS['env']];
//    setcookie('_login_identCode_open',1,0,'/main', $conf['domain']);
    if ($type == 'gt') {
//        $GtSdk = $client == '1' ? new GeetestLib(CAPTCHA_APP_ID, PRIVATE_APP_KEY) : new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
//        $user_id = $_SESSION['user_id'];
//        if ($_SESSION['gtserver'] == 1) {
//            $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $user_id);
//            if (!$result) {
//                error2(-4031, 2);
//            }
//        } else {
//            if (!$GtSdk->fail_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'])) {
//                error2(-4031, 2);
//            }
//        }
        if( !hpBizFun\checkGeetestCode( $_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $client, $redisObj ) )
        {
            error2( -4031, 2 );
        }
    } else {
        error2(-4061, 2);
    }
}
$row = $db->field('uid,password')->where("phone =$userName")->select('userstatic');
if (empty($row)) {
    error2(-4059, 2);
} else {
    //验证密码
    if ($row[0]['password'] === md5password($passWord)) {

        $auth = new UserAuthService();
        $auth->setUid($row[0]['uid']);
        //校验用户是否被封禁
        if($auth->checkDisableLoginStatus() !== true)
        {
            //获取校验结果
            $result    = $auth->getResult();
            //错误码
            $errorCode = $result['error_code'];
            //错误消息
            $errorMsg  = $result['error_msg'];
            //假如是封禁的，可以获取禁时间
            $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
            write_log("notice|uid:{$row[0]['uid']};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}",'user_login_access');
            render_error_json($errorMsg,$errorCode,2);
        }

        $staticData = array('encpass' => md5(md5($passWord)));
        $activeData = array('lip' => ip2long(fetch_real_ip($lport)), 'lport' => $lport, 'ltime' => get_datetime());
        $staticRes = $db->where('uid=' . $row[0]['uid'] . '')->update('userstatic', $staticData);
        $activeRes = $db->where('uid=' . $row[0]['uid'] . '')->update('useractive', $activeData);
        if ($staticRes && $activeRes) { //同步任务
            if (in_array($client, array(1))) {
                $keys = "IsFirstLoginfromApp:" . $row[0]['uid'];
                $res = $redisObj->get($keys);
                if (!$res) {
                    $redisObj->set($keys, 1); //设置标志
                    synchroTask($row[0]['uid'], 36, 0, 200, $db); //同步到task表中
                }
            }
            $redisObj->del($mkey); //清空登录计数
            setcookie($mkey, '', time() - 1);
            curl_post(array('uid' => $row[0]['uid'], 'encpass' => $staticData['encpass']), WEB_ROOT_URL . "api/other/checkUserExistNewMsg.php");//同步系统消息(待优化)
            setUserLoginCookie($row[0]['uid'], $staticData['encpass']);
            $anchorDataService = new AnchorDataService();
            $anchorDataService->setUid($row[0]['uid']);
            $isAnchor = $anchorDataService->isAnchor() ? '1' : '0';

            $event = new EventManager();
            $event->trigger(EventManager::ACTION_USER_LOGIN,[ 'uid' => $row[0]['uid'] ]);
            $event = null;

            succ(array('uid' => $row[0]['uid'], 'encpass' => $staticData['encpass'],'isAnchor' => $isAnchor));
        } else {
            error2(-5017);
        }
    } else {
        $redisObj->increment($mkey); //登录失败计数
        $number = $redisObj->get($mkey);
        $redisObj->expire($mkey, 60);//先设置为60秒
        setcookie($mkey, $number);
        if ($number >= 3) {
            error2(-4061, 2);
        } else {
            error2(-996, 2);
        }
    }
}



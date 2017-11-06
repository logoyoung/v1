<?php

/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/4
 * Time: 16:36
 */

use service\event\EventManager;
use service\anchor\AnchorDataService;
use lib\Task;


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
		self::ERROR_MULTIPLE_LOGIN   => '连续登录失败，请输入验证码',
		self::ERROR_SAFE_AUTH_FAILED => '验证码错误'
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

		$geeAuth = hpBizFun\checkGeetestCode( $this->_params['geetest_challenge'], $this->_params['geetest_validate'], $this->_params['geetest_seccode'] );

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
		if ( !in_array( $client, self::CLIENT_ID_LIST ) )
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

			if ( $result['error_code'] == Error::ERR_PASSWD && $timer >= $timer )
			{
				$code = self::ERROR_MULTIPLE_LOGIN;
				render_error_json( self::$errorMsg[$code], $code, 2 );
			}
			else
			{
				render_error_json( $result['error_desc'], $result['error_code'] );
			}
		}

		$uid     = $this->_loginResult['uid'];
		$encpass = $this->loginService['encpass'];

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
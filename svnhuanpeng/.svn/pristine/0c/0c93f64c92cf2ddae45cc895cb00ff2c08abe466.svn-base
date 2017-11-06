<?php
namespace service\login;

/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/1
 * Time: 14:28
 */

use lib\User;
use lib\login\Login;
use service\common\AbstractService;
use service\user\UserAuthService;
use service\login\LoginUserData;
use service\login\Timer;

class LoginService extends AbstractService
{
	private $inputPhone;
	private $inputPassword;

	private $loginUserData;
	private $error;
	private $timerObj;
	private $oauth;

	public function __construct( int $phone, string $password )
	{
		$this->inputPhone    = $phone;
		$this->inputPassword = md5password( $password );

		$this->timerObj      = Timer::getInstance( $this->inputPhone );
		$this->oauth         = new UserAuthService();
		$this->error         = new Error();
		$this->loginUserData = new LoginUserData();
	}


	public function getErrorObj()
	{
		return $this->error;
	}

	private function checkPhone()
	{
		$userInfo = User::getUserLoginDataByPhone( $this->inputPhone );
		if ( is_array( $userInfo ) && $userInfo['uid'] && $userInfo['password'] )
		{
			$this->loginUserData->setUid( $userInfo['uid'] );
			$this->loginUserData->setPassword( $userInfo['password'] );
			$this->loginUserData->setEncpass( $userInfo['encpass'] );

			return true;
		}
		else
		{
			return false;
		}
	}

	private function checkPassword()
	{
		if ( !$this->checkPhone() )
		{
			return false;
		}

		if ( $this->loginUserData->getPassword() !== $this->inputPassword )
		{
			return false;
		}

		return true;
	}


	private function oauth()
	{
		$this->oauth->setUid( $this->loginUserData->getUid() );

		$loginStatus = $this->oauth->checkDisableLoginStatus();

		if ( $loginStatus !== true )
		{
			return false;
		}

		return true;
	}

	private function success( $getToken = false )
	{

		$loginDao = new Login();
		$loginDao->setUid( $this->loginUserData->getUid() );

		$loginResult = $loginDao->login();

		if ( $loginResult === true )
		{
			$this->timerObj->clear();

			if ( $getToken )
			{
				$token = $loginDao->getToken();
				$this->loginUserData->setEncpass( $token );
			}

			return true;
		}
		else
		{
			$this->error->set( Error::ERR_SYSTEM );

			return false;
		}

	}

	private function failed()
	{
		$this->timerObj->add();
	}


	public function doLogin()
	{
		$this->getErrorObj()->clear();

		$error = 0;
		$desc  = '';

		if ( !$this->checkPassword() )
		{
			$error = Error::ERR_PASSWD;
		}

		if ( !$error )
		{
			if ( !$this->oauth() )
			{
				$error = $this->oauth->getResult();
				$error = $error['error_code'];
				$desc  = $error['error_msg'];
			}
		}

		if ( !$error )
		{
			return $this->success();
		}
		else
		{
			$this->error->set( $error, $desc );

			$this->failed();

			return false;
		}

	}

	public function doLoginWithUid( $uid )
	{
		$this->getErrorObj()->clear();

		$error = 0;
		$desc  = '';

		$this->loginUserData->setUid( $uid );

		if ( !$this->oauth() )
		{
			$error = $this->oauth->getResult();
			$error = $error['error_code'];
			$desc  = $error['error_msg'];
		}

		if( !$error )
		{
			$this->success(true);

			return true;
		}
		else
		{
			$this->error->set($error, $desc);
			$this->failed();

			return false;
		}

	}

	public function getFailedTimer()
	{
		return intval( $this->timerObj->get() );
	}

	public function getResult()
	{
		$data = [
			'error_code' => $this->error->getCode(),
			'error_desc' => $this->error->getDesc(),
			'encpass'    => $this->loginUserData->getEncpass(),
			'uid'        => $this->loginUserData->getUid()
		];

		return $data;
	}

	public function getError()
	{
		return $this->error->get();
	}

	/**
	 * @param string $inputPassword
	 */
	public function setInputPassword( string $inputPassword )
	{
		$this->inputPassword = $inputPassword;
	}
}


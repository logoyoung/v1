<?php
namespace service\due\rongCloud;

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/6
 * Time: 下午12:02
 */
use lib\due\rongCloud\RongUser;
use service\user\UserDataService;

class RongUserService
{

	private $_uid;
	private $_rongServiceObj;
	private $_rongUserObj;
	
	const ERROR_CODE_01 = 1010; //融云注册token失败码


	public function __construct( $uid )
	{
		$this->_uid            = $uid;
		$this->_rongServiceObj = RongCloudService::getInstance();
		$this->_rongUserObj    = new RongUser( $this->_uid );
	}

	public function getToken()
	{
		$token = $this->_rongUserObj->getToken();
		if ( $token )
		{
			return $token;
		}
		else
		{
			$token = $this->_getToken();
		}

		return $token;
	}

	private function _getToken()
	{
// 		$user   = new User( $this->_uid );
// 		$detail = $user->getUserInfo();
	    $userObj = new UserDataService();
	    $detail = $userObj->setUid($this->_uid)->batchGetUserInfo();
	    
		$token = $this->_rongServiceObj->getToken( $this->_uid, $detail['nick'], $detail['pic'] );
		
		$token = json_decode( $token );
		if ( $token->code == 200 )
		{
			$this->_rongUserObj->setToken( $token->token );
			return $token->token;
		}
		elseif($token->code == 2007)
		{ 
		    $log    = "error_code:2007;msg:{$token->errorMessage}|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__;
		    write_log($log);
			return ['code'=>2007,'data'=>$token->errorMessage];
		}else{
		    $log    = "error_code:".self::ERROR_CODE_01.";msg:获取融云token发生未知错误|class:" . __CLASS__ . ';func:' . __FUNCTION__.';line:'.__LINE__;
		    write_log($log);
		    return ['code'=>self::ERROR_CODE_01,'data'=>"获取融云token发生未知错误"];
		}
	} 
}



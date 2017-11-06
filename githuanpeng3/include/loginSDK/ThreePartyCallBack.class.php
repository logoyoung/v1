<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/5/2
 * Time: 14:52
 */

/**
 * 三方登录回调
 */

//共用库

include_once INCLUDE_DIR . 'loginSDK/qq/qqConnectAPI.php';
include_once INCLUDE_DIR . "User.class.php";
include_once INCLUDE_DIR . "redis.class.php";
include_once INCLUDE_DIR . 'loginSDK/ThreePartyLogin.php';
//qq
include_once INCLUDE_DIR . 'loginSDK/qq/qqConnectAPI.php';
//weibo
include INCLUDE_DIR . 'loginSDK/weibo/config.php';
include INCLUDE_DIR . 'loginSDK/weibo/saetv2.ex.class.php';


class ThreePartyCallBack
{
	private $_accessToken;
	private $_openID;
	private $_orderList = [ 'login' => 'login', 'bind' => 'bind', 'share-login' => 'share-login' ];
	private $_order;
	private $_channelList = [ 'qq' => 'qq', 'weixin' => 'wechat', 'weibo' => 'weibo' ];
	private $_channel;
	private $_clientList = [ 'android' => 'android', 'ios' => 'ios', 'web' => 'web' ];
	private $_client;
	private $_threeCookie = [];
	private $_threeGet = [];
	private $_db = null;

	//weixin
	const WEIXIN_APP_ID           = 'wx8025f0afdf08a83f';
	const WEIXIN_APP_SECRET       = '77db2db8fe919c11aa27fba0a0c5f3de';
	const WEIXIN_REDIRECT_URI     = WEB_PERSONAL_URL . 'oauth/signin/weixin/index.php';//urlencode
	const WEIXIN_RESPONSE_TYPE    = 'code';
	const WEIXIN_SCOPE            = 'snsapi_login';
	const WEIXIN_TOKEN_URL        = 'https://api.weixin.qq.com/sns/oauth2/access_token';
	const WEIXIN_ACCESS_URL       = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';
	const WEIXIN_USERINFO_URL     = 'https://api.weixin.qq.com/sns/userinfo';
	const WEIXIN_CLIENT_LOGIN_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';
	const WEIXIN_WEB_LOGIN_URL    = 'https://open.weixin.qq.com/connect/qrconnect';
	const WEIXIN_EXPRESS          = 1200;
	//weibo
	const WEIBO_ACCESS       = '3455890487';
	const WEIBO_SECRET       = '1fccc15ffed573e683e8bbbcbdeb134b';
	const WEIBO_CALLBACK_URL = WEB_PERSONAL_URL . 'oauth/signin/weibo/index.php';
	const WEIBO_ERROR        = [ 21330 => '用户或授权服务器拒绝授予数据访问权限', 21327 => 'token过期' ];


	public function __construct( $redis = null, $db = null )
	{
		if( !$db )
		{
			$db = new DBHelperi_huanpeng();
		}
		if( !$redis )
		{
			$redis = new RedisHelp();
		}
		//初始化
		$this->_db          = $db;
		$this->_redis       = $redis;
		$this->_threeGet    = $_GET;
		$this->_threeCookie = $_COOKIE;
		$this->_threeRuest  = $_REQUEST;
	}

	/**
	 * qq回调
	 */
	public function qqCallBack()
	{
		$this->_threeSet( $this->_channelList['qq'] );
		$this->_qqCheck();
		$data         = [ 'accessToken' => $this->_accessToken, 'openid' => $this->_openID ];
		$channel      = LOGIN_CHANNEL_QQ;
		$client       = LOGIN_CLIENT_WEB;
		$this->_order = $this->_threeCookie['three_notify_order'];
		if( in_array( $this->_order, $this->_orderList ) && $this->_order == $this->_threeCookie['three_startPage_order'] )
		{
			hpdelCookie( 'three_startPage_order' );
			hpdelCookie( 'three_notify_order' );
			$threeSideLogin = new ThreePartyLogin( $channel, $client, $data );
			if( $this->_order == $this->_orderList['login'] )
			{
				$result = $threeSideLogin->run( $this->_order );
			}
			else
			{
				$result = $threeSideLogin->run( $this->_order, $this->_threeCookie['_uid'], $this->_threeCookie['_enc'] );
			}
			threeSideHandleError( $this->_order, $result );
		}
		else
		{
			threeSideHandleError( $this->_order, '-5001' );
		}
	}

	/**
	 * qq初始化设置
	 */
	private function _threeSet( $channel )
	{
		if( isset( $this->_threeGet['three_notify_order'] ) )
		{
			hpsetCookie( 'three_notify_order', $this->_threeGet['three_notify_order'] );
		}
		$this->_channel = $channel;
	}

	/**
	 * qq检测
	 */
	private function _qqCheck()
	{
		$auth = new Oauth();
		if( isset( $this->_threeGet['code'] ) )
		{
			$this->_accessToken = $auth->qq_callback();
			$this->_openID      = $auth->get_openid();
		}
		else
		{
			$auth->qq_login();
			//header("Location:".WEB_ROOT_URL.'oauth.php?err='.urlencode('出错啦'));
			exit();
		}
	}


	/**
	 * weixin回调
	 */
	public function weixinCallBack()
	{
		$redisWxState = $this->_weixinSet( $this->_redis );

		if( empty( $this->_threeRuest['code'] ) || empty( $this->_threeRuest['state'] ) || ( $this->_threeRuest['state'] !== $redisWxState ) )
		{
			$this->_weixinCheck( $redisWxState, $this->_redis );
		}
		$code     = $this->_threeRuest['code'];
		$data     = [
			'appid'      => self::WEIXIN_APP_ID,
			'secret'     => self::WEIXIN_APP_SECRET,
			'code'       => $code,
			'grant_type' => 'authorization_code'
		];
		$data     = http_build_query( $data );
		$tokenUrl = self::WEIXIN_TOKEN_URL . "?$data";
		$token    = json_decode( file_get_contents( $tokenUrl ), true );
		if( !$token || isset( $token['errorcode'] ) )
		{
			exit( json_encode( $token ) );
		}
		$accessData     = [
			'appid'         => self::WEIXIN_APP_ID,
			'grant_type'    => 'refresh_token',
			'refresh_token' => $token['refresh_token']
		];
		$accessData     = http_build_query( $accessData );
		$accessTokenUrl = self::WEIXIN_ACCESS_URL . "?$accessData";
		$accessToken    = json_decode( file_get_contents( $accessTokenUrl ) );

		if( isset( $accessToken->errcode ) )
		{
			echo '<h1>错误：</h1>' . $accessToken->errcode;
			echo '<br/><h2>错误信息：</h2>' . $accessToken->errmsg;
			exit;
		}
		$userInfoData = [
			'access_token' => $accessToken->access_token,
			'openid'       => $accessToken->openid,
			'lang'         => 'zh_CN'
		];
		$userInfoData = http_build_query( $userInfoData );
		$userInfoUrl  = self::WEIXIN_USERINFO_URL . "?$userInfoData";
		$userInfo     = json_decode( file_get_contents( $userInfoUrl ) );
		if( isset( $userInfo->errorcode ) )
		{
			jumpErrPage('出错了');
			echo '<h1>错误：</h1>' . $userInfo->errcode;
			echo '<br/><h2>错误信息：</h2>' . $userInfo->errmsg;
			exit;
		}

		//执行
		$data = [
			'nickname' => $userInfo->nickname,
			'pic'      => $userInfo->headimgurl[0],
			'unionid'  => $userInfo->unionid
		];

		$this->_openID = $userInfo->openid;
		if( in_array( $this->_order, $this->_orderList ) && $this->_order == $this->_threeCookie['three_notify_order'] )
		{

			if( $this->_order == $this->_orderList['share-login'] )
			{
				UserHelp::$db2 = $this->_db;
				$isOpenidUsed  = UserHelp::isOpenidUsed( $this->_openID, $this->_channel );

				$loginRef = threeSideLogin( $this->_openID, $this->_channel, $data, $this->_db );
				$suid     = $this->_threeCookie['wx-share-user'];
				$ruid     = $loginRef['uid'];
				$luid     = $this->_threeCookie['wx-anchor-uid'];
				if( $loginRef && is_array( $loginRef ) )
				{
					setUserLoginCookie( $loginRef['uid'], $loginRef['encpass'], 30 );
					if( UserHelp::getUserEncpass( $suid ) && !$isOpenidUsed )
					{
						if( $suid && $ruid && $suid != $ruid )
						{
							$inviteResult = inviteRecord( $suid, $ruid, $luid, $this->_db );
						}
					}
					$sessionRet = 'success';
				}
				else
				{
					$sessionRet = 'failed';
				}
				$reqData = array(
					'u'       => $luid,
					'channel' => 'wechat_callback',
					'suid'    => $suid
				);
				$this->clearWxCache( $this->_redis );
				header( 'Location:' . WEB_ROOT_URL . 'h5share/live.php?' . http_build_query( $reqData ) );
			}
			elseif( $this->_order == $this->_orderList['login'] )
			{
				$this->clearWxCache( $this->_redis );
				threeSideHandleError( $this->_order, threeSideLogin( $this->_openID, $this->_channel, $data, $this->_db ) );
			}
			else
			{
				$this->clearWxCache( $this->_redis );
				threeSideHandleError( $this->_order, threeSideBind( $this->_openID, $this->_channel, $data, $this->_db ) );
			}
		}

	}


	private function _weixinSet( $redis )
	{
		if( isset( $this->_threeGet['three_notify_order'] ) )
		{
			hpsetCookie( 'three_notify_order', $this->_threeGet['three_notify_order'] );
		}
		if( $this->_threeGet['three_notify_order'] == 'share-login' )
		{
			if( $this->_threeGet['suid'] )
			{
				hpsetCookie( 'wx-share-user', (int)$this->_threeGet['suid'] );
			}
			if( $this->_threeGet['luid'] )
			{
				hpsetCookie( 'wx-anchor-uid', (int)$this->_threeGet['luid'] );
			}
		}
		$this->_channel = $this->_channelList['weixin'];
		$this->_order   = $this->_threeCookie['three_startPage_order'];
		$redisCookieKey = '_three_' . $this->_order . '_key';
		$wxStateKey     = $this->_threeCookie[$redisCookieKey];

		if( !empty( $wxStateKey ) )
		{
			$redisWxState = $redis->get( $wxStateKey );
			if( !$redisWxState )
			{
				$redisWxState = md5( uniqid( rand(), true ) );
				$redis->set( $wxStateKey, $redisWxState, self::WEIXIN_EXPRESS );
			}
			else
			{
				//mylog('redis value is set',WEIXIN_LOG);
				//todo
			}
		}
		else
		{
			$wxStateKey = md5( time() . rand( 10, 1000000 ) );
			hpsetCookie( $redisCookieKey, $wxStateKey, self::WEIXIN_EXPRESS );

			$redisWxState = md5( uniqid( rand(), true ) );
			$redis->set( $wxStateKey, $redisWxState, self::WEIXIN_EXPRESS );
		}

		return $redisWxState;
	}


	private function _weixinCheck( $redisWxState, $redis )
	{
		if( $this->_threeRuest['state'] )
		{
			$this->clearWxCache( $redis );
			if( $this->_threeRuest['state'] == $redisWxState )
			{
				threeSideHandleError( 'login', -4069 );
			}
			else
			{
				threeSideHandleError( 'login', -4070 );
			}
		}
		else
		{
			$data        = [
				'appid'         => self::WEIXIN_APP_ID,
				'redirect_uri'  => urlencode( self::WEIXIN_REDIRECT_URI ),
				'response_type' => self::WEIXIN_RESPONSE_TYPE,
				'scope'         => self::WEIXIN_SCOPE,
				'state'         => $redisWxState
			];
			$loginParams = http_build_query( $data );
			if( $this->_threeRuest['isClient'] )
			{
				$loginUrl = self::WEIXIN_CLIENT_LOGIN_URL . "?{$loginParams}#wechat_redirect";
			}
			else
			{
				$loginUrl = self::WEIXIN_WEB_LOGIN_URL . "?{$loginParams}";
			}
			header( 'Location:' . $loginUrl );
		}
	}


	/**
	 * weibo回调
	 */
	public function weiboCallBack()
	{
		$this->_threeSet( $this->_channelList['weibo'] );
		$oauth = new SaeTOAuthV2( self::WEIBO_ACCESS, self::WEIBO_SECRET );
		$token = $this->_weiboCheck( $oauth );
		setcookie( 'weibojs_' . $oauth->client_id, http_build_query( $token ) );
		$c       = new SaeTClientV2( self::WEIBO_ACCESS, self::WEIBO_SECRET, $token['access_token'] );
		$uid_get = $c->get_uid();
		$uid     = $uid_get['uid'];
		$userMsg = $c->show_user_by_id( $uid );
		if( !empty( $userMsg['id'] ) )
		{
			$this->_order = $this->_threeCookie['three_notify_order'];
			hpdelCookie( 'three_notify_order' );
			$this->_openID = $userMsg['id'];
			$data          = [ 'nickname' => $userMsg['name'], 'pic' => $userMsg['profile_image_url'] ];
			//var_dump($this->_order);
			if( in_array( $this->_order, $this->_orderList ) && $this->_order == $this->_threeCookie['three_startPage_order'] )
			{
				if( $this->_order == $this->_orderList['login'] )
				{
					threeSideHandleError( $this->_order, threeSideLogin( $this->_openID, $this->_channel, $data, $this->_db ) );
				}
				else
				{
					threeSideHandleError( $this->_order, threeSideBind( $this->_openID, $this->_channel, $data, $this->_db ) );
				}
			}
			else
			{
				jumpErrPage('order出错了');
				exit( 'order 错误' );
			}

		}
		else
		{
			jumpErrPage('页面访问失败，请重新认证');
			exit( '页面访问失败，请从新认证' );
		}
		//todo
	}

	//weibo验证
	private function _weiboCheck( $oauth )
	{
		if( isset( $this->_threeGet['error_code'] ) )
		{
			$code = $this->_threeGet['error_code'];
			jumpErrPage('出错啦');
			exit( self::WEIBO_ERROR[$code] );
		}

		if( isset( $this->_threeRuest['code'] ) )
		{
			$keys['code']         = $this->_threeRuest['code'];
			$keys['redirect_uri'] = self::WEIBO_CALLBACK_URL;
			try
			{
				$token = $oauth->getAccessToken( 'code', $keys );
			}
			catch ( OAuthException $e )
			{
				print_r( $e );
			}
		}
		else
		{
			$loginUrl = $oauth->getAuthorizeURL( self::WEIBO_CALLBACK_URL );
			header( "Location:$loginUrl" );
			exit;
		}
		if( !$token )
		{
			jumpErrPage('token出错了');
			exit( 'token error' );
		}

		return $token;
	}


	public function httpGet()
	{

	}

	public function httpPost( $data, $url )
	{
		return curl_post( $data, $url );
	}

	public function clearWxCache( $redis )
	{
		$redisCookieKey = '_three_' . $this->_order . "_key";
		$wxStateKey     = $this->_threeCookie[$redisCookieKey];
		$redis->del( $wxStateKey );
		$cookieKeyList = [
			$redisCookieKey,
			'_three_login_key',
			'_three_login_key',
			'_three_share-login_key',
			'three_notify_order',
			'wx-share-user',
			'wx-anchor-uid',
			'three_startPage_order'
		];
		foreach ( $cookieKeyList as $val )
		{
			hpdelCookie( $val );
		}
	}
}
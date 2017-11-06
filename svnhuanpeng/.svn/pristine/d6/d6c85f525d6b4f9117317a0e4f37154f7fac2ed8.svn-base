<?php

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/25
 * Time: 上午10:15
 */
class ThreePartyLogin
{
	//qq
	private $ios_qq_appid = "1105755953";
	private $ios_qq_accessToken;
	private $ios_qq_openid;

	private $android_qq_appid       = '1105828764';
	private $android_qq_accessToken = '';
	private $android_qq_openid;

	private $web_qq_appid       = '101368626';
	private $web_qq_accessToken = '';
	private $web_qq_openid;

	private $qq_getUser       = 'https://graph.qq.com/user/get_user_info?';
	private $qq_getUnionidURL = "https://graph.qq.com/oauth2.0/me?";

	private $web_qq_channelid = '';
	private $android_qq_channelid = '';
	private $ios_qq_channelid = '';

	//weibo
	private $ios_weibo_appid = "2793407089";
	private $ios_weibo_accessToken;
	private $ios_weibo_openid;

	private $android_weibo_appid = '3366398410';
	private $android_weibo_accessToken;
	private $android_weibo_openid;

	private $weibo_getUser = 'https://api.weibo.com/2/users/show.json?';

	private $web_weibo_channelid = '';
	private $android_weibo_channelid = '';
	private $ios_weibo_channelid = '';

	//wechat
	private $ios_wechat_appid       = 'wxe17443ad004d42a6';
	private $ios_wechat_secret      = '097a507a9d71e1fe76d7ccd4e25d5f21';
	private $ios_wechat_accessToken = '';
	private $ios_wechat_openid      = '';
	private $ios_wechat_code        = '';

	private $android_wechat_appid       = 'wxd463714f5fd0b48e';
	private $android_wechat_secret      = 'e49607bda5bb646d54fe127485fd41d7';//'973e3468f032e0eaf63fb05c34b708ad';
	private $android_wechat_accessToken = '';
	private $android_wechat_openid      = '';
	private $android_wechat_code        = '';

	private $web_wechat_appid = 'wx79c0b818ca367bc6';
	private $web_wechat_secret = 'b0d516d0a423589dd638a1a9d1d2e772';//'77db2db8fe919c11aa27fba0a0c5f3de';
	private $web_wechat_accessToken = '';
	private $web_wechat_openid = '';
	private $web_wechat_code = '';


	private $web_wechat_channelid = '';
	private $android_wechat_channelid = '';
	private $ios_wechat_channelid = '';

	private $wechat_getToken = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
	private $wechat_getUser  = 'https://api.weixin.qq.com/sns/userinfo?';

	private $channel = '';
	private $client  = '';

	private $order = array( 'bind', 'login' );

	private $db = null;

	private $_data;

	private $channelList = array( LOGIN_CHANNEL_WEIBO, LOGIN_CHANNEL_WECHAT, LOGIN_CHANNEL_QQ );
	private $clientList  = array( LOGIN_CLIENT_WEB, LOGIN_CLIENT_ANDROID, LOGIN_CLIENT_IOS );

	private $errcode = 0;
	private $errmsg = '';

	public function __construct( $channel, $client, $data = [], DBHelperi_huanpeng $db = null )
	{
		if ( !in_array( $client, $this->clientList ) )
		{
			return false;
		}
		if ( !in_array( $channel, $this->channelList ) )
		{
			return false;
		}

		$this->client  = $client;
		$this->channel = $channel;

		if ( !$db )
		{
			$this->db = new DBHelperi_huanpeng();
		}
		else
		{
			$this->db = $db;
		}

		$this->_data = $data;

		if ( $channel == LOGIN_CHANNEL_WECHAT )
		{
			if ( !$this->setWechatParam( $data['code'] ) )
			{
				return false;
			}
		}
		else
		{
			$this->setParam( array(
				'accessToken' => $data['accessToken'],
				'openid'      => $data['openid'],
				'channelid'   => $data['channelid']
			) );
		}
	}

	public function run( $order )
	{
		if ( !in_array( $order, $this->order ) )
		{
			return false;
		}
		$method = $this->channel . "_" . $order;

		$param = array_splice( func_get_args(), 1 );

		return call_user_func_array( [ $this, $method ], $param );
	}

	public function setWechatParam( $code )
	{
		if ( !$code )
		{
			return false;
		}
		$data = array(
			'appid'      => $this->getParam( 'appid' ),
			'secret'     => $this->getParam( 'secret' ),
			'code'       => $code,
			'grant_type' => 'authorization_code'
		);

		$res = file_get_contents( $this->wechat_getToken . http_build_query( $data ) );
		$res = jsond( $res );
		if ( $res && $res->access_token )
		{
			$this->setParam( [ 'accessToken' => $res->access_token, 'openid' => $res->openid ] );

			return true;
		}
		else
		{
			$this->errcode = $res->errcode;
			$this->errmsg = $res->errmsg;
			
			return false;
		}
	}

	public function getError()
	{
		return [
			'error_no' => $this->errcode,
			'error_msg' => $this->errmsg
		];
	}

	public function getParamName( $name )
	{
		return $this->client . "_" . $this->channel . "_" . $name;
	}

	/**
	 * 只遍历一维数组
	 *
	 * @param      $name
	 * @param null $val
	 */
	public function setParam( $name, $val = null )
	{
		if ( is_string( $name ) )
		{
			$param        = $this->getParamName( $name );
			$this->$param = $val;
		}
		if ( is_array( $name ) )
		{
			foreach ( $name as $k => $v )
			{
				$this->setParam( $k, $v );
			}
		}

	}

	public function getParam( $name )
	{
		$param = $this->getParamName( $name );

		return $this->$param;
	}


	public function buildGetUserRequestParam()
	{
		$needle = [ 'accessToken', 'openid', 'appid' ];
		$weibo  = [ 'access_token', 'uid' ];
		$wechat = [ 'access_token', 'openid' ];
		$qq     = [ 'access_token', 'openid', 'oauth_consumer_key' ];

		$channel = $this->channel;

		foreach ( $$channel as $key => $val )
		{
			$data[$val] = $this->getParam( $needle[$key] );
		}

		return $data;
	}

	public function buildGetUserUrl()
	{
		$channel = $this->channel . '_getUser';
		$url     = $this->$channel;
		$url     = $url . http_build_query( $this->buildGetUserRequestParam() );

		return $url;
	}

	public function getQQAccessUnionid()
	{
		$params = array(
			'access_token' => $this->getParam( 'accessToken' ),
			'unionid'      => 1
		);

		$requestUrl   = $this->qq_getUnionidURL . http_build_query( $params );
		$responseData = file_get_contents( $requestUrl );//callback( {"client_id":"1105755953","openid":"5B5E9CF42D12E2632540C08F356420CD"} );
		$responseData = ltrim( $responseData, 'callback(' );
		$responseData = str_replace( ');', '', $responseData );
//		$responseData = rtrim($responseData,")");
		$responseData = trim( $responseData );
		$responseData = json_decode( $responseData, true );

		if ( !$responseData )
		{
			return false;
		}

		return $responseData['unionid'];
	}

	public function qq_login()
	{
//		return false;

		$res = jsond( file_get_contents( $this->buildGetUserUrl() ) );
		if ( $res && $res->ret == 0 )
		{
			$data = array(
				'nickname' => $res->nickname,
				'pic'      => '',//$res->figureurl_qq_1,
				'unionid'  => $this->getQQAccessUnionid()
			);

			$openid    = $this->getParam( 'openid' );
			$channelid = intval( $this->_data['channelid'] );

			write_log(__FUNCTION__."channelid is $channelid",'threeparthlogin');
			return threeSideLogin( $openid, $this->channel, $data, $this->db, $channelid );
		}
		else
		{
			return false;
		}
	}

	public function wechat_login()
	{
		$res = jsond( file_get_contents( $this->buildGetUserUrl() ) );
		if ( $res && $res->openid )
		{
			$data      = array(
				'nickname' => $res->nickname,
				'pic'      => '',//$res->headimgurl
				'unionid'  => $res->unionid
			);
			$openid    = $this->getParam( 'openid' );
			$channelid = intval( $this->_data['channelid'] );

			write_log(__FUNCTION__."channelid is $channelid",'threeparthlogin');
			return threeSideLogin( $openid, $this->channel, $data, $this->db, $channelid );
		}
		else
		{
			return false;
		}
	}

	public function weibo_login()
	{
		$stime = microtime(true);
		$res               = jsond( file_get_contents( $this->buildGetUserUrl() ) );
		$etime = microtime(true);

		$debugdata['data'] = $res;
		if ( $res && $res->id )
		{
			$data = array(
				'nickname' => $res->name,
				'pic'      => ''//$res->profile_image_url
			);

			$openid    = $this->getParam( 'openid' );
			$channelid = intval( $this->_data['channelid'] );
			write_log(__FUNCTION__."channelid is $channelid",'threeparthlogin');

			return threeSideLogin( $openid, $this->channel, $data, $this->db, $channelid );
		}
		else
		{
			return false;
		}
	}

	public function qq_bind( $uid, $enc )
	{
//		return false;

		$res = jsond( file_get_contents( $this->buildGetUserUrl() ) );
		if ( $res && $res->ret == 0 )
		{
			$data = array(
				'nickname' => $res->nickname,
				'pic'      => '',//$res->figureurl_qq_1
				'unionid'  => $this->getQQAccessUnionid()
			);

			return threeSideBind( $this->getParam( 'openid' ), $this->channel, $data, $this->db, $uid, $enc );
		}
		else
		{
			return false;
		}
	}

	public function wechat_bind( $uid, $enc )
	{
		$res = jsond( file_get_contents( $this->buildGetUserUrl() ) );
		if ( $res && $res->openid )
		{
			$data = array(
				'nickname' => $res->nickname,
				'pic'      => '',//$res->headimgurl
				'unionid'  => $res->unionid
			);

			return threeSideBind( $this->getParam( 'openid' ), $this->channel, $data, $this->db, $uid, $enc );
		}
		else
		{
			return false;
		}
	}

	public function weibo_bind( $uid, $enc )
	{
		$res = jsond( file_get_contents( $this->buildGetUserUrl() ) );
		if ( $res && $res->id )
		{
			$data = array(
				'nickname' => $res->name,
				'pic'      => ''//$res->profile_image_url
			);

			return threeSideBind( $this->getParam( 'openid' ), $this->channel, $data, $this->db, $uid, $enc );
		}
		else
		{
			return false;
		}
	}

}
<?php
namespace HP\Op;
/**请求前台数据处理类
 * Created by PhpStorm.
 * User: dong
 * Date: 17/4/21
 * Time: 下午2:27
 */
class publicRequist
{

	private static $setRate_path = '/api/public/finance/setRate.php'; //比率变更
	private static $dueRate_path = '/api/public/finance/setDueRate.php'; //比率变更
	private static $recharge_path = '/api/public/finance/innerRecharge.php'; //内部发放
	private static $rateSecretKey = '7xcayBT&Mzm*BbW4fS44zidC9tI$DMkt';//更改比率加密字符串
	private static $innerSecretKey = 'Kbx!vXI2Q^I45E!*Rms@FDaD$Tnbmbih'; //内部发放
	private static $dota_path = '/event/push'; //后台用户数据变动告知前端
	private static $message_path = '/Push/sendSiteMsg'; //发送站内信
	private static $push_path = '/Push/sendPushMsg'; //消息推送
	private static $due_dota_path = '/FinanceApi/setDueSetRate'; //陪玩比率
	private static $disable_path = '/user/ups'; //封禁/解禁用户
	private static $withdrawHandle = '/FinanceApi/withdrawHandle'; //提现处理
	private static $duecert = '/AdminResetCacheApi/resetDueSkill';//资质缓存回写
	private static $userPic = 110;//用户头像审核通过

	/**
	 * 汇率改变通知财务系统
	 *
	 * @param  array $list array('uid'=>rid) 用户ID=>记录id
	 * @param int    $rate 比率
	 * @param string $desc 描述
	 *
	 * @return bool|mixed
	 */
	public static function outside_setRate( $list, $rate, $desc )
	{
		$url = $GLOBALS['env-def'][$GLOBALS['env']]['outside-domain'] . static::$setRate_path;
		$time = time();
		$sign = buildSign( array( 'list' => $list, 'rate' => (string)$rate, 'desc' => (string)$desc, 'tm' => (int)$time ), static::$rateSecretKey, false );
		$data = array(
			'list' => $list,
			'rate' => $rate,
			'desc' => $desc,
			'tm' => $time,
			'sign' => $sign
		);
		$res = self::curl_post( $url, $data );
		return $res;
	}

	/**
	 * @param $list  array('uid'=>rid)
	 * @param $rate  比率
	 * @param $desc
	 *
	 * @return bool|mixed
	 */
	public static function outside_dueRate( $list, $rate, $desc )
	{
		$data = array( 'list' => $list, 'rate' => $rate, 'desc' => $desc );
		$data['token'] = token_create( $data, DOTA_AUTHORIZE_KEY, false );
		$res = self::curl_post( DOTA_DOMAIN . static::$due_dota_path, $data );
		return $res;
	}

	/**发送站内信
	 *
	 * @param $type  0给用户 2全站
	 * @param $title 标题
	 * @param $msg   消息
	 * @param $uid   发送的用户uid
	 *
	 * @return bool|mixed
	 */
	public static function set_message( $type, $title, $msg, $uid)
	{
		$data = array( 'type' => $type, 'title' => $title, 'msg' => $msg, 'uids' => $uid ,'adminuid'=>Admin::getUid());
		$data['token'] = token_create( $data, DOTA_AUTHORIZE_KEY, false );
		$res = self::curl_post( DOTA_DOMAIN . static::$message_path, $data );
		return $res;
	}

	/**消息推送
	 *
	 * @param $type  0给用户 2全站
	 * @param $title 标题
	 * @param $msg   消息
	 * @param $uid   发送的用户uid
	 * @param $isSiteMsg  发送站内信时同时发送消息推送
	 * @return bool|mixed
	 */
	public static function push_message( $type, $title, $msg, $uid,$isSiteMsg )
	{
		$data = array( 'type' => $type, 'title' => $title, 'msg' => $msg, 'uids' => $uid ,'adminuid'=>Admin::getUid(),'isSiteMsg'=>$isSiteMsg);
		$data['token'] = token_create( $data, DOTA_AUTHORIZE_KEY, false );
		$res = self::curl_post( DOTA_DOMAIN . static::$push_path, $data );
		return $res;
	}

	/**内部发放
	 *
	 * @param  int $uid 用户id
	 *                  $data=array(
	 *                  'uid'
	 *                  'hpbean'
	 *                  'hpcoin'
	 *                  'coin'
	 *                  'bean'
	 *                  'desc'
	 *                  'activeid'
	 *                  'recordid'
	 *                  );
	 *
	 * @return bool|mixed
	 */
	public static function outside_recharge( $data )
	{
		$url = $GLOBALS['env-def'][$GLOBALS['env']]['outside-domain'] . static::$recharge_path;
		$data['tm'] = (string)time();
		$sign = buildSign( $data, static::$innerSecretKey, false );
		$data['sign'] = $sign;
		$res = self::curl_post( $url, $data );
		return $res;
	}

	/**
	 * 事件推送
	 *
	 * @param $uids 用户id  多个用逗号隔开
	 * @param $type
	 *
	 * @return bool|mixed
	 */
	public static function askDota( $uids, $type )
	{
		$data = array( 'uid' => $uids, 'ac' => $type );
		$data['token'] = token_create( $data, DOTA_AUTHORIZE_KEY, false );
		$res = self::curl_post( DOTA_DOMAIN . static::$dota_path, $data );
		return $res;
	}

	/**
	 * 对用户进行封号
	 *
	 * @param $data 封装好的数据
	 *
	 * @return bool|mixed
	 */
	public static function disuser( $data = array() )
	{
		$data['token'] = token_create( $data, DOTA_AUTHORIZE_KEY, false );
		$res = self::curl_post( DOTA_DOMAIN . static::$disable_path, $data );
		return $res;
	}

    /**
     * 礼物修改通知
     * @param $data 封装好的数据
     * @return bool|mixed
     */
    public static function gift_update( $data = array() )
    {
        $data['ac'] = 501;
        $data['token'] = token_create( $data, DOTA_AUTHORIZE_KEY, false );
        $res = self::curl_post( DOTA_DOMAIN . static::$dota_path, $data );
        return $res;
    }

	private function curl_post( $url, $data = array() )
	{
		//对空格进行转义
		$url = str_replace( ' ', '+', $url );
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, "$url" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 5 ); //定义超时3秒钟
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
		$output = curl_exec( $ch );
		$errorCode = curl_errno( $ch );
		curl_close( $ch );
		if( 0 !== $errorCode )
		{
			return false;
		}
		return $output;
	}


	/* 	{
		orderid:'订单ID’,
		otid:’后台操作票据ID’,
		desc:’相关描述',
		type:'1',//1:成功操作，2:退款操作
	} */

	public static function withdrawHandle( $tid, $type, $otid = '1', $desc = 'desc' )
	{
		$data = array( 'orderid' => $tid, 'type' => $type, 'otid' => $otid, 'desc' => $desc );
		$data['token'] = token_create( $data, DOTA_AUTHORIZE_KEY, false );
		$res = self::curl_post( DOTA_DOMAIN . static::$withdrawHandle, $data );
		return $res;
	}

	public static function duecert($uid){
		$data = ['uid'=>$uid];
		$ret = self::curl_post(DOTA_DOMAIN . static::$duecert,$data);
		$ret = json_decode($ret);
		if(empty($ret['status']))
			return false;
		return true;
	}
}

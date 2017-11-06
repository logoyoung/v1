<?php
namespace lib;

use \DBHelperi_huanpeng;
use \RedisHelp;

//use lib\FinanceError;

/**
 * 财务支付类文件
 */

/**
 * Class Finance 欢朋财务处理类
 *
 * 包含欢朋网站欢朋币，欢朋金币的相关操作，例如，充值，送礼，兑换，提现等
 */
class Finance extends FinanceBase
{
	/**
	 *
	 */
	const TRANSITION_RESULT_FAILED = false;

	/**
	 *
	 */
	const TRANSITION_RESULT_SUCCESS = true;

	const GUARANTEE_OPERATE_ADD = '+';

	const GUARANTEE_OPERATE_SUB = '-';

	/**
	 * @var DBHelperi_huanpeng|null
	 */
	private $_db = null;
	/**
	 * @var null|RedisHelp
	 */
	private $_redis = null;

	/**
	 * @var array 类操作中，设计到的数据库表名列表
	 */
	private $_tabList = array();

	/**
	 * @var array
	 */
	private $_tabListStaticKey = array();

	/**
	 * @var array 流水账单表中，汇率的类型
	 */
	private $_statementRateType = array();
	/**
	 * @var false|string 类初始化时间｜业务执行时间
	 */
	private $_ctime = '';

//	/**
//	 * @var int 业务错误编号
//	 */
//	protected $_errno = 0;

	/**
	 * @var mixed 业务返回结果
	 */
	protected $_bizResult = '';

	/**
	 * @var null
	 */
	protected $_dbType = NULL;

//	protected $_errstr = '';

	/**
	 * @var array 作为写入流水账单事务的余额，需要手动添加以及销毁
	 */
	protected $_statementBalance = array();

	/**
	 * Finance constructor.
	 *
	 * @param null $db
	 * @param null $redis
	 * @param null $ctime
	 */
	public function __construct( $db = null, $redis = null, $ctime = null )
	{

		parent::__construct();

		if ( $redis )
		{
			$this->_redis = $redis;
		}
		else
		{
			$this->_redis = new RedisHelp();
		}

		if ( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new DBHelperi_huanpeng();
		}

		$this->_errno = 0;
//		$this->_errstr = '';

		$this->setCtime($ctime);


		$this->_statementRateType = array(
			self::STATEMENT_TYPE_SENDGIFT          => self::EXC_HB_GB,
			self::STATEMENT_TYPE_RECHARGE          => self::EXC_RMB_HB,
			self::STATEMENT_TYPE_EXCHANGE          => self::EXC_GB_HB,
			self::STATEMENT_TYPE_GD_GB             => self::EXC_GD_GB,
			self::STATEMENT_TYPE_WITHDRAW          => self::EXC_GB_RMB,
			self::STATEMENT_TYPE_INTERNAL_RECHARGE => self::EXC_HB_HB,
			self::STATEMENT_TYPE_INNERCOST         => self::EXC_HB_HB,
			self::STATEMENT_TYPE_SENDBEAN          => self::EXC_HD_GD,
			self::STATEMENT_TYPE_GETBEAN           => self::EXC_HD_HD,
			self::STATEMENT_TYPE_DUE_ORDERED       => self::EXC_DUE
		);
	}

	public static function getTimeByNatureID( $id )
	{
		//目前时间是10位数，如何确保在时间戳变为11位的时候保证正确
		$length    = 10;//strlen("".time());
		$timestamp = substr( "$id", 0, $length );
		self::_log( $timestamp );

		return date( "Y-m-d H:i:s", $timestamp );
	}

	/**
	 * @return array
	 */
	public function getTabList()
	{
		return $this->_tabList;
	}

	/**
	 * @param $ctime
	 */
	public function setCtime( $ctime )
	{
		if ( $ctime )
		{
			$this->_ctime = $ctime;
		}
		else
		{
			$this->_ctime = date( "Y-m-d H:i:s" );
		}

		//设置所需数据表表名
		$this->_tabList = $this->_getTableList( strtotime( $this->_ctime ) );

		//tabList中，不需要分表的的key值
		$this->_tabListStaticKey = $this->_getTableListStatisticKey();

		//创建本月用表// 这里需要添加强制退出，确保后续操作可以成功
		$this->_doCreateMonthTable();
	}

	public function getCtime()
	{
		return $this->_ctime;
	}

	/**
	 * 用户送礼
	 *
	 * @param int    $suid 送礼人ID
	 * @param int    $ruid 收礼人ID
	 * @param int    $hbd  消费欢朋币数量
	 * @param string $desc 送礼描述
	 *
	 * @return mixed 成功返回
	 *                 array(
	 *                hp=>123,//欢朋币数
	 *                gb=>123,//金币数
	 *                tid=>201121323123123,//单据号
	 *                ctime => 2017-01-01 11:11:11 //产生记录时间
	 *                 )
	 *               失败返回 错误代码
	 */
	public function sendGift( int $suid, int $ruid, int $hbd, string $desc, int $otid )
	{
		$hbd = $this->getInputNumber( $hbd );
		$hbd = abs( $hbd );
		$this->_sendGift( $suid, $ruid, $hbd, $desc, $otid );

		return $this->_bizResult;
	}

	/**
	 * 内部用户送礼处理流程
	 *
	 * @param int    $suid
	 * @param int    $ruid
	 * @param int    $hbd
	 * @param string $desc
	 *
	 * @return bool 成功返回 true 失败返回false
	 */
	private function _sendGift( int $suid, int $ruid, int $hbd, string $desc, int $otid )
	{
		$this->_clearError();
		$type = self::STATEMENT_TYPE_SENDGIFT;
		$rate = $this->getRate( $ruid, $this->_statementRateType[$type] );
		$rate = bcmul($rate , self::RATE_HB_GB,3);

		$shbd = -abs( $hbd );
		$sgbd = 0;
		$shdd = 0;
		$sgdd = 0;

		$rhbd = 0;
		$rgbd = bcmul(abs( $hbd ) , $rate, 3);
		$rhdd = 0;
		$rgdd = 0;

		$this->_beginTransition();

		$sBalance = $this->_getBalanceForTransition( $suid );
		$shb      = $sBalance['hb'];


		if ( $shb <= 0 || ( $shbd < 0 && $shb < $hbd ) )
		{
			$this->_setError( FinanceError::BALANCE_NOT_ENOUGH );
			$this->_setBizResult();

//			echo "余额不足，送礼失败\n";

			$this->_endTransition( self::TRANSITION_RESULT_FAILED );

			return false;
		}

		$tid = $this->_addSendRecord( $suid, $ruid, $shbd, $rgbd, $desc, $otid );

		if ( $tid )
		{
			if ( $this->_statement( $suid, $shbd, $sgbd, $shdd, $sgdd, $type, $tid, $sBalance ) )
			{
				$rBalance = $this->_getBalanceForTransition( $ruid );
				$rgb      = $rBalance['gb'];


				if ( $this->_statement( $ruid, $rhbd, $rgbd, $rhdd, $rgdd, $type, $tid, $rBalance ) )
				{
					$result = $this->_outputData( array( 'shb' => $shbd + $shb, 'rgb' => $rgbd + $rgb, 'shbd' => abs( $shbd ), 'rgbd' => abs( $rgbd ) ), $tid );
					$this->_setBizResult( $result );

					$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

					return true;
				}
				else
				{
					$this->_setError( FinanceError::STATEMENT_FAILED );
				}
			}
			else
			{
				$this->_setError( FinanceError::STATEMENT_FAILED );
			}
		}
		else
		{
			$this->_setError( FinanceError::CREATE_ORDER_FAILED );
		}

		$this->_setBizResult();
		$this->_endTransition( self::TRANSITION_RESULT_FAILED );

		return false;

	}

	public function sendPackGift(int $suid, int $ruid, int $hbd, string $desc, int $otid )
	{
		$hbd = $this->getInputNumber( $hbd );
		$hbd = abs( $hbd );
		$this->_sendPackGift( $suid, $ruid, $hbd, $desc, $otid );

		return $this->_bizResult;
	}

	private function _sendPackGift( int $suid, int $ruid, int $hbd, string $desc, int $otid )
	{
		$this->_clearError();
		$type = self::STATEMENT_TYPE_SENDGIFT;
		$rate = $this->getRate( $ruid, $this->_statementRateType[$type] );
		$rate = bcmul($rate , self::RATE_HB_GB,3);

		$shbd = -abs( $hbd );
		$sgbd = 0;
		$shdd = 0;
		$sgdd = 0;

		$rhbd = 0;
		$rgbd = bcmul(abs( $hbd ) , $rate, 3);
		$rhdd = 0;
		$rgdd = 0;

		$this->_beginTransition();

		$tid = $this->_addSendRecord( $suid, $ruid, $shbd, $rgbd, $desc, $otid, self::SEND_GIFT_TYPE_PACK );

		if( $tid )
		{
			$rBalance = $this->_getBalanceForTransition( $ruid );
			$rgb      = $rBalance['gb'];

			$sBanlace = $this->_getBalance($suid);
			$shb = $sBanlace['hb'];

			if ( $this->_statement( $ruid, $rhbd, $rgbd, $rhdd, $rgdd, $type, $tid, $rBalance ) )
			{
				$result = $this->_outputData( array( 'shb' =>$shb,  'rgb' => $rgbd + $rgb, 'shbd' => abs($shbd),'rgbd' => abs( $rgbd ) ), $tid );
				$this->_setBizResult( $result );

				$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

				return true;
			}
			else
			{
				$this->_setError( FinanceError::STATEMENT_FAILED );
			}
		}
		else
		{
			$this->_setError( FinanceError::CREATE_ORDER_FAILED );
		}

		$this->_setBizResult();
		$this->_endTransition(self::TRANSITION_RESULT_FAILED);

		return false;
	}

	/**
	 * 添加送礼记录
	 *
	 * @param int $suid
	 * @param int $ruid
	 * @param int $hbd
	 * @param int $gbd 主播收益的金币数量
	 * @param int $desc
	 *
	 * @return bool|mixed 成功返回记录ID 失败返回false
	 */
	private function _addSendRecord( int $suid, int $ruid, int $hbd, int $gbd, string $desc, int $otid, int $sendType = Finance::SEND_GIFT_TYPE_MONEY )
	{
		$data = array(
			'suid'  => $suid,
			'ruid'  => $ruid,
			'hb'    => $hbd,
			'gb'    => $gbd,
			'desc'  => $desc,
			'otid'  => $otid,
			'ctime' => $this->_ctime,
			'sendType' => $sendType
		);

		return $this->_insertRecord( $this->_tabList['sendGift'], $data );
	}

	/**
	 *
	 */
	public function sendGiftRewordElder()
	{

	}

	/**
	 *
	 */
	private function _sendGiftRewordElder()
	{

	}

	/**
	 * @param int    $suid
	 * @param int    $ruid
	 * @param int    $hdd
	 * @param string $desc
	 * @param int    $otid
	 *
	 * @return mixed
	 */
	public function sendBean( int $suid, int $ruid, int $hdd, string $desc, int $otid )
	{
		$hdd = $this->getInputNumber( $hdd );
		$hdd = abs( $hdd );

		$this->_sendBean( $suid, $ruid, $hdd, $desc, $otid );

		return $this->_bizResult;
	}

	/**
	 *
	 *
	 * @param int    $suid
	 * @param int    $ruid
	 * @param int    $hdd
	 * @param string $desc
	 *
	 * @return bool
	 */
	private function _sendBean( int $suid, int $ruid, int $hdd, string $desc, int $otid )
	{
		$this->_clearError();
		$type = self::STATEMENT_TYPE_SENDBEAN;
		$rate = $this->getRate( $suid, $this->_statementRateType[$type] );

		$shbd = 0;
		$sgbd = 0;
		$shdd = -abs( $hdd );
		$sgdd = 0;

		$rhbd = 0;
		$rgbd = 0;
		$rhdd = 0;
		$rgdd = bcmul($hdd, $rate);//abs( $hdd * $rate );

		$this->_beginTransition();

//		$sBalance = $this->_getBalance($suid);
		$sBalance = $this->_getBalanceForTransition( $suid );
		$sgd      = $sBalance['gd'];
		$shd      = $sBalance['hd'];

		//将余额塞进到事务中去 保持事务完成性
		$this->_setStatementBalance( $suid, $sBalance );
		if ( $shd <= 0 || ( $shdd < 0 && $shd < $hdd ) )
		{
			$this->_setError( FinanceError::BEAN_NOT_ENOUGH );
			$this->_setBizResult();

//			echo "余额不足，送礼失败\n";
			$this->_endTransition( self::TRANSITION_RESULT_FAILED );

			return false;
		}

		$tid = $this->_addSendBeanRecord( $suid, $ruid, $shdd, $rgdd, $desc, $otid );
		if ( $tid )
		{
			if ( $this->_statement( $suid, $shbd, $sgbd, $shdd, $sgdd, $type, $tid, $sBalance ) )
			{
//				$rBalance = $this->_getBalance( $ruid );
				$rBalance = $this->_getBalanceForTransition( $ruid );

				$rgd = $rBalance['gd'];

				$this->_setStatementBalance( $ruid, $rBalance );
				if ( $this->_statement( $ruid, $rhbd, $rgbd, $rhdd, $rgdd, $type, $tid, $rBalance ) )
				{
					$result = $this->_outputData( array( 'shd' => $shd + $shdd, 'rgd' => $rgd + $rgdd, 'shdd' => abs( $shdd ), 'rgdd' => abs( $rgdd ) ), $tid );
					if ( $suid == 73163 )
					{
						$this->_log( "result===>" . json_encode( $result ) );
						$this->_log( json_encode( [ 'beforeShd' => $shd, 'shdd' => $shdd, 'afterShd' => $shd + $shdd ] ) );
					}
					$this->_setBizResult( $result );

					$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

					return true;
				}

			}
			else
			{
				$this->_setError( FinanceError::STATEMENT_FAILED );
			}
		}
		else
		{
			$this->_setError( FinanceError::CREATE_ORDER_FAILED );

		}


		$this->_setBizResult();
		$this->_endTransition( self::TRANSITION_RESULT_FAILED );

		return false;
	}

	/**
	 * 创建一条赠送欢朋豆记录
	 *
	 * @param int    $suid
	 * @param int    $ruid
	 * @param int    $hdd
	 * @param int    $gdd
	 * @param string $desc
	 *
	 * @return bool|mixed
	 */
	private function _addSendBeanRecord( int $suid, int $ruid, int $hdd, int $gdd, string $desc, int $otid )
	{
		$data = array(
			'suid'  => $suid,
			'ruid'  => $ruid,
			'hd'    => $hdd,
			'gd'    => $gdd,
			'desc'  => $desc,
			'otid'  => $otid,
			'ctime' => $this->_ctime
		);

		return $this->_insertRecord( $this->_tabList['sendBean'], $data );
	}

	/**
	 * 根据渠道，为用户增加欢朋豆
	 *
	 * @param int    $uid
	 * @param int    $hdd
	 * @param int    $channel
	 * @param string $desc
	 *
	 * @return mixed
	 */
	public function addUserBean( int $uid, int $hdd, int $channel, string $desc, int $otid )
	{
		$hdd = $this->getInputNumber( $hdd );
		$this->_addUserBean( $uid, $hdd, $channel, $desc, $otid );

		return $this->_bizResult;
	}

	/**
	 * @param int    $uid
	 * @param int    $hd
	 * @param int    $channel
	 * @param string $desc
	 * @param int    $otid
	 *
	 * @return bool
	 */
	private function _addUserBean( int $uid, int $hd, int $channel, string $desc, int $otid )
	{
		$this->_clearError();
		$hd = abs( $hd );

		$type = self::STATEMENT_TYPE_GETBEAN;
//		$rate = $this->getRate( $uid, $this->_statementRateType[$type] );
		//增加欢朋币没有比率
		$rate = 1;
		$hbd  = 0;
		$gbd  = 0;
		$gdd  = 0;
		$hdd  = bcmul($hd, $rate);

		$this->_beginTransition();
		$balance = $this->_getBalanceForTransition( $uid );

		$tid = $this->_addUserGetBeanRecord( $uid, $hd, $channel, $desc, $otid );
		if ( $tid )
		{
			if ( $this->_statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $tid, $balance ) )
			{
				$result = $this->_outputData( $this->_getBalance( $uid ), $tid );
				$this->_setBizResult( $result );

				$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

				return true;
			}
			else
			{
				$this->_setError( FinanceError::STATEMENT_FAILED );
			}
		}
		else
		{
			$this->_setError( FinanceError::CREATE_ORDER_FAILED );
		}

		$this->_setBizResult();
		$this->_endTransition( self::TRANSITION_RESULT_FAILED );

		return false;
	}

	/**
	 * @param int    $uid
	 * @param int    $hdd
	 * @param int    $channel
	 * @param string $desc
	 * @param int    $otid
	 *
	 * @return bool|mixed
	 */
	private function _addUserGetBeanRecord( int $uid, int $hdd, int $channel, string $desc, int $otid )
	{
		$data = array(
			'uid'     => $uid,
			'hd'      => $hdd,
			'channel' => $channel,
			'desc'    => $desc,
			'otid'    => $otid,
			'ctime'   => $this->_ctime
		);

		return $this->_insertRecord( $this->_tabList['getBean'], $data );
	}

	/**
	 * 创建一条充值记录
	 *
	 * @param int    $uid
	 * @param float  $rmb         充值金额
	 * @param string $channel
	 * @param string $client
	 * @param string $refUrl
	 * @param int    $promotionID 促销ID
	 * @param string $desc
	 *
	 * @return bool|mixed 成功返回订单ID，失败返回false
	 */
	public function rechargeOrderCreate( int $uid, float $rmb, string $channel, string $client, string $refUrl, int $promotionID, string $desc, int $otid, int $orderid = 0, int $ip = 0, int $port = 0 )
	{
		$rmb = $this->getInputNumber( $rmb );
		$rmb = abs( $rmb );

		if ( !$orderid )
		{
			$orderid = $this->_getRecordID(strtotime($this->_ctime));
		}

		$result = $this->_rechargeOrderCreate( $uid, $rmb, $channel, $client, $refUrl, $promotionID, $desc, $otid, $orderid, $ip, $port );

		if ( $result > 0 )
		{
			return $result;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 创建一条充值记录
	 *
	 * @access private
	 *
	 * @param int    $uid
	 * @param int    $rmb         充值金额
	 * @param string $channel
	 * @param string $client
	 * @param string $refUrl
	 * @param int    $promotionID 促销ID
	 * @param string $desc
	 *
	 * @return bool|mixed 成功返回订单ID，失败返回false
	 */
	private function _rechargeOrderCreate( int $uid, int $rmb, string $channel, string $client, string $refUrl, int $promotionID, string $desc, int $otid, int $orderid = 0, $ip = 0, $port = 0 )
	{
		$type = self::STATEMENT_TYPE_RECHARGE;

		if ( $promotionID )
		{
			//todo 促销活动的灵活性比较高，现在这套方案不能提供全面的支撑
			$promotionInfo = $this->getPromotionInfo( $promotionID );
			if ( $result = $this->checkPromotionIsValid( $promotionInfo ) )
			{
				return $result;
			}
			//花费金钱为0的情况，类似于0元抢购，的活动
			if ( $promotionInfo['rmb'] == 0 )
			{
				$hb   = $promotionInfo['hb'];
				$rate = 0;
			}
			else
			{
				$rate = $promotionInfo['hb'] / $promotionInfo['rmb'];
				$hb   = $rmb * $rate;
			}
		}
		else
		{
			$rate = $this->getRate( $uid, $this->_statementRateType[$type] );
			$hb   = bcmul($rmb , $rate);
		}

		if ( !$ip )
		{
			$port = 0;
			$ip   = ip2long( fetch_real_ip( $port ) );
		}


		$data = array(
			'uid'            => $uid,
			'rmb'            => $rmb,
			'hb'             => $hb,
			'desc'           => $desc,
			'client'         => $client,
			'refer_url'      => $refUrl,
			'ip'             => $ip ? $ip : 0,
			'port'           => $port,
			'channel'        => $channel,
			'promotionID'    => $promotionID,
			'otid'           => $otid,
			'thrid_order_id' => $orderid,
			'ctime'          => $this->_ctime
		);


		$id = $this->_insertRecord( $this->_tabList['recharge'], $data, $orderid );

		if ( $id )
		{
			return $id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 充值成功的回调的信息录入接口，根据账单表中的欢朋币数量 更新对应账户余额
	 *
	 * @param $transactionID
	 * @param $outTradeID
	 * @param $openID
	 */
	/**
	 * 充值回调获取操作
	 *
	 * @param string $transactionID 第三方账单号码
	 * @param string $outTradeID    欢朋账单号码
	 * @param string $openID        第三方账户openID
	 *
	 * @return mixed  array(
	 *                hp=>123,//欢朋币数
	 *                gb=>123,//金币数
	 *                tid=>201121323123123,//单据号
	 *                ctime => 2017-01-01 11:11:11 //产生记录时间
	 *                 )
	 */
	public function rechargeOrderFinish( string $transactionID, string $outTradeID, string $openID, int $timeend = 0 )
	{
		$this->_rechargeOrderFinish( $transactionID, $outTradeID, $openID, $timeend );

		return $this->_bizResult;

	}

	/**
	 * 充值完成处理流程
	 *
	 * @param $transactionId 第三方账单号码
	 * @param $outTradeId    欢朋账单号码
	 * @param $openid        第三方账户openID
	 *
	 * @return bool 成功返回true 失败返回false
	 */
	private function _rechargeOrderFinish( $transactionId, $outTradeId, $openid, int $timeend = 0 )
	{
//		RechargeOrder::$orderid = $outTradeId;
//		RechargeOrder::setdb( $this->_db );
		$this->_clearError();

		$orderInfo = $this->_getRechargeOrderInfo( $outTradeId );
		if ( !$orderInfo )
		{

			$this->_log( "orderinfo get failed" );

			$this->_setError( FinanceError::ORDER_NOT_EXIST );

			return false;
		}

		$uid  = $orderInfo['uid'];
		$type = self::EXC_RMB_HB;

		//TODO  hbd 应该充值成功后计算
		$hbd = $orderInfo['hb'];
		$gbd = 0;
		//TODO: 充值成功的促销活动应该在此处体现
		$hdd = 0;
		$gdd = 0;

		//todo the redis key is should write in the RechargeOrder class
		$rechargeOrderStatusRedisKey = "recharge" . $outTradeId . "-" . $uid;
		$this->_redis->set( $rechargeOrderStatusRedisKey, 1, 600 );
		$orderStatus = $orderInfo['status'];

		if ( $orderStatus == self::RECHARGE_STATUS_FINISH || $orderStatus == self::RECHARGE_STATUS_FAILED )
		{
			//该订单已经完成
			$this->_log( "order has finished" );
			$result = $this->_outputData( $this->_getBalance( $uid ), $outTradeId );
			$this->_setBizResult( $result );

			return true;
		}
		elseif ( $orderStatus == self::RECHARGE_STATUS_CREATE )
		{
			$this->_beginTransition();
			$balance = $this->_getBalanceForTransition( $uid );

			if ( $this->_successPay( $outTradeId, $transactionId, $openid, $timeend ) )
			{
				if ( $this->_statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $outTradeId, $balance ) )
				{
					$result = $this->_outputData( $this->_getBalance( $uid ), $outTradeId );
					$this->_setBizResult( $result );

					$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

					return true;
				}
				else
				{
					$this->_log( "function statement reutrn false" );
					$this->_setError( FinanceError::STATEMENT_FAILED );
				}
			}
			else
			{
				$this->_log( "function successPay return false" );
				$this->_setError( FinanceError::RECHARGE_PAY_FAILED );
			}


			$this->_setBizResult();

			$this->_endTransition( self::TRANSITION_RESULT_FAILED );

			return false;
		}
		else
		{
			return false;
		}

	}

	/**
	 * 获取订单信息
	 *
	 * @param string $outTradeId 欢朋内部订单ID
	 *
	 * @return bool 成功返回订单信息 失败返回false
	 */
	public function _getRechargeOrderInfo( string $outTradeId )
	{

		//todo 根据内部ID 自动定义到对应表
	 	$table = $this->_getMonthTableNameById($this->_tabList['recharge'], $outTradeId);
		$sql = "select * from {$table} where id=$outTradeId";
		$res = $this->_db->query( $sql, $this->_dbType );
		if(!$res)
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log($t);
			return false;
		}
		$this->_log( $sql );
		$row = $res->fetch_assoc();
		if ( $row && is_array( $row ) )
		{
			return $row;
		}
		else
		{
			return false;
		}
	}

	public function getRechargeOrderInfo(string $outTradeId)
	{
		$row = $this->_getRechargeOrderInfo($outTradeId);
		if($row && is_array($row))
		{
			$row['hb'] = $this->getOutputNumber($row['hb']);
			$row['rmb'] = $this->getOutputNumber($row['rmb']);

			return $row;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 充值成功处理
	 *
	 * @param string $id            欢朋内部订单ID
	 * @param string $transactionID 三方返回的票据
	 * @param string $openid        三方充值账号的openID
	 *
	 * @return int 返回影响行数
	 */
	private function _successPay( string $id, string $transactionID, string $openid, int $timeend = 0 )
	{
		if ( $timeend )
		{
			$paytime = date( "Y-m-d H:i:s", $timeend );
		}
		else
		{
			$paytime = date( "Y-m-d H:i:s" );
		}

		$updateData = array(
			'thrid_order_id' => $transactionID,
			'thrid_buyer_id' => $openid,
			'status'         => self::RECHARGE_STATUS_FINISH,
			'paytime'        => $paytime
		);

		$table = $this->_getMonthTableNameById($this->_tabList['recharge'], $id);

		$sql = $this->_db->where( "id=$id" )->update( $table, $updateData, true );

		$this->_db->query( $sql, $this->_dbType );

		$this->_log( "successPay " . $sql );

		return $this->_db->affectedRows;
	}

	/**
	 * 主播申请提现
	 *
	 * @param int $uid 主播ID
	 * @param int $gb  提现金币数量
	 *
	 * @return mixed 成功返回 array(
	 *                hp=>123,//欢朋币数
	 *                gb=>123,//金币数
	 *                tid=>201121323123123,//单据号
	 *                ctime => 2017-01-01 11:11:11 //产生记录时间
	 *                 )
	 *               失败返回错误代码
	 */
	public function withdraw( int $uid, float $gb, string $desc, int $otid )
	{
		$gb = $this->getInputNumber( $gb );
		$gb = abs( $gb );

		if ( $uid == 69323 )
		{
			$this->_log( __FUNCTION__ . " uid:$uid, gbd ,$gb" );
		}

		$this->_withdraw( $uid, $gb, $desc, $otid );

		return $this->_bizResult;
	}

	/**
	 * 主播申请提现
	 *
	 * @param int $uid 主播ID
	 * @param int $gb  提现金币数量
	 *
	 * @return bool 成功返回true 失败返回false
	 */
	private function _withdraw( int $uid, int $gb, string $desc, int $otid )
	{
		$this->_clearError();

		$type = self::STATEMENT_TYPE_WITHDRAW;
		$rate = $this->getRate( $uid, $this->_statementRateType[$type] );

		$rmb = bcmul($gb , $rate);

		$hbd = 0;
		$gbd = bcmul(-abs( $gb ) , $rate);
		$hdd = 0;
		$gdd = 0;

		if ( $uid == 69323 )
		{
			$this->_log( __FUNCTION__ . " uid:$uid, rmb $rmb, gbd ,$gbd" );
		}

		$this->_beginTransition();

		$balance = $this->_getBalanceForTransition( $uid );

		if ( $balance['gb'] <= 0 || $balance['gb'] < $gb )
		{
			$this->_setError( FinanceError::BALANCE_NOT_ENOUGH );
			$this->_setBizResult();

			$this->_endTransition( self::TRANSITION_RESULT_FAILED );

			return false;
		}

		$tid = $this->_addWithdrawRecord( $uid, $gbd, $rmb, $desc, $otid, self::WITHDRAW_TYPE_WITHDRAW );
		if ( $tid )
		{
			if ( $this->_statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $tid, $balance ) )
			{
				$result = $this->_outputData( $this->_getBalance( $uid ), $tid );
				$this->_setBizResult( $result );

				$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

				return true;
			}
			else
			{
				$this->_setError( FinanceError::STATEMENT_FAILED );
			}
		}
		else
		{
			$this->_setError( FinanceError::CREATE_ORDER_FAILED );
		}

		$this->_setBizResult();

		$this->_endTransition( self::TRANSITION_RESULT_FAILED );

		return false;
	}

	public function withdrawRefund( int $uid, float $gb, string $desc, int $otid )
	{
		$gb = $this->getInputNumber( $gb );
		$this->_withdrawRefund( $uid, $gb, $desc, $otid );

		return $this->_bizResult;
	}

	private function _withdrawRefund( int $uid, int $gb, string $desc, int $otid )
	{
		$this->_clearError();

		$type = self::STATEMENT_TYPE_WITHDRAW;
		$rate = $this->getRate( $uid, $this->_statementRateType[$type] );

		$rmb = -abs( $gb * $rate );

		$hbd = 0;
		$gbd = abs( $gb );
		$hdd = 0;
		$gdd = 0;

		$this->_beginTransition();

		$balance = $this->_getBalanceForTransition( $uid );

		$tid = $this->_addWithdrawRecord( $uid, $gbd, $rmb, $desc, $otid, self::WITHDRAW_TYPE_REFUND );
		if ( $tid )
		{
			$type = self::STATEMENT_TYPE_WITHDRAWREFUND;
			if ( $this->_statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $tid, $balance ) )
			{
				$result = $this->_outputData( $this->_getBalance( $uid ), $tid );
				$this->_setBizResult( $result );

				$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

				return true;
			}
			else
			{
				$this->_setError( FinanceError::STATEMENT_FAILED );
			}
		}
		else
		{
			$this->_setError( FinanceError::CREATE_ORDER_FAILED );
		}

		$this->_setBizResult();

		$this->_endTransition( self::TRANSITION_RESULT_FAILED );

		return false;
	}

	/**
	 * 创建一条提现记录
	 *
	 * @param int    $uid
	 * @param int    $gb
	 * @param int    $rmb
	 * @param string $desc
	 * @param int    $type 1:提现，5:退款
	 *
	 * @return int|bool 成功返回订单ID，失败返回false
	 */
	private function _addWithdrawRecord( int $uid, int $gb, int $rmb, string $desc, $otid, $type )
	{
		$data = array(
			'uid'   => $uid,
			'gb'    => $gb,
			'rmb'   => $rmb,
			'desc'  => $desc,
			'ctime' => $this->_ctime,
			'otid'  => $otid,
			'type'  => $type
		);

		return $this->_insertRecord( $this->_tabList['withdraw'], $data );
	}

	/**
	 *
	 *
	 * @param int $otid
	 *
	 * @return mixed
	 */
	public function getWithdrawRecordInfo( int $otid )
	{
		$filed = [ 'gb', 'rmb', 'ctime' ];
		$where = [ 'otid' => $otid ];

		$sql = $this->_db->field( $filed )->where( $where )->select( $this->_tabList['withdraw'], true );
		$res = $this->_db->query( $sql );

		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );
		}

		$row        = $res->fetch_assoc();
		$row['gb']  = $this->getOutputNumber( $row['gb'] );
		$row['rmb'] = $this->getOutputNumber( $row['rmb'] );

		return $row;
	}

	/**
	 * 主播兑现功能
	 *
	 * @param int $uid 主播ID
	 * @param int $gb  兑换金币数量
	 *
	 * @return mixed|array 成功返回
	 *                 array(
	 *                hp=>123,//欢朋币数
	 *                gb=>123,//金币数
	 *                tid=>201121323123123,//单据号
	 *                ctime => 2017-01-01 11:11:11 //产生记录时间
	 *                 )
	 *               失败返回 错误代码
	 */
	public function exchange( int $uid, int $gb, $desc, $otid )
	{
		$gb = $this->getInputNumber( $gb );
		$gb = abs( $gb );
		$this->_exchange( $uid, $gb, $desc, $otid );

		return $this->_bizResult;
	}

	/**
	 * 主播兑换私有方法
	 *
	 * @param int $uid 主播ID
	 * @param int $gb  兑换金币数量
	 *
	 * @return bool 成功返回true 失败返回false
	 */
	private function _exchange( int $uid, int $gb, $desc, $otid )
	{
		$this->_clearError();

		$type = self::STATEMENT_TYPE_EXCHANGE;
		$rate = $this->getRate( $uid, $this->_statementRateType[$type] );

		$hbd = bcmul($gb , $rate);
		$gbd = -abs( $gb );
		$hdd = 0;
		$gdd = 0;

		$this->_beginTransition();

		$balance = $this->_getBalanceForTransition( $uid );
		$ugb     = $balance['gb'];

		if ( $ugb <= 0 || $ugb < $gb )
		{

			$this->_setError( FinanceError::BALANCE_NOT_ENOUGH );
			$this->_setBizResult();

			$this->_endTransition( self::TRANSITION_RESULT_FAILED );

			return false;
		}

		$tid = $this->_addExchangeRecord( $uid, $gb, $hbd, $desc, $otid );

		if ( $tid )
		{
			if ( $this->_statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $tid, $balance ) )
			{
				$result = $this->_outputData( $this->_getBalance( $uid ), $tid );
				$this->_setBizResult( $result );

				$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

				return true;
			}
			else
			{
				$this->_setError( FinanceError::STATEMENT_FAILED );
			}
		}
		else
		{
			$this->_setError( FinanceError::CREATE_ORDER_FAILED );
		}

		$this->_setBizResult();

		$this->_endTransition( self::TRANSITION_RESULT_FAILED );

		return false;
	}

	/**
	 * @param int    $uid
	 * @param int    $gb
	 * @param int    $hb
	 * @param string $desc
	 * @param        $otid
	 *
	 * @return bool|mixed
	 */
	private function _addExchangeRecord( int $uid, int $gb, int $hb, string $desc, $otid )
	{
		$data = array(
			'uid'   => $uid,
			'gb'    => $gb,
			'hb'    => $hb,
			'desc'  => $desc,
			'otid'  => $otid,
			'ctime' => $this->_ctime
		);

		return $this->_insertRecord( $this->_tabList['exchange'], $data );
	}

	/**
	 * 金豆兑换金币，提现流程中的一环
	 *
	 * @param int $uid  主播ID
	 * @param int $bean 提现金豆数量
	 *
	 * @return mixed|array 成功返回
	 *                 array(
	 *                hp=>123,//欢朋币数
	 *                gb=>123,//金币数
	 *                tid=>201121323123123,//单据号
	 *                ctime => 2017-01-01 11:11:11 //产生记录时间
	 *                 )
	 *               失败返回 错误代码
	 */
	public function excGD2GB( int $uid, float $gd, string $desc, $otid )
	{
		$gd = $this->getInputNumber( $gd );
		$gd = abs( $gd );
		$this->_excGD2GB( $uid, $gd, $desc, $otid );

		return $this->_bizResult;
	}

	/**
	 * 金豆兑换金币，私有方法
	 *
	 * @param int $uid
	 * @param int $bean
	 *
	 * @return bool 成功返回true , 失败返回false
	 */
	private function _excGD2GB( int $uid, int $gd, $desc, $otid )
	{
		$this->_clearError();
		$type = self::STATEMENT_TYPE_GD_GB;
		$rate = $this->getRate( $uid, $this->_statementRateType[$type] );

		$gbd = bcmul($gd , $rate);
		$hbd = 0;
		$hdd = 0;
		$gdd = -$gd;

		$this->_beginTransition();

		$balance = $this->_getBalanceForTransition( $uid );

		if ( $balance['gd'] <= 0 || $balance['gd'] < $gd )
		{
			$this->_setError( FinanceError::BALANCE_NOT_ENOUGH );
			$this->_setBizResult();

			$this->_endTransition( self::TRANSITION_RESULT_FAILED );

			return false;
		}

		$balance = $this->_getBalanceForTransition( $uid );
		$tid     = $this->_addExcGD2GBRecord( $uid, $gd, $gbd, $desc, $otid );

		if ( $tid )
		{
			if ( $this->_statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $tid, $balance ) )
			{
				$result = $this->_outputData( $this->_getBalance( $uid ), $tid );
				$this->_setBizResult( $result );

				$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

				return true;
			}
			else
			{
				$this->_setError( FinanceError::STATEMENT_FAILED );
			}
		}
		else
		{
			$this->_setError( FinanceError::CREATE_ORDER_FAILED );
		}

		$this->_setBizResult();

		$this->_endTransition( self::TRANSITION_RESULT_FAILED );

		return false;

	}

	/**
	 * 主播金豆兑换金币记录表
	 *
	 * @param int    $uid
	 * @param int    $gd
	 * @param int    $gb
	 * @param string $desc
	 * @param int    $otid
	 *
	 * @return mixed|bool 成功返回订单ID，失败返回FALSE
	 */
	private function _addExcGD2GBRecord( int $uid, int $gd, int $gb, string $desc, $otid )
	{
		$data = array(
			'uid'   => $uid,
			'gd'    => $gd,
			'gb'    => $gb,
			'desc'  => $desc,
			'otid'  => $otid,
			'ctime' => $this->_ctime
		);

		return $this->_insertRecord( $this->_tabList['beanToGB'], $data );
	}


	/**
	 * @param int    $uid
	 * @param int    $hbd
	 * @param int    $channel
	 * @param string $desc
	 * @param        $otid
	 *
	 * @return mixed
	 */
	public function costUserHb( int $uid, int $hbd, int $channel, string $desc, $otid )
	{
		$hbd = $this->getInputNumber( $hbd );
		$hbd = abs( $hbd );
		$this->_costUserHb( $uid, $hbd, $channel, $desc, $otid );

		return $this->_bizResult;
	}

	/**
	 * @param int    $uid
	 * @param int    $hbd
	 * @param int    $channel
	 * @param string $desc
	 * @param        $otid
	 *
	 * @return bool
	 */
	private function _costUserHb( int $uid, int $hbd, int $channel, string $desc, $otid )
	{
		$this->_clearError();

		$type = self::STATEMENT_TYPE_INNERCOST;
//		$rate = $this->getRate( $uid, $this->_statementRateType[$type] );
		//消耗没有比率
		$hbd = -$hbd;
		$gbd = 0;
		$hdd = 0;
		$gdd = 0;

		$this->_beginTransition();

		$balance = $this->_getBalanceForTransition( $uid );
		$tid     = $this->_addCostUserHbRecord( $uid, $hbd, $channel, $desc, $otid );

		if ( $tid )
		{
			if ( $this->_statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $tid, $balance ) )
			{
				$result = $this->_outputData( $this->_getBalance( $uid ), $tid );
				$this->_setBizResult( $result );

				$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

				return true;
			}
			else
			{
				$this->_setError( FinanceError::STATEMENT_FAILED );
			}
		}
		else
		{
			$this->_setError( FinanceError::CREATE_ORDER_FAILED );
		}

		$this->_setBizResult();

		$this->_endTransition( self::TRANSITION_RESULT_FAILED );

		return false;

	}

	/**
	 * @param int    $uid
	 * @param int    $hbd
	 * @param int    $channel
	 * @param string $desc
	 * @param        $otid
	 * @param        $id
	 *
	 * @return bool|mixed
	 */
	private function _addCostUserHbRecord( int $uid, int $hbd, int $channel, string $desc, $otid, $id )
	{
		$data = array(
			'uid'     => $uid,
			'hb'      => $hbd,
			'channel' => $channel,
			'desc'    => $desc,
			'otid'    => $otid,
			'ctime'   => $this->_ctime
		);

		return $this->_insertRecord( $this->_tabList['costHb'], $data, $id );
	}

	/**
	 * 创建订单
	 *
	 * @param     $uid
	 * @param     $tuid
	 * @param     $hb
	 * @param     $desc
	 * @param     $otid
	 * @param     $runtime
	 * @param     $action
	 * @param int $handleGroup
	 *
	 * @return mixed
	 */
	public function createDueOrder( $uid, $tuid, $orderHB, $payHB, $desc, $otid, $runtime, $action, $handleGroup = Finance::GUARANTEE_LOG_GROUP_FRONT )
	{
		$orderHB = $this->getInputNumber( $orderHB );
		$payHB   = $this->getInputNumber( $payHB );
		$result  = $this->_createDueOrder( $uid, $tuid, $orderHB, $payHB, $desc, $otid, $runtime, $action, $handleGroup );

		return $this->_bizResult;
	}

	private function _createDueOrder( $uid, $tuid, $orderHB, $payHB, $desc, $otid, $runtime, $action, $handleGroup )
	{
		$this->_clearError();

		$type = self::STATEMENT_TYPE_DUE_ORDERED;
		$rate = $this->getRate( $tuid, $this->_statementRateType[$type] );
		$rate = bcmul( $rate, self::RATE_HB_GB, 3 );

		$payCoin     = $orderHB;
		$realPayCoin = $payHB;
		$payCoinType = self::COIN_TYPE_HB;

		$this->_log( __FUNCTION__ . " realPay value is $realPayCoin" );
		$this->_log( __FUNCTION__ . " rate value is $rate" );

		$income         = bcmul( $payCoin, $rate );
		$incomeCoinType = self::COIN_TYPE_GB;

		$this->_log( __FUNCTION__ . " income value is $income" );

		$this->_beginTransition();

		//Notice: three is allowed that to ordered yuewan business when the real pay price equal 0
		//允许约玩下单实际支付金额为0的情况
		if ( $orderHB > 0 && $payHB > 0 )
		{
			$balance = $this->_getBalanceForTransition( $uid );
			if ( $balance['hb'] <= 0 || $balance['hb'] < $realPayCoin )
			{
				$this->_log(__FUNCTION__." balance not enough balance is ".$balance['hb'] . " and realPay is $realPayCoin");

				$this->_setError( FinanceError::BALANCE_NOT_ENOUGH );
				$this->_setBizResult();


				$this->_endTransition( self::TRANSITION_RESULT_FAILED );

				return false;
			}
		}

		$tid = $this->_createGuaranteeOrder( $uid, $tuid, $payCoin, $realPayCoin, $payCoinType, $income, $incomeCoinType, $type, $balance, $desc, $otid );

		if ( $tid )
		{
			$desc    = json_encode( [ 'Activity' => "用户约单,下单操作", 'uid' => $uid, 'tuid' => $tuid ] );
			$cronTab = $this->_guaranteeOrderCronTab( $tid, $runtime, self::GUARANTEE_STATUS_ORDERED, $action, $desc, $uid, $otid, $handleGroup );

			if ( $cronTab )
			{
				$result = $this->_outputData( $this->_getBalance( $uid ), $tid );

				$this->_setBizResult( $result );
				$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

				return true;
			}
			else
			{
				$this->_setError( FinanceError::GUARANTEE_CRON_CREATE_FAILED );
			}
		}
		else
		{
			$this->_setError( FinanceError::CREATE_ORDER_FAILED );
		}

		$this->_setBizResult();
		$this->_endTransition( self::TRANSITION_RESULT_FAILED );

		return false;
	}

	/**
	 * 冻结约单订单
	 *
	 * @param     $orderid
	 * @param     $otid
	 * @param     $handleUid
	 * @param int $handleGroup
	 *
	 * @return mixed
	 */
	public function freezeDueOrder( $orderid, $desc, $otid, $handleUid, $handleGroup = Finance::GUARANTEE_LOG_GROUP_FRONT )
	{

		$this->_freezeDueOrder( $orderid, $desc, $otid, $handleUid, $handleGroup );

		return $this->_bizResult;
	}

	private function _freezeDueOrder( $orderid, $desc, $otid, $handleUid, $handleGroup )
	{
		$this->_clearError();
		$orderInfo = $this->_getGuaranteeOrderInfo( $orderid );
		if ( !$orderInfo )
		{
			$this->_setError( FinanceError::ORDER_NOT_EXIST );
			$this->_setBizResult();

			return false;
		}

		if ( $orderInfo['status'] != self::GUARANTEE_STATUS_ORDERED )
		{
			$this->_setError( FinanceError::ORDER_STATUS_FAILED );
			$this->_setBizResult();

			return false;
		}

		$this->_beginTransition();

//		$this->_freezeGuaranteeOrderCronTab( $orderid );


		if ( !$this->_freezeGuaranteeOrder( $orderid, $orderInfo, $desc, $handleUid, $otid, $handleGroup ) )
		{
			$this->_setError( FinanceError::ORDER_FREEZE_FAILED );
			$this->_setBizResult();
//			echo "freezeGuaranteeOrder failed";
			$this->_endTransition( self::TRANSITION_RESULT_FAILED );

			return false;
		}

		$this->_setBizResult( [ 'tid' => $orderid ] );
		$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

		return true;
	}

	/**
	 * 解冻订单
	 *
	 * @param     $orderid
	 * @param     $otid
	 * @param     $handleUid
	 * @param int $handleGroup
	 *
	 * @return mixed
	 */
	public function unFreezeDueOrder( $orderid, $desc, $otid, $handleUid, $handleGroup = Finance::GUARANTEE_LOG_GROUP_FRONT )
	{
		$this->_unFreezeDueOrder( $orderid, $desc, $otid, $handleUid, $handleGroup );

		return $this->_bizResult;
	}

	private function _unFreezeDueOrder( $orderid, $desc, $otid, $handleUid, $handleGroup )
	{
		$this->_clearError();
		$orderInfo = $this->_getGuaranteeOrderInfo( $orderid );
		if ( !$orderInfo )
		{
			// 订单不存在错误代码
			$this->_setError( FinanceError::ORDER_NOT_EXIST );
			$this->_setBizResult();

			return false;
		}

		if ( $orderInfo['status'] != self::GUARANTEE_STATUS_FREEZE )
		{
			//该订单已经处理完成，不能进行操作
			$this->_setError( FinanceError::ORDER_STATUS_FAILED );
			$this->_setBizResult();

			return false;
		}

		$this->_beginTransition();

		if ( !$this->_unFreezeGuaranteeOrder( $orderid, $orderInfo, $desc, $handleUid, $otid, $handleGroup ) )
		{
			$this->_setError( FinanceError::ORDER_UNFREEZE_FAILED );
			$this->_setBizResult();

			$this->_endTransition( self::TRANSITION_RESULT_FAILED );

			return false;
		}

		$this->_setBizResult( [ 'tid' => $orderid ] );

		$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

		return true;
	}

	/**
	 *  锁定订单
	 *
	 * @param     $orderid
	 * @param     $otid
	 * @param     $handleUid
	 * @param int $handleGroup
	 *
	 * @return bool|int
	 */
	public function lockDueOrder( $orderid, $desc, $otid, $handleUid, $handleGroup = Finance::GUARANTEE_LOG_GROUP_FRONT )
	{
		return $this->_freezeGuaranteeOrderCronTab( $orderid, $desc, $otid, $handleUid, $handleGroup );
	}

	/**
	 * 解锁订单
	 *
	 * @param     $orderid
	 * @param     $otid
	 * @param     $handleUid
	 * @param int $handleGroup
	 *
	 * @return bool|int
	 */
	public function unlockDueOrder( $orderid, $desc, $otid, $handleUid, $handleGroup = Finance::GUARANTEE_LOG_GROUP_FRONT )
	{

		return $this->_unFreezeGuaranteeOrderCronTab( $orderid, $desc, $otid, $handleUid, $handleGroup );
	}

	/**
	 * 退款设置
	 *
	 * @param     $orderid
	 * @param     $runtime
	 * @param     $otid
	 * @param     $handleUid
	 * @param int $handleGroup
	 *
	 * @return mixed
	 */
	public function refundDueOrder( $orderid, $runtime, $otid, $handleUid, $handleGroup = Finance::GUARANTEE_LOG_GROUP_FRONT )
	{
		$this->_refundDueOrder( $orderid, $runtime, $otid, $handleUid, $handleGroup );

		return $this->_bizResult;
	}

	private function _refundDueOrder( $orderid, $runtime, $otid, $handleUid, $handleGroup )
	{
		$this->_clearError();

		$orderInfo = $this->_getGuaranteeOrderInfo( $orderid );

		if ( !$orderInfo )
		{

			$this->_setError( FinanceError::ORDER_NOT_EXIST );
			$this->_setBizResult();

			return false;
		}

		if ( $orderInfo['status'] == self::GUARANTEE_STATUS_FREEZE )
		{
			$this->_setError( FinanceError::ORDER_IS_FREEZE );
			$this->_setBizResult();

			return false;
		}

		if ( $orderInfo['status'] != self::GUARANTEE_STATUS_ORDERED )
		{
			$this->_setError( FinanceError::ORDER_STATUS_FAILED );
			$this->_setBizResult();

			return false;
		}

		$rStatus = self::GUARANTEE_STATUS_BACKED;
		$rAction = self::GUARANTEE_CRON_ACTION_REFUND;
		$desc    = json_encode( [ 'Activity' => "用户约单,退款操作", 'uid' => $orderInfo['uid'], 'tuid' => $orderInfo['tuid'] ] );

		$this->_beginTransition();

		if ( !$this->_guaranteeOrderCronTab( $orderid, $runtime, $rStatus, $rAction, $desc, $handleUid, $otid, $handleGroup ) )
		{
			$this->_setError( FinanceError::GUARANTEE_CRON_CREATE_FAILED );
			$this->_setBizResult();
			$this->_endTransition( self::TRANSITION_RESULT_FAILED );

			return false;
		}

		$this->_setBizResult( [ 'tid' => $orderid ] );
		$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

		return true;
	}

	/**
	 * 到账设置
	 *
	 * @param     $orderid
	 * @param     $runtime
	 * @param     $otid
	 * @param     $handleUid
	 * @param int $handleGroup
	 *
	 * @return mixed
	 */
	public function finishDueOrder( $orderid, $runtime, $otid, $handleUid, $handleGroup = Finance::GUARANTEE_LOG_GROUP_FRONT )
	{
		$this->_finishDueOrder( $orderid, $runtime, $otid, $handleUid, $handleGroup );

		return $this->_bizResult;
	}

	private function _finishDueOrder( $orderid, $runtime, $otid, $handleUid, $handleGroup )
	{
		$this->_clearError();

		$orderInfo = $this->_getGuaranteeOrderInfo( $orderid );

		if ( !$orderInfo )
		{

			$this->_setError( FinanceError::ORDER_NOT_EXIST );
			$this->_setBizResult();

			return false;
		}

		if ( $orderInfo['status'] == self::GUARANTEE_STATUS_FREEZE )
		{
			$this->_setError( FinanceError::ORDER_IS_FREEZE );
			$this->_setBizResult();

			return false;
		}

		if ( $orderInfo['status'] != self::GUARANTEE_STATUS_ORDERED )
		{
			$this->_setError( FinanceError::ORDER_STATUS_FAILED );
			$this->_setBizResult();

			return false;
		}

		$rStatus = self::GUARANTEE_STATUS_FINISHED;
		$rAction = self::GUARANTEE_CRON_ACTION_PAY;
		$desc    = json_encode( [ 'Activity' => "用户约单,成功操作", 'uid' => $orderInfo['uid'], 'tuid' => $orderInfo['tuid'] ] );

		$this->_beginTransition();

		if ( !$this->_guaranteeOrderCronTab( $orderid, $runtime, $rStatus, $rAction, $desc, $handleUid, $otid, $handleGroup ) )
		{

			$this->_setError( FinanceError::GUARANTEE_CRON_CREATE_FAILED );
			$this->_setBizResult();
			$this->_endTransition( self::TRANSITION_RESULT_FAILED );

			return false;
		}

		$this->_setBizResult( [ 'tid' => $orderid ] );
		$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

		return true;
	}

	/**
	 * 创建担保订单，并且扣款
	 *
	 * @param $uid
	 * @param $tuid
	 * @param $payCoin          订单金额
	 * @param $realPayCoin      用户实际支付金额
	 * @param $payCoinType      用户付款货币类型
	 * @param $income           主播实际收入金额
	 * @param $incomeCoinType   主播收入货币类型
	 * @param $type             statement 类型
	 * @param $balance
	 * @param $desc
	 * @param $otid
	 *
	 * @return bool|mixed
	 */
	private function _createGuaranteeOrder( $uid, $tuid, $payCoin, $realPayCoin, $payCoinType, $income, $incomeCoinType, $type, $balance, $desc, $otid )
	{


		$tid = $this->_addGuaranteeOrder( $uid, $tuid, $payCoin, $realPayCoin, $payCoinType, $income, $incomeCoinType, $desc, $otid, $type );

		$data = $this->_getCoinDesc( $realPayCoin, $payCoinType, self::GUARANTEE_OPERATE_SUB );

		$hbd = $data['hb'];
		$hdd = $data['hd'];
		$gbd = $data['gb'];
		$gdd = $data['gd'];

		if ( !$tid )
		{
			return false;
		}


		//notice 支持订单金额为0的情况
		if ( $realPayCoin !=0 && !$this->_statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $tid, $balance ) )
		{
			return false;
		}

		if ( !$this->_guaranteeOrderLog( $tid, 0, self::GUARANTEE_STATUS_ORDERED, $uid, $otid, self::GUARANTEE_LOG_GROUP_FRONT ) )
		{
			return false;
		}

		return $tid;
	}

	private function _freezeGuaranteeOrder( $orderid, $orderInfo = false, $desc, $handleUid = 0, $handleTid = 0, $handleGroup = financeBase::GUARANTEE_LOG_GROUP_FRONT )
	{
		if ( !$orderInfo )
		{
			$orderInfo = $this->_getGuaranteeOrderInfo( $orderid );
		}

		if ( $orderInfo['status'] == self::GUARANTEE_STATUS_ORDERED )
		{
			if ( !$this->_guaranteeOrderLog( $orderid, $orderInfo['status'], self::GUARANTEE_STATUS_FREEZE, $desc, $handleUid, $handleTid, $handleGroup ) )
			{
				return false;
			}

			return $this->_updateGuaranteeOrderStatus( $orderid, self::GUARANTEE_STATUS_FREEZE );
		}
		elseif ( $orderInfo['status'] == self::GUARANTEE_STATUS_FREEZE )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private function _unFreezeGuaranteeOrder( $orderid, $orderInfo = false, $desc, $handleUid = 0, $handleTid = 0, $handleGroup = FinanceBase::GUARANTEE_LOG_GROUP_FRONT )
	{
		if ( !$orderInfo )
		{
			$orderInfo = $this->_getGuaranteeOrderInfo( $orderid );
		}

		if ( $orderInfo['status'] == self::GUARANTEE_STATUS_FREEZE )
		{
			if ( !$this->_guaranteeOrderLog( $orderid, $orderInfo['status'], self::GUARANTEE_STATUS_ORDERED, $desc, $handleUid, $handleTid, $handleGroup ) )
			{
				return false;
			}

			return $this->_updateGuaranteeOrderStatus( $orderid, self::GUARANTEE_STATUS_ORDERED );
		}
		elseif ( $orderInfo['status'] == self::GUARANTEE_STATUS_ORDERED )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 担保订单退款
	 *
	 * @param $orderid
	 */
	private function _backGuaranteeOrder( $orderInfo, $balance, $type, $desc, $handleUid = 0, $handleTid = 0, $handleGroup = FinanceBase::GUARANTEE_LOG_GROUP_SYSTEM )
	{

		//TODO 订单状态跟踪，包括 谁，修改了什么状态，再什么时刻 需要有一个单独的类来处理
		if ( $orderInfo['status'] != self::GUARANTEE_STATUS_ORDERED )
		{
			$this->_log( 'error order status orderid:' . $orderInfo['id'] );

			return false;
		}

		//TODO 如果使用优惠卷，那么在退款的时候 退回支付金额，还是订单金额？
		$id          = $orderInfo['id'];
		$uid         = $orderInfo['uid'];
		$realPayCoin = $orderInfo['real_pay'];
		$payCoinType = $orderInfo['pay_coin_type'];

		$data = $this->_getCoinDesc( $realPayCoin, $payCoinType, self::GUARANTEE_OPERATE_ADD );

		$hbd = $data['hb'];
		$hdd = $data['hd'];
		$gbd = $data['gb'];
		$gdd = $data['gd'];


		$updateResult = $this->_updateGuaranteeOrderStatus( $id, self::GUARANTEE_STATUS_BACKED );

		if ( $updateResult )
		{
			if ( !$this->_guaranteeOrderLog( $id, $orderInfo['status'], self::GUARANTEE_STATUS_BACKED, $desc, $handleUid, $handleTid, $handleGroup ) )
			{
				$this->_log("{$orderInfo['id']} back guaranteeOrderLog  failed");
				return false;
			}

			//notice 支持支付金额为0的情况下退款
			if($realPayCoin == 0)
			{
				$this->_log("{$orderInfo['id']} realPayCoin = $realPayCoin do backGuaranteeOrder");
				return true;
			}
			else if ( $this->_statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $id, $balance ) )
			{
				return true;
			}
			else
			{
				$this->_log("{$orderInfo['id']} back statement  failed");
			}
		}
		else
		{
			$this->_log(__FUNCTION__."  {$orderInfo['id']} update guarantee order status  failed");
		}

		return false;
	}

	/**
	 * 担保订单完成
	 *
	 * @param $orderid
	 */
	private function _finishGuaranteeOrder( $orderInfo, $balance, $type, $desc, $handleUid = 0, $handleTid = 0, $handleGroup = FinanceBase::GUARANTEE_LOG_GROUP_SYSTEM )
	{

		if ( $orderInfo['status'] != self::GUARANTEE_STATUS_ORDERED )
		{
			$this->_log( 'error order status orderid:' . $orderInfo['id'] );

			return false;
		}

		$id             = $orderInfo['id'];
		$uid            = $orderInfo['tuid'];
		$income         = $orderInfo['income'];
		$incomeCoinType = $orderInfo['income_coin_type'];

		$data = $this->_getCoinDesc( $income, $incomeCoinType, self::GUARANTEE_OPERATE_ADD );

		$hbd = $data['hb'];
		$hdd = $data['hd'];
		$gbd = $data['gb'];
		$gdd = $data['gd'];

		$updateResult = $this->_updateGuaranteeOrderStatus( $id, self::GUARANTEE_STATUS_FINISHED );

		if ( $updateResult )
		{
			if ( !$this->_guaranteeOrderLog( $id, $orderInfo['status'], self::GUARANTEE_STATUS_FINISHED, $desc, $handleUid, $handleTid, $handleGroup ) )
			{
				$this->_log(__FUNCTION__." {$orderInfo['id']} finish guaranteeOrderLog  failed");
				return false;
			}

			if ( $this->_statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $id, $balance ) )
			{
				return true;
			}
			else
			{
				$this->_log(__FUNCTION__ . "call \$this->_statement($uid,$hbd,$gbd,$hdd,$gdd,$type,$id,$balance)");
				$this->_log(__FUNCTION__."  {$orderInfo['id']} finish statement  failed");
			}
		}
		else
		{
			$this->_log(__FUNCTION__."  {$orderInfo['id']} update guarantee order status  failed");
		}

		return false;
	}

	/**
	 * @param $uid
	 * @param $tuid
	 * @param $payCoin
	 * @param $realPayCoin
	 * @param $payCoinType
	 * @param $income
	 * @param $incomeCoinType
	 * @param $desc
	 * @param $otid
	 * @param $type
	 *
	 * @return bool|mixed
	 */
	private function _addGuaranteeOrder( $uid, $tuid, $payCoin, $realPayCoin, $payCoinType, $income, $incomeCoinType, $desc, $otid, $type )
	{
		$data = [
			'uid'              => $uid,
			'tuid'             => $tuid,
			'pay'              => $payCoin,
			'real_pay'         => $realPayCoin,
			'pay_coin_type'    => $payCoinType,
			'income'           => $income,
			'income_coin_type' => $incomeCoinType,
			'status'           => self::GUARANTEE_STATUS_ORDERED,
			'ctime'            => $this->_ctime,
			'desc'             => $desc,
			'otid'             => $otid,
			'type'             => $type
		];

		return $this->_insertRecord( $this->_tabList['guarantee'], $data );
	}

	private function _guaranteeCronOrderLog( $orderid, $status, $action, $runtime, $desc, $handleUid = 0, $handleTid = 0, $handleGroup = Finance::GUARANTEE_LOG_GROUP_FRONT )
	{
		$data = [
			'orderid'     => $orderid,
			'status'      => $status,
			'action'      => $action,
			'handleuid'   => $handleUid,
			'runtime'     => $runtime,
			'handletid'   => $handleTid,
			'handlegroup' => $handleGroup,
			'desc'        => $desc
		];

		return $this->_insertRecord( $this->_tabList['guaranteeCronLog'], $data, self::INSERT_RECORD_ID_AUTOINCREMENT );
	}

	/**
	 * @param     $orderid
	 * @param     $s_status
	 * @param     $d_status
	 * @param     $desc
	 * @param int $handleUid
	 * @param int $handleTuid
	 * @param int $handleGroup
	 *
	 * @return bool|mixed
	 */
	private function _guaranteeOrderLog( $orderid, $s_status, $d_status, $desc, $handleUid = 0, $handleTuid = 0, $handleGroup = FinanceBase::GUARANTEE_LOG_GROUP_SYSTEM )
	{

		//todo 嵌入在改变担保订单状态的地方
		$data = [
			'orderid'     => $orderid,
			's_status'    => $s_status,
			'd_status'    => $d_status,
			'desc'        => $desc,
			'handleuid'   => $handleUid,
			'handletid'   => $handleTuid,
			'handlegroup' => $handleGroup,
			'ctime'       => date( "Y-m-d H:i:s" )
		];

		return $this->_insertRecord( $this->_tabList['guaranteeLog'], $data );
	}

	/**
	 * 更新担保订单状态
	 *
	 * @param $id
	 * @param $status
	 *
	 * @return bool|int
	 */
	private function _updateGuaranteeOrderStatus( $id, $status )
	{
		$data = [
			'status' => $status,
			'etime'  => date( "Y-m-d H:i:s" )
		];

		$where = [ 'id' => $id ];

		$table = $this->_getMonthTableNameById( $this->_tabList['guarantee'], $id );

		$sql = $this->_db->where( $where )->update( $table, $data, true );
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			//todo log
			return false;
		}

		return $this->_db->affectedRows;
	}

	/**
	 * 获取订单信息
	 *
	 * @param $orderid
	 *
	 * @return bool
	 */
	public function getGuaranteeOrderInfo( $orderid )
	{
		return $this->_getGuaranteeOrderInfo( $orderid );
	}

	private function _getGuaranteeOrderInfo( $orderid )
	{
		$field = [ 'id', 'uid', 'tuid', 'pay', 'real_pay', 'pay_coin_type', 'income', 'income_coin_type', 'status', 'type' ];
		$where = [ 'id' => $orderid ];

		$table = $this->_getMonthTableNameById( $this->_tabList['guarantee'], $orderid );

		$sql = $this->_db->field( $field )->where( $where )->select( $table, true );
		$res = $this->_db->query( $sql );

		if ( !$res )
		{
			//todo log
			return false;
		}

		$row = $res->fetch_assoc();

		if ( $row['id'] )
		{
			return $row;
		}
		else
		{
			return false;
		}
	}

	private function _getCoinDesc( $coin, $coinType, $operate = Finance::GUARANTEE_OPERATE_ADD )
	{
		$coinTypeList = [ 'hb', 'hd', 'gb', 'gd' ];
		$coinList     = [ 0, 0, 0, 0 ];

		if ( $operate == self::GUARANTEE_OPERATE_ADD )
		{
			$coinList[$coinType - 1] = abs( $coin );
		}
		elseif ( $operate == self::GUARANTEE_OPERATE_SUB )
		{
			$coinList[$coinType - 1] = -abs( $coin );
		}

		$data = [];

		foreach ( $coinTypeList as $key => $value )
		{
			$data[$value] = $coinList[$key];
		}

		return $data;
	}

	private function _guaranteeOrderCronTab( $orderId, $runTime = null, $rStatus = null, $rAction = null, $desc = null, $handleuid = 0, $handleid = 0, $group = Finance::GUARANTEE_LOG_GROUP_SYSTEM, $cronStatus = Finance::GUARANTEE_CRON_STATUS_UNFINISHED )
	{

		if ( !is_null( $rAction ) )
		{
			$updateData['action'] = $rAction;
		}

		if ( !is_null( $runTime ) )
		{
			$updateData['runtime'] = $runTime;
		}

		$insertData = $updateData;

		$insertData['orderid'] = $orderId;
		$insertData['status']  = $cronStatus;

		$sql = $this->_db->insertDuplicate( $this->_tabList['guaranteeCron'], $insertData, $updateData, true );
		$this->_log( $sql );
		if ( !$this->_db->query( $sql ) )
		{
			return false;
		}

		if ( $rStatus )
		{
			$insertData['status'] = $rStatus;
		}

		if ( $desc )
		{
			$insertData['desc'] = $desc;
		}

		$insertData['handleuid']   = $handleuid;
		$insertData['handlegroup'] = $group;
		$insertData['handletid']   = $handleid;

		if ( !$this->_insertRecord( $this->_tabList['guaranteeCronLog'], $insertData, self::INSERT_RECORD_ID_AUTOINCREMENT ) )
		{
			return false;
		}

		return true;
	}

	private function _freezeGuaranteeOrderCronTab( $orderid, $desc, $otid, $handleUid, $handleGroup )
	{
		if ( $this->_updateGuaranteeOrderCronTabStatus( $orderid, self::GUARANTEE_CRON_STATUS_FREEZE ) )
		{

			$this->_guaranteeCronOrderLog( $orderid, self::GUARANTEE_CRON_STATUS_FREEZE, 0, '', $desc, $handleUid, $otid, $handleGroup );

			return true;
		}

		return false;
	}

	private function _unFreezeGuaranteeOrderCronTab( $orderid, $desc, $otid, $handleUid, $handleGroup )
	{
		if ( $this->_updateGuaranteeOrderCronTabStatus( $orderid, self::GUARANTEE_CRON_STATUS_UNFINISHED ) )
		{
			$this->_guaranteeCronOrderLog( $orderid, self::GUARANTEE_CRON_STATUS_UNFINISHED, 0, '', $desc, $handleUid, $otid, $handleGroup );

			return true;
		}

		return false;
	}

	private function _updateGuaranteeOrderCronTabStatus( $id, $status )
	{
		$sql = "update {$this->_tabList['guaranteeCron']} set status=$status where orderid=$id";
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			return false;
		}

		return $this->_db->affectedRows;
	}

	public function doGuaranteeCronTab()
	{
		$this->_db->connect();

		$sql = "select * from " . $this->_tabList['guaranteeCron'] . " where status=" . self::GUARANTEE_CRON_STATUS_UNFINISHED . " and runtime <= now()";
		$res = $this->_db->query( $sql );
		while ( $row = $res->fetch_assoc() )
		{
			var_dump( $row );
			$this->_doGuaranteeCronTab( $row );
		}

		$this->_db->disconnect();
	}

	private function _doGuaranteeCronTab( $cronInfo )
	{
		$orderid = $cronInfo['orderid'];
		$runtime = $cronInfo['runtime'];
		$action  = $cronInfo['action'];

		if ( strtotime( $runtime ) >= time() )
		{
			return false;
		}

		$orderInfo = $this->_getGuaranteeOrderInfo( $orderid );
		if ( !$orderInfo )
		{
			echo "$orderid => 当前订单不存在.\n";

			return false;
		}

		$type   = $orderInfo['type'];
		$status = $orderInfo['status'];
		$uid    = $orderInfo['uid'];
		$tuid   = $orderInfo['tuid'];

		if ( $status == self::GUARANTEE_STATUS_FREEZE )
		{
//			$this->_freezeGuaranteeOrderCronTab( $orderid );
			echo "$orderid => 当前订单被冻结\n";

			return false;
		}

		if ( $status != self::GUARANTEE_STATUS_ORDERED )
		{
			$msg = [ 'orderid' => $orderid, 'timestamp' => time(), 'action' => $action, 'desc' => 'status not match' ];
			$this->_noticeLog( $msg, 'guarantee_order_status.log' );
			echo "$orderid => 当前订单 状态错误\n";

			//todo 是否要制成完成状态？
//			$this->_updateGuaranteeOrderCronTabStatus( $orderid, self::GUARANTEE_CRON_STATUS_FINISHED );

			return false;
		}

		$this->_beginTransition();

		$result = false;


		if ( $action == self::GUARANTEE_CRON_ACTION_PAY )
		{
			$balance = $this->_getBalanceForTransition( $tuid );
			$desc    = "系统自动到账";
			$result  = $this->_finishGuaranteeOrder( $orderInfo, $balance, $type, $desc );
		}
		elseif ( $action == self::GUARANTEE_CRON_ACTION_REFUND )
		{
			$balance = $this->_getBalanceForTransition( $uid );
			$desc    = "系统自动退款";
			$result  = $this->_backGuaranteeOrder( $orderInfo, $balance, $type, $desc );
		}
		else
		{
			echo "$orderid => 目标行为异常.\n";
		}

		if ( !$result )
		{
			$this->_endTransition( self::TRANSITION_RESULT_FAILED );
			echo "$orderid => $desc 执行失败.\n";

			return false;
		}

		//修改cron 表状态
		if ( !$this->_updateGuaranteeOrderCronTabStatus( $orderid, self::GUARANTEE_CRON_STATUS_FINISHED ) )
		{
			$this->_endTransition( self::TRANSITION_RESULT_FAILED );
			echo "$orderid => cron status 更新失败.\n";

			return false;
		}

		$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

		echo "$orderid => 订单完成.\n";

		return true;
	}

	/**
	 * 为记录表创建一条记录
	 *
	 * @param string $tabname 表名称
	 * @param array  $data    记录所需数据
	 *
	 * @return bool|mixed 成功返回订单号，失败返回FALSE
	 */
	private function _insertRecord( string $tabname, array $data, $oid = FinanceBase::INSERT_RECORD_ID_USESELF )
	{
		$id         = $this->_getRecordID(strtotime($this->_ctime));
		$data['id'] = $id;

		if ( $oid )
		{
			$data['id'] = $oid;
			if ( $oid == -1 )
			{
				unset( $data['id'] );
			}
		}

		$sql = $this->_db->insert( $tabname, $data, true );

		if ( $this->_db->query( $sql, $this->_dbType ) )
		{
			return $oid ? $oid : $id;
		}
		else
		{
			//主键重复错误代码 1062
			if ( $this->_db->errno() != 1062 )
			{
				$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
				$this->_log( $t );

				return false;
			}
			else
			{
				return $this->_insertRecord( $tabname, $data, $oid );
			}
		}

	}

	/**
	 * @param $uid
	 * @param $hb
	 * @param $gb
	 * @param $hd
	 * @param $gd
	 * @param $channel
	 * @param $desc
	 * @param $otid
	 *
	 * @return mixed
	 */
	public function innerRecharge( $uid, $hb, $gb, $hd, $gd, $channel, $desc, $otid )
	{
		$hb = $this->getInputNumber( $hb );
		$gb = $this->getInputNumber( $gb );
		$hd = $this->getInputNumber( $hd );
		$gd = $this->getInputNumber( $gd );
		$this->_innerRecharge( $uid, $hb, $gb, $hd, $gd, $channel, $desc, $otid );

		return $this->_bizResult;
	}

	/**
	 * @param $uid
	 * @param $hb
	 * @param $gb
	 * @param $hd
	 * @param $gd
	 * @param $channel
	 * @param $desc
	 * @param $otid
	 *
	 * @return bool
	 */
	private function _innerRecharge( $uid, $hb, $gb, $hd, $gd, $channel, $desc, $otid )
	{
		$this->_clearError();

		$type = self::STATEMENT_TYPE_INTERNAL_RECHARGE;
//		$rate = $this->getRate($uid,$this->_statementRateType[$type]);
		//内部充值没有比率
		$rate = 1;
		$hbd  = bcmul($hb , $rate);
		$gbd  = bcmul($gb , $rate);
		$hdd  = bcmul($hd , $rate);
		$gdd  = bcmul($gd , $rate);

		$this->_beginTransition();

		$balance = $this->_getBalanceForTransition( $uid );
		$tid     = $this->_addInnerRechargeRecord( $uid, $hbd, $gbd, $hdd, $gdd, $channel, $desc, $otid );

		if ( $tid )
		{
			if ( $hbd < 0 || $gbd < 0 || $hdd < 0 || $gdd < 0 )
			{
//				echo "source :=> $hb xxx $gb xxx $hd xxx $gd \n";
//				echo "result :=>  $hbd xxx $gbd xxx $hdd xxx $gdd\n";
			}

			if ( $this->_statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $tid, $balance ) )
			{
				$result = $this->_outputData( $this->_getBalance( $uid ), $tid );
				$this->_setBizResult( $result );

				$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

				return true;
			}
			else
			{
				$this->_setError( FinanceError::STATEMENT_FAILED );
			}
		}
		else
		{
			$this->_setError( FinanceError::CREATE_ORDER_FAILED );
		}

		$this->_setBizResult();
		$this->_endTransition( self::TRANSITION_RESULT_FAILED );

		return false;
	}

	/**
	 * @param $uid
	 * @param $hb
	 * @param $gb
	 * @param $hd
	 * @param $gd
	 * @param $channel
	 * @param $desc
	 * @param $otid
	 *
	 * @return bool|mixed
	 */
	private function _addInnerRechargeRecord( $uid, $hb, $gb, $hd, $gd, $channel, $desc, $otid )
	{
		$data = array(
			'uid'     => $uid,
			'hb'      => $hb,
			'gb'      => $gb,
			'hd'      => $hd,
			'gd'      => $gd,
			'channel' => $channel,
			'desc'    => $desc,
			'otid'    => $otid,
			'ctime'   => $this->_ctime
		);

		return $this->_insertRecord( $this->_tabList['innerRecharge'], $data );
	}

	/**
	 * @return int
	 */
	private function _getRecordID($time=0)
	{
		$id = intval( microtime( true ) * 10000 . rand( 1000, 9999 ) );

		if($time)
		{
			$id = substr_replace($id,$time,0,strlen("$time"));
		}

		return intval($id);
	}

	/**
	 *  检测活动信息是否真实有效
	 *
	 * @param array $promotionInfo
	 *
	 * @return bool
	 */
	public function checkPromotionIsValid( array $promotionInfo )
	{
		$this->_clearError();

		if ( !$promotionInfo )
		{
			$this->_setError( FinanceError::PROMOTION_ERR_UNDEFINE );
			$this->_setBizResult();

			return false;
		}

		$stime        = $promotionInfo['stime'];
		$etime        = $promotionInfo['etime'];
		$status       = $promotionInfo['status'];
		$curTimeStamp = time();
		$stimeStamp   = strtotime( $stime );
		$etimeStamp   = strtotime( $etime );

		if ( self::PROMOTION_CLOSE == $status )
		{
			$this->_setError( FinanceError::PROMOTION_ERR_UNDEFINE );
			$this->_setBizResult();

			return false;
		}

		if ( self::DEFAULT_TIMESTAMP != $stime && $stimeStamp > $curTimeStamp )
		{
			$this->_setError( FinanceError::PROMOTION_ERR_UNSTART );
			$this->_setBizResult();

			return false;
		}

		if ( self::DEFAULT_TIMESTAMP != $etime && $etimeStamp < $curTimeStamp )
		{
			$this->_setError( FinanceError::PROMOTION_ERR_DEADLINE );
			$this->_setBizResult();

			return false;
		}

		//todo 一些活动可能有特定的参与条件，这边需要做筛选
		return true;
	}

	/**
	 * 获取活动详情
	 *
	 * @param $promotionID
	 *
	 * @return bool
	 */
	public function getPromotionInfo( $promotionID )
	{
		if ( !$promotionID )
		{
			return false;
		}
		$where = array(
			'id' => $promotionID
		);

		$sql = $this->_db->field( '*' )->where( $where )->select( $this->_tabList['promotion'] );

		$res = $this->_db->query( $sql, $this->_dbType );
		$row = $res->fetch_assoc();

		if ( !$row )
		{
			return fasle;
		}
		if ( $row['desc'] )
		{
			$row['desc'] = json_decode( $row['desc'], true );
		}

		return $row;
	}

	public function getBalanceByUids($uidlist)
	{
		if ( empty( $uidlist ) )
		{
			return [];
		}

		$uidlist = "(" . implode( ',', $uidlist ) . ")";

		$sql = "select * from {$this->_tabList['balance']} where uid in $uidlist";
		$res = $this->_db->query( $sql );

		$balanceList = [];

		while ( $row = $res->fetch_assoc() )
		{
			$balanceList[$row['uid']]['gb'] = $this->getOutputNumber($row['gb']);
			$balanceList[$row['uid']]['gd'] = $this->getOutputNumber($row['gd']);
			$balanceList[$row['uid']]['hb'] = $this->getOutputNumber($row['hb']);
			$balanceList[$row['uid']]['hd'] = $this->getOutputNumber($row['hd']);
		}


		return $balanceList;
	}

	/**
	 * 获取用户余额
	 *
	 * @param $uid
	 *
	 * @return array
	 */
	private function _getBalance( $uid )
	{
		$where = [
			'uid' => $uid
		];
		$field = [ 'uid', 'hb', 'gb', 'hd', 'gd' ];
		$sql   = $this->_db->field( $field )->where( $where )->select( $this->_tabList['balance'], true );

		$res = $this->_db->query( $sql, $this->_dbType );
		$row = $res->fetch_assoc();

		return array(
			'hb' => (int)$row['hb'],
			'gb' => (int)$row['gb'],
			'hd' => (int)$row['hd'],
			'gd' => (int)$row['gd']
		);
	}

	/**
	 * 外部获取用户余额
	 *
	 *
	 * @param $uid
	 *
	 * @return array
	 */
	public function getBalance( $uid )
	{
		$balance = $this->_getBalance( $uid );
		foreach ( $balance as $key => $value )
		{
			$balance[$key] = $this->getOutputNumber( intval( $value ) );
		}

		return $balance;
	}

//	public function getBalanceByUids( $uids )
//	{
//		if ( empty( $uids ) )
//		{
//			return false;
//		}
//
//		$uids = implode( ',', $uids );
//
//		$sql = "select * from {$this->_tabList['balance']} where uid in $uids";
//		$res = $this->_db->query( $sql );
//
//		$uidsProperty = [];
//
//		while ( $row = $res->fetch_assoc() )
//		{
//			array_push( $uidsProperty, $row );
//		}
//
//		return $uidsProperty;
//	}

	/**
	 * 更新余额
	 *
	 * @param $uid
	 * @param $newHB
	 * @param $newGB
	 * @param $newHD
	 * @param $newGD
	 * @param $oldHB
	 * @param $oldGB
	 * @param $oldHD
	 * @param $oldGD
	 *
	 * @return int
	 */
	private function _updateBalance( $uid, $newHB, $newGB, $newHD, $newGD, $oldHB, $oldGB, $oldHD, $oldGD )
	{
		$data  = array(
			'hb' => $newHB,
			'gb' => $newGB,
			'hd' => $newHD,
			'gd' => $newGD,
		);
		$where = array(
			'uid' => $uid,
			'hb'  => $oldHB,
			'gb'  => $oldGB,
			'hd'  => $oldHD,
			'gd'  => $oldGD
		);

		$sql = $this->_db->where( $where )->update( $this->_tabList['balance'], $data, true );
		if ( !$this->_db->query( $sql, $this->_dbType ) )
		{
			return false;
		}
		$result = $this->_db->affectedRows;
		if ( $result == 0 )
		{
			$data['uid'] = $where['uid'];
			$sql         = $this->_db->insert( $this->_tabList['balance'], $data, true );
			if ( $this->_db->query( $sql, $this->_dbType ) )
			{
				return $this->_db->affectedRows;
			}
			else
			{
				return false;
			}
		}

//		return $data;
		return $result;
	}

	/**
	 * 事务获取余额
	 *
	 * @param $uid
	 *
	 * @return array
	 */
	private function _getBalanceForTransition( $uid )
	{

		$sql     = "SELECT uid,hb,gb,hd,gd FROM {$this->_tabList['balance']} WHERE uid=$uid FOR UPDATE";
		$res     = $this->_db->query( $sql, $this->_dbType );
		$row     = $res->fetch_assoc();
		$balance = array(
			'hb' => (int)$row['hb'],
			'gb' => (int)$row['gb'],
			'hd' => (int)$row['hd'],
			'gd' => (int)$row['gd']
		);

//		echo json_encode( $balance ) . "\n";

		return $balance;
	}

	/**
	 * 事务设置余额
	 *
	 * @param $uid
	 * @param $newHB
	 * @param $newGB
	 * @param $newHD
	 * @param $newGD
	 *
	 * @return bool|int
	 */
	private function _setBalanceForTransition( $uid, $newHB, $newGB, $newHD, $newGD )
	{
		$data = array(
			'hb' => $newHB,
			'gb' => $newGB,
			'hd' => $newHD,
			'gd' => $newGD,
		);

		$sql = "update {$this->_tabList['balance']} set hb=$newHB,gb=$newGB,hd=$newHD,gd=$newGD where uid=$uid";

		if ( !$this->_db->query( $sql, $this->_dbType ) )
		{
			return false;
		}

		$result = $this->_db->affectedRows;

		if ( $result == 0 )
		{
			$this->_log(__FUNCTION__.">>".$sql);

			$data['uid']   = $uid;
			$data['ctime'] = $this->_ctime;
			$sql           = $this->_db->insert( $this->_tabList['balance'], $data, true );
			if ( $this->_db->query( $sql, $this->_dbType ) )
			{
				return true;
			}
			else
			{
				$this->_log(__FUNCTION__.">>".$sql);
//				echo $sql."\n";
				return false;
			}
		}
		elseif ( $result > 0 )
		{
			return true;
		}
		else
		{
//			echo $sql."\n";
			$this->_log(__FUNCTION__.">>".$sql);
			return false;
		}
	}


	/**
	 * @param        $uid
	 * @param string $date
	 *
	 * @return mixed
	 */
	public function getReceivedGBByDay( $uid, $date = '' )
	{
		// TODO: Implement getReceivedGBByDay() method.
		$stime = $date . " 00:00:00";
		$etime = $date . " 23:59:59";

		return $this->getReceivedGBByDay( $stime, $etime );
	}

	/**
	 * @param $stime
	 * @param $etime
	 *
	 * @return int
	 */
	public function getReceivedGBByTime( $stime, $etime )
	{
		//TODO: GET received gb by time on month tables
		return 0;
	}


	/**
	 * @param array  $info array('uid'=>'otid',....);
	 * @param float  $rate
	 * @param string $desc
	 * @param int    $type
	 *
	 * @return bool
	 */
	public function setRate( array $info, float $rate, string $desc, int $type = Finance::EXC_HB_GB )
	{
		$rate = bcdiv($rate, self::RATE_PERCENT,3);//$rate / self::RATE_PERCENT;

		return $this->_setRateTranstion( $info, $rate, $desc, $type );
	}


	/**
	 * @param array  $info
	 * @param float  $rate
	 * @param string $desc
	 * @param int    $type
	 *
	 * @return bool
	 */
	private function _setRateTranstion( array $info, float $rate, string $desc, int $type )
	{

		$this->_beginTransition();
		foreach ( $info as $uid => $otid )
		{
			//不允许设置uid为0的情况
			if (!$uid || !$this->_setRate( $uid, $rate, $desc, $otid, $type ) )
			{
				$this->_endTransition( self::TRANSITION_RESULT_FAILED );

				return false;
			}
		}
		$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );

		return true;
	}


	/**
	 * @param int    $uid
	 * @param float  $rate
	 * @param string $desc
	 * @param int    $otid
	 * @param int    $type
	 *
	 * @return bool|\对于更新语句
	 */
	private function _setRate( int $uid, float $rate, string $desc, int $otid, int $type )
	{

		$oldRate = $this->getRate( $uid, $type );
		if ( $this->_addSetRateRecord( $uid, $oldRate, $rate, $desc, $otid, $type ) )
		{
			$update = [ 'rate' => $rate ];
			$data   = [
				'uid'  => $uid,
				'type' => $type,
				'rate' => $rate
			];
			$sql    = $this->_db->insertDuplicate( $this->_tabList['rate'], $data, $update, true );

			return $this->_db->query( $sql );
		}

		return false;
	}

	/**
	 * @param int    $uid
	 * @param float  $rate
	 * @param float  $rateNew
	 * @param string $desc
	 * @param int    $otid
	 * @param int    $type
	 *
	 * @return bool|mixed
	 */
	private function _addSetRateRecord( int $uid, float $rate, float $rateNew, string $desc, int $otid, int $type )
	{
		$port = '';
		$ip   = ip2long( fetch_real_ip( $port ) );

		$data = [
			'uid'     => $uid,
			'rate'    => $rate,
			'rateNew' => $rateNew,
			'desc'    => $desc,
			'otid'    => $otid,
			'type'    => $type,
			'ctime'   => $this->_ctime,
			'ip'      => $ip,
			'port'    => $port
		];

		return $this->_insertRecord( $this->_tabList['changeRate'], $data, self::INSERT_RECORD_ID_AUTOINCREMENT );
	}

	/**
	 * 获取汇率
	 *
	 * @param $uid
	 * @param $type
	 *
	 * @return mixed
	 */
	public function getRate( $uid, $type )
	{
		$sql = $this->_db->field( 'rate' )->where( "(uid=$uid or uid=0) and `type`=$type" )->limit( 1 )->order( 'uid desc' )->select( $this->_tabList['rate'], true );
//		$sql = "select rate from {$this->_tabList['rate']} where (uid =$uid or uid=0) and `type`=$type order by uid desc limit 1";
		$res = $this->_db->query( $sql, $this->_dbType );
		$row = $res->fetch_assoc();

		return $row['rate'];
	}

//	/**
//	 * 将外部数据转换内部使用数据
//	 *
//	 * @param float $num
//	 *
//	 * @return int
//	 */
//	public function getInputNumber( float $num )
//	{
//		return $num * self::MULTIPLE;
//	}
//
//	/**
//	 * 内部数据转换外部使用数据
//	 *
//	 * @param int $num
//	 *
//	 * @return float|int
//	 */
//	public function getOutputNumber( int $num )
//	{
//		return $num / self::MULTIPLE;
//	}

	/**
	 * 为流水账单事务构建用户余额，保持事务完整性
	 *
	 * @param int   $uid
	 * @param array $balance
	 *
	 * @return bool
	 */
	private function _setStatementBalance( int $uid, array $balance )
	{
		$this->_statementBalance[$uid] = $balance;

		return true;
	}

	/**
	 * 获取流水账单事务的用户余额，保持事务完整性
	 *
	 * @param int $uid
	 *
	 * @return bool|mixed
	 */
	private function _getStatementBalance( int $uid )
	{
		$result = !empty( $this->_statementBalance[$uid] ) ? $this->_statementBalance[$uid] : false;
		if ( $result && is_array( $result ) )
		{
			unset( $this->_statementBalance[$uid] );
		}

		return $result;
	}

	/**
	 * 更新流水账单表以及余额
	 *
	 * @param $uid
	 * @param $hbd
	 * @param $gbd
	 * @param $tid
	 * @param $type
	 *
	 * @return bool|对于更新语句
	 */
	private function _statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $tid = false, array $balance = array() )
	{
		//block statement
		$result = true;
		if ( !$tid )
		{
			$this->_log(__FUNCTION__.' tid not vaild');
			return false;
		}

		if ( !$balance )
		{
			$balance = $this->_getBalanceForTransition( $uid );
		}

		$hb = $balance['hb'];
		$gb = $balance['gb'];
		$hd = $balance['hd'];
		$gd = $balance['gd'];

		if ( $result )
		{
			$hbResult = $hb + $hbd;
			$gbResult = $gb + $gbd;
			$hdResult = $hd + $hdd;
			$gdResult = $gd + $gdd;

			$data = array(
				'uid'   => $uid,
				'hb'    => $hbResult,
				'gb'    => $gbResult,
				'hd'    => $hdResult,
				'gd'    => $gdResult,
				'hbd'   => $hbd,
				'gbd'   => $gbd,
				'hdd'   => $hdd,
				'gdd'   => $gdd,
				'tid'   => $tid,
				'type'  => $type,
				'ctime' => $this->_ctime
			);

			$result = $this->_insertRecord( $this->_tabList['statement'], $data, self::INSERT_RECORD_ID_AUTOINCREMENT );

			if ( !$result )
			{
				$this->_log(__FUNCTION__. " 更新流水失败");
			}
		}

		if ( $result )
		{
//			$result = $this->_updateBalance( $uid, $hbResult, $gbResult, $hdResult, $gdResult, $hb, $gb, $hd, $gd );
			$result = $this->_setBalanceForTransition( $uid, $hbResult, $gbResult, $hdResult, $gdResult );
			if ( !$result )
			{
//				echo "\n";
				$this->_log(__FUNCTION__." 更新余额失败");
			}
		}

		return $result;
	}

	/**
	 * 事务处理流程
	 *
	 * @param      $transactionFunc        数据类型为 method => data 其中method 为类内部方法，data为类内部方法所需要的参数
	 *                                     规定：如果 $normal 为true 则为寻常处理，那么第一条纪录为操作纪录，
	 *                                     处理完成之后，将操作纪录的tid分别传入剩余方法中
	 * @param      $tid                    返回的订单ID
	 * @param bool $normal                 是否为寻常模式
	 *
	 * @return bool
	 */
	private function _transaction( $transactionFunc, &$tid, $normal = true )
	{
		if ( !$transactionFunc || !is_array( $transactionFunc ) )
		{
			return false;
		}

		$result = true;
		$tid    = false;

		$this->_beginTransition();
		if ( $normal )
		{
			$obj = array_shift( $transactionFunc );
			$tid = call_user_func_array( [ 'this', $obj['method'] ], $obj['data'] );
			if ( !$tid )
			{
				$result = false;
			}
			unset( $obj );
		}

		if ( $result )
		{
			foreach ( $transactionFunc as $obj )
			{
				if ( $normal && $tid )
				{
					array_push( $obj['data'], $tid );
				}
				if ( is_array( $obj['method'] ) )
				{
					$result = call_user_func_array( $obj['method'], $obj['data'] );
				}
				else
				{
					$result = call_user_func_array( [ 'this', $obj['method'] ], $obj['data'] );
				}

				if ( !$result )
				{
					$result = false;
					break;
				}
			}
		}

		if ( $result )
		{
			$this->_endTransition( self::TRANSITION_RESULT_SUCCESS );
		}
		else
		{
			$this->_endTransition( self::TRANSITION_RESULT_FAILED );
		}

		return $result;
	}

	/**
	 * 构建业务成功输出数据
	 *
	 * @param array $balacne
	 * @param int   $tid
	 *
	 * @return array
	 */
	private function _outputData( array $balacne, int $tid )
	{

		foreach ( $balacne as $key => $value )
		{
			$balacne[$key] = $this->getOutputNumber( intval( $value ) );
		}

		$balacne['tid']   = $tid;
		$balacne['ctime'] = $this->_ctime;

		return $balacne;
	}

	/**
	 * @param $result
	 *
	 * @return int
	 */
	private function _setBizResult( $result = false )
	{
		if ( $this->error() )
		{
			$this->_bizResult = $this->_errno;
		}
		else
		{
			$this->_bizResult = $result;
		}
	}

	/**
	 *
	 */
	protected function _clearError()
	{
		$this->_bizResult = null;
		$this->_errno     = 0;
	}

	/**
	 * @param $result
	 *
	 * @return bool
	 */
	public static function checkBizResult( &$result )
	{
		if ( !$result || $result < 0 )
		{
			$result = array( 'errno' => $result, 'desc' => '' );

			return false;
		}
		elseif ( is_array( $result ) )
		{
			$result = $result;

			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * @return bool
	 */
	private function _doCreateMonthTable()
	{
		$result = true;

		$redisResult = $this->_redis->get( self::CREATE_TABLE_RESULT_REDIS_STRING_KEY );
		$redisResult = $redisResult ? json_decode( $redisResult, true ) ? json_decode( $redisResult, true ) : array() : array();

		$timeStamp          = strtotime( $this->_ctime );
		$redisResultListKey = date( "Ym", $timeStamp );

		if ( isset( $redisResult[$redisResultListKey] ) && $redisResult[$redisResultListKey] == 1 )
		{
//			return $result;
		}

		//清除以前残留数据
		foreach ( $redisResult as $key => $value )
		{
			if ( $key != $redisResultListKey )
			{
				unset( $redisResult[$key] );
			}
		}
		asort( $redisResult );

		$redisResult[$redisResultListKey] = 0;
		$tableNameList                    = $this->_tabList;
		foreach ( $this->_tabListStaticKey as $value )
		{
			unset( $tableNameList[$value] );
		}
		sort( $tableNameList );
		foreach ( $tableNameList as $tableName )
		{
			if ( !$this->_createMonthTable( $tableName, $redisResultListKey ) )
			{
				$result = false;
			}
		}

		if ( $result )
		{
			$redisResult[$redisResultListKey] = 1;
		}

		$this->_redis->set( self::CREATE_TABLE_RESULT_REDIS_STRING_KEY, json_encode( $redisResult ) );
		$this->_redis->expire( self::CREATE_TABLE_RESULT_REDIS_STRING_KEY, 3600 );

		return $result;
	}

	/**
	 * @param $tableName
	 * @param $appendTime
	 *
	 * @return \对于更新语句
	 */
	private function _createMonthTable( $tableName, $appendTime )
	{
		$baseTableName = str_replace( $appendTime, '', $tableName ) . "template";
		$sql           = "CREATE TABLE IF not EXISTS $tableName (LIKE $baseTableName)";

//		echo $sql;

		return $this->_db->query( $sql, $this->_dbType );
	}

	/**
	 *
	 */
	private function _beginTransition()
	{
		$this->_dbType = DBHELPERI_DBW;
		$this->_db->autocommit( false );
		$this->_db->query( 'begin', $this->_dbType );

	}

	/**
	 * @param $result
	 */
	private function _endTransition( $result )
	{
		if ( $result )
		{
			$this->_db->commit();
		}
		else
		{
			$this->_db->rollback();
		}
		$this->_dbType = NULL;
		$this->_db->autocommit( true );
//		$this->_db->disconnect( $this->_dbType );
	}
}




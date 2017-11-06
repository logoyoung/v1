<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/14
 * Time: 下午10:22
 */

/**
 * Class Finance 欢朋财务处理类
 *
 * 包含欢朋网站欢朋币，欢朋金币的相关操作，例如，充值，送礼，兑换，提现等
 */
class Finance
{

	/**
	 * @var 内部金额基数，防止出现小数问题
	 */
	const MULTIPLE = 1000;
	/**
	 * @var 数据库名称前缀
	 */
	const TABLE_PRE = 'hpf_';

	//汇率兑换类型
	/**
	 * @var 欢朋币转换金币
	 */
	const EXC_HB_GB = 1;

	/**
	 * @var 人民币转换欢朋币
	 */
	const EXC_RMB_HB = 2;

	/**
	 * @var 金币转换欢朋币
	 */
	const EXC_GB_HB = 3;

	/**
	 * @var 金豆转换金币
	 */
	const EXC_GD_GB = 4;

	/**
	 * @var 金币转换成人民币
	 */
	const EXC_GB_RMB = 5;

	/**
	 * @var 欢朋币转换成欢朋币
	 */
	const EXC_HB_HB = 6;

	/**
	 * @var 欢朋豆兑换金豆
	 */
	const EXC_HD_GD = 7;

	//流水表类型
	const STATEMENT_TYPE_SEND = 1;//送礼

	const STATEMENT_TYPE_RECHARGE = 2; //充值

	const STATEMENT_TYPE_EXCHANGE = 3; //兑换

	const STATEMENT_TYPE_GD_GB = 4; //金豆=>金币

	const STATEMENT_TYPE_WITHDRAW = 5;//提现

	const STATEMENT_TYPE_INTERNAL_RECHARGE = 6;//内部发放

	const STATEMENT_TYPE_RENAME = 7;//改名

	const STATEMENT_TYPE_SENDBEAN = 8;

	//促销活动状态
	const PROMOTION_OPEN = 1;
	const PROMOTION_CLOSE = 0;

	const PROMOTION_ERR_UNDEFINE = -1;
	const PROMOTION_ERR_UNSTART = -2;
	const PROMOTION_ERR_DEADLINE = -3;

	/**
	 * @var 充值订单创建
	 */
	const RECHARGE_STATUS_CREATE = 0;
	/**
	 * @var 充值订单完成
	 */
	const RECHARGE_STATUS_FINISH = 100;
	/**
	 * @var 充值订单失败
	 */
	const RECHARGE_STATUS_FAILED = 200;

	const DEFAULT_TIMESTAMP = "0000-00-00 00:00:00";

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
	 * @var array 流水账单表中，汇率的类型
	 */
	private $_statementRateType = array();
	/**
	 * @var false|string 类初始化时间｜业务执行时间
	 */
	private $_ctime = '';

	/**
	 * @var int 业务错误编号
	 */
	protected $_errno = 0;

	/**
	 * @var mixed 业务返回结果
	 */
	protected $_bizResult = '';


//	protected $_errstr = '';

	/**
	 * @var array 作为写入流水账单事务的余额，需要手动添加以及销毁
	 */
	protected $_statementBalance = array();

	public function __construct( $db = null, $redis = null )
	{

		if( $redis )
		{
			$this->_redis = $redis;
		}
		else
		{
			$this->_redis = new RedisHelp();
		}

		if( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new DBHelperi_huanpeng();
		}

		$this->_errno = 0;
//		$this->_errstr = '';

		$this->_ctime = date( "Y-m-d H:i:s" );
		//设置所需数据表表名
		$this->_tabList['statement'] = $this->_getMonthTableName( self::TABLE_PRE . 'statement_', $this->_ctime );
		$this->_tabList['gift'] = $this->_getMonthTableName( self::TABLE_PRE . "gift_", $this->_ctime );
		$this->_tabList['rate'] = self::TABLE_PRE . 'rate';
		$this->_tabList['balance'] = self::TABLE_PRE . 'balance';
		//
		$this->_statementRateType = array(
			self::STATEMENT_TYPE_EXCHANGE => self::EXC_HB_GB,
			self::STATEMENT_TYPE_RECHARGE => self::EXC_RMB_HB,
			self::STATEMENT_TYPE_EXCHANGE => self::EXC_GB_HB,
			self::STATEMENT_TYPE_GD_GB => self::EXC_GD_GB,
			self::STATEMENT_TYPE_WITHDRAW => self::EXC_GB_RMB,
			self::STATEMENT_TYPE_INTERNAL_RECHARGE => self::EXC_HB_HB,
			self::STATEMENT_TYPE_SENDBEAN => self::EXC_HD_GD
		);
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
	public function sendGift( int $suid, int $ruid, int $hbd, string $desc )
	{
		$hbd = $this->getRealNumber( $hbd );
		$this->_sendGift( $suid, $ruid, $hbd, $desc );

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
	private function _sendGift( int $suid, int $ruid, int $hbd, string $desc )
	{
		$type = self::STATEMENT_TYPE_SEND;
		$rate = $this->getRate( $ruid, $this->_statementRateType[$type] );

		$shbd = -abs( $hbd );
		$sgbd = 0;

		$rhbd = 0;
		$rgbd = abs( $hbd ) * $rate;

		$tid = false;

		$balance = $this->getBalance( $suid );
		$hb = $balance['hb'];
		$gb = $balance['gb'];

		//将余额塞进到事务中去 保持事务完成性
		$this->_setStatementBalance( $suid, $balance );

		if( ( $shbd < 0 && $hb < $shbd ) || ( $sgbd < 0 && $gb < $sgbd ) )
		{
			$this->_setError( FinanceError::BALANCE_NOT_ENOUGH );
			return false;
		}

		$transactionFunc = array(
			array(
				'method' => '_addSendRecord',
				'data' => [ $suid, $ruid, $shbd, $rgbd, $desc ]
			),
			array(
				'method' => '_statement',
				'data' => [ $suid, $shbd, $sgbd, $type ]
			),
			array(
				'method' => '_statement',
				'data' => [ $ruid, $rhbd, $rgbd, $type ]
			)
		);

		if( $this->_transaction( $transactionFunc, $tid ) )
		{
			$result = $this->_outputData( $this->getBalance( $suid ), $tid );
			$this->_setBizResult( $result );
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 添加送礼记录
	 *
	 * @param int    $suid
	 * @param int    $ruid
	 * @param int    $hbd
	 * @param int    $gbd 主播收益的金币数量
	 * @param string $desc
	 *
	 * @return bool|mixed 成功返回记录ID 失败返回false
	 */
	private function _addSendRecord( int $suid, int $ruid, int $hbd, int $gbd, string $desc )
	{
		$data = array(
			'suid' => $suid,
			'ruid' => $ruid,
			'hbd' => $hbd,
			'gbd' => $gbd,
			'desc' => $desc,
			'ctime' => $this->_ctime
		);
		return $this->_insertRecord( $this->_tabList['gift'], $data );
	}

	private function _sendBean( int $suid, int $ruid, int $hdd, string $desc )
	{
		$type = self::STATEMENT_TYPE_SENDBEAN;
		$rate = $this->getRate( $suid, $this->_statementRateType[$type] );

		$shdd = -abs( $hdd );
		$sgdd = 0;

		$rhdd = 0;
		$rgdd = abs( $hdd * $rate );

		$balance = $this->getBalance( $suid );
		$gd = $balance['gd'];
		$hd = $balance['hd'];

		//将余额塞进到事务中去 保持事务完成性
		$this->_setStatementBalance( $suid, $balance );
		if( ( $shdd < 0 && $hd < $shdd ) || ( $sgdd < 0 && $gd < $sgdd ) )
		{
			$this->_setError( FinanceError::BALANCE_NOT_ENOUGH );
			return false;
		}
	}

	/**
	 * 创建一条充值记录
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
	public function rechargeOrderCreate( int $uid, int $rmb, string $channel, string $client, string $refUrl, int $promotionID, string $desc )
	{
		$rmb = $this->getRealNumber( $rmb );
		$result = $this->_rechargeOrderCreate( $uid, $rmb, $channel, $client, $refUrl, $promotionID, $desc );
		if( is_array( $result ) )
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
	private function _rechargeOrderCreate( int $uid, int $rmb, string $channel, string $client, string $refUrl, int $promotionID, string $desc )
	{
		$type = self::STATEMENT_TYPE_RECHARGE;

		if( $promotionID )
		{
			//todo 促销活动的灵活性比较高，现在这套方案不能提供全面的支撑
			$promotionInfo = $this->getPromotionInfo( $promotionID );
			if( $result = $this->checkPromotionIsValid( $promotionInfo ) )
			{
				return $result;
			}
			//花费金钱为0的情况，类似于0元抢购，的活动
			if( $promotionInfo['rmb'] == 0 )
			{
				$hb = $promotionInfo['hb'];
				$rate = 0;
			}
			else
			{
				$rate = $promotionInfo['hb'] / $promotionInfo['rmb'];
				$hb = $rmb * $rate;
			}
		}
		else
		{
			$rate = $this->getRate( $uid, $this->_statementRateType[$type] );
			$hb = $rmb * $rate;
		}

		$port = 0;
		$ip = ip2long( fetch_real_ip( $port ) );

		$data = array(
			'uid' => $uid,
			'rmb' => $rmb,
			'hb' => $hb,
			'desc' => $desc,
			'client' => $client,
			'ref_url' => $refUrl,
			'ip' => $ip,
			'port' => $port,
			'channel' => $channel,
			'promotionid' => $promotionID,
			'rate' => $rate,
			'ctime' => $this->_ctime
		);

		$id = $this->_insertRecord( $this->_tabList['recharge'], $data );

		if( $id )
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
	public function rechargeOrderFinish( string $transactionID, string $outTradeID, string $openID )
	{
		$this->_rechargeOrderFinish( $transactionID, $outTradeID, $openID );

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
	private function _rechargeOrderFinish( $transactionId, $outTradeId, $openid )
	{
//		RechargeOrder::$orderid = $outTradeId;
//		RechargeOrder::setdb( $this->_db );
		$orderInfo = $this->getRechargeOrderInfo( $outTradeId );
		if( !$orderInfo )
		{
			// todo no order int the huanpeng system
			return false;
		}

		$uid = $orderInfo['uid'];
		$type = self::EXC_RMB_HB;
		$hbd = $orderInfo['hb'];
		$gbd = 0;
		$tid = 0;

		//todo the redis key is should write in the RechargeOrder class
		$rechargeOrderStatusRedisKey = "recharge" . RechargeOrder::$orderid . "-" . RechargeOrder::$uid;
		$this->_redis->set( $rechargeOrderStatusRedisKey, 1, 600 );
		$orderStatus = $orderInfo['status'];

		if( $orderStatus == self::RECHARGE_STATUS_FINISH || $orderStatus == self::RECHARGE_STATUS_FAILED )
		{
			//该订单已经完成
			return true;
		}
		elseif( $orderStatus == self::RECHARGE_STATUS_CREATE )
		{
			$transFunc = array(
				array(
					'method' => '_successPay',
					'data' => [ $outTradeId, $transactionId, $openid ]
				),
				array(
					'method' => "_statement",
					'data' => [ $uid, $hbd, $gbd, $type, $outTradeId ]
				)
			);

			if( $this->_transaction( $transFunc, $tid, false ) )
			{
				$result = $this->_outputData( $this->getBalance( $uid ), $tid );
				$this->_setBizResult( $result );
				return true;
			}
			else
			{
				return false;
			}
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
	public function getRechargeOrderInfo( string $outTradeId )
	{
		$sql = "select * from {$this->_tabList['recharge']} where id=$outTradeId";
		$res = $this->_db->query( $sql );
		$row = $res->fetch_assoc();
		if( $row && is_array( $row ) )
		{
			$row['hb'] = $this->getOutNumber($row['hb']);
			$row['rmb'] = $this->getOutNumber($row['rmb']);
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
	private function _successPay( string $id, string $transactionID, string $openid )
	{
		$sql = "update {$this->_tabList['recharge']} set thrid_order_id = $transactionID, thrid_buyer_id = $openid ,status=" . self::RECHARGE_STATUS_FINISH . " WHERE id=$id";
		$this->_db->query( $sql );
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
	public function withdraw( int $uid, int $gb )
	{
		//先进行金豆的兑换，兑换成欢朋币
		//将金豆兑换的欢朋币与需要兑换的欢朋币相加
		//进行提现
		//？？在金豆=>金币 金币=>人民币 是否需要进行事务呢？
	}

	/**
	 * 主播申请提现
	 *
	 * @param int $uid 主播ID
	 * @param int $gb  提现金币数量
	 *
	 * @return bool 成功返回true 失败返回false
	 */
	private function _withdraw( int $uid, int $gb )
	{
		$type = self::STATEMENT_TYPE_WITHDRAW;
		$rate = $this->getRate( $uid, $this->_statementRateType[$type] );

		$hbd = 0;
		$gbd = -abs( $gb ) * $rate;

		$transFunc = array(
			array(
				'method' => '_addWithdrawRecord',
				'data' => [ $uid, $gb, $rate ]
			),
			array(
				'method' => '_statement',
				'date' => [ $uid, $hbd, $gbd, $type ]
			)
		);

		if( $this->_transaction( $transFunc ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 创建一条提现记录
	 *
	 * @param int    $uid
	 * @param int    $gb
	 * @param int    $rmb
	 * @param string $desc
	 *
	 * @return int|bool 成功返回订单ID，失败返回false
	 */
	private function _addWithdrawRecord( int $uid, int $gb, int $rmb, string $desc )
	{

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
	public function excGB2HB( int $uid, int $gb )
	{
		// TODO: Implement excGB2HB() method.

	}

	/**
	 * 主播兑换私有方法
	 *
	 * @param int $uid 主播ID
	 * @param int $gb  兑换金币数量
	 *
	 * @return bool 成功返回true 失败返回false
	 */
	private function _excGB2HB( int $uid, int $gb )
	{
		$type = self::STATEMENT_TYPE_EXCHANGE;
		$rate = $this->getRate( $uid, $this->_statementRateType[$type] );
		$hbd = $gb * $rate;
		$gbd = -abs( $gb );

		$transFunc = array(
			array(
				'method' => "_addExcGB2HBRecord",
				'data' => [ $uid, $gb, $rate ]
			),
			array(
				'method' => "_stetement",
				'data' => [ $uid, $hbd, $gbd, $type ]
			)
		);

		$tid = 0;
		if( $this->_transaction( $transFunc, $tid ) )
		{
			return true;
		}
		else
		{
			return false;
		}
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
	public function excGD2GB( int $uid, int $bean )
	{
		// TODO: Implement excGD2GB() method.
	}

	/**
	 * 金豆兑换金币，私有方法
	 *
	 * @param int $uid
	 * @param int $bean
	 *
	 * @return bool 成功返回true , 失败返回false
	 */
	private function _excGD2GB( int $uid, int $bean )
	{
		$type = self::STATEMENT_TYPE_GD_GB;
		$rate = $this->getRate( $uid, $this->_statementRateType[$type] );
		$gbd = $bean * $rate;
		$hbd = 0;

		$transFunc = array(
			array(
				'method' => '_addExcGB2GDRecord',
				'data' => [ $uid, $bean, $rate ]
			),
			array(
				'method' => '_statement',
				'data' => [ $uid, $hbd, $gbd, $type ]
			)
		);

		$tid = 0;

		if( $this->_transaction( $transFunc, $tid ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 主播金豆兑换金币记录表
	 *
	 * @param int $uid
	 * @param int $bean
	 * @param int $gb
	 *
	 * @return mixed|bool 成功返回订单ID，失败返回FALSE
	 */
	private function _addExcGB2GDRecord( int $uid, int $bean, int $gb )
	{
	}

	/**
	 * 为记录表创建一条记录
	 *
	 * @param string $tabname 表名称
	 * @param array  $data    记录所需数据
	 *
	 * @return bool|mixed 成功返回订单号，失败返回FALSE
	 */
	private function _insertRecord( string $tabname, array $data )
	{
		$id = $this->_getRecordID();
		$data['id'] = $id;
		$sql = $this->_db->insert( $tabname, $data, true );

		if( $this->_db->query( $sql ) )
		{
			return $id;
		}
		else
		{
			if( $this->_db->errno() != '11000' )
			{
				return false;
			}
			else
			{
				return $this->_insertRecord( $tabname, $data );
			}
		}

	}

	/**
	 * 构建一个记录的ID
	 *
	 * @return mixed
	 */
	private function _getRecordID()
	{
		return microtime( true ) * 10000 + rand( 1000, 9999 );
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
		if( !$promotionInfo )
		{
			$this->_setError( self::PROMOTION_ERR_UNDEFINE );
			return false;
		}

		$stime = $promotionInfo['stime'];
		$etime = $promotionInfo['etime'];
		$status = $promotionInfo['status'];
		$curTimeStamp = time();
		$stimeStamp = strtotime( $stime );
		$etimeStamp = strtotime( $etime );

		if( self::PROMOTION_CLOSE == $status )
		{
			$this->_setError( self::PROMOTION_ERR_UNDEFINE );
			return false;
		}

		if( self::DEFAULT_TIMESTAMP != $stime && $stimeStamp > $curTimeStamp )
		{
			$this->_setError( self::PROMOTION_ERR_UNSTART );
			return false;
		}

		if( self::DEFAULT_TIMESTAMP != $etime && $etimeStamp < $curTimeStamp )
		{
			$this->_setError( self::PROMOTION_ERR_DEADLINE );
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
		if( !$promotionID )
		{
			return false;
		}
		$sql = "select * from {$this->_tabList['promotion']} where id=$promotionID";
		$res = $this->_db->query( $sql );
		$row = $res->fetch_assoc();

		if( !$row )
		{
			return fasle;
		}
		if( $row['desc'] )
		{
			$row['desc'] = json_decode( $row['desc'], true );
		}

		return $row;
	}

	/**
	 * 获取用户余额
	 *
	 * @param $uid
	 *
	 * @return array
	 */
	public function getBalance( $uid )
	{

		$sql = "select hb, gb, hd, gd from {$this->tabList['balance']} where uid = $uid ";
		$res = $this->db->query( $sql );
		$row = $res->fetch_assoc();

		return array(
			'hb' => (int)$row['hb'],
			'gb' => (int)$row['gb'],
			'hd' => (int)$row['hd'],
			'gd' => (int)$row['gd']
		);
	}

	public function getReceivedGBByDay( $uid, $date = '' )
	{
		// TODO: Implement getReceivedGBByDay() method.
		$stime = $date . " 00:00:00";
		$etime = $date . " 23:59:59";

		return $this->getReceivedGBByDay( $stime, $etime );
	}

	public function getReceivedGBByTime( $stime, $etime )
	{
		//TODO: GET received gb by time on month tables
		return 0;
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
		$sql = "select rate from {$this->tabList['rate']} where (uid =$uid or uid=0) and `type`=$type order by uid desc limit 1";
		$res = $this->db->query( $sql );
		$row = $res->fetch_assoc();

		return $row['rate'];
	}

	/**
	 * 将外部数据转换内部使用数据
	 *
	 * @param int $num
	 *
	 * @return int
	 */
	public function getRealNumber( int $num )
	{
		return $num * self::MULTIPLE;
	}

	/**
	 * 内部数据转换外部使用数据
	 *
	 * @param int $num
	 *
	 * @return float|int
	 */
	public function getOutNumber( int $num )
	{
		return $num / self::MULTIPLE;
	}

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
		$result = $this->_statementBalance[$uid] ? $this->_statementBalance[$uid] : false;
		if( $result && is_array( $result ) )
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
	private function _statement( $uid, $hbd, $gbd, $hdd, $gdd, $type, $tid = false )
	{
		//block statement
		$result = true;
		if( !$tid )
		{
			return false;
		}

		$balance = $this->_getStatementBalance( $uid );
		if( !$balance )
		{
			$balance = $this->getBalance( $uid );
		}

		$hb = $balance['hb'];
		$gb = $balance['gb'];
		$hd = $balance['hd'];
		$gd = $balance['gd'];

		if( $result )
		{
			$hbResult = $hb + $hbd;
			$gbResult = $gb + $gbd;
			$hdResult = $hd + $hdd;
			$gdResult = $gd + $gdd;

			$sql = "insert into {$this->_tabList['statement']} (`uid`, `hb`, `gb`,`hd`,`gd`, `hbd`, `gbd`,`hdd`,`gdd`, `tid`, `type`)" .
				" value($uid, $hbResult, $gbResult,$hdResult, $gdResult, $hbd, $gbd,$hdd, $gdd, '$tid', $type)";
			$result = $this->db->query( $sql );
		}

		if( $result )
		{
			$result = $this->_updateBalance( $uid, $hbResult, $gbResult, $hb, $gb );
		}
		return $result;
	}

	/**
	 * 更新余额
	 *
	 * @param $uid
	 * @param $newHb
	 * @param $newGb
	 * @param $oldHb
	 * @param $oldGb
	 *
	 * @return int
	 */
	private function _updateBalance( $uid, $newHB, $newGB, $newHD, $newGD, $oldHB, $oldGB, $oldHD, $oldGD )
	{
		$data = array(
			'uid' => $uid,
			'hb' => $newHB,

		);
		$this->_db->where()->update();
		$sql = "update {$this->_tabList['balance']} set hb=$newHb, gb=$newGb where uid=$uid and hp=$oldHb and gb=$oldGb";
		$this->db->query( $sql );
		return $this->db->affectedRows;
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
		if( !$transactionFunc || !is_array( $transactionFunc ) )
		{
			return false;
		}

		$result = true;
		$tid = false;

		$this->db->autocommit( false );
		$this->db->query( 'begin' );
		if( $normal )
		{
			$obj = array_shift( $transactionFunc );
			$tid = call_user_func_array( [ 'this', $obj['method'] ], $obj['data'] );
			if( !$tid )
			{
				$result = false;
			}
			unset( $obj );
		}

		if( $result )
		{
			foreach ( $transactionFunc as $obj )
			{
				if( $normal && $tid )
				{
					array_push( $obj['data'], $tid );
				}
				if( is_array( $obj['method'] ) )
				{
					$result = call_user_func_array( $obj['method'], $obj['data'] );
				}
				else
				{
					$result = call_user_func_array( [ 'this', $obj['method'] ], $obj['data'] );
				}

				if( !$result )
				{
					$result = false;
					break;
				}
			}
		}

		if( $result )
		{
			$this->db->commit();
		}
		else
		{
			$this->db->rollback();
		}

		$this->db->autocommit( true );
		return $result;
	}

	/**
	 * 按照时间获取按月分表的表明
	 *
	 * @param string $pre
	 * @param string $date Y-m-d H:i:s
	 *
	 * @return string
	 */
	private function _getMonthTableName( string $pre, string $date )
	{
		$timestamp = strtotime( $date );
		return $pre . date( "Ym", $timestamp );
	}

	/**
	 * 设置业务错误代码
	 *
	 * @param int $errno 错误代码
	 */
	private function _setError( int $errno )
	{
		$this->_errno = $errno;
	}

	/**
	 * 获取业务错误代码
	 *
	 * @return int
	 */
	public function errorno()
	{
		return $this->_errno;
	}

	/**
	 * 检测是否发生错误
	 *
	 * @return bool
	 */
	public function error()
	{
		return ( $this->_errno != 0 );
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
		$balacne['tid'] = $tid;
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
		if( $this->error() )
		{
			$this->_bizResult = $this->_errno;
		}
		else
		{
			$this->_bizResult = $result;
		}
	}
}




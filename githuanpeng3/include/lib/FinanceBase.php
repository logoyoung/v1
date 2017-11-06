<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/5/2
 * Time: 14:07
 */

namespace lib;


class FinanceBase
{
	/**
	 * @const 内部金额基数，防止出现小数问题
	 */
	const MULTIPLE = 1000;
	/**
	 * @const 数据库名称前缀
	 */
	const TABLE_PRE = 'hpf_';

	//汇率兑换类型
	/**
	 * @const 欢朋币转换金币
	 */
	const EXC_HB_GB = 1;

	/**
	 * @const 人民币转换欢朋币
	 */
	const EXC_RMB_HB = 2;

	/**
	 * @const 金币转换欢朋币
	 */
	const EXC_GB_HB = 3;

	/**
	 * @const 金豆转换金币
	 */
	const EXC_GD_GB = 4;

	/**
	 * @const 金币转换成人民币
	 */
	const EXC_GB_RMB = 5;

	/**
	 * @const 欢朋币转换成欢朋币
	 */
	const EXC_HB_HB = 6;

	/**
	 *
	 */
	const EXC_HD_GD = 7;

	/**
	 * @const 欢朋豆转换成欢朋豆
	 */
	const EXC_HD_HD = 8;

	/**
	 *
	 */
	const EXC_GIFT_CLANELDER = 9;//族长收益比率类型

	/**
	 * @const 约玩比率类型
	 */
	const EXC_DUE = 10;

	/**
	 *
	 */
	const RATE_HB_GB = 0.1;

	/**
	 *
	 */
	const RATE_PERCENT = 100;

	//流水表类型
	/**
	 * @const 用户送礼
	 */
	const STATEMENT_TYPE_SENDGIFT = 1;//送礼
	/**
	 *
	 */
	const STATEMENT_TYPE_RECHARGE = 2; //充值
	/**
	 *
	 */
	const STATEMENT_TYPE_EXCHANGE = 3; //兑换
	/**
	 *
	 */
	const STATEMENT_TYPE_GD_GB = 4; //金豆=>金币
	/**
	 *
	 */
	const STATEMENT_TYPE_WITHDRAW = 5;//提现
	/**
	 *
	 */
	const STATEMENT_TYPE_INTERNAL_RECHARGE = 6;//内部发放
	/**
	 *
	 */
	const STATEMENT_TYPE_INNERCOST = 7;//改名
	/**
	 *
	 */
	const STATEMENT_TYPE_SENDBEAN = 8;
	/**
	 *
	 */
	const STATEMENT_TYPE_GETBEAN = 9;

	const STATEMENT_TYPE_WITHDRAWREFUND = 10;

	//注 类型号 > 10000 的 为担保交易类型

	/**
	 * @const 约玩账单下单
	 */
	const STATEMENT_TYPE_DUE_ORDERED = 10001;

	/**
	 * @const 约玩账单支付成功
	 */
	const STATEMENT_TYPE_DUE_FINISH = 10002;

	/**
	 * @const 约玩账单退款
	 */
	const STATEMENT_TYPE_DUE_BACK = 10003;

	/**
	 * @const 用户通过任务获取欢朋豆
	 */
	const GET_BEAN_CHANNEL_TASK = 1;

	/**
	 * @const 用户通过到时领取获取欢朋豆
	 */
	const GET_BEAN_CHANNEL_TIME = 2;

	/**
	 * @const 用户通过开启宝箱获取欢朋豆
	 */
	const GET_BEAN_CHANNEL_TREASURE = 3;

	/**
	 * @const 每日首冲
	 */
	const GET_BEAN_CHANNEL_RECHARGE_ACTIVITY_DAY = 100;
	/**
	 * @const 每月首冲
	 */
	const GET_BEAN_CHANNEL_RECHARGE_ACTIVITY_MONTH = 110;

	/**
	 * @const 邀请渠道
	 */
	const GET_BEAN_CHANNEL_INVITE_ACTIVITY = 120;

	/**
	 *
	 */
	const COST_HB_CHANNEL_NICK = 1;

	/**
	 *
	 */
	const INTERNAL_RECHARGE_BY_SYSTEM = 0;

	/**
	 *
	 */
	const INTERNAL_RECHARGE_BY_GIFT = 1;//家族族长额外收益

	//促销活动状态
	/**
	 *
	 */
	const PROMOTION_OPEN = 1;
	/**
	 *
	 */
	const PROMOTION_CLOSE = 0;

	/**
	 *
	 */
	const PROMOTION_ERR_UNDEFINE = -1;
	/**
	 *
	 */
	const PROMOTION_ERR_UNSTART = -2;
	/**
	 *
	 */
	const PROMOTION_ERR_DEADLINE = -3;

	/**
	 *
	 */
	const CREATE_TABLE_RESULT_REDIS_STRING_KEY = 'hpf:createMonth';

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


	const SEND_GIFT_TYPE_MONEY = 0;
	const SEND_GIFT_TYPE_PACK = 1;


	const GUARANTEE_STATUS_ORDERED  = 1;
	const GUARANTEE_STATUS_FREEZE   = 5;
	const GUARANTEE_STATUS_FINISHED = 100;
	const GUARANTEE_STATUS_BACKED   = 200;

	const GUARANTEE_CRON_ACTION_PAY    = 10;
	const GUARANTEE_CRON_ACTION_REFUND = 20;

	const GUARANTEE_CRON_STATUS_UNFINISHED = 1;
	const GUARANTEE_CRON_STATUS_FREEZE     = 5;
	const GUARANTEE_CRON_STATUS_FINISHED   = 20;

	const GUARANTEE_LOG_GROUP_SYSTEM = 1;
	const GUARANTEE_LOG_GROUP_FRONT  = 10;
	const GUARANTEE_LOG_GROUP_ADMIN  = 20;
//	/**
//	 *
//	 */
//	const WITHDRAW_STATUS_APPLY = 0;
//	/**
//	 *
//	 */
//	const WITHDRAW_STATUS_CHECKING = 100;
//	/**
//	 *
//	 */
//	const WITHDRAW_STATUS_SUCCESS = 200;
//	/**
//	 *
//	 */
//	const WITHDRAW_STATUS_FAILED = 300;

	const WITHDRAW_TYPE_WITHDRAW = 1;
	const WITHDRAW_TYPE_REFUND = 5;

	/**
	 *
	 */
	const DEFAULT_TIMESTAMP = "0000-00-00 00:00:00";

	/**
	 *
	 */
	const INSERT_RECORD_ID_AUTOINCREMENT = -1;

	/**
	 *
	 */
	const INSERT_RECORD_ID_USESELF = 0;

//	/**
//	 *
//	 */
//	const TRANSITION_RESULT_FAILED = false;
//
//	/**
//	 *
//	 */
//	const TRANSITION_RESULT_SUCCESS = true;

	/**
	 * @const 货币类型：欢朋币
	 */
	const COIN_TYPE_HB = 1;

	/**
	 * @const 货币类型：欢朋豆
	 */
	const COIN_TYPE_HD = 2;

	/**
	 * @const 货币类型：金币
	 */
	const COIN_TYPE_GB = 3;

	/**
	 * @const 货币类型：金豆
	 */
	CONST COIN_TYPE_GD = 4;

	/**
	 * @var int 业务错误编号
	 */
	protected $_errno = 0;

	public function __construct()
	{
		$this->_errno = 0;
	}

	protected function _getTableList( int $timestamp = 0 )
	{
		if ( !$timestamp )
		{
			$timestamp = time();
		}

		$tabList['statement']     = $this->_getMonthTableName( self::TABLE_PRE . 'statement_', $timestamp );        //流水账单表
		$tabList['sendGift']      = $this->_getMonthTableName( self::TABLE_PRE . "sendGiftRecord_", $timestamp );         //送礼记录表
		$tabList['sendBean']      = $this->_getMonthTableName( self::TABLE_PRE . "sendBeanRecord_", $timestamp );         //赠送欢豆记录表
		$tabList['getBean']       = $this->_getMonthTableName( self::TABLE_PRE . "getHDRecord_", $timestamp );    //获取欢豆记录表  不包括内部发放记录
		$tabList['costHb']        = $this->_getMonthTableName( self::TABLE_PRE . "innerCostHBRecord_", $timestamp );    //内部消费欢币记录表
		$tabList['recharge']      = $this->_getMonthTableName( self::TABLE_PRE . "rechargeRecord_", $timestamp );         //充值记录表
		$tabList['exchange']      = $this->_getMonthTableName( self::TABLE_PRE . "exchangeRecord_", $timestamp );         //金币兑换记录表
		$tabList['beanToGB']      = $this->_getMonthTableName( self::TABLE_PRE . "beanToGBRecord_", $timestamp );         //金豆兑换金币记录表
		$tabList['innerRecharge'] = $this->_getMonthTableName( self::TABLE_PRE . 'innerRechargeRecord_', $timestamp ); //内部发放欢朋币记录表
		$tabList['withdraw']      = $this->_getMonthTableName( self::TABLE_PRE . "withdrawRecord_", $timestamp );
		$tabList['changeRate']    = $this->_getMonthTableName( self::TABLE_PRE . "changeRateRecord_", $timestamp );
//		$tabList['changeWithdrawRecord'] = $this->_getMonthTableName( self::TABLE_PRE . "changeWithdrawStatusRecord_", $timestamp );

		//TODO 担保交易记录表创建
		$tabList['guarantee']        = $this->_getMonthTableName( self::TABLE_PRE . "guarantee_", $timestamp );
		$tabList['guaranteeLog']     = $this->_getMonthTableName( self::TABLE_PRE . "guaranteeOrderLog_", $timestamp );
		$tabList['guaranteeCronLog'] = $this->_getMonthTableName( self::TABLE_PRE . "guaranteeCronOrderLog_", $timestamp );


		$tabList['guaranteeCron'] = self::TABLE_PRE . "guaranteeOrderHandle";
		$tabList['rate']          = self::TABLE_PRE . 'rate';        //兑换比例表
		$tabList['balance']       = self::TABLE_PRE . 'balance';        //用户余额表
//		$tabList['changeRate']    = self::TABLE_PRE . 'changeRateRecord';

		return $tabList;
	}

	protected function _getTableListStatisticKey()
	{
		return [
			'rate',
			'balance',
//			'changeRate',
			'guaranteeCron'
		];
	}

	/**
	 * 按照时间获取按月分表的表明
	 *
	 * @param string $pre
	 * @param int    $timestamp unix 时间戳
	 *
	 * @return string
	 */
	protected function _getMonthTableName( string $pre, int $timestamp )
	{
		return $pre . date( "Ym", $timestamp );
	}

	protected function _getMonthTableNameById( $tableName, $id )
	{
		$timestamp = substr( $id, 0, 10 );

		$append = date( "Ym", $timestamp );

		return preg_replace( "/\d{6}/", $append, $tableName );
	}

	/**
	 * 将外部数据转换内部使用数据
	 *
	 * @param float $num
	 *
	 * @return int
	 */
	public function getInputNumber( float $num )
	{
		return bcmul($num, self::MULTIPLE, 3);

	}

	/**
	 * 内部数据转换外部使用数据
	 *
	 * @param int $num
	 *
	 * @return float|int
	 */
	public function getOutputNumber( int $num )
	{
		return $num / self::MULTIPLE;
	}

	/**
	 * 设置业务错误代码
	 *
	 * @param int $errno 错误代码
	 */
	protected function _setError( int $errno )
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

	protected function _clearError()
	{
		$this->_errno = 0;
	}

	protected function _log( $msg )
	{
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}

	protected function _noticeLog( $msg, $logdirname )
	{
		$logfile = LOG_DIR . $logdirname;
		$r       = file_put_contents( $logfile, $msg, FILE_APPEND );

		return $r;
	}
}
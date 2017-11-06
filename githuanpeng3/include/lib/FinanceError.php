<?php
namespace lib;
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/3/30
 * Time: 16:09
 */
class FinanceError
{
	const BALANCE_NOT_ENOUGH = -3514;
	const BEAN_NOT_ENOUGH = -3515;
	const STATEMENT_FAILED = -101;
	const CREATE_ORDER_FAILED = -102;
	const RECHARGE_PAY_FAILED = -103;
	const GUARANTEE_CRON_CREATE_FAILED = -104;
	const ORDER_NOT_EXIST = -105;
	const ORDER_STATUS_FAILED = -106;
	const ORDER_FREEZE_FAILED = -107;
	const ORDER_UNFREEZE_FAILED = -108;

	const PROMOTION_ERR_UNDEFINE = -109;
	const PROMOTION_ERR_UNSTART = -110;
	const PROMOTION_ERR_DEADLINE = -111;

	const ORDER_IS_FREEZE = -112;
}
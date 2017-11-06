<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/3/22
 * Time: 9:45
 */


interface FinanceInterface
{
	/**
	 * 送礼
	 *
	 * @param $suid 送礼人
	 * @param $ruid 收礼人
	 * @param $hbd  送礼欢朋币数
	 * @param $desc 简单描述
	 *
	 * @return mixed 成功返回
	 * 				 array(
	 *               	hp=>123,//欢朋币数
	 *               	gb=>123,//金币数
	 *               	tid=>单据号
	 * 				 )
	 *               失败返回 false
	 *
	 */
	/**
	 * @param int $suid 送礼人ID
	 * @param int $ruid 收礼人ID
	 * @param int $hbd 送礼欢朋币数量
	 * @param int $desc 礼物描述
	 *
	 * @return mixed 成功返回
	 * 				 array(
	 *               	hp=>123,//欢朋币数
	 *               	gb=>123,//金币数
	 *               	tid=>单据号
	 * 				 )
	 *               失败返回 false
	 */
	public function sendGift( int $suid, int $ruid, int $hbd, string $desc );

	/**
	 * 提现功能
	 *
	 * @param $uid
	 * @param $gb 提现金币数量
	 *
	 * @return mixed array(
	 *               	hp=>123,//欢朋币数
	 *               	gb=>123,//金币数
	 *               	tid=>单据号
	 * 				 )
	 *               失败返回 false
	 */
	public function withdraw(int $uid, int $gb );

	/**
	 * 金豆兑换金币
	 * 注意，在此方法中，财务是不会进行对金豆数量的校验的，需要前台校验好以后，在调用此方法
	 *
	 * @param $uid
	 * @param $bean 金豆数量
	 *
	 * @return mixed array(
	 *               	hp=>123,//欢朋币数
	 *               	gb=>123,//金币数
	 *               	tid=>单据号
	 * 				 )
	 *               失败返回 false
	 */
	public function excGD2GB( $uid, $bean );

	/**
	 * 金币兑换欢朋币
	 *
	 * @param $uid
	 * @param $gb 金币数量
	 *
	 * @return mixed array(
	 *               	hp=>123,//欢朋币数
	 *               	gb=>123,//金币数
	 *               	tid=>单据号
	 * 				 )
	 *               失败返回 false
	 */
	public function excGB2HB( $uid, $gb );

	/**
	 * 获取主播余额
	 *
	 * @param $uid
	 *
	 * @return mixed array（
	 *               	hp=>123,
	 *               	gb=>123
	 *               ）
	 */
	public function getBalance( $uid );
	
	/**
	 * 计算主播每天获取的金币
	 *
	 * @param $uid
	 * @param string $date  2017-03-29
	 *
	 * @return int 
	 */
	public function getReceivedGBByDay( $uid, $date = '');
	
	/**
	 * 计算主播每天获取的金豆
	 *
	 * @param $uid
	 * @param string $date  2017-03-29
	 *
	 * @return int 
	 */
//	public function getReceivedGDByDay( $uid, $date = '');



}
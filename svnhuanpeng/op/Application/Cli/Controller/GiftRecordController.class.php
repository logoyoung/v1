<?php
/*
 * 计算每天每种礼物的赠送数量
 */
namespace Cli\Controller;

class GiftRecordController extends \Think\Controller
{


	public function timingGiftData($date = false)
	{
	    if(!$date) {
		  $date = date('Y-m-d', strtotime('-1 day')); 
        }

		$suffix = date('Ym', strtotime($date));
		$Dao_coin = D('giftrecordcoin_' . $suffix);
		$Dao_bean = D('giftrecord_' . $suffix);

		$Dao = D('GiftRecord');
		$where['ctime'] = ['like', "$date%"];
		$where['cost'] = ['gt', 0];
		$coin_result = $Dao_coin->field('giftid,sum(giftnum) as num,sum(cost) as cost')->where($where)->group('giftid')->select();  //金币
		$bean_result = $Dao_bean->field('giftid,sum(giftnum) as num,sum(cost) as cost')->where($where)->group('giftid')->select();  //金豆
		
		
		if($coin_result) {
		   foreach($coin_result as $k=>$v) {
			   $v['date'] = $date;
			   $Dao->add($v, array(), true);
		   }
		}
		if($bean_result) {
		   foreach($bean_result as $k=>$v) {
			   $v['date'] = $date;
			   $Dao->add($v, array(), true);
		   }
		}
        
	}
	
	
    public function insertGiftData()
    {
        $date = '2017-03-01'; 
        do {
            $this->timingGiftData($date);
            $date = date('Y-m-d', strtotime($date) + 86400);  //增加一天
        }while($date < date('Y-m-d'));
    }
}

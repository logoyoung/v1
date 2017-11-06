<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/7
 * Time: 下午6:07
 */


/**
 * 此文件已经费用，代码逻辑已经移植到财务系统中
 *
 */


class HpProduct{
	static $table = 'product_info';
	static $product_id = 0;
	static $db = null;

	static $name = '';
	static $price = '';
	static $val = '';

	public static function setproductid($id){
		static::$product_id = $id;
	}

	public static function setDb($db){
		if(!$db){
			if(!static::$db){
				static::$db = new DBHelperi_huanpeng();
			}
		}else{
			static::$db = $db;
		}
	}

	public static function getifno(DBHelperi_huanpeng $db=null){
		static::setDb($db);
		$sql = "select * from ".static::$table .' where id='.static::$product_id;
		$res = static::$db->query($sql);
		if(!$res){
			return false;
		}
		$row = $res->fetch_assoc();
		if(!$row['id']){
			return false;
		}
		else{
			static::setInfo($row);
			return true;
		}
	}

	private static function setInfo($arr){
		foreach ($arr as $key => $val){
			if(property_exists('HpProduct',$key)){
				$property = $key;
				static::$$property = $val;
			}
		}
	}
}

class RechargeOrder{
	static $id = 0;
	static $orderid = 0;
	static $uid = 0;
	static $status=0;
	static $quantity=0;

	static $db = null;
	static $table = 'recharge_order';
	static $total_price = 0;

	public static function createOrderId(){
		self::setOrderId(date("YmdHis").rand(10000,99999));
		return static::$orderid;
	}

	public static function setOrderId($orderid){
		static::$orderid = $orderid;
	}

	public static function setdb($db){
		if(!$db){
			if(!static::$db){
				static::$db = new DBHelperi_huanpeng();
			}
		}else{
			static::$db = $db;
		}
	}

	public static function createOrder($uid,$quantity,$channel,$client,$refUrl='',$promation_id=0){
		static::setdb();
		static::$uid = $uid;
		$port = '';
		$ip = ip2long(fetch_real_ip($port));

		$data = array(
			'uid'=>$uid,
			'order_id'=>static::createOrderId(),
			'thrid_order_id' => static::$orderid,
			'product_id'=>HpProduct::$product_id,
			'product_price'=>HpProduct::$price,
			'total_price'=>static::totalPrice($quantity,HpProduct::$price, $promation_id),
			'quantity'=>$quantity,
			'channel'=>"$channel",
			'client' => "$client",
			'ip' => $ip,
			'port'=>$port
		);
		if($refUrl)
			$data['ref_url'] = $refUrl;

		if($promation_id)
			$data['promation_id'] = $promation_id;

		return static::$db->insert(static::$table,$data);
	}

	public static function totalPrice($quantity,$price, $promation_id=0){
		if($promation_id){
			//todo the promation rule calculate totalPrice
			return 0;
		}else{
			static::$total_price = $price * $quantity;
			return static::$total_price;
		}
	}

	public static function getOrderStatus($db=null){
		static::setdb($db);
		if(!RechargeOrder::$orderid)
			return false;

		$sql = "select status from ".static::$table." where orderid=".static::$orderid;
		$res = static::$db->query($sql);
		if(!$res)
			return false;

		$row = $res->fetch_assoc();
		return (int)$row['status'];
	}

	public static function getOrderQuantity($db=null){
		static::setdb($db);
		if(!RechargeOrder::$orderid)
			return false;
		$sql = "select quantity from ".static::$table." where orderid=".static::$orderid;
		$res = static::$db->query($sql);
		if(!$res)
			return false;

		$row = $res->fetch_assoc();
		return (int)$row['quantity'];
	}

	public static function successPay($thrid_order_id,$thrid_user_id,DBHelperi_huanpeng $db=null){
		static::setdb($db);
		$data = array(
			'thrid_order_id' => $thrid_order_id,
			'thrid_buyer_id' => $thrid_user_id,
			'paytime'=> date("Y-m-d H:i:s"),
			'status' => RECHARGE_ORDER_FINISH
		);

		$ret = static::$db->where('order_id='.static::$orderid)->update(static::$table,$data);
		mylog('update result is '. $ret, LOGFN_WX_PAY);
		if($ret){
			$affectedRows = static::$db->affectedRows;
			mylog('update result is affected rows is '. $affectedRows, LOGFN_WX_PAY);
			return $affectedRows;
		}else{
			mylog('db error is  '. static::$db->errstr(), LOGFN_WX_PAY);
			return false;
		}
	}

	public static function getInfo($db=null){
		if(!static::$orderid)
			return false;

		static::setDb($db);
		$sql = "select * from ".static::$table .' where order_id='.static::$orderid;
		$res = static::$db->query($sql);
		if(!$res){
			return false;
		}
		$row = $res->fetch_assoc();
		if(!$row['id']){
			return false;
		}
		else{
			static::setInfo($row);
			return true;
		}
	}

	public static function payCount($uid, $db = null)
	{
		static::setDb($db);
		$sql = "select count(id) as num from ". static::$table . " where uid = $uid";
		$res = static::$db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['num'];
	}

	private static function setInfo($arr){
		foreach ($arr as $key => $val){
			if(property_exists('RechargeOrder',$key)){
				$property = $key;
				static::$$property = $val;
			}
		}
	}
}
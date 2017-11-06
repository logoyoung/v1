<?php

namespace Common\Model;

class DueUserCouponModel extends PublicBaseModel
{
	static $grant = 0;
	static $receive = 1;
	static $used = 2;
	static $expired = 3;
	public function getrpkstatus($index = false)
	{
		$hash = ['0'=>'已发放','1'=>'已领取','2'=>'已使用','3'=>'已过期'];
		return $index === false?$hash:$hash[$index];
	}
	public function getgrant(){
		return self::$grant;
	}
	public function getreceive(){
		return self::$receive;
	}
	public function getused(){
		return self::$used;
	}
	public function getexpired(){
		return self::$expired;
	}
}

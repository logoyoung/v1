<?php
/**
 * 按月生成送礼表结构
 * User: yalong
 * Date: 17/05/10
 * Time: 14:50
 */ 
namespace lib;
use \DBHelperi_huanpeng;
use \RedisHelp;
use system\RedisHelper;
use system\DbHelper;

class GiftTable {
	
	const SEND_TYPE_BEAN = 1;
	const SEND_TYPE_COIN = 2;
	
	const TABLE_COIN_GIFT_RECORD = 'giftrecordcoin';
	const TABLE_BEAN_GIFT_RECORD = "giftrecord";
	
	 
	private $db;
	private $redis;
	public static $dbConfName = 'huanpeng';
	public static $redisConf  = 'huanpeng';
	private $date;	   //当前日期  年月
	private $critical; //当月  月末 
	
	//实例化db、redis、当前日期
	public function __construct($db='',$redis=''){
	    $this->redis = $redis!='' ? $redis : RedisHelper::getInstance(self::$redisConf); 
	    $this->db =  $db!='' ? $db : DbHelper::getInstance(self::$dbConfName);

		$this->critical = date('Ymt', $_SERVER['REQUEST_TIME']); //获取本月最后一天
		self::getTableSuffix();
	}
	/*
	 * 创表前  获取表名后缀
	 * 注：极端情况 时间临界点 处理
	 * return string;
	 */
	private function getTableSuffix(){
		if(date("Ymd") == $this->critical && date("Hi")>"2355"){
			$this->date = $this->makeMonth();
		}else $this->date = date('Ym');
	}
	//月份处理
	private function makeMonth(){
	    $month = date("m")+1; //当前月份 +1个月
	    $year  = date("Y");
	    if(mb_strlen($month)==1)
	    {
	        return $year."0". $month;
	    }else 
	    {
	        if($month> 12)
	        {
	            $year+=1;
	            return $year."01";
	        }else
	        {
	            return $year.$month;
	        }
	    }
	}
	/**
	 * 检索  送礼记录表   是否存在  存在
	 * 
	 * @param int $type    类型       1免费礼物(giftrecord) 2 收费礼物 (giftrecordcoin)
	 * @return bool; 
	 */ 
	public function checkTable($type){ 
		if(!in_array($type,array(self::SEND_TYPE_BEAN,self::SEND_TYPE_COIN))) return false;
		$table = $type==1 ? self::TABLE_BEAN_GIFT_RECORD."_".$this->date : self::TABLE_COIN_GIFT_RECORD."_".$this->date ;
		//校验  是否生成过本月的送礼记录表
		$result = $this->redis->sismember("giftMonthTag",$type."_".$this->date);
		//预生成下个月的 送礼记录表
		if(!$result){ 
			$result = $this->createGiftRecordTable($type,$table);
		}
		//返回当前月份的 实际表名
		$prifix = $type==1 ? self::TABLE_BEAN_GIFT_RECORD."_" : self::TABLE_COIN_GIFT_RECORD."_";
		return $prifix.date('Ym');
	}
	/**
	 * 创建 本月送礼记录表结构
	 */
	private function createGiftRecordTable($type,$table){
		//创建 本月的  送礼记录表
		if($type==1)
			$sql = "
				CREATE TABLE IF NOT EXISTS `".$table."` (
			  `id` bigint(20) unsigned NOT NULL COMMENT '记录id',
			  `luid` int(10) unsigned NOT NULL COMMENT '主播id',
			  `liveid` int(10) unsigned NOT NULL COMMENT '直播id',
			  `uid` int(10) unsigned NOT NULL COMMENT '送礼人id',
			  `giftid` tinyint(3) unsigned NOT NULL COMMENT '礼物id',
			  `giftnum` int(10) unsigned NOT NULL COMMENT '礼物数',
			  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '送礼时间',
			  `otid` bigint(20) unsigned NOT NULL DEFAULT 0,
			  `income` float(14,2) NOT NULL DEFAULT '0.00',
			  `cost` float(14,2) NOT NULL DEFAULT '0.00',
			  PRIMARY KEY (`id`),
			  KEY `luid` (`luid`),
			  KEY `liveid` (`liveid`),
			  KEY `uid` (`uid`),
			  KEY `ctime` (`ctime`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;	
			";
		else 
			$sql = "
				create table IF NOT EXISTS `".$table."` (
				  `id` bigint(20) unsigned NOT NULL COMMENT '记录id',
				  `luid` int(10) unsigned NOT NULL COMMENT '主播id',
				  `liveid` int(10) unsigned NOT NULL COMMENT '直播id',
				  `uid` int(10) unsigned NOT NULL COMMENT '送礼人id',
				  `giftid` tinyint(3) unsigned NOT NULL COMMENT '礼物id',
				  `giftnum` int(10) unsigned NOT NULL COMMENT '礼物数',
				  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '送礼时间',
			      `otid` bigint(20) unsigned NOT NULL DEFAULT 0,
				  `income` float(14,2) NOT NULL DEFAULT '0.00',
				  `cost` float(14,2) NOT NULL DEFAULT '0.00',
				  PRIMARY KEY (`id`),
				  KEY `luid` (`luid`),
				  KEY `liveid` (`liveid`),
				  KEY `uid` (`uid`),
				  KEY `ctime` (`ctime`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			";
		$result = $this->db->query($sql);
		if($result){
			//更新 送礼记录redis标记值；
			$this->redis->sadd("giftMonthTag", $type."_".$this->date);
			$this->redis->expire("giftMonthTag", 3600*24);
			return true;
		}
		else return false;
	}
}

?>
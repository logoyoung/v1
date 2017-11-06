<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/14
 * Time: 下午9:02
 */
require_once 'redis.class.php';
require_once 'User.class.php';
class AnchorHelp extends UserHelp{
	public $uid;

	private $db;
	private $redis;

	public function __construct($uid, $db=null){
		$this->uid = (int)$uid;
		if(!$this->uid) return false;

		if($db)
			$this->db = $db;
		else
			$this->db = new DBHelperi_huanpeng();

		$this->redis = new redishelp();
		parent::__construct($uid, $db);
	}


	public function upHpBean($amount){
		$sql = "update anchor set bean=bean + $amount where uid = $this->uid";
		return $this->db->query($sql);
	}

	public function upHpCoin($amount){
		$sql = "update anchor set coin = coin + $amount where uid=$this->uid";
		return $this->db->query($sql);
	}

	public function costHpBean($amount, $balance){
		if(!$amount){
			return true;
		}

		$sql = "update anchor set bean = bean-$amount where uid=$this->uid and bean >= $amount and bean = $balance ";
		if($this->db->query($sql)){
			return $this->db->affectedRows;
		}
		return false;
	}

	public function costHpCoin($amount, $balance){
		if(!$amount){
			return true;
		}

		$sql = "update anchor set coin = coin-$amount where uid=$this->uid and coin >= $amount and coin = $balance ";
		if($this->db->query($sql)){
			return $this->db->affectedRows;
		}
		return false;
	}

	public function updateLevel($exp){
		$lv = $this->getLevelInfo();
		$level = $lv['level'];
		$maxLevel = $this->getMaxLevel();

		$exp += (int)$lv['integral'];

		if($level == $maxLevel){
			$sql = "update anchor set integral=$exp where uid = $this->uid";
			return $this->db->query($sql);
		}

		$nextLevel = $this->getNextLevel($exp);
        if($nextLevel){
            if($nextLevel > $level){
                $this->updatePubVideoLimitCount($nextLevel - $level);
            }
        }else{
            if($level != $maxLevel){
                $this->updatePubVideoLimitCount($maxLevel - $level);
            }
        }

		if($nextLevel){
			$sql = "update anchor set integral=$exp, level=$nextLevel where uid = $this->uid";
		}else{
			$sql = "update anchor set integral=$exp, level=$maxLevel where uid = $this->uid";
		}

		return $this->db->query($sql);
	}

    public function updatePubVideoLimitCount($count){
        $sql = "update anchor set videolimit = videolimit + $count where uid = $this->uid";
        return $this->db->query($sql);
    }

	public function getLevelInfo(){
		$res = $this->db->query("select level, integral from anchor where uid =$this->uid");
		$level = $res->fetch_assoc();
		return $level;
	}

	public function getMaxLevel(){
		$res = $this->db->query("select max(level) as level from anchorlevel");
		$row = $res->fetch_assoc();
		$maxLevel = (int)$row['level'];

		return $maxLevel;
	}

	public function getNextLevel($exp){
		$sql = "select level from anchorlevel where integral >= $exp order by level limit 1";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['level'];
	}
	public function getLastLiveid(){
		$sql = "select liveid from live where uid = $this->uid order by liveid desc limit 1";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['liveid'];
	}

    public function isMyLive($liveid){
        $sql = "select uid from live where uid=$this->uid and liveid = $liveid";
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        if((int)$row['uid'])
            return true;

        return false;
    }

	public function getLevelInfoList(){
		$level = array();
		$sql = "select * from anchorlevel";
		$res = $this->db->query($sql);
		while($row = $res->fetch_assoc()){
			array_push($level, $row);
		}
        return $level;
	}

	public function getUserLevelInfo(){
		return parent::getLevelInfo();
	}

	public function getProperty(){
		$sql = "select coin, bean from anchor where uid=$this->uid";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return $row;
	}

	public function fansCount(){
		$sql = "select count(*) as count from userfollow where uid2=$this->uid";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['count'];
	}

	public function publishedVideoCount(){
		$sql = "select count(*) as count from video where uid=$this->uid";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['count'];
	}

	public function publishedVideoIdList(){
		$sql = "select videoid from video where uid=$this->uid";
		$res = $this->db->query($sql);

		$videoList = array();

		while($row = $res->fetch_assoc()){
			array_push($videoList,$row['videoid']);
		}
		return $videoList;
	}

	public function myRoomManagerIdList(){
		$managerIdList = array();

		$sql = "select uid from roommanager where luid=$this->uid";
		$res = $this->db->query($sql);
		while($row = $res->fetch_assoc()){
			array_push($managerIdList, $row['uid']);
		}

		return $managerIdList;
	}


	public function addRoomManager($uid){
		$ctime = date('Y-m-d H:i:s');
		$sql = "insert into roommanager (luid, uid, ctime) VALUE ($this->uid, $uid, '$ctime') on duplicate key update uid = $uid";
		return $this->db->query($sql);
	}

	public function delRoomManager($uid){
		$sql = "delete from roommanager where uid = $uid  and luid = $this->uid";
		return $this->db->query($sql);
	}
	/**
	 * 获取主播金币数
	 *
	 * @return float
	 */
	public function getCoin(){
		$property = $this->getProperty();
//		return $this->exchangeToCoin($property['coin']);
		return $property['coin'];
	}

	/**
	 * 获取主播金豆数
	 *
	 * @return float
	 */
	public function getBean(){
		$property = $this->getProperty();
		return $this->exchangeToBean($property['bean']);

	}



	/**
	 * 主播获取欢朋币纪录
	 *
	 * @param $from
	 * @param $to
	 * @param $size
	 * @param $page
	 * @return array
	 */
	public function receiveCoinRecord($from, $to, $page, $size){
		$last = ($page - 1) * $size;
		$sql = "select * from giftrecordcoin where luid=$this->uid and ctime BETWEEN '$from' and '$to'  order by ctime desc limit $last, $size";
		$res = $this->db->query($sql);

		$recordList = array();

		while($row = $res->fetch_assoc()){
			array_push($recordList, $row);
		}
		return $recordList;
	}
	public function receiveCoinRecordNumCount($from,$to){
		$sql = "select count(*) as count from giftrecordcoin where luid=$this->uid and ctime BETWEEN  '$from' and '$to'";
		$res = $this->db->query($sql);

		$row = $res->fetch_assoc();

		return (int)$row['count'];
	}
	/**
	 * 获取欢朋豆纪录
	 *
	 * @param $from
	 * @param $to
	 * @param $size
	 * @param $page
	 * @return array
	 */
	public function receiveBeanRecord($from, $to, $page, $size){
		$last = ($page - 1) * $size;
		$sql = "select * from giftrecord where giftid=31 and luid=$this->uid and ctime BETWEEN '$from' AND '$to' ORDER  by id desc limit $last ,$size";
		$res = $this->db->query($sql);

		$recordList = array();

		while($row = $res->fetch_assoc()){
			array_push($recordList, $row);
		}

		return $recordList;
	}

	public  function receiveBeanRecordNumCount($from, $to){
		$sql = "select count(*) as count from giftrecord where luid=$this->uid and ctime between '$from' and '$to'";
		$res = $this->db->query($sql);

		$row = $res->fetch_assoc();

		return (int)$row['count'];
	}

	/**
	 * 本日获取金币数
	 *
	 * @return float
	 */
	public function todayReceiveCoinCount(){
		$stime = date("y-m-d")." 00:00:00";
		$etime = date("Y-m-d")." 23:59:59";

		return $this->receiveCoinCountByTime($stime, $etime);

//		$sql = "select sum(income) as income from billdetail where ctime BETWEEN '$stime' and '$etime' and beneficiaryid=$this->uid and type=0";
//		$res = $this->db->query($sql);
//		$row = $res->fetch_assoc();
//		return $this->exchangeToCoin((int)$row['income']);
	}

	/**
	 * 今日获取金豆数
	 *
	 * @return float
	 */
	public function todayReceiveBeanCount(){
		$stime = date("y-m-d")." 00:00:00";
		$etime = date("Y-m-d")." 23:59:59";

        return $this->receiveBeanCountByTime($stime, $etime);

//		$sql = "select sum(giftnum) as income from giftrecord where ctime BETWEEN '$stime' and '$etime' and luid = $this->uid";
//		$res = $this->db->query($sql);
//		$row = $res->fetch_assoc();
//		return $this->exchangeToBean((int)$row['income']);
	}

    /**
     * 获取本月金币数
     * @return float
     */
    public function monthReceiveCoinCount(){
        $stime = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $etime = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('t'), date('Y')));

        return $this->receiveCoinCountByTime($stime, $etime);

//        $sql = "select sum(income) as income from billdetail where ctime BETWEEN '$stime' and '$etime' and beneficiaryid=$this->uid and type=0";
//        $res = $this->db->query($sql);
//        $row = $res->fetch_assoc();
//        return $this->exchangeToCoin((int)$row['income']);
    }

    /**
     * 获取本月金豆数
     * @return float
     */
    public function monthReceiveBeanCount(){
        $stime = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $etime = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('t'), date('Y')));

        return $this->receiveBeanCountByTime($stime,$etime);

//        $sql = "select sum(giftnum) as income from giftrecord where ctime BETWEEN '$stime' and '$etime' and luid = $this->uid";
//        $res = $this->db->query($sql);
//        $row = $res->fetch_assoc();
//        return $this->exchangeToBean((int)$row['income']);
    }

    public function receiveBeanCountByTime($stime,$etime){
        $sql = "select sum(giftnum) as income from giftrecord where ctime between '$stime' and '$etime' and luid = ".$this->uid;
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return $this->exchangeToBean((int)$row['income']);
    }

    public function receiveCoinCountByTime($stime,$etime){
        $sql = "select sum(income) as income from billdetail where ctime BETWEEN '$stime' and '$etime' and beneficiaryid=$this->uid and type=0";
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return $this->exchangeToCoin((int)$row['income']);
    }

    public function receiveCoinCount($liveid){
        $gift = $this->getGiftInfo();
        $coin = 0;
        $sql = "select giftid, giftnum from giftrecordcoin where luid = $this->uid and liveid = $liveid";
        $res = $this->db->query($sql);
        while($row = $res->fetch_assoc()){
            $coin += ($gift[$row['giftid']]['money'] * $row['giftnum']);
        }

        return $this->exchangeToCoin($coin);
    }
    public function receiveBeanCount($liveid){
        $sql = "select sum(giftnum) as income from giftrecord where luid = $this->uid and liveid = $liveid";
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return $this->exchangeToBean((int)$row['income']);
    }
	/**
	 * 主播提现纪录
	 *
	 * @return array
	 */
	public function withdrawRecordList(){
		return array();
	}


	public function checkBank($bankid,$card){

		$row = $this->db->field('bankid','id')->where('uid='.$this->uid." and cardid='$card'")->select();
		if(!$row || !$row[0]['id']){
			return false;
		}

		if($row['0']['bankid'] != $bankid){
			return false;
		}

		return (int)$row[0]['id'];
	}


	/**
	 * 主播提现
	 *
	 * @param $coin
	 * @param $bean
	 * @return int 成功返回0，失败返回错误代码
	 *
	 */
	public function withdraw($coin, $bean, $cardid){

		$hpBean = $this->exchangeToHpBean($bean);
		$hpCoin = $this->exchangeToHpCoin($coin);

//		if(!$this->isWithdrawTime()){
//			return -5012;//非提现时间
//		}
//		if($this->isWithdrawed()){
//			return -5013;//本月已经提现过了
//		}
		if($bean && $bean < 100){
			return -4027;//金豆数必须大于100
		}
		if(($money = $this->countWithdrawMoney($coin, $bean)) < 800){
			return -4028;//提现金额必须大于800
		}
		$property = $this->getProperty();

		if($coin && $property['coin'] < $hpCoin){
			return -4029;
		}
		if( $bean && $property['bean'] < $hpBean){
			return -4030;
		}

		$lock = $this->getWithDrawLock();
		if($lock){
			return -5014;
		}
//		提现处理锁
		$this->setWithdrawLock();

		$this->db->autocommit(false);
		$this->db->query('begin');
		$id = $this->_withdrawRecord($coin, $bean, $cardid);

		if(!$id
			|| !$this->_withdrawBillRecord($id,$money) //填写账单记录 应该在提现成功以后填写
			|| !$this->withdrawHpBean($hpBean,$property['bean']) || !$this->withdrawHpCoin($hpCoin, $property['coin'])){
			$this->db->rollback();
			$this->delWithDrawLock();
			return -5014;
		}else{
			$this->db->commit();
			$this->db->autocommit(true);
			$this->delWithDrawLock();
			return 0;
		}

//		if(!$id || !$this->_withdrawBeanRecord($id, $hpBean) || !$this->_withdrawCoinRecord($id, $hpCoin) || !$this->withdrawHpBean($hpBean, $property['bean'])
//				|| !$this->costHpCoin($hpCoin, $property['coin'])){
//			$this->db->rollback();
//			$this->delWithDrawLock();
//			return -5014;
//		}else{
//			$this->db->commit();
//			$this->db->autocommit(true);
//			$this->delWithDrawLock();
//			return 0;
//		}
	}

	public function withdrawHpBean($costBean,$bean){
		if(!$costBean) return true;

		return $this->costHpBean($costBean,$bean);
	}

	public function withdrawHpCoin($costCoin,$coin){
		if(!$costCoin) return true;

		return $this->costHpCoin($costCoin, $coin);
	}

	private function setWithdrawLock(){
		$this->redis->set('withdraw:'.$this->uid, '1');
	}
	private function getWithDrawLock(){
		return (int)$this->redis->get('withdrwa:'.$this->uid);
	}
	private function delWithDrawLock(){
		$this->redis->del('withdrwa:'.$this->uid);
	}
	/**
	 *更新欢朋币提现纪录
	 *
	 * @param 	int 	$id		提现纪录ID
	 * @param 	int		$hpCoin
	 * @return bool|对于更新语句
	 */
	private function _withdrawCoinRecord($id, $hpCoin){
		if(!$id) return false;

		$sql = "insert into billdetail(customerid, purchase, beneficiaryid,income,type,info) values($this->uid,$hpCoin, 0,$hpCoin,".BILL_CASH_COIN.",'$id')";
		return $this->db->query($sql);
	}

	/**
	 * 更新欢朋豆提现纪录
	 *
	 * @param int  $id 		提现纪录ID
	 * @param int  $hpBean
	 * @return bool|对于更新语句
	 */
	private function _withdrawBeanRecord($id, $hpBean){
		if(!$id) return false;
		if(!$hpBean) return true;

		$sql = "insert into billdetail(customerid, purchase, beneficiaryid,income,type,info) values($this->uid,$hpBean, 0,$hpBean,".BILL_CASH_BEAN.",'$id')";

		return $this->db->query($sql);
	}


	public function _withdrawBillRecord($id,$money){

		$data = array(
			'customerid'=>0,
			'purchase' => $money,
			'beneficiaryid'=>$this->uid,
			'income'=>$money,
			'type'=>BILL_CASH,
			'info'=>$id
		);

		return $this->db->insert('billdetail',$data);
	}

	/**
	 * 更新提现纪录表
	 *
	 * @param $coin
	 * @param $bean
	 * @return int
	 */
	private function _withdrawRecord($coin,$bean,$cardid){
		$id = date('YmdHis').random(4,1);
		$coinMoney = $this->countWithdrawCoinMoney($coin);
		$beanMoney = $this->countWithdrawBeanMoney($bean);
		$money = $beanMoney + $coinMoney;

		$sql = "insert into withdrawRecord (id,luid, coin, bean, coinMoney, beanMoney, Money,cardid) value('$id', $this->uid, $coin, $bean, $coinMoney, $beanMoney, $money, $cardid)";
		if($this->db->query($sql)){
			return $id;
		}else{
			return 0;
		}
	}


	/**
	 * 欢朋豆 转换成金豆
	 *
	 * @param $hpBean
	 * @return float
	 */
	public static function exchangeToBean($hpBean){
		return ($hpBean / 1000);
	}

	/**
	 * 欢朋币转换成金币
	 *
	 * @param $hpCoin
	 * @return float
	 */
	public static function exchangeToCoin($hpCoin){
		return $hpCoin / 20;
	}

	/**
	 * 金豆转换成欢朋豆
	 *
	 * @param $bean
	 * @return mixed
	 */
	public static function exchangeToHpBean($bean){
		return $bean * 1000;
	}

	/**
	 * 金币转换成欢朋币
	 *
	 * @param $coin
	 * @return mixed
	 */
	public static function exchangeToHpCoin($coin){
		return $coin * 20;
	}

	/**
	 *计算提现总额
	 *
	 * @param $coin
	 * @param $bean
	 * @return mixed
	 */
	public function countWithdrawMoney($coin, $bean){
		return $this->countWithdrawBeanMoney($bean) + $this->countWithdrawCoinMoney($coin);
	}

	/**
	 * 计算金币->RMB
	 *
	 * @param $coin
	 * @return float
	 */
	public function countWithdrawCoinMoney($coin){
		return $coin;
	}

	/**
	 * 计算金豆->RMB
	 *
	 * @param $bean
	 * @return float
	 */
	public function countWithdrawBeanMoney($bean){
		return $bean;
	}

	/**
	 * 是否是提现日期
	 *
	 * @return bool
	 */
	public function isWithdrawTime(){
		return true;
		$day = date('d');
		if($day >= 15 && $day <= 18)
			return true;

		return false;
	}

	/**
	 * 本月是否提现过
	 *
	 * @return bool
	 */
	public function isWithdrawed(){
		$start = date("Y-m").'-01 00:00:00';
		$end = date("Y-m-t")." 23:59:59";
		$sql = "select id from withdrawRecord where luid=$this->uid and ctime between '$start' and '$end'";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return $row['id'] ? true : false;
	}

	/**
	 * 获取当前月提现信息
	 *
	 * @return array
	 */
	public function currentWithdrawInfo(){
		$start = date("Y-m").'-01 00:00:00';
		$end = date("Y-m-t")." 23:59:59";
		$sql = "select * from withdrawRecord where luid=$this->uid and ctime BETWEEN  '$start' and '$end'";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return $row;
	}

	public function getWithdrawRecord(){
		$sql = "select * from withdrawRecord where luid = $this->uid";
		$res = $this->db->query($sql);

		$record = array();
		while($row = $res->fetch_assoc()){
			array_push($record, $row);
		}

		return $record;
	}

	public function isAnchor(){
		$sql = "select uid from anchor where uid = $this->uid";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['uid'] ? true : false;
	}

	public function isRealAnchor($anchor){

		return ($anchor && ( !RN_MODEL || $this->getRealNameCertifyInfo()['status'] == 101 ));
	}

    public function isBlack(){
        $sql = "select luid from anchor_blackList where luid=$this->uid";
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return (int)$row['luid'] ? true : false;
    }

	public function isLiving(){
		$sql = "select liveid from live where uid = $this->uid and status =" . LIVE;
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();
		return (int)$row['liveid'];
	}

    public function getMyLivingInfo(){
        $liveid = $this->getLastLiveid();
        $sql = "select * from live where liveid = $liveid";
        $res = $this->db->query($sql);

        $row = $res->fetch_assoc();

        return $row;
    }


    public static function getRoomIDs($luids, $db){
		if(!$luids || !is_array($luids))
			return false;

		$list = '('. implode(',',$luids).')';
		$sql = "select uid,roomid from roomid where uid in $list";
		$res = $db->query($sql);

		$result = array();

		while($row = $res->fetch_assoc()){
			$result[$row['uid']] = $row['roomid'];
		}

		return $result;
	}

    /**
     * 获取房间ID
     * @return bool|int
     */
	public function getRoomID(){
        $sql = "select roomid from roomid where uid={$this->uid}";
        $res = $this->db->query($sql);
        $row = mysqli_fetch_row($res);
        return isset($row[0])?(int)$row[0]:false;
    }
} 
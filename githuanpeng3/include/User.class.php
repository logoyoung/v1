<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/14
 * Time: 下午4:30
 */
require_once 'redis.class.php';
class UserHelp{
	public $uid;
	public static $db2;
	private $db;
	private $redis;

	public function __construct($uid, $db = null){
		$this->uid = (int)$uid;
		if(!$this->uid) return false;

		if($db)
			$this->db = $db;
		else
			$this->db = new DBHelperi_huanpeng();

		$this->redis = new redishelp();

		return true;
	}

	/**
	 * 获取用户 nick and pic
	 *
	 * @return mixed
	 */
	public function getUsers(){
		$sql = "select nick, pic from userstatic where uid = $this->uid";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();
        if(!$row['pic'])
            $row['pic'] = DEFAULT_PIC;
        else
            $row['pic'] = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . '/'.$row['pic'];
		return $row;
	}


	public function  upHpbean($amount){
		$sql = "update useractive set hpbean=hpbean + $amount where uid = $this->uid";
		return $this->db->query($sql);
	}

	public function upHpcoin($amount){
		$sql = "update useractive set hpcoin=hpcoin + $amount where uid = $this->uid";
		return $this->db->query($sql);
	}

	/**
	 * 用户消费欢朋币
	 * @param	int 	$amount 	欢朋币数量
	 * @param	int 	$balance 	校准余额
	 * @return bool|int
	 */
	public function costHpCoin($amount,$balance){
		$sql = "update useractive set hpcoin = hpcoin-$amount where uid=$this->uid and hpcoin >= $amount and hpcoin = $balance ";
		if($this->db->query($sql)){
			return $this->db->affectedRows;
		}
		return false;
	}

	/**
	 * 用户消费欢朋豆
	 * @param	int 	$amount 	欢朋币数量
	 * @param	int 	$balance 	校准余额
	 * @return bool|int
	 */
	public function costHpBean($amount, $balance){
		$sql = "update useractive set hpbean = hpbean-$amount where uid=$this->uid and hpbean >= $amount and hpbean = $balance";
		if($this->db->query($sql)){
			return $this->db->affectedRows;
		}
		return false;
	}

	/**
	 * 检查用户登录错误
	 * @param $enc
	 * @return int  0:没有错误，< 0 错误代码
	 */
	public function checkStateError($enc){
		$row = $this->db->field('encpass')->where('uid='.$this->uid)->select('userstatic');
		if(!$row[0])
			return -1014;

		if($row[0]['encpass'] != $enc)
			return -1013;
		return 0;
	}

	/**
	 * 更新用户等级
	 * @param	int	$exp 经验值
	 * @return bool|对于更新语句
	 */
	public function updateLevel($exp){
		$lv = $this->getLevelInfo();
		$level = $lv['level'];
		$maxLevel = $this->getMaxLevel();
		$exp += (int)$lv['integral'];
		if($level == $maxLevel){
			$this->db->query("update useractive set integral=$exp where uid = $this->uid");
			return true;
		}
		$res = $this->db->query("select * from userlevel where integral >= $exp order by level limit 1");
		$row = $res->fetch_assoc();
		$level = $row['level'];
		if($level){
			return $this->db->query("update useractive set integral=$exp,level=$level where uid = $this->uid");
		}else{
			return $this->db->query("update useractive set integral=$exp, level=$maxLevel where uid = $this->uid");
		}
	}

	/**
	 * 获取用户等级信息
	 * @return mixed
	 */
	public  function getLevelInfo(){
		$res = $this->db->query('select level, integral from useractive where uid='.$this->uid);
		$level = $res->fetch_assoc();
		return $level;
	}

	/**
	 * 获取用户最大等级
	 * @return int
	 */
	public  function getMaxLevel(){
		$res = $this->db->query("select max(level) as level from userlevel");
		$row = $res->fetch_assoc();
		$maxLevel = (int)$row['level'];
		return $maxLevel;
	}

	/**
	 * 获取等级列表信息
	 * @return array
	 */
	public function getLevelInfoList(){
		$sql = "select * from userlevel";
		$res = $this->db->query($sql);
		$level = array();
		while($row = $res->fetch_assoc()){
			array_push($level, $row);
		}
		return $level;
	}

	/**
	 * 获取个人财产
	 * @return mixed
	 * array(
	 * 	hpcoin => '',
	 * 	hpbean => ''
	 * )
	 */
	public function getProperty(){
		$res = $this->db->query("select hpcoin, hpbean from useractive where uid = $this->uid");
		$row = $res->fetch_assoc();
		return $row;
	}

	/**
	 * 获取用户手机认证状态
	 * @return mixed
	 */
	public function getPhoneCertifyInfo(){
		$row = $this->db->field('phone')->where('uid='.$this->uid)->select('userstatic');
		$phone = $row[0]['phone'];

		if(!$phone){
			$r['phone'] = '';
			$r['status'] = 0;
		}else{
			$r['phone'] = $phone;
			$r['status'] = 1;
		}

		return $r;
	}

	/**
	 * 获取邮箱认证状态
	 * @return mixed
	 */
	public function getEmailCertifyInfo(){
		$row = $this->db->field('mail, mailstatus')->where('uid = ' . $this->uid)->select('userstatic');
        $ret['mail'] = $row[0]['mail'];
        $ret['status'] = $row[0]['mailstatus'];
        return $ret;
	}

	/**
	 * 获取实名认证状态
	 * @return mixed
	 */
	public function getRealNameCertifyInfo(){
		$row = $this->db->field('id, papersid, status')->where('uid=' . $this->uid)->select('userrealname');
		if(empty($row)){
			$r['ident'] = '';
			$r['status'] = 0;
		}else{
			$r['ident'] = $row[0]['papersid'];
			$r['status'] = (int)$row[0]['status'];
		}

		return $r;
	}

	/**
	 * 获取银行卡认证状态
	 * @return mixed
	 */
	public function getBankCertifyInfo(){
		$row = $this->db->field('id,cardid, status')->where('uid=' . $this->uid)->select('userbankcard');
		if(!isset($row[0]['id'])){
			$r['bank'] = '';
			$r['status'] = 0;
		}else{
			$r['bank'] = $row[0]['cardid'];
			$r['status'] = (int)$row[0]['status'];
		}

		return $r;
	}

	/**
	 * 获取认证银行卡的银行
	 * @return string
	 */
	public function getCertifyBankName(){
		$row = $this->db->field('bank')->where('uid=' . $this->uid)->select('userbankcard');
		if($row[0]['bank']){
			return $row[0]['bank'];
		}else{
			return '';
		}
	}

	/**
	 * 获取用户认证信息
	 * @return mixed
	 */
	public function getCertifyInfo(){
		$tmp = $this->getEmailCertifyInfo();
		$r['email'] = $tmp['mail'];
		$r['emailstatus'] = (int)$tmp['status'];

		$tmp = $this->getPhoneCertifyInfo();
		$r['phone'] = $tmp['phone'];
		$r['phonestatus'] = $tmp['status'];

		$tmp = $this->getRealNameCertifyInfo();
		$r['ident'] = $tmp['ident'];
		$r['identstatus'] = $tmp['status'];

		$tmp = $this->getBankCertifyInfo();
		$r['bank'] = $tmp['bank'];
		$r['bankstatus'] = $tmp['status'];

		return $r;
	}

	/**
	 * 是否关注
	 * @param $luid
	 * @return bool
	 */
	public function isFollow($luid){
		$sql = "select uid1 from userfollow where uid1=$this->uid and uid2 = $luid";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['uid1'] ? true : false;
	}

	/**
	 * 关注主播
	 * @param $luid
	 * @return 对于更新语句
	 */
	public function followAnchor($luid){
		$sql = "insert into userfollow (uid1, uid2) value($this->uid, $luid)";
		return $this->db->query($sql);
	}

	/**
	 * 取消关注
	 * @param $luid
	 * @return 对于更新语句
	 */
	public function rmFollowedAnchor($luid){
		$sql = "delete from userfollow where uid1=$this->uid and uid2=$luid";
		return $this->db->query($sql);
	}

	/**
	 * 关注人数
	 * @return int
	 */
	public function followCount(){
		$sql = "select count(*) as count from userfollow where uid1 = $this->uid";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();
		return (int)$row['count'];
	}

	/**
	 * 关注主播ID列表
	 * @return array
	 */
	public function followList(){
		$sql = "select uid2 from userfollow where uid1=$this->uid";
		$res = $this->db->query($sql);

		$followlist = array();
		while($row = $res->fetch_assoc){
			array_push($followlist, $row['uid2']);
		}

		return $followlist;
	}

	/**
	 * 录像是否收藏
	 * @param $videoid
	 * @return bool
	 */
	public function isCollect($videoid){
		$res = $this->db->field('videoid')->where("videoid=$videoid and uid=$this->uid")->select('videofollow');
		if((int)$res[0]['videoid']){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 收藏录像
	 * @param $videoid
	 * @return 对于更新语句
	 */
	public function collectVideo($videoid){
		$sql = "insert into videofollow (uid, videoid) value($this->uid, $videoid) on duplicate key update videoid=$videoid";
		return $this->db->query($sql);
	}

	/**
	 * 取消收藏录像
	 * @param $videoid
	 * @return 对于更新语句
	 */
	public function rmCollectedVideo($videoid){
		$sql = "delete from videofollow where uid=$this->uid and videoid=$videoid";
		return $this->db->query($sql);
	}

	/**
	 * 录像收藏数量
	 * @return int
	 */
	public function collectCount(){
		$sql = "select count(videoid) as count from videofollow where uid=$this->uid";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();
		return (int)$row['count'];
	}

	/**
	 * 收藏录像 videoid列表
	 * @return array
	 */
	public function collectList(){
		$sql = "select videoid from videofollow where uid=$this->uid";
		$res = $this->db->query($sql);

		$collectList = array();
		while($row = $res->fetch_assoc()){
			array_push($collectList, $row['videoid']);
		}
		return $collectList;
	}


	/**
	 * 是否被禁言
	 * @param $luid
	 * @return bool
	 */
	public function isSilenced($luid){
		$time = date('Y-m-d H:i:s', time()-3600);
		$sql = "select uid, ctime from silencedlist where uid =$this->uid and luid=$luid and ctime >= '$time'";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();
		if($row['uid']){
			return time() - strtotime($row['ctime']);
		}else{
			return 0;
		}
	}

    public function isRoomAdmin($luid){
        $sql = "select uid from roommanager where uid = $this->uid and luid = $luid";
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();
        if($row['uid']){
            return true;
        }else{
            return false;
        }
    }

	/**
	 * 获取该房间已经获取的宝箱ID列表
	 * @param $luid
	 * @return array
	 */
	public function getPickedTreasureBoxIdList($luid){
		$treasureBoxIdList = array();

		$sql = "select treasureid from pickTreasure where uid=$this->uid and luid=$luid";
		$res = $this->db->query($sql);
		while($row = $res->fetch_assoc()){
			array_push($treasureBoxIdList, $row['treasureid']);
		}
		return $treasureBoxIdList;
	}

	/**
	 * 获取该房间尚未领取的宝箱ID列表
	 * @param $luid
	 * @return array
	 */
	public function getUnPickTreasureBoxInfoList($luid){
		$sql = "select id, uid as suid, ctime from treasurebox where status=0 and luid=$luid";

		if($treasureid = $this->getPickedTreasureBoxIdList($luid)){
			$treasureid = '(' . implode(',', $treasureid) . ')';
			$sql = "select id, uid as suid, ctime from treasurebox where status=0 and luid=$luid and id not in $treasureid";
		}

		$res = $this->db->query($sql);

		$unPickTreasureBoxList = array();

		$isTest = false;
		$whiteList  = explode(',', WHITE_LIST);
		if(in_array($this->uid, $whiteList)){
			$isTest = true;
		}

		while($row = $res->fetch_assoc()){
			if(!$isTest && in_array($row['suid'], $whiteList))
				continue;

			array_push($unPickTreasureBoxList, $row);
//			if($isTest){
//				array_push($unPickTreasureBoxList, $row);
//			}else{
//				if(!in_array($row['suid'], $whiteList)){
//					array_push($unPickTreasureBoxList, $row);
//				}
//			}

		}

		return $unPickTreasureBoxList;
	}

	/**
	 * 赠送欢朋豆纪录
	 * @param $from 开始时间
	 * @param $to 	结束时间
	 * @param $page	业数
	 * @param $size 数量
	 * @return array
	 */
	public function sendBeanRecord($from, $to, $page, $size){
		$last = ($page - 1) * $size;
		$sql = "select * from giftrecord where uid = $this->uid and ctime BETWEEN '$from' and '$to'  order by ctime desc limit $last, $size";
		$res = $this->db->query($sql);

		$recordList = array();

		while($row = $res->fetch_assoc()){
			array_push($recordList, $row);
		}

		return $recordList;
	}

	/**
	 * 赠送礼物列表
	 * @param $from
	 * @param $to
	 * @param $page
	 * @param $size
	 * @return array
	 */
	public function sendGiftRecord($from, $to, $page, $size){
		$last = ($page - 1) * $size;
		$sql = "select * from giftrecordcoin where uid = $this->uid and ctime BETWEEN  '$from' and '$to' order by ctime desc limit $last, $size";
		$res = $this->db->query($sql);

		$recordList = array();

		while($row = $res->fetch_assoc()){
			array_push($recordList, $row);
		}

		return $recordList;
	}

	public function sendBeanRecordNumCount($from, $to){
		$sql = "select count(*) as count from giftrecord where uid = $this->uid and ctime between '$from' and '$to'";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['count'];
	}

	public function sendGiftRecordNumCount($from, $to){
		$sql = "select count(*) as count from giftrecordcoin where uid = $this->uid and ctime between '$from' and '$to'";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['count'];
	}
	/**
	 * 今日赠送欢朋币总数量
	 *
	 * @return int
	 */
	public function todaySendHpCoinCount(){
		$from = date('Y-m-d')." 00:00:00";
		$to = date("Y-m-d")." 23:59:59";

		$sql = "select sum(purchase) as count from billdetail where customerid = $this->uid and ctime BETWEEN '$from' and '$to'";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['count'];
	}


	/**
	 * 今日赠送欢朋豆总数量
	 *
	 * @return int
	 */
	public function todaySendHpBeanCount(){
		$from = date('Y-m-d')." 00:00:00";
		$to = date("Y-m-d")." 23:59:59";

		$sql = "select sum(giftnum) as count from giftrecord where uid = $this->uid and ctime BETWEEN '$from' and '$to'";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['count'];
	}

	/**
	 * 获取礼物信息
	 *
	 * @return array
	 */
	public function getGiftInfo(){
		$sql = "select * from gift";
		$res = $this->db->query($sql);

		$gift = array();
		while($row = $res->fetch_assoc()){
			$gift[$row['id']] = $row;
		}
		return $gift;
	}


    public function setNick($nick){
        $nick = $this->db->realEscapeString($nick);
        $sql = "update userstatic set nick = '$nick' where uid = $this->uid";
        $res = $this->db->query($sql);

        return $this->db->affectedRows;
    }


	/**
	 * nick get uid
	 *
	 * @param $nick
	 * @param null $db
	 * @return int
	 */
	public static function getUserIdByNick($nick, $db=null){
		if(!$db) $db = new DBHelperi_huanpeng();

		$nick = $db->realEscapeString($nick);
		$sql = "select uid from userstatic where nick='$nick'";
		$res = $db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['uid'];
	}

	/**
	 * 判断用户名是否存在
	 *
	 * @param $userName
	 * @param null $db
	 * @return int
	 */
	public static function isUserNameExist($userName, $db = null){
		if(!$db) $db = new DBHelperi_huanpeng();

		$userName = $db->realEscapeString($userName);
		$sql = "select * from userstatic where username = '$userName'";
		$res = $db->query($sql);

		$row = $res->fetch_assoc();

		return (int)$row['uid'];
	}

	/**
	 * 判断用户昵称是否存在
	 *
	 * @param $userNick
	 * @param null $db
	 * @return int
	 */
	public static function isUserNickExist($userNick, $db = null){
		if(!$db) $db = new DBHelperi_huanpeng();

		$uid = self::getUserIdByNick($userNick, $db);

		return $uid;
	}

	/**
	 * 用户是否设置昵称
	 *
	 * @return bool
	 */
	public function isSetNick(){
		$user = $this->getUsers();
		if($user['nick'])
			return true;

		return false;
	}

	/**
	 * 获取用户任务列表
	 *
	 * @return array
	 */
	public function getTaskList(){
		$sql = "select id, bean, `type`, title from taskinfo where status=".TASK_STAT_ONLINE;
		$res = $this->db->query($sql);

		$list = array();
		while($row = $res->fetch_assoc()){
			$list[$row['id']] = $row;
		}

		return $list;
	}

	/**
	 * 获取任务详情
	 *
	 * @param $taskid
	 * @return mixed
	 */
	public function getTaskInfo($taskid){
		$sql = "select * from taskinfo where id = $taskid and status=".TASK_STAT_ONLINE;
		$res = $this->db->query($sql);

		$row = $res->fetch_assoc();
		return $row;
	}

	/**
	 * 获取用户已经完成的任务ID列表
	 *
	 * @return array
	 */
	public function getFinishedTaskIdList(){
		$sql = "select taskid, status from task where uid = $this->uid";
		$res = $this->db->query($sql);

		$list = array();
		while($row = $res->fetch_assoc()){
			$list[$row['taskid']] = $row['status'];
		}

		return $list;
	}

	/**
	 * 我的任务列表
	 *
	 * @return array
	 */
	public function myTaskList(){
		$finishList = $this->getFinishedTaskIdList();
		$taskList = $this->getTaskList();

		$myTaskList = array();

		if($finishList){
			foreach($taskList as $key => $row){
				if($finishList[$key])
					$row['status'] = $finishList[$key];
				else
					$row['status'] = 0;

				array_push($myTaskList, $row);
			}
		}else{
			foreach($taskList as $key => $row){
				$row['status'] = 0;
				array_push($myTaskList, $row);
			}
		}

		return $myTaskList;
	}

	/**
	 * 完成任务领取欢豆
	 *
	 * @param $taskid
	 * @return int
	 */
	public function getBeanByTask($taskid){
		$recordid = $this->isTaskFinish($taskid);
		if(!$recordid){
			return -5018;
		}

		$taskinfo = $this->getTaskInfo($taskid);
		if(!$taskinfo){
			return -5019;
		}
		$bean = $taskinfo['bean'];
		$this->db->autocommit(false);
		$this->db->query('begin');
		$upRecord = $this->db->query("update task set getbean = $bean,status=".TASK_BEAN_RECEIVED." where id = $recordid");

		if(!$upRecord || !$this->upHpbean($bean)){
			$this->db->rollback();
			return -5017;
		}

		$this->db->commit();
		$this->db->autocommit(true);

		return $bean;
	}

	/**
	 * 任务是否完成
	 * @param $taskid
	 * @return int
	 */
	public function isTaskFinish($taskid){
		$sql = "select id from task where uid= $this->uid and taskid = $taskid and status=".TASK_FINISHED;
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();
		return (int)$row['id'];
	}

	public function setIphonePushNotify($deviceToken, $stat){
		if($stat == 0){
			return $this->setIphonePushNotifyClose();
		}

		$stat = (int)$stat;
		$sql = "insert into push_notify_set (deviceToken,uid,isopen) values('$deviceToken','$this->uid', $stat) on duplicate key update isopen = $stat";

		return $this->db->query($sql);
	}

	public function setIphonePushNotifyClose(){
		$sql = "update push_notify_set set isopen=0 where uid =".$this->uid;
		return $this->db->query($sql);
	}

	public function isLiveNotify($luid){
		$sql = "select uid from live_notice where uid=$this->uid and luid=$luid";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['uid'] ? true : false;
	}

	//设置用户推送总开关
	public function setNotifyStatus($stat){
		$data = array(
			'isnotice' => $stat
		);
		$res = $this->db->where('uid=' . $this->uid)->update('useractive', $data);
		return $res;
	}

	public function setLiveNotify($luid, $stat){
		$stat = (int)$stat;
		if ($stat == 0) {//删除
			$res = $this->db->where("uid=$this->uid  and luid=$luid")->delete('live_notice');
		}
		if ($stat == 1) {//添加
			$data = array(
				'uid' => $this->uid,
				'luid' => $luid
			);
			$res = $this->db->where("uid=$this->uid  and luid=$luid")->select('live_notice');
			if ($res) {
				$res = true;
			} else {
				$res = $this->db->insert('live_notice', $data);
			}
		}
		return $res;
	}

	public static function getUserEncpass($uid, $update=false,$db=null){
		static::setStaticDB($db);
		if($update){
			$encpass = md5(time() + $uid + random(6,1));
			$sql = "update userstatic set encpass='$encpass' where uid = $uid";
			static::$db2->query($sql);
			return $encpass;
		}else{
			$sql = "select encpass from userstatic where uid = $uid";
			$res = static::$db2->query($sql);
			$row = $res->fetch_assoc();
			return trim($row['encpass']);
		}
	}

	//three side register and bind

	public static function isNickExist($nick, $db=null){
		static::setStaticDB($db);
		$sql = "select uid from userstatic where nick='$nick'";
		$res = static::$db2->query($sql);
		$row = $res->fetch_assoc();
		return $row['uid'];
	}
	public static function setStaticDB($db){
		if(static::$db2) return true;
		if($db)
			static::$db2 = $db;
		else
			static::$db2 = new DBHelperi_huanpeng();
	}

	public static function createNick(){
		$nick = 'hp'.md5(random(10,1).microtime(true));
		if(static::isNickExist($nick)){
			return static::createNick();
		}else
			return substr($nick,0,10);

	}

	public static function createUser($userName,$openid,$nick,$pic,$type=1,$db=null){
		static::setStaticDB($db);
		$db = static::$db2;

		if(!$type){
			$changeData = array(
				'password' => md5password($openid + time() + $nick),
				'pic' => ''//GrabImage($pic)
			);
		}else{
			$changeData = array(
				'password' => md5password($openid),
				'pic' => $pic,
				'phone'=>$userName
			);
		}
		$rport = '';
		$rip = ip2long(fetch_real_ip($rport));
		$staticData = array(
			'username' => $userName,
			'nick' => $nick,
			'rip' => $rip ? $rip : "" ,
			'rport' => $rport ? : "",
			'rtime' => get_datetime(),
			'encpass' => md5(md5($openid . time())),
			'sex' => 1
		);
		$staticData = array_merge($staticData,$changeData);
		$staticRes = $db->insert('userstatic', $staticData);

		$lport = '';
		$lip = ip2long(fetch_real_ip($lport));
		if ($staticRes) {
			$activeDate = array(
				'uid' => $staticRes,
				'lip' => $lip ? $lip : "",
				'lport' => $lport ? $lport : "",
				'ltime' => get_datetime()
			);
			$activeRes = $db->insert('useractive', $activeDate);
			if ($activeRes) {
				$result = ['uid'=>$staticRes,'encpass'=>$staticData['encpass']];
			} else {
				$result = false;
			}
		} else {
			$result = false;
		}
		return $result;
	}

	public static function upUserToThreeSide($uid,$openid,$channel,$nick,$db=null){
		static::setStaticDB($db);
		$sql = "insert into three_side_user (uid,openid,channel,nick,status) VALUE ($uid,'$openid','$channel','$nick',1) on duplicate key update status=1, nick='$nick'";
		return static::$db2->query($sql);
	}

	public static function upUnionid($uid,$openid,$channel,$unionid,$db){
		static::setStaticDB($db);
		$sql = "update three_side_user set unionid='$unionid' where uid=$uid and openid='$openid' and channel='$channel'";
		return static::$db2->query($sql);
	}

	public static function isOpenidUsed($openid, $channel,$db=null){
		static::setStaticDB($db);

		$sql = "select uid from three_side_user where openid='$openid' and channel = '$channel' and status=1";
		$res = static::$db2->query($sql);
		$row = $res->fetch_assoc();
		return (int)$row['uid'];
	}

	public static function isUnionidUsed($unionid,$channel, $db=null){
		static::setStaticDB($db);

		$sql = "select uid from three_side_user where unionid='$unionid' and channel='$channel' and status=1";

		mylog("$sql",LOGFN_SENDGIFT_LOG);

		$res = static::$db2->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['uid'];
	}

	public function bindThreeSide($openid,$channel,$nick){
//		if($channel == 'qq')
//			return false;
		return static::upUserToThreeSide($this->uid,$openid,$channel,$nick,$this->db);
	}
}
?>
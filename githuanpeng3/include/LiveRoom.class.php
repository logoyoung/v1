<?php
/**
 * 聊天室类
 *
 * @author Lance Li <lance@6rooms.com>
 * @copyright 6.cn
 */
include_once 'PickBean.class.php';
require_once 'redis.class.php';
require_once 'User.class.php';
require_once 'Anchor.class.php';
require_once 'LRRank.class.php';
require_once 'WABPush.class.php';
class LiveRoom
{
	public $luid;

	private $db;
	private $redis;
	private $_servers = array();        // 本聊天室相关的聊天服务器列表
	private $debug = false;
	private $isTest = false;
	private $whiteList = array();

	public function __construct($luid, $db = null, $debug = false)
	{
		$this->luid = (int)$luid;
		if (!$this->luid) return false;

		if ($db)
			$this->db = $db;
		else
			$this->db = new DBHelperi_huanpeng();

		$this->redis = new redishelp();
		$this->debug = $debug;
		$this->debug = true;

		$this->whiteList = explode(',', WHITE_LIST);
		return true;
	}

	// 用户进入房间
	public function userEnter($uid, $useraddr, $serveraddr)
	{
		list($userip, $userport) = explode(':', $useraddr);
		list($serverip, $serverport) = explode(':', $serveraddr);
		$serverip = ip2long($serverip);
		$userip = ip2long($userip);

		// 取用户昵称
		$isAnchor = $uid == $this->luid ? 1 : 0;
		$user = $this->_getUser($uid, $isAnchor);
		if (!$user) return false;

		// 写在线表
		$sql = "insert into liveroom (luid,uid,userip,userport,serverip,serverport)"
			. " values"
			. " ({$this->luid}, $uid, $userip, $userport, $serverip, $serverport)"
			. " ON DUPLICATE KEY UPDATE"
			. " serverip=$serverip, serverport=$serverport";// userip=$userip, userport=$userport";//, serverip=$serverip, serverport=$serverport";
		$res = $this->db->query($sql);
		if (!$res) {
			$t = "QueryError @ LiveRoom::userEnter($uid, $serveraddr)[{$this->db->errno()}][{$this->db->errstr()}][$sql]";
			mylog($t);
			return false;
		}

		$this->liveUserCountAdd();
		$this->setLiveCountPeakValue();

		// 用户是游客，进入不通知
		if ($uid >= LIVEROOM_ANONYMOUS) return true;

		// 写浏览历史表
		if ($this->luid != PUSH_ROOM_ID) {
			$stime = date("Y-m-d H:i:s");
			$sql = "insert into history (uid,luid,stime)"
				. " values"
				. "($uid,{$this->luid},'$stime')"
				. " ON DUPLICATE KEY UPDATE"
				. " stime = '$stime'";
			$res = $this->db->query($sql);
			if (!$res) {
				$t = "QueryError @ LiveRoom::userEnter($uid, $serveraddr)[{$this->db->errno()}][{$this->db->errstr()}][$sql]";
				mylog($t);
				return false;
			}
		}

		return true;

//        $user['pic'] = $user['pic'] ? 'http://'.$GLOBALS['env-def'][$GLOBALS['env']]['domain-img']."/".$user['pic'] : DEFAULT_PIC;
//        // 输出房间消息
//        $msg = array(
//            't' => 501,
//            'tm' => time(),
//            'nn' => $user['nick'],
//			'uid' => $uid,
//			'level' => (int)$user['level'],
//            'pic' => $user['pic'],
//			'group' => $this->getUsergroup($uid)
//        );
//        return $this->sendRoomMsg(json_encode(toString($msg)));
	}

	public function succEnter($uid, $arr)
	{
		$backmsg = array(
			't' => 1104,
			'mid' => $arr['mid'],
			'e' => 0
		);

		// 用户是游客，进入不通知
		if ($uid >= LIVEROOM_ANONYMOUS) {
			mylog("succEnter: is anonynous", LOGFN_SEND_MSG_ERR);

			$msg = array(
				't' => 501,
				'tm' => time(),
				'uid' => $uid,
				'viewCount' => $this->getLiveUserCountFictitious(),
				'showHead' => 0,
				'showWel'=>0,
				'isGust' => 1,
				'level' => 1,
				'pic' => '',
				'group' => 1,
			);
			$r = $this->sendRoomMsg(json_encode(toString($msg)));
			$this->sendUserMsg($uid, json_encode(toString($backmsg)));
			return true;
		}

		mylog("succEnter: current uid is $uid", LOGFN_SEND_MSG_ERR);
		$isAnchor = $uid == $this->luid ? 1 : 0;
		$user = $this->_getUser($uid, $isAnchor);
		mylog("user detail info is " . json_encode($user), LOGFN_SEND_MSG_ERR);
		if (!$user) return false;

		$user['pic'] = $user['pic'] ? DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . "/" . $user['pic'] : DEFAULT_PIC;
		$msg = array(
			't' => 501,
			'tm' => time(),
			'nn' => $user['nick'],
			'uid' => $uid,
			'level' => (int)$user['level'],
			'pic' => $user['pic'],
			'group' => $this->getUsergroup($uid),
			'viewCount' => $this->getLiveUserCountFictitious(),
			'showHead' => 1,
			'showWel'=>$this->isShowWel((int)$user['level']),
			'isGust' => 0
		);

		mylog("succEnter: sendMsg content is " . json_encode($msg), LOGFN_SEND_MSG_ERR);
		$r = $this->sendRoomMsg(json_encode(toString($msg)));


		return $this->sendUserMsg($uid, json_encode(toString($backmsg)));
	}

	// 用户离开房间
	public function userExit($uid, $addr)
	{
		list($ip, $port) = explode(':', $addr);
		$ip = ip2long( $ip );
		$sql = "delete from liveroom where luid={$this->luid} and uid=$uid and userport=$port and userip=$ip";
		$res = $this->db->query($sql);
		if (!$res) {
			$t = "QueryError @ LiveRoom::userExit($uid)[{$this->db->errno()}][{$this->db->errstr()}][$sql]";
			mylog($t);
			return false;
		}
		$this->liveUserCountSub();

		if ($uid >= LIVEROOM_ANONYMOUS) {
			$msg = array(
				't' => 506,
				'tm' => time(),
				'uid' => $uid,
				'viewCount' => $this->getLiveUserCountFictitious(),
				'showHead' => 0,
				'showWel'=>0,
				'isGust' => 1
			);
			$this->sendRoomMsg(json_encode(toString($msg)));
			return true;
		}

		$msg = array(
			't' => 506,
			'tm' => time(),
			'uid' => $uid,
//			'group' => 1
			'viewCount' => $this->getLiveUserCountFictitious(),
			'showHead' => 1,
			'showWel'=>1,
			'isUser'=>0,
		);

		$pick = new PickBean($uid, $this->db);
		$pick->exitRoom($this->luid);

		$this->sendRoomMsg(json_encode(toString($msg)));
		return true;
	}


	public function isShowWel($lvl){
		if($lvl >= 1){
			return 1;
		}else{
			return 0;
		}
	}

	// 更新用户在线状态（心跳）
	public function userHB($uid)
	{
		$sql = "update liveroom set tm=now() where luid={$this->luid} and uid=$uid";
		$res = $this->db->query($sql);
		if (!$res) {
			$t = "QueryError @ LiveRoom::userHB($uid)[{$this->db->errno()}][{$this->db->errstr()}][$sql]";
			mylog($t);
			return false;
		}
		return true;
	}

	// 发送直播开始消息
	public function start($liveid)
	{
		$this->liveUserCountInit();

		if( $this->isCanAddToPushMsgList( $liveid ) )
		{
			$this->addPushMsgList( $liveid );
		}
//		$wapPush = new WABPush($this->db);
//		$wapPush->liveStart($this->luid);
		$msg = array(
			't' => 601,
			'lid' => $liveid,
		);
		liveStatusMsgToAdmin($liveid, 1);
		return $this->sendRoomMsg(json_encode(toString($msg)));
	}

	// 发送直播开始消息
	public function stop($liveid)
	{
//        $liveid = $this->_getLive()['liveid'];
		$this->delSendGiftTimer($liveid);

		$msg = array(
			't' => 602,
		);
		liveStatusMsgToAdmin($liveid, 2);
		return $this->sendRoomMsg(json_encode(toString($msg)));
	}

	// 用户发言
	public function userMsg($uid, $arr, $useraddr, $serveraddr)
	{
		if ($uid >= LIVEROOM_ANONYMOUS) roomerror(-3008);

		$errno = 0;

		if ((int)$this->redis->get('silenced:' . $this->luid . ':' . $uid)) {
			$errno = -3009;
		}

		if (mb_strlen($arr['msg'], 'UTF8') > 50) {
			$errno = -3513;
		}
		// 取发言用户
		if (!$errno) {
			$isAnchor = $uid == $this->luid ? 1 : 0;
			$user = $this->_getUser($uid, $isAnchor);
			if (!$user) $errno = -3503;
			$nick = $user['nick'];
		}

		// 写消息表
		if (!$errno) {
			$msgall = json_encode(array($arr, $useraddr, $serveraddr));
			$msgall = $this->db->realEscapeString($msgall);
			$sql = "insert into livemsg (luid,uid,msg)"
				. " values"
				. " ({$this->luid}, $uid, '$msgall')";
			$res = $this->db->query($sql);
			if (!$res) {
				$t = "QueryError @ LiveRoom::userMsg($uid, $useraddr, $serveraddr)[{$this->db->errno()}][{$this->db->errstr()}][$sql]";
				mylog($t);
				$errno = -3501;
			}
		}

		// 消息过滤   TODO
		$str_relace = array("\r", "\n");
		$arr['msg'] = str_replace($str_relace, '', $arr['msg']);
		if (!$arr['msg']) $errno = -3512;

		// 消息发送
		if (!$errno) {
			$msgid = $this->db->insertID;
			$msg = array(
				't' => 502,
				'tm' => time(),
				'cuid' => $uid,
				'cunn' => $nick,
				'msg' => $arr['msg'],
				'group' => $this->getUsergroup($uid),
				'msgid' => $msgid,
				'level' => (int)$user['level'],
				'phone' => (int)$arr['way']
			);
			$r = $this->sendRoomMsg(json_encode(toString($msg)));
			if (!$r) $errno = -3502;
		}

		// 发言用户回调
		$msg = array(
			't' => 1100,
			'mid' => $arr['mid'],
			'e' => $errno
		);
		$msg = array_merge($msg, $this->_socketErrorNotice($errno));
		return $this->sendUserMsg($uid, json_encode(toString($msg)));
	}

	// 用户点赞
	public function up($uid, $arr)
	{
		if ($uid >= LIVEROOM_ANONYMOUS) roomerror(-3008);

		$errno = 0;

		// 取发言用户
		if (!$errno) {
			$user = $this->_getUser($uid);
			if (!$user) $errno = -3503;
			$nick = $user['nick'];
		}

		// 取直播
		if (!$errno) {
			$live = $this->_getLive();
			if (!$live) $errno = -3504;
			$liveid = $live['liveid'];
		}

		// 更新直播赞
		if (!$errno) {
			$sql = "update live set upcount=upcount+1 where liveid=$liveid";
			$res = $this->db->query($sql);
			if (!$res) {
				$t = "QueryError @ LiveRoom::up()[{$this->db->errno()}][{$this->db->errstr()}][$sql]";
				mylog($t);
				$errno = -3505;
			}
		}

		// 消息发送
		if (!$errno) {
			$msg = array(
				't' => 503,
				'tm' => time(),
				'ouid' => $uid,
				'ounn' => $nick,
				'uc' => $live['upcount'] + 1,
			);
			$r = $this->sendRoomMsg(json_encode(toString($msg)));
			if (!$r) $errno = -3506;
		}

		// 点赞用户回调
		$msg = array(
			't' => 1101,
			'mid' => $arr['mid'],
			'e' => $errno,
		);
		return $this->sendUserMsg($uid, json_encode(toString($msg)));
	}

	public function sendBean($uid, $arr)
	{
		if ($uid >= LIVEROOM_ANONYMOUS) roomerror(3008);

		$iosList = explode(',', IOS_TEST_USER_LIST);
		if (in_array($uid, $iosList)) {
			$this->testSendGift($uid, $arr);
			return true;
		}

		$errno = 0;

		$userHelp = new UserHelp($uid, $this->db);
		$anchorHelp = new AnchorHelp($this->luid, $this->db);

		if (!$arr['enc'] || !(int)$arr['gid'] || !(int)$arr['num']) {//|| !(int)$arr['liveid'] 主播没有开播过 是可以送礼的
			$errno = -3510;
		} else {
			$liveid = $anchorHelp->getLastLiveid();
			$gift = $this->getRoomGiftInfo($arr['gid'], 1);
			if ($liveid != (int)$arr['liveid'] || !$gift) {
				$errno = -3511;
			} else {
				//登录状态
				if ($code = $userHelp->checkStateError($arr['enc'])) $errno = $code;
			}

			//获取用户信息
			if (!$errno) {
				if (!$userInfo = $this->_getUser($uid)) $errno = -3504;
				if (!$luserInfo = $this->_getUser($this->luid)) $errno = -3507;
			}
		}

		//验证用户余额
		if (!$errno) {
			$property = $userHelp->getProperty();
			$money = $gift['money'];
			$num = $arr['num'];

			$amount = $num;
			$myBalance = $property['hpbean'];

			if ($myBalance < $amount) {
				$errno = -3515;
			} else {
				$phone = $userHelp->getPhoneCertifyInfo();
				if (!$phone['status']) {
					//$errno = -5;
				}
			}
		}


		if (in_array($uid, $this->whiteList)) {
			if (in_array($this->luid, $this->whiteList)) {
				$this->isTest = true;
			} else {
				$errno = -3514;
			}
		}

		//执行送礼逻辑
		if (!$errno) {
			$this->db->autocommit(false);
			$this->db->query('begin');
			$giftRecord = $this->db->query("insert into giftrecord(luid, liveid, uid, giftid, giftnum) value($this->luid, $liveid, $uid, {$arr['gid']}, $num)");
			$upUserBalance = $userHelp->costHpBean($amount, $myBalance);
			$upAnchorIncomeBean = $anchorHelp->upHpBean($amount);

			$exp = $num / $gift['money'] * $gift['exp'];
			$upUserLevel = $userHelp->updateLevel($exp);
			$upAnchorLevel = $anchorHelp->updateLevel($exp);


			if (!$giftRecord || !$upUserBalance || !$upAnchorIncomeBean || !$upUserLevel || !$upAnchorLevel) {
				$rollback = $this->db->rollback();
				$errno = -3510;//系统错误
			} else {
				//notice there only use redis storage, in the next time should use sql to save the data and judge if the user had finished the task 24;
				if (!(int)$this->redis->get("firstSenbBean:$uid")) {
					if (synchroTask($uid, 24, 0, 100, $this->db)) {
						$this->redis->set("firstSendBean:$uid", 1);
					}
				}
				$this->db->commit();
				$this->db->autocommit(true);
			}
		}
		//消息发送
		if (!$errno) {
			$isAnchor = $uid == $this->luid ? 1 : 0;
			$userInfo = $this->_getUser($uid, $isAnchor);
			$content = array(
				't' => 504,
				'tm' => time(),
				'ouid' => $uid,
				'ounn' => $userInfo['nick'],
				'gid' => $arr['gid'],
				'gnum' => $num,
				'gname' => $gift['giftname'],
				'level' => $userInfo['level'],
				'phone' => $arr['way'],
				'group' => $this->getUsergroup($uid),
			);
			$r = $this->sendRoomMsg(json_encode(toString($content)));
			if (!$r) $errno = -3509;
		}

		$property = $userHelp->getProperty();
		//消息回调
		$msg = array(
			't' => 1102,
			'mid' => $arr['mid'],
			'e' => $errno,
			'cost' => (int)$amount,
			'coin' => $property['hpcoin'],
			'bean' => $property['hpbean'],
			'costNUm' => $num,
			'constamount' => $amount
		);
		$msg = array_merge($msg, $this->_socketErrorNotice($errno));
		$this->sendUserMsg($uid, json_encode(toString($msg)));
	}

	public function sendGift($uid, $arr)
	{
		if ($uid >= LIVEROOM_ANONYMOUS) roomerror(-3008);

		$errno = 0;

		$iosList = explode(',', IOS_TEST_USER_LIST);
		if (in_array($uid, $iosList)) {
			$this->testSendGift($uid, $arr);
			return true;
		}

		$userHelp = new UserHelp($uid, $this->db);
		$anchorHelp = new AnchorHelp($this->luid, $this->db);

		$liveid = $anchorHelp->getLastLiveid();

		$gift = $this->getRoomGiftInfo((int)$arr['gid'], 2);
		if (!$arr['enc'] || !(int)$arr['liveid'] || !(int)$arr['gid'] || $liveid != $arr['liveid'] || !$gift) {
			$errno = -3511;
		}


		//验证用户登录状态
		if (!$errno) {
			if ($code = $userHelp->checkStateError($arr['enc'])) {
				$errno = $code;
			}
		}
		//获取用户信息
		if (!$errno) {
			if (!$userInfo = $this->_getUser($uid)) $errno = -3504;
			if (!$luserInfo = $this->_getUser($this->luid)) $errno = -3507;
		}

		if (!$errno) {
			$property = $userHelp->getProperty();
			$money = $gift['money'];
			$num = 1;
			$amount = $money * $num;
			$myBalance = $property['hpcoin'];

			if ($myBalance < $amount) {
				$errno = -3514;
			} else {
				//验证手机
				$phone = $userHelp->getPhoneCertifyInfo();
				if (!$phone['status']) {
//					$errno = -5;
				}
			}


			if (in_array($uid, $this->whiteList)) {
				if (in_array($this->luid, $this->whiteList)) {
					$this->isTest = true;
				} else {
					$errno = -3514;
				}
			}


			if (!$errno) {
				$this->db->autocommit(false);
				$this->db->query('begin');
				$sendGiftID = time() . random(6, 1);

				$giftRecord = $this->db->query("insert into giftrecordcoin(id,luid,liveid,uid,giftid,giftnum) values('$sendGiftID',$this->luid,{$liveid},$uid,{$arr['gid']},$num)");
				$updateBalance = $userHelp->costHpCoin($amount, $myBalance);
				$upAnchorCoin = $anchorHelp->upHpCoin($amount);
				if (!$giftRecord || !$updateBalance || !$upAnchorCoin) {
					mylog('step mybalance result is ' . $myBalance, LOGFN_SENDGIFT_LOG);
					mylog("step giftrecord result" . (int)$giftRecord, LOGFN_SENDGIFT_LOG);
					mylog("step updateBalance result" . (int)$updateBalance, LOGFN_SENDGIFT_LOG);
					mylog("step upAnchorCoin result" . (int)$upAnchorCoin, LOGFN_SENDGIFT_LOG);
					mylog("step end ========\n", LOGFN_SENDGIFT_LOG);
					$this->db->rollback();
					$errno = -3510;//服务器繁忙;
				} else {
					$this->db->commit();
					$this->db->autocommit(true);
				}
			}

			if (!$errno) {
				$income = $amount;
				//
				$transRecord = $this->db->query("insert into billdetail(customerid, purchase, beneficiaryid,income,type,info) values($uid,$amount,$this->luid,$income,0,'$sendGiftID')");
				$insertTreasure = true;
				if ($arr['gid'] == 35) {
					$insertTreasure = $this->createTreasure($uid);
				}
				$upUserLevel = $userHelp->updateLevel($num * $gift['exp']);

				$rRank = new RankUpdate($this->luid, $this->redis, $this->db, $this);
				$upRank = $rRank->intoRankList($uid, $amount);

				$upAnchorLevel = $anchorHelp->updateLevel($num * $gift['exp']);
				if (!$transRecord || !$insertTreasure || !$upUserLevel || !$upRank || !$upAnchorLevel) {
					$errno = -3510;
				}
			}
//			if(!$errno){
//				$this->db->autocommit(false);
//				$this->db->query('begin');
//				$sendGiftID = time().random(6,1);
//				$income = $amount;
//
//				$giftRecord = $this->db->query("insert into giftrecordcoin(id,luid,liveid,uid,giftid,giftnum) values('$sendGiftID',$this->luid,{$liveid},$uid,{$arr['gid']},$num)");
//				$transRecord = $this->db->query("insert into billdetail(customerid, purchase, beneficiaryid,income,type,info) values($uid,$amount,$this->luid,$income,0,'$sendGiftID')");
//				$updateBalance = $userHelp->costHpCoin($amount, $myBalance);
//
//				$insertTreasure = true;
//				if($arr['gid'] == 35){
//					$insertTreasure = $this->createTreasure($uid);
//				}
//
//				$upUserLevel = $userHelp->updateLevel($num * $gift['exp']);
//
//				$rRank = new RankUpdate($this->luid, $this->redis, $this->db, $this);
//				$upRank = $rRank->intoRankList($uid, $amount);
//
//				$upAnchorLevel = $anchorHelp->updateLevel($num * $gift['exp']);
//				$upAnchorCoin = $anchorHelp->upHpCoin($amount);
//
//				if(!$giftRecord || !$transRecord || !$updateBalance || !$insertTreasure || !$upUserLevel || !$upRank || !$upAnchorLevel ||!$upAnchorCoin){
//				    mylog("step giftrecord result".(int)$giftRecord,LOGFN_SENDGIFT_LOG);
//                    mylog("step transRecord result".(int)$transRecord,LOGFN_SENDGIFT_LOG);
//                    mylog("step updateBalance result".(int)$updateBalance,LOGFN_SENDGIFT_LOG);
//                    mylog("step insertTreasure result".(int)$insertTreasure,LOGFN_SENDGIFT_LOG);
//                    mylog("step upUserLevel result".(int)$upUserLevel,LOGFN_SENDGIFT_LOG);
//                    mylog("step upRank result".(int)$upRank,LOGFN_SENDGIFT_LOG);
//                    mylog("step upAnchorLevel result".(int)$upAnchorLevel,LOGFN_SENDGIFT_LOG);
//                    mylog("step upAnchorCoin result".(int)$upAnchorCoin,LOGFN_SENDGIFT_LOG);
//					$this->db->rollback();
//					$errno = -3510;//服务器繁忙;
//				}else{
//					$this->db->commit();
//					$this->db->autocommit(true);
//
//                    $this->setSendGiftTimer($liveid, $uid, $arr['gid']);
//					if($arr['gid'] == 35){
//						//发送飞机
//						$content = array(
//							't' => 535,
//							'tm' => time(),
//							'uid' => $uid,
//							'nick' => $userInfo['nick'],
//							'luid' => $this->luid,
//							'lunick' => $luserInfo['nick'],
//							'gname' => $gift['giftname'],
//							'treasureID' => $insertTreasure,
//                            'timeOut' => TREASURE_TIME_OUT
//						);
//						$r = $this->sendAllMsg(json_encode(toString($content)));
//						if(!$r) $errno = -3508;
//
//					}
//				}
//			}
		}

		if (!$errno) {
			$isAnchor = $uid == $this->luid ? 1 : 0;
			$userInfo = $this->_getUser($uid, $isAnchor);
			$this->setSendGiftTimer($liveid, $uid, $arr['gid']);
			if ($arr['gid'] == 35) {
				$content = array(
					't' => 535,
					'tm' => time(),
					'uid' => $uid,
					'nick' => $userInfo['nick'],
					'luid' => $this->luid,
					'lunick' => $luserInfo['nick'],
					'gname' => $gift['giftname'],
					'treasureID' => $insertTreasure,
					'timeOut' => TREASURE_TIME_OUT
				);
				if ($this->isTest)
					$this->sendAllMsgForTest(json_encode(toString($content)));
				else
					$this->sendAllMsg(json_encode(toString($content)));
			}
			$content = array(
				't' => 504,
				'tm' => time(),
				'ouid' => $uid,
				'ounn' => $userInfo['nick'],
				'gid' => $arr['gid'],
				'gnum' => $num,
				'gname' => $gift['giftname'],
				'level' => $userInfo['level'],
				'timer' => $this->getSendGiftTimer($liveid, $uid, $arr['gid']),
				'phone' => (int)$arr['way'],
				'group' => $this->getUsergroup($uid),
			);
			$r = $this->sendRoomMsg(json_encode(toString($content)));
			if (!$r) $errno = -3509;
		}
		$property = $userHelp->getProperty();
		//送礼回调
		$msg = array(
			't' => 1103,
			'mid' => $arr['mid'],
			'e' => $errno,
			'cost' => (int)$amount,
			'coin' => $property['hpcoin'],
			'bean' => $property['hpbean']
		);
		$msg = array_merge($msg, $this->_socketErrorNotice($errno));
		return $this->sendUserMsg($uid, json_encode(toString($msg)));
	}

	public function testSendGift($uid, $arr)
	{
		if ($uid >= LIVEROOM_ANONYMOUS) roomerror(3008);


		if ($arr['gid'] == 31) {
			$isBean = true;
		} else {
			$isBean = false;
		}

		$userHelp = new UserHelp($uid, $this->db);
		$anchorHelp = new AnchorHelp($this->luid, $this->db);

		$errno = 0;


		if (!$arr['enc'] || !(int)$arr['gid'] || ($isBean && !(int)$arr['num'])) {
			$errno = -3520;
//		if(!$arr['enc'] || !(int)$arr['gid'] || !(int)$arr['num']){

		} else {

			$liveid = $anchorHelp->getLastLiveid();

			if ($isBean) {
				$gift = $this->getRoomGiftInfo($arr['gid'], 1);
			} else {
				$gift = $this->getRoomGiftInfo($arr['gid'], 2);
			}


			if ($liveid != (int)$arr['liveid'] || !$gift) {
				$errno = -3511;
			} else {
				//登录状态
				if ($code = $userHelp->checkStateError($arr['enc'])) $errno = $code;
			}

			//获取用户信息
			if (!$errno) {
				if (!$userInfo = $this->_getUser($uid)) $errno = -3504;
				if (!$luserInfo = $this->_getUser($this->luid)) $errno = -3507;
			}
		}

		if (!$errno) {
			$property = $userHelp->getProperty();
			$num = $arr['num'];

			if ($isBean) {
				$amount = $num;
				$myBalance = $property['hpbean'];
			} else {
				$num = 1;
				$amount = $gift['money'] * $num;
				$myBalance = $property['hpcoin'];
			}


			if ($myBalance < $amount) {
				$errno = $isBean ? -3515 : -3514;
			} else {
				$phone = $userHelp->getPhoneCertifyInfo();
				if (!$phone['status']) {
					//$errno = -5;
				}
			}
		}

		if (!$errno) {
			if ($isBean) {
				$ret = $userHelp->costHpBean($amount, $myBalance);
			} else {
				$ret = $userHelp->costHpCoin($amount, $myBalance);
			}


			mylog('send result is ' . $ret, LOGFN_SENDGIFT_LOG);

			if (!$ret)
				$errno = -3510;
		}

		if (!$errno) {
			$this->setSendGiftTimer($liveid, $uid, $arr['gid']);
			$isAnchor = $uid == $this->luid ? 1 : 0;
			$userInfo = $this->_getUser($uid, $isAnchor);

			$content = array(
				't' => 504,
				'tm' => time(),
				'ouid' => $uid,
				'ounn' => $userInfo['nick'],
				'gid' => $arr['gid'],
				'gnum' => $num,
				'gname' => $gift['giftname'],
				'level' => $userInfo['level'],
				'timer' => $this->getSendGiftTimer($liveid, $uid, $arr['gid']),
				'phone' => (int)$arr['way'],
				'group' => $this->getUsergroup($uid),
			);

			$r = $this->sendUserMsg($uid, json_encode(toString($content)));
			if (!$r) $errno = -3509;
		}

		$property = $userHelp->getProperty();

		$msg = array(
			't' => $isBean ? 1102 : 1103,
			'mid' => $arr['mid'],
			'e' => $errno,
			'cost' => (int)$amount,
			'coin' => $property['hpcoin'],
			'bean' => $property['hpbean'],
			'costNUm' => $num,
			'constamount' => $amount
		);
		$msg = array_merge($msg, $this->_socketErrorNotice($errno));
		$this->sendUserMsg($uid, json_encode(toString($msg)));
	}

	private function setSendGiftTimer($liveid, $uid, $giftid)
	{
		$key = "liveGiftTimer:$liveid";
		$value = "sid:$uid:$giftid";
		$score = $this->getSendGiftTimer($liveid, $uid, $giftid) + 1;

		$this->redis->zadd($key, $score, $value);
	}

	private function getSendGiftTimer($liveid, $uid, $giftid)
	{
		$key = "liveGiftTimer:$liveid";
		$value = "sid:$uid:$giftid";

		return (int)$this->redis->zScore($key, $value);
	}

	private function delSendGiftTimer($liveid)
	{
		$key = "liveGiftTimer:$liveid";

		$this->redis->zRemRangeByRank($key, 0, -1);
	}

	//创建房间宝箱
	public function createTreasure($uid)
	{
		$sql = "insert into treasurebox(uid, luid) value($uid, $this->luid)";
		if (!$this->db->query($sql)) {
			return 0;
		} else {
			return $this->db->insertID;
		}
	}

	// 获取在线用户
	public function getRoomUsers()
	{
		$uids = array();

		$sql = "SELECT uid FROM liveroom WHERE luid={$this->luid}";
		$res = $this->db->query($sql);
		if (!$res) {
			$t = "QueryError @ getRoomUsers()[{$this->db->errno()}][{$this->db->errstr()}][$sql]";
			mylog($t);
			return false;
		}
		while ($row = $res->fetch_row())
			$uids[] = $row[0];

		return $uids;
	}

	// 获取在线用户数量
	public function getRoomUserCount()
	{
		$sql = "SELECT count(DISTINCT(uid)) FROM liveroom WHERE luid={$this->luid}";
		$res = $this->db->query($sql);
		if (!$res) {
			$t = "QueryError @ getRoomUserCount()[{$this->db->errno()}][{$this->db->errstr()}][$sql]";
			mylog($t);
			return false;
		}
		$row = $res->fetch_row();
		return $row[0];
	}

	/**
	 * @param                    $luids
	 * @param DBHelperi_huanpeng $db
	 *
	 * @return array array(array('luid'=>123,total=>456),....);
	 */
	public static function getRoomUserByLuid($luids, $db)
	{
		if( empty($luids) )
			false;

		$luids = "(".implode(',',$luids).")";
		$sql = "select luid, count(DISTINCT(uid)) as total from liveroom where luid in $luids and uid < ". LIVEROOM_ANONYMOUS . " group by luid";
		$res = $db->query( $sql );

		$result = array();
		while( $row = $res->fetch_assoc() )
		{
			array_push($result, $row);
		}

		return $result;
	}

	// 获取最近发言
	public function getRecentMsg($num = 20)
	{
		$sql = "SELECT * FROM livemsg WHERE luid={$this->luid} order by msgid desc limit $num";
		$res = $this->db->query($sql);
		if (!$res) {
			$t = "QueryError @ getRecentMsg($num)[{$this->db->errno()}][{$this->db->errstr()}][$sql]";
			mylog($t);
			return false;
		}

		$ret = array();
		while ($row = $res->fetch_assoc()) {
			$t = json_decode($row['msg'], true);
			$ret[] = array(
				'uid' => $row['uid'],
				'tm' => $row['tm'],
				'msg' => $t[0]['msg']
			);
		}
		return $ret;
	}

	// 向房间中发送消息
	public function sendRoomMsg($content)
	{
		$content = $this->msgEncode($content);

		if (!$this->_servers) $this->_getServers();
		$urls = array();
		foreach ($this->_servers as $server)
			$urls[] = "http://$server/send?roomid={$this->luid}&encrypted=yes&content=$content";

		// 逐个请求，当聊天服务器增多时应改为并行请求
		foreach ($urls as $url) {
			if ($this->debug) mylog($url);
			$sendRet = file_get_contents($url);

			if ($this->debug) {
				$log = "send roomid={$this->luid} msg=" . $this->msgDecode($content) . "result= $sendRet";
				mylog($log, LOGFN_SEND_MSG_ERR);
			}
		}

		return true;
	}

	// 向房间中指定用户发送消息
	public function sendUserMsg($uid, $content)
	{
		$content = $this->msgEncode($content);

		if (!$this->_servers) $this->_getServers();
		$urls = array();
		foreach ($this->_servers as $server)
			$urls[] = "http://$server/sendonce?roomid={$this->luid}&userid=$uid&encrypted=yes&content=$content";

		// 逐个请求，当聊天服务器增多时应改为并行请求
		foreach ($urls as $url) {
			if ($this->debug) mylog($url);
			file_get_contents($url);
		}

		return true;
	}

	public function sendAllMsg($content)
	{
		$content = $this->msgEncode($content);
		if (!$this->_servers) {
			$this->_getServers();
		}
		$urls = array();
		foreach ($this->_servers as $server) {
			$urls[] = "http://$server/sendtoall?roomid={$this->luid}&encrypted=yes&content=$content";
		}
		// 逐个请求，当聊天服务器增多时应改为并行请求
		foreach ($urls as $url) {
			if ($this->debug) {
				mylog($url);
			}
			file_get_contents($url);
		}
		return true;
	}


	public function sendAllMsgForTest($content)
	{
		$whiteList = explode(',', WHITE_LIST);
		foreach ($whiteList as $value) {
			$this->sendUserMsg($value, $content);
		}
	}


	public function isRoomAdmin($uid)
	{
		$sql = "SELECT uid FROM roommanager where uid =$uid and luid = {$this->luid}";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		$result = (int)$row ? true : false;

		return $result;
	}

	public function getUsergroup($uid)
	{
		if ($uid == $this->luid)
			return 5;
		if ($this->isRoomAdmin($uid))
			return 4;
		else
			return 1;
	}

	public function liveUserCountInit()
	{
//		$count = $this->getRoomUserCount();
//		$this->setLiveUserCount($count);
//		$this->redis->set('liveCount' . $this->luid . ':peakValue', '0');

		$this->setLiveCountPeakValue( 0 );

//		$count = getFictitiousViewCount( $count, 1 );
//		$this->setLiveUserCountFictitious( $count );

	}

	public function liveUserCountAdd()
	{
		$this->setLiveUserCount($this->getLiveUserCount() + 1);
		$this->liveUserCountAddFictitious();
	}

	public function liveUserCountSub()
	{
		$this->setLiveUserCount($this->getLiveUserCount() - 1);
		$this->liveUserCountSubFictitious();
	}

	public function setLiveUserCount($count)
	{
		$count = (int)$count;
		$this->redis->set('liveCount:' . $this->luid, "$count");
		$this->setLiveUserCountFictitious( $count );
	}


	public function getLiveUserCount()
	{
		$viewCount = (int)$this->redis->get('liveCount:' . $this->luid);
		if($viewCount <= 0)
		{
			$viewCount = $this->getRoomUserCount();
			$this->setLiveUserCount( $viewCount );
			$viewCount = (int)$this->redis->get('liveCount:' . $this->luid);
		}
		return $viewCount;
	}

	public function setLiveCountPeakValue( $count = null )
	{
		$this->setLiveCountPeakValueFictitious( $count );

		if( $count !== null )
		{
			$this->redis->set('liveCount' . $this->luid . ':peakValue', $count );

			return true;
		}

		$count = (int)$this->redis->get('liveCount:' . $this->luid . ':peakValue');
		$tmpCount = $this->getLiveUserCount();

		if ($count < $tmpCount) {
			$this->redis->set('liveCount' . $this->luid . ':peakValue', $tmpCount);
		}

		return true;
	}

	public function getLiveCountPeakValue()
	{
		return (int)$this->redis->get('liveCount' . $this->luid . ':peakValue');
	}

	public function setLiveUserCountFictitious($count)
	{
		$count = (int)$count;
		$this->redis->set('liveCountFictitious' . $this->luid, "$count");
	}

	public function getLiveUserCountFictitious()
	{
		$viewCount = (int)$this->redis->get( 'liveCountFictitious'. $this->luid );
		if($viewCount < 0)
		{
			$viewCount = $this->getRoomUserCount();
			$this->setLiveUserCountFictitious( $viewCount );
		}

		return $viewCount;
	}

	public function liveUserCountAddFictitious( $add = 1, $conf = null )
	{
//		$viewCount = $this->getLiveUserCountFictitious() + 1;
//		$viewCount = getFictitiousViewCount( $this->getLiveUserCountFictitious(), $add , $conf);
		$viewCount = getFictitiousViewCount( $this->getLiveUserCount(), $add, $conf);
		mylog( "add user fictitious view count" . $this->luid.":" . $viewCount );
		$this->setLiveUserCountFictitious( $viewCount );
	}

	public function liveUserCountSubFictitious( $add = -1, $conf = null)
	{
//		$viewCount = $this->getLiveUserCountFictitious() - 1;
//		$viewCount = getFictitiousViewCount( $this->getLiveUserCountFictitious(), $add, $conf );
		$viewCount = getFictitiousViewCount( $this->getLiveUserCount(), $add, $conf);
		mylog( "add user fictitious view count__sub:" . $this->luid.":" . $viewCount );
		$this->setLiveUserCountFictitious( $viewCount );
	}

	public function setLiveCountPeakValueFictitious( $count = null )
	{
		if( $count !== null )
		{
			$this->redis->set( 'liveCount' . $this->luid . ':peakValueFictitious', $count );
			return true;
		}

		$count = $this->getLivecountPeakValueFictitious();
		$tmpCount = $this->getLiveUserCountFictitious();
		if($count < $tmpCount)
		{
			$this->redis->set( 'liveCount' . $this->luid . ':peakValueFictitious', $tmpCount );
		}

		return true;
	}

	public function getLiveUserCountByLuid( $luid )
	{
		$viewCount = (int)$this->redis->get( 'liveCountFictitious'. $luid );
		$db = $this->db;
		$getRoomUserCount = function( $luid ) use ( $db )
		{
			$sql = "SELECT count(DISTINCT(uid)) FROM liveroom WHERE luid={$luid}";
			$res = $this->db->query($sql);
			if (!$res) {
				$t = "QueryError @ getRoomUserCount()[{$this->db->errno()}][{$this->db->errstr()}][$sql]";
				mylog($t);
				return false;
			}
			$row = $res->fetch_row();
			return $row[0];
		};

		if($viewCount < 0)
		{
			$viewCount = $getRoomUserCount( $luid );
			$count = (int)$viewCount;
			$this->redis->set('liveCountFictitious' . $this->luid, "$count");
		}

		return (int)$this->redis->get( 'liveCountFictitious'. $luid );

	}

	public function getLiveCountPeakValueFictitious()
	{
		return (int)$this->redis->get( 'liveCount' . $this->luid . ':peakValueFictitious' );
	}

	public static function staticGetLiveCountPeakValueFictitious( $luid, $redis )
	{
		return $redis->get( 'liveCount' . $luid . ':peakValueFictitious' );
	}


	public static function staticGetLiveCountPeakValue( $luid, $redis )
	{
		return $redis->get( 'liveCount' . $luid . ':peakValue' );
	}

	public static function staticGetLiveuserCount( $luid, $redis)
	{
		return (int)$redis->get( 'liveCountFictitious' . $luid );
	}

	public function getRoomGiftInfo($gid, $type){
		$res = $this->db->query("select * from gift where id = $gid and type = $type");
		$row = $res->fetch_assoc();

		if(!$row['id'])
			return false;

		return $row;
	}
    // 取用户信息
    private function _getUser($uid, $anchor=0)
    {

        if ($uid>=LIVEROOM_ANONYMOUS) return array('nick'=>'');

        if($anchor){
            $sql = "select userstatic.nick as nick, anchor.level as `level`, userstatic.pic as pic from userstatic, anchor where userstatic.uid=$uid and anchor.uid = userstatic.uid";
        }else{
            $sql = "SELECT userstatic.nick as nick, useractive.level as `level`, userstatic.pic as pic FROM userstatic , useractive WHERE userstatic.uid=$uid and useractive.uid= userstatic.uid";
        }

    	$res = $this->db->query($sql);
        if (!$res)
        {
            $t = "QueryError @ LiveRoom::_getUser($uid)[{$this->db->errno()}][{$this->db->errstr()}][$sql]";
            mylog($t);
            return false;
        }
        if (!$res->num_rows)
        {
            $t = "User $uid not exist @ LiveRoom::_getUser($uid)";
            mylog($t);
            return false;
        }

        $user = $res->fetch_assoc();
        return $user;
    }
    // 取直播信息
    private function _getLive()
    {
    	$sql = "SELECT * FROM live WHERE uid={$this->luid} and status=".LIVE;
    	$res = $this->db->query($sql);
        if (!$res)
        {
            $t = "QueryError @ _getLive()[{$this->db->errno()}][{$this->db->errstr()}][$sql]";
            mylog($t);
            return false;
        }
        if (!$res->num_rows)
        {
            $t = "Live not exist @ _getLive()";
            mylog($t);
            return false;
        }

        $live = $res->fetch_assoc();
        return $live;
    }

    // 取所有聊天服务器地址
    private function _getServers()
    {
        $sql = "select distinct(concat(inet_ntoa(serverip),':',serverport)) from liveroom where luid={$this->luid}";
        $res = $this->db->query($sql);
        while ($row = $res->fetch_row())
            $this->_servers[] = $row[0];
        return true;
    }
	// 消息压缩
    public function msgEncode($msg)
    {
        $msgGz = gzdeflate($msg, 6);
        $msgGz = $this->_myBase64Encode($msgGz);
        return $msgGz;
    }

    //消息解压
    public function msgDecode($msgGz)
    {
        $msg = $this->_myBase64Decode($msgGz);
        $msg = gzinflate($msg);
        return $msg;
    }

    private function _myBase64Encode($str)
    {
        $base64Str = base64_encode($str);
        $base64Str = str_replace(array('+', '/', '='), array('(', ')','@'), $base64Str);
        return $base64Str;
    }

    private function _myBase64Decode($str)
    {
        $base64Str = str_replace(array('(', ')','@'), array('+', '/', '='), $str);
        $base64Str = base64_decode($base64Str);
        return $base64Str;
    }

    private function _socketErrorNotice($code){

        $error = array(
            -3503=>array(
                'errtype'=>1,
                'errwd'=>'获取用户信息失败，或者当前用户不存在'
            ),
            -3501=>array(
                'errtype'=>1,
                'errwd'=>'聊天消息保存失败'
            ),
            -3512=>array(
                'errtype'=>1,
                'errwd'=>'聊天消息不能为空'
            ),
            -3502=>array(
                'errtype'=>1,
                'errwd'=>'用户发言发送失败，socket发送失败'
            ),
            -3009=>array(
                'errtype'=>2,
                'errwd'=>'当前用户被禁言',
            ),
            -3015=>array(
                'errtype'=>2,
                'errwd'=>'发言字数超过限制'
            ),

            //送礼相关
            -3511=>array(
                'errtype'=>1,
                'errwd'=>'传入参数错误'
            ),
            -3504=>array(
                'errtype'=>1,
                'errwd'=>'获取用户信息失败，或者当前用户不存在'
            ),
            -3507=>array(
                'errtype'=>1,
                'errwd'=>'获取主播信息失败，或者当前主播不存在',
            ),
            -3510=>array(
                'errtype'=>1,
                'errwd'=>'当前服务器繁忙'
            ),
            -3508=>array(
                'errtype'=>9,
                'errwd'=>'送礼成功，全局通知消息发送失败'
            ),
            -3509=>array(
                'errtype'=>9,
                'errwd'=>'送礼成功，房间通知消息发送失败'
            ),
            -3514=>array(
                'errtype'=>2,
                'errwd'=>'您的欢朋币余额不足,是否立即充值'
            ),
            -3515=>array(
                'errtype'=>2,
                'errwd'=>'您的欢朋豆余额不足'
            )
        );


        if($error[$code]){
            return $error[$code];
        }else{
            return array();
        }
    }

    public function isCanAddToPushMsgList($liveid)
	{
		$limitTime = 300;
		$sql = "select id,stime,status from live_pushmsg_list where liveid=$liveid order by id desc limit 1";
		$res = $this->db->query( $sql );
		$row = $res->fetch_assoc();

		if(!$row['id'])
		{
			return true;
		}
		if( $row['status'] != LIVE_PUSH_FINISH )
		{
			return false;
		}
		if( $row['stime'] != "0000-00-00 00:00:00" && time() - strtotime($row['stime']) >$limitTime )
		{
			return true;
		}

		return false;
	}
    public function addPushMsgList($liveid)
	{
		$sql = "insert into live_pushmsg_list (liveid,luid) VALUE ($liveid,{$this->luid})";
		return $this->db->query( $sql );
	}
}

?>
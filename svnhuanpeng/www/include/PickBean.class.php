<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/3/14
 * Time: 下午3:57
 */
class PickBean{

	public $uid;

	private $db;
	private $date = '';
	private $result = array();

	public function __construct($uid, $db){
		$this->uid = (int)$uid;
		if(!$this->uid){
			return false;
		}

		if($db){
			$this->db = $db;
		}else{
			$this->db = new DBHelperi_huanpeng();
		}

		$this->date = date('Y-m-d');

		$this->result['time'] = '';
		$this->result['lvl'] = '1';
		$this->result['isVip'] = '0';

		return true;
	}

	//
	public function enterRoom($luid){
		if($this->isFirstEnter()){
			if(!$this->createEnterRoomRecord($luid,1)){
				return -1111;
			}
		}else{
			if($r = $this->isFinished()){
				$this->result['time'] = '0';
				$this->result['lvl'] = $r;
				$this->result['isVip'] = '0';

				return $this->result;
			}
		}

		if(!$this->getPickRoomID()){
			$this->changeRoom($luid);
			$this->updateUtimer();
		}

		$pick = $this->getPickInfo();
		$this->result['time'] = $pick['time'];
		$this->result['lvl'] = ''.$pick['pickid'];

		return $this->result;
	}
	public function lockInTime($luid){
		if(!$luid){
			return -400;
		}
		if(!$this->isCurrentRoom($luid)){
			return -400;
		}

		return $this->updateTimer();
	}
	public function exitRoom($luid){
		if($this->isFinished()){
			return true;
		}
		if(!$this->isInRoom()){
			$this->updateTimer(true);
			$this->setNoRoom();
			return false;
		}

		if($this->isCurrentRoom($luid)){
			$this->updateTimer(true);

			if($r = $this->isInRoom()){
				$this->changeRoom($r);
			}else{
				$this->setNoRoom();
			}
		}
	}

	public function pickTheBean($luid,$lvl){
		//encpass?
		if(!$this->canGetBean($lvl)){
			return -4048;
		}
		//获取hpbean范围
		$beanRange = $this->getRuleBeanRange($lvl);
		$beanRange = explode(',', $beanRange);

		$hpbean = rand($beanRange[0], $beanRange[1]);
		//更新获取纪录表
        $this->db->autocommit(false);
		$this->db->query('begin');
		if($this->updateRecord($luid,$lvl,$hpbean)){
			if($lvl < $this->getLastPickID()){
				$lvl = $lvl + 1;
				if(!$this->createEnterRoomRecord($luid, $lvl)){
					$this->db->rollback();
					return -5017;
				}else{
					$sql = "update useractive set hpbean=hpbean + $hpbean where uid={$this->uid}";
					$res = $this->db->query($sql);
					if(!$res){
						$this->db->rollback();
						return -5017;
					}else{
                        $this->db->commit();
                        $this->db->autocommit(true);
                    }
				}
			}else{//最后一次获取
                $sql = "update useractive set hpbean=hpbean + $hpbean where uid={$this->uid}";
                $res = $this->db->query($sql);
                if(!$res){
                    $this->db->rollback();
                    return -5017;
                }else{
                    $this->db->commit();
                    $this->db->autocommit(true);
                }
            }
		}else{
			return -5017;
		}

		return $hpbean;
	}

	public function isInRoom(){
		$sql = "select luid from liveroom where uid={$this->uid} group by tm desc limit 1";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['luid'] ? (int)$row['luid'] : false;
	}
	public function getPickInfo(){
		$sql = "select pickid, status, time from pickupHpbean where uid={$this->uid} and date='{$this->date}' group by pickid desc limit 1";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		$getHp = $row;

		$ruleTime = $this->getPickRuleTime($getHp['pickid']);

		$utime = $this->getUtimer();
		$sTime = time() - $utime + $getHp['time'];

		$getHp['time'] = $ruleTime - $sTime;

		return $getHp;
    }
	public function getPickRuleTime($id){
		$sql = "select time from pickupRule where id = $id";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return $row['time'];
	}
	public function isFinished(){
		$pickid = $this->getLastPickID();

		$sql = "select status from pickupHpbean where uid={$this->uid} and date='{$this->date}' and pickid=$pickid";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['status'] ? (int)$pickid + 1 : false;
	}

	public function getLastPickID(){
		$sql = "select max(id) as id from pickupRule";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return $row['id'];
	}

	public function isFirstEnter(){
		$sql = "select count(pickid) as count from pickupHpbean where uid={$this->uid} and date='{$this->date}'";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['count'] ? false : true;
	}

	public function createEnterRoomRecord($luid,$pickid){
		$utime = date('Y-m-d H:i:s');
		$sql = "insert into pickupHpbean(date,uid,pickid,luid, utime) value('{$this->date}',{$this->uid},$pickid,$luid,'$utime')";
		return $this->db->query($sql);
	}

	public function updateRecord($luid,$lvl,$hpbean){
		$sql = "update pickupHpbean set getNum=$hpbean,status=1 where `date`='{$this->date}' and uid={$this->uid} and pickid=$lvl";
		$this->db->query($sql);
        return $this->db->affectedRows;
	}
	public function isCurrentRoom($luid){
		$sql = "select luid from pickupHpbean where uid={$this->uid} and date='{$this->date}' and status=0 and luid = $luid";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['luid'] ? true : false;
	}
	public function setNoRoom(){
		$sql = "update pickupHpbean set luid=0 where uid = {$this->uid} and date='{$this->date}' and status=0";
		return $this->db->query($sql);
	}
	public function getPickRoomID(){
		$sql = "select luid from pickupHpbean where uid = {$this->uid} and date='{$this->date}' and status=0";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['luid'];
	}
	public function changeRoom($luid){
		$sql = "update pickupHpbean set luid=$luid where uid = {$this->uid} and date='{$this->date}' and status=0";

		return $this->db->query($sql);
	}

	public function updateTimer($flag=false){
		$utime = $this->getUtimer();
		if(!(int)$utime){
			return -4001;
		}
		$time = time() - $utime;
		$utime = date('Y-m-d H:i:s');

		if($flag){
			$sql = "update pickupHpbean set `time` = `time` + $time,utime='$utime' where uid = {$this->uid} and date='{$this->date}' and status=0";
			if(!$this->db->query($sql)){
				return -4002;
			}
		}else{
			if($time >= 30){
				$sql = "update pickupHpbean set `time` = `time` + $time,utime='$utime' where uid = {$this->uid} and date='{$this->date}' and status=0";
				if(!$this->db->query($sql)){
					return -4003;
				}
			}
		}

		return true;
	}
	public function isPick($lvl){
		$sql = "select status from pickupHpbean where `date`='{$this->date}' and uid={$this->uid} and pickid=$lvl";
		$res = $this->db->query($sql);

		$row = $res->fetch_assoc();

		return (int)$row['status'] ? true : false;
	}
	private  function getUtimer(){
		$sql = "select utime from pickupHpbean where uid = {$this->uid} and date='{$this->date}' and status=0";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return strtotime($row['utime']);
	}
	private function updateUtimer(){
		$utime = date('Y-m-d H:i:s');
		$sql = "update pickupHpbean set utime='$utime' where uid = {$this->uid} and date='{$this->date}' and status=0";

		return $this->db->query($sql);
	}

	private function canGetBean($lvl){
		$utime = $this->getUtimer();

		if(!$this->isCurrLvl($lvl)){
			return false;
		}

		$ruleTime = $this->getRuleTime($lvl);
		$waitTime = $this->getWaitTime($lvl);

		if((time() - $utime + $waitTime) >= $ruleTime){
			return true;
		}

		return false;
	}
	private function isCurrLvl($lvl){
		if(!(int)$lvl){
			return false;
		}

		$currLvl = $this->getCurrLvl();
		if(!$currLvl || $currLvl != $lvl){
			return false;
		}

		return true;
	}
	public function getCurrLvl(){

		$sql = "select pickid from pickupHpbean where uid = {$this->uid} and date='{$this->date}' and status=0 and luid != 0";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['pickid'];
	}
	private function getRuleTime($lvl){
		$sql = "select time from pickupRule where id = $lvl";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return $row['time'];
	}
	private function getWaitTime($lvl){
		$sql = "select time from pickupHpbean where uid={$this->uid} and date='{$this->date}' and pickid=$lvl and status=0";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['time'];
	}

	private function getRuleBeanRange($lvl){
		$sql = "select `range` from pickupRule where id = $lvl";
		$res = $this->db->query($sql);
		$row = $res->fetch_assoc();

		return $row['range'];
	}
}
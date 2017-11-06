<?php

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/7/11
 * Time: 上午10:11
 */
namespace hp\live;
class Review
{

    const tab_task = "admin_liveReview";
    const tab_resion = "admin_liveReviewReason";
    const tab_review = "admin_liveReviewResult";
    const tab_blacklist = "anchor_blackList";
    const tab_live = "live";
    const tab_record = "anchorblackrecord";

    const live_on = '1';
    const live_stop = '0';

    const task_lock = "1";
    const task_unlock = "0";
    const task_off = "2";

    const msgKey = MSG_ADMIN;

	private $msgUrl = '';
    private $finish = 0;
    private $notice = 1;
    private $stop = 2;
    private $kill = 3;


    private $db = null; 
    private $uid;

    public function __construct($uid, $db)
    {
        $this->uid = $uid;
        if ($db)
            $this->db = $db;
        else
            $this->db = new \DBHelperi_admin();

		if(strtoupper($GLOBALS['env'] ) == 'DEV'){
			$this->msgUrl = 'http://dev.huanpeng.com/api/public/manage/liveManage.php';
		} elseif(strtoupper($GLOBALS['env'] ) == 'PRE') {
			$this->msgUrl = 'http://pre.huanpeng.com/api/public/manage/liveManage.php';
		} else {
            $this->msgUrl = 'http://www.huanpeng.com/api/public/manage/liveManage.php';
		}


    }

    public function getTask($page = 1)
    {
        $num = 9;
        $offset = ($page -1) * $num;
        $sql = "select liveid, uid as luid from " . self::tab_live . " where status=" . LIVE . " order by liveid desc limit " . $offset . "," . $num;
        
        $res = $this->db->doSql($sql);
        return $res;
    }

    public static function setTask($liveid, $db = null)
    {
        if (!$db) $db = $db = new \DBHelperi_admin();
        if (!$liveid) return false;

        $luid = self::getLuid($liveid);
        return $db->insert(self::tab_task, array('liveid' => $liveid, "livestatus" => self::live_on, 'luid'=> $luid));
    }

    public static function taskHanlder($liveid, $db = null)
    {
        if (!$db) $db = $db = new \DBHelperi_admin();
        $sql = "select uid from " . self::tab_live . " where liveid=$liveid";
        return (int)$db->doSql($sql)[0]['uid'];
    }

    public function myTask()
    {
        $sql = "select liveid, luid from " . self::tab_live . " where uid = $this->uid and livestatus=" . self::live_on;
        $row = $this->db->doSql($sql);
        return $row ? $row : array();
    }

    public function lockTask($liveid)
    {
        if (!$liveid) return false;
        $this->db->where("liveid=$liveid and livestatus=" . self::live_on . " and status=" . self::task_unlock)->update(self::tab_live, array('status' => self::task_lock, 'uid' => $this->uid));

        if ($this->db->affec1tedRows) {
            return $liveid;
        }
        return false;
    }

    public function unlockTask($liveid)
    {
        if (!$liveid) return false;
        return $this->db->where("liveid=$liveid and status=" . self::task_lock)->update(self::tab_live, array('status' => self::task_unlock, "uid" => 0));
    }

    public function succEnd($liveid)
    {
        $this->handelRecord( $liveid, $this->finish, 0);
    }

    public function notice($liveid, $reason)
    {
        $luid = $this->getLuid($liveid);   
        $this->handelRecord($liveid, $this->notice, $reason);
        $rid=$reason;
        $reason = $this->getReason($reason);
        if($rid ==44){
            $reason='你的直播因违反相关规定,特此警告!';
        }else{
            $reason='你的直播因涉嫌'.$reason.',特此警告!';
        }
        return $this->_sendMessage($luid, $liveid, $this->notice, $reason);
    }

    public function stop($liveid, $reason)
    {

        $luid = $this->getLuid($liveid);
        $rid=$reason;
        $this->handelRecord($liveid, $this->notice, $reason);
        $reason = $this->getReason($reason);
        if($rid ==44){
            $reason='你的直播因违反相关规定,已被停止!';
        }else{
            $reason='你的直播因涉嫌'.$reason.',已被停止!';
        }
        return $this->_sendMessage($luid, $liveid, $this->stop, $reason);
    }

    public function kill($liveid, $reason)
    {
        $luid = $this->getLuid($liveid);
        $recordid = $this->handelRecord($liveid, $this->notice, $reason);
        if(empty($recordid)){
            $recordid =0;
        }
        $rid=$reason;
        $sql = "insert into " . self::tab_blacklist . " (luid, recordid) values($luid, $recordid)";
        dong_log('封号',$sql.$recordid,$this->db);
        $this->db->query($sql);
        $reason = $this->getReason($reason);
        if($rid ==44){
            $reason='你的直播因违反相关规定,已被封号!';
        }else{
            $reason='你的直播因涉嫌'.$reason.',已被封号!';
        }
        return $this->_sendMessage($luid, $liveid, $this->kill, $reason);
    }
    
    public function killNoLive($luid)
    {
        if(!$luid){
            return false;
        }
        $recordid = 0;
        $sql = "insert into " . self::tab_blacklist . " (luid, recordid) values($luid, $recordid)";
        dong_log('封号',$sql.$recordid,$this->db);
        
        return $this->db->query($sql);
    }

    public static function getLuid($liveid, $db=null)
    {
        if (!$db) $db = $db = new \DBHelperi_admin();
        $sql = "select uid from " . self::tab_live . " where liveid=$liveid";
        return (int)$db->doSql($sql)[0]['uid'];
    }

    public function isLiveOn($liveid){
        $sql = "select liveid from live where liveid=$liveid and status = ".LIVE;
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return (int)$row['liveid'];
    }

    public function getReason($reasonid)
    {
        $sql = "select reason from " . self::tab_resion . " where id=$reasonid";
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return $row['reason'];
    }


    private function _sendMessage($luid, $liveid, $order, $reason)
    {
        $data = array(
            "luid" => $luid,
            //'liveid' => $liveid,
            "order" => $order,
            "tm" => time(),
            "reason" => $reason
        );
        $sign = buildSign(toString($data), self::msgKey);
        $url =$this->msgUrl . "?" . http_build_query($data) . "&sign=$sign";
        mylog("--handleAdminOrder:".$url, LOGFN_SEND_MSG_ERR);
        return file_get_contents($url);
//        return $url;
    }

    private function handelRecord($liveid, $type, $reason)
    {

        $data = array(
            "taskid" => $liveid,
            "type" => $type,
            "reason" => $reason
        );
        if($this->db->insert(self::tab_review, $data))
            return $this->db->insertID;
        else
            return false;
    }
    
    /*
    private function getTaskId($liveid)
    {
        $sql = "select id from ". self::tab_task ." where liveid = $liveid";
        $res = $this->db->doSql($sql);
        return $res[0]['id'];
    }
    
    public static function setLiveStatus($liveid, $status, $db = null)
    {
        if (!$db) $db = new \DBHelperi_admin();
        if (!$liveid) return false;

        return $db->where("liveid=$liveid")->update(self::tab_task, array('livestatus' => $status));
    }
    */
    function insertRecord($uid, $luid, $type, $reason, $liveid)
    {
        $data = array(
            "uid" => $uid,
            "luid" => $luid,
            "type" => $type,
            "reason" => $reason,
            "liveid" => $liveid
        );
        if($this->db->insert(self::tab_record, $data)) {
            return $this->db->insertID;
        } else {
            return false;
        }
    }

}
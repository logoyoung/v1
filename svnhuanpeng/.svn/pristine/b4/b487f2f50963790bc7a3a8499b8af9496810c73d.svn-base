<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/26
 * Time: 下午6:46
 */

class LiveHelp{

	public $liveid;

	private $db;

	public function __construct($liveid, $db=null){
		$this->liveid = (int)$liveid;
		if(!$this->liveid) return false;

		if($db)
			$this->db = $db;
		else
			$this->db = new DBHelperi_huanpeng();
	}

    /**
     * 获取直播流名称
     *
     * @return string
     */
	public function getLiveStream(){
		return "Y-".$this->liveid.'-'. rand(1000000, 9999999);
	}

	public static function getStreamCallBackString($stream,$liveid,$uid){
	    $data = array(
            'liveid' => $liveid,
            'uid'=>$uid,
            'tm'=>time()
        );
        $data['sign'] = buildSign(toString($data), STREAM_SECRET, false);
	    return $stream."?".http_build_query($data);
    }
    /**
     * 判断是否是当前直播
     *
     * @param $deviceid
     * @return bool
     */
	public function isCurrentLive($deviceid){
		$sql = "select deviceid from live where liveid=$this->liveid";
		$res = $this->db->query($sql);

		$row = $res->fetch_assoc();

		if($row['deviceid'] != $deviceid){
            return false;
        }else{
            return true;
        }
	}

    /**
     * 判断当前配置信息是否更改
     *
     * @param $title
     * @param $gameid
     * @param $gametid
     * @param $gamename
     * @param $quality
     * @param $orientation
     * @return bool
     */
    public function isCurrentConfig($title, $gameid, $gametid, $gamename, $quality, $orientation,$livetype){
        $row = $this->getLiveInfo();
        if(!$row) return false;
        foreach($row as $key => $val){
            if($$key != $val){
                mylog("the diff is $key: {$$key}, and val:{$val}", LOGFN_SEND_MSG_ERR);
                return false;
            }
        }

        return true;
    }

	/**
	 * 继续直播接口
	 *
	 * @param $server
	 * @param $stream
	 * @return bool
	 */
	public function continueLive(&$server, &$stream){
	    if($stream){
	        $stream = $stream;
        }else{
            $stream = $this->getLiveStream();
        }
		$server = $this->getStreamServer();

		$this->db->autocommit(false);
		$this->db->query('begin');
		if(!$this->addLiveRecord($server, $stream) || !$this->updateLiveStream($server, $stream)){
			$this->db->rollback();
			return false;
		}
		$this->db->commit();
		$this->db->autocommit(true);

		return true;
	}

    /**
     * 中断重连的直播流地址列表
     *
     * @param $server
     * @param $stream
     * @return 对于更新语句
     */
	public function addLiveRecord($server, $stream){
        $utime=date('Y-m-d H:i:s',time());
		$sql = "insert into liveStreamRecord(liveid, server, stream,utime) VALUE($this->liveid, '$server', '$stream','$utime')";
		return $this->db->query($sql);
	}

	public function addLiveRecordClient($server, $stream){
		$utime=date('Y-m-d H:i:s',time());
		$sql = "insert into liveStreamRecord(liveid, server, stream,utime,status) VALUE($this->liveid, '$server', '$stream','$utime',".LIVE_CLIENT_CREATE.")";
		return $this->db->query($sql);
	}

    /**
     * 更新直播流地址
     *
     * @param $server
     * @param $stream
     * @return 对于更新语句
     */
	public function updateLiveStream($server, $stream){
		$sql = "update live set server='$server', stream='$stream' where liveid=$this->liveid";
		return $this->db->query($sql);
	}

	public function getStreamServer(){
		$conf = $GLOBALS['env-def'][$GLOBALS['env']];
		return $conf['stream-pub'];
	}

    /**
     * 获取录像地址
     *
     * @return mixed
     */
	public function getNotifyServer(){
		$conf = $GLOBALS['env-def'][$GLOBALS['env']];
		return $conf['stream-stop-notify'];
	}

    /**
     * 停止直播
     *
     * @return bool
     */
	public function stopLive($stop_reason=0){
		$etime = date('Y-m-d H:i:s');
		$sql = "update live set status=".LIVE_STOP.", etime='$etime', stop_reason=$stop_reason where liveid=$this->liveid";
		$res = $this->db->query($sql);
		if(!$res){
			$t = 'Query Error (' . $this->db->errno() . ') '. $$this->db->errstr();
			mylog($t);
			return false;
		}

		return true;
	}

    /**
     * 录像转换直播记录表
     *
     * @param $stype
     * @return 对于更新语句
     */
	public function addLive2VideoRecord($stype = VIDEO_SAVETYPE_CALL){
		$sql = "insert into videosave_queue(liveid, status, stype) VALUE ($this->liveid,".LIVE_STOP.", $stype)";
		return $this->db->query($sql);
	}

	public function live2videoGo(){
		$sql = "update videosave_queue set `go`=1 where liveid=".$this->liveid;
		return $this->db->query($sql);
	}

    /**
     * 获取直播信息
     *
     * @return mixed
     */
    public function getLiveInfo(){
        $sql = "select title,gameid, gametid,gamename, orientation, quality,livetype from live where liveid=$this->liveid";
        $res = $this->db->query($sql);

        $row = $res->fetch_assoc();
//        if($row)
//            $row['quality'] = 2;
        return $row;
    }

    public function isClientCreateStatus(){
        $sql = "select liveid from live where liveid=$this->liveid and status=".LIVE_CLIENT_CREATE;
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        if($row['liveid'])
            return (int)$row['liveid'];

        return false;
    }

    public function getClientConfServer(){
        $sql = "select stream,server from live where liveid=$this->liveid and status=".LIVE_CLIENT_CREATE;
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        if($row['stream'] && $row['server']){
            return $row;
        }
        return false;
    }

    public function setLiveStaus($status){
        $sql = "update live set status=$status where liveid=$this->liveid";
        $this->db->query($sql);

        return $this->db->affectedRows;
    }

    public function isLiving(){
        $sql = "select liveid from live where liveid = $this->liveid and status = ".LIVE;
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();
        return (int)$row['liveid'];
    }

    public function clientStart(){
        if(!$ret = $this->getClientConfServer())
            return false;
//        $this->db->autocommit(false);
//        $this->db->query('begin');

        if(!$this->setLiveStaus(LIVE)){//!$this->addLiveRecord($ret['server'], $ret['stream'] )||
//            $this->db->rollback();
            return false;
        }

//        $this->db->commit();
//        $this->db->autocommit(true);

        return true;
    }
}

?>
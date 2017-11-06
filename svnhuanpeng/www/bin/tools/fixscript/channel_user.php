<?php
include __DIR__."/../../../include/init.php";

error_reporting(E_ALL);

$db = new DBHelperi_huanpeng();


$fixStartData = "2017-08-29 00:00:00";


/**
* 
*/
class FixChannelUser 
{
	
	private $db;

	private $startTime;

	private $endTime;

	function __construct($startTime, $endTime)
	{
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->db = new DBHelperi_huanpeng();
	}

	private function getNoneChannelUserIDs()
	{

		$sql = "select uid from channel_user where ctime between '{$this->startTime}' and '{$this->endTime}' and channel=0";
		$res = $this->db->query($sql);

		if(!$res)
		{
			throw new Exception("$sql".$this->db->errstr, 1);
		}


		$uidList = [];
		while ($row = $res->fetch_assoc()) {
			array_push($uidList, $row['uid']);
		}

		return $uidList;
	}

	private function getUidsFirstChannelIDs($uidList)
	{
		if(empty($uidList))
		{
			return [];
		}

		$uidList = implode(',', $uidList);

		$sql = "select channel,uid from admin_userviewrecord where ctime between '{$this->startTime}'  and '{$this->endTime}' and uid in ($uidList) group by uid";
		$res = $this->db->query($sql);

		if(!$res)
		{
			throw new Exception("$sql  error:".$this->db->errstr, 1);
		}


		$channelList = [];
		while ($row = $res->fetch_assoc() ) 
		{
			if($row['channel'])
				$channelList[$row['uid']] = $row['channel'];
		}

		return $channelList;
	}

	private function update($uid,$channel)
	{
		if(!$uid)
		{
			return false;
		}

		$sql = "update channel_user set channel=$channel where uid=$uid";
		return $this->db->query($sql);
	}

	private function insert($uid,$channel,$ctime)
	{
		$sql = "insert into channel_user(uid,channel,ctime) value($uid,$channel,'$ctime')";

		return $this->db->query($sql);
	}

	public function doFix()
	{
		$uidList = $this->getNoneChannelUserIDs();

		echo"none channel uid list is " . json_encode($uidList) ."\n";

		$channelUser = $this->getUidsFirstChannelIDs($uidList);
		echo "real channel user list " . json_encode($channelUser)."\n";
		// exit();
		foreach ($channelUser as $uid => $channel) {
			$result = $this->update($uid,$channel);

			echo "set $uid to channel $channel result is ".json_encode($result)."\n";
		}
	}

	private function getUnInsertUserInfoByDate()
	{
		$sql = "select uid,rtime from userstatic where rtime between '{$this->startTime}' and '{$this->endTime}' and uid not in (select uid from channel_user where ctime between '{$this->startTime}' and '{$this->endTime}')";

		$res = $this->db->query($sql);

		if(!$res)
		{
			throw new Exception("$sql". $this->db->errstr, 1);
		}

		$list = [];

		while ($row = $res->fetch_assoc()) {
			$uid = $row['uid'];
			$rtime = $row['rtime'];
			$list[$uid] = [];

			$list[$uid]['ctime'] = $rtime;
		}

		return $list;
	}

	private function getUnInsertUserInfoByUidList($uidList)
	{
		if(empty($uidList) || !is_array($uidList))
		{
			return [];
		}

		$uidList = implode(",", $uidList);

		$sql = "select uid,rtime from userstatic where uid in ($uidList)";
		$res = $this->db->query($sql);

		$list = [];
		while($row = $res->fetch_assoc())
		{
			$uid = $row['uid'];
			$rtime = $row['rtime'];
			$list[$uid] = [];

			$list[$uid]['ctime'] = $rtime;
		}

		return $list;
	}

	private function getUnInsertUserList($uidList=[])
	{
		
		$list = [];

		if( empty( $uidList ) || !is_array( $uidList ) )
		{
			$list = $this->getUnInsertUserInfoByDate();
		}
		else
		{
			$list = $this->getUnInsertUserInfoByUidList($uidList);
		}


		$uidList = array_keys($list);

		$uidChannelList = $this->getUidsFirstChannelIDs($uidList);

		foreach ($list as $uid => $info) {

			$channel = $uidChannelList[$uid] ?? 0;

			$list[$uid]['channel'] = intval( $channel );
		}

		return $list;
	}


	/**
	*	$info [
			123 => [channel=>1,ctime=>2017]
	*	]
	*/
	public function doFixUnInsertUser($uidList=[])
	{
		$list = $this->getUnInsertUserList($uidList);

		echo "uninsert uesr info list is " . json_encode($list) ."\n";

		foreach ($list as $uid => $info) 
		{
			$channel = $info['channel'];
			$ctime = $info['ctime'];

			$result = $this->insert($uid,$channel, $ctime);

			echo "inser into channel_user uid:$uid, channel:$channel, ctime:$ctime ==> result is: ".json_encode($result)."\n";
		}
	}
}

$ctime = "2017-09-01 00:00:00";
$etime = "2017-09-01 23:59:59";

$fix = new FixChannelUser($ctime,$etime);

$fix->doFixUnInsertUser();
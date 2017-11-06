<?php
include_once INCLUDE_DIR . "LiveRoom.class.php";

/**
 * Class WABPush
 * 用于发送安卓的消息推送
 */
class WABPush
{
    private $lroom;
    private $db;

    public function  __construct($db = null)
    {
        if ($db) {
            $this->db = $db;
        } else {
            $this->db = new DBHelperi_huanpeng();
        }

        $this->lroom = new LiveRoom(PUSH_ROOM_ID, $db);
    }

    public function liveStart($luid)
    {
        $anchorHelp = new AnchorHelp($luid, $this->db);
        $info = $anchorHelp->getUsers();
        $nick = $info['nick'];
        $pic = $info['pic'];
        $content = array(
            "t" => 2001,
            "luid" => $luid,
            "msg" => "主播:$nick 开始直播啦，快点前去围观吧~",
            'nick' => $nick,
            'pic' => $pic,
            "tm" => time()
        );

		$pushUidList = $this->getReceivePushMsgUserList($this->getAcnhorPushMsgUserList($luid));
		//ios推送
		$this->pushToIphone($pushUidList, $content);

        $uidList = $this->getpushMsgUserList($pushUidList);

        foreach ($uidList as $key => $val) {
            $this->lroom->sendUserMsg($val, json_encode($content));
        }

        $content = array(
            't' => 2002,
            'luid'=>$luid,
            'tm' => time()
        );
        $this->lroom->sendRoomMsg(json_encode(toString($content)));
    }

    /**
     * 检查是否开启推送
     *
     * @param $uid
     *
     * @return bool
     */
    public function isReceivePushMsg($uid)
    {
        $sql = "select isnotice from useractive where uid = $uid";
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();

        return (int)$row['isnotice'] ? true : false;
    }

    /**
     * 发送开播提醒的用户列表
     *
     * @param $uids
     *
     * @return array
     */
    public function getpushMsgUserList($uids)
    {
        $userList = array();
        if (!$uids || !is_array($uids)) {
            return $userList;
        }

        $value = "(" . implode(',', $uids) . ")";

        $sql = "select uid from liveroom where uid in $value and luid =" . PUSH_ROOM_ID;
        $res = $this->db->query($sql);
        while ($row = $res->fetch_assoc()) {
            array_push($userList, $row['uid']);
        }

        return $userList;
    }

    /**
     * 返回接受开播提醒的 uidList
     *
     * @param $uids
     *
     * @return array
     */
    public function getReceivePushMsgUserList($uids)
    {
        $userList = array();
        if (!$uids || !is_array($uids))
            return $userList;

        $values = "(" . implode(',', $uids) . ")";

        $sql = "select uid from useractive where uid in $values and isnotice = " . LIVE_START_NOTICE_RECEIVE;
        $res = $this->db->query($sql);


        while ($row = $res->fetch_assoc()) {
            array_push($userList, $row['uid']);
        }

        return $userList;
    }

    /**
     * 返回对主播设置开播提醒的UID 列表
     *
     * @param $luid
     *
     * @return array
     */
    public function getAcnhorPushMsgUserList($luid)
    {
        $sql = "select uid from live_notice where luid = $luid";
        $res = $this->db->query($sql);

        $userList = array();

        while ($row = $res->fetch_assoc()) {
            array_push($userList, $row['uid']);
        }

        return $userList;
    }

    public function pushToIphone($uidList,$content){
		if(!$uidList)
			return true;
		$uids = "(" . implode(',', $uidList) .")";
		$sql = "select deviceToken, uid from push_notify_set where uid in $uids and isopen=1";
		$res = $this->db->query($sql);
		if(!$res)
			mylog($this->db->errstr()."and the uidList is ".jsone($uidList), LOGFN_DBH_DBUG);
		$whiteList = explode(',',WHITE_LIST);
		$isTestAuthor = in_array($content['luid'], $whiteList);

		while($row = $res->fetch_assoc()){
            mylog(json_encode($row), LOGFN_IOS_PUSH_LOG);
			$custom = static::buildPushToPhoneData(1,['luid'=>$content['luid'],'nick'=>$content['nick'],'pic'=>$content['pic']]);
			$data = [
				'prod'=>APPLE_PUSH_PRO,
				'tk'=>$row['deviceToken'],
				'content'=>$content['msg'],
				'mid' => $content['luid'].'-'.$row['uid'].'-'.time(),
				'title'=>'查看',
				'custom'=>$custom,
				'image'=>'Default.png',
				'sound'=>'default'
			];

			if($GLOBALS['env'] == 'DEV')
			    $url = 'http://pncp.dev/push.php';
			else
			    $url = 'http://applepie/push.php';

			mylog($url, LOGFN_IOS_PUSH_LOG);

			if($this->checkIosPushData($data)){
			    $ret = $this->curl_get($url, $data);
//			    if($GLOBALS['env'] == 'PRO' && ($isTestAuthor || in_array($row['uid'], $whiteList))){
//			        $data['prod'] = 50;
//    			    $url = 'http://applepie/push.php?'.http_build_query($data);
//                }
            }else{
			    $ret = "'data is not format'";
            }
//			$ret = file_get_contents($url);
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $url);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//            curl_setopt($ch, CURLOPT_HEADER, 0);
            //执行并获取HTML文档内容
//            $output = curl_exec($ch);
//            $error = curl_error($ch);
//            curl_close($ch);

//            mylog("what's the fuck error  ".$error, LOGFN_IOS_PUSH_LOG);
//            $get = "curl -i $url";
//            $ret = `$get`;
//			if(is_bool($output))
//			    $output = 'bool'.(int)$output;
			mylog("send push msg ret is ".$ret, LOGFN_IOS_PUSH_LOG);
		}
	}

	public static function buildPushToPhoneData($type,$data){
		return jsone(toString(['type'=>$type, 'data'=>$data]));
	}
	public function checkIosPushData($data){
	    // todo check data is format
        return true;
    }

    public function curl_get($url,$data){
	    $url = $url."?".http_build_query($data);
	    $get = 'curl -Ss '."'$url'";
        $ret = `$get`;

        return $ret;
    }
}

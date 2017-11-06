<?php

    // 检查流更新状态

    require(__DIR__.'/../include/init.php');
	require_once INCLUDE_DIR.'LiveRoom.class.php';
	require_once INCLUDE_DIR."Live.class.php";


//	$GLOBALS['env'] = 'PRO';

    define('MAX_STREAM_IDLE_TIME', 600);
	define("UN_PUSH_IDLE_TIME", 180);
    define('VIDEO_SAVE', WEB_ROOT_URL."/a/timeoutSaveVideo.php");
//    define('VIDEO_SAVE', 'http://dev.huanpeng.com/main/a/timeOutSaveVideo.php');


	mylog("current MAX_STREAM_IDLE TIME IS ".MAX_STREAM_IDLE_TIME, LOGFN_SENDGIFT_LOG);
	mylog("current ENV is ".$GLOBALS['env'], LOGFN_SENDGIFT_LOG);
    function getLiveByStatus($db, $status)
    {
        $sql = "select * from live where status=$status";
        $res = $db->query($sql);
        if (!$res )
        {
            $t = 'Query Error (' . $db->errno() . ') '. $db->errstr();
            mylog($t);
            return false;
        }
        if (!$res->num_rows) return false;
        $ret = array();
        while ($row = $res->fetch_assoc())
            $ret[] = $row;
        return $ret;
    }

    function updateLiveByStatus($db, $liveid, $status){

        $sql = "update live set status=$status,etime=now() where liveid=$liveid";
        $res = $db->query($sql);
        if (!$res )
        {
            $t = 'Query Error (' . $db->errno() . ') '. $db->errstr();
            mylog($t);
            return false;
        }
        return true;
    }

    $debug = true;

    $curl = '/usr/bin/curl';

    $db = new DBHelperi_huanpeng();


	$getLiveBuStatus = function($status) use($db)
	{
		$sql = "select * from live where status=$status";
		$res = $db->query($sql);
		if(!$res)
		{
			$t = 'Query Error (' . $db->errno() . ') '. $db->errstr();
			mylog($t);
			return false;
		}
		if(!$res->num_rows) return false;
		$ret = array();
		while ($row = $res->fetch_assoc())
			$ret[] = $row;
		return $ret;
	};

	$updateLiveByStatus = function($liveid, $status) use($db)
	{
		$sql = "update live set status=$status,etime=now() where liveid=$liveid";
		$res = $db->query($sql);
		if (!$res )
		{
			$t = 'Query Error (' . $db->errno() . ') '. $db->errstr();
			mylog($t);
			return false;
		}
		return true;
	};

    $lives = getLiveByStatus($db, LIVE);

	mylog("current db env ===".$db->env, LOGFN_SENDGIFT_LOG);


	$getUidFromLive = function ( $liveid ) use ( $db ){
		$sql = "select uid from live where liveid=$liveid";
		$res = $db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['uid'];
	};

	$isThisLiveIDLived = function ( $liveid ) use ($db)
	{
		$sql = "select liveid from live where liveid=$liveid and stime != '0000-00-00 00:00:00' and now() - stime > ".UN_PUSH_IDLE_TIME;
		$res =  $db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['liveid'];

	};

	$liveStopHandleFlow = function ($liveid, $uid, $videoType) use ( &$db )
	{
		mylog("the live is end by video time out the liveid ===".$liveid,LOGFN_SENDGIFT_LOG);
		updateLiveByStatus($db,$liveid, LIVE_STOP);
		$live = new LiveHelp($liveid, $db);
		$live->addLive2VideoRecord( $videoType );
		$live->live2videoGo();

		if($uid)
		{
			mylog( "current handle stop stream uid  ===".$uid, LOGFN_SENDGIFT_LOG );
			$lroom = new LiveRoom( $uid, $db );
			setMostPopual( $uid, $liveid,$lroom->getLiveCountPeakValue(), $db );
//			toLiveLength( $liveid, $db );
			$lroom->stop($liveid);
		}

		liveStatusMsgToAdmin($live['liveid'], 0);
	};

    foreach ($lives as $live)
    {


		$liveid = $live['liveid'];
		$uid = $getUidFromLive( $liveid );


		mylog("current handle id ===".$liveid, LOGFN_SENDGIFT_LOG);
		$sql = "select id, status,utime from liveStreamRecord where liveid=$liveid and status < 9 order by id desc limit 1";
		$res = $db->query($sql);
		$row = $res->fetch_assoc();

		mylog("change file check Live", LOGFN_SENDGIFT_LOG);
		$dm = strtotime($row['utime']);
		//status ==  0 handle flow
		if($row['status'] == 0 && (time() - $dm) > UN_PUSH_IDLE_TIME){

			if($isThisLiveIDLived($liveid)){
				$liveStopHandleFlow( $liveid, $uid, VIDEO_SAVETYPE_IDLETIME );
			}else{
				mylog("un push idle time out ==> liveid".$liveid, LOGFN_SENDGIFT_LOG);
				updateLiveByStatus( $db, $liveid, LIVE_TIMEOUT );
				$lroom = new LiveRoom( $uid, $db );
				$lroom->stop( $liveid );
			}
			continue;
		}


		if(!$row['id'] || $row['status'] < 2 )
			continue;


		mylog("current handle stop stream liveid  ===".$liveid, LOGFN_SENDGIFT_LOG);


		$dn = time();

		if($dn - $dm > MAX_STREAM_IDLE_TIME){
			$liveStopHandleFlow($liveid, $uid, VIDEO_SAVETYPE_TIMEOUT);
//			mylog("the live is end by video time out the liveid ===".$liveid,LOGFN_SENDGIFT_LOG);
//			updateLiveByStatus($db,$liveid, LIVE_STOP);
//			$live = new LiveHelp($liveid, $db);
//			$live->addLive2VideoRecord(VIDEO_SAVETYPE_TIMEOUT);
////			if($row['status'] == 3)
//				$live->live2videoGo();
//
////			$sql = "select uid from live where liveid=$liveid";
////			$res = $db->query($sql);
////			$row = $res->fetch_assoc();
//
//			if($uid)
//			{
//				mylog( "current handle stop stream uid  ===".$uid, LOGFN_SENDGIFT_LOG );
//				$lroom = new LiveRoom( $uid, $db );
//				setMostPopual( $uid, $liveid,$lroom->getLiveCountPeakValue(), $db );
////				toLiveLength( $liveid, $db );
//				$lroom->stop($liveid);
//			}
//
//			liveStatusMsgToAdmin($live['liveid'], 0);
		}



//        list($ip, $port) = explode(':',trim($live['server']));
//        $ckurl = "http://$ip:3559/{$live['stream']}.flv";
//        $cmd = $curl." -Ss -I \"$ckurl\"";
//        if ($debug) {mylog($cmd);}
//        $r = `$cmd`;
//        if ($debug) {mylog($r);}
//        $pat = '/Date:(.*?)\r\n.*?Last-Modified:(.*?)\r\n/s';
//        if (preg_match($pat, $r, $mat))
//        {
//            $dn = strtotime($mat[1]);
//            $dm = strtotime($mat[2]);
//            if ($dn-$dm>MAX_STREAM_IDLE_TIME)
//            {
//                updateLiveByStatus($db, $live['liveid'], LIVE_STOP);
//                if ($debug) mylog("stop {$live['liveid']} succ");
//                $res = file_get_contents(VIDEO_SAVE."?liveid={$live['liveid']}");
//                if($debug) mylog("the live {$live['liveid']} to save video reported $res");
//                liveStatusMsgToAdmin($live['liveid'], 0);
//            }
//        }
    }

    echo count($lives);
    exit;

?>
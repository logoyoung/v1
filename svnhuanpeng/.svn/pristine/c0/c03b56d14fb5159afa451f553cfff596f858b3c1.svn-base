<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/7/25
 * Time: 10:03
 */


/*
 *
 * 录像修复
 *
 *
 */

include( __DIR__ . '/../../include/init.php' );
use lib\Video;

class repairvideo {

	/**
	 * 获取未生成的录像
	 *
	 */
	static $_instance = null;
	static $_video = null;
	static $_dir       = array(
		'DEV' => array( 'v' => 'dev/v/' ),
		'PRE' => array( 'v' => 'pre/v/' ),
		'PRO' => array( 'v' => 'pro/v/' )
	);

	function __construct()
	{
		self::$_instance = new DBHelperi_huanpeng();
		self::$_video = new Video();

	}

	function getrepairvideo($start,$end){
		//获取失败录像
		$sql = "select a.liveid aliveid,b.liveid bliveid from live a left join video b on a.liveid=b.liveid where a.etime>'{$start}' and a.etime<'{$end}'";
		$res = self::$_instance->query($sql);
		$liveids = [];
		while ($row = mysqli_fetch_assoc($res)){
			if(empty($row['bliveid']))
				$liveids[] = $row['aliveid'];
		}
		return $liveids;
	}

	function getflvs($liveid){
		$sql = "select * from live_VideoRecord where liveid='{$liveid}'";
		$res = self::$_instance->query($sql);
		$flv = [];
		while ($row = mysqli_fetch_assoc($res)){
			$flv[] = $row['keys'];
		}
		return $flv;
	}

	function repair($param){
		$param = explode('/',$param);
		//获得开始时间
		$start = $param[0];
		//获得结束时间
		$end = $param[1];
		//获得执行方案
		$exec = isset($param[2])?$param[2]:'check';
		$liveids = $this->getrepairvideo($start,$end);
		if($exec == 'do')
		{
			foreach ($liveids as $liveid){
				$flv = $this->getflvs($liveid);
				if(!count($flv)) continue;
				$r= self::$_video->mergeFiles($flv,self::$_dir[$GLOBALS['env']]['v']."$liveid.mp4");
				$r = json_decode($r,true);
				if(empty($r['persistentId']))
				{
					echo "\n任务{$liveid}失败\n";
					var_dump($r);
				}

				//更新记录
				$sql = "update live set status=".LIVE_TO_FLV." where liveid='{$liveid}'";
				$res = self::$_instance->query($sql);
				//添加合并记录
				$optRecord = array(
					'taskid' => $r['persistentId'],
					'liveid' => $liveid,
					'opt'    => (count($flv)>1)?Video::OPT_MERGE:Video::OPT_TRANSCODE,
					'bucket' => Video::WCS_BUCKET_VIDEO,
					'vname'  => self::$_dir[$GLOBALS['env']]['v']."$liveid.mp4",
				);
				//mylog(json_encode($optRecord),LOG_DIR.'Live.error.log');
				$mret      = self::$_video->addOptRecord( $optRecord );
				echo "任务{$liveid}成功 ";
			}
		}
		if($exec == 'rollback')
		{
			foreach ($liveids as $liveid)
			{
				$flv = $this->getflvs($liveid);
				if(!count($flv)) continue;
				//更新记录
				$sql = "update live set status=".LIVE_TO_FLV." where liveid='{$liveid}'";
				$res = self::$_instance->query($sql);
				echo "总行数：".count($liveids);
				echo "\n更新行数".self::$_instance->affectedRows."\n";
			}
		}
		else
		{
			var_dump($liveids);
		}
	}

}
//$param = $argv[1];
$repair = new repairvideo();
$repair->repair($argv[1]);


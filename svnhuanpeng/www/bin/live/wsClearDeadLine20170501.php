<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/5/31
 * Time: 14:27
 */

/**
 *
 * 清除已完成合并flv源文件
 *
 */



include( __DIR__ . '/../../include/init.php' );


use lib\Video;

//test
/*$video = new Video();
$pid = $video->deleteFiles(['1315.mp4']);
var_dump($pid);exit();*/

defined( 'VIDEO_CLEAR' ) or define( 'VIDEO_CLEAR',200 );
define('VIDEO_DEL_FLV_LOG',LOG_DIR.'ws/wsClearDeadLineFlv.log');
define('VIDEO_DEL_MP4_LOG',LOG_DIR.'ws/wsClearDeadLineMP4.log');
define('FLV_DELETE_RECORD_LOG',LOG_DIR.'ws/wsDeleteFLV.log');
define('MP4_DELETE_RECORD_LOG',LOG_DIR.'ws/wsDeleteMP4.log');
class wsClearDeadLine20170501{


	const SRC_CLEAR = 4;
	const JIETU_COMPLETE = 3;
	const OPT_JIETU = 3;
	const OPT_JIETU_COMPLETE = 1;
	const OPT_JIETU_CLEAR = 2;

	//const DEADLINE = '2017-05-01';
	const START_TIME = '2017-03-01';
	private  $flvdeadline = '';
	private  $mp4deadline = '';
	private  $db;
	private  $video;
	public static $deleteSize = 10;
	public static $flvupdate = '2017-05-23';


	public function __construct($flvdeadline=null,$mp4deadline=null)
	{
		if($flvdeadline)
			$this->flvdeadline = date('Y-m-d',strtotime($flvdeadline));
		else
			$this->flvdeadline = date('Y-m-d',strtotime('-1 month'));
		if($mp4deadline)
			$this->mp4deadline = date('Y-m-d',strtotime($mp4deadline));
		else
			$this->mp4deadline = date('Y-m-d',strtotime('-2 month'));

		$this->db = $this->getDB();
		$this->video = new Video();

		return true;
	}

	public function getDB(){
		return new DBHelperi_huanpeng();
	}
	public function getMergeRecords($db){

		$r = $db->field('liveid')->where('status='.self::JIETU_COMPLETE.' and ctime<\''.$this->flvdeadline.'\'' .' and ctime>\''.self::START_TIME.'\'')->limit(1)->select('video_merge_record');

		if(!isset($r[0]['liveid']))
		{
			return 0;
		}
		$liveid = $r[0]['liveid'];

		$sql = "update video_merge_record set status=".self::SRC_CLEAR." where liveid={$liveid}";
		mylog($sql,VIDEO_DEL_FLV_LOG);
		$res = $db->query($sql);
		if(!$db->affectedRows)
		{
			return 0;
		}
		return $liveid;
	}
	public function getMergeRecords2($db){

		$r = $db->field('liveid,id')->where('opt='.self::OPT_JIETU.' and status='.self::OPT_JIETU_COMPLETE.' and ctime<\''.$this->flvdeadline.'\'' .' and ctime>\''.self::START_TIME.'\'')->limit(1)->select('video_merge_record');

		if(!isset($r[0]['liveid']))
		{
			return 0;
		}
		$id = $r[0]['id'];
		$liveid = $r[0]['liveid'];

		$sql = "update video_merge_record set status=".self::OPT_JIETU_CLEAR." where id={$id}";
		mylog($sql,VIDEO_DEL_FLV_LOG);
		$res = $db->query($sql);
		if(!$db->affectedRows)
		{
			return 0;
		}
		return $liveid;
	}

	public function getDeleteFiles($liveid,$db){
		$r = $db->field('keys,ctime')->where("liveid={$liveid}")->select('live_VideoRecord');
		$flvs = [];
		if(!isset($r[0]['keys']))
		{
			return 0;
		}
		if(strtotime($r[0]['ctime'])>strtotime(self::$flvupdate))
		{
			$flvs = array_map(function($v){
				return $v['keys'];
			},$r);
			return $flvs;
		}
		if( !is_null(json_decode( $r[0]['keys'] )) )
		{
			$flvs = array_map(function($v){
				$flv = json_decode( $v['keys'],true );
				$flv = explode( ':', $flv[0] );
				$flv = $flv[1];
				return $flv;
			},$r);
			return $flvs;
		}
		else
		{
			$flvs = array_map(function($v){
				return $v['keys'];
			},$r);
			return $flvs;
		}

	}
	public function getDeleteFiles2($liveid,$db){
		$r = $db->field('keys')->where("liveid={$liveid}")->select('liveStreamRecord');
		if(!isset($r[0]['keys']))
		{
			return 0;
		}
		$flv = json_decode($r[0]['keys']);
		$flv = explode(':',$flv[0]);
		$flv = $flv[1];
		if(!$flv)
		{
			return 0;
		}
		//update date video_merge_recorde
		return $flv;

	}

	public function doDeleteFlv()
	{
		//$db = self::getDB();
		$db = $this->db;
		$files = [];
		for($i=0;$i<self::$deleteSize;$i++)
		{
			$liveid = $this->getMergeRecords2($db);
			//var_dump($liveid);
			if(!$liveid)
			{
				continue;
			}
			$flv = $this->getDeleteFiles($liveid,$db);
			//var_dump($flv);
			if(!count($flv))
			{
				continue;
				//todo error log
			}
			//mylog($flv,VIDEO_DEL_FLV_LOG);
			$files = array_merge($files,$flv);
		}
		//var_dump($files);
		if(!count($files))
		{
			return 0;
		}
		//todo delete
		//$Video = new Video();
		//do delete
		//var_dump($files);
		foreach ($files as $file){
			mylog($file,FLV_DELETE_RECORD_LOG);
		}
		$pid = $this->video->deleteFiles($files);
		mylog("$pid:".json_encode($files),VIDEO_DEL_FLV_LOG);
		return $pid;
	}

	public function doDeleteMP4($db=null)
	{
		if( !$db )
			$db = $this->db;
		$mp4s = [];
		for($i=0;$i<self::$deleteSize;$i++){
			$mp4 = $this->getUnpublishMP4($db);
			if( !$mp4 )
				continue;
			$mp4 = $mp4['vfile'];
			//mylog($mp4,VIDEO_DEL_MP4_LOG);
			$mp4s[] = $mp4;
		}
		//var_dump($mp4s);
		if(!count($mp4s))
			return 0;
		foreach ($mp4s as $file){
			mylog($file,MP4_DELETE_RECORD_LOG);
		}
		$pid = $this->video->deleteFiles($mp4s);
		mylog("$pid:".json_encode($mp4s),VIDEO_DEL_MP4_LOG);
		return $pid;
	}

	public function getUnpublishMP4($db)
	{
		$r = $db->field('liveid,videoid,vfile,status,ctime')->where( 'ctime<\''.$this->mp4deadline.'\'' .' and ctime>\''.self::START_TIME.'\'' . ' and status!='.VIDEO.' and status!='.VIDEO_CLEAR )->limit(1)->select('video');
		if(!isset($r[0]['videoid']))
		{
			return 0;
		}

		$videoid = $r[0]['videoid'];
		$sql = "update video set status=".VIDEO_CLEAR." where videoid={$videoid}";
		mylog($sql,VIDEO_DEL_MP4_LOG);
		$res = $db->query($sql);
		if(!$db->affectedRows)
		{
			mylog("update video $videoid failed\n",VIDEO_DEL_MP4_LOG);
			return 0;
		}
		//var_dump($r);
		return $r[0];
	}


}


/*******************main************************/
$Clear = new wsClearDeadLine20170501();
$pid = $Clear->doDeleteFlv();
if(!$pid)
{
	echo date('Y-m-d H:i:s')." delete flv failed or no task \n";
}
else
{
	echo date('Y-m-d H:i:s')." delete flv ok and pid is $pid\n";
}

$pid = $Clear->doDeleteMP4();
if(!$pid)
{
	echo date('Y-m-d H:i:s')." delete mp4 failed or no task \n";
}
else
{
	echo date('Y-m-d H:i:s')." delete mp4 ok and pid is $pid\n";
}



<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/5/22
 * Time: 14:58
 */

include ('/data/huanpeng/include/init.php');
use lib\WcsHelper;

class flvdelete{

	public static $deadline = '2017-04-01';
	public static $logfile  = LOG_DIR.'ws/outdeleteflv.log';
	//public static $startindex = 'bGl2ZXJlY29yZC1ZLTEwMTE5NS01NTk1MzA4LS0yMDE3MDMyMzA5MjkxOC5mbHY6MA==';
	public static $bucket = '6huanpeng-test001';


	public $limit = 1000;
	public $deletelimit = 100;
	public $prefix = '';
	public $marker = '';
	public $nextmarker = 'bGl2ZXJlY29yZC1ZLTEwMTE5NS01NTk1MzA4LS0yMDE3MDMyMzA5MjkxOC5mbHY6MA==';//'bGl2ZXJlY29yZC1ZLTQ5MTcyNy02ODk0MjM0LS0yMDE3MDYwNTEzNDUyOC5mbHY6MA==';
	public $mode = 0;

	public function __construct( $marker = null )
	{
		/*$this->limit = isset($_GET['limit'])?$_GET['limit']:'';
		$this->prefix = isset($_GET['prefix'])?$_GET['prefix']:'';
		$this->marker = isset($_GET['marker'])?$_GET['marker']:'';*/
	}
	public function getDeleteFlv()
	{
		$this->marker = $this->nextmarker;
		$WS = new WcsHelper();
		$r  = $WS->bucketList( self::$bucket, $this->limit, $this->prefix, $this->mode, $this->marker );
		//var_dump($r);
		$r      = json_decode( $r, true );
		$this->nextmarker = $r['marker'];
		var_dump($this->nextmarker);
		var_dump($r['items'][0]['key']);
		$list = [];
		foreach ( $r['items'] as $v )
		{
			$tmp['key']  = $v['key'];
			$tmp['time'] = date( 'Y-m-d', $v['putTime'] / 1000 );
			$list[] = $tmp;
		}
		return $list;
	}
	public function enableDelete($time)
	{
		return strtotime($time) < strtotime(self::$deadline);
	}

	public function preDoDeleteFlv()
	{
		$records = $this->getDeleteFlv();
		if(!count($records)){
			return ;
		}
		$flvs = [];
		foreach ($records as $v){
			if( $this->enableDelete($v['time']) ){
				if(strstr($v['key'],'liverecord'))
					$flvs[] = $v['key'];
				//mylog(json_encode($v),self::$logfile);
			}
		}
		$flvs = array_slice($flvs,0,$this->deletelimit);
		if(!count($flvs)){
			return ;
		}
		$video = new \lib\Video();
		$r = $video->deleteFiles($flvs);
		//var_dump($flvs);
		mylog( json_encode($flvs),self::$logfile);
		var_dump($flvs);
		var_dump($r);
	}

	public function getNextMarker()
	{
		return $this->nextmarker;
	}



}

$delete = new flvdelete();
//$delete->preDoDeleteFlv();

//exit;
while ($delete->getNextMarker())
{
	$delete->preDoDeleteFlv();
	sleep(1);
}

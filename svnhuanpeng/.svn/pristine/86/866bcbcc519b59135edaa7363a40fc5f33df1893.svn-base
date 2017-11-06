<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/8/16
 * Time: 13:16
 */

include( __DIR__ . '/../../include/init.php' );
use lib\Video;

class deleteflv{
	public $stime = '';
	public $etime = '';
	public $db = null;
	public $chunksize = 10;
	public $do = false;
	/**
	 * @return string
	 */

	public function __construct($stime,$etime,$do = false)
	{
		$this->stime = $stime;
		$this->etime = $etime;
		$this->do = $do;
		$this->db = new DBHelperi_huanpeng();
	}

	public function getflvs()
	{
		$results = $this->db->field('keys')
			->where("ctime between '{$this->stime}' and '{$this->etime}'")
		 	->select('live_VideoRecord');
		$datas = [];
		foreach ($results as $result){
			$v = $result['keys'];
			if(strpos($v,':')){
				$v = json_decode($v,true);
				$v = explode(':',$v[0]);
				$datas[] = $v[1];
			}else{
				$datas[] = $v;
			}

		}
		$datas = array_chunk($datas,$this->chunksize);
		return $datas;
	}
	public function delete()
	{
		$flvs = $this->getflvs();
		if(!$this->do){
			var_dump($flvs);
			return;
		}
		$video = new Video();
		foreach ($flvs as $flv){
			$r = $video->deleteFiles($flv);
			//var_dump($r . json_encode($flv));
			echo date('Y-m-d H:i:s') . $r . json_encode($flv) . "\n";
			sleep(1);
		}
	}
}

$stime = date('Y-m-d H:i:s',strtotime($argv[1]));
$etime = date('Y-m-d H:i:s',strtotime($argv[2]));
$do = (isset($argv[3])&&$argv[3]=='do')?true:false;
$delete = new deleteflv($stime,$etime,$do);
$delete->delete();
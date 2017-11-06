
<?php
/**
 * live stream status
 * v 1.0
 * date 2017-09-14
 */

include( __DIR__ . '/../../include/init.php' );
use lib\WcsHelper;
use system\RedisHelper;
use lib\live\LiveHelper;

class livestatus{

	protected $streams = [];
	static $redisname = 'huanpeng';
	/*static $liveheart = 'liveheart';
	static $expire = 3600;*/

	public function getstreams(){
		list($domain,$node) = explode('/', $GLOBALS['env-def'][$GLOBALS['env']]['stream-pub']);
		$streams = WcsHelper::getWsStreamInfoByApi($domain);
		$streams = $streams['content'];
		$streams = json_decode($streams,true);
		foreach ($streams['dataValue'] as $stream){
			$this->streams[] = basename($stream['streamname']);
		}
		return $this->streams;
	}
	/*public static function getredis(){
		return RedisHelper::getInstance(self::$redisname);
	}*/

	public function main(){

		//clear
		$streams = LiveHelper::getallstreamstatus();
		$curtime = time();
		foreach ($streams as $k => $v){
			$expire = $curtime - (int)$v;
			if($expire > \lib\live\Config::$heart_expire)
				LiveHelper::delstreamstatus($k);
		}
		//update
		$streamlist = $this->getstreams();
		\lib\live\LiveLog::livestatus(json_encode($streamlist));
		if(empty($streamlist))
			return false;
		foreach ($streamlist as $stream){
			LiveHelper::setstreamstatus($stream,time());
		}
	}
}

$v = new livestatus();
$v->main();

/*****************test*******************/

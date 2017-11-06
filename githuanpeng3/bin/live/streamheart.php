<?php
/**
 * stream heart
 * v 1.0
 * date 2017-09-25
 */
include( __DIR__ . '/../../include/init.php' );

use lib\live\LiveHelper;

class streamheart{

	const CMD = "/usr/bin/curl -i -s -w %{http_code}  -o /dev/null --connect-timeout 10 -m 10 ";
	static $file = "playlist.m3u8";

	public static function checkvalid($stream = ''){
		if(!$stream)
			return false;
		$stream = self::getvalidstream($stream);
		$cmd = self::CMD . "\"{$stream}\"";
		$result = `$cmd`;
		//404
		if(strstr($result, '404'))
			return false;
		//timeout
		if(strstr($result, '000'))
			;//todo log
		return true;
	}
	public static function getvalidstream($stream = ''){
		if(!$stream)
			return '';
		$st="liverecord/".$stream;
		$iparam = createHlsSecret($st);
		getLiveServerList($server, $socket);
		$file = self::$file;
		return "http://$server/$stream/$file?$iparam";
	}
}


$master = isset($argv[1])?$argv[1]:'';
if($master == 'master')
	$master = true;
elseif($master == 'slave')
	$master = false;
else
	exit;

$expire = 180;
$interval = 60;

$livefield = [
	'liveid',
	'uid',
	'gamename',
	'livetype',
	'status',
	'utime',
	'stream',
];

$streamfield = [
	'liveid',
	'status',
	'utime',
	'stream',
];


while (true){
	echo "check\n";
	//get create lives
	$lives = LiveHelper::getheartlive([LIVE],$livefield,$master);
	if(empty($lives)){
		continue;
	}
	$streamkey = array_map(function($v){
		return $v['stream'];
	},$lives);
	$streamlist = LiveHelper::getheartstream($streamkey,$streamfield,$master);
	unset($streamkey);
	$streams = [];
	foreach ($streamlist as $stream){
		$streams[$stream['liveid']] = $stream;
	}
	unset($streamlist);

	foreach ($lives as $live){
			$dtime = time() - strtotime($streams[$live['liveid']]['utime']);
			$streamstatus = $streams[$live['liveid']]['status'];
			//check L100 S100
			if($streamstatus == STREAM_START && $dtime > $expire){
				$streamname = $streams[$live['liveid']]['stream'];
				if( !streamheart::checkvalid($streamname) )
				{
					echo "stop stream $streamname  \n";
					//todo log
					\lib\live\LiveLog::processlog("stop stream $streamname ");
					LiveHelper::staticsetstream($streamname,['status'=>STREAM_DISCONNECT,'utime'=>date("Y-m-d H:i:s")],0,$master);
				}
			}
	}
	sleep($interval);
}





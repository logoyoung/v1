<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/7/10
 * Time: 11:28
 */
namespace lib\live;

class LiveLog{

	//日志根目录
	const LOG_DIR = '/data/logs/live/';
	//
	static $wslog = self::LOG_DIR . 'ws';
	//
	static $applog = self::LOG_DIR . 'app';
	//
	static $processlog = self::LOG_DIR . 'process';
	//
	static $liveheart = self::LOG_DIR . 'liveheart';
	//
	static $videoheart = self::LOG_DIR . 'videoheart';
	static $livestatus = self::LOG_DIR . 'livestatus';


	public static function islivedir($dir = null,$mode = 0777){

		if( is_dir( $dir ) ) return true;
		return mkdir( $dir, $mode, true );
	}

	public static function getlogname($filename){
		if( !self::islivedir( dirname( $filename ) ) ) return false;
		return $filename . '_' . date('Ymd') . '.log';
	}

	public static function wslog($msg){
		$filename = self::getlogname( self::$wslog );
		$msg = self::getmsg($msg);
		$r = file_put_contents($filename, $msg, FILE_APPEND);
		return $r;
	}
	public static function applog($msg){
		$filename = self::getlogname( self::$applog );
		$msg = self::getmsg($msg);
		$r = file_put_contents($filename, $msg, FILE_APPEND);
		return $r;
	}
	public static function processlog($msg){
		$filename = self::getlogname( self::$processlog );
		$msg = self::getmsg($msg);
		$r = file_put_contents($filename, $msg, FILE_APPEND);
		return $r;
	}

	public static function liveheart($msg){
		$filename = self::getlogname( self::$liveheart );
		$msg = self::getmsg($msg);
		$r = file_put_contents($filename, $msg, FILE_APPEND);
		return $r;
	}

	public static function videoheart($msg){
		$filename = self::getlogname( self::$videoheart );
		$msg = self::getmsg($msg);
		$r = file_put_contents($filename, $msg, FILE_APPEND);
		return $r;
	}
	public static function livestatus($msg){
		$filename = self::getlogname( self::$livestatus );
		$msg = self::getmsg($msg);
		$r = file_put_contents($filename, $msg, FILE_APPEND);
		return $r;
	}
	public static function getmsg($msg){
		return '[' . getmypid() . '] [' . date('Y-m-d H:i:s') . '] ' . $msg . ' at ' . $_SERVER['SCRIPT_FILENAME'] . "\n";
	}

}
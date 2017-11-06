<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/7/6
 * Time: 15:18
 */

namespace bin\live;

use lib\WcsHelper;
use lib\CDNHelper;
use lib\LiveRoom;
use lib\Video;
use lib\SiteMsgBiz;
use lib\MsgPackage;

class checklive{

	/*
	 * timeout
	 */
	//
	const LIVE_TIMEOUT = 180;
	//doflvtimeout()
	const FLV_TIMEOUT = 3600;
	//
	const VIDEO_TIMEOUT = 3600*6;
	//
	const POSTER_TIMEOUT = 600;

	/*
	 * live status
	 */
	//
	const LIVE_CREATE = 0;
	//
	const LIVE = 0;
	//
	const LIVE_STOP = 110;
	//
	const FLV = 120;
	//
	const VIDEO = 130;
	//
	const POSTER = 140;
	//
	const COMPLETE = 200;
	//
	const VIDEO_FAILED = 210;
	//
	const POSTER_FAILED = 220;
	//
	const LIVE_TIMEOUT_STOP = 230;
	//
	static $db = null;
	//
	static $redis = null;

	function __construct( ){

	}

	function getdbinstance(){
		//return new \DBHelperi_huanpeng();
	}

	function getredisinstance(){
		//return new \RedisHelp();
	}

	function getids(){
		//todo
		return ;
	}

	function gettype($task){
		//todo
		return;
	}

	//L0,S0 => L230,S210
	function dostreamcreatecallbacktimeout(){

	}
	//L0,S100 => L100,S100
	function dolivestatuserror(){

	}
	//L0,S>=200 => L230,S>=200
	function dostreamcallbackerror(){

	}

	//L100，S>=200 => L110,S>=200
	function dostreamconnecttimeout(){

	}
	//L100,S0 => L110,S210
	function dostreamstartcallbacktimeout(){

	}
	//L100 S100
	function dolivecheck(){

	}
	//L110 => L120
	function doflvtimeout(){

	}
	//L120 => L210
	function dovideotimeou(){

	}
	//L130 => L220
	function dopostertimeout(){

	}
	//
	function exec( $task ){
		$type = $this->gettype($task);
		switch ( $type ){
			case 'L0S0':
				$this->dostreamcreatecallbacktimeout();
				break;
			case 'L0S100':
				$this->dolivestatuserror();
				break;
			case 'L0S200':
				$this->dostreamcallbackerror();
				break;
			case 'L100S200':
				$this->dostreamconnecttimeout();
				break;
			case 'L100S0':
				$this->dostreamstartcallbacktimeout();
				break;
			/*case 'L100S100':
				$this->dolivecheck();
				break;*/

			case 'L110':
				$this->doflvtimeout();
				break;
			case 'L120':
				//todo
				break;
			case 'L130':
				//todo
				break;
			default:
				//todo
				break;

		}
	}

}
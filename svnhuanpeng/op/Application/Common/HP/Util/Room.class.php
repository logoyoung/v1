<?php

// +----------------------------------------------------------------------
// 房间类
// +----------------------------------------------------------------------

namespace HP\Util;
use HP\Log\Log;

class Room
{
    static public  function getoneid($uid){
        $dao = D('roomid');
        $roomid = $dao->max('roomid');
        $roomid = $roomid + 1;
		return self::newRoomid( $uid,$roomid );
//    	return self::checkRoomId($roomid);
    }
    
//    static public  function checkRoomId( $roomid )
//    {
//        if( empty( $roomid ) )
//        {
//            return false;
//        }
//        $date = file_get_contents( './roomid.txt' );
//        if( strstr( $date, "$roomid" ) )
//        {
//            $roomid++;
//            return self::checkRoomId( "$roomid" );
//        }
//        else
//        {
//            return $roomid;
//        }
//    }


	public static function newRoomid($uid, $roomid )
	{
		$rule = array( 111, 222, 333, 444, 555, 666, 777, 888, 999, 1314, 520, 521, 1314, 1212, 2323, 3434, 4545, 5656, 7878, 8989, 9898, 8787, 7676, 6565, 5454, 4343, 3232, 2121, 1122, 2233, 3344, 4455, 5566, 6677, 7788, 8899, 9988, 8877, 7766, 6655, 5544, 4433, 3322, 2211 );

		$checkResult = true;
		$str = '';
		while (false !== $checkResult){
			if(strlen(str_replace($rule, array($str), "$roomid")) == strlen("$roomid")) {
				$checkResult = false;
				Log::statis($uid.'==='.$roomid,'','roomid.new');

			} else {
				Log::statis($uid.'==='.$roomid,'','roomid.nice');
				$roomid++;
			}
		}
		return $roomid;
	}
}

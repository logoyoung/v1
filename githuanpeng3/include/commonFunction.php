<?php

/*
 * 封装一些常用的公用的方法
 * date 2015-12-18 10:52 Am
 * author yandong@6rooms.com
 * copyright@6.cn
 */

/**
 * 过滤接受的参数或者数组,如$_GET,$_POST
 * date 2015-12-08
 * author yandong@6rooms.com
 *
 * @param array|string $arr 接受的参数或者数组
 *
 * @return array|string
 */
function filterData( $arr )
{
	if ( is_array( $arr ) )
	{
		foreach ( $arr as $k => $v )
		{
			$arr[$k] = filterWords( $v );
		}
	}
	else
	{
		$arr = filterWords( $arr );
	}

	return $arr;
}

/**
 * 参数过滤
 * date 2015-12-10
 * author yandong@6rooms.com
 *
 * @param string $str 接受的参数
 *
 * @return string
 */
function filterWords( $str )
{
	$farr = array(
		"/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
		"/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
		"/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is"
	);
	$str  = htmlspecialchars( trim( $str ) );
	$str  = preg_replace( $farr, '', $str );

	return $str;
}

/**
 * 验证密码是否符合长度
 *
 * @param string $password
 *
 * @return boolean
 */
function checkPasswordLeng( $password )
{
	//密码中是否包含中文
	preg_match( '/[\x{4e00}-\x{9fa5}]+/u', $password, $matches_c );
	if ( $matches_c )
	{
		return false;
	}
	if ( mb_strlen( $password, 'utf-8' ) < 6 || mb_strlen( $password, 'utf-8' ) > 12 )
	{
		return false;
	}

	return true;
}

/**
 * 转化成json格式的数据
 * date 2015-12-08
 * author yandong@6rooms.com
 *
 * @param string|array $data
 *
 * @return json
 */
function jsone( $data )
{
	return json_encode( $data );
}

/**
 * curl模拟post发起请求
 *
 * @param array  $data 请求参数数组
 * @param string $url  目标url
 */
function curl_post( $data, $url, $method = 'POST' )
{
	$ch = curl_init();
	if ( substr( $url, 0, 5 ) == 'https' )
	{
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
	}
	if ( $method == 'GET' )
	{
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		$params = http_build_query( $data );
		$url    = $url . '?' . $params;
	}
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	if ( $method == 'POST' )
	{
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	}
	curl_setopt( $ch, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
	$data = curl_exec( $ch );
	curl_close( $ch );

	return $data;
}

/**
 * 转化json数据为数组或对象
 * date 2015-12-08
 * author yandong@6rooms.com
 *
 * @param json $data
 * @param bool $assoc (false时返回的是对象,true时返回的是数组,默认false)
 *
 * @return array|object
 */
function jsond( $data, $assoc = false )
{
	return json_decode( $data, $assoc );
}

/**纪录主播每场直播的人气
 *
 * @param int $uid     主播id
 * @param int $liveid  直播ID
 * @param int $popular 人气值
 * @param     $db
 *
 * @return bool
 */
function setMostPopual( $uid, $liveid, $popular, $db )
{
	if ( empty( $uid ) || empty( $liveid ) )
	{
		return false;
	}
	$data = array(
		'uid'     => $uid,
		'liveid'  => $liveid,
		'popular' => $popular
	);
	$res  = $db->insert( 'anchor_most_popular', $data );
	if ( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * 用户密码加密
 * date 2015-12-11
 * author yandong@6rooms.com
 *
 * @param srting $passwod
 *
 * @return string
 */
function md5password( $passwod )
{
	return md5( md5( md5( $passwod ) ) );
}

/**
 * 检测用户登录状态
 *
 * @param type $uid
 * @param type $encpass
 * @param type $db
 *
 * @return boolean|string
 */
function CheckUserIsLogIn( $uid, $encpass, $db )
{
	$row = $db->field( 'encpass' )->where( 'uid =' . $uid . '' )->limit( 1 )->select( 'userstatic' );
	if ( empty( $row ) )
	{
		return -1014;
	}
	if ( $row[0]['encpass'] != $encpass )
	{
		return -1013;
	}

	return true;
}

/**
 * 获取用户信息
 *
 * @param int    $uid
 * @param object $db
 *
 * @return array
 */
function getUserInfo( $uid, $db )
{
	if ( is_array( $uid ) )
	{
		$uid = implode( ',', $uid );
		$res = $db->field( 'uid,nick,pic,sex' )->where( "uid in($uid)" )->select( 'userstatic' );
		if ( $res )
		{
			foreach ( $res as $v )
			{
				$row[$v['uid']] = $v;
			}
		}
	}
	else
	{
		$row = $db->field( 'uid,nick,pic' )->where( 'uid=' . $uid . '' )->select( 'userstatic' );
	}

	return $row ? $row : array();
}

/**
 * 判断主播是否在线[可批量]
 */
function getAnchorIsOnLine( $uid, $db )
{
	if ( is_array( $uid ) )
	{
		$uid = implode( ',', $uid );
		$res = $db->field( 'uid' )->where( "uid in($uid) and status=" . LIVE )->select( 'live' );
		if ( $res )
		{
			foreach ( $res as $v )
			{
				$row[$v['uid']] = $v['uid'];
			}
		}
		else
		{
			$row = array();
		}
	}
	else
	{
		$row = $db->field( 'uid' )->where( "uid= $uid and status=" . LIVE )->select( 'live' );
	}

	return $row ? $row : array();
}

/**
 * 判断是不是主播
 */
function checkUserIsAnchor( $uid, $db )
{
	if ( is_array( $uid ) )
	{
		$uid = implode( ',', $uid );
		$res = $db->field( 'uid' )->where( "uid in ($uid)" )->select( 'anchor' );
		if ( $res )
		{
			foreach ( $res as $v )
			{
				$row[$v['uid']] = $v['uid'];
			}
		}
		else
		{
			$row = array();
		}
	}
	else
	{
		$row = $db->field( 'uid' )->where( "uid=$uid" )->select( 'anchor' );
	}

	return $row ? $row : array();
}

/**
 * 获取用户等级
 *
 * @param int    $uid
 * @param object $db
 *
 * @return string
 */
function getUserLevelByUid( $uid, $db )
{
	$row = $db->field( 'level' )->where( 'uid=' . $uid . '' )->select( 'useractive' );
	if ( empty( $row ) )
	{
		return -998;
	}

	return $row ? $row[0]['level'] : '';
}

/**
 * 批量获取用户昵称
 * date  2015-12-14
 *
 * @param array  $uids
 * @param object $db
 *
 * @return array
 */
function getUserNicks( $uids, $db )
{
	$s   = implode( ',', $uids );
	$ret = array();
	$res = $db->field( 'uid,nick' )->where( 'uid in (' . $s . ')' )->select( 'userstatic' );
	foreach ( $res as $key => $val )
	{
		$ret[$val['uid']] = $val['nick'];
	}

	return $ret;
}

/**
 * 获取主播等级
 *
 * @param type $uid 主播
 * @param type $db
 *
 * @return boolean
 */
function getAnchorLevel( $uid, $db )
{
	if ( empty( $uid ) )
	{
		return false;
	}
	$res = $db->field( 'level' )->where( "uid=$uid" )->select( 'anchor' );

	return $res ? $res[0]['level'] : '0';
}

//获取房间号
function getRoomIdByUid( $uid, $db )
{
	if ( empty( $uid ) )
	{
		return false;
	}
	$res = $db->field( 'uid,roomid' )->where( "uid in ($uid)" )->select( 'roomid' );
	if ( false !== $res && !empty( $res ) )
	{
		$list = array();
		foreach ( $res as $v )
		{
			$list[$v['uid']] = $v['roomid'];
		}

		return $list;
	}
	else
	{
		return array();
	}
}

/**
 * 获取直播在线用户数量
 *
 * @param int    $luid
 * @param object $db
 *
 * @return string
 */
function getLiveRoomUserCount( $luid, $db )
{
	$row = $db->field( 'count(*) as count' )->where( "luid= $luid  and uid != $luid  and uid < " . LIVEROOM_ANONYMOUS )->select( 'liveroom' );

	return $row[0]['count'] ? $row[0]['count'] : '0';
}

function getMoreLiveRoomUserCount( $luid, $db )
{
	$row = $db->field( 'luid,count(*) as count' )->where( "luid in ($luid)  and uid not in($luid)  and uid < " . LIVEROOM_ANONYMOUS )->select( 'liveroom' );
	if ( false !== $row && !empty( $row ) )
	{
		foreach ( $row as $v )
		{
			$temp[$v['luid']] = $v['count'];
		}

		return $temp;
	}
	else
	{
		return array();
	}
}

/**
 * 获取主播开播时间
 *
 * @param int  $uid
 * @param      $liveId
 * @param type $db
 *
 * @return type
 */
function getLiveLongTime( $liveId, $db )
{
	$res = $db->field( 'uid,ctime,stime,etime' )->where( "liveid=$liveId  and  status >" . LIVE )->limit( 1 )->select( 'live' );

	return $res ? $res : array();
}

/**检测该场直播是否超时
 *
 * @param $liveid
 *
 * @return bool
 */
function checkLiveOverTime( $liveid, $db )
{
	if ( empty( $liveid ) )
	{
		return false;
	}
	$res = $db->where( "liveid=$liveid  and stype=1 " )->limit( 1 )->select( 'videosave_queue' );
	if ( false !== $res )
	{
		if ( !empty( $res ) )
		{
			return $res;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

/**
 * 获取直播在线用户列表
 *
 * @param int    $luid
 * @param object $db
 *
 * @return string
 */
function getLiveUserByLuid( $luid, $db )
{
	$ids = array();
	$row = $db->field( 'uid' )->where( 'luid=' . $luid . '' )->order( 'tm DESC' )->select( 'liveroom' );
	if ( $row )
	{
		foreach ( $row as $v )
		{
			$ids[] = $v['uid'];
		}
	}

	return $ids;
}

/**
 * 获取用户关注的主播
 *
 * @param int    $uid
 * @param object $db
 *
 * @return array
 */
function userFollow( $uid, $db )
{
	$rows = $db->field( 'uid2' )->where( 'uid1 =' . $uid . '' )->order( 'tm desc' )->select( 'userfollow' );

	return $rows;
}

/**
 * 获取收藏数量
 *
 * @param type $videoid
 * @param type $db
 *
 * @return type
 */
function getVideoCount( $videoid, $db )
{
	$num = $db->field( 'count(*) as count' )->where( 'videoid=' . $videoid . '' )->select( 'videofollow' );

	return $num[0]['count'];
}

/**
 * 批量获取录像的收藏数量
 *
 * @param type $videoid array();
 * @param type $db
 *
 * @return type
 */
function getVideoCountByVideoId( $videoid, $db )
{
	$res = array();
	$num = $db->field( 'videoid,count(*) as count' )->where( "videoid in($videoid) group by videoid " )->select( 'videofollow' );
	foreach ( $num as $v )
	{
		$res[$v['videoid']] = $v['count'];
	}

	return $res;
}

/**
 * 判断是否收藏
 *
 * @param type $uid
 * @param type $videoid
 * @param type $db
 *
 * @return type
 */
function getVideoIsCollect( $uid, $videoid, $db )
{
	$num = $db->field( 'count(*) as count' )->where( "uid=$uid and videoid=$videoid" )->select( 'videofollow' );

	return $num[0]['count'] ? $num[0]['count'] : '0';
}

/**
 * 获取粉丝数量
 *
 * @param int    $uid2
 * @param object $db
 *
 * @return string
 */
function getFansCount( $uid2, $db )
{
	$fans = $db->field( 'count(*) as fans' )->where( 'uid2=' . $uid2 . '' )->select( 'userfollow' );

	return $fans[0]['fans'] ? $fans[0]['fans'] : 0;
}

/**
 * 获取游戏类型名称
 *
 * @param int    $gametid
 * @param object $db
 *
 * @return string
 */
function getGameTypeName( $gametid, $db )
{
	$gtname = '';
	$name   = $db->field( 'name' )->where( 'gametid=' . $gametid . '' )->limit( '1' )->select( 'gametype' );
	if ( $name )
	{
		$gtname = $name[0]['name'];
	}

	return $gtname;
}

/**
 * 批量获取游戏名称
 *
 * @param type $gameids
 * @param type $db
 *
 * @return type
 */
function getMoreGameName( $gameids, $db )
{
	if ( empty( $gameids ) )
	{
		return false;
	}
	$gamename = $db->field( 'gameid,name' )->where( "gameid in ($gameids)" )->select( 'game' );
	foreach ( $gamename as $v )
	{
		$gname[$v['gameid']] = $v['name'];
	}

	return $gname ? $gname : '';
}

/**
 * 获取观众数量
 *
 * @param int    $liveId
 * @param object $db
 *
 * @return string
 */
function getViewerCount( $uid, $db )
{
	$count = $db->field( 'count(*) as count' )->where( 'luid=' . $uid . '' )->select( 'liveroom' );

	return $count[0]['count'] ? $count[0]['count'] : '0';
}

/**
 * 获取评分
 *
 * @param type $videoid
 * @param type $db
 *
 * @return type
 */
function getVideoRate( $videoid, $db )
{
	$rate = $db->field( 'avg(rate) as score' )->where( 'videoid=' . $videoid . '' )->select( 'videocomment' );

	return ( $rate[0]['score'] ) ? $rate[0]['score'] : '';
}

/**
 * 批量获取录像评论总数
 */
function getVideoCommentCountByVideoId( $videoid, $db )
{
	if ( empty( $videoid ) )
	{
		return false;
	}
	if ( is_array( $videoid ) )
	{
		$videoid = implode( ',', $videoid );
		$res     = $db->field( "videoid, count(*) as count" )->where( "videoid in($videoid) group by videoid" )->select( 'videocomment' );
	}
	else
	{
		$res = $db->field( "videoid, count(*) as count" )->where( "videoid=$videoid" )->select( 'videocomment' );
	}
	if ( $res )
	{
		foreach ( $res as $v )
		{
			$comment[$v['videoid']] = $v['count'];
		}
	}
	else
	{
		$comment = array();
	}

	return $comment;
}

/**
 * 是否关注
 *
 * @param int    $uid1
 * @param int    $uid2
 * @param object $db
 *
 * @return array
 */
function isOneFollowOne( $uid1, $uid2, $db )
{
	$res = $db->where( 'uid1=' . $uid1 . ' and uid2=' . $uid2 . '' )->limit( 1 )->select( 'userfollow' );

	return $res;
}

/**
 * 根据游戏id获取游戏名称
 *
 * @param int    $gameId
 * @param object $db
 *
 * @return array
 */
function getGameNameByGameId( $gameId, $db )
{
	$name = $db->field( 'name,gametid' )->where( 'gameid=' . $gameId . '' )->limit( 1 )->select( 'game' );

	return $name;
}

/**
 * 批量获取多个主播的观众数量
 *
 * @param array  $luid
 * @param object $db
 *
 * @return type
 */
function batchGetLiveRoomUserCount( $luid, $db )
{
	$rows = array();
	$row  = $db->field( 'luid,count(*) as count' )->where( 'luid in (' . $luid . ') group by luid' )->select( 'liveroom' );
	foreach ( $row as $rv )
	{
		$rows[$rv['luid']] = $rv['count'];
	}

	return $rows;
}

/**
 * 批量获取多个主播的粉丝数量
 *
 * @param type $uid2
 * @param type $db
 *
 * @return type
 */
function batchGetFansCount( $uid2, $db )
{
	$fan = $db->field( 'uid2,count(*) as fans' )->where( 'uid2 in (' . $uid2 . ') group by uid2' )->select( 'userfollow' );
	if ( $fan )
	{
		foreach ( $fan as $fv )
		{
			$fans[$fv['uid2']] = $fv['fans'];
		}
	}
	else
	{
		$fans = array();
	}

	return $fans;
}

/**
 * 验证身份证
 * author yandong@6rooms.com
 * date 2016-1-12 12
 *
 * @param string $idcard
 *
 * @return boolean
 */
function checkIDCard( $idcard )
{
	if ( strlen( $idcard ) != 18 )
	{
		return false;
	}
	// 取出本体码
	$idcard_base = substr( $idcard, 0, 17 );
	// 取出校验码
	$verify_code = substr( $idcard, 17, 1 );
	// 加权因子
	$factor = array( 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 );
	// 校验码对应值
	$verify_code_list = array( '1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2' );
	// 根据前17位计算校验码
	$total = 0;
	for ( $i = 0; $i < 17; $i++ )
	{
		$total += substr( $idcard_base, $i, 1 ) * $factor[$i];
	}
	// 取模
	$mod = $total % 11;
	// 比较校验码
	if ( $verify_code == $verify_code_list[$mod] )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * 检查目标用户是否存在
 *
 * @param int    $targetUserID
 * @param object $db
 *
 * @return array
 */
function checkUserIsExist( $targetUserID, $db )
{
	$res = $db->field( 'uid' )->where( "uid = $targetUserID" )->select( 'userstatic' );

	return $res;
}

/**
 * 获取昨天开始结束时间
 * author yandong@6rooms.com
 * date 2016-2-1 10:45
 */
function getYesterdayTime()
{
	$beginYesterday = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, date( 'm' ), date( 'd' ) - 1, date( 'Y' ) ) );
	$endYesterday   = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) ) - 1 );

	return $time = array( 'begin' => $beginYesterday, 'end' => $endYesterday );
}

/**
 * 获取今天开始结束时间
 * author yandong@6rooms.com
 * date 2016-2-1 10:45
 */
function getTodayTime()
{
	$beginToday = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) ) );
	$endToday   = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, date( 'm' ), date( 'd' ) + 1, date( 'Y' ) ) - 1 );

	return $time = array( 'begin' => $beginToday, 'end' => $endToday );
}

/**
 * 防止直接通过url来访问
 * 这个职能针对小白用户,HTTP_REFERER完全可以伪造
 */
function checkRequesttFrom()
{
	$fromurl = "http://www.huanpeng.com"; //跳转往这个地址
	if ( $_SERVER['HTTP_REFERER'] == "" )
	{
		header( "Location:" . $fromurl );
		exit;
	}
}

/**
 * 获取直播流地址
 *
 * @param type $streamServer
 * @param type $notifyServer
 *
 * @return boolean
 */
function getLiveServerList( &$streamServer, &$notifyServer )
{
	$conf         = $GLOBALS['env-def'][$GLOBALS['env']];
	$streamServer = $conf['stream-watch'];
	$notifyServer = $conf['stream-stop-notify'];

	/* if ($GLOBALS['env'] == 'DEV')
      {
      $streamServer = '223.203.212.30:8080';
      $notifyServer = 'http://223.203.212.30:9300/r?s=';
      }
      else
      {
      //TODO
      } */

	return true;
}

/**
 * 二维数组排序
 *
 * @param array  $array
 * @param string $sort_key
 * @param string $sort
 *
 * @return array
 */
function dyadicArray( $array, $sort_key, $sort = SORT_DESC )
{
	if ( is_array( $array ) )
	{
		foreach ( $array as $row_array )
		{
			if ( is_array( $row_array ) )
			{
				$key_array[] = $row_array[$sort_key];
			}
			else
			{
				return false;
			}
		}
	}
	else
	{
		return false;
	}
	array_multisort( $key_array, $sort, $array );

	return $array;
}

/**
 * 二维数组排序多重条件排序
 *
 * @param array  $array
 * @param string $sort_key
 * @param string $sort
 *
 * @return array
 */
function twoKeyOrder( $array, $keyone, $sort = SORT_DESC, $keytwo, $tsort = SORT_DESC )
{
	if ( is_array( $array ) )
	{
		foreach ( $array as $row_array )
		{
			if ( is_array( $row_array ) )
			{
				$key_array[]  = $row_array[$keyone];
				$key_array2[] = $row_array[$keytwo];
			}
			else
			{
				return false;
			}
		}
	}
	else
	{
		return false;
	}
	array_multisort( $key_array, $sort, $key_array2, $tsort, $array );

	return $array;
}


/**
 * 计算时间差
 *
 * @param type $begin_time
 * @param type $end_time
 *
 * @return type
 */
function timediff( $begin_time, $end_time )
{
	if ( $begin_time < $end_time )
	{
		$starttime = $begin_time;
		$endtime   = $end_time;
	}
	else
	{
		$starttime = $end_time;
		$endtime   = $begin_time;
	}
	$timediff = $endtime - $starttime;
	$days     = intval( $timediff / 86400 );
	$remain   = $timediff % 86400;
	$hours    = intval( $remain / 3600 );
	$remain   = $remain % 3600;
	$mins     = intval( $remain / 60 );
	$secs     = $remain % 60;
	$res      = array( "days" => $days ? $days : '0', "hour" => $hours ? $hours : '0', "min" => $mins ? $mins : '0', "sec" => $secs ? $secs : '0', 'diff' => $timediff ? $timediff : 0 );

	return $res;
}


/**
 * 把秒数格式化
 *
 * @param 秒数 $second
 *
 * @return boolean|string
 */
function SecondFormat( $second )
{
	if ( empty( $second ) )
	{
		return false;
	}
	$str = '';
	$d   = floor( $second / 3600 / 24 );
	$h   = floor( ( $second % ( 3600 * 24 ) ) / 3600 );  //%取余
	$m   = floor( ( $second % ( 3600 * 24 ) ) % 3600 / 60 );
	$s   = floor( ( $second % ( 3600 * 24 ) ) % 60 );
	if ( !empty( $d ) )
	{
		$str .= $d . '天';
	}
	if ( !empty( $h ) )
	{
		$str .= $h . '时';
	}
	if ( !empty( $m ) )
	{
		$str .= $m . '分';
	}
	if ( !empty( $s ) )
	{
		$str .= $s . '秒';
	}

	return $str;
}

/**
 * 字符串定长截取
 *
 * @param string $str
 * @param int    $len
 * @param bool   $suffix
 *
 * @return string
 */
function strCut( $str, $len, $suffix = true )
{
	if ( mb_strlen( $str, 'utf8' ) > $len )
	{
		if ( $suffix )
		{
			return mb_substr( $str, 0, $len, 'utf8' ) . '....';
		}
		else
		{
			return mb_substr( $str, 0, $len, 'utf8' );
		}
	}

	return $str;
}

/**
 * 添加一条新的站内消息
 *
 * @param string $title
 * @param string $message
 * @param object $db
 *
 * @return string
 */
function addMessagesText( $title, $message, $type, $group = '', $sendid = 0, $db )
{
	$data = array(
		'title'  => $title,
		'msg'    => $message,
		'type'   => $type,
		'group'  => $group,
		'sendid' => $sendid
	);
	$res  = $db->insert( 'sysmessage', $data );

	return $res;
}

/**
 * 添加一条用户消息
 *
 * @param type $uid
 * @param type $msgid
 * @param type $db
 *
 * @return type
 */
function addUserMessages( $uid, $msgid, $db )
{
	$data = array(
		'uid'   => $uid,
		'msgid' => $msgid
	);
	$res  = $db->insert( 'usermessage', $data );

	return $res;
}

/**
 * 更改用户站内信数量
 *
 * @param int    $uid
 * @param object $db
 *
 * @return bool
 */
function updateUserMailStatus( $uid, $db )
{
	$sql = "update useractive set readsign=readsign+1 where uid=$uid";
	$res = $db->doSql( $sql );

	return $res;
}

/**
 * 发送消息
 *
 * @param type $sendId
 * @param type $title
 * @param type $message
 * @param type $type
 * @param type $db
 *
 * @return int 成功返回1 失败返回0
 */
function sendMessages( $sendId, $title, $message, $type, $db )
{
	if ( empty( $sendId ) || empty( $title ) || empty( $message ) )
	{
		return false;
	}
	if ( !in_array( $type, array( 0, 2 ) ) )
	{
		return false;
	}
	//一对一
	if ( $type == 0 )
	{
		$addMsgRes = addMessagesText( $title, $message, $type, $group = 1, $sendid = 0, $db );
		$adduseRes = addUserMessages( $sendId, $addMsgRes, $db );
		if ( $adduseRes )
		{
			$res = updateUserMailStatus( $sendId, $db );
		}
		else
		{
			$res = false;
		}
	}
	//一对多
	if ( $type == 2 )
	{
		$res = addMessagesText( $title, $message, $type, $group = 2, $sendid = 0, $db );
	}
	if ( $res !== false )
	{
		$back = 1;
	}
	else
	{
		$back = 0;
	}

	return $back;
}

/**
 * 根据uid获取直播信息
 *
 * @param type $uid 用户uid
 * @param type $db
 *
 * @return array()
 */
function getLiveInfoByUid( $liveid, $db )
{
	if ( empty( $liveid ) )
	{
		return false;
	}
	$res = $db->field( 'uid,gametid,gameid,gamename,title,poster,ip,port,orientation,antopublish' )->where( "liveid=$liveid" )->limit( 1 )->select( 'live' );

	return $res ? $res : array();
}

/**
 * 获取主播可发布的录像数
 *
 * @param type $uid 主播id
 * @param type $db
 *
 * @return type
 */
function getAuchorVideoLimit( $uid, $db )
{
	$res = $db->field( 'videolimit' )->where( "uid=$uid" )->select( 'anchor' );

	return $res ? $res[0]['videolimit'] : 0;
}

/**
 * 获取主播审核中&&已发布的录像[审核中&&已发布]
 *
 * @param type $uid 主播id
 * @param type $db
 *
 * @return type
 */
function getAnchorAlreadyPublishVideo( $uid, $db )
{
	$res = $db->field( 'count(*) as pub' )->where( "uid=$uid and status in (" . VIDEO_UNPUBLISH . ',' . VIDEO . ")" )->select( 'video' );

	return $res ? $res[0]['pub'] : 0;
}

/**
 * 获取已发布录像个数
 *
 * @param type $uid
 * @param type $db
 *
 * @return type
 */
function getAnchorPublishVideo( $uid, $db )
{
	$res = $db->field( 'count(*) as pub' )->where( "uid=$uid and status=" . VIDEO )->select( 'video' );

	return $res ? $res[0]['pub'] : 0;
}

/**
 * 获取审核中录像个数
 *
 * @param type $uid
 * @param type $db
 *
 * @return type
 */
function getAnchorCheckVideo( $uid, $db )
{
	$res = $db->field( 'count(*) as pub' )->where( "uid=$uid and status=" . VIDEO_UNPUBLISH )->select( 'video' );

	return $res ? $res[0]['pub'] : 0;
}

/**
 * 获取主播未发布的录像数
 *
 * @param type $uid 主播id
 * @param type $db
 *
 * @return type
 */
function getAnchorNoPublishVideo( $uid, $db )
{
	$res = $db->field( 'count(*) as pub' )->where( "uid=$uid and status=" . VIDEO_WAIT )->select( 'video' );

	return $res ? $res[0]['pub'] : 0;
}

/**
 * 添加发消息
 *
 * @param type $uid  用户id
 * @param type $luid 主播id
 * @param type $db
 *
 * @return boolean
 */
function addLiveNotice( $uid, $luid, $db )
{
	if ( empty( $uid ) || empty( $luid ) )
	{
		return false;
	}
	$data = array(
		'uid'  => $uid,
		'luid' => $luid
	);
	$res  = $db->insert( 'live_notice', $data );
	if ( $res !== false )
	{
		$res = true;
	}
	else
	{
		$res = false;
	}

	return $res;
}

/**
 * 删除直播通知
 *
 * @param type $uid  用户id
 * @param type $luid 主播id
 * @param type $db
 *
 * @return bool
 */
function deleteLiveNotice( $uid, $luid, $db )
{
	$res = $db->where( "uid=$uid  and luid in ($luid)" )->delete( 'live_notice' );

	return $res;
}

/**
 * 检测用户是否开启直播通知
 *
 * @param type $uid 用户id
 * @param type $db
 */
function checkUserIsOpenLiveNotice( $uid, $db )
{
	if ( empty( $uid ) )
	{
		return false;
	}
	$res = $db->field( 'isnotice' )->where( 'uid=' . $uid )->select( 'useractive' );

	return $res;
}

/**
 *  获取直播通知列表[App端]
 *
 * @param type $uid  用户id
 * @param type $luid 主播id
 * @param type $db
 *
 * @return array
 */
function getLiveNoticeAnchor( $uid, $luid, $db )
{
	$rows = $db->field( 'luid' )->where( "uid=$uid and luid in ($luid)" )->select( 'live_notice' );
	if ( $rows !== false )
	{
		$res = array_column( $rows, 'luid' );
	}
	else
	{
		$res = array();
	}

	return $res;
}

function checkUersIsExist( $uid, $db )
{
	$res = $db->where( "uid=$uid" )->limit( 1 )->select( 'userstatic' );
	if ( false !== $res && !empty( $res ) )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * 同步任务
 *
 * @param type $uid    用户id
 * @param type $taskid 任务id
 * @param type $bean   获得金豆数
 * @param type $db
 *
 * @return boolean
 */
function synchroTask( $uid, $taskid, $type, $bean, $db )
{
	if ( empty( $uid ) || empty( $taskid ) )
	{
		return false;
	}
	$isExist = checkUersIsExist( $uid, $db );
	if ( !$isExist )
	{
		return false;
	}
	$data = array(
		'uid'     => $uid,
		'taskid'  => $taskid,
		'status'  => TASK_FINISHED,
		'type'    => $type,
		'getbean' => $bean
	);
	$res  = $db->insert( 'task', $data );
	if ( $res !== false )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function getInfoFromGameZone( $gameIds, $db )
{
	$res = $db->where( "gameid in ($gameIds)" )->select( 'game_zone' );
	if ( $res )
	{
		foreach ( $res as $v )
		{
			$info[$v['gameid']] = $v;
		}
	}
	else
	{
		$info = array();
	}

	return $info;
}

/**
 * 获取用户地址
 *
 * @param $uid 用户id
 * @param $db
 *
 * @return bool
 */
function getUserAddress( $uid, $db )
{
	if ( empty( $uid ) )
	{
		return false;
	}
	$res = $db->field( 'province,city,address' )->where( "uid =$uid" )->select( 'useractive' );
	if ( false !== $res )
	{
		return $res;
	}
	else
	{
		return false;
	}

}

/**
 * 获取本周开始结束时间
 * @return array
 */
function ThisWeekStartEnd()
{
	$date      = date( "Y-m-d" );  //当前日期
	$first     = 1; //$first =1 表示每周星期一为开始时间 0表示每周日为开始时间
	$w         = date( "w", strtotime( $date ) );  //获取当前周的第几天 周日是 0 周一 到周六是 1 -6
	$d         = $w ? $w - $first : 6;  //如果是周日 -6天
	$beginTime = date( "Y-m-d", strtotime( "$date -" . $d . " days" ) ); //本周开始时间
	$endTime   = date( "Y-m-d", strtotime( "$beginTime +6 days" ) );  //本周结束时间
	return array( 'start' => $beginTime, 'end' => $endTime );
}

function makeSign( $data, $secretKey )
{
	ksort( $data );
	foreach ( $data as $key => $val )
	{
		$data[$key] = urldecode( $val );
	}
	$tmpdata = json_encode( $data );
	$sign    = md5( sha1( $tmpdata . $secretKey ) );

	return array( 'sign' => $sign, 'tmp' => $tmpdata );
}

/**
 * 同步用户消息
 *
 * @param type $uid
 * @param type $db
 *
 * @return type
 */
function synchroUserMessage( $uid, $db )
{
	$sql = "update useractive set readsign=0 where uid=$uid";
	$res = $db->doSql( $sql );

	return $res;
}

/**
 * 发布录像时同步到admin_wait_pass_video表中
 *
 * @param type $videoid 录像id
 * @param type $db
 *
 * @return boolean
 */
function synchroAdminWiatPassVideo( $videoid, $db )
{
	if ( empty( $videoid ) )
	{
		return false;
	}
	$data = array(
		'videoid' => $videoid
	);
	$res  = $db->insert( 'admin_wait_pass_video', $data );

	return $res ? $res : '';
}

/**
 * 检测是否为QQ
 *
 * @param type $qq
 *
 * @return type
 */
function CheckQQ( $qq )
{
	if ( preg_match( "/^[1-9]\d{4,9}$/", $qq ) )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**获取主播
 *
 * @param $uid
 * @param $db
 *
 * @return int
 */
function getCompangByUid( $uid, $db )
{
	$res = $db->field( 'cid' )->where( "uid in ($uid)" )->limit( 1 )->select( 'company_anchor' );
	if ( false !== $res )
	{
		return $res;
	}
	else
	{
		return array();
	}
}

function getCompanyTypeByCid( $cids, $db )
{
	$res = $db->field( 'type,id' )->where( "id in ($cids) " )->select( "company" );
	if ( false !== $res )
	{
		if ( $res )
		{
			foreach ( $res as $v )
			{
				$list[$v['id']] = $v['type'];
			}

			return $list;
		}
		else
		{
			return array();
		}
	}
	else
	{
		return array();
	}
}

/**
 * 检测手机号是否已使用
 *
 * @param type $mobile 手机号
 *
 * @return boolean
 */
function checkMobileIsUsed( $mobile, $db )
{
	if ( empty( $mobile ) )
	{
		return false;
	}
	$res = $db->field( 'uid' )->where( "phone=$mobile" )->limit( 1 )->select( 'userstatic' );
	if ( false !== $res && !empty( $res ) )
	{
		return $res;
	}
	else
	{
		return false;
	}
}

/**获取最后一条的房间号
 *
 * @param $db
 *
 * @return int
 */
function getMax( $db )
{
	$res = $db->field( "max(id) as id" )->select( 'roomid' );
	if ( $res && isset( $res[0]['id'] ) )
	{
		$re = $db->field( "roomid" )->where( "id=" . $res[0]['id'] )->select( 'roomid' );

		return $re[0]['roomid'];
	}
	else
	{
		return 100000;
	}
}

//添加房间号
function addRoomid( $uid, $roomid, $db )
{
	if ( empty( $uid ) || empty( $roomid ) )
	{
		return false;
	}
	$date = array(
		'uid'    => $uid,
		'roomid' => $roomid
	);
	$db->insert( 'roomid', $date );
}

/**剔除靓号
 *
 * @param $roomid  房间id
 *
 * @return bool
 */
function checkRoomId( $roomid )
{
	if ( empty( $roomid ) )
	{
		return false;
	}
	$date = file_get_contents( './roomid.txt' );
	if ( strstr( $date, "$roomid" ) )
	{
		$roomid++;

		return checkRoomId( "$roomid" );
	}
	else
	{
		return $roomid;
	}
}

function setInviteTest( $uid, $db )
{
	if ( empty( $uid ) )
	{
		return false;
	}
	$res = $db->where( "ruid=$uid" )->update( 'inside_test_inviteRecoed', array( 'status' => 1 ) );
	if ( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * 检测用户是否绑定过手机
 *
 * @param int  uid   用户id
 *
 * @return boolean
 */
function checkUserIsBindMobile( $uid, $db )
{
	if ( empty( $uid ) )
	{
		return false;
	}
	$res = $db->field( 'phone' )->where( "uid=$uid" )->limit( 1 )->select( 'userstatic' );
	if ( false !== $res )
	{
		if ( !empty( [ $res[0]['phone'] ] ) && $res[0]['phone'] !== '' )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function getInformationById( $id, $db )
{
	if ( empty( $id ) )
	{
		return false;
	}
	$res = $db->field( 'title,content' )->where( "id=$id" )->limit( 1 )->select( 'admin_information' );
	if ( false !== $res )
	{
		if ( !empty( $res ) )
		{
			return array( 'title' => $res[0]['title'], 'content' => $res[0]['content'] );
		}
		else
		{
			return array( 'title' => '', 'content' => '' );
		}
	}
	else
	{
		return false;
	}
}


/**
 * 检测昵称是否已使用
 *
 * @param type $nick 昵称
 *
 * @return boolean
 */
function checkNickIsUsed( $nick, $db )
{
	if ( empty( $nick ) )
	{
		return false;
	}
	$res = $db->where( "nick='$nick'" )->limit( 1 )->select( 'userstatic' );
	if ( $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * 检测主播有正在直播的主播
 *
 * @param type $uid 主播id
 * @param type $db
 *
 * @return boolean
 */
function checkAuchorExistLive( $uid, $db )
{
	$row = $db->field( 'uid' )->where( "uid= $uid and status=" . LIVE )->select( 'live' );
	if ( !empty( $row ) && false !== $row )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**获取当前直播的设备码
 *
 * @param $uid  主播id
 * @param $db
 *
 * @return bool
 */
function checkDeviceisDiff( $uid, $db )
{
	$row = $db->field( 'deviceid' )->where( "uid= $uid and status=" . LIVE )->select( 'live' );
	if ( !empty( $row ) && false !== $row )
	{
		return $row[0]['deviceid'];
	}
	else
	{
		return false;
	}

}

/**
 * 用户头像审核
 *
 * @param int    $uid
 * @param string $pic
 * @param type   $db
 *
 * @return boolean
 */
function admin_user_pic( $uid, $pic, $db, $status = 0 )
{
	if ( empty( $uid ) || empty( $pic ) )
	{
		return false;
	}
	$db->where( 'uid=' . $uid )->delete( 'admin_user_pic' );
	$data = array(
		'uid'    => $uid,
		'pic'    => $pic,
		'status' => $status
	);
	$res  = $db->insert( 'admin_user_pic', $data );
	if ( $res !== false )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * 检测是否禁言
 *
 * @param int $uid
 * @param int $luid
 * @param obj $db
 *
 * @return boolean
 */
function is_speakOk( $uid, $luid, $db )
{
	if ( empty( $uid ) || empty( $luid ) )
	{
		return false;
	}
	$res = $db->field( 'ctime' )->where( "uid = $uid and luid=$luid" )->select( 'silencedlist' );
	if ( isset( $res[0]['ctime'] ) )
	{
		return strtotime( $res[0]['ctime'] ) + ROOM_SILENCE_TIMEOUT;
	}
	else
	{
		return false;
	}
}

function setUserLoginCookie( $uid, $enc, $express = LOGIN_COOKIE_TIMEOUT )
{
	if ( $express )
	{
		$express = time() + $express;
	}
	hpsetCookie( '_uid', $uid, $express );
	hpsetCookie( '_enc', $enc, $express );
//    setcookie('_uid', $uid, $express, '/main', $conf['domain']);
//    setcookie('_enc', $enc, $express, '/main', $conf['domain']);
}

function delUserLoginCookie( $uid, $enc )
{
	$conf = $GLOBALS['env-def'][$GLOBALS['env']];
	setcookie( '_uid', $uid, time() - 3600, '/main', $conf['domain'] );
	setcookie( '_enc', $enc, time() - 3600, '/main', $conf['domain'] );
}

function setLiveTitleToAdmin( $liveid, $title, $uid, $nick, $db, $status = 0 )
{
	if ( empty( $liveid ) || empty( $title ) )
	{
		return false;
	}
	$data = array(
		'liveid' => $liveid,
		'title'  => $title,
		'uid'    => $uid,
		'nick'   => $nick,
		'status' => $status
	);
	$res  = $db->insert( 'admin_live_title', $data );
	if ( $res !== false )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**检查审核模式
 *
 * @param $param  头像、昵称、直播标题、评论、弹幕
 * @param $db
 *
 * @return bool
 */
function checkMode( $param, $db )
{
	$res = $db->field( 'status' )->where( "id=$param" )->select( 'admin_check_mode' );
	if ( $res !== false )
	{
		return $res[0]['status'];
	}
	else
	{
		return false;
	}
}

/**
 *修改昵称同步到admin_user_nick表
 *
 * @param int    $uid
 * @param string $nick
 * @param type   $db
 *
 * @return boolean
 */
function setNickToAdmin( $uid, $nick, $db, $status )
{
	if ( empty( $uid ) || empty( $nick ) )
	{
		return false;
	}
	$res = $db->field( 'nick' )->where( "uid=$uid" )->select( 'userstatic' );
	if ( false !== $res )
	{
		$oldnick = $res[0]['nick'];
		$sql     = "INSERT INTO `admin_user_nick` (`uid`,`nick`,`oldnick`,`status`) VALUES ($uid,'$nick' , '$oldnick',$status ) on duplicate key update uid = $uid , nick = '$nick', oldnick='$oldnick',status=$status";
		$res     = $db->query( $sql );
		if ( false === $res )
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		return false;
	}
}

/**
 *网站首页推荐直播获取
 * 1.先获取后台推荐直播
 * 2.如果数量不够获取最新直播拼凑
 * 3.如果数量不够获取最新录像拼凑
 *
 * @param     $db
 * @param int $size
 *
 * @return array
 */
function getHomeLiveRecommendByDb( $db, $size = 6 )
{
	$size          = (int)$size;
	$recommendList = array();
	if ( !$size )
	{
		return $recommendList;
	}
	//获取推荐
	$sql = "SELECT `uid` FROM `recommend_live` WHERE NOW()>`stime` AND NOW()<`etime` AND `status`=0 ORDER BY `order` LIMIT $size";
	$res = $db->query( $sql );
	while ( $row = mysqli_fetch_row( $res ) )
	{
		$recommendList[] = $row[0];
	}
	//return $recommendList;

	$nLen = $size - count( $recommendList );
	if ( $nLen == 0 )
	{
		return $recommendList;
	}
	//按最新获取
	$dsql = " ";
	if ( $size - $nLen )
	{
		$uidList = implode( $recommendList, ',' );
		$dsql    = " AND `uid` NOT IN ({$uidList})";
	}
	$sql = "SELECT `uid` FROM `live` WHERE `status`=" . LIVE . $dsql . "  ORDER BY `ctime` DESC LIMIT  $nLen";
	$res = $db->query( $sql );
	while ( $row = mysqli_fetch_row( $res ) )
	{
		$recommendList[] = $row[0];
	}

	$nLen = $size - count( $recommendList );
	if ( $nLen == 0 )
	{
		return $recommendList;
	}
	//获取录像
	if ( $size - $nLen )
	{
		$uidList = implode( $recommendList, ',' );
		$dsql    = " AND `uid` NOT IN ({$uidList})";
	}
	$sql = "SELECT `uid` FROM `live` WHERE `status`=" . LIVE_VIDEO . $dsql . " ORDER BY `ctime` DESC LIMIT  $nLen";
	$res = $db->query( $sql );
	while ( $row = mysqli_fetch_row( $res ) )
	{
		$recommendList[] = $row[0];
	}

	return $recommendList;
}


/*检查用户是否有免费的改名机会
 * @param int  $uid  用户id
 * @param $db
 * @return bool
 */
function checkisFreeChangeNick( $uid, $db )
{
	if ( empty( $uid ) )
	{
		return false;
	}
	$res = $db->field( 'isfree' )->where( "uid=$uid" )->limit( 1 )->select( 'userstatic' );
	if ( !empty( $res[0]['isfree'] ) && false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**重置免费更名字段
 *
 * @param int $uid    用户id
 * @param int $status 1设置免费改名标志  0清空标志
 * @param     $db
 *
 * @return bool
 */
function changeIsfreeStatus( $uid, $status, $db )
{
	if ( empty( $uid ) )
	{
		return false;
	}
	$res = $db->where( "uid=$uid" )->update( 'userstatic', array( 'isfree' => $status ) );
	if ( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function addLiveLength( $uid, $length, $db )
{
	if ( !$uid || !$length )
	{
		return false;
	}
	$date  = date( 'Y-m-d' );
	$utime = date( 'Y-m-d H:i:s', time() );
	$sql   = "insert into live_length (`uid`, `date`,`length`,`utime`) value($uid,'$date',$length,'$utime') on duplicate key update utime='$utime',length = length + $length";
	$res   = $db->query( $sql );
	if ( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**添加到直播时长
 *
 * @param $uid
 * @param $liveid
 * @param $db
 *
 * @return bool
 */
function toLiveLength( $liveid, $db )
{
	if ( empty( $liveid ) )
	{
		return false;
	}
	$longTime = getLiveLongTime( $liveid, $db );
	if ( $longTime )
	{
		if ( $longTime[0]['etime'] == '0000-00-00 00:00:00' )
		{
			$e_time = time();
		}
		else
		{
			$e_time = strtotime( $longTime[0]['etime'] );
		}
		$isOver = checkLiveOverTime( $liveid, $db );//是否超时
		if ( $isOver )
		{
			$e_time -= 600;
		}
		if ( $longTime[0]['stime'] == '0000-00-00 00:00:00' )
		{
			$s_time = time();
		}
		else
		{
			$s_time = strtotime( $longTime[0]['stime'] );
		}
		if ( $e_time > $s_time )
		{
			$time = timediff( $s_time, $e_time );
		}
		else
		{
			$time = timediff( time(), time() );
		}
	}
	else
	{
		$time = timediff( time(), time() );
	}
	if ( $time['diff'] && $time['diff'] > 60 )
	{
		addLiveLength( $longTime[0]['uid'], $time['diff'], $db );
	}
}

function hpsetCookie( $name, $value, $express = LOGIN_COOKIE_TIMEOUT )
{
	$conf = $GLOBALS['env-def'][$GLOBALS['env']];
	if ( $express )
	{
		$express = time() + $express;
	}
	setcookie( $name, $value, $express, $conf['cookiePath'], $conf['domain'] );

}

function hpdelCookie( $name )
{
	if ( isset( $_COOKIE[$name] ) )
	{
		hpsetCookie( $name, $_COOKIE[$name], -3600 );
	}
}

/**
 * 同步头像url到服务器
 *
 * @param string $url
 *
 * @return ''|string
 */
function GrabImage( $url )
{
	if ( empty( $url ) )
	{
		return '';
	}
	$conf = $GLOBALS['env-def'][$GLOBALS['env']];
	$dir  = $conf['img-dir'];
	$ndir = rand( 1, 20 ) . '/' . rand( 1, 20 );//目录
	$pdir = $dir . '/' . $ndir;
	if ( !is_dir( $pdir ) )
	{
		@mkdir( $pdir, 0755, true );
	}
	$backfile = '/' . $ndir . '/' . date( 'YndHis' ) . '_' . rand( 100, 999 ) . '.png';//保存到数据库的路径
	$filename = $dir . '' . $backfile;
	$image    = file_get_contents( $url );
	file_put_contents( $filename, $image );
	//开始捕捉
//    ob_start();
//    readfile($url);
//    $img = ob_get_contents();
//    ob_end_clean();
//    $fp2 = @fopen($filename, "a");
//    fwrite($fp2, $img);
//    fclose($fp2);
	return $backfile; //返回新路径&文件名
}

/**
 * @param $openid
 * @param $channel
 * @param $res array('usernick'=>'','pic'=>'');
 * @param $db
 */
function threeSideLogin( $openid, $channel, $res, $db, $channelID = 0 )
{

	$loginUID      = false;
	userHelp::$db2 = $db;

	if ( !$channelID )
	{
		$channelID = hp_getRequestChannelID();
	}

	if ( $channel != LOGIN_CHANNEL_WEIBO )
	{
		$unionid = $res['unionid'];

		if ( !$unionid )
		{
			return -5001;
		}

		$loginUID = userHelp::isUnionidUsed( $unionid, $channel );

		if ( !$loginUID )
		{
			$loginUID = userHelp::isOpenidUsed( $openid, $channel );

			if ( $loginUID )
			{
				userHelp::upUnionid( $loginUID, $openid, $channel, $unionid, $db );
			}
		}
	}
	else
	{
		$loginUID = userHelp::isOpenidUsed( $openid, $channel );
	}

	//当前用户已经登录过
	if ( $loginUID )
	{
		$login = new \service\login\LoginService( 0, '' );

		$login->doLoginWithUid( $loginUID );

		$result = $login->getResult();

		if ( $result['error_code'] )
		{
			return $result['error_code'];
		}
		else
		{
			setUserLoginCookie( $loginUID, $result['encpass'], 30 * 24 * 3600 );

			return [ 'uid' => $loginUID, 'encpass' => $result['encpass'], 'isRegister' => 0 ];
		}
	}
	else
	{
		//不存在用户信息，进行用户创建
		$redis   = new RedisHelp();
		$lockKey = "register_" . $openid;

		if ( lockRequest( $lockKey, $redis ) )
		{
			write_log( "duplicate register key $lockKey" );

			return -5001;
		}

		$nickIsUsed = 0;
		$nickname   = $res['nickname'];

		$nickHelp = new \service\user\UserNickService();

		$validNick = \service\user\UserNickService::isValidNick( $nickname );
		if ( !$validNick )
		{
			$nickname = $nickHelp->createNick();
		}

		$logName = "user_register";

//		$db->autocommit( false );

		$createUserRet = userHelp::createUser( $nickname, $openid, md5( $nickname ), $res['pic'], 0 );

		$loginUID   = $createUserRet['uid'];
		$encpass    = $createUserRet['encpass'];
		$isREgister = 1;

		$nickHelp->setUid( $loginUID );

		$outerNick = $res['nickname'];
		$unionid   = $res['unionid'] ?? 0;

		if ( $loginUID && userHelp::upUserToThreeSide( $loginUID, $openid, $channel, $outerNick )
			&& ( $channel == LOGIN_CHANNEL_WEIBO || userHelp::upUnionid( $loginUID, $openid, $channel, $unionid ) )
			&& $nickHelp->alterByThreeSideLogin( $nickname )
		)
		{
//			$db->commit();
//			$db->autocommit( true );

			$logMsg = "uid:{$createUserRet['uid']};nicke:{$nickname};channel:{$channel}";

			hpsetCookie( '_notice_user_modify_nick', 1 );
			changeIsfreeStatus( $loginUID, 1, $db );

			$event = new \service\event\EventManager();
			$event->trigger( \service\event\EventManager::ACTION_USER_REGISTER, [ 'uid' => $loginUID ] );

			$logMsg .= ";channelID:{$channelID}";

			if ( !bindUserChannel( $loginUID, intval( $channelID ), $db ) )
			{
				write_log( 'error|第三方登陆，添加注册渠道异常;' . $logMsg, $logName );
			}

			$promocode = hp_getRequestPromoCode();
			if ( !updateUserPromocode( $loginUID, $promocode, \system\DbHelper::getInstance( 'huanpeng' ) ) )
			{
				$logMsg .= ";promocode :{$promocode}";
				write_log( "error | 第三方登录，添加注册码异常" . $logMsg, $logName );
			}

			unLockRequest( $lockKey, $redis );

			return [ 'uid' => $loginUID, 'encpass' => $encpass, 'isRegister' => $isREgister ];
		}
		else
		{
			$db->rollback();
			unLockRequest( $lockKey, $redis );
			write_log( 'error|第三方登陆，注册异常;channel:;' . $channel . ";" . json_encode( $res ), $logName );

			return -5001;
		}
	}


//
//    userHelp::$db2 = $db;
//
//    $toSetUserLogin = function ($loginUID, $encpass = null, $isRegister=false) {
//        $encpass = $encpass ? $encpass : userHelp::getUserEncpass($loginUID);
//        setUserLoginCookie($loginUID, $encpass, (30 * 24 * 3600));
//		$isRegister = $isRegister ? 1 : 0;
//        $event = new \service\event\EventManager();
//        $event->trigger(\service\event\EventManager::ACTION_USER_LOGIN,['uid' => $loginUID]);
//        $event = null;
//        return ['uid' => $loginUID, 'encpass' => $encpass, "isRegister"=>$isRegister];
//    };
//
//	$loginUID = false;
//    if ($channel != LOGIN_CHANNEL_WEIBO) {
//        $unionid = $res['unionid'];
//        if (!$unionid) {
//            return -5001;
//        }
//
//        if ($loginUID = userHelp::isUnionidUsed($unionid, $channel)) {
////            return $toSetUserLogin( $loginUID );
//        } else if ($loginUID = userHelp::isOpenidUsed($openid, $channel)) {
//            userHelp::upUnionid($loginUID, $openid, $channel, $unionid, $db);
////            return $toSetUserLogin( $loginUID );
//        }
//    } else if ($loginUID = userHelp::isOpenidUsed($openid, $channel)) {
////        return $toSetUserLogin( $loginUID );
//    }
//
//    if($loginUID)
//	{
//		//check user is in black list
//		$auth = new service\user\UserAuthService();
//		$auth->setUid($loginUID);
//
//		if($auth->checkDisableLoginStatus() !== true )
//		{
//			$result = $auth->getResult();
//			$errno = $result['error_code'];
//
//			return $errno;
//		}
//
//		return  $toSetUserLogin($loginUID);
//	}
//
//    //不存在用户信息，进行用户创建
//	$redis = new RedisHelp();
//	$lockKey = "register_".$openid;
//
//	if(lockRequest($lockKey))
//	{
//		write_log("duplicate register key $lockKey");
//		return -5001;
//	}
//
//    //TODO 检测nick是否符合格式
//    //检测昵称是否被占用
//    $checkUserName = function ($nick) {
//        if (mb_strlen($nick, 'utf-8') < 3 || mb_strlen($nick, 'utf-8') > 10) {
//            return false;
//        } else {
//            if (mb_strlen($nick, 'latin1') < 3 || mb_strlen($nick, 'latin1') > 30) {
//                return false;
//            } else {
//				$textService = new service\rule\TextService();
//				$textService->setCaller('api:'.__FILE__.';line:'.__LINE__);
//				//关闭后如果接请求反垃圾接口网络服务异常都会返回true,默认通过
//				//$textService->setCallLevel(true);
//				$port = 0;
//				$textService->addText($nick,'',service\rule\TextService::CHANNEL_NICKNAME)->setIp(fetch_real_ip($port));
//				//反垃圾过滤
//				if(!$textService->checkStatus())
//				{
////					write_log("error|昵称包含敏感内容;",'modify_nick');
////					error2( -4035, 2 );\
//					return false;
//				}
//				else
//				{
//					return true;
//				}
//            }
//        }
//
//    };
//
//    $nickIsUsed = 0;
//    $nickname = $res['nickname'];
//    if (userHelp::isNickExist($nickname) || !$nickname || checkEmoji($nickname) || !$checkUserName($nickname)) {
//        $nickname = userHelp::createNick();
//        $nickIsUsed = 1;
//    }
//    $db->autocommit(false);
//    $createUserRet = userHelp::createUser($nickname, $openid, $nickname, $res['pic'], 0);
//
//    $logName = 'user_register';
//    $logMsg  = "uid:{$createUserRet['uid']};nicke:{$nickname};channel:{$channel}";
//
//    if ($createUserRet['uid'] && userHelp::upUserToThreeSide($createUserRet['uid'], $openid, $channel, $res['nickname'])
//        && ($channel == LOGIN_CHANNEL_WEIBO || userHelp::upUnionid($createUserRet['uid'], $openid, $channel, $res['unionid']))
//    ) {
//        $db->commit();
//        $db->autocommit(true);
//
////        hpSetcookie('_notice_user_modify_nick', $nickIsUsed);
//        //if ($nickIsUsed) {
//			hpsetCookie("_notice_user_modify_nick", 1);
//            changeIsfreeStatus($createUserRet['uid'], 1, $db);//设置免费改名标志 第三方登录都能免费改一次
//        //}
//        //v6RegChannel($createUserRet['uid'], $db);
//        $logMsg .= ";channelID:{$channelID}";
//
//        $event = new \service\event\EventManager();
//        $event->trigger(\service\event\EventManager::ACTION_USER_REGISTER,['uid' => $createUserRet['uid'] ]);
//        $event = null;
//
//        //绑定注册渠道
//        if(!bindUserChannel($createUserRet['uid'], (int) $channelID, $db )) {
//            write_log('error|第三方登陆，添加注册渠道异常;'.$logMsg,$logName);
//        }
//
//        write_log('success|第三方登陆，注册成功;'.$logMsg,$logName);
//		unLockRequest($lockKey, $redis);
//        return $toSetUserLogin( $createUserRet['uid'], $createUserRet['encpass'], true );
//    } else {
//        $db->rollback();
//		unLockRequest($lockKey, $redis);
//        write_log('error|第三方登陆，注册异常;'.$logMsg,$logName);
//        return -5001;
//    }
}

/**
 * @param $openid
 * @param $channel
 * @param $res array('usernick'=>'','pic'=>'');
 * @param $db
 */
function threeSideBind( $openid, $channel, $res, $db )
{
	if ( func_num_args() == 6 )
	{
		$uid = (int)func_get_arg( 4 );
		$enc = trim( func_get_arg( 5 ) );
	}
	else
	{
		$uid = (int)$_COOKIE['_uid'];
		$enc = trim( $_COOKIE['_enc'] );
	}
	//登录验证错误
	$userHelp = new UserHelp( $uid, $db );
	if ( !$userHelp || $userHelp->checkStateError( $enc ) )
	{
		//TODO handle not login
		return -4067;

	}

	$auth = new service\user\UserAuthService();
	$auth->setUid( $uid );

	if ( $auth->checkDisableLoginStatus() !== true )
	{
		$result = $auth->getResult();

//		$errorCode = $result['error_code'];

		return -1013;
	}

	if ( $channel != LOGIN_CHANNEL_WEIBO )
	{
		$unionid = $res['unionid'];
		if ( !$unionid )
		{
			mylog( 'unionid error 5001' );

			return -5001;
		}

		if ( $userHelp::isUnionidUsed( $unionid, $channel, $db ) )
		{
			return -4068;
		}
		else
		{
			if ( $userHelp::isOpenidUsed( $openid, $channel, $db ) )
			{
				$userHelp::upUnionid( $uid, $openid, $channel, $unionid, $db );

				return -4068;
			}
		}
	}
	else
	{
		if ( $userHelp::isOpenidUsed( $openid, $channel, $db ) )
		{
			return -4068;
		}
	}


//    if ($channel == 'wechat' && $userHelp->isWeichatUnionidUsed($res['unionid'], $channel, $db)) {
//        return -4068;
//    }
//
//    if ($userHelp->isOpenidUsed($openid, $channel, $db)) {
//        //TODO handle the three side openid is used
//        //exit('该账号已经绑定其他用户');
//        return -4068;
//    }

	//绑定用户
	if ( $userHelp->bindThreeSide( $openid, $channel, $res['nickname'] ) && userHelp::upUnionid( $uid, $openid, $channel, $res['unionid'], $db ) )
	{
		return true;

	}
	else
	{
		//TODO handle the three side user failed
		//exit('绑定用户失败');
		mylog( 'bind error 5001' );

		return -5001;
	}
}

/**
 * 错误跳转页
 *
 * @param        $str
 * @param string $uri
 * @param bool   $mode
 */
function jumpErrPage( $str, $uri = 'oauth.php', $mode = true )
{
	$str = $mode ? urlencode( $str ) : $str;
	header( "Location:" . WEB_ROOT_URL . "$uri?err=$str" );
}

/**
 * 根据error code 决定网站上的页面跳转
 *
 * @param $type //bind or login
 * @param $code //error code
 */
function threeSideHandleError( $type, $code )
{
	if ( $code === -4070 )
	{
		jumpErrPage( '请求非法' );
		exit;
	}

	if ( $code === -1013 )
	{
		jumpErrPage( '请先登录' );
		exit();
	}

	if ( $code === -30008 || $code === -30009 )
	{
		jumpErrPage( "帐号被封禁" );
	}

	if ( $code === -4069 )
	{
		jumpErrPage( '授权失败' );
		exit();
	}
	if ( $code === false )
	{
		jumpErrPage( '授权失败' );
		exit();
	}

	if ( $type == 'login' )
	{
		if ( $code && is_array( $code ) )
		{
			//登录成功 返回首页

			if ( isMobile() )
			{
				if ( $_COOKIE['t_login_ref_url'] )
				{
					$ref = urldecode($_COOKIE['t_login_ref_url']);
					hpdelCookie( 't_login_ref_url' );
					header( "Location:{$ref}" );
				}
				else
				{
					header( "Location:" . WEB_ROOT_URL . "mobile/" );
				}
			}
			else
			{
				header( 'Location:' . WEB_ROOT_URL );
			}
			exit;
		}
		else
		{
			if ( $code === -5001 )
			{
				jumpErrPage( '创建用户失败' );
			}
		}
	}
	elseif ( $type == 'bind' )
	{
		if ( $code && $code === true )
		{
			$backurl = $_GET['backurl'] == 'personal' ? 'perspnal/mp' : 'personal/';
			header( 'Location:' . WEB_ROOT_URL . $backurl );
		}
		else
		{
			if ( $code === -4067 )
			{
				jumpErrPage( '请先登录' );
			}
			if ( $code === -4068 )
			{
				jumpErrPage( '账号已经被绑定' );
			}
			if ( $code === -5001 )
			{
				jumpErrPage( '绑定账号失败，请重新绑定' );
			}
		}
	}
}

/**
 * 检查输入的参数与规则是否匹配
 *
 * @param $condition
 * @param $data
 *
 * @return bool
 */
function checkParam( $condition, &$data, &$params )
{
	$temp = array();

	$default = [
		'type'    => 'string',
		'must'    => false,
		'default' => null,
		'values'  => null
	];

	foreach ( $condition as $param => $constraint )
	{
		$currConf = $default;
		if ( is_string( $constraint ) )
		{
			if ( $constraint == 'int' )
			{
				$currConf['type'] = 'int';
			}
			else
			{
				$currConf['type'] = 'string';
			}
		}
		elseif ( is_array( $constraint ) )
		{
			$currConf = array_merge( $currConf, $constraint );
		}

		if ( $currConf['type'] == 'int' )
		{
			$checkParamOne = ( isset( $param ) && (int)$data[$param] ) ? (int)$data[$param] : 0;
		}
		else
		{
			$checkParamOne = ( isset( $data[$param] ) && trim( urldecode( $data[$param] ) ) ) ? trim( urldecode( $data[$param] ) ) : '';
		}
		if ( $currConf['must'] )
		{
			if ( !$checkParamOne )
			{
				if ( is_null( $currConf['default'] ) )
				{
					return false;
				}
				else
				{
					$checkParamOne = $currConf['default'];
				}
			}
		}
		else
		{
			if ( !$checkParamOne )
			{
				$checkParamOne = $currConf['type'] == 'int' ? (int)$currConf['default'] : trim( $currConf['default'] );
			}
		}

		if ( is_array( $currConf['values'] ) )
		{
			if ( !in_array( $checkParamOne, $currConf['values'] ) )
			{
				return false;
			}
		}
		$temp[$param] = $checkParamOne;
	}

	if ( is_array( $params ) )
	{
		$params = $temp;
	}
	else
	{
		$data = $temp;
	}

	return true;
}


//获取首页直播推荐相关函数
function getRecommentLists( $db )
{
	$rows = $db->field( 'list' )->where( 'client=2' )->select( 'recommend_live' );
	if ( $rows !== false && !empty( $rows ) )
	{
		$rows = $rows[0]['list'];
	}
	else
	{
		$rows = array();
	}

	return $rows;
}

function getInfoByUidList( $uids, $db )
{
	if ( empty( $uids ) )
	{
		return false;
	}
	$res = $db->field( 'uid,poster' )->where( "uid in ($uids)  and status=1" )->select( 'admin_recommend_live' );
	if ( false !== $res && !empty( $res ) )
	{
		foreach ( $res as $v )
		{
			$temp[$v['uid']] = $v['poster'];
		}

		return $temp;
	}
	else
	{
		return array();
	}
}

function getWaitAuthorList( $db )
{
	$res = $db->field( 'uid,poster' )->where( 'status=0' )->order( 'ctime DESC' )->select( 'admin_recommend_live' );
	if ( false !== $res && !empty( $res ) )
	{
		foreach ( $res as $v )
		{
			$temp[$v['uid']] = $v['poster'];
		}

		return $temp;
	}
	else
	{
		return array();
	}
}

/**获取已推荐主播最后一次的直播信息
 *
 * @param string $uids 主播id串
 * @param        $db
 *
 * @return array|bool
 */
function getRecommendAnchorLast( $uids, $db )
{
	if ( empty( $uids ) )
	{
		return false;
	}
	$sql = "select liveid,poster,uid,status,stream,server,orientation from (select * from live  order by ctime  desc) live where uid in ($uids)  group by uid order by ctime desc";
	$res = $db->doSql( $sql );
	if ( false !== $res && !empty( $res ) )
	{
		foreach ( $res as $v )
		{
			$lives[$v['uid']] = $v;
		}

		return $lives;
	}
	else
	{
		return array();
	}
}

function recommendLiveList( $db )
{
	$recommend = getRecommentLists( $db );
	if ( $recommend )
	{
		$info  = getInfoByUidList( $recommend, $db );
		$order = explode( ',', $recommend );
		$res   = getRecommendAnchorLast( $recommend, $db );
	}
	else
	{
		$res = array();
	}
	if ( $res )
	{
		$recommendList = array();
		$conf          = $GLOBALS['env-def'][$GLOBALS['env']];
		for ( $i = 0, $k = count( $order ); $i < $k; $i++ )
		{
			if ( isset( $res[$order[$i]] ) )
			{
				$arr['uid']    = $res[$order[$i]]['uid'];
				$roomid        = getRoomIdByUid( $res[$order[$i]]['uid'], $db );
				$arr['roomID'] = $roomid[$res[$order[$i]]['uid']] ? $roomid[$res[$order[$i]]['uid']] : 0;
				$arr['liveID'] = $res[$order[$i]]['liveid'];
				$arr['stream'] = $res[$order[$i]]['stream'];
				$arr['server'] = $res[$order[$i]]['server'];
				if ( $res[$order[$i]]['status'] == 100 )
				{
					$arr['isLiving'] = 1;
				}
				else
				{
					$arr['isLiving'] = 0;
				}
				if ( !empty( $info[$order[$i]] ) )
				{
					$arr['poster'] = !empty( $info[$order[$i]] ) ? "http://" . $conf['domain-img'] . '/' . $info[$order[$i]] : '';
				}
				else
				{
					$arr['poster'] = !empty( $res[$order[$i]]['poster'] ) ? "http://" . $conf['domain-img'] . '/' . $res[$order[$i]]['poster'] : '';
				}


				$arr['orientation'] = $res[$order[$i]]['orientation'];
			}

			array_push( $recommendList, $arr );
		}
		if ( $recommendList )
		{
			return $recommendList;
		}
		else
		{
			return array();
		}

	}
	else
	{
		return array();
	}
}

function dong_log( $title, $content, $db )
{
	$data = array(
		'title'   => $title,
		'content' => $content
	);
	$db->insert( 'dong_log', $data );
}

/**
 * 财务系统成功返回,后续操作失败日志
 *
 * @param $type
 * @param $desc
 * @param $db
 */
function unsuccess_log_for_financeBack( $title, $desc, $db )
{
	$data = array(
		'title' => $title,
		'desc'  => json_encode( $desc ),
	);
	$db->insert( 'unsuccess_log_for_financeBack', $data );

}

/**添加邀请登录纪录
 *
 * @param int $suid 分享者id
 * @param int $ruid 新用户id
 * @param int $luid 新用户id
 * @param     $db
 *
 * @return bool
 */
function inviteRecord( $suid, $ruid, $luid, $db )
{
	if ( empty( $suid ) || empty( $ruid ) || empty( $luid ) )
	{
		return false;
	}
	$data = array(
		'suid'   => $suid,
		'ruid'   => $ruid,
		'luid'   => $luid,
		'status' => 0
	);
//    dong_log('邀请纪录', $data, $db);
	$res = $db->insert( 'invite_record', $data );
	if ( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function getHomeGameBlock( $db )
{
	$res = $db->field( 'type,gameid,number' )->where( 'type=3' )->select( 'admin_recommend_game' );
	if ( false !== $res && !empty( $res ) )
	{
		$list     = array();
		$gname    = getMoreGameName( $res[0]['gameid'], $db );
		$gcount   = explode( ',', $res[0]['gameid'] );
		$numCount = explode( ',', $res[0]['number'] );
		for ( $i = 0, $k = count( $gcount ); $i < $k; $i++ )
		{
			$tem['gameID']   = $gcount[$i];
			$tem['gameName'] = isset( $gname[$gcount[$i]] ) ? $gname[$gcount[$i]] : '';
			$tem['number']   = isset( $numCount[$i] ) ? $numCount[$i] : 0;
			array_push( $list, $tem );
		}

		return array( 'list' => $list );
	}
	else
	{
		return array( 'list' => array() );
	}
}


/**根据录像ID获取录像详情
 *
 * @param int $videoID 录像id
 * @param     $db
 *
 * @return array|bool
 */
function getVideoInfoById( $videoID, $db )
{
	if ( empty( $videoID ) )
	{
		return false;
	}
	else
	{
		if ( !is_numeric( $videoID ) )
		{
			return false;
		}
	}
	$res = $db->field( 'uid,title,poster,gameid,viewcount,vfile,orientation' )->where( "videoid=$videoID  and status=" . VIDEO )->limit( 1 )->select( 'video' );
	if ( false !== $res )
	{
		$conf  = $GLOBALS['env-def'][$GLOBALS['env']];
		$gname = getMoreGameName( implode( ',', array_column( $res, 'gameid' ) ), $db );
		foreach ( $res as $v )
		{
			$temp['uid']         = $v['uid'];
			$temp['title']       = $v['title'];
			$temp['poster']      = ( $v['poster'] ) ? ( "http://" . $conf['domain-img'] . "/" . $v['poster'] ) : CROSS;
			$temp['videoUrl']    = ( $v['vfile'] ) ? ( $conf['domain-video'] . $v['vfile'] ) : '';
			$userinfo            = getUserInfo( $v['uid'], $db ); //用户信息
			$temp['nick']        = ( $userinfo[0]['nick'] ) ? $userinfo[0]['nick'] : '';
			$temp['head']        = ( $userinfo[0]['pic'] ) ? "http://" . $conf['domain-img'] . "/" . $userinfo[0]['pic'] : DEFAULT_PIC;
			$temp['viewCount']   = $v['viewcount'] ? $v['viewcount'] : '0';
			$temp['orientation'] = $v['orientation'];
			$temp['gameName']    = isset( $gname[$v['gameid']] ) ? $gname[$v['gameid']] : '0';
		}

		return $temp;
	}
	else
	{
		return array();
	}

}

/**获取推荐录像
 *
 * @param int $videoID 录像id
 * @param int $size    数量 默认6
 * @param     $db
 *
 * @return array|bool
 */
function getRecommendVideoList( $videoID, $size = 6, $db )
{
	if ( empty( $videoID ) )
	{
		return false;
	}
	else
	{
		if ( !is_numeric( $videoID ) )
		{
			return false;
		}
	}
	$gtype = $db->field( 'gametid' )->where( "videoid=$videoID  and status=" . VIDEO )->limit( 1 )->select( 'video' );
	if ( false !== $gtype )
	{
		$list = array();
		$res  = $db->field( 'uid,title,poster,gameid,viewcount,orientation' )->where( "gametid=" . $gtype[0]['gametid'] . " and status=" . VIDEO )->limit( $size )->select( 'video' );
		$conf = $GLOBALS['env-def'][$GLOBALS['env']];
		if ( false !== $res )
		{
			if ( empty( $res ) )
			{
				$list = array();
			}
			else
			{
				$userinfo = getUserInfo( array_column( $res, 'uid' ), $db ); //用户信息
				$gname    = getMoreGameName( implode( ',', array_column( $res, 'gameid' ) ), $db );
				foreach ( $res as $v )
				{
					$temp['title']       = $v['title'];
					$temp['poster']      = ( $v['poster'] ) ? ( "http://" . $conf['domain-img'] . "/" . $v['poster'] ) : CROSS;
					$temp['nick']        = isset( $userinfo[$v['uid']] ) ? $userinfo[$v['uid']]['nick'] : '';
					$temp['head']        = isset( $userinfo[$v['uid']] ) ? "http://" . $conf['domain-img'] . "/" . $userinfo[$v['uid']]['pic'] : DEFAULT_PIC;
					$temp['viewCount']   = $v['viewcount'] ? $v['viewcount'] : '0';
					$temp['orientation'] = $v['orientation'];
					$temp['gameName']    = isset( $gname[$v['gameid']] ) ? $gname[$v['gameid']] : '0';
					array_push( $list, $temp );
				}
			}
		}
		else
		{
			$list = array();
		}

		return array( 'list' => $list );
	}
	else
	{
		return array();
	}

}

/**根据UID获取直播详情（H5分享）
 *
 * @param int $uid 主播id
 * @param     $db
 *
 * @return array|bool
 */
function getLiveInfoById( $uid, $db )
{
	if ( empty( $uid ) )
	{
		return false;
	}
	else
	{
		if ( !is_numeric( $uid ) )
		{
			return false;
		}
	}
	$res = $db->field( 'uid,liveid,title,poster,gamename,orientation' )->where( "uid=$uid  and status=" . LIVE )->limit( 1 )->select( 'live' );
	if ( false !== $res )
	{
		$conf = $GLOBALS['env-def'][$GLOBALS['env']];
		foreach ( $res as $v )
		{
			$temp['title']       = $v['title'];
			$temp['poster']      = ( $v['poster'] ) ? ( "http://" . $conf['domain-img'] . "/" . $v['poster'] ) : CROSS;
			$userinfo            = getUserInfo( $v['uid'], $db ); //用户信息
			$temp['nick']        = ( $userinfo[0]['nick'] ) ? $userinfo[0]['nick'] : '';
			$temp['head']        = ( $userinfo[0]['pic'] ) ? "http://" . $conf['domain-img'] . "/" . $userinfo[0]['pic'] : DEFAULT_PIC;
			$temp['userCount']   = getLiveRoomUserCount( $v['uid'], $db );
			$temp['orientation'] = $v['orientation'];
			$temp['gameName']    = $v['gamename'];
		}

		return $temp;
	}
	else
	{
		return array();
	}

}

//网宿回调异常日志记录
function Log_for_net( $title, $content, $db )
{
	$data = array(
		'title'   => $title,
		'content' => json_encode( $content )
	);
	$res  = $db->insert( 'log_for_chinaNet', $data );
	if ( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**获取推荐直播（H5分享）
 *
 * @param int $uid  主播id
 * @param int $size 数量 默认6
 * @param     $db
 *
 * @return array|bool
 */
function getRecommendLiveList( $uid, $size = 6, $db )
{
	if ( empty( $uid ) )
	{
		return false;
	}
	else
	{
		if ( !is_numeric( $uid ) )
		{
			return false;
		}
	}
	$gtype = $db->field( 'gametid' )->where( "uid=$uid  and status=" . LIVE )->limit( 1 )->select( 'live' );
	if ( false !== $gtype )
	{
		$list = array();
		if ( empty( $gtype ) )
		{
			$gtype = $db->field( 'gametid' )->where( "uid=$uid " )->order( 'ctime desc' )->limit( 1 )->select( 'live' );
		}
		$res  = $db->field( 'uid,liveid,title,poster,gamename,orientation' )->where( "gametid=" . $gtype[0]['gametid'] . " and uid != $uid and status=" . LIVE )->limit( $size )->select( 'live' );
		$conf = $GLOBALS['env-def'][$GLOBALS['env']];
		if ( false !== $res )
		{
			if ( count( $res ) < $size )
			{
				$osize = $size - count( $res );
				$ores  = $db->field( 'uid,liveid,title,poster,gamename,orientation' )->where( "uid != $uid and status=" . LIVE )->limit( $osize )->select( 'live' );
				if ( false !== $ores && !empty( $ores ) )
				{
					$res = array_merge( $res, $ores );
				}
			}
			if ( count( $res ) < $size )
			{
				$ssize = $size - count( $res );
				$ores  = $db->field( 'uid,liveid,title,poster,gamename,orientation' )->where( "uid != $uid  group by uid" )->limit( $ssize )->select( 'live' );
				if ( false !== $ores && !empty( $ores ) )
				{
					$res = array_merge( $res, $ores );
				}
			}
			$userinfo = getUserInfo( array_column( $res, 'uid' ), $db ); //用户信息
			$ucount   = getMoreLiveRoomUserCount( implode( ',', array_column( $res, 'uid' ) ), $db );
			foreach ( $res as $v )
			{
				$temp['uid']         = $v['uid'];
				$temp['title']       = $v['title'];
				$temp['poster']      = ( $v['poster'] ) ? ( "http://" . $conf['domain-img'] . "/" . $v['poster'] ) : CROSS;
				$temp['nick']        = isset( $userinfo[$v['uid']] ) ? $userinfo[$v['uid']]['nick'] : '';
				$temp['head']        = isset( $userinfo[$v['uid']] ) ? "http://" . $conf['domain-img'] . "/" . $userinfo[$v['uid']]['pic'] : DEFAULT_PIC;
				$temp['userCount']   = isset( $ucount[$v['uid']] ) ? $ucount[$v['uid']] : '0';
				$temp['orientation'] = $v['orientation'];
				$temp['gameName']    = $v['gamename'];
				array_push( $list, $temp );
			}
		}
		else
		{
			$list = array();
		}

		return array( 'list' => $list );
	}
	else
	{
		return array();
	}

}

function addUserHpcoin( $amount, $uid, $db )
{
	$sql = "update useractive set hpcoin=hpcoin + $amount where uid = $uid";
	$res = $db->query( $sql );
	if ( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function rechargeHandleFlow( $transactionId, $outTradeId, $openid, $db, $timeend = 0, $financeObj = null )
{
	$redis = new \RedisHelp();
	$ctime = lib\Finance::getTimeByNatureID( $outTradeId );

	if ( !$financeObj )
	{
		$financeObj = new lib\Finance( $db, $redis, $ctime );
	}

	$result = $financeObj->rechargeOrderFinish( $transactionId, $outTradeId, $openid, $timeend );

	if ( $ret = $financeObj::checkBizResult( $result ) )
	{
		$info  = $financeObj->getRechargeOrderInfo( $outTradeId );
		$uid   = $info['uid'];
		$rcoin = $info['hb'];

		$user = new lib\User( $uid, $db, $redis );
		$user->updateUserHpCoin( $result['hb'] );
		$rkey = "SHAMAPI_RECHARGE_$uid";
		$user->afterRecharge( $info['ctime'] );//date("Y-m-d H:i:s"));
		//调动事件
		$event = new \service\event\EventManager();
		$event->trigger( $event::ACTION_USER_MONEY_UPDATE, [ 'uid' => $uid ] );
		unset( $event );

		$rechargeOrderStatus_redis = "recharge:" . $info['id'] . "-" . $uid;
		$setRedis                  = $redis->set( $rechargeOrderStatus_redis, 1, 600 );
		mylog( "setRedis $uid >>>$rechargeOrderStatus_redis>>> $setRedis", LOG_DIR . "service\\payment\\WxpayHP.log" );
//		$redis->isExists($rkey)

		//if rechargeCount == 1 那么则是首次充值
		$rechargeCount = $user->getRechargeNumber();

		if ( $rechargeCount == 1 && $rcoin >= 100 )
		{
			synchroTask( $uid, 30, 0, 200, $db );
			$redis->set( $rkey, 1 );
		}

//	1	("success pay". json_encode( $result ),"service\\payment\\WxpayHp" );

		//每月每日充值任务
		$packEvent = new \service\pack\PackEvnentService();
		$result    = $packEvent->dayExchange( $uid, $info['rmb'], $outTradeId );
		write_log( "packEvent day uid:$uid, rmb:{$info['rmb']},sourceId:$outTradeId,  result is " . intval( $result ), "pay_active_result" );
		if ( !$result )
		{
			$code = $packEvent->getErrorCode();
			$msg  = $packEvent->getErrorMessage();
			write_log( "day exchange failed code:$code, msg:$msg", "pay_active_result" );
		}

		$result = $packEvent->monthExchange( $uid, $info['rmb'], $outTradeId );
		write_log( "packEvent month uid:$uid, rmb:{$info['rmb']},sourceId:$outTradeId,  result is " . intval( $result ), "pay_active_result" );
		if ( !$result )
		{
			$code = $packEvent->getErrorCode();
			$msg  = $packEvent->getErrorMessage();
			write_log( "month exchange failed code:$code, msg:$msg", "pay_active_result" );
		}

		mylog( "success pay " . json_encode( $result ), LOG_DIR . "service\\payment\\WxpayHP.log" );

		return $result['tid'];
	}
	else
	{
//		write_log( $transactionId."-> finance recharge failed " . json_encode( $result ) );

		write_log( json_encode( func_get_args() ) . "in functions " . __FUNCTION__ . "finance recharge " . json_encode( $result ), "financeRechargeError" );
	}

	return false;

//    if (!$uid)
//        return false;
//    if (!$db)
//        $db = new \DBHelperi_huanpeng();
//	if (!$redis)
//		$redis = new RedisHelp();
//    if (!$userHelp)
//        $userHelp = new hp\lib\User($uid, $db, $redis);
//
//    $userHelp->updateUserHpCoin($coin);
//    $rkey = "SHAMAPI_RECHARGE_$uid";
//
//    if($redis->isExists($rkey) === false && $rcoin >= 100)
//	{
//		synchroTask($uid, 30, 0, 200, $db);
//		$redis->set($rkey, 1);
//	}
//
//	return true;
//
//    if ($userHelp->upHpcoin($coin)) {
//        $sql = "insert into billdetail (beneficiaryid, income, type, info) value($uid, $coin, " . BILL_RECHARGE . ",'$id')";
//        if ($db->query($sql)) {
//            $rkey = "SHAMAPI_RECHARGE_$uid";
//            if ($redis->isExists($rkey) === false && $coin >= 100) {//同步任务
//                synchroTask($uid, 30, 0, 200, $db);
//                $redis->set($rkey, 1);
//            }
//            return true;
//        } else
//            return false;
//    } else {
//        return false;
//    }
}


function alipayRechargeHandleFlow( $transactionId, $outTradeId, $openid, $db )
{
	$ret = rechargeHandleFlow( $transactionId, $outTradeId, $openid, $db );

	mylog( "rechargeHandleFlow result is " . json_encode( $ret ), LOG_DIR . "service\\payment\\WxpayHP.log" );
	if ( $ret == false )
	{
		return false;
	}
	else
	{
		return true;
	}

//    RechargeOrder::$orderid = $outTradeId;
//    RechargeOrder::setdb($db);
//    if (!RechargeOrder::getInfo()) {
//        //TODO can't find order in system handle flow
//        mylog('no order in the huanpeng system', LOGFN_WX_PAY);
//        return false;
//    }
//
//    $redis = new RedisHelp();
//    $rechargeOrderStatus_redis = "recharge:" . RechargeOrder::$orderid . "-" . RechargeOrder::$uid;
//    $redis->set($rechargeOrderStatus_redis, 1, 600);
//    mylog('rechargeOrder redis status is ' . $redis->get($rechargeOrderStatus_redis), LOGFN_WX_PAY);
//    $orderStatus = RechargeOrder::$status;
//    if ($orderStatus == 1) {
//        mylog('order is finished', LOGFN_WX_PAY);
//        return true;
//    } else {
//        if ($orderStatus == 0) {
//            $db->autocommit(false);
//            $successPayResult = RechargeOrder::successPay($transactionId, $openid);
//            $rechargeHandleResult = rechargeHandleFlow(RechargeOrder::$quantity, RechargeOrder::$uid, RechargeOrder::$id, $db, null, $redis);
//            if ($successPayResult && $rechargeHandleResult) {
//                $db->commit();
//                $db->autocommit(true);
//                mylog('order handle finished', LOGFN_WX_PAY);
//                return true;
//            } else {
//                mylog('successPayResult is ' . $successPayResult, LOGFN_WX_PAY);
//                mylog('rechargeHandleResult is ' . $rechargeHandleResult, LOGFN_WX_PAY);
//                mylog('order handle failed the error is ' . $db->errstr(), LOGFN_WX_PAY);
//                $db->rollback();
//                return false;
//            }
//        } else {
//            mylog('some where is errored', LOGFN_WX_PAY);
//            return false;
//        }
//    }
}

/**根据用户uid获取三方绑定的昵称
 *
 * @param $uid  用户id
 *
 * @channel  渠道
 *
 * @param $db
 *
 * @return array|bool
 */
function getThreeNick( $uid, $channel, $db )
{
	if ( empty( $uid ) || empty( $channel ) )
	{
		return false;
	}
	$res = $db->field( 'nick' )->where( "uid=$uid and channel='$channel'" )->limit( 1 )->select( 'three_side_user' );
	if ( false !== $res )
	{
		if ( empty( $res ) )
		{
			return array();
		}
		else
		{
			return $res[0]['nick'];
		}
	}
	else
	{
		return false;
	}
}

function getLiveInfoByStream( $stream, $db )
{
	if ( empty( $stream ) )
	{
		return false;
	}
	$res = $db->field( 'liveid,uid' )->where( "stream='$stream'" )->order( 'ctime desc' )->limit( 1 )->select( 'live' );
	if ( false !== $res )
	{
		if ( empty( $res ) )
		{
			return array();
		}
		else
		{
			return $res;
		}
	}
	else
	{
		return false;
	}
}

function getFictitiousViewCount( $currentViewCount, $enterCount, $config = null )
{
//	if($enterCount < 0){
//		$k = 5;
//		if($currentViewCount < 65 && $currentViewCount > 40 ){
//			$k = 5;
//		}elseif($currentViewCount > 65 && $currentViewCount < 1000){
//			$k = 15;
//		}elseif($currentViewCount > 1000){
//			$k = 15;
//		}
//	}
//	else{
//		$k = 3;
//		if($currentViewCount <= 10)
//		{
//			$k = 5;
//		}elseif($currentViewCount <= 15 ){
//			$k = rand(6, 7);
//		}elseif($currentViewCount <=1000){
//			$k = rand(15,20);
//		}else{
//			$k = rand(5,10);
//		}
//	}

	$k = 1;
	if ( $currentViewCount <= 0 )
	{
		return 1;
	}

//	return $currentViewCount + $k * $enterCount;
	return $k * $currentViewCount;
}

function updateStreamRecordStatus( $liveid, $stream, $status, $db )
{

	if ( !in_array( $status, array( 0, 1, 2, 3 ) ) )
	{
		return false;
	}

	$sql = "select status,id from liveStreamRecord where liveid=$liveid and stream='$stream'";//
	$res = $db->query( $sql );
	$row = $res->fetch_assoc();

	$utime    = date( 'Y-m-d H:i:s', time() );
	$recordid = $row['id'];
	if ( !$recordid )
	{
		return false;
	}
//    dong_log('状态', json_encode(array('status' => $status)), $db);
	if ( $row['status'] == LIVE_CLIENT_CREATE && $status > 1 )
	{//对于正在创建中的流 在进行推流结束回调以及录像生成回调 不更改状态
		$sql = "update liveStreamRecord set `utime`='$utime',`etime`='$utime'  where id=$recordid";
	}
	else
	{
		if ( $status == 2 )
		{
			$sql = "update liveStreamRecord set `status`=$status,`utime`='$utime',`etime`='$utime'  where id=$recordid";
		}
		else
		{
			if ( $status == 1 )
			{
				$sql = "update liveStreamRecord set `status`=$status,`utime`='$utime',`stime`='$utime'  where id=$recordid";
			}
			else
			{
				$sql = "update liveStreamRecord set `status`=$status,`utime`='$utime'  where id=$recordid";
			}
		}

	}

//	if($status > 1){
//		$sql = "update liveStreamRecord set `status`=$status,`utime`='$utime' where liveid=$liveid  and  stream='$stream' and status<".LIVE_CLIENT_CREATE;
//	}

//    $sql = "update liveStreamRecord set `status`=$status,`utime`='$utime' where liveid=$liveid  and  stream='$stream' and status<".LIVE_CLIENT_CREATE;
//    $sql = $db->where("liveid=$liveid  and  stream='$stream'")->update('liveStreamRecord', array('status' => $status, 'utime' => date('Y-m-d H:i:s', time())), 1);
//    $res = $db->where("liveid=$liveid  and  stream='$stream'")->update('liveStreamRecord', array('status' => $status, 'utime' => date('Y-m-d H:i:s', time())));
	$res       = $db->query( $sql );
	$affectRow = $db->affectedRows;
	log_for_net( '成功sql语句', array( 'sql' => $sql ), $db );
	Log_for_net( '影响行数', array( 'hangshu' => $affectRow ), $db );
	if ( false !== $res )
	{
		return true;
	}
	else
	{
		return false;
	}
}


function getCidByUid( $uid, $db )
{
	if ( empty( $uid ) )
	{
		return false;
	}
	$res = $db->field( 'cid' )->where( "uid=$uid" )->limit( 1 )->select( 'anchor' );
	if ( false !== $res )
	{
		if ( empty( $res ) )
		{
			return 0;
		}
		else
		{
			return $res[0]['cid'];
		}
	}
	else
	{
		return 0;
	}
}

function getRobotHeadList( $luid, \redishelp $redisObj )
{
	$env = $GLOBALS['env'];
	$key = $env . "_robotHeadList-$luid";

	return $redisObj->smembers( $key );
}

function getUserBindChannel( $uid, $db )
{
	$sql = "select channel from channel_user where uid =$uid";
	$res = $db->query( $sql );
	$row = $res->fetch_assoc();

	return (int)$row['channel'] ? (int)$row['channel'] : '7001';
}

function getUserBindStatus( $uid, $channel, $db )
{
	$uid     = (int)$uid;
	$channel = (int)$channel;
	$sql     = "SELECT `uid` FROM `channel_user` WHERE `uid` = '{$uid}' AND `channel` = '{$channel}' LIMIT 1";
	$res     = $db->query( $sql );
	$row     = $res->fetch_assoc();

	return isset( $row['uid'] ) ? true : false;
}

function bindUserChannel( $uid, $channel, $db )
{

	//注册渠道
	if ( !$channel )
	{
		//6间房
		$channel = isset( $_COOKIE['datamain'] ) ? ( trim( $_COOKIE['datamain'] ) == '6cn' ? 7001 : 0 ) : 0;
	}

	if ( getUserBindStatus( $uid, $channel, $db ) )
	{
		return true;
	}

	$sql = "insert into channel_user (uid,channel) value($uid, $channel)";

	return $db->query( $sql );
}


function getUserBindInfo( $uid, $db )
{
	$sql = "select uid,channel,promocode from channel_user where uid =$uid";
	$res = $db->query( $sql );

	return $row;
}


function bindUserChannelInfo( $uid, $promocode, $channel, $db )
{

	$res = getUserBindInfo( $uid, $db );
	if ( empty( $res ) )
	{
		$sql                    = "insert into channel_user (uid,channel,promocode) value(:uid, :channel,:promocode)";
		$bindParam['uid']       = $uid;
		$bindParam['channel']   = $channel;
		$bindParam['promocode'] = $promocode;

		return $db->execute( $sql, $bindParam );
	}

	return false;
}

function updateUserPromocode( $uid, $promocode, $db )
{
	$sql                    = "UPDATE  channel_user SET  promocode=:promocode WHERE  uid=:uid LIMIT 1";
	$bindParam['uid']       = $uid;
	$bindParam['promocode'] = $promocode;

	return $db->execute( $sql, $bindParam );
}

function isBindChannelExist( $channel, $db )
{
	$sql = "select channel from admin_channel_version where channel=$channel";
	$res = $db->query( $sql );
	$row = $res->fetch_assoc();
	if ( (int)$row['channel'] )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function dump_error_info()
{
	error_reporting( E_ALL );
	ini_set( 'display_errors', '1' );
}

function hp_commonMsgDecode( $msgGz )
{
	$msg = hp_base64Decode( $msgGz );
	$msg = gzinflate( $msg );

	return $msg;
}

function hp_commonMsgEncode( $msg )
{
	$msgGz = gzdeflate( $msg, 6 );
	$msgGz = hp_base64Encode( $msgGz );

	return $msgGz;
}

function hp_base64Decode( $str )
{
	$base64Str = str_replace( array( '(', ')', '@' ), array( '+', '/', '=' ), $str );
	$base64Str = base64_decode( $base64Str );

	return $base64Str;
}

function hp_base64Encode( $str )
{
	$base64Str = base64_encode( $str );
	$base64Str = str_replace( array( '+', '/', '=' ), array( '(', ')', '@' ), $base64Str );

	return $base64Str;
}

/**
 * 检索进程pid 是否还活着
 *
 * @param unknown $pid
 *
 * @return boolean
 */
function isRunning( $pid )
{
	$isRunning = false;
	if ( posix_kill( intval( $pid ), 0 ) )
	{
		$isRunning = true;
	}

	return $isRunning;
}

function hp_getRequestChannelID()
{
	$channelID = $_POST['channelID'] ?? $_COOKIE['channelID'] ?? 0;

	return intval( $channelID );
}

function hp_getRequestPromoCode()
{
	$promocode = $_POST['promocode'] ?? $_COOKIE['promo_code'];

	return $promocode;
}

function hp_getRechargeActive( $orderid, $activeid = 1, $isOn = true )
{
	//TODO 先用函数替代，代以后规划号之后，写成类进行控制，目前只有一个活动

	if ( !$isOn )
	{
		return [
			"isActivityOn" => 0
		];
	}

	$isActivityOn = 0;

	$packNoticeList = [ "每日首冲\"超值礼包\"", "每月首冲\"尊贵礼包\"" ];

	$mark        = [ 0, 0 ];
	$indexResult = [ 'isDayFirst' => 0, "isMonthFirst" => 1 ];

	$packEvent = new \service\pack\PackEvnentService();
	$result    = $packEvent->isDayOrMonthExchange( $orderid );

//	$result = ['isDayFirst' => false, 'isMonthFirst' => true];

	foreach ( $result as $key => $value )
	{
		if ( $value )
		{
			$index        = $indexResult[$key];
			$mark[$index] = 1;
			$isActivityOn = 1;
		}
	}


	$notice = [];
	if ( $isActivityOn )
	{
		foreach ( $mark as $key => $value )
		{
			if ( $value )
			{
				array_push( $notice, $packNoticeList[$key] );
			}
		}

		$notice = "获得" . implode( "+ ", $notice ) . ", 请到\"我的背包\"查看礼物";

		return [
			"isActivityOn" => 1,
			"activityIds"  => [ $activeid ],
			"activityInfo" => [
				[
					"activityId"   => $activeid,
					"isDayFirst"   => intval( $result['isDayFirst'] ),
					"isMonthFirst" => intval( $result['isMonthFirst'] ),
					"notice"       => $notice
				]
			]
		];
	}
	else
	{
		return [
			"isActivityOn" => 0
		];
	}
}
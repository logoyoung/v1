<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/4/20
 * Time: 20:29
 */

namespace lib;


class RedisCacheManage
{
	const CLEAR_QUEUE = "HP_REDIS_CLEAR_QUEUE";

	const REDIS_TYPE_HASH = "hash";
	const REDIS_TYPE_LIST = 'list';
	const REDIS_TYPE_STRING = 'string';
	const REDIS_TYPE_SET = 'set';
	const REDIS_TYPE_SORTEDSET = 'sortedset';


	public static function addToClearQueue( array $info, \RedisHelp $redisHelp )
	{
		$redisHelp->lpush( self::CLEAR_QUEUE, json_encode( $info ) );
	}
}
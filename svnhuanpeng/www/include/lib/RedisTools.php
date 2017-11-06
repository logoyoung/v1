<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/4/24
 * Time: 下午11:52
 */

namespace lib;


class RedisTools
{
	const REDIS_BETWEEN_MINUTE = 60;

	const REDIS_BETWEEN_HOUR = 3600;

	const REDIS_BETWEEN_DAY = 24 * 3600;

	const REDIS_BETWEEN_WEEK = 7 * 24 * 3600;

	const KEY_CONNECTOR = "_";

	const FIELD_CONNECTOR = ":";

	public function getRedisKey( array $pre, int $between, $connector )
	{
		$key = implode( $connector, $pre ) . $connector;
	}

	public function getRedisExpire( $between )
	{

	}


	public function getRedisField( array $keys, string $connector = RedisTools::FIELD_CONNECTOR )
	{
		return implode( $connector, $keys );
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/15
 * Time: 下午8:05
 */

namespace lib\room;


class FictitiousViewerRule
{
	const ROOM_REAL_ROBOT_COUNT = 20; //每个房间真实robot最大总数

	const ROOM_HEAD_SHOW_NUMBER = 20;

	private function _rule()
	{
		$rule = [
			[
				'range'     => [ 0, 15 ],
				'multiply'  => 3,
				'waveRange' => [ 1, 2 ],//波动范围
				'addTime'   => 2,//增加每个人所需时间
				'subTime'   => 3,
				'line'      => 0
			],
			[
				'range'     => [ 15, 50 ],
				'multiply'  => 5,
				'waveRange' => [ 2, 3 ],
				'addTime'   => 10,
				'subTime'   => 8,
				'line'      => 0.8
			],
			[
				'range'     => [ 50, -1 ],
				'multiply'  => 5,
				'waveRange' => [ 10, '@param/2' ],
				'addTime'   => 10,
				'subTime'   => 8,
				'line'      => 0.8
			]
		];

		return $rule;
	}

	private function _compile( $str )
	{
		$params = func_get_args();
		array_shift( $params );

		$match = [];

		if ( preg_match( "@param", $str, $match ) )
		{
			$match = $match[0];
			if ( count( $match ) == count( $params ) )
			{
				foreach ( $params as $param )
				{
					$str = preg_replace( '/@param/', $param, $str, 1 );

					return eval( "return " . $str . ";" );
				}
			}
		}

		return false;
	}

	public function getRule( $realViewer, $fictitiousViewer )
	{
		$rule = $this->_rule();
		$tmp  = [];

		foreach ( $rule as $key => $value )
		{
			$max = $value[1];
			$min = $value[0];
			if ( $max == -1 )
			{
				if ( $realViewer >= $min )
				{
					$tmp                 = $value;
					$tmp['waveRange'][1] = $this->_compile( $value['waveRange'][1], $realViewer );
				}
				else
				{
					return false;
				}
			}
			else
			{
				if ( $realViewer >= $min && $realViewer <= $max )
				{
					$tmp = $value;
				}
			}
		}

		$tmp['realRobot'] = self::ROOM_HEAD_SHOW_NUMBER;

		return $tmp;
	}

}

class FictitiousViewer
{
	const REDIS_KEY_VIEWER_INFO = "ROOM_VIEWER_INFO";

	private $_viewerInfo = [];

	private $_redis;

	private $_rule;

	public function __construct( \RedisHelp $redisHelp )
	{
		$this->_redis = $redisHelp;
		$this->_rule  = new FictitiousViewerRule();
	}

	//定义redis 以及规则数据格式
	public function init( $luid, $realViewer, $fictitiousViewer )
	{
		$rule = $this->_getViewerRule( $realViewer, $fictitiousViewer );
		if ( !$rule )
		{
			return false;
		}

		$field = [ 'md', 'waveRange', 'addTime', 'subTime', 'line' ];

		foreach ($field as $value)
		{
			$this->_viewerInfo[$luid][$value] = $rule[$value];
		}

		$this->_save();
	}

	private function getInfo( $luid )
	{
		if ( !$this->_viewerInfo[$luid] )
		{
			$viewInfo = $this->_redis->hget( self::REDIS_KEY_VIEWER_INFO, $luid );

			if ( $viewInfo && $viewInfo = json_decode( $viewInfo, true ) )
			{
				$this->_setInfo( $luid, $viewInfo );
			}
			else
			{
				return false;
			}
		}

		return $this->_viewerInfo;
	}

	private function _setInfo( $luid, $info )
	{
		$this->_viewerInfo[$luid] = $info;
	}

	private function _save()
	{
		foreach ( $this->_viewerInfo as $luid => $info )
		{
			$this->_redis->hset( self::REDIS_KEY_VIEWER_INFO, $luid, json_encode( $info ) );
		}
	}


	private function _getViewerRule( $realViewer, $fictitiousViewer )
	{
		$rule = $this->_rule->getRule( $realViewer, $fictitiousViewer );

		if ( $rule )
		{

			$rule['md']   = $rule['multiply'] * $realViewer - $fictitiousViewer;
			$rule['line'] = intval( $rule['md'] * $rule['line'] );

			return $rule;
		}

		return false;
	}
}


//todo 在创建一个类 在robot活动期间 来判定应该进行什么操作
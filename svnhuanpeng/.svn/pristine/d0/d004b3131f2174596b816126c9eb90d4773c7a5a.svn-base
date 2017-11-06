<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/6/20
 * Time: 15:47
 */
namespace lib;
use system\HttpHelper;


class LiveCheck
{
	//
	static $timeout = 180;
	//
	static $requestInterval = 10;
	//
	static $historyTime = 5;
	//
	static $portal = '6_huanpeng';
	//
	static $key = 'F7C55786CE31EF9';
	//
	static $node = 'liverecord';
	//
	static $domainConf = [
		'DEV' => [
			'push' => 'dev-urtmp.huanpeng.com',
			'pull' => 'dev-drtmp.huanpeng.com',
		],
		'PRE' => [
			'push' => 'pre-urtmp.huanpeng.com',
			'pull' => 'pre-drtmp.huanpeng.com',
		],
		'PRO' => [
			'push' => 'urtmp.huanpeng.com',
			'pull' => 'drtmp.huanpeng.com',
		],
	];
	//
	static $api = 'http://qualiter.wscdns.com/api/streamStatusStatistic.jsp';



	public function getPushStreams( $channel='' )
	{
		$params = [
			'n' => self::$portal,
			'r' => time(),
			'k' => md5( time().self::$key ),
			'u' => self::$domainConf[$GLOBALS['env']]['push'],
			'g' => self::$requestInterval,
			//'t' => self::$historyTime,
			'd' => 'push',
		];
		if( !empty($channel) )
		{
			$params['channel'] = $channel;
		}

		$curl = new HttpHelper();
		$curl->addPost( self::$api, $params);
		$results = $curl->getResult();
		$results = $this->dataFormat($results[0]);
		return $results;
	}

	public function getPullStreams( $channel='' )
	{
		$params = [
			'n' => self::$portal,
			'r' => time(),
			'k' => md5( time().self::$key ),
			'u' => self::$domainConf[$GLOBALS['env']]['pull'],
			'g' => self::$requestInterval,
			//'t' => self::$historyTime,
			'd' => 'pull',
		];
		if( !empty($channel) )
		{
			$params['channel'] = $channel;
		}
		$curl = new HttpHelper();
		$curl->addGet( self::$api, $params);
		$results = $curl->getResult();
		$results = $this->dataFormat($results[0]['content']);
		return $results;
	}

	public function dataFormat( $data )
	{
		$data = json_decode( $data, true );
		$data = $data['dataValue'];
		$results = array_map(function ($v){
			$stream = $v['streamname'];
			return basename($stream);
		},$data);

		return $results;
	}
}
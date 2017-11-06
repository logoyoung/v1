<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/16
 * Time: 下午4:09
 */


namespace service\robot;

use lib\room\FictitiousViewer;


class RobotControl
{
	private $_db;
	private $_redis;

	private $_viewObj;

	public function __construct($db,$redis)
	{
		if($db)
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new \DBHelperi_huanpeng();
		}

		if($redis)
		{
			$this->_redis = $redis;
		}
		else
		{
			$this->_redis = new \RedisHelp();
		}

		$this->_viewObj = new FictitiousViewer($this->_redis);
	}


}
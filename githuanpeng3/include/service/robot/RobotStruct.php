<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/16
 * Time: 下午4:25
 */

namespace service\robot;


class RobotStruct
{
	private $_db;
	private $_redis;

	private $_robotList = [];

	private $_spareRobot = [];

	public function __construct($db,$redis)
	{
		$this->_db = $db;
		$this->_redis=$redis;

		$this->initRobotList();
	}

	public function initRobotList()
	{
		$result = array();

		$sql = "select nick,pic,userstatic.uid as uid,`level` from userstatic,useractive where username='hpRobot' and userstatic.uid=useractive.uid";
		$res = $this->_db->query( $sql );
		while($row = $res->fetch_assoc())
		{
			$result[$row['uid']]['nick'] = $row['nick'];
			$result[$row['uid']]['pic'] = $row['pic'];
			$result[$row['uid']]['level'] = $row['level'];
		}

		$this->_robotList = $result;
		$this->_spareRobot = array_keys($result);
	}


	public function getRobotUid()
	{
		$indexMax = count($this->_spareRobot);
	}


}
<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/6
 * Time: 上午10:51
 */

namespace lib\due\rongCloud;

use system\DbHelper;
use system\RedisHelper;
use Think\Exception;

class RongUser
{

	const DUE_USER_TABLE = 'due_rong_user';

	protected $_uid;
	protected $_db;
	protected $_redis;

	public function __construct( $uid, $db=null, $redis=null )
	{
		$this->_uid = $uid;

		if ( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = DbHelper::getInstance( MYSQL_DATABASE_DUE );
		}

		if ( $redis )
		{
			$this->_redis = $redis;
		}
		else
		{
			$this->_redis = RedisHelper::getInstance( REDIS_DATABASE_HUANPENG );
		}
	}

	private function _getUserTableName()
	{
		return self::DUE_USER_TABLE;
	}

	/**
	 * 设置融云token
	 *
	 * @param $token
	 *
	 * @return bool
	 */
	public function setToken( $token )
	{
		$tableName = $this->_getUserTableName();

		$data = [
			'uid'   => $this->_uid,
			'token' => $token,
			'token1'=> $token
		];

		$sql = "insert into $tableName (uid,token) VALUE (:uid,:token) on duplicate key update token=:token1";

		try
		{
			return $this->_db->execute( $sql, $data );
		} catch ( Exception $e )
		{
			var_dump($this->_db->errorInfo());
			return false;
		}
	}

	public function getToken()
	{
		$tableName = $this->_getUserTableName();

		$data = ['uid'=>$this->_uid];

		$sql = "select token from $tableName where uid=:uid";

		$result = $this->_db->query($sql,$data);
		//var_dump($result);
		return $result[0]['token'];
	}
}
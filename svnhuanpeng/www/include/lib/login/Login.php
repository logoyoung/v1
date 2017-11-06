<?php
namespace lib\login;
use Think\Exception;

/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/4
 * Time: 14:54
 */
class Login
{
	private $db;

	private $uid;

	/**
	 * @param mixed $uid
	 */
	public function setUid( $uid )
	{
		$this->uid = $uid;
	}

	/**
	 * @return \system\MysqlConnection
	 */
	public function getDb()
	{
		if ( !$this->db )
		{
			$this->db = \system\DbHelper::getInstance('huanpeng');
		}

		return $this->db;
	}

	public function getToken()
	{
		$data=['uid'=>$this->uid];
		$sql = "select encpass from userstatic where uid=:uid";

		try
		{
			$result = $this->getDb()->query($sql, $data);
			write_log($this->uid."login".json_encode($result));
			return $result[0]['encpass'];
		}
		catch (Exception $exception)
		{
			return false;
		}
	}

	public function login()
	{
		$lport = '';
		$lip   = fetch_real_ip( $lport );
		$ltime = get_datetime();
//		var_dump($lip);
		$lip = ip2long($lip);
		$data = [
			'lip'   => $lip,
			'lport' => $lport,
			'ltime' => $ltime,
			'uid' => $this->uid
		];

		$sql = "update useractive set lip=:lip,lport=:lport,ltime=:ltime where uid=:uid";

		try
		{
			$result = $this->getDb()->execute( $sql, $data );

			return $result != 0 ? true : false;
		}
		catch ( Exception $exception )
		{
			//todo log;
			return false;
		}
	}

}
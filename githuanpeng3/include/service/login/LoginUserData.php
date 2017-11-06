<?php
namespace service\login;
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/4
 * Time: 14:43
 */
class LoginUserData
{
	private $uid;
	private $password;

	private $encpass;

	/**
	 * @param mixed $uid
	 */
	public function setUid( $uid )
	{
		$this->uid = $uid;
	}

	/**
	 * @return mixed
	 */
	public function getUid()
	{
		return $this->uid;
	}

	/**
	 * @param mixed $password
	 */
	public function setPassword( $password )
	{
		$this->password = $password;
	}

	/**
	 * @return mixed
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param mixed $encpass
	 */
	public function setEncpass( $encpass )
	{
		$this->encpass = $encpass;
	}

	/**
	 * @return mixed
	 */
	public function getEncpass()
	{
		return $this->encpass;
	}
}
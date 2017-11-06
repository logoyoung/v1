<?php

/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/8
 * Time: 16:02
 */

class AssertLogin
{
	private $data;

	const ERROR_PASSWORD  = '-4061';

	/**
	 * @param mixed $data
	 */
	public function setData( $data )
	{
		$this->data = $data;
	}

	public function errorPassword()
	{
		if($this->data && $this->data['status'] === '0')
		{
			$error = $this->data['content'];

			if ( $error['code'] == self::ERROR_PASSWORD )
			{
				return true;
			}
			else
			{

			}

		}
	}
}

class testLoginApi
{
	private $_client;
	private $_mobile;
	private $_password;
	private $_uid;


	const API = 'hantong.huanpeng.com/api/user/logIn.php';


	public function errorPassword()
	{

	}

	public function errorMobile()
	{

	}

	public function Login()
	{

	}

	public function appFirstLogin()
	{

	}

}
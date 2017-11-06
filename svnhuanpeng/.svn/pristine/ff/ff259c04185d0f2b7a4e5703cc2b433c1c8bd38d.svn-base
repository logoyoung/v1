<?php
namespace service\login;
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/1
 * Time: 16:44
 */
class Error
{

	const ERR_PASSWD = -996;

	const ERR_USERNAME = -4058;

	const ERR_CODE = -4031; //极验验证，或者验证码错误

	const ERR_SYSTEM = '';

	private static $errorDesc = [
		self::ERR_PASSWD => '用户名或密码错误',
		self::ERR_USERNAME => '请输入正确的手机号码',
		self::ERR_CODE => '极验验证失败'
	];

	private $code;

	private $desc;


	public function __construct()
	{
		$this->clear();
	}

	/**
	 * @param mixed $code
	 */
	public function setCode( $code )
	{
		$this->code = $code;
	}

	/**
	 * @return mixed
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param mixed $desc
	 */
	public function setDesc( $desc )
	{
		$this->desc = $desc;
	}

	/**
	 * @return mixed
	 */
	public function getDesc()
	{
		return $this->desc;
	}

	public function clear()
	{
		$this->setCode(0);
		$this->setDesc('');
	}

	public function set($code,$desc='')
	{
		$desc = $desc ? $desc : self::$errorDesc[$code];

		$this->setCode($code);
		$this->setDesc($desc);
	}

	public function get()
	{
		return [
			'code' => intval($this->getCode()),
			'desc' => (string)$this->getDesc()
		];
	}
}
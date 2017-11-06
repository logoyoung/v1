<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/8/17
 * Time: 13:28
 */
namespace HP\Op;
use Think\Exception;
class Hplive{

	//log todo
	//error todo
	//
	//当前CDN连接
	static $instance = null;

	//获取CDN对象
	public static function getInstance($class = '')
	{
		if(self::$instance && self::$instance instanceof $class)
			return self::$instance;
		$class = empty($class)?"\\HP\\Op\\Wshelper":$class;
		self::$instance = new $class();
		return self::$instance;
	}
	public static function __callStatic( $method, $args )
	{
		// TODO:
		$instance = self::getInstance();
		if(!method_exists($instance,$method))
		{
			throw new Exception('method ' . $method .' of ' . get_class(self::$instance) . ' not defined');
			return false;
		}
		try{
			// todo some log
			$result = call_user_func_array([$instance,$method],$args);
		}catch (Exception $e){
			//todo some log
			throw $e;
		}
		return $result;
	}

}
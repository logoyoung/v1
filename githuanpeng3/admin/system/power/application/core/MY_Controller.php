<?php
/**
 * adminuser管理类父类
 */
class Admin_Controller extends CI_Controller
{
	public function __construct()
    {
        parent::__construct(); 
		define('PROJECT_ID', 22);
        $this->__checkLogin();
        $this->load->helper('form');

    }

	private function __checkLogin()
	{
		if(!defined("PROJECT_ID")) {
			show_error("没有设置项目ID");
		}
		$this->load->model('verify_mdl');
		$this->verify_mdl->verify();
	}
	
	protected function _log($msg='', $type='')
	{
		$data = array(
			'msg' => $msg,
			'type' => $type,
		);
		$this->load->model('log_mdl');
		$this->log_mdl->insert($data);
	}
}


/**
 * company管理类父类
 */
class Company_Controller extends CI_Controller
{
	public function __construct()
    {
        parent::__construct();
		define('PROJECT_ID', 23);
        $this->__checkLogin();
        if(!isset($_COOKIE['cid']) || $_COOKIE['cid'] <= 0) {
			show_error('没有权限！');
		}
		$this->cid = $_SESSION['cid'];
    }
	
	private function __checkLogin()
	{
		if(!defined("PROJECT_ID")) {
			show_error("没有设置项目ID");
		}
		$this->load->model('verify_mdl');
		$this->verify_mdl->verify();
	}
	
	protected function _log($msg='', $type='')
	{
		$data = array(
			'msg' => $msg,
			'type' => $type,
		);
		$this->load->model('log_mdl');
		$this->log_mdl->insert($data);
	}
}

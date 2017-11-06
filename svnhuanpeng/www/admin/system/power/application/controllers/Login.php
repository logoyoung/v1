<?php
/**
 * 后台登录
 * @author 史建涛
 * @version 1.0 20160327
 */

class Login extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->referer = $this->input->get_post('referer');
        $this->load->model('adminuser/user_mdl');
        $this->load->model('adminuser/login_mdl');

	}

	/**
	 * 登录页
	 */
	function index()
	{
        $this->_redirect();
		$data['referer'] = $this->referer;
        $data['type'] = 1;
		$data['title'] = "欢朋直播后台管理";
		if($_POST) {
		    $type = $this->input->post('type');
		    if($type == 1) {
		        $this->doLogin($data); return;
            } else if($type == 2) {
                $this->company();
            }
        }
		$this->load->view('login', $data);
	}
	
	function company()
	{
        $this->_redirect();
        $data['referer'] = $this->config->config['adminuser_url'] . '?d=company&c=anchor';
        $data['type'] = 2;
        $data['title'] = "欢朋直播后台管理";
        if($_POST) {
            $this->doLogin($data); return;
        }
		$this->load->view('login', $data);
	}
    
    /**
     * 登录验证
     */
	function doLogin($data = array())
	{
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		
		if($email && $password)
		{
            do {
                $admin = $this->user_mdl->getAdminByEmail($email);
				/*
                if ($this->input->post('type') != $admin['type']) {
                    $data['error'] = "用户不存在！";
                    break;
                }
				*/
                if ($admin && ($admin['password'] == md5($password))) {
                    $agent = md5($_SERVER['HTTP_USER_AGENT']);
                    //$_SESSION['admin_id'] = $admin['id'];
                    //$_SESSION['admin_agent'] = $agent;
                    //$_SESSION['admin_nickname'] = $admin['nickname'];
                    //$_SESSION['cid'] = $admin['company_id'];
					setcookie('admin_uid', $admin['id'], time() + 24*3600, '/', 'huanpeng.com');
					setcookie('admin_enc', $agent, time() + 24*3600, '/', 'huanpeng.com');
					setcookie('admin_type', $admin['type'], time() + 24*3600, '/', 'huanpeng.com');
					setcookie('admin_name', $admin['nickname'], time() + 24*3600, '/', 'huanpeng.com');
					setcookie('cid', $admin['company_id'], time() + 24*3600, '/', 'huanpeng.com');
                    $arr = array(
                        'admin_id' => $admin['id'],
                        'session_id' => time(),
                        'agent' => $agent,
                        'stime' => date('Y-m-d H:i:s'),
                    );
        
                    $role = $this->login_mdl->replace($arr);
					if($admin['type'] == 2) {
						$this->referer = 'http://' . $_SERVER["HTTP_HOST"] . '/system/broker/view/anchorList.php';
					}
                    $this->_redirect(true);
                } else {
                    $data['error'] = "用户名或密码不正确，请重新输入！";
                    break;
                }

            }while(false);
		} else {
            $data['error'] = "用户名或密码不能为空，请重新输入！";
        }
		$data['title'] = "欢朋直播后台管理";
		$data['referer'] = $this->referer;
        $this->load->view('login', $data);
	}
	
	protected function _redirect($type = false)
	{
        $this->referer = $this->config->config['adminuser_url'] . '?d=adminuser&c=user';
		if(!empty($_COOKIE['admin_uid']) || $type) {
            redirect($this->referer);
		} 
	}
	
	/**
     * 退出登陆
     */
    function logout()
    {
		$this->login_mdl->del($_COOKIE['admin_uid']);
        foreach($_COOKIE as $k=>$v) {
            setcookie($k, '', time() - 3600, '/', 'huanpeng.com');
		}
        redirect('?c=login');
    }
}
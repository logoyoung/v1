<?php
/**
 * 后台权限验证
 * @author sjt
 */
class Verify extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('adminuser/user_mdl');
        $this->load->model('adminuser/role_mdl');
        $this->load->model('adminuser/login_mdl');
        $this->load->model('adminuser/control_mdl');
	}
    
	function index()
	{
		$user = $this->user_mdl->get($this->input->get('id'));
		$project_id = $this->input->get('project_id');
		$url = $this->input->get('url');
		if(!$user) {
			echo json_encode(array('code'=> 1, 'msg'=>"用户不存在")); exit;
		}
		$ua = urldecode($this->input->get('ua'));
		$agent = $this->input->get('agent');
		$login = $this->login_mdl->get($this->input->get('id'));
		if($agent != md5($this->config->config['secret'] . $ua) || $agent != $login['agent']) {
			echo json_encode(array('code'=> 100, 'msg'=> 'SESSION不对应')); exit;
		}
		$role = $this->role_mdl->get($user['role']);
		if(!$role || !$role['control']) {
			echo json_encode(array('code'=> 2, 'msg'=>"用户组不存在")); exit;
		}
		$control = unserialize($role['control']);
		if(empty($control) || !in_array($project_id, $control)){
			echo json_encode(array('code'=> 4, 'msg'=>"没有权限1")); exit;
		}
		
		$control_list = $this->control_mdl->getListByParent($project_id);
		if(empty($control_list)) {
			echo json_encode(array('code'=> 5, 'msg'=>"没有权限2")); exit;
		}
		$power = array();
		$view = false;
		foreach($control_list as $k=>$v) {
			if(in_array($v['id'], $control)) {
				$power[] = $v['url'];
				if(strstr($url, $v['url']) !== false) {
					$view = true;
				}
			}
		}
		if($view) {
			echo json_encode(array('code'=> 200, 'msg'=>json_encode($power))); exit;
		} else {
			echo json_encode(array('code'=> 6, 'msg'=>"没有权限3")); exit;
		}
	}
}
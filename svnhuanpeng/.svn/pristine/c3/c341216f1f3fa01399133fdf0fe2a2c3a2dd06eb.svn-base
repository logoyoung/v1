<?php
/**
 * 后台用户验证的model
 * @author shijiantao
 */
class Verify_mdl extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
    }
    
    public function verify()
	{

		$referer = 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '?' . $_SERVER['QUERY_STRING'];
		$login = $this->config->config['adminuser_url'] . '?c=login';
		if(!isset($_COOKIE['admin_uid']) || !isset($_COOKIE['admin_enc']) || !isset($_COOKIE['admin_name'])){
			$login .= "&referer=" . urlencode($referer);
			header("Location: " . $login); exit;
        }
		$id = $_COOKIE['admin_uid'];
		$user = $this->db->where('id', $id)->get('admin_user_user')->row_array();
		if(!$user) {
			show_error('用户不存在');
		}
		$ua = $_SERVER['HTTP_USER_AGENT'];
		$agent = $_COOKIE['admin_enc'];
		$login = $this->db->select('*')->where('admin_id' , $id)->get('admin_user_login')->row_array();
		if($agent != md5($ua) || $agent != $login['agent']) { //登录验证失败，重新登录
			foreach($_COOKIE as $k=>$v) {
                setcookie($k, '', time() - 3600, '/', 'huanpeng.com');
			}
			header('Location: ' . $login . '&referer=' . urlencode($referer)); exit;
		}
		$role = $res = $this->db->select('*')->where('id', $user['role'])->get('admin_user_role')->row_array();
		if(!$role || !$role['control']) {
			show_error('用户组不存在');
		}
		//var_dump($role);
		$control = unserialize($role['control']);
		if(empty($control) || !in_array(PROJECT_ID, $control)){
			show_error('没有权限1');
		}
		
		$control_list = $this->db->select('*')->where('parent_id', PROJECT_ID)->where('status', 1)->get('admin_user_control')->result_array();
		if(empty($control_list)) {
			show_error('没有权限2');
		}
		$power = array();
		$view = false;
		foreach($control_list as $k=>$v) {
			if(in_array($v['id'], $control)) {
				$power[] = $v['url'];
				if(strstr($referer, $v['url']) !== false) {
					$view = true;
				}
			}
		}
		if(!$view) {
			show_error('没有权限3');
		}
	}
    
}
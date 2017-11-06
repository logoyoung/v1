<?php
/**
 * @filename HuanPengAdmin.php
 * @desc 后台管理员,用来验证是否有权限使用当前功能
 * 使用方式：
 *   HuanPengAdmin::checkLogin()
 *  说明：
 *   检查用户登录状态及权限
 * 使用方式：
 *   HuanPengAdmin::getUserInfo()
 *  说明：
 *   获取昵称和登录用户ID
 * 使用方式：
 *   HuanPengAdmin::getUrlOfManage()
 *  说明：
 *   获取登录及修改密码的地址
 */
if($_SERVER['HTTP_HOST'] == 'localhost') {
	define('DOMAIN', 'http://localhost/huanpeng/admin/system/power/index.php');
} else {
	define('DOMAIN', 'http://' . $_SERVER['HTTP_HOST'] . '/system/power/index.php');
	define('DOMAIN2', 'http://' . $_SERVER['SERVER_ADDR'] . '/system/power/index.php');
} 
class HuanPengAdmin
{
	private $db;
	private $power = array();
	public function __construct($db = null) 
	{
		defined("PROJECT_ID") or exit("没有设置项目ID");
		if ($db) {
            $this->db = $db;
		} else {
            $this->db = new DBHelperi_admin();
		}
	}
	
	/**
	 * 检查用户登录状态及权限
	 */
	static public function checkLogin()
	{
		$referer = 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '?' . $_SERVER['QUERY_STRING'];
		$login = $this->config->config['adminuser_url'] . '?c=login';
		if(!isset($_COOKIE['admin_uid']) || !isset($_COOKIE['admin_enc']) || !isset($_COOKIE['admin_name'])){
			$login .= "&referer=" . urlencode($referer);
			header("Location: " . $login); exit;
        }
		$id = $_COOKIE['admin_uid'];
		$user = $this->db->where('id', $id)->select('admin_user_user');
		if(!$user) {
			exit('用户不存在');
		}
		$user = $user[0];
		
		$ua = $_SERVER['HTTP_USER_AGENT'];
		$agent = $_COOKIE['admin_enc'];
		$login_info = $this->db->where('admin_uid' , $id)->select('admin_user_login');
		if($agent != md5($this->config->config['secret'] . $ua) || (isset( $login_info[0]['agent']) && $agent != $login_info[0]['agent'])) {
			foreach($_COOKIE as $k=>$v) {
                setcookie($k, '', time() - 3600, '/', 'huanpeng.com');
			}
			header('Location: ' . $login . '&referer=' . urlencode($referer)); exit;
		}
		$role = $this->db->where('id', $user['role'])->select('admin_user_role');
		if(!$role || !isset($role[0]['control']) || !$role[0]['control']) {
			exit('用户组不存在');
		}
		$control = unserialize($role[0]['control']);
		if(empty($control) || !in_array(PROJECT_ID, $control)){
			show_error('没有权限1');
		}
		
		$control_list = $this->db->where('parent_id', PROJECT_ID)->where('status', 1)->select('admin_user_control');
		if(empty($control_list)) {
			show_error('没有权限2');
		}

		$view = false;
		foreach($control_list as $k=>$v) {
			if(in_array($v['id'], $control)) {
				$this->power[] = $v['url'];
				if(strstr($referer, $v['url']) !== false) {
					$view = true;
				}
			}
		}
		if(!$view) {
			exit('没有权限3');
		}
	}

	/**
	 * 获取当前登录用户的信息
	 */
	static public function getUserInfo() {
		$adminInfo = array(
			'id'=>$_COOKIE['admin_uid'],
			'nickname'=>$_COOKIE['admin_name']
		);
		return $adminInfo;
	}

	/**
	 * 获取当前登录用户的权限
	 */
	static public function getPower() {
		return $this->$power;
	}

	/**
	 * 得到退出、修改密码等URL
	 * @return array("logout"=>"logouturl","changePassword"=>"manageurl")
	 */
	static public function getUrlOfManage()
	{
		return array(
			"logout" => DOMAIN . '?c=login&m=logout',
			"changePassword" => DOMAIN . '?d=adminuser&c=modify',
		);
	}
}
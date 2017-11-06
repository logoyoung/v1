<?php
/**
 * 登录验证
 * $power = new Power();
 * $power->checkLogin();
 */
class Power
{
	private $power;
	private $domain;
	private $project_id; 
	public function __construct($db = null)
    {
		session_start();
        if ($db) {
            $this->db = $db;
		} else {
			$this->db = new DBHelperi_admin();
		}
		$this->domain = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/';
		$this->project_id = PROJECT_ID;
    }
	
	public function checkLogin()
	{
		$referer = $this->domain . $_SERVER["REQUEST_URI"] . ($_SERVER['QUERY_STRING'] ? ('?' . $_SERVER['QUERY_STRING']) : '');
		$login = $this->domain . 'system/power/index.php?c=login';
		if(!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_agent']) || !isset($_SESSION['admin_nickname'])){
			$login .= "&referer=" . urlencode($referer);
			header("Location: " . $login); exit;
        }
		$id = $_SESSION['admin_id'];
		$user = $this->db->field("*")->where("id={$id}")->select('admin_user_user');
		if(empty($user)) {
			error(1003);
		}
		$user = $user[0];
		$ua = $_SERVER['HTTP_USER_AGENT'];
		$agent = $_SESSION['admin_agent'];
		$login = $this->db->field("*")->where("admin_id={$id}")->select('admin_user_login');
		if(empty($login) || $agent != md5($ua) || $agent != $login[0]['agent']) {
			session_destroy();
			header('Location: ' . $login . '&referer=' . urlencode($referer)); exit;
		}
		
		$role = $this->db->field("*")->where("id={$user['role']}")->select('admin_user_role');
		if(empty($role) || !$role[0]['control']) {
			error(1040);
		}
		
		$control = unserialize($role[0]['control']);
		if(empty($control) || !in_array($this->project_id, $control)){
			error(1041);
		}
		
		$control_list = $this->db->field("id, url")->where("parent_id={$this->project_id}")->select('admin_user_control');
		if(empty($control_list)) {
			error(1042);
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
			error(1042);
		}
	}
	
	public function getPower()
	{
		return $this->power;
	}
}

$power = new Power();
$power->checkLogin();
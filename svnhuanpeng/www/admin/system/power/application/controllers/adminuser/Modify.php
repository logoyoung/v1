<?php
/**
 * 后台用户修改密码
 * @author 
 * @version 1.0
 */
class Modify extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('adminuser/user_mdl');
	}
    
    /**
     * 修改密码
     *
     */
	function index()
	{
		do {
			$data = array();
            if ($_POST) {
                $password = $this->input->post('old_password');
				$new_password = $this->input->post('new_password');
				$new_password2 = $this->input->post('new_password2');
				$nickname = $this->input->post('nickname');

                if ($nickname == '') {
                    $data['error'] = '昵称不能为空';
                    break;
                }
				$len = strlen($new_password);
                if ($len < 6 || $len > 12) {
                    $data['error'] = '密码长度应该在6-12之间';
                    break;
				}
				if ($new_password != $new_password2) {
                    $data['error'] = '两次输入密码不一致';
                    break;
                }
				$user = $this->user_mdl->get($_SESSION['admin_id']);
				if (!$user) {
                    $data['error'] = '用户不存在';
                    break;
                }
				if ($user['password'] != md5($password)) {
                    $data['error'] = '当前密码不正确';
                    break;
                }
				
				$update = array(
					'password' => md5($new_password),
					'nickname' => $nickname,
				);
				$row = $this->user_mdl->update($update, $_SESSION['admin_id']);
                redirectJs('?c=login&m=logout', '操作成功，请重新登录！');
            }
        }while(false);
		
		$this->load->view('adminuser/modify', $data);
	}
}
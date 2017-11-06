<?php
/**
 * 后台用户管理
 * @author 
 * @version 1.0
 */
class User extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('adminuser/user_mdl');
        $this->load->model('adminuser/role_mdl');
	}
    
    /**
     * 用户角色列表
     *
     */
	function index()
	{
		$role = $this->role_mdl->getList();
		$arr = [];
		foreach($role as $k=>$v) {
			$arr[$v['id']] = $v['des'];
		}
		$data['role'] = $arr;
		
		$perpage = 10;
		$page = $this->input->post_get('per_page') ? $this->input->post_get('per_page') : 1;
		
		$offset = ($page - 1) * $perpage;
		$user = $this->user_mdl->getList($perpage, $offset);
		
		$data['user_list'] = $user['result'];
        $data['config'] = $this->__getConfig();
		$config['base_url'] = $this->config->config['adminuser_url'] . '?d=adminuser&c=user' . ((isset($user['para']) && $user['para']) ? $user['para'] : '');
		$config['total_rows'] = $data['total']= $user['rows'];
		$config['per_page'] = $perpage;
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
		$this->load->view('adminuser/user_list', $data);
	}
	
    /**
     * 添加、更新用户信息
     *
     */
	function edit()
	{
        $data = array();
		$id = trim(strip_tags($this->input->post_get('id')));

		do {
            if ($_POST) {
                $data['mobile'] = trim(strip_tags($this->input->post('mobile')));
                $data['email'] = trim(strip_tags($this->input->post('email')));
                $data['real_name'] = trim(strip_tags($this->input->post('real_name')));
                $data['nickname'] = trim(strip_tags($this->input->post('nickname')));
				$data['type'] = $this->input->post('type');
				$data['company_id'] = $this->input->post('company_id');
                $data['role'] = $this->input->post('role');

                if (!$this->isValidEmail($data['email'], $id)) {
                    $data['user'] = $_POST;
                    $data['error'] = '无效的邮箱！';
                    break;
                }

                if ($id) {
                    if ($this->input->post('password') != '') {
                        $data['password'] = md5($this->input->post('password'));
                    }
                    $data['utime'] = date('Y-m-d H:i:s');
                    $rows = $this->user_mdl->update($data, $id);
                } else {
                    if ($this->input->post('password') == '') {
                        $data['password'] = md5('123456');
                    } else {
                        $data['password'] = md5($this->input->post('password'));
                    }
                    $data['nickname'] = trim(strip_tags($this->input->post('nickname')));

                    $rows = $this->user_mdl->add($data);
                }
                if($rows == 1) {
                    redirectJs('?d=adminuser&c=user', '操作成功');
                } else {
                    redirectJs('?d=adminuser&c=user', '操作失败');
                }
            } else {
				if ($id) {
					$data['user'] = $this->user_mdl->get($id);
				}
			}
        }while(0);
		$data['config'] = $this->__getConfig();
		$data['role_list'] = $this->role_mdl->getList();
		$data['company_list'] = $this->user_mdl->getCompanyList();
		$this->load->view('adminuser/user_edit',$data);
	}
    
    /**
     * 删除用户
     *
     * @param int $id  用户id
     */
	function del()
	{
		$id = $this->input->post_get('id');
		$rows = $this->user_mdl->del($id);
		
		if($rows == 1) {
            redirectJs('?d=adminuser&c=user', '操作成功');
		} else {
            redirectJs('?d=adminuser&c=user', '操作失败');
		}
	}

	function isValidEmail($email = '', $id = 0)
    {
        if($email) {
            $admin = $this->user_mdl->getAdminByEmail($email);
            if($admin && $admin['id'] != $id){
                return false;
            }
        } else {
			return false;
		}
        return true;
    }

    private function __getConfig()
    {
        $config['type'] =  array(
                        1 => "普通管理员",
                        2 => "经纪公司管理人员"
                    );
        
        return $config;
    }
}
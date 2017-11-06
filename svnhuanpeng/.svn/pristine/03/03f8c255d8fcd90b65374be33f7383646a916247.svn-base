<?php
/**
 * 后台角色管理
 * @author 
 * @version 1.0
 */
class Role extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('adminuser/role_mdl');
        $this->load->model('adminuser/control_mdl');
	}
    
    /**
     * 用户角色列表
     *
     */
	function index()
	{
		$data['role_list'] = $this->role_mdl->getList();
		$this->load->view('adminuser/role_list',$data);
	}
	
    /**
     * 添加、更新角色信息
     *
     */
	function edit($uid = 0)
	{
        $data = array();
		$id = $this->input->post_get('id');
		
		if($_POST) {
			$data['des'] = trim(strip_tags($this->input->post('des')));
			$data['control'] = serialize($this->input->post('control', 0));
			$data['operater'] = $_SESSION['admin_id'];
			
			if($id) {
				$data['utime'] = date('Y-m-d H:i:s');
				$rows = $this->role_mdl->update($data, $id);
			} else {
				$rows = $this->role_mdl->add($data);
			}
			if($rows == 1) {
                redirectJs('?d=adminuser&c=role', '操作成功');
			} else {
                redirectJs('?d=adminuser&c=role', '操作失败');
			}
		}
		
		if($id) {
			$data['role'] = $this->role_mdl->get($id);
			if(isset($data['role']['control'])) {
				$data['role']['control'] = unserialize($data['role']['control']);
			}
		}
		$data['child_list'] = $this->control_mdl->getChildList();
		$data['parent_list'] = $this->control_mdl->getParentList();
		$this->load->view('adminuser/role_edit', $data);
	}
    
    /**
     * 删除角色
     *
     * @param int $id
     */
	function del()
	{
		$id = $this->input->post_get('id');
		$data['status'] = 2;
		$rows = $this->role_mdl->update($data, $id);
		
		if($rows == 1) {
            redirectJs('?d=adminuser&c=role', '操作成功');
		} else {
            redirectJs('?d=adminuser&c=role', '操作失败');
		}
	}
}
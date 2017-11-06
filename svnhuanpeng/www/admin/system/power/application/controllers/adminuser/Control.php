<?php
/**
 * 后台权限管理
 * @author 
 * @version 1.0
 */
class Control extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('adminuser/control_mdl');
	}
    
    /**
     * 权限列表
     *
     */
	function index()
	{
		$data['child_list'] = $this->control_mdl->getChildList();
		$data['parent_list'] = $this->control_mdl->getParentList();
		
		$this->load->view('adminuser/control_list',$data);
	}
	
    /**
     * 添加、更新权限
     *
     */
	function edit()
	{
        $data = array();
		$id = $this->input->post_get('id');
		if($_POST) {
			$data['name'] = trim(strip_tags($this->input->post('name')));
            $data['url'] = trim(strip_tags($this->input->post('url')));
			$data['parent_id'] = trim(strip_tags($this->input->post('parent_id', 0)));
			$data['operater'] = $_SESSION['admin_id'];
			
			if($id) {
				$data['utime'] = date('Y-m-d H:i:s');
				$rows = $this->control_mdl->update($data, $id);
			} else {
				$rows = $this->control_mdl->add($data);
			}
			if($rows == 1) {
                redirectJs('?d=adminuser&c=control', '操作成功');
			} else {
                redirectJs('?d=adminuser&c=control', '操作失败');
			}
		}
		if($id) {
			$data['control'] = $this->control_mdl->get($id);
		}
		$data['parent_list'] = $this->control_mdl->getParentList();
	
		$this->load->view('adminuser/control_edit', $data);
	}
    
    /**
     * 删除权限
     *
     * @param int $id  权限id
     */
	function del()
	{
		$id = $this->input->post_get('id');
		
		$data['status'] = 2;
		$rows = $this->control_mdl->update($data, $id);
		
		if($rows == 1) {
            redirectJs('?d=adminuser&c=control', '操作成功');
		} else {
            redirectJs('?d=adminuser&c=control', '操作失败');
		}
	}
}
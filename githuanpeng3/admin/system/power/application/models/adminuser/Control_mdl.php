<?php
/**
 * 权限的model
 * @author shijiantao
 */
class Control_mdl extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
		$this->table = 'admin_user_control';
    }
    
	/**
	 * 获取权限列表
	 * @return array  
	 */
    public function getChildList()
	{
		$res = $this->db->select('*')
			->where('parent_id>', 0)
			->where('status', 1)
			->get($this->table)
			->result_array();

		return $res;
	}
	
	/**
	 * 获取项目权限列表
	 * @return array  
	 */
    public function getParentList()
	{
		$res = $this->db->select('*')
			->where('parent_id', 0)
			->where('status', 1)
			->get($this->table)
			->result_array();

		return $res;
	}

	/**
	 * 根据项目获取权限
	 * @return array
	 */
    public function getListByParent($parent_id)
	{
		$res = $this->db->select('*')
			->where('parent_id', $parent_id)
			->where('status', 1)
			->get($this->table)
			->result_array();

		return $res;
	}
	
	/**
	 * 根据id获取详情
	 * @return array  
	 */
    public function get($id = 0)
	{
		$res = $this->db->select('*')
			->where('id', $id)
			->get($this->table)
			->row_array();

		return $res;
	}
    
    
	/**
	 * 添加权限
	 * @param array $arr
	 * @return int
	 */
	public function add($arr = array())
	{
		if(!empty($arr)) {
			$this->db->insert($this->table , $arr);
			return $this->db->affected_rows();
		} else {
			return 0;
		}
	}
	
	/**
	 * 更新权限详情
	 *
	 * @param array $arr
	 * @param int $id
	 * @return int 0失败 1成功
	 */
	public function update($arr = array(), $id = 0)
	{
		if(!empty($arr) && $id) {
			$this->db->where('id', $id)->update($this->table, $arr);
			return $this->db->affected_rows();
		} else {
			return 0;
		}
	}
}
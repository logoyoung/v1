<?php
/**
 * 后台用户验证的model
 * @author shijiantao
 */
class Login_mdl extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
		$this->table = 'admin_user_login';
    }
    
	/**
	 * 取得用户登录信息
	 *
	 * @param int $admin_id
	 * @return array  
	 */
    public function get($admin_id = 0)
	{
		$res = $this->db->select('*')
			->where('admin_id' , $admin_id)
			->get($this->table)
			->row_array();

		return $res;
	}
    
    
	
	/**
	 * 更新用户登录信息
	 *
	 * @param array $arr
	 * @param int $uid
	 * @return int
	 */
	public function replace($arr = array())
	{
		$this->db->replace($this->table, $arr);
	}
	
	/**
	 * 删除用户登录信息
	 *
	 * @param int $uid
	 * @return int
	 */
	public function del($uid = 0)
	{
		$this->db->delete($this->table, array('admin_id' => $uid));
	}
	
}
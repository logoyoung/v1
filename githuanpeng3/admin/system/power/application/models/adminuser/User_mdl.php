<?php
/**
 * 后台用户的model
 * @author shijiantao
 */
class User_mdl extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
		$this->table = 'admin_user_user';
    }
    
	/**
	 * 取得用户信息
	 *
	 * @param string $username
	 * @return array  
	 */
    public function getAdminByEmail($email = '')
	{
		$res = $this->db->select('*')
			->where('email' , $email)
			->get($this->table)
			->row_array();

		return $res;
	}
    
    
	/**
	 * 取得用户信息
	 *
	 * @param int $uid
	 * @return object
	 */
	public function get($uid = 0)
	{
		$res = $this->db->where('id', $uid)->get($this->table)->row_array();
        
        return $res;
	}
    
    
	/**
	 * 添加用户
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
	 * 更新用户信息
	 *
	 * @param array $arr
	 * @param int $uid
	 * @return int
	 */
	public function update($arr = array(), $uid=0)
	{
		if(!empty($arr) && $uid) {
			$this->db->where('id', $uid)->update($this->table, $arr);
			return $this->db->affected_rows();
		} else {
			return 0;
		}
	}
	
	/**
	 * 删除用户
	 *
	 * @param array $arr
	 * @param int $uid
	 * @return int
	 */
	public function del($uid = 0)
	{
		if(!empty($uid)) {
			$this->db->delete($this->table, array('id' => $uid));
			return $this->db->affected_rows();
		} else {
			return 0;
		}
	}
	
	
	/**
	 * 获取用户列表
	 *
	 * @return array
	 */
	public function getList($perpage = 10, $offset = 0)
	{
		$this->db->select('*');
		$para = '';
		if($email = $this->input->get_post('email')) {
			$this->db->like('email', $email);
			$para .= '&email=' . $email;
		}
		if($mobile = $this->input->get_post('mobile')) {
			$this->db->where('mobile', $mobile);
			$para .= '&mobile=' . $mobile;
		}
		if($real_name = $this->input->get_post('real_name')) {
			$this->db->like('real_name', $real_name);
			$para .= '&real_name=' . $real_name;
		}
		if($nickname = $this->input->get_post('nickname')) {
			$this->db->like('nickname', $nickname);
			$para .= '&nickname=' . $nickname;
		}
		if($role = $this->input->get_post('role')) {
			$this->db->where('role', $role);
			$para .= '&role=' . $role;
		}
		
		$db = clone($this->db);
		$res['rows'] = $this->db->get($this->table)->num_rows();
		$res['result'] = $db->get($this->table, $perpage, $offset)->result_array();
		$res['para'] = $para;
		return $res;
	}
	
	/**
	 * 获取经纪公司列表
	 * @return array  
	 */
    public function getCompanyList()
	{
		$res = $this->db->select('id, name')
			->where('status', 0)
			->get('company')
			->result_array();

		return $res;
	}
}
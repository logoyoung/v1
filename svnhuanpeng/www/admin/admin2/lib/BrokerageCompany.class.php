<?php
/**
 * 经纪公司
 *
 */
class BrokerageCompany
{
	private $db;
    public function __construct($db = null)
    {
        if ($db) {
            $this->db = $db;
		} else {
			$this->db = new DBHelperi_admin();
		}
    }
	
	/**
     * 获取经纪公司列表
     *
     */
	function getList()
	{
		$res = $this->db->field('id,name,type,status')->select('company');
		if ($res) {
			return $res;
		} else {
			return array();
		}
	}
	
	/**
     * 获取经纪公司详情
     *
     */
	function getDetail()
	{
		$res = $this->db->field('id,name,type,status')->where("id={$id}")->select('company');
		if ($res) {
			return $res[0];
		} else {
			return array();
		}
	}
	
	/**
     * 添加/编辑经纪公司信息
     *
     */
	function edit()
	{
		
	}
	
	/**
     * 删除经纪公司
     *
     */
	function del()
	{
		
	}
	
	/**
     * 经纪公司的配置信息
     *
     */
	function getConfig()
	{
		$config['type'] = array(
			0 => '官方',
			1 => '经纪公司',
			2 => '工会'
		);
		
		return $config;
	}
}

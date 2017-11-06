<?php
/**
 * 游戏类型管理
 * @author jiantao@6room.com
 * date 2017-03-30
 */
class GameType
{
	private $db;
    public function __construct($db = null, $cid = 0)
    {
        if ($this->db) {
            $this->db = $this->db;
		} else {
			$this->db = new DBHelperi_admin();
		}
    }
	
	/**
	 * 获取游戏类型列表  数据不多，不分页
	 */
	public function getList()
	{
		$data = array();
		$list = $this->db->field('gametid,name,icon')->select('gametype');
		if ($list !== false) {
			foreach ($list as $k=>$v) {
				$list[$k]['icon'] = !empty($v['icon']) ? "http://" . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . '/' . $v['icon'] : '';
			}
		}
		$sql_total = "SELECT count(*) as total
				FROM gametype";
		$total = $this->db->doSql($sql_total);
		$data['count'] = isset($total['total']) ? $total['total'] : 0;
		$data['list'] = $list;
		return $data;
	}
	
	/**
	 * 获取游戏类型详情
	 */
	public function get()
	{
		$gametid = isset($_GET['gametid']) ? trim($_GET['gametid']) : '';
		$gametype = $this->db->field('gametid,name,icon')->where("gametid=$gametid")->select('gametype');
		if (empty($gametid)) {
			return false;
		}
		$data = array();
		if ($res !== false) {
			$data['gametype'] = $gametype[0];
			$data['gametype']['icon'] = !empty($data['gametype']['icon']) ? "http://" . $conf['domain-img'] . '/' . $data['gametype']['icon'] : '';
		} else {
			header("HTTP/1.1 404 not found"); exit;
		}
		return $data;
	}
	
	/**
	 * 更新/添加游戏类型  有gameid就更新，没有就添加
	 * @return boolean
	 */
	function edit() 
	{
		$typename = isset($_POST['typename']) ? trim($_POST['typename']) : '';
		$img = isset($_POST['img']) ? trim($_POST['img']) : '';
		$gametid = isset($_POST['gametid']) ? trim($_POST['gametid']) : '';
		if (empty($typename)) {
			error(-1007);
		}
		$data = array(
			'name' => $typename,
			'icon' => $img
		);
		if(is_numeric($gameid)) {
			$res = $this->db->where("gametid=$gametid")->update('gametype', $data);  // 更新
		} else {
			$res = $this->db->insert('gametype', $data);
		}

		return $res === false ? $res : true;
	}
	
	/**
	 * 删除游戏类型
	 * @return boolean
	 */
	function del($gametid) {
		if (empty($gametid)) {
			return false;
		}
		$res = $this->db->where("gametid=$gametid")->delete('gametype');
		return $res === false ? $res : true;
	}
}

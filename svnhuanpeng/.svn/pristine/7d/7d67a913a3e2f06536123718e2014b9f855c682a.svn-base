<?php
/**
 * 游戏管理
 */
class Game
{
	private $this->db;
	public $cross;
    public function __construct($this->db = null, $cid = 0)
    {
        if ($this->db) {
            $this->db = $this->db;
		} else {
			$this->db = new DBHelperi_admin();
		}
		$this->cross = CROSS;
    }
	
	/**
	 * 批量获取游戏列表
     */
	public function getList()
	{
		$gameName = isset($_GET['gameName']) ? trim($_GET['gameName']) : '';
		$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
		$size = 10;
		$offset = ($page - 1) * $size;
		
		$where = '1 and game.status=0';
		if (!empty($gameName)) {
			$where.=" and game.name like binary '%$gameName%'";
		}
		$sql = "SELECT gametid,name,ctime,icon,game_zone.* 
				FROM game 
				JOIN game_zone ON game.gameid=game_zone.gameid
				where $where
				limit $offset, $size";
		
		$list = $this->db->doSql($sql);
		if($list) {
			foreach($list as $k=>$v) {
				$list[$k]['description'] = $v['description'] ? $v['description'] : '暂无简介';
				$list[$k]['icon'] = $v['icon'] ? ('http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . $v['icon']) : $this->cross;
				$list[$k]['bgpic'] = $v['bgpic'] ? ('http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . $v['bgpic']) : $this->cross;
				$list[$k]['poster'] = $v['poster'] ? ('http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . $v['poster']) : $this->cross;
			}
		}
		$sql_total = "SELECT count(*) as total
				FROM game 
				where $where";
		$total = $this->db->doSql($sql_total);
		$data['count'] = isset($total['total']) ? $total['total'] : 0;
		$data['list'] = $list;
		
		return $data;
	}
	
	/**
	 * 获取游戏详情
     */
	public function get()
	{
		$gameid = isset($_GET['gameid']) ? trim($_GET['gameid']) : '';
		if (empty($gameid)) {
			return false;
		}
		$data = array();
		$where = "game.gameid=$gameid";
		$sql = "SELECT gametid,name,ctime,icon,game_zone.* 
				FROM game 
				JOIN game_zone ON game.gameid=game_zone.gameid
				where $where";
		
		$game = $this->db->doSql($sql);
		if($game) {
			$data['game'] = $game[0];
		} else {
			header("HTTP/1.1 404 not found"); exit;
		}
		
		return $data;
	}
	
	/**
	 * 编辑/添加游戏
	 * @author yandong@6room.com
	 * date 2016-06-29  14:51
	 */
	public function edit() 
	{
		$gameid = isset($_POST['gid']) ? trim($_POST['gid']) : '';  //游戏ID

		if (!$gametid = isset($_POST['gametid']) ? (int) ($_POST['gametid']) : '') {
			error(-1007);
		} else {
			$data['gametid'] = $gametid;
		}
		if ($poster = isset($_POST['poster']) ? trim($_POST['poster']) : '') {
			$datazone['poster'] = $poster;
		}
		if ($icon = isset($_POST['icon']) ? trim($_POST['icon']) : '';) {
			$datazone['icon'] = $icon;
		}
		if ($bgpic = isset($_POST['bgpic']) ? trim($_POST['bgpic']) : '';) {
			$datazone['bgpic'] = $bgpic;
		}
		$datazone['description'] = isset($_POST['description']) ? trim($_POST['description']) : '';
		
		if (!$gameid) {  //增加
			$data = array('name' => $gname, 'gametid' => $gtype);
			$gameid = $this->db->insert('game', $data);
			if ($gameid) { 
				$gameid = $res;
				$zone_data['gameid'] = $gameid;
				$res = $this->db->insert('game_zone', $zone_data);
			}
		} else { 
			$res = $this->db->where('gameid=' . $gameid)->update('game', $data);
			if($res) {
				$res = $this->db->where('gameid=' . $gameid)->update('game_zone', $datazone);
			}
		}
	}

	/**
	 * 删除游戏 [逻辑删除] 只更改状态
	 * @param type $gameid 游戏id
	 * @param type $db
	 * @return boolean
	 */
	public function del()
	{
		$gameid = isset($_POST['gameid']) ? trim($_POST['gameid']) : '';  //游戏ID
		if (empty($gameid)) {
			return false;
		}
		$res = $db->where("gameid=$gameid")->update('game', array('status'=>1));
		if ($res !== false) {
			$zres = $db->where("gameid=$gameid")->update('game_zone', array('status'=>1));
			if ($zres !== false) {
				return true;
			} else {
				return false;
			}
		}
		return false;
		
	}
}

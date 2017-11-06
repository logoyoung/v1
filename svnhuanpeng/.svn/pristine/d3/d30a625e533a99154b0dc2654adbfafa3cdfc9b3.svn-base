<?php
class Anchor
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
     * 根据条件查询主播
     *
     */
	public function searchList()
	{
		//可以通过昵称、UID、房号、姓名、手机号搜索
		$perpage = isset($_REQUEST['perpage']) ? (int)$_REQUEST['perpage'] : 10;
		$data['page'] = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
		$offset = ($data['page'] - 1) * $perpage;
		$join = '';
		$where = ' where 1=1 ';
		$uid = isset($_REQUEST['userid']) ? (int)$_REQUEST['userid'] : 0;
		if($uid) {
			$where .= " and anchor.uid={$uid}";
		}
		$cid = isset($_REQUEST['cid']) ? (int)$_REQUEST['cid'] : 0;
        if($cid >= 0) {
            $where .= " and anchor.cid={$cid}";
        }
		
		$nick = isset($_REQUEST['nick']) ? filterWords($_REQUEST['nick']) : '';
		$name = isset($_REQUEST['name']) ? filterWords($_REQUEST['name']) : '';
		if($nick || $phone) {
			$join = ' LEFT JOIN userstatic ON anchor.uid=userstatic.uid ';
			if($nick) {
				$where .= " and userstatic.nick LIKE '%{$nick}%' ";
			}
			if($name) {
				$where .= " and userstatic.username LIKE '%{$name}%' ";
			}
			if($phone = $_REQUEST['phone']) {
				$where .= " and userstatic.phone LIKE '{$phone}%' ";
			}
		}
        if($name) {
			$join = ' LEFT JOIN userrealname ON anchor.uid=userrealname.uid ';
			$where .= " and userrealname.name LIKE '%{$name}%' ";
		}
		$roomid = isset($_REQUEST['roomid']) ? (int)$_REQUEST['roomid'] : 0;
		if($roomid) {
			$join = ' LEFT JOIN roomid ON anchor.uid=roomid.uid ';
			$where .= " and roomid.roomid={$roomid} ";
		}
		
		$sql_count = 'SELECT count(anchor.uid) as total from anchor ' . $join . $where;
		$res_count = $this->db->doSql($sql_count);
		$data['total'] = $res_count[0]['total'];
		
		$sql_list = 'SELECT anchor.uid as uid,anchor.cid as cid from anchor ' . $join . $where . " order by anchor.uid desc limit {$offset}, {$perpage}";
		$list = $this->db->doSql($sql_list);
		$month = isset($_REQUEST['month']) ? trim($_REQUEST['month']) : date('Y-m');
		if($list) {
			$uids = array_column($list, 'uid');
			$info = $this->anchorInfo($uids);
			$room = $this->anchorRoomID($uids);
			$length = $this->anchorLiveLength($uids, $month);
			$popular = $this->anchorPopular($uids, $month);
            $first = $this->anchorFirstDay($uids);
			$valid = $this->anchorValidDay($uids, $month);
            $real = $this->anchorRealInfo($uids);
			foreach($list as $k=>$v) {
				$list[$k]['pic'] = (isset($info[$v['uid']]['pic']) && $info[$v['uid']]['pic']) ? ('http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain-avatar'] . $info[$v['uid']]['pic']) : DEFAULT_PIC;
				$list[$k]['nick'] = isset($info[$v['uid']]['nick']) ? $info[$v['uid']]['nick'] : '';
				$list[$k]['roomID'] = isset($room[$v['uid']]['roomid']) ? $room[$v['uid']]['roomid'] : '无';
				//$res[$k]['beans'] = isset($v['totalbeans']) ? $v['totalbeans'] : 0;
				//$res[$k]['money'] = isset($money[$v['uid']]['totalmoney']) ? $money[$v['uid']]['totalmoney'] : 0;
				$list[$k]['popularoty'] = isset($popular[$v['uid']]['popular']) ? $popular[$v['uid']]['popular'] : 0;
				$list[$k]['length'] = isset($length[$v['uid']]['length']) ? SecondFormat($length[$v['uid']]['length']) : SecondFormat(0);
				$list[$k]['first'] = isset($first[$v['uid']]['ctime']) ? $first[$v['uid']]['ctime'] : '无';
				$list[$k]['valid'] = isset($valid[$v['uid']]['total']) ? $valid[$v['uid']]['total'] : '0';
                $list[$k]['atime'] = isset($real[$v['uid']]['ctime']) ? $real[$v['uid']]['ctime'] : '0';
                $list[$k]['realName'] = isset($real[$v['uid']]['name']) ? $real[$v['uid']]['name'] : '0';
                $list[$k]['papersid'] = isset($real[$v['uid']]['papersid']) ? $real[$v['uid']]['papersid'] : '0';
			}
		}
		$data['list'] = $list;
		
		return $data;
		
	}
	
	/**
     * 主播详情
     *
     */
	public function anchorDetail()
	{
		$uid = $_REQUEST['uid'];
		$user = $this->db->field('uid')->where("uid={$uid}")->select('anchor');
		if(!$user) {
			exit('404');
		}
		$data['userinfo'] = $this->anchorInfo($uid);
		$date = isset($_REQUEST['date']) ? trim($_REQUEST['date']) : '';
		if(!$date) {
			$date = date('Y-m');
		}
		if($date != date('Y-m')) {
			$days = date('t', strtotime($date));
		} else {
			$days = (int)date('d');
		}
		$data['selected_date'] = $date;
		$data['date'] = '';
		for($i = 1; $i <= $days; $i++) {
			$data['date'] .= '"' . $i . '日",';
		}
		$data['date'] = trim($data['date'], ',');
		
		//取人气
		$popular = $this->anchorPopular($uid, $date);
		$data['popular'] = '';
		$data['top_popular'] = 0;
		if($popular) {
			$tmp = array();
			foreach($popular as $k=>$v) {
				$tmp[(int)$v['day']] = $v['popular'];
				$data['top_popular'] = $v['popular'] > $data['top_popular'] ? $v['popular'] : $data['top_popular'];
			}
			for($day = 1; $day <= $days; $day++) {
				if(isset($tmp[$day])) {
					$data['popular'] .=  $tmp[$day]. ',';
				} else {
					$data['popular'] .= '0,';
				}
			}
			$data['popular'] = trim($data['popular'], ',');
		}
		
		//取直播时长
		$length = $this->anchorLiveLength($uid, $date);
		
		$data['length'] = '';
		$data['total_length'] = 0;
		if($length) {
			$tmp = array();
			foreach($length as $k=>$v) {
				$tmp[(int)$v['day']] = $v['length'];
			}
			for($day = 1; $day <= $days; $day++) {
				if(isset($tmp[$day])) {
					$data['length'] .=  round($tmp[$day]/3600, 2) . ',';
					$data['total_length'] += $tmp[$day];
				} else {
					$data['length'] .= '0,';
				}
			}
			$data['length'] = trim($data['length'], ',');
			$data['total_length'] = secondFormat($data['total_length']);
		}
		
		return $data;
	}
	
	/**批量获取用户昵称头像
     * @param $uids  用户id列表
     * @param $db
     */
	public function anchorInfo($uids)
	{
		$list = array();
		if(is_array($uids)){
			$uids = implode(',',$uids);
			$res = $this->db->field("uid,nick,pic")
			->where("uid in ($uids)")
			->select('userstatic');

			if($res){
				foreach ($res  as $v){
					$list[$v['uid']] = $v;
				}
			}
		} else {
			$res = $this->db->field("uid,nick,pic")
			->where("uid={$uids}")
			->select('userstatic');
			if($res){
				$list = $res[0];
			}
		}
		return $list;
	}

    /**批量获取主播首播日期
     * @param $uids  用户id列表
     * @param $db
     */
	public function anchorFirstDay($uids)
	{
		$list = array();
		if(is_array($uids)){
			$uids = implode(',',$uids);
			$res = $this->db->field("uid, min(ctime) as ctime")
			->where("uid in ($uids) group by uid")
			->select('live');

			if($res){
				foreach ($res  as $v){
					$list[$v['uid']] = $v;
				}
			}
		} else {
			$res = $this->db->field("min(ctime) as ctime")
			->where("uid={$uids}")
			->select('live');
			if($res){
				$list = $res[0];
			}
		}
		return $list;
	}    
	
	/**批量获取主播有效播出天数
     * @param $uids  用户id列表
     * @param $db
     */
	public function anchorValidDay($uids, $month)
	{
        $mstart	= $month . '-01';
		$mend = $month . '-31';
		$list = array();
		if(is_array($uids)){
			$uids = implode(',', $uids);
			$res = $this->db->field("uid, count(*) as total")
				->where("uid in ({$uids}) and length>=3600 and date >='{$mstart}' and date <='{$mend}' group by uid")
				->select('live_length');
			if($res){
				foreach ($res as $v){
					$list[$v['uid']] = $v;
				}
			}
		} else {
			$sql = "select uid, count(length) as total from live_length
				 where uid={$uids} and length>=3600 and date>='{$mstart}' and date<='{$mend}'";
			$res = $this->db->doSql($sql);
			if($res){
				$list = $res;
			}
		}
		return $list;
	}
	
    /**批量获取主播房间id
     * @param $uids  主播id列表
     * @return array
     */
	public function anchorRoomID($uids)
	{
		if(is_array($uids)){
			$uids = implode(',',$uids);
		}
		$res = $this->db->field("uid,roomid")
			->where("uid in ($uids)")
			->select('roomid');
		$list = array();
		if($res){
			foreach ($res  as $v){
				$list[$v['uid']] = $v;
			}
		}
		
		return $list;
	}
    
    /**批量获取主播身份信息
     * @param $uids  主播id列表
     * @return array
     */
	public function anchorRealInfo($uids)
	{
		if(is_array($uids)){
			$uids = implode(',',$uids);
		}
		$res = $this->db->field("uid,name,papersid,ctime")
			->where("uid in ($uids)")
			->select('userrealname');
		$list = array();
		if($res){
			foreach ($res  as $v){
				$list[$v['uid']] = $v;
			}
		}
		
		return $list;
	}

    /**获取主播在线时长
     * @param $uids
     * @param $month 年和月份  2017-02
     * @return array|bool
     */
	public function anchorLiveLength($uids, $month)
	{
		if(empty($uids) || empty($month)){
			return false;
		}
		$mstart	= $month . '-01';
		$mend = $month . '-31';
		$list = array();
		if(is_array($uids)){
			$uids = implode(',', $uids);
			$res = $this->db->field("uid,sum(length) as length")
				->where("uid in ({$uids})  and date >='{$mstart}' and date <='{$mend}'  group by uid")
				->select('live_length');
			if($res){
				foreach ($res as $v){
					$list[$v['uid']] = $v;
				}
			}
		} else {
			$sql = "select uid, length, DATE_FORMAT(`date`, '%d') as day from live_length
				 where uid={$uids} and date>='{$mstart}' and date<='{$mend}' order by date asc";
			$res = $this->db->doSql($sql);
			if($res){
				$list = $res;
			}
		}
		return $list;
	}

    /**获取主播人气
     * @param $uids
     * @param $month
     * @return array|bool
     */
    public function anchorPopular($uids, $month)
	{
        if(empty($uids) || empty($month)){
            return false;
        }
		$list = array();
		$mstart = $month . '-01 00:00:00';
		$mend = $month . '-31 23:59:59';
        if(is_array($uids)){
            $uids=implode(',', $uids);
			$res = $this->db->field("uid,max(popular) as popular")
				->where("uid in({$uids}) and ctime>='{$mstart}' and ctime<='{$mend}' group by uid ")
				->select('anchor_most_popular');
			
			if($res){
				foreach ($res as $v){
					$list[$v['uid']] = $v;
				}
			}
        } else {
			$sql = "select uid, max(popular) as popular, DATE_FORMAT(`ctime`, '%d') as day
					from anchor_most_popular
					where uid={$uids} and ctime >='{$mstart}' and ctime <='{$mend}' 
					group by DATE_FORMAT(`ctime`, '%Y%m%d') order by ctime desc";
			$res = $this->db->doSql($sql);
			if($res){
				$list = $res;
			}
		}
		return $list;
    }
	
}

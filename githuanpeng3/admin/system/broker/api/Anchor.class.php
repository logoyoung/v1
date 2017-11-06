<?php
/**
 * 经纪公司后台管理
 */
class Anchor
{
	private $db;
	private $cid;
    public function __construct($db = null, $cid = 0)
    {
		if(!$cid) {
			exit("404");
		} else {
			$this->cid = $cid;
		}
        if ($db) {
            $this->db = $db;
		} else {
			$this->db = new DBHelperi_admin();
		}
    }
	
	/**
	 * 批量获取用户信息列表
     */
	public function anchorList()
	{
		$data['date'] = isset($_REQUEST['date']) ? trim($_REQUEST['date']) : date('Y-m');

		$perpage = isset($_REQUEST['perpage']) ? $_REQUEST['perpage'] : 10;
		$data['page'] = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
		
		$count = $this->db->field('count(*) as total')->where('cid=' . $this->cid)->select('anchor');
		$data['count'] = isset($count[0]['total']) ? $count[0]['total'] : 0;
		$anchor = $this->db->field('uid')->where('cid=' . $this->cid)->limit($data['page'], $perpage)->select('anchor');
		if($anchor) {
			$uids = array();
			foreach($anchor as $k=>$v) {
				$uids[] = $v['uid'];
			}
			$info = $this->anchorInfo($uids);
			$room = $this->anchorRoomID($uids);
			$length = $this->anchorLiveLength($uids, $data['date']);
			$popular = $this->anchorPopular($uids, $data['date']);
			foreach($anchor as $k=>$v) {
				$anchor[$k]['pic'] = $info[$v['uid']]['pic'] ? ('http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . $info[$v['uid']]['pic']) : 'http://www.huanpeng.com/static/img/userface.png';
				$anchor[$k]['nick'] = $info[$v['uid']]['nick'];
				$anchor[$k]['roomid'] = isset($room[$v['uid']]['roomid']) ? $room[$v['uid']]['roomid'] : '无';
				//$res[$k]['beans'] = isset($v['totalbeans']) ? $v['totalbeans'] : 0;
				//$res[$k]['money'] = isset($money[$v['uid']]['totalmoney']) ? $money[$v['uid']]['totalmoney'] : 0;
				$anchor[$k]['popular'] = isset($popular[$v['uid']]['popular']) ? $popular[$v['uid']]['popular'] : 0;
				$anchor[$k]['length'] = isset($length[$v['uid']]['length']) ? SecondFormat($length[$v['uid']]['length']) : SecondFormat(0);
			}
			$data['anchor_list'] = $anchor;
		}
		$data['company'] = $this->companyInfo($this->cid);
		
		return $data; 
	}
	
	/**
     * 主播详情
     *
     */
	function anchorDetail()
	{
		$uid = $_REQUEST['uid'];
		$user = $this->db->field('uid')->where("uid={$uid} and cid={$this->cid}")->select('anchor');
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
		$data['company'] = $this->companyInfo($this->cid);
		
		return $data;
	}
	
	/**批量获取用户昵称头像
     * @param $uids  用户id列表
     * @param $db
     */
	function anchorInfo($uids)
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

    /**批量获取主播房间id
     * @param $uids  主播id列表
     * @return array
     */
	function anchorRoomID($uids)
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

    /**获取主播在线时长
     * @param $uids
     * @param $month 年和月份  2017-02
     * @return array|bool
     */
	function anchorLiveLength($uids, $month)
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
    function anchorPopular($uids, $month)
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
	
	/**根据公司id获取公司信息
     * @param $cid
     * @return array|bool
     */
    function companyInfo($cid){
        if(empty($cid)){
            return false;
        }
        $res=$this->db->field("*")
			->where("id={$cid}")
			->select('company');
		$list = array();
		if($res){
			$list = $res[0];
		}
		
		return $list;
    }
}

<?php
/**
 * 权限的model
 * @author shijiantao
 */
class Anchor_mdl extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
    }
    
	/**
	 * 获取旗下主播列表
	 * @return array  
	 */
	public function getList($cid, $perpage = 10, $offset = 0, $date = '')
	{
		/*
		select uid, sum(giftnum * gift.money) as totalmoney from giftrecordcoin 
			join gift on giftrecordcoin.giftid =gift.id
			where uid in(select uid from anchor where cid=15)
			group by giftrecordcoin.uid
			order by totalmoney desc 
		select anchor.uid, sum(giftnum * gift.money) as totalmoney from giftrecordcoin 
			join gift on giftrecordcoin.giftid =gift.id
		  	right join anchor on giftrecordcoin.uid = anchor.uid
			where anchor.cid=15
			group by giftrecordcoin.uid
			order by totalmoney desc 
		*/
		/*           
		$para = '';
		if($order = $this->input->get_post('orderby')) {
			$orderby = explode('_', $order);
			$para = '&orderby=' . $order;
		} else {
			$orderby = array('totalmoney', '1');
		}
		if($orderby[0] == 'totalmoney') {
			$sql = 'select anchor.uid, liveid, sum(giftnum * gift.money) as totalmoney from giftrecordcoin 
			join gift on giftrecordcoin.giftid =gift.id
		  	right join anchor on giftrecordcoin.uid = anchor.uid
			where anchor.cid=' . $cid . '
			group by anchor.uid
			order by totalmoney ' . ($orderby[1] == 1 ? 'desc' : 'asc') . '
			limit ' . $offset . ',' . $perpage;
			
			$res = $this->db->query($sql)->result_array();
			if($res) {
				$uid = array();
				foreach($res as $k=>$v) {
					$uid[] = $v['uid'];
				}
				$info = $this->getUserInfo($uid);
				$beans = $this->getUserBeans($uid);
				$popular = $this->getUserPopular($uid);
				$length = $this->getUserLength($uid);
				
				foreach($res as $k=>$v) {
					$res[$k]['pic'] = $this->config->config['user_pic'] . $info[$v['uid']]['pic'];
					$res[$k]['nick'] = $info[$v['uid']]['nick'];
					$res[$k]['money'] = isset($v['totalmoney']) ? $v['totalmoney'] : 0;
					$res[$k]['beans'] = isset($beans[$v['uid']]['totalbeans']) ? $beans[$v['uid']]['totalbeans'] : 0;
					$res[$k]['popular'] = isset($popular[$v['uid']]['popular']) ? $popular[$v['uid']]['popular'] : 0;
					$res[$k]['length'] = isset($length[$v['uid']]['totallength']) ? secondFormat($length[$v['uid']]['totallength']) : 0;
				}
			}
		}
		if($orderby[0] == 'totalbeans') {
			$sql = 'select anchor.uid, liveid, sum(giftnum) as totalbeans from giftrecord
		  	right join anchor on giftrecord.uid = anchor.uid
			where anchor.cid=' . $cid . '
			group by anchor.uid
			order by totalbeans ' . ($orderby[1] == 1 ? 'desc' : 'asc') . '
			limit ' . $offset . ',' . $perpage;
			
			$res = $this->db->query($sql)->result_array();
			
			if($res) {
				$uid = array();
				foreach($res as $k=>$v) {
					$uid[] = $v['uid'];
				}
				$info = $this->getUserInfo($uid);
				$money = $this->getUserCoin($uid);
				$popular = $this->getUserPopular($uid);
				$length = $this->getUserLength($uid);
				
				foreach($res as $k=>$v) {
					$res[$k]['pic'] = $this->config->config['user_pic'] . $info[$v['uid']]['pic'];
					$res[$k]['nick'] = $info[$v['uid']]['nick'];
					$res[$k]['beans'] = isset($v['totalbeans']) ? $v['totalbeans'] : 0;
					$res[$k]['money'] = isset($money[$v['uid']]['totalmoney']) ? $money[$v['uid']]['totalmoney'] : 0;
					$res[$k]['popular'] = isset($popular[$v['uid']]['popular']) ? $popular[$v['uid']]['popular'] : 0;
					$res[$k]['length'] = isset($length[$v['uid']]['totallength']) ? secondFormat($length[$v['uid']]['totallength']) : 0;
				}
			}
		}
		*/
		
		$res = $this->db->select('uid')->where('cid', $cid)->get('anchor', $perpage, $offset)->result_array();
				
		if($res) {
			$uid = array();
			foreach($res as $k=>$v) {
				$uid[] = $v['uid'];
			}
			$info = $this->getUserInfo($uid);
			//$money = $this->getUserCoin($uid);
			$popular = $this->getUserPopular($uid, $date);
			$length = $this->getUserLength($uid, $date);
			$room = $this->getRoomId($uid);
			
			foreach($res as $k=>$v) {
				$res[$k]['pic'] = $info[$v['uid']]['pic'] ? ($this->config->config['user_pic'] . $info[$v['uid']]['pic']) : 'http://www.huanpeng.com/static/img/userface.png';
				$res[$k]['nick'] = $info[$v['uid']]['nick'];
				$res[$k]['roomid'] = isset($room[$v['uid']]['roomid']) ? $room[$v['uid']]['roomid'] : '无';
				//$res[$k]['beans'] = isset($v['totalbeans']) ? $v['totalbeans'] : 0;
				//$res[$k]['money'] = isset($money[$v['uid']]['totalmoney']) ? $money[$v['uid']]['totalmoney'] : 0;
				$res[$k]['popular'] = isset($popular[$v['uid']]['popular']) ? $popular[$v['uid']]['popular'] : 0;
				$res[$k]['length'] = isset($length[$v['uid']]['totallength']) ? secondFormat($length[$v['uid']]['totallength']) : 0;
			}
		}
		$para = '';
		if($date) {
			$para = '&date=' . $date;
		}
		$total = $this->db->where('cid', $cid)->get('anchor')->num_rows();
		
		return array(
			'para' => $para, 
			'result' => $res, 
			'rows' => $total
		);
	}

	/**
	 * 获取头像
	 * @return array  
	 */
	public function getUserInfo($uid)
	{
		$this->db->select('uid,nick,pic,phone');

		if(is_array($uid)) {
			$this->db->where_in('uid', $uid);
			$res = $this->db->get('userstatic')->result_array();
			$row = array();
			if ($res) {
	            foreach ($res as $v) {
	                $row[$v['uid']] = $v;
	            }
	        }
		} else {
			$this->db->where('uid', $uid);
			$row = $this->db->get('userstatic')->row_array();

		}
		
		return $row;
	}

	/**
	 * 获取人气峰值
	 * @return array  
	 */
	public function getUserPopular($uid, $date = '')
	{
		if(empty($uid)) {
			return false;
		}
		
		if(is_array($uid)) {
			$this->db->select('uid, max(popular) as popular');
			$this->db->where_in('uid', $uid);
			if($date) {
				$this->db->where("DATE_FORMAT(`ctime`, '%Y-%m')=", $date);
			}
			$this->db->group_by('uid');
			$res = $this->db->get('anchor_most_popular')->result_array();
			$row = array();
			if ($res) {
	            foreach ($res as $v) {
	                $row[$v['uid']] = $v;
	            }
	        }
		} else {
			$this->db->select("uid, max(popular) as popular, DATE_FORMAT(`ctime`, '%d') as day");
			$this->db->where('uid', $uid);
			if($date) {
				$this->db->where("DATE_FORMAT(`ctime`, '%Y-%m')=", $date);
			}
			$this->db->order_by('ctime', 'ASC');
			$this->db->group_by("DATE_FORMAT(`ctime`, '%Y%m%d')");
			$row = $this->db->get('anchor_most_popular')->result_array();
		}
		
		return $row;
	}

	/**
	 * 获取金币收入
	 * @return array  
	 */
	public function getUserCoin($uid)
	{
		if(empty($uid)) {
			return false;
		}		
		$this->db->select('uid, sum(giftnum * gift.money) as totalmoney');
		$this->db->join('gift', 'giftrecordcoin.giftid =gift.id');
		if(is_array($uid)) {
			$this->db->where_in('uid', $uid);
		} else {
			$this->db->where('uid', $uid);
		}
		
		$res = $this->db->group_by('uid')->get('giftrecordcoin')->result_array();
		
		$row = array();
		if ($res) {
            foreach ($res as $v) {
                $row[$v['uid']] = $v;
            }
        }
		return $row;
	}
	
	/**
	 * 获取金豆收入
	 * @return array  
	 */
	public function getUserBeans($uid)
	{
		if(empty($uid)) {
			return false;
		}
		$this->db->select('uid, sum(giftnum) as totalbeans');
		if(is_array($uid)) {
			$this->db->where_in('uid', $uid);
		} else {
			$this->db->where('uid', $uid);
		}
		
		$res = $this->db->get('giftrecord')->result_array();
		
		$row = array();
		if ($res) {
            foreach ($res as $v) {
                $row[$v['uid']] = $v;
            }
        }
		return $row;
	}
	
	/**
	 * 获取直播总时长
	 * @return array  
	 */
	public function getUserLength($uid, $date = '')
	{
		if(empty($uid)) {
			return false;
		}
		if(is_array($uid)) {
			$this->db->select('uid, sum(length) as totallength');
			$this->db->where_in('uid', $uid);
			if($date) {
				$this->db->where("DATE_FORMAT(`date`, '%Y-%m')=", $date);
			}
			$this->db->group_by('uid');
			$res = $this->db->get('live_length')->result_array();
			$row = array();
			if ($res) {
	            foreach ($res as $v) {
	                $row[$v['uid']] = $v;
	            }
	        }
		} else {
			$this->db->select("length, DATE_FORMAT(`date`, '%d') as day");
			$this->db->where('uid', $uid);
			if($date) {
				$this->db->where("DATE_FORMAT(`date`, '%Y-%m')=", $date);
			}
			$this->db->order_by('date', 'ASC');
			$row = $this->db->get('live_length')->result_array();
		}
		
		
		return $row;
	}
	
	/**
	 * 获取主播信息
	 * @return array  
	 */
	public function get($uid, $cid = false)
	{
		if(empty($uid)) {
			return false;
		}
		$this->db->select('*');
		
		$this->db->where('uid', $uid);
		if($cid) {
			$this->db->where('cid', $cid);
		}
		
		$res = $this->db->get('anchor')->row_array();
		return $res;
	}

	public function getRoomId($uid)
	{
		if(empty($uid)) {
			return false;
		}
		$this->db->select('uid, roomid');
		$this->db->where_in('uid', $uid);
		$res = $this->db->get('roomid')->result_array();
		if ($res) {
            foreach ($res as $v) {
                $row[$v['uid']] = $v;
            }
        }
        return $row;
	}
	
	/**
	 * 获取经纪公司列表
	 * @return array  
	 */
    public function getCompany($cid)
	{
		$res = $this->db->select('id, name')
			->where('id', $cid)
			->get('company')
			->row_array();

		return $res;
	}
}
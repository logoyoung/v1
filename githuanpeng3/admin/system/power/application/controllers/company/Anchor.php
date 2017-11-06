<?php
/**
 * 经纪公司后台管理
 * @author 
 * @version 1.0
 */
class Anchor extends Company_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('company/anchor_mdl');
		$this->load->library("curl");
	}
    
    /**
     * 主播列表
     *
     */
	function index()
	{
		
		$data['date'] = $this->input->get_post('date');
		if(!$data['date']) {
			$data['date'] = date('Y-m');
		}
		$perpage = 10;
		$page = $this->input->post_get('per_page') ? $this->input->post_get('per_page') : 1;
		
		$offset = ($page - 1) * $perpage;
		$anchor = $this->anchor_mdl->getList($this->cid, $perpage, $offset, $data['date']);
		$data['anchor_list'] = $anchor['result'];
		
		$config['base_url'] = $this->config->config['adminuser_url'] . '?d=company&c=anchor' . ((isset($anchor['para']) && $anchor['para']) ? $anchor['para'] : '');
		$config['total_rows'] = $data['total'] = $anchor['rows'];
		$config['per_page'] = $perpage;
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
		
		$data['orderby'] = $this->input->get_post('orderby');
		$data['pass_month'] = getPastMonth();
		$data['url'] = $this->config->config['adminuser_url'] . '?d=company&c=anchor';
		$data['url2'] = $this->config->config['adminuser_url'] . '?d=company&c=anchor&m=detail&date=' . $data['date'];
		$data['company'] = $this->anchor_mdl->getCompany($this->cid);
		$this->load->view('company/anchor_list', $data);
	}
	
	
	/**
     * 主播详情
     *
     */
	function detail()
	{
		$data = array();
		$uid = $this->input->get('uid');
		$user = $this->anchor_mdl->get($uid, $this->cid);
		if(!$user) {
			exit('404');
		}
		$data['userinfo'] = $this->anchor_mdl->getUserInfo($uid);
		$date = $this->input->get_post('date');
		if(!$date) {
			$date = date('Y-m');
		}
		if($date != date('Y-m')) {
			$days = date('t',strtotime($date));
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
		$popular = $this->anchor_mdl->getUserPopular($uid, $date);
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
		$length = $this->anchor_mdl->getUserLength($uid, $date);
		
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
		$data['pass_month'] = getPastMonth();
		$data['url'] = $this->config->config['adminuser_url'] . '?d=company&c=anchor&m=detail&uid=' . $uid;
		$data['company'] = $this->anchor_mdl->getCompany($this->cid);
		$this->load->view('company/anchor_detail', $data);
	}
	
}
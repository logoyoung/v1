<?php

namespace Company\Controller;
use HP\Op\Anchor;
use HP\Op\Company;
use HP\Op\Live;

class CompanymanagerController extends BaseController{

    protected $pageSize = 10;

    protected function _access(){
        return [
			'anchorstatistics' => ['index'],
            'checkapply' => ['apply'],
        ];
    }
    
    function __construct()
    {
        parent::__construct();
        $dao = D('AclUser');
        $where['uid'] = \HP\Op\Admin::getUid();
        $user = $dao->where($where)->find();
        if($user['companyid']) {
        	$this->companyId = $user['companyid'];
        } else {
        	$this->companyId = I('get.cid', 0); 
        	if($this->companyId) {
        		cookie('op_company_id', $this->companyId);
        	}
        	if(!$this->companyId && cookie('op_company_id')) {
        		$this->companyId = cookie('op_company_id');
        	}
        }
        if(!$this->companyId) {
        	exit('您不是管理人员，请联系技术人员');
        }
        $this->company = \HP\Op\Company::getCompangInfo()[$this->companyId];//公司名称
    }

   /*  
    * 主播查询列表
    */
    public function index()
    {
        $anchorDao = D('anchor');
        $roomidDao = D('roomid');
        $userstaticDao = D('userstatic');
        $lengthDao = D('liveLength');
		
		$whereStr = ' a.cid=' . $this->companyId . ' ';
        $where['a.cid'] = $this->companyId;
        
        if($id = I('get.uid')){
            $where['a.uid'] = $id;
			$whereStr .= ' and a.uid=' . $id;
        }
        if($username = I('get.username')){
            $where['c.username'] = ['like',"%$username%"];
			$whereStr .= ' and c.username like "%' . $username . '%"';
        }
        if($nick = I('get.nick')){
            $where['c.nick'] = ['like',"%$nick%"];
			$whereStr .= ' and c.nick like "%' . $nick . '%"';
        }
        if($phone = I('get.phone')){
            $where['c.phone'] = ['like',"%$phone%"];
			$whereStr .= ' and c.phone like "%' . $phone . '%"';
        }
        if($roomid = I('get.roomid')){
            $where['b.roomid'] = $roomid;
			$whereStr .= ' and b.roomid=' . $roomid;
        }
        if(!($startTime = I('get.timestart'))) {
            $_GET['timestart'] = $startTime = date('Y-m-01');
        }
		if(!($endTime = I('get.timeend'))) {
            $_GET['timeend'] = $endTime = date('Y-m-d');
        }
		if(!($order = I('get.order'))) {
            $_GET['order'] = $order = 1;
        }
		$orderby = $anchorDao->getOrderSql($order);
        $whereDate = ' `date`>="' . $startTime . '" and `date`<="' . $endTime . '"';

		$sql = "select a.uid,a.level,c.nick,c.username,a.cid,b.roomid,d.length,d.bean,d.coin,e.valid from " . $anchorDao->getTableName() . ' a '
		       . " left join ".$roomidDao->getTableName()." as b on a.uid = b.uid"
			   . " left join ".$userstaticDao->getTableName()." as c on a.uid = c.uid "
			   . " left join (select uid,sum(length)as length,sum(bean) as bean,sum(coin) as coin from " . $lengthDao->getTableName() ." where " . $whereDate . " group by uid)d on a.uid=d.uid "
			   . " left join (select uid,count(*) as valid from " . $lengthDao->getTableName() ." where length>=3600 and " . $whereDate . " group by uid)e on a.uid=e.uid "
			   . " where " . $whereStr
			   . " order by " . $orderby;
			
        if($export = I('get.export')){//导出数据
			//$sql .= ' limit 0,300';
            $results = $anchorDao->query($sql);
        }else{
            $count = $anchorDao
            ->alias(' a ')
            ->join(" left join ".$roomidDao->getTableName()." as b on a.uid = b.uid ")
            ->join(" left join ".$userstaticDao->getTableName()." as c on a.uid = c.uid ")
            ->where($where)->count();
            
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
			$sql .= ' limit ' . $Page->firstRow . ',' . $Page->listRows;
            $results = $anchorDao->query($sql);
            $uids = array_column($results, 'uid');
            
        }
        $anchorPopular = \HP\Op\Anchor::anchorPopular($uids, $startTime, $endTime);//最佳人气
        $first = \HP\Op\Anchor::anchorFirstDay($uids);//首播日期
        $anchorRealInfo = \HP\Op\Anchor::anchorRealInfo($uids);//身份证号
        
        foreach ($results as $result ){
            $data = $result;
            $data['popular'] = $anchorPopular[$result['uid']];
            $data['first'] = $first[$result['uid']];
            $data['companyname'] = $companymap[$result['cid']]['name'];
            $data['realname'] = $anchorRealInfo[$result['uid']]['name'];
            $data['papersid'] = $anchorRealInfo[$result['uid']]['papersid'];
            $data['length'] = secondFormatH($result['length']);
            $data['total'] = floor($result['coin']) + floor($result['bean']);
            $datas[] = $data;
        }
        if($export = I('get.export')){//导出数据
            $excel[] = array('UID','昵称','等级','金币收益','金豆收益','合计（取整）','直播时长','直播间','人气峰值','首播日期','有效天数','真实姓名','身份证号');
            foreach ($datas as $data) {
                $excel[] = array($data['uid'],$data['nick'],$data['level'],$data['coin'],$data['bean'],$data['total'],$data['length'],$data['roomid'],$data['popular'],"\t".$data['first'],$data['valid'],$data['realname'],"\t".$data['papersid']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'主播列表');
        }
        $this->data = $datas;
		$this->reasontype = Live::getUnpassreson();
        $this->page = $Page->show();
		$this->orderHash = $anchorDao->getOrder();
        $this->display();
    }
   
    /*
     * 主播详情统计  按月取数据
     */
    public function anchorstatistics()
    {
        $uid = I('get.uid');
        $anchorDao = D('anchor');
        $where['cid'] = $this->companyId;
        $where['uid'] = $uid;
        $anchor = $anchorDao->where($where)->find();
        if(!$anchor) {  //找不到主播或主播不属于该公司
            return false;
        }
        if(!($startTime = I('get.timestart'))) {
            $_GET['timestart'] = $startTime = date('Y-m-01');
        }
		if(!($endTime = I('get.timeend'))) {
            $_GET['timeend'] = $endTime = date('Y-m-d');
        }
        if($export = I('get.export')) {
            $this->exportDetail($uid, $startTime, $endTime, $export);
        }
        $strDate = ''; $days = [];
        $tmpTime = $startTime;
		while($tmpTime <= $endTime) {
            $timestamp = strtotime($tmpTime);
            $tmp =date('m-d', $timestamp);
            $strDate .= '"' . $tmp . '",';
            $days[] = $tmp;
            $tmpTime = date('Y-m-d', $timestamp + 86400);
		}
        $anchorIncome = \HP\Op\Anchor::getLiveLengthByUid($uid, $startTime, $endTime);//直播时长及收入
		$anchorInfo = \HP\Op\Anchor::anchorInfo([$uid]);//获取昵称等信息
        $anchorPopular = \HP\Op\Anchor::getPopularByUid($uid, $startTime, $endTime);//最佳人气
		
		$data = ['strDate' => trim($strDate, ','),
				'strLength' => '',
				'strCoin' => '',
				'strBean' => '',
				'strPopular' => '',
				'totalLength' => 0,
				'totalCoin' => 0,
				'totalBean' => 0,
				'maxPopular' => 0];
		if($anchorIncome) {
			$tmpLength = $tmpCoin = $tmpBean = array();
			foreach($anchorIncome as $k=>$v) {
				$index = $v['day'];
				$tmpLength[$index] = $v['length'];
				$tmpCoin[$index] = $v['coin'];
				$tmpBean[$index] = $v['bean'];
			}
        }
		foreach($days as $v) {
			if(isset($tmpLength[$v])) {
				$data['strLength'] .= round($tmpLength[$v]/3600, 2) . ',';
				$data['totalLength'] += $tmpLength[$v];
                $data['strCoin'] .= $tmpCoin[$v] . ',';
				$data['totalCoin'] += $tmpCoin[$v];
                $data['strBean'] .= $tmpBean[$v] . ',';
				$data['totalBean'] += $tmpBean[$v];
			} else {
				$data['strLength'] .= '0,';
				$data['strCoin'] .= '0,';
				$data['strBean'] .= '0,';
			}
		}
		$data['strLength'] = trim($data['strLength'], ',');
        $data['strCoin'] = trim($data['strCoin'], ',');
        $data['strBean'] = trim($data['strBean'], ',');
		$data['totalLength'] = round($data['totalLength']/3600, 2);

		if($anchorPopular) {
			$tmpPopular = array();
			foreach($anchorPopular as $k=>$v) {
				$tmpPopular[$v['day']] = $v['popular'];
				$data['maxPopular'] = max($v['popular'], $data['maxPopular']);
			}
        }
		foreach($days as $v) {
			if(isset($tmpPopular[$v])) {
				$data['strPopular'] .=  $tmpPopular[$v]. ',';
			} else {
				$data['strPopular'] .= '0,';
			}
		}
		$data['strPopular'] = trim($data['strPopular'], ',');
		
        $this->data = $data;
        $this->user = $anchorInfo[$uid];
        $this->display();
    }
    
    function apply()
    {
        $Dao = D('anchorapplycompany');
        $Dao_user = D('userstatic');
        $checkStatus = $Dao->getCheckstatus();
        $liveStyle = $Dao->getLiveStyle();
        $where['a.cid'] = $this->companyId;
        $status = I('get.status', -1);
        if($status >= 0) {
            $where['a.status'] = $status;
        }
        $_GET['status'] = $status;
        if($uid = I('get.uid', 0)) {
            $where['a.uid'] = $uid;
        }
        if($nick = I('get.nick', 0)) {
            $where['b.nick'] = ['like', "%$nick%"];
        }
        if($timestart = I('get.timestart')){
        	$where['a.ctime'][] = ['egt', $timestart . ' 00:00:00'];
        }
        if($timeend = I('get.timeend')){
        	$where['a.ctime'][] = ['elt', $timeend . ' 23:59:59'];
        }
        $count = $Dao
            ->alias(' a ')
            ->join(" left join " . $Dao_user->getTableName() . " as b on a.uid = b.uid ")
            ->where($where)->count();
        $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
        
        $results = $Dao
            ->alias(' a ')
            ->join(" left join " . $Dao_user->getTableName() . " as b on a.uid = b.uid ")
            ->where($where)
            ->order('id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->field('a.*,b.nick,b.pic')
            ->select();
        
        if($results) {
            $videoid = array_column($results, 'videoid');
            $video = D('video')->where(['videoid'=>['in', $videoid]])->getField('videoid,poster,vfile');
            $uid = array_column($results, 'uid');
            $user = D('userrealname')->where(['uid'=>['in', $uid]])->getField('uid,papersid,name');
            $gameid = array_column($results, 'gameid');
            if(!empty($gameid)) {
                $game = D('game')->where(['gameid' => ['in', $gameid]])->getField('gameid,name');
            }
            foreach($results as &$result) {
            	$result['pic'] = avator($result['pic']);
                $result['vfile'] = sfile($video[$result['videoid']]['vfile']);
                $result['poster'] = sposter($video[$result['videoid']]['poster']);
                $result['papersid'] = $user[$result['uid']]['papersid'];
                $result['realname'] = $user[$result['uid']]['name'];
                $result['gamename'] = isset($game[$result['gameid']]) ? $game[$result['gameid']] : '';
                $result['showface'] = isset($liveStyle[$result['showface']]) ? $liveStyle[$result['showface']] : '';
            }
        }
        $this->data = $results;
        $this->checkStatus = $checkStatus;
        $this->page = $Page->show();
        $this->display();
    }
    
    function checkapply()
    {
        $msg = ['status'=>0,'info'=>'操作失败'];
        
        $id = I('post.id', 0);
        $Dao = D('anchorapplycompany');
        $Dao_anchor = D('anchor');
        $info = $Dao->where(['id'=>$id, 'cid'=>$this->companyId])->find();
        $anchor = $Dao_anchor->where("uid=" . $info['uid'])->find();
        if(!$anchor || !$info) {
            $msg = ['status'=>0,'info'=>'主播不存在，请联系运营人员'];
            return $this->ajaxReturn($msg);
        }
        if($anchor['cid'] != 0) {
            $msg = ['status'=>0,'info'=>'主播已经有所属公司，请联系运营人员'];
            return $this->ajaxReturn($msg);
        }
        if(in_array($info['status'], [0,2]) && $info['cid'] == $this->companyId) {
        	$newid = $Dao->where("uid=" . $info['uid'])->max('id');
        	if($id !== $newid) {  //用户多条记录，防止出错，只能审核最新的记录
        		$data = [
        				'status' => 100,
        				'utime' => get_date(),
        				'companyuid' => \HP\Op\Admin::getUid(),
        				'companyreason' => '异常，不是最新申请记录'
        		];
        		$Dao->where("id=$id")->save($data);
        		$msg = ['status'=>0,'info'=>'这不是该用户最新记录，无法操作'];
        		return $this->ajaxReturn($msg);
        	}
            $status = I('post.status', 0);
            if($status == 2 || $status == 3) {
                $reason = trim(I('post.reason', ''));
                if($status == 3 && $reason == '') {
                    $msg = ['status'=>0,'info'=>'提交失败！拒绝原因为必填项'];
                    return $this->ajaxReturn($msg);
                }
                if($status == 2) { //同意申请
                	if($info['failcount'] >= 3) {
                		$msg = ['status'=>0,'info'=>'用户被运营拒绝次数太多，请联系运营人员'];
                		return $this->ajaxReturn($msg);
                	}
                	if($info['failcount'] != -1) {
                		$count = $Dao->where(['cid'=>$this->companyId,'uid'=>$info['uid'],'status'=>5])->count();
                		if($count >= 3) {
                			$Dao->where(['id'=>$id])->save(['failcount'=>$count]);
	                		$msg = ['status'=>0,'info'=>'用户被运营拒绝次数太多，请联系运营人员'];
	                		return $this->ajaxReturn($msg);
                		}
                	}
                }
                $data = [
                    'status' => $status,
                    'utime' => get_date(),
                    'companyuid' => \HP\Op\Admin::getUid(),                    
                    'companyreason' => $reason
                    ];
                $res = $Dao->where("id=$id")->save($data);
                if($res) {
                    $msg = ['status'=>1,'info'=>'操作成功'];
                }
            }
        } else {
            $msg = ['status'=>0,'info'=>'状态已变，无法修改'];
        }
        return $this->ajaxReturn($msg);
    }
}

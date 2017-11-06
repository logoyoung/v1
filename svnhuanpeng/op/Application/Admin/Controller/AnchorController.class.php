<?php

namespace Admin\Controller;
use HP\Op\Anchor;
use HP\Op\Company;
use HP\Op\Live;

class AnchorController extends BaseController{

    protected $pageSize = 10;

    protected function _access(){
        return [
            'anchorcontract' => ['anchorcontract'],
            'anchorcontractsave' => ['anchorcontract'],
			'anchorstatistics' => ['index'],
            'anchorbank' => ['index'],
        ];
    }

   /*  
    * 主播查询列表
    * zwq add 2017年5月8日 
    */
    
    public function index()
    {
        $anchorDao = D('anchor');
        $roomidDao = D('roomid');
        $userstaticDao = D('userstatic');
        $lengthDao = D('liveLength');

        $where = [];
		$whereStr = ' 1 ';
        if($id = I('get.uid')){
            $where['a.uid'] = $id;
			$whereStr .= ' and a.uid="' . $id . '"';
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
			$whereStr .= ' and b.roomid="' . $roomid . '"';
        }
        if($cid = I('get.cid')){
            $where['a.cid'] = $cid;
			$whereStr .= ' and a.cid="' . $cid . '"';
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
			//$sql .= ' limit 0,500';
            $results = $anchorDao->query($sql);
        }else{
            $count = $anchorDao
            ->alias(' a ')
            ->join(" left join ".$roomidDao->getTableName()." as b on a.uid = b.uid ")
            ->join(" left join ".$userstaticDao->getTableName()." as c on a.uid = c.uid ")
            ->where($where)->count();
            
            $Page = new \HP\Util\Page($count, $this->pageSize);
			$sql .= ' limit ' . $Page->firstRow . ',' . $Page->listRows;
            $results = $anchorDao->query($sql);
        }
		$uids = array_column($results, 'uid');
        $anchorPopular = \HP\Op\Anchor::anchorPopular($uids, $startTime, $endTime);//最佳人气
        $first = \HP\Op\Anchor::anchorFirstDay($uids);//首播日期
        $companymap = \HP\Op\Company::getCompanymap();//公司名称
        $anchorRealInfo = \HP\Op\Anchor::anchorRealInfo($uids);//身份证号
        $anchorStatus = \HP\Op\Anchor::getBlackList($uids);//黑名单
		$historyStatus = \HP\Op\Anchor::getHistoryBlackList($uids,$startTime,$endTime);//历史黑名单
        $contractTime = \HP\Op\Anchor::getContractTime($uids);//首次签约日期
        $this->status = $anchorDao->getAnchorStatus();
        foreach ($results as $result ){
            $data = $result;
            $data['popular'] = $anchorPopular[$result['uid']];
            $data['first'] = $first[$result['uid']];
            $data['companyname'] = $companymap[$result['cid']]['name'];
            $data['realname'] = $anchorRealInfo[$result['uid']]['name'];
            $data['papersid'] = $anchorRealInfo[$result['uid']]['papersid'];
            $data['length'] = secondFormatH($result['length']);
            $data['coin'] = floor($result['coin']);
            $data['bean'] = floor($result['bean']);
            $data['total'] = floor($result['coin']) + floor($result['bean']);
            $data['status'] = isset($anchorStatus[$result['uid']]) ? 2 : 1;
			$data['historystatus'] = isset($historyStatus[$result['uid']]) ? 2 : 1;
			/*if(isset($historyStatus[$result['uid']]) && $historyStatus[$result['uid']]=='3')
				$data['historystatus'] = 2;
			elseif(isset($historyStatus[$result['uid']]) && $historyStatus[$result['uid']]=='100')
				$data['historystatus'] = 1;
			else
				$data['historystatus'] = $data['status'];*/
			//dump($anchorStatus[$result['uid']]);
            $data['contractTime'] = isset($contractTime[$result['uid']]) ? $contractTime[$result['uid']] : '';
            $datas[] = $data;
        }
        if($export = I('get.export')){//导出数据
            $excel[] = array('UID','昵称','等级','金币收益','金豆收益','合计（取整）','直播时长','直播间','人气峰值','首播日期','首次签约日期','有效天数','真实姓名','所属公司','身份证号','状态');
            foreach ($datas as $data) {
                $data['nick'] = '"'.$data['nick'].'"';
            	$excel[] = array($data['uid'],$data['nick'],$data['level'],$data['coin'],$data['bean'],$data['total'],$data['length'],$data['roomid'],$data['popular'],"\t".$data['first'],$data['contractTime'],$data['valid'],$data['realname'],$data['companyname'],"\t".$data['papersid'],$this->status[$data['historystatus']]);
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
     * 签约管理
     * zwq add 2017年5月8日
     */
    
    public function anchorcontract(){
        $anchorDao = D('anchor');
//        $companyAnchorDao = D('companyAnchor');
        $userstaticDao = D('userstatic');
        if($id = I('get.uid')){
            $where['a.uid'] = $id;
        }
        if($username = I('get.username')){
            $where['c.username'] = ['like',"%$username%"];
        }
        if($nick = I('get.nick')){
            $where['c.nick'] = ['like',"%$nick%"];
        }
        if($phone = I('get.phone')){
            $where['c.phone'] = ['like',"%$phone%"];
        }
        if(I('get.hascompany')>0 ){
            $hascompany = I('get.hascompany');
            $hascompany==1?$where['a.cid'] = ['gt',0]:$where['a.cid'] = ['eq',0];
        }
        if($cid = I('get.cid')){
            $where['a.cid'] = $cid;
        }
        if($export = I('get.export')){//导出数据
            $results = $anchorDao
            ->alias(' a ')
//            ->join(" left join ".$companyAnchorDao->getTableName()." as b on a.uid = b.uid and b.status = 0 ")
            ->join(" left join ".$userstaticDao->getTableName()." as c on a.uid = c.uid ")
            ->where($where)
            ->order('a.uid desc')
//            ->field('a.uid,c.nick,c.username,a.cid,b.status')
				->field('a.uid,c.nick,c.username,a.cid')
            ->select();
        }else{
            $count = $anchorDao
            ->alias(' a ')
//            ->join(" left join ".$companyAnchorDao->getTableName()." as b on a.uid = b.uid and b.status = 0 ")
            ->join(" left join ".$userstaticDao->getTableName()." as c on a.uid = c.uid ")
            ->where($where)->count();
            
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $results = $anchorDao
            ->alias(' a ')
//            ->join(" left join ".$companyAnchorDao->getTableName()." as b on a.uid = b.uid and b.status = 0 ")
            ->join(" left join ".$userstaticDao->getTableName()." as c on a.uid = c.uid ")
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('a.uid desc')
//            ->field('a.uid,c.nick,c.username,a.cid,b.status')
				->field('a.uid,c.nick,c.username,a.cid')
            ->select();
        }
//        $status = D('CompanyAnchor')->getStatus();
        $type = D('Company')->getType();
        $companymap = \HP\Op\Company::getCompanymap();
        foreach ($results as $result ){
            $data = $result;
//            if($data['status']==='0'){
			if($data['cid']){
                $data['companyname'] = $companymap[$result['cid']]['name'];
//                $data['status'] = $status[$result['status']];
                $data['type'] = $type[$companymap[$result['cid']]['type']];
            }
            $datas[] = $data;
        }
        if($export = I('get.export')){//导出数据
            $excel[] = array('主播ID','主播昵称','公司ID','公司名称','签约类型');
            foreach ($datas as $data) {
                $excel[] = array($data['uid'],$data['nick'],$data['cid'],$data['companyname'],$data['type']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'签约管理列表');
        }
        //dump($datas);
        $this->data = $datas;
        $this->page = $Page->show();
        $this->hascompany = [1=>'已签约',2=>'未签约'];
        $this->display();
    }
    
    
    /*
     * 设置签约公司
     * zwq add 2017年5月10日
     */
    public function anchorcontractsave(){
//        $dao = D('companyAnchor');
		$dao = D('anchor');
        $companydao = D('company');
        $id = is_numeric(I('get.id'))?I('get.id'):null;
        $userstatics = \HP\Op\Anchor::anchorInfo([$id]);
        $userstatic = $userstatics[$id];
        $companyinfos = \HP\Op\Company::getCompangInfo();
        
        if(IS_POST){
            $fcid = I("post.fcid");//修改前的cid
            $cid = I("post.cid");//修改后cid
			$status = I("post.status");
			$uid = I("post.uid");
			if(empty($cid)|| empty($uid) || !in_array($status,array(0,1))){
				return $this->error('请求非法');
			}
            if($status==0){//签约
//				if($cid !=$fcid && $fcid !=0){
//					return $this->error('必须先解约，才能变更经济公司');
//				}
				$rate = $companyinfos[$cid]['rate'];
			}else{
				$cid=0;
				$rate=$companydao->getRate('base');
			}

			$data=array(
				'uid'=>$uid,
				'cid'=>$cid,
				'rate'=>$rate,
				'status'=>$status
			);
			$res = \HP\Op\Anchor::setAnchorContract($fcid,$data);
			if($res){
				return $this->success('操作成功!',U('anchor/anchorcontract'));
			}else{
				return $this->error('操作失败!');
			}
        }
        
        $contract = $id?$dao->where(['uid'=>$id])->find():[];
        if($contract['cid']) $contract['companyname'] =$companyinfos[$contract['cid']]['name'];
		if($contract['cid']==0){
			$contract['status'] = 1;
			unset($contract['utime']);
		}else{
			$contract['status'] = 0;
		}
        $this->contract=$contract;
        $this->userstatic = $userstatic;
		$this->nowstatic = ['0'=>'已签约','1'=>'未签约'];
        $this->uid = $id;
        $this->display();
    }
    
    /*
     * 主播详情统计  按月取数据
     */
    public function anchorstatistics()
    {
        $uid = I('get.uid');
        if(!($startTime = I('get.timestart'))) {
            $_GET['timestart'] = $startTime = date('Y-m-01');
        }
        if(!($endTime = I('get.timeend')) || $endTime > date('Y-m-d')) {
            $_GET['timeend'] = $endTime = date('Y-m-d');
        }
        if($export = I('get.export')) {
            $this->exportDetail($uid, $startTime, $endTime, $export);
        }
        $days = [];
        $tmpTime = $startTime;
		while($tmpTime <= $endTime) {
            $timestamp = strtotime($tmpTime);
            $tmp =date('m-d', $timestamp);
            $days[] = $tmp;
            $tmpTime = date('Y-m-d', $timestamp + 86400);
		}
        $anchorIncome = \HP\Op\Anchor::getLiveLengthByUid($uid, $startTime, $endTime);//直播时长及收入
		$anchorInfo = \HP\Op\Anchor::anchorInfo([$uid]);//获取昵称等信息
        $anchorPopular = \HP\Op\Anchor::getPopularByUid($uid, $startTime, $endTime);//最佳人气
		
        $data = ['strDate' => $days,
				'strLength' => [],
				'strCoin' => [],
				'strBean' => [],
				'strPopular' => [],
				'totalLength' => 0,
				'totalCoin' => 0,
				'totalBean' => 0,
				'maxPopular' => 0,
				'fans' => [],
				'newfans' => []];
		if($anchorIncome) {
			$tmpLength = $tmpCoin = $tmpBean = $tmpfans = $tmpnewfans = array();
			foreach($anchorIncome as $k=>$v) {
				$index = $v['day'];
				$tmpLength[$index] = $v['length'];
				$tmpCoin[$index] = $v['coin'];
				$tmpBean[$index] = $v['bean'];
				$tmpfans[$index] = $v['fans'];
				$tmpnewfans[$index] = $v['newfans'];
			}
        }
		foreach($days as $v) {
			if(isset($tmpLength[$v])) {
				$data['strLength'][] = round($tmpLength[$v]/3600, 2);
				$data['totalLength'] += $tmpLength[$v];
                $data['strCoin'][] = $tmpCoin[$v];
				$data['totalCoin'] += $tmpCoin[$v];
                $data['strBean'][] = $tmpBean[$v];
				$data['totalBean'] += $tmpBean[$v];
				$data['fans'][] = $tmpfans[$v];
				$data['newfans'][] = $tmpnewfans[$v];
			} else {
				$data['strLength'][] = '0';
				$data['strCoin'][] = '0';
				$data['strBean'][] = '0';
				$data['fans'][] = '0';
				$data['newfans'][] = '0';
			}
		}

		if($anchorPopular) {
			$tmpPopular = array();
			foreach($anchorPopular as $k=>$v) {
				$tmpPopular[$v['day']] = $v['popular'];
				$data['maxPopular'] = max($v['popular'], $data['maxPopular']);
			}
        }
		foreach($days as $v) {
			if(isset($tmpPopular[$v])) {
				$data['strPopular'][] =  $tmpPopular[$v];
			} else {
				$data['strPopular'][] = '0';
			}
		}
		foreach($data as $k=>$v) {
			if(is_array($v)) {
				if($k != 'strDate' && $k != 'strLength' && $k != 'strPopular' && $endTime == date('Y-m-d')) {
					array_pop($data[$k]);
				}
				if($k == 'strDate') {
					$data[$k] = '"' . implode('","', $data[$k]) . '"';
				} else {
					$data[$k] = implode(',', $data[$k]);
				}
			}
		}
		$data['totalLength'] = round($data['totalLength']/3600, 2);
		//var_dump($data); exit;
        $this->data = $data;
        $this->user = $anchorInfo[$uid];
        $this->display();
    }
    
    function exportDetail($uid, $startTime, $endTime, $export)
    {
        $anchorInfo = \HP\Op\Anchor::anchorInfo([$uid]);//获取昵称等信息
        $nick = $anchorInfo[$uid]['nick'];
        if($export == 1){//直播详情
            $Dao = D('Live');
            $where['uid'] = $uid;
            $where['stime'][] =['egt', $startTime.' 00:00:00'];
            $where['stime'][] =['elt', $endTime.' 23:59:59'];
            $datas = $Dao->field("uid,liveid,gamename,stime,etime")
    				->where($where)
    				->order('ctime')
    		 		->select();
            $excel[] = array('直播ID','游戏名称','开始时间','结束时间');
            if($datas) {
                foreach ($datas as $data) {
                    $excel[] = array($data['liveid'],$data['gamename'],$data['stime'],$data['etime']);
                }
            }
            \HP\Util\Export::outputCsv($excel, $nick.'-直播详情');
        } elseif ($export == 2){//人气详情
            $Dao = D('AnchorMostPopular');
            $where['uid'] = $uid;
            $where['ctime'][] =['egt', $startTime.' 00:00:00'];
            $where['ctime'][] =['elt', $endTime.' 23:59:59'];
            $datas = $Dao->field("uid,popular,ctime")
    				->where($where)
    				->order('ctime')
    				->select();
            $excel[] = array('时间','人气');
            if($datas) {
                foreach ($datas as $data) {
                    $excel[] = array($data['ctime'],$data['popular']);
                }
            }
            \HP\Util\Export::outputCsv($excel, $nick.'-人气详情');
        } elseif ($export == 3){//金币收益列表
            $where['luid'] = $uid;
            $where['ctime'][] =['egt', $startTime.' 00:00:00'];
            $where['ctime'][] =['elt', $endTime.' 23:59:59'];
            $where['income'] =['neq', 0];
            $sMonth = date('Ym', strtotime($startTime));
            if($sMonth < '201703') {
                $sMonth = '201703';
            }
            $eMonth = date('Ym', strtotime($endTime));
            $Dao = D('giftrecordcoin_' . $sMonth);
            $Dao->field('*')->table('giftrecordcoin_' . $sMonth);
            while($sMonth < $eMonth) {
                $sMonth = date('Ym', strtotime('+1 month', strtotime($startTime)));
                $Dao->union(array('field'=>'*','table'=>'giftrecordcoin_' . $sMonth));
            }
            $datas = $Dao->where($where)->select();
            $Dao_gift = D('gift');
            $gift = $Dao_gift->getField('id,giftname');
            $excel[] = array('时间','赠送者','礼物名称','收入','直播ID');
            if($datas) {
                foreach ($datas as $data) {
                    $excel[] = array($data['ctime'],$data['uid'],$gift[$data['giftid']],$data['income'],$data['liveid']);
                }
            }
            \HP\Util\Export::outputCsv($excel, $nick.'-金币收益列表');
        } elseif ($export == 4){// 金豆收益列表
            $where['luid'] = $uid;
            $where['ctime'][] =['egt', $startTime.' 00:00:00'];
            $where['ctime'][] =['elt', $endTime.' 23:59:59'];
            $where['income'] =['neq', 0];
            $sMonth = date('Ym', strtotime($startTime));
            if($sMonth < '201703') {
                $sMonth = '201703';
            }
            $eMonth = date('Ym', strtotime($endTime));
            $Dao = D('giftrecord_' . $sMonth);
            $Dao->field('*')->table('giftrecord_' . $sMonth);
            while($sMonth < $eMonth) {
                $Dao = date('Ym', strtotime('+1 month', strtotime($startTime)));
                $Dao->union(array('field'=>'*','table'=>'giftrecordcoin_' . $sMonth));
            }
            $datas = $Dao->where($where)->select();
            $excel[] = array('时间','赠送者','收入','直播ID');
            if($datas) {
                foreach ($datas as $data) {
                    $excel[] = array($data['ctime'],$data['uid'],$data['income'],$data['liveid']);
                }
            }
            \HP\Util\Export::outputCsv($excel, $nick.'-金豆收益列表');
        }
    }
    
        
    public function anchorbank()
    {   
        if(!\HP\Op\Admin::checkAccessWithKey('anchor_anchorbank')) {
            return $this->error('缺少权限!');
        }
        $dao = D('bankCard');
        $uid = I('get.uid');
        $anchor = D('userrealname')->where(['uid'=>$uid])->field('name,papersid')->find();
        $userInfo = D('userstatic')->where(['uid'=>$uid])->field('nick,phone')->find();
        $bank = D('bank')->where(['status'=>0])->order('name')->getField('id,name');
        if(!$anchor || !$userInfo) {
            return $this->error('该用户不是主播或缺少实名信息!');
        }
        $cardInfo = $dao->where(['uid'=>$uid])->find();
        
        if(IS_POST){
            $msg = ['status'=>0,'info'=>'操作失败'];
            if($wid = I('get.wid')) {
                $dao_wages = D('admin_wages');
                $wages = $dao_wages->where(['id'=>$wid])->find();
                if($wages['uid'] != $uid) {
                    return $this->ajaxReturn($msg);
                }
            }
            $bankid = I('post.bankid');
            $cardid = I('post.cardid');
            $address = I('post.address');
            $accountbank = I('post.accountbank');
            if(!$bankid || !$cardid || !$address) {
                $msg = ['status'=>0,'info'=>'缺少必填信息'];
                return $this->ajaxReturn($msg);
            }
            if($id = I('post.id')) { //更新操作
                $data = [
                    'bankid' => $bankid,
                    'cardid' => $cardid,
                    'address' => $address,
                    'accountbank' => $accountbank,
                    'utime' => get_date()
                ];
                $res = $dao->where(['id' => $id])->save($data);
            } else {
                if($cardInfo) {
                    $msg = ['status'=>0,'info'=>'该用户已经有银行卡'];
                    return $this->ajaxReturn($msg);
                }
                $data = [
                    'uid' => $uid,
                    'name' => $anchor['name'],
                    'phone' => $userInfo['phone'],
                    'bankid' => $bankid,
                    'cardid' => $cardid,
                    'address' => $address,
                    'accountbank' => $accountbank
                ];
                $res = $dao->add($data);
            }
            if($res && $wid) {
                $data['wid'] = $wid;
                $update = [
                    'bank_id' => $bankid,
                    'bank_name' => $bank[$bankid],
                    'bank_card' => $cardid,
                    'bankaddress' => $address,
                	'accountbank' => $accountbank,
                    'utime_bank_card' => get_date()
                ];
                $res = $dao_wages->where(['id' => $wid])->save($update);
            }
            $data['adminid'] = get_uid();
            \HP\Log\Op::write(\HP\Log\Op::CHANGE_ANCHORBANK,$data);
            if($res) {
                $msg = ['status'=>1,'info'=>'操作成功'];
            }
            return $this->ajaxReturn($msg);
        }
        $this->anchor = $anchor + $userInfo;
        $this->cardInfo = $cardInfo;
        $this->bank = $bank;
        $this->display();
    }
    
    /**
     * 主播人气统计
     */
    public function anchorpopular()
    {
    	if(!($startTime = I('get.timestart'))) {
    		$_GET['timestart'] = $startTime = date('Y-m-01');
    	}
    	if(!($endTime = I('get.timeend'))) {
    		$_GET['timeend'] = $endTime = date('Y-m-d');
    	}
    	if($uid = I('get.uid', '')) {
    		$where['uid'] = $uid;
    	}
    	$where['date'][] = ['egt', $startTime . ' 00:00:00'];
    	$where['date'][] = ['elt', $endTime . ' 23:59:59'];
    	$dao = D('popularotyRecord');
    	
    	if(I('get.export')) {
	    	$dateUid = $dao->field('CONCAT(DATE_FORMAT(date,"%Y-%m-%d"),"_",uid) as dateuid')
	    		->where($where)
	    		->order('date desc')
	    		->group('dateuid')->select();
    	} else {
    		$count = $dao->where($where)->count('distinct CONCAT(DATE_FORMAT(date,"%Y-%m-%d"),"_",uid)');
    		$count = array_keys($count)[0];
    		$Page = new \HP\Util\Page($count, 20);
    		
    		$dateUid = $dao->field('CONCAT(DATE_FORMAT(date,"%Y-%m-%d"),"_",uid) as dateuid')
    		->where($where)
    		->limit($Page->firstRow.','.$Page->listRows)
    		->order('date desc')
    		->group('dateuid')->select();
    	}
    	$results = $dates = $uids = $hours = [];
    	if($dateUid) {
	    	foreach($dateUid as $k=>$v) {
	    		$arr = explode('_', $v['dateuid']);
	    		$dates[] = $tmp['date'] = $arr[0]; $uids[] = $tmp['uid'] = $arr[1];
	    		$results[] = $tmp;
	    	}
	    	$dates = array_unique($dates);
	    	$con['date'][] = ['elt', current($dates) . ' 23:59:59'];
	    	$con['date'][] = ['egt', end($dates) . ' 00:00:00'];
	    	
	    	$con['uid'] = ['in', $uids];
	    	$data = $dao->where($con)->order('date desc')->select();
	    	foreach($results as $k=>$v) {
	    		foreach($data as $vv) {
	    			if($v['uid'] == $vv['uid'] && $v['date'] == date('Y-m-d', strtotime($vv['date']))) {
	    				$h = (int)date('H', strtotime($vv['date']));
	    				if(!in_array($h, $hours)) {
	    					$hours[] = $h;
	    				}
	    				$results[$k][$h] = $vv['virtual'] . "<br/>" . $vv['popularoty'] . "<br/>" . $vv['count'];
	    			}
	    		}
	    	}
	    	sort($hours);
	    	foreach($results as $k=>$v) {
	    		$data = ['date' => $v['date'], 'uid' => $v['uid']];
	    		foreach($hours as $hour) {
	    			$data[$hour] = isset($v[$hour]) ? $v[$hour] : '-';
	    		}
	    		$datas[] = $data;
	    	}
    	}
    	$this->hours = $hours;
    	$this->data = $datas;
    	if(I('get.export')) {
    		$html = $this->fetch('anchorpopular_export');
    		 header('Content-Transfer-Encoding: GBK');
    		 header('Content-Type: application/vnd.ms-excel;charset=GBK');
    		 header("Content-type: application/x-msexcel");
    		 header('Content-Disposition: attachment; filename="' . iconv("utf-8", "gb2312", "人气") . '.xls"');
    		echo $html; exit;
    	}
    	$this->page = $Page->show();
    	$this->display();
    }
    
    
    /**
     * 主播粉丝统计
     */
    public function anchorfans()
    {
		$statisDao = D('AnchorStatis');
		$anchorDao = D('anchor');
        $userstaticDao = D('userstatic');
		
		$where = [];
    	if($uid = trim(I('get.uid'))){
    		$where['a.uid'] = $uid;
    	}
    	if(!($date = I('get.date'))) {
    		$_GET['date'] = $date = date('Y-m-d', strtotime('-1 day'));
    	}
    	if(!($order = I('get.order'))) {
    		$_GET['order'] = $order = 1;
    	}
		$where['a.date'] = $date;
    	$orderby = $statisDao->getOrderSql($order);
		
		if($cid = I('get.cid')){
            $where['b.cid'] = $cid;
        }
        if($username = trim(I('get.username'))){
            $where['c.username'] = ['like',"%$username%"];
        }
        if($nick = trim(I('get.nick'))){
            $where['c.nick'] = ['like',"%$nick%"];
        }
        
        if($export = I('get.export')){//导出数据
            $results = $statisDao
            ->alias('a')
			->join(" left join ".$anchorDao->getTableName()." as b on a.uid = b.uid ")
            ->join(" left join ".$userstaticDao->getTableName()." as c on a.uid = c.uid ")
            ->where($where)
            ->order($orderby)
			->field('a.date,a.uid,a.fans,a.newfans,b.cid,c.nick,c.username')
            ->select();
        }else{
            $count = $statisDao
            ->alias('a')
			->join(" left join ".$anchorDao->getTableName()." as b on a.uid = b.uid ")
            ->join(" left join ".$userstaticDao->getTableName()." as c on a.uid = c.uid ")
            ->where($where)->count();
            
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $results = $statisDao
            ->alias('a')
			->join(" left join ".$anchorDao->getTableName()." as b on a.uid = b.uid ")
            ->join(" left join ".$userstaticDao->getTableName()." as c on a.uid = c.uid ")
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order($orderby)
			->field('a.date,a.uid,a.fans,a.newfans,b.cid,c.nick,c.username')
            ->select();
        }
        $companymap = \HP\Op\Company::getCompanymap();
        foreach ($results as $result ){
            $data = $result;
			if($data['cid']){
                $data['companyname'] = $companymap[$result['cid']]['name'];
            }
            $datas[] = $data;
        }
        if($export = I('get.export')){//导出数据
            $excel[] = array('日期','主播ID','主播昵称','公司名称','粉丝数量','新增粉丝数量');
            foreach ($datas as $data) {
                $excel[] = array("\t".$data['date'],$data['uid'],$data['nick'],$data['companyname'],$data['fans'],$data['newfans']);
            }
            \HP\Util\Export::outputCsv($excel, $date.'主播粉丝数量列表');
        }
        $this->data = $datas;
        $this->page = $Page->show();
        $this->orderHash = $statisDao->getOrder();
        $this->display();
    }
    
}

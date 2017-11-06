<?php

namespace Admin\Controller;
use HP\Log\Log;
use HP\Op\Check;
use HP\Op\Admin;
class WithdrawController extends BaseController
{
	protected $pageSize = 10;
	protected function _access(){
		//return self::ACCESS_NOLOGIN;
		return [
			'withdraw'=>['withdraw'],
			'pass'=>['withdraw'],
			'unpass'=>['withdraw'],
		];
	}
	public function withdraw(){
		//$withdrawDao = D('Withdraw');
		$bankcardDao = D('BankCard');
		$bankDao = D('Bank');
		$where = [];
		
		if(!($month = I('get.month'))) {
		    $_GET['month'] = $month = date('Y-m',strtotime(date('Y-m-01'))-86400);
		}

		isset($_GET['timestart'])?$_GET['timestart']=$_GET['timestart']:$_GET['timestart']= $month.'-01';
		isset($_GET['timeend'])?$_GET['timeend']=$_GET['timeend']:$_GET['timeend']= date('Y-m-t',strtotime($month.'-01'));
		if($start = I('get.timestart')){
			//$withdrawDao = $withdrawDao->selectTable($start);
			$withdrawDao = new \Common\Model\WithdrawModel('exchange_detail',$start);
			//Log::statis($withdrawDao->getTableName());
			$where['a.ctime'] = ['gt',$start];
		}
		if($end = I('get.timeend')){
			//$withdrawDao = $withdrawDao->selectTable($start);
			$where['a.ctime'] = ['lt',$end];
		}
		if($start && $end){
			$where['a.ctime'] = ['between',"$start 00:00:00,$end 23:59:59"];
		}
		if($uid = I('get.uid')){
			$where['a.uid'] = $uid;
		}
		if($tid = I('get.tid')){
			$where['a.tid'] = $tid;
		}
		if($name=I('get.name')){
			$where['b.name'] = ['like',"%$name%"];
		}
		//月份查询
		//默认
		$where['type'] = 5;
		isset($_GET['status'])?$_GET['status']=$_GET['status']:$_GET['status']='2';

		if(I('get.status')!='-1'){
			$where['a.status'] = I('get.status');
		}
		if($export = I('get.export')){//导出数据
			$results = $withdrawDao
				->alias('a')
				->join(" left join ".$bankcardDao->getTableName()." b  on a.uid = b.uid ")
				->join(" left join ".$bankDao->getTableName()." c on b.bankid = c.id ")
				->where($where)
				//->field("b.*,a.gamename,a.title,a.vfile,a.uid,a.length,a.poster")
				->field("a.tid,a.number,a.id as aid,a.status as mystatus,b.*,c.name as cname")
				->order('a.ctime desc ')
				->select();
		}else{
			//获取被其他adminuid锁定的uids============
			/* if(I('get.status')==RN_WAIT)$diffids = Check::getdiffuid(CacheKey::DIFF_CHECK_REALNAME);
			if($diffids) $where['a.uid'] =["not in",$diffids]; */
			$count = $withdrawDao
				->alias('a')
				->join(" left join ".$bankcardDao->getTableName()." b  on a.uid = b.uid ")
				->join(" left join ".$bankDao->getTableName()." c on b.bankid = c.id ")
				->where($where)
				->count();
			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);

			$results = $withdrawDao
				->alias('a')
				->join(" left join ".$bankcardDao->getTableName()." b  on a.uid = b.uid ")
				->join(" left join ".$bankDao->getTableName()." c on b.bankid = c.id ")
				->where($where)
				//->field("b.*,a.gamename,a.title,a.vfile,a.uid,a.length,a.poster")
				->field("a.tid,a.number,a.id as aid,a.status as mystatus,b.*,c.name as cname")
				->order('a.ctime desc ')
				->limit($Page->firstRow.','.$Page->listRows)
				->select();
		}
		$checkstatus = $withdrawDao->getCheckstatus();
		if($export = I('get.export')){//导出数据
            $excel[] = array('账单ID','用户UID','提现金额','提现人','手机号','银行卡号','开户银行','居住地址','申请提现时间','审核状态');
            foreach ($results as $data) {
                $excel[] = array($data['tid'],$data['uid'],$data['name'],$data['phone'],$data['cardid'],$data['cname'],$data['address'],$data['ctime'],$checkstatus[$data['mystatus']]);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'提现列表');
        }
		$this->datas = $results;
		$this->checkstatus = $checkstatus;
		$this->page = $Page->show();
		$this->display();
	}
	public function pass(){
		$msg = ['status'=>0,'info'=>'操作失败'];
		if(IS_POST){
			$status = \Common\Model\WithdrawModel::getStatus('pass');
			$id = I('post.id');
			$date = I('post.date');
			if( $id&&$date ){
				$res = Check::withdraw($date, $id,$status);
				if($res)$msg = ['status'=>1,'info'=>'操作成功'];
			}
		}
		return $this->ajaxReturn($msg);
	}
	public function unpass(){
		$msg = ['status'=>0,'info'=>'操作失败'];
		if(IS_POST){
			$id = I('post.id');
			$date = I('post.date');
			$status = \Common\Model\WithdrawModel::getStatus('unpass');
			if( $id&&$date ){
				$res = Check::withdraw($date, $id,$status);
				if($res)$msg = ['status'=>1,'info'=>'操作成功'];
			}
		}
		return $this->ajaxReturn($msg);
	}
}

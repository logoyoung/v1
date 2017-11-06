<?php

namespace Admin\Controller;
use HP\Log\Log;
use HP\Op\Anchor;
use \HP\Op\Game;
use HP\Op\Check;
use HP\Op\Admin;
use HP\Cache\CacheKey;
use HP\Op\Live;
use HP\Util\Room;


class LiveController extends BaseController{

	protected $pageSize = 9;
	public function _access()
	{
		return [
			'live' => ['live'],
			'checklive' => ['live'],
			'jinbo' => ['live'],
		];
	}

	//实名认证审核
	function live(){
		$dao = D('Live');
		$slaveDao = D('Slavelive');
		$userstaticDao = D('userstatic');
		$where['a.status'] = LIVE;
		if($uid = I('get.uid')){
			$where['a.uid'] = $uid;
		}
		if($nick = I('get.name')){
			$where['b.nick'] = ['like',"%$nick%"];
		}
		if($gamename = I('get.gamename')){
			$where['a.gamename'] = ['like',"%$gamename%"];
		}
		if($roomid = I('get.roomid')){
			//$where['a.roomid'] = $roomid;
			$uid = D('roomid')->field('uid')->where(['roomid'=>$roomid])->select();
			$uid = $uid[0]['uid'];
			$where['a.uid'] = $uid;
		}
		/*isset($_GET['status'])?$_GET['status']=$_GET['status']:$_GET['status']='100';
		if(I('get.status')!='-1'){
			$where['a.status'] = I('get.status');
		}*/

		if($export = I('get.export')){//导出数据
			$datas = $dao
				->field("a.* ,b.nick,b.username ")
				->alias("a")
				->join(" left join ".$userstaticDao->getTableName()." b on a.uid = b.uid  ")
				->where($where)
				->order('a.ctime desc ')
				->select();
		}else{
			//获取被其他adminuid锁定的uids============
			//if(I('get.status')==RN_WAIT)$diffids = Check::getdiffuid(CacheKey::DIFF_CHECK_REALNAME);
			//if($diffids) $where['uid'] =["not in",$diffids];
			$count = $dao
				->alias("a")
				->join(" left join ".$userstaticDao->getTableName()." b on a.uid = b.uid  ")
				->where($where)
				->count();
			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);

			$datas = $dao
				->field("a.* ,b.nick,b.username ")
				->alias("a")
				->join(" left join ".$userstaticDao->getTableName()." b on a.uid = b.uid  ")
				->where($where)
				->limit($Page->firstRow.','.$Page->listRows)
				->order('a.ctime desc ')
				->select();
		}

		if($export = I('get.export')){//导出数据
			$excel[] = array('游戏类型ID','名称','icon');
			foreach ($datas as $data) {
				$excel[] = array($data['gametid'],$data['name'],$data['icon']);
			}
			\HP\Util\Export::outputCsv($excel,date('Y-m-d').'游戏类型列表');
		}
		$uids = array_column($datas, "uid");
		//设置被锁定的uid============
		//if(I('get.status')==RN_WAIT)Check::initdiffuid(CacheKey::DIFF_CHECK_REALNAME,$uids);
		//添加播流加密串
		//$datas['stream'] = \HP\Secure\WsSrc::getWcsPlayLiveSecret($datas['stream']);
		$whereliveids = [];
		foreach ($datas as $live){
			$whereliveids[] = $live['liveid'];
		}
		if(!empty($whereliveids))
			$slavewhere['liveid'] = ['in',$whereliveids];
		$slavewhere['status'] = LIVE;
		$slavelives = $slaveDao->field('liveid,stream')->where($slavewhere)->select();
		foreach ($slavelives as $slavelive){
			$slave[$slavelive['liveid']] = $slavelive['stream'];
		}
		foreach ($datas as $key => &$liveData)
		{
			$liveData['stream'] = $liveData['stream'].'?'.\HP\Secure\WsSrc::getWcsPlayLiveSecret($liveData['stream']);
			if($slave[$liveData['liveid']])
				$liveData['slavestream'] = $slave[$liveData['liveid']].'?'.\HP\Secure\WsSrc::getWcsPlayLiveSecret($slave[$liveData['liveid']]);
			else
				$liveData['slavestream'] = "";
		}
		$this->data = $datas;
		$this->page = $Page->show();
		$reasontype = Live::getUnpassreson();
		krsort($reasontype);
		$this->reasontype = $reasontype;
		$this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
		$this->display();
	}

	//'notice' 警告, 'stop' 关流, 'kill' 封号
	function checklive(){
		$msg = ['status'=>0,'info'=>'操作失败'];
		if(IS_POST){
			$luid = I('post.luid');
			$liveid = I('post.liveid', 0);

			$reasontype = I('post.reasontype');
			$reason = I('post.reason');
			$act = I('post.act');
			$remark = I('post.remark');
			$opt['luid'] = $luid;
			$opt['reasontype'] = $reasontype;
			$opt['reason'] = $reason;
			$opt['remark']= $remark;
			$opt['act'] = $act;
			$opt['content'] = I('post.content')?I('post.content'):'';
			$opt['pic'] = I('post.gamepic')?I('post.gamepic'):'';
			$tmp = explode(',', $opt['pic']);
			if($tmp&&(count($tmp)>3))
				return $this->ajaxReturn(array('status'=>0,'info'=>'图片不能超过三张'));
			if(empty($opt['content'])){
				return $this->ajaxReturn(array('status'=>0,'info'=>'具体描述不能为空'));
			}

			if( $opt ){
				$res = Live::checklive($liveid, $opt);
				if($res)$msg = ['status'=>1,'info'=>'操作成功'];
			}
		}
		return $this->ajaxReturn($msg);
	}

	function jinbo(){
		$uid = I('get.uid');
		if(!$uid) return;
		$dao = D('anchor');
		$daoCom = D('company');
		$res = $dao->field("a.cid,b.name")
			->alias("a")
			->join(" left join ".$daoCom->getTableName()." b on a.cid = b.id  ")
			->where(['a.uid'=>$uid])
			->select();
		$company = $res[0];
		$reasontype = Live::getUnpassreson();
		$history=$this->get_notice_by_uid($uid);
		krsort($reasontype);
		$this->company = $company;
		$this->reasontype = $reasontype;
		$this->history=$history;
		$this->display();
	}
	function get_notice_by_uid($uid){
		$list=array();
		$dao=D('anchorblackrecord');
		$history=$dao->field("uid,type,reason,liveid,ctime,content,remark")->where("luid=$uid")->order("ctime desc ")->select();
		if($history){
			$adao = D( 'AclUser' );
			$adminids=implode(',',array_unique(array_column($history,'uid')));
			$adminsName = $adao->where("uid in ($adminids)")->getField( "uid,realname" );
			$rdao = D('Livereviewreason');
			$resons = $rdao->getField("id,reason");
			$typesstr = Live::$typesstr;
			foreach ($history as $v){
				$temp['ctime']=$v['ctime'];
				$temp['adminName']=$adminsName[$v['uid']]?$adminsName[$v['uid']]:'';
				$temp['content']=$v['content']?$v['content']:'';
				$temp['remark']=$v['remark']?$v['remark']:'';
				$temp['type']=$v['type']?$typesstr[$v['type']]:'';
				$temp['reason']=$v['reason']?$resons[$v['reason']]:'';
				array_push($list,$temp);
			}
			return $list;
		}else{
			return array();
		}
	}

}

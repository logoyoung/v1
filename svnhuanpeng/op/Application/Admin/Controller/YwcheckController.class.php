<?php

namespace Admin\Controller;
use HP\Log\Log;
use HP\Op\Admin;
use HP\Op\Anchor;
use HP\Op\publicRequist;
class YwcheckController extends BaseController
{
	protected $pageSize = 10;
	protected function _access(){
		//return self::ACCESS_NOLOGIN;
		return [
			'ywqualifications'=>['ywqualifications'],
			'ywchecksave'=>['ywqualifications'],
			'ywchecksavepass'=>['ywqualifications'],
			'ywchecksaveunpass'=>['ywqualifications'],
			'ywcommentcheck'=>['ywcommentcheck'],
			'ywcommentpass'=>['ywcommentcheck'],
			'ywcommentunpass'=>['ywcommentcheck'],
			'ywrefundcheck'=>['ywrefundcheck'],
			'ywrefundcheckpass'=>['ywrefundcheck'],
			'ywrefundcheckunpass'=>['ywrefundcheck'],
		];
	}
	public function ywqualifications(){
		$dueDao = D('dueAdminCert');
		$userDao = D('userstatic');
		$gameDao = D('game');
		$where = [];
		isset($_GET['status'])?$_GET['status']=$_GET['status']:$_GET['status']= '-1';
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d',strtotime('-1 month'));
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
		if($uid = I('get.uid')){
			$where['a.uid'] = $uid;
		}
		if($nick = I('get.nick')){
			$where['b.nick'] = ['like',"%$nick%"];
		}
		if(I('get.status') != '-2'){
			$where['a.status'] = I('get.status');
		}
		if($gamename = I('get.gamename')){
			list($gname,$gid) = explode('|',$gamename);
			$where['a.game_id'] = $gid;
		}
		if(($timestart = I('get.timestart'))&&($timeend = I('get.timeend'))){
			$where['a.ctime'] = ['between',["$timestart 00:00:00","$timeend 23:59:59"]];
		}
		if($export = I('get.export')){//导出数据
			$results = $dueDao
				->alias(' a ')
				->join(" left join ".$userDao->getTableName()." as b on a.uid = b.uid ")
				->join(" left join ".$gameDao->getTableName()." as c on a.game_id = c.gameid ")
				->where($where)
				->order('a.ctime desc')
				->field('a.*,b.nick,c.name as gamename')
				->select();
		}else{
			$count = $dueDao
				->alias(' a ')
				->join(" left join ".$userDao->getTableName()." as b on a.uid = b.uid ")
				->join(" left join ".$gameDao->getTableName()." as c on a.game_id = c.gameid ")
				->where($where)->count();

			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
			$results = $dueDao
				->alias(' a ')
				->join(" left join ".$userDao->getTableName()." as b on a.uid = b.uid ")
				->join(" left join ".$gameDao->getTableName()." as c on a.game_id = c.gameid ")
				->where($where)
				->limit($Page->firstRow.','.$Page->listRows)
				->order('a.ctime desc')
				->field('a.*,b.nick,c.name as gamename')
				->select();
		}
		$checkStatus = $dueDao->getCheckstatus();
		if($export = I('get.export')){//导出数据
			$excel[] = array('UID','昵称','认证游戏','提交时间','审核时间','审核状态');
			foreach ($results as $data) {
				$excel[] = array($data['uid'],$data['nick'],$data['gamename'],$data['ctime'],$data['utime'],$checkStatus[$data['status']]);
			}
			\HP\Util\Export::outputCsv($excel,date('Y-m-d').'约玩资质认证列表');
		}
		$this->datas = $results;
		$this->checkstatus = $checkStatus;
		$this->page = $Page->show();
		$this->display();
	}
	public function ywchecksave(){
		$dueDao = D('dueAdminCert');
		$userDao = D('userstatic');
		$gameDao = D('game');
		$where = [];
		if($id = I('get.id')){
			$where['a.id'] = $id;
		}
		$results = $dueDao
			->alias(' a ')
			->join(" left join ".$userDao->getTableName()." as b on a.uid = b.uid ")
			->join(" left join ".$gameDao->getTableName()." as c on a.game_id = c.gameid ")
			->where($where)
			->field('a.*,b.nick,c.name as gamename')
			->select();
		//$data = $results[0];
		$checkStatus = $dueDao->getCheckstatus();
		$pics = explode(',',$results[0]['pic_urls']);
		$this->data = $results[0];
		$this->pics = $pics;
		$this->status = $checkStatus[$results[0]['status']];
		$this->certdomain = DUE_CERT;
		//dump($pics);
		$this->display();
	}
	public function ywchecksavepass(){
		$msg = ['status'=>0,'info'=>'操作失败'];
		if(IS_POST){
			$uid = I('post.uid');
			$id = (int)I('post.id');
			$nick = I('post.nick');
			if( $id&&$uid&&$nick ){
				$dueDao = D('dueCert');
				$dueadminDao = D('dueAdminCert');
				$dueskillDao = D('dueSkill');
				$status = $dueDao->getCheckstatus2('pass');
				$info = $dueadminDao->where(['id'=>$id])->select();
				if($info[0]['status']==$status){
					return $this->ajaxReturn($msg);
				}
				//开启事务
				//M()->startTrans();
				$res1 = $dueadminDao->where(['id'=>$id])->save(['status'=>$status,'reason'=>'','utime'=>date('Y-m-d H:i:s')]);
				$res2 = $dueDao->where(['id'=>$id])->save(['pic_urls'=>$info[0]['pic_urls'],'info'=>$info[0]['info'],'status'=>$status,'utime'=>date('Y-m-d H:i:s')]);

				//$skill = $dueskillDao->where(['cert_id'=>$id])->select();
				$res3 = $dueskillDao->where(['cert_id'=>$id])->save(['switch'=>1,'utime'=>date('Y-m-d H:i:s')]);
				if(!$res3){
					$price = $dueDao->getPrice( 'default' );
					$skill = [
						'uid'     => $info[0]['uid'],
						'cert_id' => $info[0]['id'],
						'game_id' => $info[0]['game_id'],
						'price'   => $price,
						'unit'    => 1,
						'switch'   => 1,
						//'ctime'   => time(),
					];
					$res3  = $dueskillDao->add( $skill );
				}
				if(!publicRequist::duecert($info[0]['uid']))
					Log::statis("调用前台用户{$info[0]['uid']}技能缓存更新驱动失败",null,'duecert');
				//todo log
				$adminid = get_uid();
				$result = D('AclUser')->where(['uid'=>$adminid])->select();
				$log = [
					'rid'=>$id,
					'adminid'=>$adminid,
					'aname'=>$result[0]['realname'],
					'uid'=>$uid,
					'uname'=>$nick,
					'opt'=>'同意认证',
					'status'=>'1',
					'reason'=>'',
				];
				$r = D('logDueCert')->add($log);

				if($res1&&$res2&&$res3&&$r){
					$msg = ['status'=>1,'info'=>'操作成功'];
					//陪玩比率
					Anchor::due_rate_charge($uid);
					//提交
					//M()->commit();
				}
				/*else{
					M()->rollback();
				}*/

			}
		}
		return $this->ajaxReturn($msg);
	}

	public function ywchecksaveunpass(){
		$msg = ['status'=>0,'info'=>'操作失败'];
		if(IS_POST){
			$uid = I('post.uid');
			$id = I('post.id');
			$nick = I('post.nick');
			$reason = I('post.reason');
			$reasonLen = mb_strlen($reason,'utf-8');
			if($uid && $nick && $id && $reason && ($reasonLen>=5 && $reasonLen<=50) ){
				$dueDao = D('dueCert');
				$dueadminDao = D('dueAdminCert');
				$status = $dueDao->getCheckstatus2('unpass');
				$res1 = $dueDao->where(['id'=>$id])->save(['status'=>$status,'reason'=>$reason,'utime'=>date('Y-m-d H:i:s')]);
				$res2 = $dueadminDao->where(['id'=>$id])->save(['status'=>$status,'reason'=>$reason,'utime'=>date('Y-m-d H:i:s')]);
				//todo log
				$adminid = get_uid();
				$result = D('AclUser')->where(['uid'=>$adminid])->select();
				$log = [
					'rid'=>$id,
					'adminid'=>$adminid,
					'aname'=>$result[0]['realname'],
					'uid'=>$uid,
					'uname'=>$nick,
					'opt'=>'拒绝认证',
					'status'=>'0',
					'reason'=>$reason,
				];
				$r = D('logDueCert')->add($log);

				if($res1&&$res2&&$r) $msg = ['status'=>1,'info'=>'操作成功'];
			}
		}
		return $this->ajaxReturn($msg);
	}

	public function ywcommentcheck(){
		$dueDao = D('dueComment');
		$userDao = D('userstatic');
		$where = [];
		isset($_GET['status'])?$_GET['status']=$_GET['status']:$_GET['status']= '-1';
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d',strtotime('-1 month'));
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');

		if($uid = I('get.uid')){
			$where['a.uid'] = $uid;
		}
		if($luid = I('get.luid')){
			$where['a.cert_uid'] = $luid;
		}
		if($orderid = I('get.orderid')){
			$where['a.order_id'] = $orderid;
		}
		if($nick = I('get.name')){
			$where['b.nick'] = ['like',"%$nick%"];
		}
		if(($timestart = I('get.timestart'))&&($timeend = I('get.timeend'))){
			$where['a.ctime'] = ['between',["$timestart 00:00:00","$timeend 23:59:59"]];
		}
		if(I('get.status') != '-2'){
			$where['a.status'] = I('get.status');
		}
		if($export = I('get.export')){//导出数据
			$results = $dueDao
				->alias(' a ')
				->join(" left join ".$userDao->getTableName()." as b on a.uid = b.uid ")
				->where($where)
				->order('a.ctime desc')
				->field('a.*,b.nick')
				->select();
		}else{
			$count = $dueDao
				->alias(' a ')
				->join(" left join ".$userDao->getTableName()." as b on a.uid = b.uid ")
				->where($where)->count();

			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
			$results = $dueDao
				->alias(' a ')
				->join(" left join ".$userDao->getTableName()." as b on a.uid = b.uid ")
				->where($where)
				->limit($Page->firstRow.','.$Page->listRows)
				->order('a.ctime desc')
				->field('a.*,b.nick')
				->select();
		}
		$checkStatus = $dueDao->getCheckstatus();
		if($export = I('get.export')){//导出数据
			$excel[] = array('提交时间','订单ID','用户UID','用户昵称','主播UID','评论','审核时间','审核状态');
			foreach ($results as $data) {
				$excel[] = array($data['ctime'],$data['order_id'],$data['uid'],$data['nick'],$data['cert_uid'],$data['comment'],$data['utime'],$checkStatus[$data['status']]);
			}
			\HP\Util\Export::outputCsv($excel,date('Y-m-d').'约玩订单评论审核列表');
		}
		$this->datas = $results;
		$this->checkstatus = $checkStatus;
		$this->page = $Page->show();
		$this->display();
	}
	public function ywcommentpass(){
		$msg = ['status'=>0,'info'=>'操作失败'];
		if(IS_POST){
			$id = I('post.id');
			$uid = I('post.uid');
			$nick = I('post.nick');
			if( $id && $uid && $nick ){
				$dueDao = D('dueComment');
				$status = $dueDao->getCheckstatus2('pass');
				$res = $dueDao->where(['id'=>$id])->save(['status'=>$status,'utime'=>date('Y-m-d H:i:s')]);
				//todo log
				$adminid = get_uid();
				$result = D('AclUser')->where(['uid'=>$adminid])->select();

				$log = [
					'rid'=>$id,
					'adminid'=>$adminid,
					'aname'=>$result[0]['realname'],
					'uid'=>$uid,
					'uname'=>$nick,
					'opt'=>'评论通过',
					'status'=>'1',
					'reason'=>'',
				];
				$r = D('logDueComment')->add($log);
				if($res&&$r) $msg = ['status'=>1,'info'=>'操作成功'];
			}
		}
		return $this->ajaxReturn($msg);
	}
	public function ywcommentunpass(){
		$msg = ['status'=>0,'info'=>'操作失败'];
		if(IS_POST){
			$id = I('post.id');
			$uid = I('post.uid');
			$nick = I('post.nick');
			if($reason = I('post.reason')){
				if(mb_strlen($reason,'utf-8')<5||mb_strlen($reason,'utf-8')>50)
					return $this->ajaxReturn(['status'=>0,'info'=>'填写拒绝原因不符合规范']);
			}
			if( $uid && $nick && $id ){
				$dueDao = D('dueComment');
				$status = $dueDao->getCheckstatus2('unpass');
				$res = $dueDao->where(['id'=>$id])->save(['status'=>$status,'utime'=>date('Y-m-d H:i:s')]);
				//todo log
				$adminid = get_uid();
				$result = D('AclUser')->where(['uid'=>$adminid])->select();
				$log = [
					'rid'=>$id,
					'adminid'=>$adminid,
					'aname'=>$result[0]['realname'],
					'uid'=>$uid,
					'uname'=>$nick,
					'opt'=>'拒绝评论',
					'status'=>'0',
					'reason'=>$reason,
				];
				$r = D('logDueComment')->add($log);
				if($res&&$r) $msg = ['status'=>1,'info'=>'操作成功'];
			}
		}
		return $this->ajaxReturn($msg);
	}

	public function ywrefundcheck(){
		$dueDao = D('dueOrder');
		$dueAppealDao = D('dueOrderAppeal');
		$userDao = D('userstatic');
		$where = [];
		isset($_GET['status'])?$_GET['status']=$_GET['status']:$_GET['status']= '0';
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d',strtotime('-1 month'));
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');

		if($uid = I('get.uid')){
			$where['a.uid'] = $uid;
		}
		if($luid = I('get.luid')){
			$where['a.cert_uid'] = $luid;
		}
		if($orderid = I('get.orderid')){
			$where['a.order_id'] = $orderid;
		}
		if($nick = I('get.name')){
			$where['c.nick'] = ['like',"%$nick%"];
		}
		if(($timestart = I('get.timestart'))&&($timeend = I('get.timeend'))){
			$where['b.ctime'] = ['between',["$timestart 00:00:00","$timeend 23:59:59"]];
		}

		if(I('get.status') != '-1'){
			$where['b.status'] = I('get.status');
		}
		if($export = I('get.export')){//导出数据
			$results = $dueAppealDao
				->alias(' b ')
				->join(" left join ".$dueDao->getTableName()." as a on a.order_id = b.order_id ")
				->join(" left join ".$userDao->getTableName()." as c on a.uid = c.uid ")
				->where($where)
				->order('b.ctime desc')
				->field('a.*,a.status astatus,b.*,b.ctime bctime,b.utime butime,b.status bstatus,b.pic,c.nick')
				->select();
		}else{
			$count = $dueAppealDao
				->alias(' b ')
				->join(" left join ".$dueDao->getTableName()." as a on a.order_id = b.order_id ")
				->join(" left join ".$userDao->getTableName()." as c on a.uid = c.uid ")
				->where($where)->count();

			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
			$results = $dueAppealDao
				->alias(' b ')
				->join(" left join ".$dueDao->getTableName()." as a on a.order_id = b.order_id ")
				->join(" left join ".$userDao->getTableName()." as c on a.uid = c.uid ")
				->where($where)
				->limit($Page->firstRow.','.$Page->listRows)
				->order('b.ctime desc')
				->field('a.*,a.status astatus,b.*,b.*,b.ctime bctime,b.utime butime,b.status bstatus,b.pic,c.nick')
				->select();

		}
		$checkStatus = $dueAppealDao->getCheckstatus();
		$orderStatus = $dueDao->getCheckstatus();
		if($export = I('get.export')){//导出数据
			$excel[] = array('提交时间','订单ID','用户UID','用户昵称','主播UID','订单金额','实付款','申诉原因','审核时间','订单状态','审核状态');
			foreach ($results as $data) {
				$excel[] = array($data['bctime'],$data['order_id'],$data['uid'],$data['nick'],$data['cert_uid'],$data['amount'],$data['real_amount'],$data['reason'],$data['butime'],$orderStatus[$data['astatus']],$checkStatus[$data['bstatus']]);
			}
			\HP\Util\Export::outputCsv($excel,date('Y-m-d').'约玩订单评论审核列表');
		}
		$this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
		$this->datas = $results;
		$this->checkstatus = $checkStatus;
		$this->orderstatus = $orderStatus;

		$this->checkstatusdesc = $dueAppealDao->getStatus();
		$this->orderstatusdesc = $dueDao->getStatus();
		$this->page = $Page->show();
		$this->display();
	}

	public function ywrefundcheckpass(){
		$msg = ['status'=>0,'info'=>'操作失败'];
		if(IS_POST){
			$id = I('post.id');
			$uid = I('post.uid');
			$nick = I('post.nick');
			$reason = I('post.reason');

			if($uid && $nick&& (int)$id && $reason && mb_strlen($reason)<60 && mb_strlen($reason)>0  ){
				$dueDao = D('dueOrder');
				//$status = $dueDao->getStatus('pass');
				//$res1 = $dueDao->where(['order_id'=>$id])->save(['reason'=>$reson,'otime'=>date('Y-m-d H:i:s')]);
				$dueDao = D('dueOrderAppeal');
				$status = $dueDao->getStatus('pass');
				$res2 = $dueDao->where(['order_id'=>$id])->save(['status'=>$status,'reply'=>$reason,'utime'=>date('Y-m-d H:i:s')]);
				//todo log
				$adminid = get_uid();
				$result = D('AclUser')->where(['uid'=>$adminid])->select();
				$log = [
					'rid'=>$id,
					'adminid'=>$adminid,
					'aname'=>$result[0]['realname'],
					'uid'=>$uid,
					'uname'=>$nick,
					'opt'=>'同意认证',
					'status'=>'1',
					'reason'=>$reason,
				];
				$r = D('logDueAppeal')->add($log);
				if($res2&&$r) $msg = ['status'=>1,'info'=>'操作成功'];
			}
		}
		return $this->ajaxReturn($msg);
	}
	public function ywrefundcheckunpass(){
		$msg = ['status'=>0,'info'=>'操作失败'];
		if(IS_POST){
			$id = I('post.id');
			$uid = I('post.uid');
			$nick = I('post.nick');
			$reason = I('post.reason');
			if($uid && $nick&& (int)$id && $reason && mb_strlen($reason)<60 && mb_strlen($reason)>0  ){
				$dueDao = D('dueOrder');
				//$status = $dueDao->getStatus('unpass');
				//$res1 = $dueDao->where(['order_id'=>$id])->save(['reason'=>$reson,'otime'=>date('Y-m-d H:i:s')]);
				$dueDao = D('dueOrderAppeal');
				$status = $dueDao->getStatus('unpass');
				$res2 = $dueDao->where(['order_id'=>$id])->save(['status'=>$status,'reply'=>$reason,'utime'=>date('Y-m-d H:i:s')]);
				//todo log
				$adminid = get_uid();
				$result = D('AclUser')->where(['uid'=>$adminid])->select();
				$log = [
					'rid'=>$id,
					'adminid'=>$adminid,
					'aname'=>$result[0]['realname'],
					'uid'=>$uid,
					'uname'=>$nick,
					'opt'=>'拒绝认证',
					'status'=>'0',
					'reason'=>$reason,
				];
				$r = D('logDueAppeal')->add($log);
				if($res2&&$r) $msg = ['status'=>1,'info'=>'操作成功'];
			}
		}
		return $this->ajaxReturn($msg);
	}
}

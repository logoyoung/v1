<?php

namespace Admin\Controller;

class ActivityController extends BaseController{

	protected $pageSize = 10;

	protected function _access(){
		return [
			'coupon'=>['coupon'],
			'addcoupon'=>['coupon'],
			'addredpk'=>['coupon'],
			'couponedit'=>['coupon'],
			'updatecoupon'=>['coupon'],
			'coupongrant'=>['coupon'],
			'addactivitysave'=>['addactivity'],
			'addactivityedit' =>['addactivity'],
		];
	}

	public function coupon(){
		$dao = D('dueCoupon');
		$activityDao = D('dueActivity');
		$_GET['status'] = isset($_GET['status'])?$_GET['status']:'-1';

		if($cid = I('get.cid')){
			$where['a.cid'] = $cid;
		}
		if($cname = I('get.cname')){
			$where['a.name'] = ['like',"%$cname%"];
		}
		if($aid = I('get.aid')){
			$where['a.aid'] = $aid;
		}
		if($aname = I('get.aname')){
			$where['b.name'] = ['like',"%$aname%"];
		}
		$status = I('get.status');

		if($export = I('get.export')){//导出数据
			if($status!='-1')
				$where['a.status'] = $status;
			$datas = $dao
				->alias(' a ')
				->join(" left join ".$activityDao->getTableName()." as b on a.aid = b.aid ")
				->field('a.*,b.name aname')
				->where($where)
				->order('a.cid desc')
				->select();
		}else{

			if($status!='-1')
				$where['a.status'] = $status;
			$count = $dao
				->alias(' a ')
				->join(" left join ".$activityDao->getTableName()." as b on a.aid = b.aid ")
				->where($where)
				->count();
			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
			unset($where['a.status']);
			if($status!='-1')
				$where['a.status'] = $status;
			$datas = $dao
				->alias(' a ')
				->join(" left join ".$activityDao->getTableName()." as b on a.aid = b.aid ")
				->field('a.*,b.name aname')
				->where($where)
				->limit($Page->firstRow.','.$Page->listRows)
				->order('a.cid desc ')
				->select();
		}
		$rpkstatus = $dao->getrpkstatus();
		foreach ($datas as &$data) {
			$data['condition'] = json_decode($data['condition'],true);
			$data['condition'] = $data['condition']['basePrice'][1];
		}
		if($export = I('get.export')){//导出数据
			$excel[] = array('创建时间','优惠卷ID','优惠卷名称','活动ID','活动名称','金额（欢朋币）','使用条件（满欢朋币多少使用）','状态','修改时间');
			foreach ($datas as &$data) {
				$excel[] = array($data['ctime'],$data['cid'],$data['name'],$data['aid'],$data['aname'],$data['price'],$data['condition'],$rpkstatus[$data['status']],$data['utime']);
			}
			\HP\Util\Export::outputCsv($excel,date('Y-m-d').'优惠卷列表');
		}
		$this->data = $datas;
		$this->page = $Page->show();
		$this->status = $rpkstatus;
		$this->display();
	}

	public function addcoupon(){
		$this->display();
	}
	public function couponedit(){
		$dao = D('dueCoupon');
		if($mid=I('get.id')){
			$where['cid'] = $mid;
		}
		$data = $dao->where($where)->select();
		$this->data = $data[0];
		$this->display();
	}
	public function addredpk(){

		$data = [];
		if($name=I('get.mname')){
			$data['name'] = $name;
		}
		if($price=I('get.price')){
			$data['price'] = $price;
		}
		if($condition=I('get.condition')){
			$data['condition'] = $condition;
		}
		if($valid=I('get.valid')){
			$data['status'] = $valid;
			if($valid=='1')
				$data['stime'] = date('Y-m-d H:i:s');
		}

		$msg = ['status'=>0,'info'=>'填写错误'];
		if(empty($name)||empty($price)||empty($condition)||!isset($valid)){
			return $this->ajaxReturn($msg);
		}
		elseif(!is_numeric($price)||!is_numeric($condition)){
			return $this->ajaxReturn($msg);
		}
		else{
			$r = D('dueCoupon')->add($data);
			if($r){
				$msg = ['status'=>1,'info'=>'添加成功'];
				return $this->ajaxReturn($msg);
			}
		}
	}
	public function updatecoupon(){
		if(I('post.type')=='-1')
			return $this->ajaxReturn(['status'=>0,'info'=>'请选择活动类型']);
		$name = I('post.name');
		$type = I('post.type');
		$coupons = I('post.coupons');
		$poster = I('post.poster');
		if(empty($name) || empty($coupons) || empty($type) ){
			return $this->ajaxReturn(['status'=>0,'info'=>'填写不完整']);
		}
		/*if(empty($poster)&&($type != '4')){
			return $this->ajaxReturn(['status'=>0,'info'=>'非内部活动不能缺少活动图片']);
		}*/
		//获取规则
		$rules = D('dueCouponConfig')->select();
		$activityrule = json_decode($rules[0]['config'],true);
		$couponrule = json_decode($rules[1]['config'],true);
		$couponjson = [];
		$couponallow = [];

		//参数检测 内部活动
		if($type=='4'){
			$uids = I('post.uids');
			$timestart = date('Y-m-d',strtotime(I('post.timestart2')))." 00:00:00";
			$timeend = date('Y-m-d',strtotime(I('post.timeend2')))." 23:59:59";
			if(empty($uids)||empty($timestart)||empty($timeend)){
				return $this->ajaxReturn(['status'=>0,'info'=>'填写不完整']);
			}
			$uids = explode("\n",$uids);
			$uids = array_map(function($uid){
				return trim($uid);
			},$uids);
			//检测uid合法
			$uids = array_unique($uids);
			$users = D('userstatic')->field('uid,phone')->where(['uid'=>['in',$uids]])->select();
			if(count($users) != count($uids)) return $this->ajaxReturn(['status'=>0,'info'=>'UID格式不对或者不存在UID']);

		}
		else{
			$timestart = date('Y-m-d',strtotime(I('post.timestart')))." 00:00:00";
			$timeend = date('Y-m-d',strtotime(I('post.timeend')))." 23:59:59";
			$expire = I('post.expire');
			$limit = I('post.limit');
			$samelimit = I('post.samelimit');
			$getway = I('post.getway');
			$sameactivitylimit = I('post.sameactivitylimit');

			if(empty($timestart) || empty($timeend) || empty($expire)||empty($limit)
				||empty($samelimit)||empty($getway)||empty($sameactivitylimit)){
				return $this->ajaxReturn(['status'=>0,'info'=>'填写不完整']);
			}

		}
		$coupons = array_map(function ($co){
			if(isset($co['sendnum']) && ($co['num']<$co['sendnum']))
				return $this->ajaxReturn(['status'=>0,'info'=>'修改发放数量不能小于实际已发放的数量']);
			return $co;
		},$coupons);
		//添加优惠卷
		foreach ($coupons as $k => $coupon){
			//创建优惠卷
			/*if(!$coupon['condition'])
				continue;*/
			$coupon['condition'] = $coupon['condition']?$coupon['condition']:0;
			$couponrule['basePrice'][1] = $coupon['condition'];

			if(isset($uids)){
				$sendnum = count($uids);
			}
			elseif(isset($coupon['sendnum'])){
				$sendnum = $coupon['sendnum'];
			}
			else {
				$sendnum = 0;
			}

			$data = ['name'=>"{$name}优惠券",'type'=>$type,
					 'price'=>$coupon['price'],'max_number'=>$coupon['num'],
			         'condition'=>json_encode($couponrule),
				     'send_number' => $sendnum,
				     /*'stime' => $timestart,
					 'etime' => $timeend,*/
					 'expire'=> $coupon['couponexpire'],
				     'status'=>1,
					];
			$cid = $coupon['cid'];
			if(!$cid)
				$cid = D('dueCoupon')->add($data);
			else
			{
				$data['utime'] = date('Y-m-d H:i:s');
				$affectrow = D( 'dueCoupon' )->where( [ 'cid' => $cid ] )->save( $data );
			}
			if(!$cid) return $this->ajaxReturn(['status'=>0,'info'=>'插入失败']);
			//添加允许
			//$couponallow['all'][] = $cid;
			//$c[$cid] = $coupon['num'];
			$couponjson[$cid] = $coupon['num'];
			//unset($c);
			$couponids[] = $cid;
			if($coupon['user']) $couponallow['user'][] = $cid;
			if($coupon['anchor']) $couponallow['anchor'][] = $cid;
		}

		//添加活动
		$activityrule['configPackageConfig'][1] = json_encode($couponjson);
		$activityrule['configUserReceiveLimit'][1] = implode(',',$couponallow['user']);
		$activityrule['configAnchorReceiveLimit'][1] = implode(',',$couponallow['anchor']);
		$activityrule['configActivityTime'][1] = "{$timestart},{$timeend}";
		$activityrule['configActivityReceiveLimit'][1] = $limit?$limit:0;
		$activityrule['configExpireTime'][1] = $expire?$expire:0;
		$activityrule['configReceiveType'][1] = $getway?$getway:0;
		$activityrule['configActivitySameReceiveLimit'][1] = $sameactivitylimit?$sameactivitylimit:0;
		$activityrule['configActivityEveryoneReceiveLimit'][1] = $samelimit?$samelimit:0;
		$activityrule = json_encode($activityrule);
		$activitydata = [
			'type'=>$type,
			'name'=>$name,
			'configure'=>$activityrule,
			'status'=>1,
			'pic' => $poster,
			//'send_number' => isset($uids)?count($uids):0,
		];
		if(isset($uids) && count($uids))
			$activitydata['send_number'] = count($uids);

		/*if($expire)
			$activitydata['expire'] = $expire;*/
		if($timestart)
			$activitydata['stime'] = $timestart;
		if($timeend)
			$activitydata['etime'] = $timeend;
		$r = I('post.aid');
		if(!$r)
			$r = D('dueActivity')->add($activitydata);
		else
		{
			$activitydata['utime'] = date('Y-m-d H:i:s');
			$affectrow = D( 'dueActivity' )->where( [ 'aid' => $r ] )->save( $activitydata );
		}

		if(!$r) return $this->ajaxReturn(['status'=>0,'info'=>'创建活动失败']);

		//优惠券绑定活动
		if(is_array($couponids) && count($couponids))
			$couponaffect = D('dueCoupon')->where(['cid'=>['in',$couponids]])->save(['aid'=>$r]);

		//内部发放 uid检测
		if($type=='4' && $uids){
			//todo
			//添加内部发放记录
			//后续改为异步的方式
			$day = $coupons[0]['couponexpire'] -1;
			if($day >= 0){
				$etime = date('Y-m-d',strtotime("$day day")) ." 23:59:59";
			}
			else{
				$etime = date('Y-m-d H:i:s');
			}
			foreach ($users as $user){
				$records[] = $record = [
					'uid' => $user['uid'],
					'phone' => $user['phone'],
					'price' => $coupon['price'],
					'coupon_id' => $cid,
					'type'  => $type,
					'stime' => date('Y-m-d H:i:s'),
					'etime' => $etime,
					'activity_id' => $r,
					'status' => D('dueUserCoupon')->getreceive(),
				];
			}
			$res = D('dueUserCoupon')->addAll($records);
			if(!$res) return $this->ajaxReturn(['status'=>0,'info'=>'发放内部优惠卷失败']);
		}

		return $this->ajaxReturn(['status'=>1,'info'=>'操作成功']);
	}
	public function coupongrant(){
		if($id=I('get.id')){
			$where['mid'] = $id;
		}
		$data = D('redPacket')->where($where)->select();
		$this->data = $data[0];
		$this->display();
	}

	public function addactivity(){

		$dao = D('dueActivity');

		$_GET['status'] = isset($_GET['status'])?$_GET['status']:'-1';

		if($aid = I('get.aid')){
			$where['aid'] = $aid;
		}
		if($aname = I('get.aname')){
			$where['name'] = ['like',"%$aname%"];
		}
		$status = I('get.status');
		if($status!='-1')
			$where['status'] = $status;
		if($export = I('get.export')){//导出数据
			$datas = $dao
				->where($where)
				->order('aid desc')
				->select();
		}else{
			$count = $dao
				->where($where)
				->count();
			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
			$datas = $dao
				->where($where)
				->limit($Page->firstRow.','.$Page->listRows)
				->order('aid desc ')
				->select();
		}
		$rpkstatus = ['0'=>'未上架','1'=>'已上架'];
		if($export = I('get.export')){//导出数据
			$excel[] = array('创建时间','活动ID','活动名称','活动状态','修改时间');
			foreach ($datas as $data) {
				$excel[] = array($data['ctime'],$data['aid'],$data['name'],$rpkstatus[$data['status']],$data['utime']);
			}
			\HP\Util\Export::outputCsv($excel,date('Y-m-d').'活动列表');
		}
		$this->data = $datas;
		$this->page = $Page->show();
		$this->status = $rpkstatus;
		$this->display();
	}
	public function addactivitysave(){


		//获取优惠卷类别
		$coupons = D('dueCoupon')->select();

		//获取活动类型
		$typeresult = D('dueActivityCategory')->where(['status'=>'1'])->select();
		$types = [];
		foreach ($typeresult as $t){
			$types[$t['id']] = $t['name'];
		}
		unset($typeresult);

		//获取规则
		$rules = D('dueCouponConfig')->select();

		//
		$activityrule = $rules[0]['config'];
		//
		$couponrule = $rules[1]['config'];

		$activityhtml = $this->ruletohtml($activityrule);
		$couponhtml = $this->ruletohtml($couponrule);

		$this->activityhtml = $activityhtml;//dump($activityhtml);
		//dump($activityhtml);
		$this->couponhtml = $couponhtml;//dump($couponhtml);
		$this->types = $types;
		$this->coupons = $coupons;
		$this->display();
	}

	public function addactivityedit(){
		$id = I('get.id');
		//获取活动
		$activity = D('dueActivity')->where(['aid'=>$id])->select();
		$activitydata = $activity[0];
		$typename = D('dueActivityCategory')->where(['id'=>$activitydata['type']])->select();
		$activitydata['typename'] = $typename[0]['name'];
		//获取优惠卷
		//dump($activity);
		$activity = json_decode($activity[0]['configure'],true);
		//dump($activity);
		//
		$couponids = json_decode($activity['configPackageConfig'][1],true);

		$userlimit = explode(',',$activity['configUserReceiveLimit'][1]);
		$anchorlimit = explode(',',$activity['configAnchorReceiveLimit'][1]);
		$activitydata['configReceiveType'] = explode(',',$activity['configReceiveType'][1])[0];
		$activitydata['linkexpire'] = explode(',',$activity['configExpireTime'][1])[0];
		//一个链接一个用户领取限制
		$activitydata['linklimit'] = explode(',',$activity['configActivityEveryoneReceiveLimit'][1])[0];
		//一个链接可领取优惠券数量
		$activitydata['activitlinklimit'] = explode(',',$activity['configActivityReceiveLimit'][1])[0];
		//同一活动用户可领取的优惠券数量
		$activitydata['configActivitySameReceiveLimit'] = explode(',',$activity['configActivitySameReceiveLimit'][1])[0];

		/*$configActivityEveryoneReceiveLimit = explode(',',$activity['configActivityEveryoneReceiveLimit'][1]);
		$anchorlimit = explode(',',$activity['configAnchorReceiveLimit'][1]);*/

		$coupons = [];
		foreach ($couponids as $cid => $num){
			$coupon['cid'] = $cid;
			$coupon['num'] = $num;
			$condition = D('dueCoupon')->where(['cid'=>$cid])->select();
			$price = $condition[0]['price'];
			$sendnum = $condition[0]['send_number'];
			$couponexpire = $condition[0]['expire'];
			$condition = json_decode($condition[0]['condition'],true);
			$condition = $condition['basePrice'][1];
			$coupon['condition'] = $condition;
			$coupon['userlimit'] = in_array($cid,$userlimit)?1:0;
			$coupon['anchorlimit'] = in_array($cid,$anchorlimit)?1:0;
			$coupon['price'] = $price;
			$coupon['sendnum'] = $sendnum;
			$coupon['couponexpire'] = $couponexpire;
			$coupons[] = $coupon;
		}
		//dump($coupons);
		//获取uid
		if('4' == $activitydata['type'])
		{
			$uids = D( 'dueUserCoupon' )->field('uid')->where( [ 'coupon_id' => $cid ] )->select();
			$uids = array_map(function($uid){return $uid['uid'];},$uids);
			$this->uids = $uids;
		}
		$activitydata['coupons'] = $coupons;
		$activitydata['stime'] = date('Y-m-d',strtotime($activitydata['stime']));
		$activitydata['etime'] = date('Y-m-d',strtotime($activitydata['etime']));
		$this->data =  $activitydata;
		$this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
		$this->display('addactivityedit');

	}

	public function record(){
		$dao = D('dueUserCoupon');
		$userDao = D('userstatic');
		$channelDao = D('channelUser');
		$code = D('promocode')->field('promocode,name')->getField('promocode,name');

		//获取活动
		$activitys = D('dueActivity')->field('aid,name')->getField('aid,name');
		//获取优惠卷
		$coupons = D('dueCoupon')->field('cid,name')->getField('cid,name');
		$_GET['status'] = isset($_GET['status'])?$_GET['status']:'-1';
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d');
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
		if(($stime = I('get.timestart'))&&($etime = I('get.timeend'))){
			$where['a.ctime'] = ['between',["$stime 00:00:00","$etime 23:59:59"]];
		}
		if($cid = I('get.cid')){
			$where['a.coupon_id'] = $cid;
		}
		if($aid = I('get.aid')){
			$where['a.activity_id'] = $aid;
		}
		if($nick = I('get.nick')){
			$where['b.nick'] = ['like',"%$nick%"];
		}
		if($uid = I('get.uid')){
			$where['a.uid'] = $uid;
		}
		if($codeid = I('get.codeid')){
			$where['c.promocode'] = $codeid;
		}
		$status = I('get.status');
		if($status=='3'){
			$where['etime'] = ['lt',date("Y-m-d H:i:s")];
			$where['status'] = ['lt',2];
		}
		elseif($status!='-1')
			$where['status'] = $status;

		if($export = I('get.export')){//导出数据
			$datas = $dao
				->alias(' a ')
				->join(" left join ".$userDao->getTableName()." as b on a.uid = b.uid ")
				->join(" left join ".$channelDao->getTableName()." as c on a.uid = c.uid ")
				->field('a.*,b.nick,c.promocode')
				->where($where)
				->order('a.ctime desc')
				->select();
		}else{
			$count = $dao
				->alias(' a ')
				->join(" left join ".$userDao->getTableName()." as b on a.uid = b.uid ")
				->join(" left join ".$channelDao->getTableName()." as c on a.uid = c.uid ")
				->where($where)
				->count();
			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
			$datas = $dao
				->where($where)
				->alias(' a ')
				->join(" left join ".$userDao->getTableName()." as b on a.uid = b.uid ")
				->join(" left join ".$channelDao->getTableName()." as c on a.uid = c.uid ")
				->field('a.*,b.nick,c.promocode')
				->limit($Page->firstRow.','.$Page->listRows)
				->order('a.ctime desc')
				->select();
		}

		//获取手机、身份证、银行卡权限
		$phoneauth = \HP\Op\Admin::checkAccessWithKey('phoneauthkey');
		//$certauth  = \HP\Op\Admin::checkAccessWithKey('certauthkey');

		foreach ($datas as &$data){
			if(!$phoneauth) $data['phone'] = get_secure_phone($data['phone']);
			//if(!$certauth) $data['papersid'] = get_secure_cert($data['papersid']);
		}

		$rpkstatus = $dao->getrpkstatus();
		if($export = I('get.export')){//导出数据
			$excel[] = array('创建时间','优惠卷','活动','用户ID','用户昵称','手机号','优惠券码','金额（欢朋币）','推广码','订单','开始时间','结束时间','状态');
			foreach ($datas as $data) {
				$excel[] = array($data['ctime'],$coupons[$data['coupon_id']],$activitys[$data['activity_id']],$data['uid'],$data['nick'],$data['phone'],$data['code'],$data['price'],$code[$data['promocode']],"\"{$data['orderid']}\"",$data['stime'],$data['etime'],$rpkstatus[$data['status']]);
			}
			\HP\Util\Export::outputCsv($excel,date('Y-m-d').'优惠卷列表');
		}
		//$dao->getrpkstatus();
		$this->data = $datas;
		$this->code = $code;
		$this->coupons = $coupons;
		$this->activitys = $activitys;
		$this->page = $Page->show();
		$this->status = $rpkstatus;
		$this->display();
	}

	/**
	 * 规则转html
	 */
	public function ruletohtml($rule){
		return json_decode($rule,true);
		if(!$rule) return '';
		if(!$rule = json_decode($rule)) return '';
		$htmlstr = '';
		$decode = [];
		$xml = '';
		foreach ($rule as $key => $value){
			if( 'json'==strtolower($value[0]) && 'packageConfig'==$key ){
				foreach ($value[1] as $v){

					$xml .= "<input value=\"{$v["value"]}\" class=\"span2\" type=\"text\" placeholder=\"面值\"/>欢朋币&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				$decode['coupon']['html'] = $xml;
				$decode['coupon']['desc'] = $value[2];
			}
			elseif('in'==strtolower($value[0]) && ('userReceiveLimit'==$key)){
				$xml = "<checkbox></checkbox>";
				$decode['userReceiveLimit']['html'] = $xml;
				$decode['userReceiveLimit']['desc'] = $value[2];
			}
			elseif('in'==strtolower($value[0]) && ('achorReceiveLimit'==$key)){
				$xml = "<checkbox></checkbox>";
				$decode['achorReceiveLimit']['html'] = $xml;
				$decode['achorReceiveLimit']['desc'] = $value[2];
			}
			elseif('between'==strtolower($value[0]) && 'activityTime'==$key){
				$xml1 = "<input type=\"text\" value=\"12-02-2012\" data-date-format=\"mm-dd-yyyy\" class=\"span11\">";
				$xml2 = "<input type=\"text\" value=\"12-02-2012\" data-date-format=\"mm-dd-yyyy\" class=\"span11\">";
				$decode['time'][0]['html'] = $xml1;
				$decode['time'][0]['desc'] = '开始时间';
				$decode['time'][1]['html'] = $xml2;
				$decode['time'][1]['desc'] = '结束时间';
			}
			elseif('eq'==strtolower($value[0]) && 'expireTime'==$key){
				$xml = "<input placeholder=\"$value[2]\"/>";
				$decode['expire']['html'] = $xml;
				$decode['expire']['desc'] = $value[2];
			}
			elseif('eq'==strtolower($value[0]) && 'activityLinkReceiveLimit'==$key){
				$xml = "<input placeholder=\"$value[2]\"/>";
				$decode['activityLinkReceiveLimit']['html'] = $xml;
				$decode['activityLinkReceiveLimit']['desc'] = $value[2];
			}

			elseif('egt'==strtolower($value[0]) && 'basePrice'==$key){
				$xml = "<input placeholder=\"$value[2]\"/>";
				$decode['basePrice']['html'] = $xml;
				$decode['basePrice']['desc'] = $value[2];
			}
		}
		return $decode;
	}

	/**
	 * html转规则
	 */
	public function htmltorule($html){
		return json_decode($html,true);
	}
}

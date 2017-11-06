<?php

namespace Admin\Controller;
use HP\Op\Live;
use HP\Log\Log;
use HP\Op\Admin;
use HP\Op\publicRequist;
class UserController extends BaseController{

    protected $pageSize = 10;

    protected function _access(){
        return [
            'addsilence' => ['userlist'],
            'delsilence' => ['userlist'],
			'checklive' => ['userlist'],
			'delblack'=>['userlist'],
			'delsilencesave'=>['userlist'],
            'disableuser'=>['userlist'],
            'enableuser'=>['userlist'],
        ];
    }

   /**  
    * 用户查询列表
    */
    public function userlist()
    {
        $userstaticDao = D('userstatic');
        $channelUserDao = D('channelUser');
        if($id = I('get.uid')){
            $where['a.uid'] = $id;
        }
        if($username = I('get.username')){
            $where['a.username'] = ['like', "%$username%"];
        }
        if($nick = I('get.nick')){
            $where['a.nick'] = ['like', "%$nick%"];
        }
        if($phone = I('get.phone')){
            $where['a.phone'] = ['like', "%$phone%"];
        }
        if($mail = I('get.mail  ')){
            $where['a.mail'] = ['like', "%$mail%"];
        }
        if($startTime = I('get.timestart')) {
            $where['a.rtime'][] = ['egt', $startTime . ' 00:00:00'];
        }
		if($endTime = I('get.timeend')) {
            $where['a.rtime'][] = ['elt', $endTime . ' 23:59:59'];
        }
        if($channel = I('get.channel')) {
        	$where['b.channel'] = $channel;
        }
        if($promocode = I('get.promocode')) {
        	$where['promocode'] = $promocode;
        }
		
        $this->exportMax = 1000;
        if($export = I('get.export')){//导出数据
        	$results = $userstaticDao
        		->alias('a')
        		->join(" left join ".$channelUserDao->getTableName()." b on a.uid = b.uid ")
        		->field('a.uid,a.nick,a.pic,a.rtime,a.phone,a.mail,b.channel,b.promocode')
        		->where($where)
        		->order('a.uid desc')
        		->limit(0, $this->exportMax)
        		->select();
        }else{
            $count = $userstaticDao
            	->alias('a')
            	->join(" left join ".$channelUserDao->getTableName()." b on a.uid = b.uid ")
            	->where($where)
            	->count();
            $Page = new \HP\Util\Page($count,$_GET['export'] ? 0 : $this->pageSize);
            $results = $userstaticDao
				->alias('a')
				->join(" left join ".$channelUserDao->getTableName()." b on a.uid = b.uid ")
				->field('a.uid,a.nick,a.pic,a.rtime,a.phone,a.mail,b.channel,b.promocode')
                ->where($where)
				->group('uid')
                ->order('a.uid desc')
                ->limit($Page->firstRow, $Page->listRows)
                ->select();
            if($results) {
            	$uids = array_column($results, 'uid');
                $con['type'] = 10;
                $con['uid'] = ['in', $uids];
                $con['etime'] = ['gt', time()];
                $disbaleList = D('userDisableStatus')->where($con)->getField('uid,etime');
                $silenceList = D('usersilence')->where(['type'=>1, 'luid'=>['in', $uids], etime=>['gt', date('Y-m-d H:i:s')]])->getField('luid,etime');
                
                foreach($results as $k=>$v) {
                    $results[$k]['pic'] = avator($v['pic']);
                    $results[$k]['status'] = isset($disbaleList[$v['uid']]) ? 2 : 1;
                    $results[$k]['type'] = isset($silenceList[$v['uid']]) ? 1 : 0;
                }
            }
        }

		//获取手机、身份证、银行卡权限
		$phoneauth = \HP\Op\Admin::checkAccessWithKey('phoneauthkey');

		foreach ($results as &$data){
			if(!$phoneauth) $data['phone'] = get_secure_phone($data['phone']);
		}

        if($export = I('get.export')){//导出数据
            $excel[] = array('UID','昵称','注册时间','手机','邮箱','渠道','推广码');
            foreach ($results as $data) {
            	$excel[] = array($data['uid'],$data['nick'],$data['rtime'],$data['phone'],$data['mail'],$data['channel'],$data['promocode']);
            }
            \HP\Util\Export::outputCsv($excel, '用户列表');
        }

		$this->reasontype = Live::getUnpassreson();
        $this->data = $results;
        $this->page = $Page->show();
        
        $this->channel = D('channelVersion')->where(['status'=>1])->getField('channel,channelName');
        $this->promocode = D('Promocode')->where(['status'=>1])->getField('promocode,name');
        $this->display();
    }
    
    /** 
     * 添加禁言
     */
    function addsilence()
    {
        if(!($luid = I('post.s_uid'))){
            $msg = ['status' => 0, 'info' => '缺少参数'];
            return $this->ajaxReturn($msg);
        }
        if(!($roomid = I('post.s_roomid'))){
            $roomid = 0;
        }
        $reason = I('post.s_reason', '');
        $timeLength = I('post.s_timeLength', '');
        $stime = time();
        $etime = $timeLength ? ($stime + $timeLength) : strtotime('+10 years');
        $content = array('stime'=>$stime, 'etime'=>$etime);
		$anchoruid=$this->getUidsByRoomid($roomid);
		$prm=array('uid'=>$luid,'type'=>20,'status'=>2,'scope'=>$anchoruid,'etime'=>$timeLength);
		$back=publicRequist::disuser($prm);
		Log::statis('dprm=='.json_encode($prm).'----callback--'.$back,'','disuser');
        $uid = \HP\Op\Admin::getUid();
        $data = array(
            'uuid' => $uid,
            'utime' => date('Y-m-d H:i:s'),
            'type' => 2
        );
        $Dao = D('usersilence');
        $where = '`luid`=' . $luid . ' and `roomid`=' . $roomid . ' and `type`=1';
        $Dao->where($where)->save($data);
        
        $data = array(
            'uid' => $uid,
            'luid' => $luid,
            'roomid' => $roomid,
            'fromto' => 1,
            'reason' => $reason,
            'stime' => date('Y-m-d H:i:s', $stime),
            'etime' => date('Y-m-d H:i:s', $etime),
            'type' => 1
        );
        $Dao->add($data);
        $msg = ['status' => 1];
        return $this->ajaxReturn($msg);
    }
    
    /**  
     * 删除禁言
     */
    function delsilence()
    {
        if(!($luid = I('post.s_uid'))){
            exit(json_encode(['status' => 0, 'info' => '缺少参数']));
        }
        $roomid = I('post.s_roomid', 0);
		$anchoruid=$this->getUidsByRoomid($roomid);
		$prm=array('uid'=>$luid,'type'=>20,'status'=>1,'scope'=>$anchoruid,'etime'=>0);
		$back=publicRequist::disuser($prm);
		Log::statis('udprm=='.json_encode($prm).'----callback--'.$back,'','undisuser');
        $Dao = D('usersilence');
        $where = '`luid`=' . $luid . ' and `roomid`=' . $roomid . ' and `type`=1';
        $data = array(
            'uuid' => \HP\Op\Admin::getUid(),
            'utime' => date('Y-m-d H:i:s'),
            'type' => 2
        );
        
        $Dao->where($where)->save($data);
        $msg = ['status' => 1,'info'=>'操作成功'];
        return $this->ajaxReturn($msg);
    }

	function delsilencesave(){
		$Dao = D('usersilence');
		$where = [];
		if($uid=I('get.uid')){
			$where['luid'] = $uid;
		}else{
			$where['luid'] = 0;
		}
		$where['type'] = 1;
		$results = $Dao->where($where)->select();
		//dump($results);
		$this->data = $results;
		$this->display();
	}
    
    /**  
     * 禁言列表
     */
    function silencelist()
    {
        $userstaticDao = D('userstatic');
        $Dao = D('usersilence');
        if($id = I('get.uid')){
            $where['a.uid'] = $id;
        }
        if($username = I('get.username')){
            $where['a.username'] = ['like', "%$username%"];
        }
        if($nick = I('get.nick')){
            $where['a.nick'] = ['like', "%$nick%"];
        }
        if($phone = I('get.phone')){
            $where['a.phone'] = ['like', "%$phone%"];
        }
        if($mail = I('get.mail')){
            $where['a.mail'] = ['like', "%$mail%"];
        }
        if($roomid = I('get.roomid')){
            $where['b.roomid'] = $roomid;
        }
        $where['b.type'] = 1;
        $where['etime'] = ['egt', date('Y-m-d H:i:s')];
        
        $count = $userstaticDao
            ->alias(' a ')
            ->join($Dao->getTableName()." as b on a.uid = b.luid")
            ->where($where)
            ->field('a.uid,a.nick,a.pic,a.phone,a.mail,b.etime,b.uid as admin_id')
            ->count();
        
        $Page = new \HP\Util\Page($count, $this->pageSize);
        $results = $userstaticDao
            ->alias(' a ')
            ->join($Dao->getTableName()." as b on a.uid = b.luid")
            ->where($where)
            ->order('b.stime desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field('a.uid,a.nick,a.pic,a.phone,a.mail,b.roomid,b.etime,b.uid as admin_uid')
            ->select();
		//获取手机、身份证、银行卡权限
		$phoneauth = \HP\Op\Admin::checkAccessWithKey('phoneauthkey');

        if($results) {
            foreach($results as $k=>$v) {
            	$results[$k]['pic'] = avator($v['pic']);
				if(!$phoneauth)  $results[$k]['phone'] = get_secure_phone($v['phone']);
            }
        }
        $this->data = $results;
        $this->page = $Page->show();
        $this->display();
    }
    
    function getRedisSilence()
    {
        $luid = I('post.s_uid');
        $roomid = I('post.s_roomid', 0);
        $redis = new \Think\Cache\Driver\Redis();
        $redis->hdel('silence_' . $roomid, $luid);
        $timeLength = I('get.s_timeLength', '');
        $stime = time();
        $etime = $timeLength ? ($stime + $timeLength) : strtotime('+10 years');
        $content = array('stime'=>$stime, 'etime'=>$etime);
        $redis = new Think\Cache\Driver\Redis();
        $redis->hset('silence_' . $roomid, $luid, json_encode($content));
    }
    
    /**  
     * 删除主播禁播
     */
    function delblack(){
		$msg = ['status'=>0,'info'=>'操作失败'];
		if(IS_POST){
			$uid = I('post.uid');
			if( $uid ){
				$dao = D('AnchorBlackRecord');
				$reasontype = '';
				$reason = '';
				$type = Live::$msgTypeGroup['cancel'];
				$data = array(
					"liveid" => 0,
					"type" => $type,
					"luid" => $uid,
					"uid" => Admin::getUid(),
					"reason" => $reasontype,
					"content"=>'',
				);
				$resid = $dao->add($data);

				$dao = D('AnchorBlacklist');
				$res = $dao->where(['luid'=>$uid])->delete();
				if($res)$msg = ['status'=>1,'info'=>'操作成功'];
			}
		}
		return $this->ajaxReturn($msg);
	}
    
    /**  
     * 对用户封号
     */
    function disableuser()
    {
        $msg = ['status'=>0,'info'=>'操作失败'];
        if(IS_POST){
            $luid = I('post.luid');
            $reasontype = I('post.reasontype');
            $reason = I('post.reason');
            $length = I('post.b_timeLength', 0);
            
            //断流开始
            $anchor = \HP\Op\Anchor::anchorInfo([$luid]);
            if($anchor) {
                $opt['luid'] = $luid;
                $opt['reasontype'] = $reasontype;
                $opt['reason'] = $reason;
                $opt['act'] = 'stop';
                Live::checklive(0, $opt);
            }
            //断流结束
            
            $data = [
                'uid' => $luid,
                'type' => 10, //10(登陆操作)，20（禁言操作), 30(直播操作) 必选
                'status' => 2, //(int) 1(解禁)，2(封禁) 必选
                'etime' => $length,
            	'ac_uid' => get_uid(),
            	'ext_text' => Live::getUnpassreson()[$reasontype] . '|' . $reason
                ];
            $msg = publicRequist::disuser($data);//事件推送
            $msg = json_decode($msg, true);
            if($msg['status'] == 1) {
                $insert = [
                    'uid' => $luid,
                    'reasontype' => $reasontype,
                    'reason' => $reason,
                    'timelength' => $length,
                    'adminid' => get_uid(),
                    'type' => 1,   //(int) 1(封禁)，2(解禁)
                    'stime' => date('Y-m-d H:i:s'),
                    'etime' => $length == 0 ? '2099-12-31 23:59:59' : date('Y-m-d H:i:s', time() + $length)
                    ];
                D('userblockedlist')->add($insert);
            }
        }
        return $this->ajaxReturn($msg);
    }
    
    /**  
     * 取消封号
     */
    function enableuser()
    {
        $msg = ['status'=>0,'info'=>'操作失败'];
        if(IS_POST){
            $luid = I('post.luid');
            $data = [
                'uid' => $luid,
                'type' => 10, //10(登陆操作)，20（禁言操作), 30(直播操作) 必选
                'status' => 1, //(int) 1(解禁)，2(封禁) 必选
                'etime' => 0,
            	'ac_uid' => get_uid(),
                ];
            $msg = publicRequist::disuser($data);

            $msg = json_decode($msg, true);
            if($msg['status'] == 1) {
                $insert = [
                    'uid' => $luid,
                    //'reasontype' => $reasontype,
                    //'reason' => $reason,
                    'adminid' => \HP\Op\Admin::getUid(),
                    'type' => 2 //(int) 1(封禁)，2(解禁)
                    ];
                D('userblockedlist')->add($insert);
            }
        }
        return $this->ajaxReturn($msg);
    }


    function  getUidsByRoomid($roomid){
		$dao=D('roomid');
		$res=$dao->where("roomid=$roomid")->limit(1)->select();
		if($res){
			return $res[0]['uid'];
		}else{
			return 1;
		}
	}

}

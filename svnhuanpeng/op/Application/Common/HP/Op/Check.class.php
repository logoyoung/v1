<?php
// +----------------------------------------------------------------------
// | 审核类
// +----------------------------------------------------------------------
// | Author: zwq 2017年5月15日
// +----------------------------------------------------------------------
namespace HP\Op;
use Common\Model\WithdrawBaseModel;
use HP\Cache\CacheKey;
use HP\Util\Room;
use HP\Op\Message;
use HP\Log\Log;
use HP;
class Check extends \HP\Cache\Proxy{
    static $setMsgType=0;//指定用户
	static $isSiteMsg = 1; //发送了站内信的同时推送一条消息
	static $msgTitle = '审核失败'; //标题
	static $rpass=1; //实名通过
	static $runpass=2; //实名未通过
    
    /**
     * 根据uids 头像审核通过。
     * 1.更新 admin_user_pic 表 status=1 //头像审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
     * 2.更新userstatic pic字段
     * 3.同步任务：首次提交头像并且审核通过。
     * 
     */
    static function headPicPass($datas){
        if(!is_array($datas)) return false;
        $uids = array_keys($datas);
        $db = D('UserPic');
        $updata['utime'] = get_date();
        $updata['status'] = USER_PIC_PASS;
        $where['uid'] = ["in",$uids];
        $res = $db->where($where)->save($updata);
        $db = D("Userstatic");
        foreach ($datas as $uid=>$pic){
            $keys = "IsFirstUploadPic:" . $uid;//头像上传任务
            $resul = S($keys);
            if (!$resul) {
                //S($keys,1);
                //synchroTask($uid, 6, 0, 100); //同步任务
            }
            $uppic['pic'] = $pic;
            $db->where(["uid"=>$uid])->save($uppic);
        }
        return $res;
    }
    
    /**
     * 根据uids 设置未通过
     * 
     */
    static function headPicUnpass($uids){
        if(!is_array($uids))return false;
        $db = D("userPic");
        $where['uid'] = ['in',$uids];
        $data['status'] = USER_PIC_UNPASS;
        $data['utime'] = get_date();
        $res = $db->where($where)->save($data);
        return $res;
    }
    
    /**
     * 通过的要变成未通过
     */
    static function headPicpton($uids){
        if(!is_array($uids))return false;
        $db = D("userPic");
        $where['uid'] = ['in',$uids];
        $data['status'] = USER_PIC_UNPASS;
        $data['utime'] = get_date();
        $res = $db->where($where)->save($data);
        $data = null;
        $db = D("userstatic");
        $where['uid'] = ['in',$uids];
        $data['pic'] = DEFAULT_HEAD_PATH;
        $data['utime'] = get_date();
        $res = $db->where($where)->save($data);
        return true;
    }
    
    //锁定当前页用户头像审核uid
    static function initdiffuid($key,$uids,$adminuid=0){
        $adminuid?$adminuid=$adminuid:$adminuid=Admin::getUid();
        if($key&&$adminuid && is_array($uids)){
            $datas = S($key);
            $datas[$adminuid] = $uids;
            S($key,$datas,['expire'=>3600]);
            return true;
        }else{
            return false;
        }
    }
    //获取被锁定的uid
    static function getdiffuid($key,$adminuid=0){
        $adminuid?$adminuid=$adminuid:$adminuid=Admin::getUid();
        if($adminuid && $key){
            $datas = S($key);
            unset($datas[$adminuid]);
            if($datas&&is_array($datas)){
                $ruids=[];
                foreach ($datas as $admuid=>$uids){
                    $ruids = array_merge($ruids,$uids);
                }
                return array_unique($ruids);
            }
        }else{
            return false;
        }
    }
    
    
    
    /* 
     *  实名认证审核通过
     *  ·更新 userrealname 表
     *  ·添加到主播表
     *  ·更新内测记录表
     *  ·设置roomid表
     *  ·增加角色变更记录
     *  ·增加汇率变更记录
     *  ·通知财务系统
     *      1.是 更新汇率变更记录表状态
     *      2.否 记录失败日志
     */
    static function realnamePass($map){
        if( !is_array($map) ) return false;
        $ids = array_keys($map);
        $uids = array_values($map);
        //1
        $realnameDao = D('userrealname');
        $data['passtime'] = get_date();
        $data['status'] = RN_PASS;
		$data['adminid'] = Admin::getUid();
        $where['id'] = ['in',$ids];
        $realnameDao->where($where)->save($data);
        //2
        $dao = D("anchor");
        foreach ($map as $id=>$uid){
            $data['uid'] = $uid;
            $data['utime'] = get_date();
            $data['rate'] = BASE_RATE;
			$data['cert_status'] = 1;
            $datas[] = $data;
			self::realnamelog($uid,$data['adminid'],self::$rpass);//日志
        }
        $dao->addAll($datas,'',true);
        //3
        $dao = D('InsideTestInviteRecoed');
        $data = $where = [];
        $data['status'] = 1;
        $where['ruid'] = ['in',$uids];
        $dao->where($where)->save($data);
        //4
        $dao = D('roomid');
        $datas = [];
        foreach ($uids as $uid){
            $roomid = Room::getoneid($uid);
            $data['roomid'] = $roomid;
            $data['uid'] = $uid;
            $data['utime'] = get_date();
            $datas[] = $data;
        }
        $dao->addAll($datas,'',true);
        
        //实名认证通过以后rate变化的通知财务系统
//      Anchor::rateChange($uids);
        return true;
    }
    
    //实名认证审核不通过
    static function realnameUnpass($ids,$reason,$uid){
        $realnameDao = D('userrealname');
        $data['passtime'] = get_date();
        $data['status'] = RN_UNPASS;
		$data['reason'] = "亲爱的欢朋用户！你的实名审核未通过，请重新提交认证。原因：$reason 如有疑问，请联系客服！";
		$data['adminid'] = Admin::getUid();
        $where['id'] = ['in',$ids];
        $realnameDao->where($where)->save($data);
		self::realnamelog($uid,$data['adminid'],self::$runpass,$reason);//日志
		//站内信消息
		$megCallBack = publicRequist::set_message( self::$setMsgType, self::$msgTitle, $data['reason'], $uid );
		Log::statis( json_encode( array( 'adminid' => $data['adminid'] ,'status'=>$data['status'], 'reason'=>$data['reason'],'type' => self::$setMsgType, 'title' => self::$msgTitle,  'uid' =>$uid , 'res' => json_decode( $megCallBack ) ) ),'','realname_msg_callback' );
		$megCallBack = json_decode( $megCallBack, true );
		if( $megCallBack['status'] == 1 )
		{
			//发推送消息
			$pushCallback = publicRequist::push_message( self::$setMsgType, self::$msgTitle, $data['reason'], $uid, self::$isSiteMsg );
			Log::statis( json_encode( array( 'adminid' => $data['adminid'], 'status'=>$data['status'],'luid' => $uid, 'reason' => $data['reason'], 'callback' => json_decode($pushCallback) ) ), '', 'realname_push_callback' );
		}
        return true;
    }
    
    /* 
     * 录像审核通过 zwq 2017年5月17日 add
     * ·vido表状态更新
     * ·推送消息
     */
    static function vidoePass($vid){
        $dao = D('video');
        $data['status'] = VIDEO;
        $where['videoid'] = $vid;
        $res = $dao->where($where)->save($data);
        
        $videoInfo = D('video')->find($vid);
        $liveInfo = D('live')->find($videoInfo['liveid']);
        $isauto = $liveInfo['antopublish'];
        $title='系统消息';
        if($isauto){
            $message="您的直播视频“".$videoInfo['gamename'].'-'.$videoInfo['title']."”已生成并发布成功！";
        }else{
            $message="您的直播视频“".$videoInfo['gamename'].'-'.$videoInfo['title']."”已发布成功！";
        }
        Message::sendMessages($videoInfo['uid'], $title, $message, 0);//发送消息
        
        
        $dao = D('WaitPassVideo');
        $data = array(
            'status' => VIDEO_CHECK_PASS,
            'utime' => date('Y-m-d H:i:s'),
            'admin_id' => get_uid(),
        );
        $dao->where(['videoid'=>$vid])->save($data);
		$dao = D('UnpassVideo');
		$data = null;
		$data['videoid'] = $vid;
		$data['adminid'] = get_uid();
		$data['type'] = 0;
		$data['describe'] = '';
		$dao->add($data);
        return true;
    }
    
    /* 
     * 录像审核通过 zwq 2017年5月17日 add
     * ·vido表状态更新
     * ·waitpassvideo 表状态更新
     * ·插入审核失败记录表
     * ·发送消息
     */
    static function vidoeUnpass($vid,$opt){
        //更新vido表
        $dao = D('video');
        $data['status'] = VIDEO_UNPASS;
        $where['videoid'] = $vid;
        $res = $dao->where($where)->save($data);
        //waitpassvideo 表状态更新
        $dao = D('WaitPassVideo');
        $data = array(
            'status' => VIDEO_CHECK_UNPASS,
            'utime' => date('Y-m-d H:i:s'),
            'admin_id' => get_uid(),
        );
        //插入审核失败记录表
        $dao->where(['videoid'=>$vid])->save($data);
        $dao = D('UnpassVideo');
        $data = null;
        $data['videoid'] = $vid;
        $data['adminid'] = get_uid();
        $data['type'] = $opt['reasontype'];
        $data['describe'] = $opt['reason'];
        $dao->add($data);
        //发送消息
        $videoInfo = D('video')->find($vid);
        $title='系统消息';
        $message="您的直播视频“".$videoInfo['gamename'].'-'.$videoInfo['title']."”未能通过审核，发布失败。";
        Message::sendMessages($videoInfo['uid'], $title, $message, 0);
        return true;
    }

    static function withdraw($date,$id,$status){
		$withdrawDao = new \Common\Model\WithdrawModel('exchange_detail',$date);
		$data = [
			'status'=>$status,
			'utime' => date("Y-m-d H:i:s")
		];
		$where['id'] = $id;
		HP\Log\Log::statis($withdrawDao->getTableName());
		$withdrawDao->where($where)->save($data);
		HP\Log\Log::statis(M()->getLastSql());
		return true;
	}
	/*static function withdrawUnpass($date,$id){
		$withdrawDao = D('Withdraw');
		$withdrawDao = $withdrawDao->selectTable($date);
		$data = [
			'status'=>'',
			'utime' => date("Y-m-d H:i:s")
		];
		$where['id'] = $id;
		$withdrawDao->where($where)->save($data);
		return true;
	}*/


	static  function realnamelog($uid,$adminid,$type,$reason=''){
		$dao=D('check_realname_record');
        $data=array(
		   'uid'=>$uid,
		   'reason'=>$reason,
		   'type'=>$type,
		   'adminid'=>$adminid
	   );
		$res=$dao->add($data);
		if(false ===$res){
			Log::statis( json_encode( array( 'adminid' => $adminid , 'reason'=>$reason,'type' =>$type, 'uid' =>$uid  ) ),'','check_realname_log' );
		}
	}
}

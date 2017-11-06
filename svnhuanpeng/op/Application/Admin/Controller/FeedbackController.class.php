<?php

namespace Admin\Controller;

use HP\Op\Admin;
use HP\Log\Log;
use HP\Op\Anchor;
use HP\Op\publicRequist;
class FeedbackController extends BaseController{
	static $setMsgType=0;//指定用户
	static $isSiteMsg = 1; //发送了站内信的同时推送一条消息
	static $reportMsgTitle = '举报回复'; //标题
	static $feedMsgTitle = '反馈回复'; //标题
    protected $pageSize = 10;
    public function _access()
    {
        return [
			'feedback' => [ 'feedback' ],
			'report' => [ 'feedback' ],
			'answer' => [ 'feedback' ],
			'feedback_answer'=>['feedback']
		];
    }
    
    function feedback()
    {
        $Dao = D('feedback');
        if($startTime = I('get.timestart')) {
            $where['tm'][] =['egt', $startTime.' 00:00:00'];
        }
		if($endTime = I('get.timeend')) {
            $where['tm'][] =['elt', $endTime.' 23:59:59'];
        }
		$status=I('get.status')?I('get.status'):0;
		if( !in_array($status,array(0,1,2)))
		{
			$message = [ 'status' => 0, 'info' => '状态非法' ];
			return $this->ajaxReturn( $message );
		}
		if($status) {
			if($status==1){
				$where['status'] = 0;
			}
			if($status==2){
				$where['status'] = 1;
			}
		}
        $count = $Dao->where($where)->count();
        $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
        
        if($export = I('get.export')){//导出数据
            $datas = $Dao->field("*")->where($where)->order('tm desc')->select();
        } else {
        $count = $Dao->field("*")->where($where)->count();
        $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
        $datas = $Dao
            ->field("*")
            ->where($where)
            ->order('tm desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        }
        if($datas) {
			$adao = D( 'AclUser' );
			$usernick=Anchor::anchorInfo(array_column($datas,'uid'));
			$adminsName = $adao->getField( "uid,realname" );
            foreach($datas as $k=>$v) {
                $feedback = json_decode($v['feedback'], true);
                $datas[$k]['contact'] = $feedback['contact'];
                $datas[$k]['comment'] = $feedback['comment'];
				$datas[$k]['usernick'] =$usernick[$v['uid']]['nick'];
				$datas[$k]['ruser'] =$adminsName[$v['adminid']]?$adminsName[$v['adminid']]:'';
            } 
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('用户ID','用户昵称','时间','联系方式','内容','状态','回复者','回复内容','回复时间');
            foreach ($datas as $data) {
				$data['comment'] = '"'.str_replace(array(',','&nbsp;','<br>','<br/>','<br />'),array('，',' ',PHP_EOL,PHP_EOL,PHP_EOL),$data['comment']).'"';
                $excel[] = array($data['uid'],$data['usernick'],"\t" .$data['tm'],"\t" .$data['contact'],$data['comment'],"\t" .$data['status'],$data['ruser'],"\t" .$data['answer'],"\t" .$data['rtime']);
            }
            \HP\Util\Export::outputCsv($excel,'意见反馈列表');
        }

        $this->data = $datas;
		$this->status = array('1' => '未回复', '2' => '已回复' );
        $this->page = $Page->show();
        $this->display();
    }
    
    function report()
    {
        $Dao = D('report');
        $Dao_user = D('userstatic');
        $Dao_room = D('roomid');
        if($startTime = I('get.timestart')) {
            $where['a.ctime'][] = ['egt', $startTime.' 00:00:00'];
        }
		if($endTime = I('get.timeend')) {
            $where['a.ctime'][] = ['elt', $endTime.' 23:59:59'];
        }
        if($uid = I('get.uid')) {
            $where['a.uid'] = $uid;
        } 
        if($luid = I('get.luid')) {
            $where['a.luid'] = $luid;
        }
		$status=I('get.status')?I('get.status'):0;
		if( !in_array($status,array(0,1,2)))
		{
			$message = [ 'status' => 0, 'info' => '状态非法' ];
			return $this->ajaxReturn( $message );
		}
		if($status) {
			if($status==1){
				$where['a.status'] = 0;
			}
			if($status==2){
				$where['a.status'] = 1;
			}
		}
		if($nick = I('get.nick')) {
            $where['b.nick'] = ['like', "%$nick%"];
        }      

        if($export = I('get.export')){//导出数据
            $datas = $Dao->alias(' a ')
                ->join(" left join " . $Dao_user->getTableName() . " as b on a.luid = b.uid ")
                ->field("a.*,b.nick")
                ->where($where)
                ->order('ctime desc')
                ->select();
        } else {
            $count = $Dao->alias(' a ')
                    ->join(" left join " . $Dao_user->getTableName() . " as b on a.luid = b.uid ")
                    ->where($where)
                    ->count();
            $Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
            $datas = $Dao
                ->alias(' a ')
                ->join(" left join " . $Dao_user->getTableName() . " as b on a.luid = b.uid ")
                ->join(" left join " . $Dao_room->getTableName() . " as c on a.luid = c.uid ")
                ->field("a.*,b.nick,c.roomid")
                ->where($where)
                ->order('ctime desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }
		$adao = D( 'AclUser' );
        $usernick=Anchor::anchorInfo(array_column($datas,'uid'));
		$adminsName = $adao->getField( "uid,realname" );
        if($datas) {
            foreach($datas as $k=>$v) {
                if($v['pic']) {
                    $datas[$k]['pic'] = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] .  '/' . $v['pic'];
                    $datas[$k]['room'] = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['outside-domain'] .  '/' . $v['roomid'];
                }
				$datas[$k]['usernick'] =$usernick[$v['uid']]['nick'];
				$datas[$k]['ruser'] =$adminsName[$v['adminid']]?$adminsName[$v['adminid']]:'';
            } 
        }
        
        if($export = I('get.export')){//导出数据
            $excel[] = array('举报者ID','时间','被举报者ID','被举报者昵称','直播间','原因','内容','截图','状态','回复者','回复内容','回复时间');
            foreach ($datas as $data) {
				$data['contact'] = '"'.str_replace(array(',','&nbsp;','<br>','<br/>','<br />'),array('，',' ',PHP_EOL,PHP_EOL,PHP_EOL),$data['contact']).'"';
                $excel[] = array($data['uid'],"\t" .$data['ctime'],"\t" .$data['luid'],$data['nick'],$data['room'],"\t" .$data['reason'],"\t" .$data['contact'],$data['pic'],$data['status'],"\t" .$data['ruser'],$data['answer'],"\t" .$data['rtime']);
            }
            \HP\Util\Export::outputCsv($excel,'举报列表');
        }

        $this->data = $datas;
		$this->status = array('1' => '未回复', '2' => '已回复' );
        $this->page = $Page->show();
        $this->display();
    }

    function answer(){
		I( "post.id" ) ? $id = I( "post.id" ) : '';
		I( "post.uid" ) ? $uid = I( "post.uid" ) : '';
		if( !$id || !$uid)
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		I( "post.answer" ) ? $answer = I( "post.answer" ) : '';
		if( !$answer)
		{
			$message = [ 'status' => 0, 'info' => '回复内容不能为空' ];
			return $this->ajaxReturn( $message );
		}
		$Dao = D('report');
		$res=$Dao->where("id=$id")->save(array('adminid'=>Admin::getUid(),'answer'=>$answer,'status'=>1,'rtime'=>date('Y-m-d H:i:s')));
		if($res){

			$megCallBack = publicRequist::set_message( self::$setMsgType, self::$reportMsgTitle, $answer, $uid );
			Log::statis( json_encode( array( 'adminid' => Admin::getUid() ,'title' => self::$reportMsgTitle,'answer'=>$answer, 'uid' =>$uid , 'res' => json_decode( $megCallBack ) ) ),'','report_msg_callback' );
			$megCallBack = json_decode( $megCallBack, true );
			if( $megCallBack['status'] == 1 )
			{
				//发推送消息
				$pushCallback = publicRequist::push_message( self::$setMsgType, self::$reportMsgTitle, $answer, $uid, self::$isSiteMsg );
				Log::statis( json_encode( array( 'adminid' => Admin::getUid(),'luid' => $uid, 'answer'=>$answer,'callback' => json_decode($pushCallback) ) ), '', 'report_push_callback' );
			}
			$message = [ 'status' => 1, 'info' => '操作成功' ];
			return $this->ajaxReturn( $message );
		}else{
			$message = [ 'status' => 0, 'info' => '操作失败' ];
			return $this->ajaxReturn( $message );
		}
	}

	function feedback_answer(){
		I( "post.id" ) ? $id = I( "post.id" ) : '';
		I( "post.uid" ) ? $uid = I( "post.uid" ) : '';
		if( !$id || !$uid)
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		I( "post.answer" ) ? $answer = I( "post.answer" ) : '';
		if( !$answer)
		{
			$message = [ 'status' => 0, 'info' => '回复内容不能为空' ];
			return $this->ajaxReturn( $message );
		}
		$Dao = D('feedback');
		$res=$Dao->where("id=$id")->save(array('adminid'=>Admin::getUid(),'answer'=>$answer,'status'=>1,'rtime'=>date('Y-m-d H:i:s')));
		if($res){

			$megCallBack = publicRequist::set_message( self::$setMsgType, self::$feedMsgTitle, $answer, $uid );
			Log::statis( json_encode( array( 'adminid' => Admin::getUid() ,'title' => self::$feedMsgTitle,'answer'=>$answer, 'uid' =>$uid , 'res' => json_decode( $megCallBack ) ) ),'','feedback_msg_callback' );
			$megCallBack = json_decode( $megCallBack, true );
			if( $megCallBack['status'] == 1 )
			{
				//发推送消息
				$pushCallback = publicRequist::push_message( self::$setMsgType, self::$feedMsgTitle, $answer, $uid, self::$isSiteMsg );
				Log::statis( json_encode( array( 'adminid' => Admin::getUid(),'luid' => $uid, 'answer'=>$answer,'callback' => json_decode($pushCallback) ) ), '', 'feedback_push_callback' );
			}
			$message = [ 'status' => 1, 'info' => '操作成功' ];
			return $this->ajaxReturn( $message );
		}else{
			$message = [ 'status' => 0, 'info' => '操作失败' ];
			return $this->ajaxReturn( $message );
		}
	}

}

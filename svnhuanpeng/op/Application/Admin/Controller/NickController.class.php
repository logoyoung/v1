<?php

namespace Admin\Controller;

use Common\Model\SalaryModel;
use HP\Op\Statis;
use HP\Op\Anchor;
use HP\Op\publicRequist;


class NickController extends BaseController
{

	protected $pageSize = 20;
	protected $man_pass = 1; //人工审核通过
	protected $man_unpass = 2; //人工审核未通过
	protected $free = 1; //设置免费改名标志
	protected $unfree = 0; //清空免费改名标志
	protected $pass = 1; //通过
	protected $unpass = 0; //不通过

	public function _access()
	{
		return [
			'index' => [ 'index' ],
			'pass' => [ 'index' ],
			'unpass' => [ 'index' ],
			'synchro'=>['index']
		];
	}


	public function index()
	{
//		I( "get.timestart" ) ? $stime = I( "get.timestart" ) : $stime = date( "Y-m-d", strtotime( get_date()) );
//		I( "get.timeend" ) ? $etime = I( "get.timeend" ) : $etime = date( "Y-m-d", strtotime( get_date() ) );

		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d');
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
		$nick = I( "get.name" ) ? I( "get.name" ) : '';
		$uid = I( "get.uid" ) ? I( "get.uid" ) : '';
		$status = I( "get.IMstatus" ) ? I( "get.IMstatus" ) : '3';
		$channel = I( "get.channel" ) ? I( "get.channel" ) : '';
		$timestart = I('get.timestart');
		$timeend = I('get.timeend');
		$where = 1;
		if( $nick )
		{
			$nick = filterData( $nick );
			$where .= " and nick like '%$nick%'";
		}
		if( $uid )
		{
			$where .= " and uid=$uid";
		}
		if($timestart)
		{
			$where .= " and ctime>='{$timestart} 00:00:00'";
		}
		if($timeend)
		{
			$where .= " and ctime<='{$timeend} 23:59:59'";
		}
		if( $status && !in_array( $status, array( 1, 2, 3, 4 ,5) ) )
		{
			$message = [ 'status' => 0, 'info' => '请求非法' ];
			return $this->ajaxReturn( $message );
		}
		if( $status == 1 )
		{
			$where .= " and status=1";
		}
		elseif( $status == 2 )
		{
			$where .= " and status=2";
		}
		elseif( $status == 3 )
		{
			$where .= " and status=3";
		}
		elseif( $status == 4 )
		{
			$where .= " and status=4";
		}
		else
		{
	  //
		}


//		if( $channel && in_array( $channel, array( 1, 2, 3 ) ) )
//		{
//			$where .= " and from=$channel";
//		}
		$dao = D( 'admin_user_nick' );
		$total = $dao->where( $where )->count();
		$Page = new \HP\Util\Page( $total, $this->pageSize );
		if( $total )
		{
			$res = $dao->where( $where )->order( 'ctime desc ' )->limit( $Page->firstRow . ',' . $Page->listRows )->select();
			$this->data = $res;
		}
		else
		{
			$this->data = array();
		}
		$this->IMstatus = array( '5'=>'选择类型','1' => '人工审核通过', '2' => '人工审核未通过', '3' => '机器审核通过', '4' => '机器审核未通过' );
//		$this->channel = array( '1' => '注册', '2' => '三方登录', '3' => '修改昵称' );
		$this->page = $Page->show();
		$this->status = $status;
		$this->display();
	}

	public function pass()
	{
		$ids = I( 'post.ids' );
		$ids = explode( ',', $ids );
		$data = $this->getUserNickByUids( $ids,$this->pass);
		$res = $this->updateUserStatic( $data, $this->unfree );
		if( $res )
		{
			$res = $this->updateAdminNick( $ids, $this->man_pass );
		}
		else
		{
			//TODO 记日志
			$res = false;
		}

		$message = [ 'status' => 0, 'info' => '操作失败' ];
		if( false !== $res )
		{
			$message = [ 'status' => 1, 'info' => '操作成功' ];
			publicRequist::askDota($ids,110);//事件推送
		}

		return $this->ajaxReturn( $message );
	}

	public function unpass()
	{
		$ids = I( 'post.ids' );
		$ids = explode( ',', $ids );
		$data = $this->getUserNickByUids( $ids,$this->unpass );
		$res = $this->updateUserStatic( $data, $this->free );
		if( $res )
		{
			$res = $this->updateAdminNick( $ids, $this->man_unpass );
		}
		else
		{
			//TODO 记日志
			$res = false;
		}
		$message = [ 'status' => 0, 'info' => '操作失败' ];
		if( false !==$res )
		{
			$message = [ 'status' => 1, 'info' => '操作成功' ];
		}
		return $this->ajaxReturn( $message );
	}

	/**
	 * 更新userstatic 表昵称
	 *
	 * @param array $data uid nick 键值对
	 *
	 * @return bool
	 */
	private function updateUserStatic( $data, $upfree )
	{
		if( empty( $data ) )
		{
			return false;
		}
		$Dao = D( 'userstatic' );
		foreach ( $data as $k => $v )
		{
			$res = $Dao->where( "uid=$k" )->save( array( 'nick' => $v, 'isfree' => $upfree ) );
			if( false === $res )
			{
				//TODO 写日志
			}
		}
		return true;
	}

	/**更新审核状态
	 *
	 * @param array $uids   用户uids
	 * @param       $status 状态
	 *
	 * @return bool
	 */
	private function updateAdminNick( $uids, $status )
	{
		if( empty( $uids ) || empty( $status ) )
		{
			return false;
		}
		$Dao = D( 'admin_user_nick' );
		$uids = implode( ',', $uids );
		$res = $Dao->where( "uid in ($uids)" )->save( array( 'status' => $status ,'utime'=>date('Y-m-d H:i:s')) );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private function getUserNickByUids( $uids ,$type=0)
	{
		if( empty( $uids ) || !is_array( $uids ) )
		{
			return false;
		}
		$list = array();
		$Dao = D( 'admin_user_nick' );
		$uids = implode( ',', $uids );
		$res = $Dao->field( 'uid,nick,oldnick' )->where( "uid in ($uids)" )->select();
		if( $res )
		{
			foreach ( $res as $v )
			{
				if($type){
					$v['nick'] = $v['nick'];
				}else{
					if( $v['nick'] == $v['oldnick'] )
					{
						$v['nick'] = '欢友' . $v['uid'];
					}
					else
					{
						$v['nick'] = $v['oldnick'];
					}
				}
				$list[$v['uid']] = $v['nick'];
			}
		}
		return $list;
	}

	public function  synchro(){
		$uid = I( "post.uid" ) ? I( "post.uid" ) : '';
		if(empty($uid) || !is_numeric($uid)){
			return $this->ajaxReturn( array('status' => 0, 'info' => '请输入正确的用户uid') );
		}
		$udao=D('userstatic');
		$info=$udao->where("uid=$uid")->select();
		if($info){
			$adao=D('admin_user_nick');
			$res=$adao->where("uid=$uid")->select();
			if(!$res){
				$data=array(
					'uid'=>$uid,
					'nick'=>$info[0]['nick'],
					'oldnick'=>$info[0]['nick'],
					'utime'=>date('Y-m-d H:i:s'),
					'status'=>3
				);
				$isok=$adao->add($data);
				if($isok){
					return $this->ajaxReturn( array('status' => 1, 'info' => '操作成功') );
				}else{
					return $this->ajaxReturn( array('status' => 0, 'info' => '操作失败') );
				}
			}
		}else{
			return $this->ajaxReturn( array('status' => 0, 'info' => '无此用户，请确认用户uid是否正确') );
		}
	}


}

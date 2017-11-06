<?php

namespace Admin\Controller;


use HP\Log\Log;
use HP\Log\Op;
use HP\Op\Anchor;
use HP\Op\Admin;
use HP\Op\publicRequist;

class SysmessageController extends BaseController
{

	protected $pageSize = 10;

	public function _access()
	{
		return [
			'sysmessage' => [ 'sysmessage' ],
			'sysmessagesave' => [ 'sysmessage' ],
		];
	}

	public function sysmessage()
	{
		$dao = D( 'Sysmessage' );
		$timestart = I( 'get.timestart' ) ? I( 'get.timestart' ) . ' 00:00:00' : date( 'Y-m-d H:i:s', strtotime( "-1 day" ) );
		$timeend = I( 'get.timeend' ) ? I( 'get.timeend' ) . ' 23:59:59' : date( 'Y-m-d H:i:s', time() );
		$id = I( 'get.id' ) ? I( 'get.id' ) : '';
		$type = I( 'get.type' ) ? I( 'get.type' ) : '1';
		$uid = I( 'get.uid' ) ? I( 'get.uid' ) : '0';
		if( $type == 1 )
		{
			$type = 0;
		}
		$where = "type=$type";
		if( $uid )
		{
			$udao = D( 'usermessage' );
			$msgid = $udao->where( "uid=$uid" )->select();
			if( $msgid )
			{
				$msgid = implode( ',', array_column( $msgid, 'msgid' ) );
				$where .= " and id in($msgid)";
			}
		}
		if( $id )
		{
			$where .= " and id=$id";
		}
		if( $timestart )
		{
			$where .= " and stime >='$timestart'";
		}
		if( $timeend )
		{
			$where .= " and  stime<='$timeend'";
		}
		if( strtotime( $timeend ) <= strtotime( $timestart ) )
		{
			$message = [ 'status' => 0, 'info' => '结束时间必须大于开始时间' ];
			return $this->ajaxReturn( $message );
		}
		if( $title = I( 'get.title' ) )
		{
			$where .= " and title like '%$title%'";
		}
		if( $msg = I( 'get.msg' ) )
		{
			$where .= " and msg like '%$msg%'";
		}
		if( IS_POST )
		{//删除操作
			$id = I( 'post.id' );
			if( !$id )
			{
				$message = [ 'status' => 0, 'msg' => 'id不能为空' ];
				$this->ajaxReturn( $message );
			}
			else
			{
				$dao->delete( $id );
				$this->ajaxReturn( [ 'msg' => '删除成功', 'status' => 1 ] );
			}
		}

		if( $export = I( 'get.export' ) )
		{//导出数据
			$results = $dao
				->where( $where )
				->select();
		}
		else
		{
			$count = $dao
				->where( $where )
				->count();
			$Page = new \HP\Util\Page( $count, $_GET['export'] ? 0 : $this->pageSize );
			$results = $dao
				->where( $where )
				->limit( $Page->firstRow . ',' . $Page->listRows )
				->order( 'id desc ' )
				->select();
		}
		$datas = $results;
		if( $export = I( 'get.export' ) )
		{//导出数据
			$excel[] = array( 'ID', '标题', '内容', '时间', '状态' );
			foreach ( $datas as $data )
			{
				$excel[] = array( $data['id'], $data['title'], $data['msg'] );
			}
			\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '站内信列表' );
		}
		$this->data = $datas;
		$this->page = $Page->show();
		$this->type = array( '1' => '个人消息', '2' => '全站消息' );
		$this->timestart = substr( $timestart, 0, 10 );
		$this->timeend = substr( $timeend, 0, 10 );
		$this->display();
	}

	public function sysmessagesave()
	{
		$dao = D( 'sysmessage' );
		$id = is_numeric( I( 'get.id' ) ) ? I( 'get.id' ) : null;
		if( IS_POST )
		{
			$type = I( 'post.type' ) ? I( 'post.type' ) : 0;
			$pushtype = I( 'post.ptype' ) ? I( 'post.ptype' ) : 0;
			$title = I( 'post.title' ) ? I( 'post.title' ) : '';
			$msg = I( 'post.msg' ) ? I( 'post.msg' ) : '';
			$uid = I( 'post.uids' ) ? I( 'post.uids' ) : 0;
			if( empty( $title ) || empty( $msg ) )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'msg' => '标题、内容不能为空' ) );
			}
			if( !$type && empty( $uid ) )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'msg' => '类型未选择' ) );
			}
			if( $type && !empty( $uid ) )
			{
				return $this->ajaxReturn( array( 'status' => 0, 'msg' => '发送给全部用户不需要填写用户uid' ) );
			}
//			$uid=implode(',',array_filter(explode(',',$uid)));
			$uid = implode( ',', array_filter( explode( ',', str_replace( '，', ',', $uid ) ) ) );
			$res = publicRequist::set_message( $type, $title, $msg, $uid );
			$adminid = Admin::getUid();
			Log::statis( json_encode( array( 'adminid' => $adminid, 'type' => $type, 'title' => $title, 'msg' => $msg, 'uid' => $uid, 'res' => json_decode( $res ) ) ), '', 'sysmsg_msg_callback' );
			$res = json_decode( $res, true );
			if( $res['status'] == 1 )
			{
				//发推送消息
				if( $pushtype == 1 )
				{
					if(empty($uid)){
						$uid='-1';
					}
					$pushCallback = publicRequist::push_message( $type, $title, $msg, $uid, 1 );
					Log::statis( json_encode( array( 'adminid' => $adminid,'type'=>$type, 'luid' => $uid, 'title'=>$title,'content' => $msg, 'callback' => json_decode( $pushCallback ) ) ), '', 'sysmsg_push_callback' );
				}
				$message = array( 'status' => 1, 'info' => '操作成功' );
			}
			else
			{
				$message = array( 'status' => 0, 'info' => '操作失败' );
			}
			return $this->ajaxReturn( $message );
		}
		$assign = $id ? $dao->find( $id ) : [];
		$this->assign( $assign );
		$this->display();
	}


}

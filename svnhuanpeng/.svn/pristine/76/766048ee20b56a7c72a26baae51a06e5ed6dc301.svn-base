<?php

namespace Admin\Controller;


use HP\Op\Admin;
use HP\Op\publicRequist;

class RechargeController extends BaseController
{

	protected $pageSize = 20;

	public function _access()
	{
		return [
			'recharge' => [ 'recharge' ],
			'rechargesave' => [ 'recharge' ],
		];
	}

	public function recharge()
	{
		$dao = D( 'InternalDistributionRecord' );
		$type = $dao->getType();
		$active = $dao->getActive();
		if( $uid = I( 'get.uid' ) )
		{
			$where['uid'] = $uid;
		}
		if( I( 'get.type' ) != 0 )
		{
			$where['type'] = I( 'get.type' );
		}
		if( I( 'get.activeid' ) != 0 )
		{
			$where['activeid'] = I( 'get.activeid' );
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
				->order( 'id desc ' )
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
		foreach ( $results as $result )
		{
			$data = $result;
			$data['type'] = $type[$result['type']];
			$data['active'] = $active[$result['activeid']];
			$datas[] = $data;
		}
		if( $export = I( 'get.export' ) )
		{//导出数据
			$excel[] = array( '用户ID', '类型', '活动', '欢朋币', '欢朋豆', '发放时间', '描述' );
			foreach ( $datas as $data )
			{
				$excel[] = array( $data['uid'], $data['type'], $data['active'], $data['hpcoin'], $data['hpbean'], $data['ctime'], $data['desc'] );
			}
			\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '发放列表' );
		}
		$this->type = $type;
		$this->active = $active;
		$this->data = $datas;
		$this->page = $Page->show();
		$this->display();
	}

	public function rechargesave()
	{
		$dao = D( 'InternalDistributionRecord' );
		$type = $dao->getType();
		$active = $dao->getActive();
		if( IS_POST )
		{
			$message = [ 'status' => 0, 'info' => '操作失败' ];

			if(($cdata = $_POST) != false )
			{
				$tmp = explode("\r\n", I('post.uids', ''));
				$uids = [];
				if($tmp) {
					foreach($tmp as $k=>$v) {
						$v = trim($v);
						if(is_numeric($v)) {
							$uids[] = $v;
						}
					}
				}
				if(empty($uids)) {
					return $this->ajaxReturn( array( 'status' => 0, 'info' => '账户非法' ) );
				}
				if(count($uids) > 100) {
					return $this->ajaxReturn( array( 'status' => 0, 'info' => 'UID不能超过100个' ) );
				}
				if(count($uids) > count(array_unique($uids))) {
					return $this->ajaxReturn( array( 'status' => 0, 'info' => '有重复的ID，请检查后重新提交' ) );
				}
				$UserDao = D( 'userstatic' );
				$noExistStr = '';
				$users = $UserDao->field('uid')->where(['uid'=>['in', $uids]])->select();
				if(count($uids) != count($users)) {
					return $this->ajaxReturn( array( 'status' => 0, 'info' => '用户' . implode(',', array_diff($uids, array_column($users,'uid'))) . '不存在，请检查后重新提交!'));
				}
				if( empty( $cdata['type'] ) ) {
					return $this->ajaxReturn( array( 'status' => 0, 'info' => '请选择类型' ) );
				}
				if( empty( $cdata['activeid'] ) ) {
					return $this->ajaxReturn( array( 'status' => 0, 'info' => '请选择所属活动' ) );
				}
				if( empty( $cdata['hpcoin'] ) && empty( $cdata['hpbean'] ) ) {
					return $this->ajaxReturn( array( 'status' => 0, 'info' => '欢币欢豆，不允许同时为空' ) );
				}
				if( empty( trim( $cdata['desc'] ) ) ) {
					return $this->ajaxReturn( array( 'status' => 0, 'info' => '描述不能为空' ) );
				}
				
				foreach($uids as $v)
				{
					$dao->create();
					$dao->adminid = Admin::getUid();
					$dao->ctime = get_date();
					$dao->uid = $v;
					$res = $dao->add();
					if( $res )
					{
						$data = array(
							'uid' => $v,
							'hpcoin' => $cdata['hpcoin'] ? $cdata['hpcoin'] : "0",
							'hpbean' => $cdata['hpbean'] ? $cdata['hpbean'] : "0",
							'coin' => "0",
							'bean' => "0",
							'desc' => $cdata['desc'],
							'activeid' => $cdata['activeid'],
							'recordid' => $res
						);
						$back = publicRequist::outside_recharge( $data );//通知财务系统
						if( $back ) {
							if( $back == -1 ) {
								unsuccessLogForFinanceBack( '财务系统返回成功,但更新账户余额失败', array( 'financeBack' => $back, 'data' => $data ) );
							} else {
								$this->updateFtidToInternal( $data['recordid'], $back );//是否通知到财务系统
							}
						} else {
							unsuccessLogForFinanceBack( '财务系统返回失败', array( 'financeBack' => $back, 'data' => $data ) );
						}

						$message['status'] = 1;
						$message['info'] = '操作成功';
					}
				}
			}
			return $this->ajaxReturn( $message );
		}
		$this->active = $active;
		$this->type = $type;
		$this->display();
	}

	public function updateFtidToInternal( $recordid, $ftid )
	{
		if( empty( $recordid ) || empty( $ftid ) )
		{
			return false;
		}
		$dao = D( 'InternalDistributionRecord' );
		$res = $dao->where( "id=$recordid" )->save( array( 'ftid' => $ftid ) );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}


}

<?php

namespace Admin\Controller;

use HP\Op\Statis;
use HP\Log\Log;
use HP\File\Dao;
use Think\Model;
use Org\Util\Date;
use Common\Model\HPFMonthModel;
use HP\Op\Live;
use HP\Op\Anchor;
use HP\Op\Company;
use HP\Op\Admin;


class OplogController extends BaseController
{

	protected $pageSize = 10;

	public function _access()
	{
		return [
			'live' => [ 'live' ],
			'getadmins' => [ 'live' ],
		];
	}


	public function live()
	{
		$typesstr = Live::$typesstr;
		$dao = D( 'Livereviewreason' );
		$resons = $dao->getField( "id,reason" );
		$dao = D( 'AclUser' );
		$admins = $dao->getField( "uid,realname" );
		$dao = D( "AnchorBlackRecord" );
		$companys = D('company')->field('id,name')->getField('id,name');
		if( $uid = I( 'get.uid' ) )
		{
			$where['a.uid'] = $uid;
		}
		if( $luid = I( 'get.luid' ) )
		{
			$where['a.luid'] = $luid;
		}
		if( $t = I( 'get.t' ) )
		{
			$where['a.type'] = $t;
		}
		$_GET['timestart'] = $_GET['timestart']?$_GET['timestart']:date('Y-m-d');
		$_GET['timeend'] = $_GET['timeend']?$_GET['timeend']:date('Y-m-d');
		if( $stime = I( "get.timestart" ) )
		{
			$stime .= " 00:00:00";
			$where['a.ctime'][] = [ 'egt', $stime ];
		}
		if( $etime = I( "get.timeend" ) )
		{
			$etime .= " 23:59:59";
			$where['a.ctime'][] = [ 'elt', $etime ];
		}
		if($username = I('get.username')){
			$where['b.nick'] = ['like',"%$username%"];
		}
		if($company = I('get.company')){
			list($companyname,$cid) = explode('|',$company);
			$where['c.cid'] = $cid;
		}

		if( $export = I( 'get.export' ) )
		{//导出数据
			$results = $dao
				->alias( ' a ' )
				->join( " left join " . D('userstatic')->getTableName() . " as b on a.luid = b.uid " )
				->join( " left join " . D('anchor')->getTableName() . " as c on a.luid = c.uid " )
				->field('a.*,b.nick,c.cid')
				->where( $where )
				->limit( '0,1000' )
				->order( 'id desc ' )
				->select();
		}
		else
		{
			$count = $dao
				->alias( ' a ' )
				->join( " left join " . D('userstatic')->getTableName() . " as b on a.luid = b.uid " )
				->join( " left join " . D('anchor')->getTableName() . " as c on a.luid = c.uid " )
				->field('a.*,b.nick,c.cid')
				->where( $where )->count();
			$Page = new \HP\Util\Page( $count, $_GET['export'] ? 0 : $this->pageSize );
			$results = $dao
				->alias( ' a ' )
				->join( " left join " . D('userstatic')->getTableName() . " as b on a.luid = b.uid " )
				->join( " left join " . D('anchor')->getTableName() . " as c on a.luid = c.uid " )
				->field('a.*,b.nick,c.cid')
				->where( $where )
				->limit( $Page->firstRow . ',' . $Page->listRows )
				->order( 'id desc ' )
				->select();
		}
		foreach ( $results as &$result )
		{
			$result['realname'] = $admins[$result['uid']];
			$result['reason'] = $resons[$result['reason']];
			$result['remark'] = $result['remark'];
			$result['type'] = $typesstr[$result['type']];
			$result['pic'] = explode(',',$result['pic']);
		}
		if( $export = I( 'get.export' ) )
		{//导出数据
			$excel[] = array( '管理员ID', '管理员姓名', '主播ID', '直播ID','昵称','公司', '操作类型', '操作原因','描述','截图', '操作时间' );
			foreach ( $results as $data )
			{
				$excel[] = array( $data['uid'], $data['realname'], $data['luid'], $data['liveid'],$data['nick'],$companys[$data['cid']], $data['type'], $data['reason'],"\"{$data['content']}\"", $data['pic'], $data['ctime'] );
			}
			\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '直播审核列表' );
		}
		$this->conf = $GLOBALS['env-def'][$GLOBALS['env']];
		$this->companys = $companys;
		$this->data = $results;
		$this->type = $typesstr;
		$this->page = $Page->show();
		$this->display();
	}

	public function video()
	{
		$typesstr = [ '1' => '通过', '2' => '拒绝' ];
		$dao = D( 'Livereviewreason' );
		$resons = $dao->getField( "id,reason" );
		$dao = D( 'AclUser' );
		$admins = $dao->getField( "uid,realname" );

		$dao = D( "UnpassVideo" );
		$dao_video = D( "video" );

		if( $adminid = I( 'get.adminid' ) )
		{
			$where['a.adminid'] = $adminid;
		}
		if( $uid = I( 'get.uid' ) )
		{
			$where['b.uid'] = $uid;
		}
		if( $t = I( 'get.t' ) )
		{
			if( $t == 1 )
			{
				$where['a.type'] = 0;
			}
			else
			{
				$where['a.type'] = [ 'gt', 0 ];
			}
		}

		if( $stime = I( "get.timestart" ) )
		{
			$stime .= " 00:00:00";
			$where['a.ctime'][] = [ 'egt', $stime ];
		}
		if( $etime = I( "get.timeend" ) )
		{
			$etime .= " 23:59:59";
			$where['a.ctime'][] = [ 'elt', $etime ];
		}
		if( $uid = I( "get.uid" ) )
		{
			$where['b.uid'] = $uid;
		}

		if( $export = I( 'get.export' ) )
		{//导出数据
			$results = $dao
				->alias( ' a ' )
				->join( " left join " . $dao_video->getTableName() . " as b on a.videoid = b.videoid " )
				->where( $where )
				->limit( '0,1000' )
				->order( 'id desc ' )
				->field( 'a.*, b.uid' )
				->select();
		}
		else
		{
			$count = $dao
				->alias( ' a ' )
				->join( " left join " . $dao_video->getTableName() . " as b on a.videoid = b.videoid " )
				->where( $where )->count();
			$Page = new \HP\Util\Page( $count, $_GET['export'] ? 0 : $this->pageSize );
			$results = $dao
				->alias( ' a ' )
				->join( " left join " . $dao_video->getTableName() . " as b on a.videoid = b.videoid " )
				->where( $where )
				->limit( $Page->firstRow . ',' . $Page->listRows )
				->order( 'id desc ' )
				->field( 'a.*, b.uid' )
				->select();
		}
		foreach ( $results as &$result )
		{
			$result['realname'] = $admins[$result['adminid']];
			$result['reason'] = $result['describe'];
			$result['checktype'] = '拒绝';
			$result['type'] == 0 && $result['checktype'] = '通过';
			$result['type'] = $resons[$result['type']];
		}

		if( $export = I( 'get.export' ) )
		{//导出数据
			$excel[] = array( '管理员ID', '管理员姓名', '主播ID', '录像ID', '审核类型', '拒绝类型', '拒绝原因', '操作时间' );
			foreach ( $results as $data )
			{
				$excel[] = array( $data['adminid'], $data['realname'], $data['uid'], $data['videoid'], $data['checktype'], $data['type'], $data['reason'], $data['ctime'] );
			}
			\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '录像审核列表' );
		}
		$this->data = $results;
		$this->type = $typesstr;
		$this->page = $Page->show();
		$this->display();
	}

	/*
	 * 返回管理员列表
	 * zwq add 2017年6月20日
	 */

	public function getadmins()
	{
		$dao = D( 'AclUser' );
		$results = $dao->select();
		foreach ( $results as $result )
		{
			$data['value'] = $result['realname'] . '|' . $result['name'] . '|' . $result['uid'];
			$data['id'] = $result['uid'];
			$datas[] = $data;
		}
		return $this->ajaxReturn( $datas );
	}

	/**
	 * 对帐号的操作记录,比如封号，解除封号，禁言，解除禁言等
	 */
	function blocked()
	{
		if( $uid = I( 'get.uid' ) )
		{
			$where['uid'] = $uid;
		}
		if( $adminid = I( 'get.adminid' ) )
		{
			$where['adminid'] = $adminid;
		}
		if( $type = I( 'get.type' ) )
		{
			$where['type'] = $type;
		}
		if( $stime = I( "get.timestart" ) )
		{
			$stime .= " 00:00:00";
			$where['ctime'][] = [ 'egt', $stime ];
		}
		if( $etime = I( "get.timeend" ) )
		{
			$etime .= " 23:59:59";
			$where['ctime'][] = [ 'elt', $etime ];
		}

		$dao = D( 'userblockedlist' );
		if( $export = I( 'get.export' ) )
		{//导出数据
			$results = $dao
				->where( $where )
				->limit( '0,1000' )
				->order( 'id desc ' )
				->select();
		}
		else
		{
			$count = $dao->where( $where )->count();
			$Page = new \HP\Util\Page( $count, $_GET['export'] ? 0 : $this->pageSize );
			$results = $dao
				->where( $where )
				->limit( $Page->firstRow . ',' . $Page->listRows )
				->order( 'id desc ' )
				->select();
		}
		$typesstr = $dao->getType();
		if( $results )
		{
			$resons = D( 'Livereviewreason' )->getField( "id,reason" );
			$admins = D( 'AclUser' )->getField( "uid,realname" );

			foreach ( $results as &$result )
			{
				$result['realname'] = $admins[$result['adminid']];
				$result['reason'] = $resons[$result['reason']];
				$result['type'] = $typesstr[$result['type']];
				$result['stime'] = $result['stime'] == '0000-00-00 00:00:00' ? '--' : $result['stime'];
				$result['etime'] = $result['etime'] == '0000-00-00 00:00:00' ? '--' : $result['etime'];
			}
			if( $export = I( 'get.export' ) )
			{//导出数据
				$excel[] = array( '管理员ID', '管理员姓名', '主播ID', '直播ID', '操作类型', '操作原因', '操作时间', '开始时间', '结束时间' );
				foreach ( $results as $data )
				{
					$excel[] = array( $data['adminid'], $data['realname'], $data['uid'], $data['liveid'], $data['type'], $data['reason'], $data['ctime'], $data['stime'], $data['etime'] );
				}
				\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '直播审核列表' );
			}
		}
		$this->data = $results;
		$this->type = $typesstr;
		$this->page = $Page->show();
		$this->display();
	}


	function anchorchange()
	{
		if( $uid = I( 'get.uid' ) )
		{
			$where['uid'] = $uid;
		}
		$stime = I( "get.timestart" ) ? I( "get.timestart" ) : date( 'Y-m-d', strtotime( '-1 months' ) );
		$etime = I( "get.timeend" ) ? I( "get.timeend" ) : date( 'Y-m-d', time() );
		$type = I( "get.type" ) ? I( "get.type" ) : '';
		$where['status']=0;
		if( $stime )
		{
			$stime .= " 00:00:00";
			$where['ctime'][] = [ 'egt', $stime ];
		}
		if( $etime )
		{
			$etime .= " 23:59:59";
			$where['ctime'][] = [ 'elt', $etime ];
		}
		if( $type == 1 )
		{
			$where['_string'] = ' before_cid+after_cid >0';

		}
		if( $type == 2 )
		{
			$where['_string'] = ' before_cid=0 and after_cid=0';
		}
		$dao = D( 'anchor_change_record' );
		$count = $dao->where( $where )->count();
		$Page = new \HP\Util\Page( $count, $_GET['export'] ? 0 : $this->pageSize );
		$results = $dao
			->where( $where )
			->limit( $Page->firstRow . ',' . $Page->listRows )
			->order( 'ctime desc ' )
			->select();

		if( $results )
		{
			$userinfo = Anchor::anchorInfo( array_column( $results, 'uid' ) );
			$company = Company::getCompanymap();
			$admins = D( 'admin_acl_user' )->getField( "uid,realname" );
			foreach ( $results as $k => $v )
			{
				$results[$k]['nick'] = $userinfo[$v['uid']]['nick'];
				$results[$k]['before_name'] = isset( $company[$v['before_cid']] ) ? $company[$v['before_cid']]['name'] : '非签约状态';
				$results[$k]['after_name'] = isset( $company[$v['after_cid']] ) ? $company[$v['after_cid']]['name'] : '普通主播';
				if( $v['adminid'] == 100 )
				{
					$results[$k]['admin_name'] = '--';
				}
				else
				{
					$results[$k]['admin_name'] = array_key_exists( $v['adminid'], $admins ) ? $admins[$v['adminid']] : '--';
				}
			}
		}
		$this->data = $results;
		$this->timestart = substr( $stime, 0, 10 );
		$this->timeend = substr( $etime, 0, 10 );
		$this->type = array( '1' => '签约变动', '2' => '实名认证' );
		$this->page = $Page->show();
		$this->display();
	}
    public function ywrefundcheck(){
		$dao = D('logDueAppeal');
		$this->ywlog($dao);
	}
	public function ywcommentcheck(){
		$dao = D('logDueComment');
		$this->ywlog($dao);
	}
	public function ywqualifications(){
		$dao = D('logDueCert');
		$this->ywlog($dao);
	}
	public function ywlog($dao){
		//$dao = D('logDueAppeal');
		if($adminid = I('get.aid')){
			$where['adminid'] = $adminid;
		}
		if($uid = I('get.uid')){
			$where['uid'] = $uid;
		}
		if($aname = I('get.aname')){
			$where['aname'] = ['like',"%$aname%"];
		}
		if($uname = I('get.uname')){
			$where['uname'] = ['like',"%$uname%"];
		}

		if($stime = I("get.timestart")) {
			$stime .= " 00:00:00";
			$where['ctime'][] = ['egt',$stime];
		}
		if($etime = I("get.timeend")) {
			$etime .= " 23:59:59";
			$where['ctime'][] = ['elt',$etime];
		}
		$_GET['status'] = isset($_GET['status'])?$_GET['status']:'-1';
		$status = I('get.status');
		if($status!='-1')
			$where['status'] = $status;

		if($export = I('get.export')){//导出数据
			$results = $dao
				->where($where)
				->order('id desc ')
				->select();
		}else{
			$count = $dao
				->where($where)->count();
			$Page = new \HP\Util\Page($count,$_GET['export']?0:$this->pageSize);
			$results = $dao
				->where($where)
				->limit($Page->firstRow.','.$Page->listRows)
				->order('id desc ')
				->select();
		}
		foreach ($results as &$result){
			;
		}
		$status = ['0'=>'拒绝','1'=>'通过'];
		if($export = I('get.export')){//导出数据
			$excel[] = array('关联ID','管理员ID','管理员姓名','用户ID','用户昵称','审核操作','操作结果','原因','操作时间');
			foreach ($results as $data) {
				$excel[] = array($data['rid'],$data['adminid'],$data['aname'],$data['uid'],$data['uname'],$data['opt'],$status[$data['status']],$data['reason'],$data['ctime']);
			}
			\HP\Util\Export::outputCsv($excel,date('Y-m-d').'录像审核列表');
		}
		$this->data = $results;
		$this->status = $status;
		$this->page = $Page->show();
		$this->display('ywrefundcheck');
	}
}

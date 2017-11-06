<?php

namespace Admin\Controller;

use HP\Op\Statis;
use HP\Op\Anchor;
use HP\Op\Company;


class CostController extends BaseController
{

	protected $pageSize = 20;

	public function _access()
	{
		return [
			'usercost' => [ 'usercost' ],
			'usercostdetail' => [ 'usercost' ]
		];
	}


	public function getCidByUid( $uidsArray )
	{
		$Dao = D( 'anchor' );
		$uidslist = implode( ',', $uidsArray );
		return $Dao->field( 'uid,cid' )->where( "uid in ($uidslist)" )->getField( 'uid,cid' );
	}

	public function usercost()
	{
		$stime = I( "get.timestart" ) ? I( "get.timestart" ) : date( "Y-m-d", strtotime( get_date() ) );
		$etime = I( "get.timeend" ) ? I( "get.timeend" ) : date( "Y-m-d", strtotime( get_date() ) );
		$luid = I( "get.luid" ) ? I( "get.luid" ) : '';
		$pst = substr( $stime, 5, 2 );
		$pet = substr( $etime, 5, 2 );
		if( (int)$pst < (int)$pet )
		{
			$etime = substr( $stime, 0, 7 ) . '-31';
		}
		if( $luid )
		{
			$where = " and luid=$luid ";
		}
		else
		{
			$where = " ";
		}
		$timestart = $stime;
		$timeend = $etime;
		$stime = $stime . ' 00:00:00';
		$etime = $etime . ' 23:59:59';
		$suffix = str_replace( '-', '', substr( $stime, 0, 7 ) );
		$Dao = D( 'giftrecordcoin_' . $suffix );
		if( $export = I( 'get.export' ) )
		{//导出数据
			$res = $Dao->field( 'uid,sum(cost) as salary' )
				->where( "ctime >='$stime' and ctime<='$etime'  and  otid !=0 $where" )
				->group( 'uid' )
				->order( 'salary desc ' )
				->select();
		}
		else
		{
			$total = $Dao->field( 'uid,sum(cost) as salary' )
				->where( "ctime >='$stime' and ctime<='$etime'  and  otid !=0 $where" )
				->group( 'uid' )
				->order( 'salary desc ' )
				->select();
			$Page = new \HP\Util\Page( count( $total ), $this->pageSize );
			$res = $Dao->field( 'uid,sum(cost) as salary' )
				->where( "ctime >='$stime' and ctime<='$etime'  and  otid !=0 $where" )
				->group( 'uid' )
				->order( 'salary desc ' )
				->limit( $Page->firstRow . ',' . $Page->listRows )
				->select();
		}

		if( $res )
		{
			$uids = array_column( $res, 'uid' );
			$userstatics = \HP\Op\Anchor::anchorInfo( $uids );
			if( $res )
			{
				$list = array();
				foreach ( $res as $v )
				{
					$temp['uid'] = $v['uid'];
					$temp['name'] = isset( $userstatics[$v['uid']] ) ? $userstatics[$v['uid']]['nick'] : '';
					$temp['coin'] = $v['salary'];
					array_push( $list, $temp );
				}
				$this->data = $list;
				if( $export = I( 'get.export' ) )
				{//导出数据
					$excel[] = array( '用户uid', '用户昵称', '消费欢朋币总数' );
					foreach ( $list as $data )
					{
						$excel[] = array( "\t" . $data['uid'], $data['name'], $data['coin'] );
					}
					\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '用户消费排行列表' );
				}
			}
			else
			{
				$this->data = array();
			}

		}
		else
		{
			$this->data = array();
		}
		$this->timestart = $timestart;
		$this->timeend = $timeend;
		$this->page = $Page->show();
		$this->display();
	}

	public function usercostdetail()
	{
		I( "get.timestart" ) ? $stime = I( "get.timestart" ) : $stime = date( "Y-m-d", strtotime( get_date() ) );
		I( "get.timeend" ) ? $etime = I( "get.timeend" ) : $etime = date( "Y-m-d", strtotime( get_date() ) );

		$where['otid'] = ['neq', 0];
		if($uid = I( "get.uid")) {
			$where['uid'] = $uid;
		}
		if($giftid = I( "get.giftid")) {
			$where['giftid'] = $giftid;
		}
		$where['ctime'] = [['egt', $stime . ' 00:00:00'],['elt',$etime . ' 23:59:59']];

		$suffix = str_replace( '-', '', substr( $stime, 0, 7 ) );
		$Dao = D( 'giftrecordcoin_' . $suffix );
		if(I( 'get.export')) {//导出数据
			$res = $Dao->field( 'liveid,luid,uid,cost,ctime,giftid' )
				->where($where)
				->order('ctime desc')
				->select();
		} else {
			$total = $Dao->field('*')->where($where)->count();
			$Page = new \HP\Util\Page($total, $this->pageSize );
			$res = $Dao->field( 'liveid,luid,uid,cost,ctime,giftid' )
				->where($where)
				->order('ctime desc')
				->limit( $Page->firstRow . ',' . $Page->listRows )
				->select();

		}
		$gift = D('Gift')->where(['id'=>['gt','31']])->order('money')->getField('id,giftname');
		if( $res ) {
			$list = array();
			$userstatics = \HP\Op\Anchor::anchorInfo(array_merge(array_column( $res, 'uid'), array_column( $res, 'luid')) );
			foreach ( $res as $v ) {
				$v['liveid'] = $v['liveid'] ? $v['liveid'] : 0;
				$v['name'] = isset( $userstatics[$v['uid']] ) ? $userstatics[$v['uid']]['nick'] : '';
				$v['lname'] = isset( $userstatics[$v['luid']] ) ? $userstatics[$v['luid']]['nick'] : '';
				$v['giftname'] = isset($gift[$v['giftid']]) ? $gift[$v['giftid']] : '';
				array_push($list, $v);
			}
			
			$this->data = $list;
			if(I('get.export')) {//导出数据
				$excel[] = array( '用户uid', '用户昵称', '直播id', '主播uid', '主播昵称', '礼物','消费欢朋币数额','送礼时间');
				foreach ( $list as $data )
				{
					$excel[] = array( "\t" . $data['uid'], $data['name'], $data['liveid'], $data['luid'], $data['lname'],$data['giftname'],$data['cost'],"\t" .$data['ctime']);
				}
				\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '用户--'.$userstatics[$uid]['nick'].'--送礼消费明细列表' );
			}
		} else {
			$this->data = array();
		}
		$this->gift = $gift;
		$this->page = $Page->show();
		$this->backUid = $uid;
		$this->userNick = $userstatics[$uid]['nick'];
		$this->display();
	}


}

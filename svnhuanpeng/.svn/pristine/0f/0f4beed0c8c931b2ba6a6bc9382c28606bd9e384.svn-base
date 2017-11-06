<?php

namespace Admin\Controller;

use Common\Model\SalaryModel;
use HP\Op\Statis;
use HP\Op\Anchor;
use HP\Op\Company;


class SalaryController extends BaseController
{

	protected $pageSize = 20;

	public function _access()
	{
		return [
			'anchorSalary' => [ 'anchorSalary' ],
			'salarydetail' => [ 'anchorSalary' ]
		];
	}


	public function getCidByUid( $uidsArray )
	{
		$Dao = D( 'anchor' );
		$uidslist = implode( ',', $uidsArray );
		return $Dao->field( 'uid,cid' )->where( "uid in ($uidslist)" )->getField( 'uid,cid' );
	}

	public function anchorSalary()
	{
		$stime =I( "get.timestart" ) ? I( "get.timestart" ) :  date( "Y-m-d", strtotime( get_date()) );
		$etime = I( "get.timeend" ) ?  I( "get.timeend" ) :  date( "Y-m-d", strtotime( get_date() ) );
		$luid = I( "get.luid" ) ?  I( "get.luid" ) : '';
		if($luid){
			$where= " and luid=$luid ";
		}else{
			$where= " ";
		}
		$pst=substr($stime,5,2);
		$pet=substr($etime,5,2);
		if((int)$pst<(int)$pet){
			$etime=substr($stime,0,7).'-31';
		}
		$timestart=$stime;
		$timeend=$etime;
		$stime = $stime . ' 00:00:00';
		$etime = $etime . ' 23:59:59';
		$suffix = str_replace( '-', '', substr( $stime, 0, 7 ) );
		$Dao = D( 'giftrecordcoin_' . $suffix );
		if( $export = I( 'get.export' ) )
		{//导出数据
			$res = $Dao->field( 'luid,sum(income) as salary' )
				->where( "ctime >='$stime' and ctime<='$etime'  and  otid !=0  $where" )
				->group( 'luid' )
				->order( 'ctime desc ' )
				->select();
		}else{
			$total = $Dao->field( 'luid,sum(income) as salary' )
				->where( "ctime >='$stime' and ctime<='$etime'  and  otid !=0  $where" )
				->group( 'luid' )
				->order( 'salary desc ' )
				->select();
			$Page = new \HP\Util\Page( count( $total ), $this->pageSize );
			$res = $Dao->field( 'luid,sum(income) as salary' )
				->where( "ctime >='$stime' and ctime<='$etime'  and  otid !=0 $where" )
				->group( 'luid' )
				->order( 'salary desc ' )
				->limit( $Page->firstRow . ',' . $Page->listRows )
				->select();
		}

		if($res)
		{
			$uids = array_column( $res, 'luid' );
			$userstatics = \HP\Op\Anchor::anchorInfo( $uids );
			$companymap = \HP\Op\Company::getCompanymap();
			$cids = $this->getCidByUid( $uids );
			if( $res )
			{
				$list = array();
				foreach ( $res as $v )
				{
					$temp['uid'] = $v['luid'];
					$temp['name'] = isset( $userstatics[$v['luid']] ) ? $userstatics[$v['luid']]['nick'] : '';
					if( $cids[$v['luid']] )
					{
						$temp['company'] = $companymap[$cids[$v['luid']]]['name'];
					}
					else
					{
						$temp['company'] = '个人未签约主播';
					}
					$temp['coin'] = $v['salary'];
					array_push( $list, $temp );
				}
				$this->data = $list;

				if( $export = I( 'get.export' ) )
				{//导出数据
					$excel[] = array( '主播uid', '主播昵称', '所属经纪公司', '金币总收益');
					foreach ( $list as $data )
					{
						$excel[] = array( "\t" . $data['uid'], $data['name'], $data['company'], $data['coin']);
					}
					\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '主播收益排行列表' );
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
		$this->timeend =$timeend;
		$this->page = $Page->show();
		$this->display();
	}

	public function salaryDetail()
	{
		I( "get.timestart" ) ? $stime = I( "get.timestart" ) : $stime = date( "Y-m-d", strtotime( get_date() ) );
		I( "get.timeend" ) ? $etime = I( "get.timeend" ) : $etime = date( "Y-m-d", strtotime( get_date() ) );
		I( "get.uid" ) ? $luid = I( "get.uid" ) : '';
		if( $luid )
		{
			if($luid){
				$where= " and luid=$luid ";
			}else{
				$where= " ";
			}
			$stime = $stime . ' 00:00:00';
			$etime = $etime . ' 23:59:59';
			$suffix = str_replace( '-', '', substr( $stime, 0, 7 ) );
			$Dao = D( 'giftrecordcoin_' . $suffix );
			if( $export = I( 'get.export' ) )
			{//导出数据
				$res = $Dao->field( 'liveid,luid,uid,income,ctime' )
					->where( "ctime >='$stime' and ctime<='$etime'  and  otid !=0 $where" )
					->order( 'ctime desc ' )
					->select();
			}else{
				$total = $Dao->field( 'luid,uid,liveid,income,ctime' )
					->where( "ctime >='$stime' and ctime<='$etime'  and  otid !=0 $where" )
					->order( 'ctime desc ' )
					->select();
				$Page = new \HP\Util\Page( count( $total ), $this->pageSize );
				$res = $Dao->field( 'liveid,luid,uid,income,ctime' )
					->where( "ctime >='$stime' and ctime<='$etime'  and  otid !=0 $where" )
					->order( 'ctime desc ' )
					->limit( $Page->firstRow . ',' . $Page->listRows )
					->select();
			}
				if( $res )
				{
					$list = array();
					$uids = array_column( $res, 'uid' );
					$uids = array_push( $uids, $luid );
					$userstatics = \HP\Op\Anchor::anchorInfo( $uids );
					foreach ( $res as $v )
					{
						$temp['liveid'] = $v['liveid'] ? $v['liveid'] : 0;
						$temp['luid'] = $v['luid'];
						$temp['uid'] = $v['uid'];
						$temp['name'] = isset( $userstatics[$v['uid']] ) ? $userstatics[$v['uid']]['nick'] : '';
						$temp['salary'] = $v['income'];
						$temp['ctime'] = $v['ctime'];
						array_push( $list, $temp );
					}
					$this->data = $list;
					if( $export = I( 'get.export' ) )
					{//导出数据
						$excel[] = array( '主播uid', '直播id', '用户id', '用户昵称','收益','送礼时间');
						foreach ( $list as $data )
						{
							$excel[] = array( "\t" . $data['luid'], $data['liveid'], $data['uid'], $data['name'],$data['salary'],"\t" .$data['ctime']);
						}
						\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '主播--'.$userstatics[$luid]['nick'].'--礼物收益明细列表' );
					}
				}
				else
				{
					$this->data = array();
				}
				$this->page = $Page->show();
				$this->backUid = $luid;
				$this->AnchorNick = $userstatics[$luid]['nick'];
			$this->display();
		}
	}


}

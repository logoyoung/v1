<?php
namespace Cli\Controller;

use HP\Log\Log;
use HP\Op\Anchor;
use HP\Op\publicRequist;

class RateController extends \Think\Controller
{
//	/**
//	 * 同步比率遗留老数据问题
//	 */
//	public function run()
//	{
//
////		$Fdao=D('rate_change_record');
////		$data=date('Y-m-d',strtotime('-1 day')).' 00:00:00';
////		$list=$Fdao->where("status=0 and role_change_id !=0 and ctime >'$data'")->select();
////		if( $list )
////		{
////			$uids=array_column($list,'uid');
////			Anchor::RepairRate($uids);
////		}
//		Anchor::RepairRate(array('137771','132407'));
//	}


	public function run( $t = '' )
	{
//		if( empty( $t ) )
//		{
//			$stime = date( 'Y-m-d' ) . ' 00:00:00';
//			$etime = date( 'Y-m-d' ) . ' 23:59:59';
//		}
//		else
//		{
//			$stime = $t . ' 00:00:00';
//			$etime = $t . ' 23:59:59';
//		}
//
//		$dao = D( 'rate_change_record' );
//		$res = $dao->where( "status=0 and ctime>='$stime'  and ctime<='$etime' and role_change_id !=0" )->order( "ctime asc" )->select();
//		if( $res )
//		{
//			foreach ( $res as $v )
//			{
//				if( $v['after_rate'] )
//				{
//					$res = publicRequist::outside_setRate( array( $v['uid'] => $v['id'] ), $v['after_rate'], $v['desc'] );//通知财务系统
//					if( $res )
//					{
//						$dao->where( "id=" . $v['id'] )->save( array( 'status' => 1 ) );
//						Log::statis( json_encode( $res ) );
//					}
//					else
//					{
//						Log::statis( json_encode( array( 'unsuccess' => $res ) ) );
//					}
//				}
//			}
//		}
	}

	public function one( $id = '' )
	{
//		if( empty( $id ) )
//		{
//			return false;
//		}
//
//		$dao = D( 'rate_change_record' );
//		$res = $dao->where( "id=$id" )->select();
//		if( $res )
//		{
//			foreach ( $res as $v )
//			{
//				if( $v['after_rate'] )
//				{
//					$res = publicRequist::outside_setRate( array( $v['uid'] => $v['id'] ), $v['after_rate'], $v['desc'] );//通知财务系统
//					dump($res);
//					if( $res )
//					{
//						$dao->where( "id=" . $v['id'] )->save( array( 'status' => 1 ) );
//						Log::statis( json_encode( $v ) );
//					}
//					else
//					{
//						Log::statis( json_encode( array( 'unsuccess' => $res ) ) );
//					}
//				}
//			}
//		}
	}


}
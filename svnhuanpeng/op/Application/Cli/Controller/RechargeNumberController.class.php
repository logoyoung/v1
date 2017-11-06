<?php
namespace Cli\Controller;
use HP\Log;
class RechargeNumberController extends \Think\Controller
{
	/**
	 * 同步用户充值次数
	 */

	public function number()
	{
		$m = array( -4,-3, -2, -1, 0 );
		$Udao = D( 'userstatic' );
		$sql = "update userstatic set recharge_number=0";
		$Udao->execute( $sql ); //清空
		for ( $i = 0, $k = count( $m ); $i < $k; $i++ )
		{
			$month = "$m[$i] months";
			$im = date("Ym",strtotime($month));
			$dao = new \Common\Model\HPFMonthModel( 'rechargeRecord', $month );
			$sql = "select uid,count(*) as total  from hpf_rechargeRecord_$im where status=100 group by uid";
			$list = $dao->query( $sql );
			if($list){
				foreach ( $list as $v )
				{
					$sql = "update userstatic set recharge_number=recharge_number+" . $v['total'] . " where uid=" . $v['uid'];
					$Udao->execute( $sql );
				}
			}
		}
	}


	public function synchro()
	{
		$UserDao = D( "userstatic" );
		$m = array( 0 );
		for ( $i = 0, $k = count( $m ); $i < $k; $i++ )
		{
			$month = "$m[$i] months";
			$Cdao = new \Common\Model\HPFMonthModel( 'rechargeRecord', $month );
			$rechargeRes = $Cdao->field( 'uid,min(ctime) as ftime' )->where( "status=100" )->group( 'uid' )->select();
			if( $rechargeRes )
			{
				foreach ( $rechargeRes as $v )
				{
					if( strtotime( $v['ftime'] ) > strtotime( '2017-07-14 00:00:00' ) )
					{
						Log\Log::api( json_encode( array( 'uid' => $v['uid'], 'first' => $v['ftime'] ) ) );
						$time = $UserDao->field( 'uid,first_recharge_time' )->where( "uid=" . $v['uid'] )->select();
						Log\Log::api( json_encode( array( $v['uid'] => $time ) ) );
						if( $time[0]['first_recharge_time'] == '0000-00-00 00:00:00' )
						{
							$UserDao->where( 'uid=' . $v['uid'] )->save( array( 'first_recharge_time' => $v['ftime'] ) );
							Log\Log::api( json_encode( array( 'uid' => $v['uid'], 'first' => $v['ftime'] ) ) );
						}
						else
						{
							if( strtotime( $time[0]['first_recharge_time'] ) > strtotime( '2017-07-14 00:00:00' ) )
							{
//								if(strtotime($time[0]['first_recharge_time'])>strtotime($v['ftime'])){
								$UserDao->where( 'uid=' . $v['uid'] )->save( array( 'first_recharge_time' => $v['ftime'] ) );
								Log\Log::api( json_encode( array( 'uid' => $v['uid'], 'ofirst' => $v['ftime'] ) ) );
//								}
							}
						}
					}
				}
			}
		}
	}

}
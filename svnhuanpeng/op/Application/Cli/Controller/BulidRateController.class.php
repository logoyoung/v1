<?php
namespace Cli\Controller;

use HP\Log\Log;
use HP\Op\Anchor;
use HP\Op\publicRequist;

class BulidRateController extends \Think\Controller
{

//public function  arate(){
//	$dao=D("anchor");
//	$list=$dao->select();
//}


	public function lost_hfp( $t = '' )
	{
		$dao = D( 'anchor' );
		$list = $dao->where( "rate not in (0,50,60)" )->select();
		foreach ( $list as $v )
		{
			$check = $this->hpf( $v['uid'] );
			if( $check )
			{
				$hprake = $check[0]['rate'] * 100;
				if( $v['rate'] != (int)$hprake )
				{
					if( $t )
					{
						$this->unsame( $v['uid'], $hprake, $v['rate'] );
					}
					Log::statis( json_encode( array( 'uid' => $v['uid'], 'cid' => $v['cid'], 'lrate' => $v['rate'], 'hrate' => $hprake ) ), '', 'unsame_hpf' );
				}
			}
			else
			{
				if( $t )
				{
					$this->one( $v['uid'] );
				}
				Log::statis( json_encode( array( 'uid' => $v['uid'], 'cid' => $v['cid'], 'rate' => $v['rate'] ) ), '', 'not_in_hpf' );
			}
		}
	}

	public function hpf( $uid )
	{
		$dao = D( 'hpf_rate' );
		$res = $dao->where( "type=1 and uid=" . $uid )->select();
		return $res;
	}


	public function one( $uid = '' )
	{

		$dao = D( 'rate_change_record' );
		$sql = "select *  from rate_change_record where uid=$uid order by ctime desc  limit 1";
		$res = $dao->query( $sql );
		if( $res )
		{
			foreach ( $res as $v )
			{
				if( $v['after_rate'] )
				{
					$res = publicRequist::outside_setRate( array( $v['uid'] => $v['id'] ), $v['after_rate'], $v['desc'] );//通知财务系统
					Log::statis( json_encode( array( 'call' => $res, 'v' => $v ) ), '', 'callback' );
					if( $res )
					{
						$dao->where( "id=" . $v['id'] )->save( array( 'status' => 1 ) );
					}
					else
					{
						Log::statis( json_encode( array( 'unsuccess' => $res ) ) );
					}
				}
			}
		}
	}

	public function unsame( $uid, $brate, $arate )
	{

		$dao = D( 'rate_change_record' );
		$sql = "select *  from rate_change_record where uid=$uid and before_rate=$brate and  after_rate=$arate limit 1";
		$res = $dao->query( $sql );
		if( $res )
		{
			foreach ( $res as $v )
			{
				if( $v['after_rate'] )
				{
					$res = publicRequist::outside_setRate( array( $v['uid'] => $v['id'] ), $v['after_rate'], $v['desc'] );//通知财务系统
					Log::statis( json_encode( array( 'scall' => $res, 'v' => $v ) ), '', 'callback' );
					if( $res )
					{
						$dao->where( "id=" . $v['id'] )->save( array( 'status' => 1 ) );
					}
					else
					{
						Log::statis( json_encode( array( 'unsuccess' => $res ) ) );
					}
				}
			}
		}
	}


	public function getRate( $uid, $time )
	{
		$dao = D( 'rate_change_record' );
		$sql = "select  *  from  rate_change_record  where uid=$uid  and ctime <='$time' order  by ctime desc limit 1;";
		$res = $dao->query( $sql );
		if( $res )
		{
			return $res[0]['after_rate'];
		}
		else
		{
			return 60;
		}
	}

	public function getuids()
	{
		$dao = new \Common\Model\HPFMonthModel( "sendGiftRecord", '2017-08-01' );
		$ruid = $dao->field( 'ruid' )->group( 'ruid' )->select();
		if( $ruid )
		{
			return array_column( $ruid, 'ruid' );
		}
		else
		{
			return array();
		}
	}

	public function getInfoByUids2($uids)
	{
		$dao = new \Common\Model\HPFMonthModel( "sendGiftRecord", '2017-08-01' );
		$ruid = $dao->where("ruid=$uids")->select();
		if( $ruid )
		{
			return $ruid;
		}
		else
		{
			return array();
		}
	}

	public function runner2(){
		$uids=$this->getuids();
		if($uids){
			$list=array();
			echo  '主播id, 当时计算比率 ,实际比率 , 收礼时间'."\n";
			for ($i=0,$k=count($uids);$i<$k;$i++){
				$list = $this->getInfoByUids2($uids[$i]);//获取uid所有记录数
				$ratelog=$this->ratelog($uids[$i]);
				if($list)
				{//导出数据
					foreach ( $list as $v )
					{
						$rate=$this->getRatebyCtime($ratelog,$v['ctime']);
						if($rate==0){
							$rate=60;
						}
						$orate=( $v['gb'] * 10 ) / abs( $v['hb'] );
						if(($orate*100) != $rate){
							$id=$v['id'];
							$hb=$v['hb'];
							$suid=$v['suid'];
							$ruid=$v['ruid'];
							$trate=( $v['gb'] * 10 ) / abs( $v['hb'] );
							$realrate= $rate/100;
							$ctime=$v['ctime'];
							echo "array('id'=>$id,'hb'=>$hb,'suid'=>$suid,'ruid'=>$ruid,'rate'=>$trate,'realrate'=>$realrate,'ctime'=>'$ctime'),\n";
						}
//						$excel[] = array( $v['ruid'], ( $v['gb'] * 10 ) / abs( $v['hb'] ),$rate/100 ,$v['ctime']);
					}
//					\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '主播收礼比率' );
				}
			}
		}
	}

public  function ratelog($uid){
	$dao=D('rate_change_record');
	$res=$dao->where("uid=$uid and after_rate>50")->order('ctime desc')->select();
	if($res){
		foreach ($res as $v){
			$rate[strtotime($v['ctime'])]=$v['after_rate'];
		}
	}else{
		$udao=D('anchor');
		$info=$udao->where("uid=$uid")->select();
		$rate=$info[0]['rate'];
	}
	return $rate;
}

public  function  getRatebyCtime($list,$ctime){
	if(is_array($list)){
		foreach ( $list as $k => $v )
		{
			if( (int)strtotime($ctime) > (int)$k )
			{
				return $list[$k];
				break;
			}
		}
	}else{
		return $list;
	}
}






}
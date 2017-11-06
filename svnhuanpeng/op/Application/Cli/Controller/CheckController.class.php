<?php
/*
 * 检测
 */
namespace Cli\Controller;
class CheckController extends \Think\Controller
{

   public function creditsresend($do=null){
       $dao = D("AclAccess");
       $where['id'] = $_GET['id'];
       
       $res = $dao->where($where)->select();
       
       
       echo $dao->_sql();
       
       dump($res);
       
   }

	/**
	 * 直播时长检测、修改
	 *
	 * @param        $start
	 * @param        $end
	 * @param string $do
	 */
	public function livelengthcheck($start,$end,$do=''){
		$liveDao = D('live');
		$liveLenDao = D('liveLength');
		//$where = [];
		$date = '';
		$timenull = '0000-00-00 00:00:00';
		$timeindex = '2017-06-12';
		if(strtotime($start)>strtotime($end) || strtotime($start)<strtotime($timeindex) ){
			echo "please input correct date \n";
		}
		$records = [];
		for ($date=$start;strtotime($date)<=strtotime($end);
			$date=
				date('Ymd',strtotime('1 day',strtotime($date)))){

			//echo "check date => $date\n";
			$nextdate = date('Ymd',strtotime('1 day',strtotime($date)));
			$results = $liveDao
				->field('uid,liveid,stime,etime')
				->where("stime!='{$timenull}' and stime>'$timeindex' and ((stime>='$date' and stime<'$nextdate') or (etime>='$date' and etime<'$nextdate') or (stime<'$date' and etime>='$nextdate') or (stime<'$nextdate' and etime='$timenull'))")
				->select();
			//echo (M()->getLastSql());
			foreach ($results as $data){
				$stime = strtotime($data['stime']);
				$etime = strtotime($data['etime']);
				$dateunix = strtotime($date);
				$nextdateunix = strtotime($nextdate);
				if($stime < $dateunix) $stime = $dateunix;
				if($data['etime'] == $timenull||$etime>=$nextdateunix) {
					//echo "跨天\n";
					$etime = $nextdateunix-1;
					//echo "stime:.".date("Y-m-d H:i:s",$stime).",etime:".date("Y-m-d H:i:s",$etime)."\n";
				}
				$timelen = $etime - $stime;
				$timelen = $timelen<0?0:$timelen;
				$records[$data['uid']][date('Y-m-d',$stime)] += $timelen;
				/*$where = [];
				$where['uid'] = $data['uid'];
				$where['date'] = date('Y-m-d',$stime);
				$curlivelength = $liveLenDao->field('length')->where($where)->select();
				//echo M()->getLastSql()."\n";
				//var_dump($curlivelength);continue;
				$checktime = $records[$data['uid']][date('Y-m-d',$stime)];
				if($checktime != $curlivelength[0]['length']){
					echo "========check date ".date('Y-m-d',$stime)." error for user {$data['uid']}=======\n";
					echo "current time length:{$curlivelength[0]['length']}\n";
					echo "check time length:$checktime\n";
					$cha = $checktime-$curlivelength[0]['length'];
					echo "distance time length:$cha\n";
					if($do == 'do'){
						echo "do ".date('Y-m-d',$stime)." for user {$data['uid']}\n";
						$r = $liveLenDao->where($where)->save(['length'=>$timelen]);
						if( !$r ) echo "update failed\n";
						else echo "update suceess\n";
					}else{
						echo "not do!\n";
					}
				}*/
			}
		}
		$where = [];
		foreach ($records as $k=>$v){
			$uid = $k;
			foreach ($v as $lengthdate=>$length){
				$where['uid'] = $uid;
				$where['date'] = $lengthdate;
				$curlivelength = $liveLenDao->field('length')->where($where)->select();
				$curlen = $curlivelength[0]['length'];
				//echo M()->getLastSql()."\n";
				//var_dump($curlivelength);continue;
				//$checklen = ($length<0)?0:$length;
				$checklen = $length;
				if((int)$checklen !== (int)$curlen){
					echo '---- '.$checklen."\n";
					echo '----'.$curlen."\n";
					echo "========check date ".$lengthdate." error for user {$uid}=======\n";
					echo "current time length:{$curlen}\n";
					echo "check time length:$checklen\n";
					$cha = $checklen-$curlen;
					echo "distance time length:$cha\n";
					if($do == 'do'){
						echo "do ".$lengthdate." for user {$uid}\n";
						if(isset($curlivelength[0]['length']))
							$r = $liveLenDao->where($where)->save(['length'=>$checklen]);
						else
							echo "should inert data\n";
							//add data $r = $liveLenDao->where($where)->add([,'length'=>$checklen]);
						if( !isset($r)||!$r ) echo "update failed\n";
						else echo "update suceess\n";
					}else{
						echo "not do!\n";
					}
				}
			}
		}
	}

	public function livecountcheck($start,$end,$do=''){
		$liveDao = D('live');
		$liveLenDao = D('liveLength');
		//$where = [];
		$date = '';
		$timenull = '0000-00-00 00:00:00';
		$timeindex = '2017-06-12';
		if(strtotime($start)>strtotime($end) || strtotime($start)<strtotime($timeindex) ){
			echo "please input correct date \n";
		}
	}

	public function stoplive($start,$end,$do=''){
		$dao = M('live');
		$where = [];
		$where['status'] = ['lt',LIVE];
		$where['ctime'] =['between',"$start,$end"];
		$results = $dao->where($where)->select();
		//var_dump(count($results));
		if(!count($results)){
			echo "无记录\n";
			return;
		}
		foreach ($results as $data){
			$liveids[] = $data['liveid'];
		}
		if(!count($liveids)){
			echo "无liveid\n";
			return;
		}
		$liveids = implode(',',$liveids);
		echo "$liveids\n";
		if( $do=='do' )
		{
			$r = $dao->where( [ 'liveid' => [ 'in', $liveids ] ] )->save(['status'=>LIVE_TIMEOUT]);
			if($r) echo "update sucess\n";
			else echo "update failed\n";
		}
		echo "complete\n";
	}
   
}

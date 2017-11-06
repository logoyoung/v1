<?php
/**
 *同步用户首次充值时间
 *2017-06-08 13：18:00
 *Dylan
 */
namespace Cli\Controller;

class SynchroFirstRechargeController extends \Think\Controller
{

   public function synchro()
   {
	   $UserDao=D("userstatic");
	   $m=array(-3,-2,-1,0);
	   for($i=0,$k=count($m);$i<$k;$i++){
		   $month = "$m[$i] months";
		   $Cdao = new \Common\Model\HPFMonthModel('rechargeRecord',$month);
		   $rechargeRes=$Cdao->field('uid,min(ctime) as ftime')->where("status=100")->group('uid')->select();
		   if($rechargeRes){
			   foreach ($rechargeRes as $v){
				   $time=$UserDao->field('uid,first_recharge_time')->where("uid=".$v['uid'])->select();
				   if($time[0]['first_recharge_time']=='0000-00-00 00:00:00'){
					   $UserDao->where('uid='.$v['uid'])->save(array('first_recharge_time'=>$v['ftime']));
				   }else{
					   if(strtotime($time[0]['first_recharge_time'])>strtotime($v['ftime'])){
						   $UserDao->where('uid='.$v['uid'])->save(array('first_recharge_time'=>$v['ftime']));
					   }
				   }
			   }
		   }
	   }
   }
}
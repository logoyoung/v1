<?php
/*
 * 充值统计
 */
namespace Cli\Controller;
use HP\Op\Statis;
class RechargestatisController extends \Think\Controller
{

   public function init($do=null,$stime=null,$etime=null){
       $dao = D("Statisrecharge");
       $stime?$stime = $stime: $stime = date("Y-m-d",strtotime(date("Y-m-d"))-86400);
       $etime?$etime = $etime: $etime = $stime;
       $datas = Statis::getRecharges($stime, $etime);
       foreach ($datas as $data){
           if($do=="do"){
               $res = $dao->add($data);
           }else{
               $res = $dao->fetchSql(true)->add($data);
               echo($res."\r\n");
           }
       }
   }
   
   public function reset($do=null,$stime=null,$etime=null){
       $dao = D("Statisrecharge");
       $stime?$stime = $stime: $stime = date("Y-m-d",strtotime(date("Y-m-d"))-86400);
       $etime?$etime = $etime: $etime = $stime;
       
       
       if($do=="do"){
           $dao->where(" date>= '".date("Ymd",strtotime($stime))."' and date <= '".date("Ymd",strtotime($etime))."' ")->delete();
       }else{
           $res = $dao->fetchSql(true)->where(" date>= '".date("Ymd",strtotime($stime))."' and date <= '".date("Ymd",strtotime($etime))."' ")->delete();
           echo($res."\r\n");
       }
       self::init($do,$stime,$etime);
   }
}

<?php
/*
 * 活跃设备，新增设备
 */
namespace Cli\Controller;
use HP\Op\Statis;
ini_set('memory_limit', '512M');
class RegisterstatisController extends \Think\Controller
{
   
   public function reset($do=null,$stime=null,$etime=null){
       $dao = D("statisregister");
       $stime?$stime = $stime: $stime = date("Y-m-d",strtotime(date("Y-m-d"))-86400);
       $etime?$etime = $etime: $etime = $stime;
       
       
       if($do=="do"){
           $dao->where(" date>= '".date("Ymd",strtotime($stime))."' and date <= '".date("Ymd",strtotime($etime))."' ")->delete();
       }else{
           $res = $dao->fetchSql(true)->where(" date>= '".date("Ymd",strtotime($stime))."' and date <= '".date("Ymd",strtotime($etime))."' ")->delete();
           echo($res."/n");
       }
       self::init($do,$stime,$etime);
   }
   
   
   public function init($do=null,$stime=null,$etime=null){
       $dao = D("statisregister");
       $stime?$stime = $stime: $stime = date("Y-m-d",strtotime(date("Y-m-d"))-86400);
       $etime?$etime = $etime: $etime = $stime;
       $type = I("get.type")?I("get.type"):"day";
       $datas = self::getUserStatisByday($stime, $etime);
       foreach ($datas['data_day'] as $date=>$data){//日数据
           $date = date("Ymd",strtotime($date));
           $data['date']=$date;
           $data['type']=Statis::REGISTER_TYPE_DAY;
           $data['userview'] = count($data['userview']);
           if($do=="do"){
               echo $dao->add($data);
           }else{
               echo $dao->fetchSql(true)->add($data);
               echo "\r\n";
           }
       }
       foreach ($datas['data_channel'] as $date=>$channels){//日渠道数据
           foreach ($channels as $channel=>$data){
               $date = date("Ymd",strtotime($date));
               $data['date']=$date;
               $data['channel']=$channel;
               $data['type']=Statis::REGISTER_TYPE_DAY_CHANNEL;
               $data['userview'] = count($data['userview']);
               if($do=="do"){
                   echo $dao->add($data);
               }else{
                   echo $dao->fetchSql(true)->add($data);
                   echo "\r\n";
               }
           }
       }
       foreach ($datas['data_hours'] as $date=>$data){//小时日数据
           $date = $date.":00:00";
           $day = date("Ymd",strtotime($date));
           $hours = date("H",strtotime($date));
           $data['date']=$day;
           $data['hours']=$hours;
           $data['type']=Statis::REGISTER_TYPE_HOURS;
           $data['userview'] = count($data['userview']);
           if($do=="do"){
               echo $dao->add($data);
           }else{
               echo $dao->fetchSql(true)->add($data);
               echo "\r\n";
           }
       }
       foreach ($datas['data_hours_channel'] as $date=>$channels){//小时渠道
           $date = $date.":00:00";
           foreach ($channels as $channel=>$data){
               $day = date("Ymd",strtotime($date));
               $hours = date("H",strtotime($date));
               $data['date']=$day;
               $data['hours']=$hours;
               $data['channel']=$channel;
               $data['type']=Statis::REGISTER_TYPE_HOURS_CHANNEL;
               $data['userview'] = count($data['userview']);
               if($do=="do"){
                   echo $dao->add($data);
               }else{
                   echo $dao->fetchSql(true)->add($data);
                   echo "\r\n";
               }
           }
       }
   }
   
   
   function getUserStatisByday($stime,$etime){
       $datas = [];
       $stime = $stime." 00:00:00";
       $etime = date("Y-m-d",strtotime($etime)+86400)." 00:00:00";
       $userChannel = Statis::getUserChannels($stime, $etime);//渠道
       Statis::getUserByday($stime, $etime,$userChannel,$datas);//注册
       Statis::getRealUserByday($stime, $etime,$userChannel,$datas);//实名认证
       Statis::getUserViewByday($stime, $etime,$userChannel,$datas);//设备
       Statis::getUserDeviceByday($stime, $etime,$userChannel,$datas);//新增设备
       return $datas;
   }
   
}

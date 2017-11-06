<?php
/*
 * 留存率
 *   留存率计算公式编辑
 *  留存率=新增用户中登录用户数/新增用户数*100%（一般统计周期为天）
 *  新增用户数：在某个时间段（一般为第一整天）新登录应用的用户数；
 *  登录用户数：登录应用后至当前时间，至少登录过一次的用户数；
 *  次日留存率：（当天新增的用户中，在注册的第2天还登录的用户数）/第一天新增总用户数；
 *  第3日留存率：（第一天新增用户中，在注册的第3天还有登录的用户数）/第一天新增总用户数；
 *  第7日留存率：（第一天新增的用户中，在注册的第7天还有登录的用户数）/第一天新增总用户数；
 *  第30日留存率：（第一天新增的用户中，在注册的第30天还有登录的用户数）/第一天新增总用户数。
 *  
 */
namespace Cli\Controller;
use HP\Op\Statis;
use Org\Util\Date;
class RetainedstatisController extends \Think\Controller
{
   
   public function init($do=null,$stime=null,$etime=null){
       $dao = D('statisretained');
       $stime?$stime = $stime: $stime = date("Y-m-d",strtotime(date("Y-m-d"))-86400);
       $etime?$etime = $etime: $etime = $stime;
       $days = (strtotime($etime) - strtotime($stime))/86400;
       echo " ============= stime: $stime , etime: $etime , days: $days";echo "\r\n";
       for ($i=0;$i<=$days;$i++){
           $tdate = date("Y-m-d",strtotime($stime)+$i*86400);
           $datas = self::getdatas($tdate);
           foreach ($datas["insert"] as $date=>$channels){
               foreach ($channels as $channel=>$col){
                   $adddata = $col;
                   $adddata["date"] = $date;
                   $adddata["channel"] = $channel;
                   if($do=="do"){
                       echo $dao->add($adddata);
                   }elseif($do=="reset"){
                       $where["date"] = $date;
                       $where["channel"] = $channel;
                       echo $dao->where($where)->delete();
                       echo $dao->add($adddata);
                   }else{
                       echo $dao->fetchSql(true)->add($adddata);
                       echo "\r\n";
                   }
               }
           }
           
           foreach ($datas["update"] as $date=>$channels){
               foreach ($channels as $channel=>$col){
                   $where["date"] = $date;
                   $where["channel"] = $channel;
                   if($do=="do"||$do=="reset"){
                       echo $dao->where($where)->save($col);
                   }else{
                       echo $dao->fetchSql(true)->where($where)->save($col);
                       echo "\r\n";
                   }
                   
               }
           }
       }
   }
   
   
   function getdatas($tdate){
        $days = [0=>'t_0',1=>'t_1',3=>'t_3',7=>'t_7',15=>'t_15',30=>'t_30'];
        $dao = D("userviewrecord");//访问记录表
        $userdevicedao = D("userdevice");//新增记录表
        $stime = $tdate." 00:00:00";
        $etime = $tdate." 23:59:59";
        $where["action"] = 1;//打开app
        $where["ctime"] = array(array('egt',$stime),array('elt',$etime));
        $res = $dao->where($where)->distinct(true)->getField("udid",true);
        
        foreach ($days as $day=>$col){
            //不同时间新增的设备udid
            $date = date("Ymd",strtotime($tdate)-86400*$day);
            if(strtotime($date)<strtotime('2017-06-01'))continue;
            $devicedatas = $userdevicedao->where(["cdate"=>["eq",$date]])->getField("udid,channel");
            if($col=='t_0'){
                foreach ($devicedatas as $udid=>$channel){
                    $insertdata[$date][$channel]['t_0']++;
                }
            }else{
                foreach ($res as $udid){
                    $channel = $devicedatas[$udid];
                    if($channel!==null){//留存了
                        $updatedata[$date][$channel][$col]++;
                    }
                    
                }
            }
        }
        $data["insert"] = $insertdata;
        $data["update"] = $updatedata;
        return $data;
    }
   
}

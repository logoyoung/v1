<?php
header("Content-Type: text/html;charset=utf-8");
include '../../includeAdmin/init.php';
include '../../includeAdmin/Redis.class.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$db = new DBHelperi_admin();
$redis = new RedisHelp();
$res=$redis->get('yandong');
var_dump($res);
//function  getuid($db){
//    $res=$db->field('uid')->order('uid asc')->select('userstatic');
//    return $res;
//}
//function cleantask($db){
//       $sql = "truncate  table  task";
//       $res = $db->query($sql);
//       if($res !==false){
//           echo '清空task数据库成功~'."<br/>";
//       }else{
//           echo '清空task数据库失败~'."<br/>";
//       }      
//}
/**
 * 清除redis
 * @param type $redis
 * @param type $db
 */
//function  clean($redis,$db){
//     $res=getuid($db);
//     foreach($res as $v){
//         $redis->del("IsFirstUploadPic:" . $v['uid']) ;
//         $redis->del("FOLLOWUSER_OVER_".$v['uid']) ;
//         $redis->del("IsFirstLoginfromApp:" . $v['uid']) ;
//         $redis->del("firstSendBean:".$v['uid']) ;
//         $redis->del("SHAMAPI_RECHARGE_".$v['uid']) ;
//         echo "数据清空完成~";
//     }
//}
//cleantask($db);
//clean($redis,$db);
//$keys = "IsFirstUploadPic:" . $uid;  //首次上传照片
//"FOLLOWUSER_OVER_$uid";//关注5人
//"IsFirstLoginfromApp:" . $row[0]['uid'];// 首次登录app
//"firstSendBean:$uid"//首次充值
//        "SHAMAPI_RECHARGE_$uid"//;
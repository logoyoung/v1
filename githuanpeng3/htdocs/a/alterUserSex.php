<?php

include '../init.php';
/**
 * 修改用户性别
 * date 2016-07-21  10:00 
 * author yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();
/**
 * 修改性别
 * @param type $uid  用户id
 * @param type $sex  性别
 * @param type $db
 * @return boolean
 */
function updateSex($uid,$sex,$db){
    if(empty($uid)){
        return false;
    }
    $data=array(
        'sex'=>$sex
    );
    $res=$db->where("uid=$uid")->update('userstatic',$data);
    if($res !==false){
        return array('isSuccess'=>'1','sex'=>$sex);
    }else{
        return array('isSuccess'=>'0','sex'=>$sex); 
    }
}
/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$sex = isset($_POST['sex']) ? trim($_POST['sex']) : '';
if (empty($uid) || empty($encpass) || !in_array($sex,array(0,1,2))) {
    error(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$res=updateSex($uid,$sex,$db);
exit(jsone($res));
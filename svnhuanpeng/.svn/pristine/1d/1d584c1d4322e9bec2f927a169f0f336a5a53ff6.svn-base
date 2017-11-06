<?php

include '../../../include/init.php';
require(INCLUDE_DIR . 'User.class.php');
/**
 * 获取省市
 * date 2016-12-05 19:47
 * author yandong@6rooms.com
 */

$db = new DBHelperi_huanpeng();

function getaddress($province, $db)
{
    if (empty($province)) {
        $res =$db->field('id,name')->select('province');
    } else {
        $res =$db->field('id,name')->where("pid=$province")->select('city');
    }
    return $res;
}

function getProvinceName($province,$db){
    if(empty($province)){
        return false;
    }
    $res =$db->field('id,name')->where("id=$province")->select('province');
    if(false !==$res){
        return $res[0]['name'];
    }else{
        return false;
    }
}

function  getCityNameByPids($pids,$db){
    if(empty($pids)){
        return false;
    }
    $res=$db->field('pid,id,name')->where("pid in ($pids)")->select('city');
    if(false !==$res){
        return $res;
    }else{
        return false;
    }
}


/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '90';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '9db06bcff9248837f86d1a6bcf41c9e7';
$province= isset($_POST['pid']) ? (int)($_POST['pid']) : '';
$type= isset($_POST['type']) ? (int)($_POST['type']) : 0;
if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
if (!empty($province)) {
    if (!is_numeric($province)) {
        error2(-4070,2);
    }
    if ($province < 1 || $province > 36) {
        error2(-4070,2);
    }
}
//检查用户登陆状态
$userState = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $userState) {
    error2(-4067,2);
}
if($type){
    if(!in_array($type,array(1))){
        error(-4070,2);
    }else{
        $province=0;
    }
}
$res=getaddress($province, $db);
if(false !==$res){
    $list=array();
    $tmp=$map=$clist=array();
    if($type){
        $robj = new RedisHelp();
        $Addrkey="ADDR_MAPS";
         $getCatch = $robj->get($Addrkey);
        if($getCatch){
           $map=json_decode($getCatch,true);
        }else {
            $city = getCityNameByPids(implode(',', array_column($res, 'id')), $db);
            for ($i = 0, $k = count($res); $i < $k; $i++) {
                $tmp['pid'] = $res[$i]['id'];
                $tmp['name'] = $res[$i]['name'];
                for ($m = 0, $n = count($city); $m < $n; $m++) {
                    if ($city[$m]['pid'] == $res[$i]['id']) {
                        $tep['id'] = $city[$m]['id'];
                        $tep['name'] = $city[$m]['name'];
                        array_push($clist, $tep);
                    }
                    $tmp['list'] = $clist;
                }
                array_push($map, $tmp);
                $clist = array();
            }
            $robj->set($Addrkey, json_encode($map));
        }
//        echo $_GET['jsoncallback'].json_encode(array('list'=>$map));
           succ(array('list'=>$map));
    }else {
        if ($province) {
            $pname = getProvinceName($province, $db);
            foreach ($res as $v) {
                $temp['cid'] = $v['id'];
                $temp['cname'] = $v['name'];
                array_push($list, $temp);
            }
            succ(array('pid' => $province, 'pname' => $pname, 'list' => $list));
        } else {
            foreach ($res as $v) {
                $temp['pid'] = $v['id'];
                $temp['pname'] = $v['name'];
                array_push($list, $temp);
            }
            succ(array('list' => $list));
        }
    }
}else{
    error2(-5017,2);
}
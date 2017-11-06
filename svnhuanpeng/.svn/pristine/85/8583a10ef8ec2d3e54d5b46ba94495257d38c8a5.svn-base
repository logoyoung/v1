<?php

require '../../../includeAdmin/init.php';
require '../../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 添加资讯推荐
 * @author yandong@6room.com
 * date 2016-12-02  10:52
 */

function  getRe($itype, $db){
    if(empty($itype)){
       return false;
    }
    $res=$db->field('id,list')->where("id=$itype")->select('recommend_information');
    if(false !==$res){
        if($res){
               foreach($res as $v){
                   $temp[$v['id']]=$v['list'];
               }
               return $temp;
        }else{
            return array();
        }
    }else{
        return false;
    }

}
function addToRecommentList($itype, $ids, $db)
{
    if (empty($itype) || empty($ids)) {
        return false;
    }
    $utime = date('Y-m-d H:i:s', time());
    $sql = "insert into recommend_information (`id`,`list`,`utime`) value($itype,'$ids','$utime') on duplicate key update list='$ids',utime='$utime'";
    $res = $db->query($sql);
    if (false !== $res) {
        return  true;
    } else {
        return false;
    }

}

function  changeIsrecommend($ids,$itype,$db){
    if(empty($ids)){
        return false;
    }
    $res=$db->where("id in ($ids)")->update('admin_information',array('isrecommend'=>$itype));
    if(false !==$res){
        return true;
    }else{
        return false;
    }
}


/**
 * start
 */
$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$itype = isset($_POST['itype']) ? (int)($_POST['itype']) : '';
$id = isset($_POST['ids']) ? (int)($_POST['ids']) : '';
if (empty($uid) || empty($encpass)) {
    error(-1007);
}
if(empty($id)){
    error(-1026);
}
if(!in_array($itype,array(1,2))){
    error(-1023);
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$new = array_filter(explode(',', $id));
if (count($new) > (int)INFORMATION_RECOMMENT_NUMBER) {
    error(-1022);
}
$res =getRe($itype, $db);
if(false ===$res){
    error(-1014);
}
if ((int)count(explode(',', $res[$itype])) >= (int)INFORMATION_RECOMMENT_NUMBER) {
    error(-1020);
}
if (((int)count($new)) + (count(explode(',', $res[$itype]))) > (int)INFORMATION_RECOMMENT_NUMBER) {
    error(-1022);
}
if ($res) {
    $lists = $res[$itype] . ',' . implode(',',$new);
} else {
    $lists = implode(',',$new);
}
$isfull =explode(',', $lists);
if (count($isfull) > (int)INFORMATION_RECOMMENT_NUMBER) {
    error(-1035);
}
$ares=addToRecommentList($itype, $lists, $db);
if($ares){
    $isres=changeIsrecommend($lists,$itype,$db);
    if($isres){
        succ();
    }else{
        error(-1014);
    }
}else{
    error(-1014);
}





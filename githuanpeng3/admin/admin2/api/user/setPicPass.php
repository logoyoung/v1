<?php

/**
 * 审核头像通过
 * yandong@6rooms.com
 * date 2016-10-12 13:55
 * 
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
require '../../includeAdmin/Redis.class.php';
$db = new DBHelperi_admin();

/**
 * 完成审核动作清空admin_wait_user_pic表对应数据
 * @param string $uids  主播id
 * @param type $db
 * @return boolean
 */
function upWaitUserPic($adminid,$uids, $db) {
    if (empty($uids)) {
        return false;
    }
    $res = $db->where("uid in ($uids) and adminid=$adminid")->update('admin_wait_user_pic', array('status' =>1));
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * 处理未通过的
 * @param string $failluid  未通过用户uid 多个用逗号隔开
 * @param type $db
 * @return boolean
 */
function failUserPic($uid,$failluid, $fileStatus,$db) {
    $row = $db->field('uid,pic')->where("uid in ($failluid)")->select('admin_user_pic');
    if (!empty($row) && (false !== $row)) {
        $update = $db->where("uid in ($failluid)")->update('admin_user_pic', array('status' =>$fileStatus, 'utime' => date('Y-m-d H:i:s', time())));
        if ($update !== false) {
            $res=upWaitUserPic($uid,$failluid, $db);
            if($res){
                return true;  
            }else{
                return false;  
            }         
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**修改表userstatic表中的pic值
 * @param $row
 * @param $db
 * @return bool
 */
function changeUserStatic($row,$db){
    if(empty($row)){
        return false;
    }
    foreach($row  as  $v){
        $data[$v['uid']]=$v['pic'];
    }
    $ids = implode(',', array_keys($data));
    $sql = "UPDATE userstatic SET pic = CASE uid ";
    foreach ($data as $id => $pic) {
        $sql .= "WHEN $id THEN '$pic' ";
    }
    $sql .= "END WHERE uid IN ($ids)";
    $res=$db->query($sql);
    if($res !==false){
        return true;
    }else{
        return false;
    }
}


/**
 * 修改审核状态
 * @param string $succluid  主播id 多个的话用逗号隔开
 * @param type $db
 * @return boolean
 */
function UpdatePicStatus($uid,$succluid, $failluid, $db) {
    if (empty($succluid) && empty($failluid)) {
        return false;
    }

    $succStatus=USER_PIC_PASS;//人工审通过
    $fileStatus=USER_PIC_UNPASS;//人工审核未通过
    if ($succluid) {
        $row = $db->field('uid,pic')->where("uid in ($succluid)")->select('admin_user_pic');
        if (!empty($row) && (false !== $row)) {
            $update = $db->where("uid in ($succluid) ")->update('admin_user_pic', array('status' =>$succStatus, 'utime' => date('Y-m-d H:i:s', time()))); //修改审核状态通过
            if ($update !== false) {
                $redisObj = new RedisHelp();
                foreach ($row as $v) {
                    $keys = "IsFirstUploadPic:" . $v['uid'];//头像上传任务
                    $resul = $redisObj->get($keys);
                    dong_log('任务头像',$resul.':'.$v['uid'],$db);
                    if (!$resul) {
                        $redisObj->set($keys, 1); //设置标志
                        synchroTask($v['uid'], 6, 0, 100, $db); //同步任务
                    }
                    $tostatic = $db->where("uid= " . $v['uid'])->update('userstatic', array('pic' => $v['pic']));
                }
//                $tostatic =  changeUserStatic($row,$db)；
                if ($tostatic !== false) {
                    if ($failluid) {
                        failUserPic($uid,$failluid,$fileStatus,$db);
                    }
                    upWaitUserPic($uid,$succluid, $db);
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        if ($failluid) {
            $res=failUserPic($uid,$failluid,$fileStatus,$db);
            if($res){
                return true;
            }else{
                 return false;
            }
        }
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
$succluid = isset($_POST['succuid']) ? trim($_POST['succuid']) : ''; //主播id列表批量可用逗号隔开(可通过的)
$failluid = isset($_POST['failuid']) ? trim($_POST['failuid']) : ''; //主播id列表批量可用逗号隔开(不合格的)
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
if (empty($succluid) && empty($failluid)) {
    error(-1007);
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = UpdatePicStatus($uid,$succluid, $failluid, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}

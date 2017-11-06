<?php

/**
 * 审核昵称
 * yandong@6rooms.com
 * date 2016-10-19 11:07
 * 
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();

/**
 * 审核成功更改userstatic表中的字段
 * @param type $row
 * @param type $db
 */
function changeUserStatic($row,$db){
    if(empty($row)){
        return false;
    }
    foreach($row  as  $v){
        $data[$v['uid']]=$v['nick'];
    }
    $ids = implode(',', array_keys($data));
    $sql = "UPDATE userstatic SET nick = CASE uid ";
    foreach ($data as $id => $nick) {
        $sql .= "WHEN $id THEN '$nick' ";
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
 * 未通过审核标记userstatic中isfree
 * @param type $row
 * @param type $db
 * @return boolean
 */
function setIsFree($row,$db){
    if(empty($row)){
        return false;
    }
    foreach($row  as  $v){
        $data[$v['uid']]=1;
    }
    $uids = implode(',', array_keys($data));
    $sql = "UPDATE userstatic SET  isfree= CASE uid ";
    foreach ($data as $uid => $isfree) {
        $sql .= "WHEN $uid THEN '$isfree' ";
    }
    $sql .= "END WHERE uid IN ($uids)";
    $res=$db->query($sql);
    if($res !==false){
        return true;
    }else{
        return false;
    }
}

/**
 * 完成审核动作更新admin_wait_user_pic表对应数据
 * @param string $uids  //主播id
 * @param int $adminid  //审核者id
 * @param type $db
 * @return boolean
 */
function upWaitUserNick($uids, $adminid, $db) {
    if (empty($uids) || empty($adminid)) {
        return false;
    }
    $res = $db->where("uid in ($uids) and  adminid=$adminid")->update('admin_wait_user_nick', array('status' => USER_NICK_PASS));
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
function failUserNick($failluid, $uid,$db) {
    $row = $db->field('uid,nick')->where("uid in ($failluid)")->select('admin_user_nick');
    if (!empty($row) && (false !== $row)) {
        $update = $db->where("uid in ($failluid)")->update('admin_user_nick', array('status' => USER_NICK_UNPASS, 'utime' => date('Y-m-d H:i:s', time())));
        if ($update !== false) {
            $res = upWaitUserNick($failluid,$uid, $db);
            if ($res) {
                $res=setIsFree($row,$db);
                if($res !==false){
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
    } else {
        return false;
    }
}


/**
 * 淇敼瀹℃牳鐘舵€? * @param string $succluid  涓绘挱id 澶氫釜鐨勮瘽鐢ㄩ€楀彿闅斿紑
 * @param type $db
 * @return boolean
 */
function UpdateNickStatus($uid, $succluid, $failluid, $db) {
    if (empty($succluid) && empty($failluid)) {
        return false;
    }
    if ($succluid) {
        $row = $db->field('uid,nick')->where("uid in ($succluid)")->select('admin_user_nick');
        if (!empty($row) && (false !== $row)) {
            $update = upWaitUserNick($succluid, $uid,$db); //鏇存敼admin_wait_user_nick琛ㄧ姸鎬?            if ($update !== false) {
                $tostatic = $db->where("uid in ($succluid) ")->update('admin_user_nick', array('status' => USER_NICK_PASS, 'utime' => date('Y-m-d H:i:s', time()))); //淇敼瀹℃牳鐘舵€侀€氳繃
                if ($tostatic !== false) {
                    if ($failluid) {
                        failUserNick($failluid, $uid,$db); //澶勭悊鏈€氳繃鐨?                    }
                    changeUserStatic($row,$db); //鏇存敼userstatic琛ㄧ殑nick
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
            $res = failUserNick($failluid,$uid, $db);//鍓嶄竴姝ュ簲璇ュ垽鏂槸鍏堝鍚庡彂杩樻槸鍏堝彂鍚庡
            if ($res) {
                return true;
            } else {
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
$succluid = isset($_POST['succuid']) ? trim($_POST['succuid']) : ''; //涓绘挱id鍒楄〃鎵归噺鍙敤閫楀彿闅斿紑(鍙€氳繃鐨?
$failluid = isset($_POST['failuid']) ? trim($_POST['failuid']) : ''; //涓绘挱id鍒楄〃鎵归噺鍙敤閫楀彿闅斿紑(涓嶅悎鏍肩殑)
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

$res = UpdateNickStatus($uid, $succluid, $failluid, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}

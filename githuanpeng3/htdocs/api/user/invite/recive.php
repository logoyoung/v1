<?php
/**
 * 领取奖励
 * auchor  Dylan
 * date 2016-12-08 14:38
 */
include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();

/**添加到奖励纪录比表
 * @param $uid  邀请者id
 * @param $ruid  新注册用户id
 * @param $db
 * @return array|bool
 */
function recordList($uid, $ruid, $reward, $db)
{
    $data = array(
        'uid' => $uid,
        'ruid' => $ruid,
        'reward' => $reward
    );
    $res = $db->insert("invite_reward_record", $data);
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

/**领取奖励修改状态
 * @param $uid  邀请者id
 * @param $ruid  新注册用户id
 * @param $db
 * @return bool
 */
function updaterecord($uid, $ruid, $db)
{
    $res = $db->where("suid =$uid  and  ruid=$ruid")->update('invite_record', array('status' => 2));
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

/**添加奖励到用户账户
 * @param $uid   用户uid
 * @param $reward  奖励数额
 * @param $db
 */
function updateUserActive($uid, $reward, $db)
{
    if (empty($uid) || empty($reward)) {
        return false;
    }
    $sql = "update useractive set hpbean=hpbean + $reward  where uid =$uid";
    $res=$db->query($sql);
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}


function recive($uid, $ruid, $reward, $db)
{
    $record = updaterecord($uid, $ruid, $db);
    $addres = recordList($uid, $ruid, $reward, $db);
    $active=updateUserActive($uid, $reward, $db);
    if ($record && $addres && $active) {
        return true;
    } else {
        return false;
    }

}


function checkIsRecive($uid,$ruid,$db){
    if(empty($uid)||empty($ruid)){
        return false;
    }
    $res=$db->field('status')->where("suid=$uid  and ruid=$ruid")->select('invite_record');
    if(false !==$res){
        if($res){
            return $res[0]['status'];
        }else{
            return array();
        }
    }else{
        return false;
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '90';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$ruid = isset($_POST['ruid']) ? (int)$_POST['ruid'] : '';
$reward = 500;
if (empty($uid) || empty($encpass) || empty($ruid)) {
    error2(-4013);
}
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error2(-4067,2);
}
$check=checkIsRecive($uid,$ruid,$db);//校验是否已经领过
if(false !==$check){
    if($check==2){
       error2(-4075,2);
    }
}else{
    error2(-5017,2);
}
$res = recive($uid, $ruid, $reward, $db);
if (false !== $res) {
    succ();
} else {
    error2(-5017,2);
}
<?php

/**
 * 重审昵称
 * yandong@6rooms.com
 * date 2016-11-16 13:12
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
function changeUserStatic($row, $db)
{
    if (empty($row)) {
        return false;
    }
    foreach ($row as $v) {
        $data[$v['uid']] = $v['nick'];
    }
    $ids = implode(',', array_keys($data));
    $sql = "UPDATE userstatic SET nick = CASE uid ";
    foreach ($data as $id => $nick) {
        $sql .= "WHEN $id THEN '$nick' ";
    }
    $sql .= "END WHERE uid IN ($ids)";
    $res = $db->query($sql);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * 未通过审核标记userstatic中isfree
 * @param type $row
 * @param type $db
 * @return boolean
 */
function setIsFree($luid, $db)
{
    if (empty($luid)) {
        return false;
    }

   $res=$db->where("uid  in ($luid)")->update('userstatic',array('isfree'=>1));
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}


function getOldNick($uids, $db)
{
    if (empty($uids)) {
        return false;
    }
    $res = $db->field('uid,oldnick')->where("uid in ($uids)")->select("admin_user_nick");
    if (!empty($res) && false !== $res) {
        foreach ($res as $v) {
            $temp[$v['uid']] = $v['oldnick'];
        }
        return $temp;
    } else {
        return array();
    }
}


/**
 * 修改审核状态
 * @param string $succluid 主播id 多个的话用逗号隔开
 * @param type $db
 * @return boolean
 */
function UpdateNickStatus($succluid, $db)
{
    $list=array();
    if (empty($succluid)) {
        return false;
    }
//    $tostatic = $db->where("uid in ($succluid) ")->update('admin_user_nick', array('status' => 0, 'utime' => date('Y-m-d H:i:s', time()))); //淇敼瀹℃牳鐘舵€侀€氳繃
  $tostatic=true;
    if ($tostatic !== false) {
        $res = getOldNick($succluid, $db);
        if ($res) {
            $uids = explode(',', $succluid);
            for ($i = 0, $k = count($uids); $i < $k; $i++) {
                $row['uid'] = $uids[$i];
                $row['nick'] = $res[$uids[$i]];
                array_push($list,$row);
            }
           changeUserStatic($list, $db); //更改userstatic表的nick
            setIsFree($succluid, $db);
            return true;
        }else{
            return false;
        }

    } else {
        return false;
    }
}


/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
$succluid = isset($_POST['succuid']) ? trim($_POST['succuid']) : ''; //主播id列表批量可用逗号隔开(可通过的)
if (empty($uid) || empty($encpass) || empty($type) || empty($succluid)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = UpdateNickStatus($succluid, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}

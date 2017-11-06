<?php

/**
 * 审核直播标题
 * yandong@6rooms.com
 * date 2016-10-20 17:15
 * 
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

/**
 * 审核通过 更改live表中的字段
 * @param type $row
 * @param type $db
 * @return boolean
 */
function changeLiveTitle($row, $db) {
    if (empty($row)) {
        return false;
    }
    foreach ($row as $v) {
        $data[$v['liveid']] = $v['title'];
    }
    $ids = implode(',', array_keys($data));
    $sql = "UPDATE live SET title = CASE liveid ";
    foreach ($data as $id => $nick) {
        $sql .= "WHEN $id THEN '$nick' ";
    }
    $sql .= "END WHERE liveid IN ($ids)";
    $res = $db->query($sql);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * 完成审核动作更新admin_wait_live_title表对应数据
 * @param string $liveid  //直播id
 * @param int $adminid  //审核者id
 * @param type $db
 * @return boolean
 */
function upWaitLiveTitle($liveid, $adminid, $db) {
    if (empty($liveid) || empty($adminid)) {
        return false;
    }
    $res = $db->where("liveid in ($liveid) and  adminid=$adminid")->update('admin_wait_live_title', array('status' =>1));
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
function failLiveTitle($failluid, $uid, $db,$status) {
    $row = $db->field('liveid,title')->where("liveid in ($failluid)")->select('admin_live_title');
    if (!empty($row) && (false !== $row)) {
        $update = $db->where("liveid in ($failluid)")->update('admin_live_title', array('status' => $status, 'utime' => date('Y-m-d H:i:s', time())));
        if ($update !== false) {
            $res = upWaitLiveTitle($failluid, $uid, $db);
            if ($res) {
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
}

/**
 * 直播标题审核
 * @param int $uid  //审核者id
 * @param type $succluid  //审核成功
 * @param type $failluid  //审核失败
 * @param type $db
 * @return boolean
 */
function UpdateLiveTitleStatus($uid, $succluid, $failluid, $db) {
    if (empty($succluid) && empty($failluid)) {
        return false;
    }
    $checkTitleMode=checkMode(CHECK_TITLE,$db);//
    if($checkTitleMode){
         //先发后审
        $succStatus=LIVE_TITLE_AUTO_PASS;
        $fileStatus=LIVE_TITLE_AUTO_UNPASS;
    }else{
        //先审后发
        $succStatus=LIVE_TITLE_PASS;
        $fileStatus=LIVE_TITLE_UNPASS;
    }
    if ($succluid) {
        $row = $db->field('liveid,title')->where("liveid in ($succluid)")->select('admin_live_title');
        if (!empty($row) && (false !== $row)) {
            $update = upWaitLiveTitle($succluid, $uid, $db); //更改admin_wait_user_nick表状态
            if ($update !== false) {
                $tostatic = $db->where("liveid in ($succluid) ")->update('admin_live_title', array('status' => $succStatus, 'utime' => date('Y-m-d H:i:s', time()))); //修改审核状态通过
                if ($tostatic !== false) {
                    if ($failluid) {
                        failLiveTitle($failluid, $uid, $db,$fileStatus); //处理未通过的
                    }
                    changeLiveTitle($row, $db); //更改live表
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
            $res = failLiveTitle($failluid, $uid, $db,$fileStatus); //前一步应该判断是先审后发还是先发后审
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
$succluid = isset($_POST['succList']) ? trim($_POST['succList']) : ''; //主播id列表批量可用逗号隔开(可通过的)
$failluid = isset($_POST['failedList']) ? trim($_POST['failedList']) : ''; //主播id列表批量可用逗号隔开(不合格的)
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

$res = UpdateLiveTitleStatus($uid, $succluid, $failluid, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}
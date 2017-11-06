<?php
/**
 * 获取待审核头像列表
 * yandong@6rooms.com
 * date 2016-10-12 15:22
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

/**
 * 同一审核者对同一用户只保留一条数据
 * @param string $uids  //用户id
 * @param int $adminid //审核者id
 * @param type $db
 * @return boolean
 */
function dellocalList($uids, $adminid, $db) {
    if (empty($uids) || empty($adminid)) {
        return false;
    }
    $res = $db->where("adminid=$adminid  and uid in ($uids)")->delete('admin_wait_user_pic');
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * 同步数据到admin_wait_user_pic表
 * @param string $uids //用户id
 * @param int $adminid //审核者id
 * @param type $db
 * @return boolean
 */
function localWaitList($uids, $adminid, $db) {
    $str='';
    if (empty($uids) || empty($adminid)) {
        return false;
    }
    foreach ($uids as $v) {
        $str .= "($adminid," . $v['uid'] . "),";
    }
    $str = rtrim($str, ',');
    $sql = "INSERT INTO admin_wait_user_pic (`adminid`,`uid`) values $str";
    $res = $db->query($sql);
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

/**
 * 检测是否有已经锁定的数据
 * @param int $adminid  管理员id
 * @param type $db
 * @return boolean
 */
function getIsLockPic($adminid, $db) {
    $res = $db->field("uid")->where("adminid=$adminid and status=0")->select('admin_wait_user_pic');
    if (!empty($res) && $res !== false) {
        return array_column($res, 'uid');
    } else {
        return false;
    }
}

/**
 * 检测其他管理员是否有已经锁定的数据
 * @param int $adminid  管理员id
 * @param type $db
 * @return boolean
 */
function getOtherLockPic($adminid, $db) {
    $res = $db->field("uid")->where("adminid!=$adminid and status=0")->select('admin_wait_user_pic');
    if (!empty($res) && $res !== false) {
        return array_column($res, 'uid');
    } else {
        return false;
    }
}



/**
 * 获取数据
 * @param int $uid  管理员id
 * @param int $page
 * @param int $size
 * @param int $status
 * @param type $conf
 * @param type $db
 * @return array()
 */
function getWaitList($uid, $page, $size, $status, $stime, $etime, $conf, $db) {
    $list = array();
    $where=1;
    if($stime){
        $where .=" and ctime >= '$stime'";
    }
    if($etime){
        $where .=" and ctime <= '$etime'";
    }
        if($status==0){
            $status='0,3';
        }
        if($status==2){
            $status='2,4';
        }
    $where .=" and status in ($status)";
    $count = $db->field('count(*) as total')->where("$where")->select('admin_user_pic');
    if (!empty($count) && isset($count[0]['total'])) {
        $count = $count[0]['total'];
    } else {
        $count = 0;
    }
    $page = Page($count, $size, $page);
    $lockList = getIsLockPic($uid, $db); //自己锁定的
    $otherlockList = getOtherLockPic($uid, $db); //其他审核者锁定的
    if ($lockList) {
        $lockuid = implode(',', $lockList);
        $myres = $db->field('uid,pic,ctime')->where("$where  and  uid in($lockuid)")->select('admin_user_pic');
        if (count($lockList) >= $size) {
            $res = $myres;
        } else {
            $size = $size - count($lockList);
            if ($otherlockList) {
                $otherlockuid = implode(',', $otherlockList);
            } else {
                $otherlockuid = array();
            }
            if ($otherlockuid) {
                $otherres = $db->field('uid,pic,ctime')->where("$where  and uid not in($otherlockuid) and uid not in($lockuid)")->limit($page, $size)->select('admin_user_pic');
                $res = array_merge($myres, $otherres);
            } else {
                $otherres = $db->field('uid,pic,ctime')->where("$where and uid not in($lockuid)")->limit($page, $size)->select('admin_user_pic');
                $res = array_merge($myres, $otherres);
            }
        }
    } else {
        if ($otherlockList) {
            $otherlockuid = implode(',', $otherlockList);
            $res = $db->field('uid,pic,ctime')->where("$where and  uid not in($otherlockuid)")->limit($page, $size)->select('admin_user_pic');
        } else {
            $res = $db->field('uid,pic,ctime')->where("$where")->limit($page, $size)->select('admin_user_pic');
        }
    }
    if (!empty($res) && $res !== false) {
        if($status){
            $uids = array_column($res, 'uid');
            $nicksAndPic =getUserNicks($uids, $db);
        }
        foreach ($res as $v) {
            $temp['uid'] = $v['uid'];
            $temp['pic'] = $v['pic'] ? "http://" . $conf['domain-avatar'] . $v['pic'] : '';
            if($status){
                $temp['nick']=$nicksAndPic[$v['uid']];
                $temp['ctime']=$v['ctime'];
                $temp['status']="$status";
            }
            array_push($list, $temp);
        }
        if($status == 0){
            if ($lockList) {
                $uids = implode(',', $lockList);
                dellocalList($uids, $uid, $db); //相同审核者对同一用户只保留一条数据
            }
            localWaitList($list, $uid, $db);//同步到admin_wait_user_pic
        }
        return array('data' => $list, 'total' => $count);
    } else {
        return array('data' => array(), 'total' => '0');
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
$size = isset($_POST['size']) ? (int) $_POST['size'] : 9;
$status = isset($_POST['status']) ? (int) $_POST['status'] : 0; //状态 待审核0,已通过1,未通过2
$stime=isset($_POST['stime']) ? trim($_POST['stime']) : 0;//开始时间
$etime=isset($_POST['etime']) ? trim($_POST['etime']) : 0;//结束时间


if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
if($status != 0 && in_array($uid,array(1,2,7,41))){
    succ(array());  //管理员不绑定审核数据，直接返回空数据
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = getWaitList($uid, $page, $size, $status, $stime, $etime, $conf, $db);
succ($res);

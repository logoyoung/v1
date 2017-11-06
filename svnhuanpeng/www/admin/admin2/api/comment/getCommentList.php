<?php

/**
 * 获取评论列表
 * yandong@6rooms.com
 * date 2016-11-04 10:59
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
function dellocalList($commentids, $adminid, $db) {
    if (empty($commentids) || empty($adminid)) {
        return false;
    }
    $res = $db->where("adminid=$adminid  and commentid in ($commentids)")->delete('admin_wait_video_comment');
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * 同步数据到admin_wait_user_nick表
 * @param string $uids //用户id
 * @param int $adminid //审核者id
 * @param type $db
 * @return boolean
 */
function localWaitList($commentids, $adminid, $db) {
    $str='';
    if (empty($commentids) || empty($adminid)) {
        return false;
    }
    foreach ($commentids as $v) {
        $str.="($adminid," . $v['commentid'] . "),";
    }
    $str = rtrim($str, ',');
    $sql = "INSERT INTO admin_wait_video_comment (`adminid`,`commentid`) values $str";
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
function getIsLockNick($adminid, $db) {
    $res = $db->field("commentid")->where("adminid=$adminid   and status=0")->select('admin_wait_video_comment');
    if (!empty($res) && $res !== false) {
        return array_column($res, 'commentid');
    } else {
        return false;
    }
}

function getOtherLockNick($adminid, $db) {
    $res = $db->field("commentid")->where("adminid !=$adminid  and  status=0")->select('admin_wait_video_comment');
    if (!empty($res) && $res !== false) {
        return array_column($res, 'commentid');
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
function getCommentList($uid, $page, $size, $status, $db,$search,$keyword,$stime,$etime) {
    $list = array();
    $where="1";
    if($search=='4'){
        $where .=" and  comment like '%$keyword%' ";
    }
    if($stime){
        $where .=" and ctime >= '$stime'";
    }
    if($etime){
        $where .=" and ctime <= '$etime'";
    }
    if($status==0){
        $where .=" and status in (0,3)";
    }
    if($status==1){
        $where .=" and status=$status";
    }
    if($status==2){
        $where .=" and status in (2,4)";
    }
    $count = $db->field('count(*) as total')->where("$where")->select('videocomment');
    if (!empty($count) && isset($count[0]['total'])) {
        $count = $count[0]['total'];
    } else {
        $count = 0;
    }
    $page = Page($count, $size, $page);
    $lockList = getIsLockNick($uid, $db); //自己锁定的
    $otherlockList = getOtherLockNick($uid, $db); //其他审核者锁定的
    if ($lockList) {
        $lockuid = implode(',', $lockList);
        $myres = $db->field('id,videoid,uid,comment,tm')->where(" $where  and  id in($lockuid)")->select('videocomment');
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
                $otherres = $db->field('id,videoid,uid,comment,tm')->where("$where and id not in($otherlockuid) and id not in($lockuid)")->limit($page, $size)->select('videocomment');
                $res = array_merge($myres, $otherres);
            } else {
                $otherres = $db->field('id,videoid,uid,comment,tm')->where("$where and id not in($lockuid)")->limit($page, $size)->select('videocomment');
                $res = array_merge($myres, $otherres);
            }
        }
    } else {
        if ($otherlockList) {
            $otherlockuid = implode(',', $otherlockList);
            $res = $db->field('id,videoid,uid,comment,tm')->where("$where  and  id not in($otherlockuid)")->limit($page, $size)->select('videocomment');
        } else {
            $res = $db->field('id,videoid,uid,comment,tm')->where("$where")->limit($page, $size)->select('videocomment');
        }

    }
    if (!empty($res) || $res !== false) {
        foreach ($res as $v) {
            $temp['commentid'] = $v['id'];
            $temp['comment'] = $v['comment'];
            $temp['uid'] = $v['uid'];
            $temp['nick'] = 'demo';
            $temp['ctime'] = $v['tm'];
            if(in_array($status,array(0,3))){
                $temp['status'] = '0';
            }
            if($status=='1'){
                $temp['status'] = '1';
            }
            if(in_array($status,array(2,4))){
                $temp['status'] = '2';
            }
            array_push($list, $temp);
        }
        if($status=='0'){//只有获取待审核的时候才同步表
            if ($lockList) {
                $uids = implode(',', $lockList);
                dellocalList($uids, $uid, $db); //相同审核者对同一用户只保留一条数据
            }
             localWaitList($list, $uid, $db); // 同步到admin_wait_video_comment表
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
$size = isset($_POST['size']) ? (int) $_POST['size'] : 10;
$status = isset($_POST['status']) ? (int) $_POST['status'] : '0'; //状态 待审核0,已通过1,未通过2
$search = isset($_POST['searchType']) ? (int) $_POST['searchType'] : 0; //搜索类型  4
$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : 0;//关键字
$stime=isset($_POST['stime']) ? trim($_POST['stime']) : 0;//开始时间
$etime=isset($_POST['etime']) ? trim($_POST['etime']) : 0;//结束时间
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
if(!is_numeric($type) || !is_numeric($uid)){
    error(-1023);
}
if($search==2){
    if(! is_numeric($keyword)){
        error(-1007);
    }
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = getCommentList($uid, $page, $size, $status, $db,$search,$keyword,$stime,$etime);
succ($res);

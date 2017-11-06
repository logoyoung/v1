<?php

/**
 * 获取待审核
 * yandong@6rooms.com
 * date 2016-10-18 16:00
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

/**
 * 同一审核者对同一用户只保留一条数据
 * @param string $uids //用户id
 * @param int $adminid //审核者id
 * @param type $db
 * @return boolean
 */
function dellocalList($uids, $adminid, $db)
{
    if (empty($uids) || empty($adminid)) {
        return false;
    }
    $res = $db->where("adminid=$adminid  and liveid in ($uids)")->delete('admin_wait_live_title');
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
function localWaitList($uids, $adminid, $db)
{
    $str = '';
    if (empty($uids) || empty($adminid)) {
        return false;
    }
    foreach ($uids as $v) {
        $str .= "($adminid," . $v['liveid'] . "),";
    }
    $str = rtrim($str, ',');
    $sql = "INSERT INTO admin_wait_live_title (`adminid`,`liveid`) values $str";
    $res = $db->query($sql);
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

/**
 * 检测是否有已经锁定的数据
 * @param int $adminid 管理员id
 * @param type $db
 * @return boolean
 */
function getIsLockTitle($adminid, $db)
{
    $res = $db->field("liveid")->where("adminid=$adminid   and status=0")->select('admin_wait_live_title');
    if (!empty($res) && $res !== false) {
        return array_column($res, 'liveid');
    } else {
        return false;
    }
}

/**
 * 检测其他人锁定的数据
 * @param $adminid  管理员id
 * @param $db
 * @return array|bool
 */
function getOtherLockTitle($adminid, $db)
{
    $res = $db->field("liveid")->where("adminid !=$adminid  and  status=0")->select('admin_wait_live_title');
    if (!empty($res) && $res !== false) {
        return array_column($res, 'liveid');
    } else {
        return false;
    }
}

/**获取数据
 * @param $uid  审核者id
 * @param $status  状态  0待审核 、1已通过 、2未通过
 * @param $search  搜索类型  1昵称 、2uid、3直播标题
 * @param $keyword 搜索关键字
 * @param $stime   开始时间
 * @param $etime   结束时间
 * @param $page   页数
 * @param $size   请求数量
 * @param $db
 * @return array
 */
function getLiveTitleList($uid, $status, $search, $keyword, $stime, $etime, $page, $size, $db)
{
    $list = array();
    $where = 1;
    if ($search == '1') {
        $where .= " and  nick like '%$keyword%' ";
    }
    if ($search == '2') {
        $where .= " and uid=$keyword ";
    }
    if ($search == '3') {
        $where .= " and title like '%$keyword%'";
    }
    if ($stime) {
        $where .= " and ctime >= '$stime'";
    }
    if ($etime) {
        $where .= " and ctime <= '$etime'";
    }
    if ($status == 1) {
        $status = '0,3';
    }
    if ($status == 2) {
        $status = '2,4';
    }
    $where .= " and status in ($status)";
    $count = $db->field('count(*) as total')->where("$where")->select('admin_live_title');
    if (!empty($count) && isset($count[0]['total'])) {
        $count = $count[0]['total'];
    } else {
        $count = 0;
    }
    $page = Page($count, $size, $page);
    if ($status == 0) {
        $lockList = getIsLockTitle($uid, $db); //自己锁定的
        $otherlockList = getOtherLockTitle($uid, $db); //其他审核者锁定的
        if ($lockList) {
            $locktitle = implode(',', $lockList);
            $myres = $db->field('liveid,title,nick,uid,ctime')->where("$where  and liveid in($locktitle)")->select('admin_live_title');
            if (count($lockList) >= $size) {
                $res = $myres;
            } else {
                $size = $size - count($lockList);
                if ($otherlockList) {
                    $otherlocktitle = implode(',', $otherlockList);
                } else {
                    $otherlocktitle = array();
                }
                if ($otherlocktitle) {
                    $otherres = $db->field('liveid,title,nick,uid,ctime')->where("$where  and liveid not in($otherlocktitle) and uid not in($locktitle)")->limit($page, $size)->select('admin_live_title');
                    $res = array_merge($myres, $otherres);
                } else {
                    $otherres = $db->field('liveid,title,nick,uid,ctime')->where("$where and liveid not in($locktitle)")->limit($page, $size)->select('admin_live_title');
                    $res = array_merge($myres, $otherres);
                }
            }
        } else {
            if ($otherlockList) {
                $otherlocktitle = implode(',', $otherlockList);
                $res = $db->field('liveid,title,nick,uid,ctime')->where("$where  and  liveid not in($otherlocktitle)")->limit($page, $size)->select('admin_live_title');
            } else {
                $res = $db->field('liveid,title,nick,uid,ctime')->where("$where")->limit($page, $size)->select('admin_live_title');
            }
        }
    } else {
        $res = $db->field('liveid,title,nick,uid,ctime')->where("$where")->limit($page, $size)->select('admin_live_title');
    }
    if ($res || $res !== false) {
        foreach ($res as $v) {
            $temp['liveid'] = $v['liveid'];
            $temp['title'] = $v['title'];
            $temp['nick'] = $v['nick'];
            $temp['uid'] = $v['uid'];
            $temp['ctime'] = $v['ctime'];
            if ($status == '0') {
                $temp['status'] = '0';
            }
            if ($status == '1') {
                $temp['status'] = '1';
            }
            if ($status == '2') {
                $temp['status'] = '2';
            }
            array_push($list, $temp);
        }
        if ($status == 0) {
            if ($lockList) {
                $uids = implode(',', $lockList);
                dellocalList($uids, $uid, $db); //相同审核者对同一用户只保留一条数据
            }
            localWaitList($list, $uid, $db); //在admin_user_nick 获取数据 同步到admin_wait_user_nick表
        }
        return array('data' => $list, 'total' => $count);
    } else {
        return array('data' => array(), 'total' => '0');
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$size = isset($_POST['size']) ? (int)$_POST['size'] : 10;
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
$search = isset($_POST['searchType']) ? (int)$_POST['searchType'] : 0; //搜索类型  1昵称,2uid,3直播标题
$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : 0;//昵称或uid
$stime = isset($_POST['stime']) ? trim($_POST['stime']) : 0;//开始时间
$etime = isset($_POST['etime']) ? trim($_POST['etime']) : 0;//结束时间

if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}

if ($search == 2) {
    if (!is_numeric($keyword)) {
        error(-1007);
    }
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = getLiveTitleList($uid, $status, $search, $keyword, $stime, $etime, $page, $size, $db);
succ($res);

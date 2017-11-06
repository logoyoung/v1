<?php

/**
 * 获取主播公告列表
 * yandong@6rooms.com
 * date 2016-11-31｀ 16:00
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

function lock($luids, $uid,$db)
{
    if (empty($luids)) {
        return false;
    }
    $res = $db->where("luid in ($luids)")->update('admin_livebulletin', array('adminid'=>$uid,'status' => 3));
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
function getIsLockNotice($adminid, $db)
{
    $res = $db->field("luid")->where("adminid=$adminid and status=3")->select('admin_livebulletin');
    if (!empty($res) && $res !== false) {
        return array_column($res, 'luid');
    } else {
        return false;
    }
}

function getOtherLockNotice($adminid, $db)
{
    $res = $db->field("luid")->where("adminid !=$adminid and status=3")->select('admin_livebulletin');
    if (!empty($res) && $res !== false) {
        return array_column($res, 'luid');
    } else {
        return false;
    }
}

function getNoticeInfo($uids, $db)
{
    if (empty($uids)) {
        return false;
    }
    $res = $db->field('luid,bulletin')->where("luid in ($uids)")->select('livebulletin');
    if (false !== $res) {
        if (empty($res)) {
            return array();
        } else {
            foreach ($res as $v) {
                $temp[$v['luid']] = $v['bulletin'];
            }
            return $temp;
        }
    } else {
        return false;
    }
}


/**获取数据
 * @param $uid
 * @param $page
 * @param $size
 * @param $status
 * @param $conf
 * @param $db
 * @param $stime
 * @param $etime
 * @return array
 */
function getNoticeList($uid, $page, $size, $status, $conf, $db)
{
    $list = array();
    $where = " status=$status";

    $count = $db->field('count(*) as total')->where("$where")->select('admin_livebulletin');
    if (!empty($count) && isset($count[0]['total'])) {
        $count = $count[0]['total'];
    } else {
        $count = 0;
    }
    $page = Page($count, $size, $page);
    $lockList = getIsLockNotice($uid, $db); //自己锁定的
    $otherlockList = getOtherLockNotice($uid, $db); //其他审核者锁定的
    if ($lockList) {
        $lockuid = implode(',', $lockList);
        $myres = $db->field('luid,ctime,status')->where("luid in($lockuid)")->select('admin_livebulletin');
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
                $otherres = $db->field('luid,ctime,status')->where("luid not in($otherlockuid) and luid not in($lockuid)")->limit($page, $size)->select('admin_livebulletin');
                $res = array_merge($myres, $otherres);
            } else {
                $otherres = $db->field('luid,ctime,status')->where("$where and luid not in($lockuid)")->limit($page, $size)->select('admin_livebulletin');
                $res = array_merge($myres, $otherres);
            }
            $count=$count - count($otherres);
        }
    } else {
        if ($otherlockList) {
            $otherlockuid = implode(',', $otherlockList);
            $res = $db->field('luid,ctime,status')->where("luid not in($otherlockuid)")->limit($page, $size)->select('admin_livebulletin');
        } else {
            $res = $db->field('luid,ctime,status')->where("$where")->limit($page, $size)->select('admin_livebulletin');
        }

    }
    if ($res) {
        foreach ($res as $v) {
            $temp[$v['luid']] = $v;
        }
        $listUids = array_keys($temp);
        $userInfo = getUserNicks($listUids, $db);
        $infoList = getNoticeInfo(implode(',', $listUids), $db);
        for ($i = 0, $k = count($listUids); $i < $k; $i++) {
            $tmp['uid'] = $listUids[$i];
            $tmp['nick'] = $userInfo[$listUids[$i]];
            $tmp['ctime'] = $temp[$listUids[$i]]['ctime'];
            $tmp['content'] = isset($infoList[$listUids[$i]]) ? $infoList[$listUids[$i]] : '';
            $tmp['status'] = $status;
            array_push($list, $tmp);
        }
        if ($status == '0') {//只有获取待审核的时候s锁定数据
            lock(implode(',', $listUids), $uid,$db);//锁定
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
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0; //状态 待审核0,已通过1,未通过2
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = getNoticeList($uid, $page, $size, $status, $conf, $db);
succ($res);

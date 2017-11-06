<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
function getAdvertisementList($adtype, $location, $status, $stime, $etime, $page, $size, $db)
{
    $where = 1;
    if (in_array($adtype, array(0, 1))) {
        $where .= " and type=$adtype ";
    }
    if (in_array($location, array(0, 1))) {
        $where .= " and location =$location";
    }
    if (in_array($status, array(0, 1, 2))) {
        $where .= " and status=$status";
    }
    if ($stime) {
        $where .= " and ctime >='$stime'";
    }
    if ($etime) {
        $where .= " and ctime <='$etime'";
    }
    $num = $db->field('count(*) as num')->where("$where")->select('admin_advertisement');
    $count = $num[0]['num'] ? $num[0]['num'] : 0;
    $page = Page($count, $size, $page);
    $res = $db->field('id,type,location,url,poster,ctime,luid,click,status')->where("$where")->limit($page, $size)->select('admin_advertisement');
    if ($res !== false) {
        if (empty($res)) {
            return array();
        } else {
            return array('res'=>$res,'total'=>$count);
        }
    } else {
        return false;
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$adtype = isset($_POST['adtype']) ? (int)($_POST['adtype']) : 0;
$location = isset($_POST['location']) ? (int)($_POST['location']) : 0;
$status = isset($_POST['status']) ? (int)($_POST['status']) : 0;
$stime = isset($_POST['stime']) ? trim($_POST['stime']) : 0;
$etime = isset($_POST['etime']) ? (int)($_POST['etime']) : 0;
$page = isset($_POST['page']) ? (int)($_POST['page']) : 1;
$size = isset($_POST['size']) ? (int)($_POST['size']) : 10;
if (empty($uid) || empty($encpass)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = getAdvertisementList($adtype, $location, $status, $stime, $etime, $page, $size, $db);
if (false !== $res) {
    if (!empty($res)) {
        $list = array();
        foreach ($res['res'] as $v) {
            $temp['id'] = $v['id'];
            $temp['adtype'] = $v['type'];
            $temp['location'] = $v['location'];
            $temp['url'] = $v['url'];
            $temp['poster'] = $v['poster'] ? "http://" . $conf['domain-img'] . '/' . $v['poster'] : '';
            $temp['ctime'] = $v['id'];
            $temp['luid'] = $v['luid'];
            $temp['click'] = $v['click'];
            $temp['status'] = $v['status'];
            array_push($list, $temp);
        }
        succ(array('list' => $list,'total'=>$res['total']));
    } else {
        succ(array('list' => array(),'total'=>0));
    }
} else {
    error(-1014);
}




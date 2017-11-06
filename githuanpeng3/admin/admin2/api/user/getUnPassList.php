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

function getUnPass($page, $size,$conf,$db) {
    $list = array();
    $count = $db->field('count(*) as total')->where("status in (2,4)")->select('admin_user_pic');
    if (!empty($count) && isset($count[0]['total'])) {
        $count = $count[0]['total'];
    } else {
        $count = 0;
    }
    if ($count) {
        $page = Page($count, $size, $page);
        $res = $db->field('uid,pic,status')->where(" status in (2,4)")->limit($page, $size)->select('admin_user_pic');
        if ($res !== false) {
            foreach ($res as $v) {
                $temp['uid'] = $v['uid'];
                $temp['pic'] = $v['pic'] ? "http://" . $conf['domain-avatar'] . '/' . $v['pic'] : '';
                $temp['status'] = $v['status'];
                array_push($list, $temp);
            }
            return array('data' => $list, 'total' => "$count");
        } else {
            return array('data' => array(), 'total' => "$count");
        }
    } else {
        return array('data' => array(), 'total' => "$count");
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

if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = getUnPass($page, $size,$conf,$db);
succ($res);

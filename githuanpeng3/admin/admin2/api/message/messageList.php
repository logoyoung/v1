<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
/**
 * 站类信列表
 * @author yandong@6room.com
 * date 2016-07-12  11:14
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_admin();

function getMessageList($keyword, $type, $page, $size, $db)
{
    $list = array();
    if ($keyword) {
        $total = $db->field("count(*) as total")->where("type=$type  and  title like '%$keyword%'  or msg like '%$keyword%'")->order("stime DESC")->select("sysmessage");
        $res = $db->where("type=$type  and  title like '%$keyword%'  or msg like '%$keyword%'")->limit($page, $size)->order("stime DESC")->select("sysmessage");
    } else {
        $total = $db->field("count(*) as total")->where("type=$type")->select('sysmessage');
        $res = $db->where("type=$type")->limit($page, $size)->order("stime DESC")->select("sysmessage");
    }

    if ($res !== false) {
        foreach ($res as $v) {
            $temp['id'] = $v['id'];
            $temp['title'] = $v['title'];
            $temp['msg'] = strCut($v['msg'], 20, $suffix = true);
            $temp['stime'] = $v['stime'];
            array_push($list, $temp);
        }
    }
    return array('list' => $list, 'total' => $total ? $total[0]['total'] : '0');
}

$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim(($_POST['encpass'])) : '';
$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$size = isset($_POST['size']) ? (int)$_POST['size'] : 10;
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
if (!is_numeric($type) || !is_numeric($uid)) {
    error(-1023);
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = getMessageList($keyword, 2, $page, $size, $db);
succ($res);






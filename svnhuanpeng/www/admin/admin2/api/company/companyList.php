<?php

/**
 * 获取经纪公司列表
 * yandong@6rooms.com
 * date 2017-01-22 16:43
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();

/**
 * 获取数据
 * @param obj $db
 * @return array()
 */
function getCompanyList($db)
{
    $res = $db->field('id,name,type,status,rate')->select('company');
    if (false !== $res && !empty($res)) {
        $list = array();
        foreach ($res as $v) {
            $temp['id'] = $v['id'];
            $temp['name'] = $v['name'];
            $temp['type'] = $v['type'];
			$temp['rate'] = $v['rate'];
            $temp['status'] = $v['status'];
            array_push($list, $temp);
        }
        return array('list' => $list);
    } else {
        return array('list' => array());
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;

//if (empty($uid) || empty($encpass) || empty($type)) {
//    error(-1007);
//}
//
//$adminHelp = new AdminHelp($uid, $type);
//$err = $adminHelp->loginError($encpass);
//if ($err) {
//    error($err);
//}


$res = getCompanyList($db);
if ($res) {
    succ($res);
} else {
    error(-1014);
}

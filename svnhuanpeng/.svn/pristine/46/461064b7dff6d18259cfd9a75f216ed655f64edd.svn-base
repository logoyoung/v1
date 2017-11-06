<?php

/**
 * 获取直播标题待审核、已通过、未通过数量
 * yandong@6rooms.com
 * date 2016-11-01 11:25
 * 
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

function  getLiveTitlenumber($db){
    $waitPass = $pass = $unPass = '0';
    $res = $db->field('status, count(*) as total ')->where('1=1 group by  status')->select('admin_live_title');
    if ($res) {
        for ($i = 0, $k = count($res); $i < $k; $i++) {
            if ($res[$i]['status'] == '0') {
                $waitPass +=$res[$i]['total'];
            }
            if ($res[$i]['status'] == '1') {
                $pass +=$res[$i]['total'];
            }
            if ($res[$i]['status'] == '2') {
                $unPass +=$res[$i]['total'];
            }
            if ($res[$i]['status'] == '3') {
                $pass +=$res[$i]['total'];
            }
            if ($res[$i]['status'] == '4') {
                $unPass +=$res[$i]['total'];
            }
        }
        return array('waitPass' => "$waitPass", 'pass' => "$pass", 'unPass' => "$unPass");
    } else {
        return array('waitPass' => "0", 'pass' => "0", 'unPass' => "0");
    }

    return $res;

}





/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;

if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = getLiveTitlenumber($db);
if ($res) {
    succ($res);
} else {
    error(-1014);
}
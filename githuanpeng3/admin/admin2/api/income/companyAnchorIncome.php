<?php

/**
 * 获取经纪公司列表
 * yandong@6rooms.com
 * date 2017-01-22 16:43
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
require '../../includeAdmin/Anchor.class.php';
$db = new DBHelperi_admin();


function checkCompanyIsExist($cid, $db)
{
    if (empty($cid)) {
        return false;
    }
    $res = $db->where("id=$cid and status=0")->limit(1)->select('company');
    if (false !== $res && !empty($res)) {
        return true;
    } else {
        return false;
    }

}


function companyAnchorIncome($cid, $month, $incomeType, $order, $page, $size, $db)
{
    $list = array();
    $data = getCompanyAnchorInfo($cid, $page, $size, $db);
    if ($data['res']) {
        $uids = array_column($data['res'], 'uid');
        $anchorobj = new Anchor();
        $anchorInfo = $anchorobj->anchorInfo($uids);
        $roomids = $anchorobj->anchorRoomID($uids);
        $length = $anchorobj->anchorLiveLength($uids, $month);
        $popular = $anchorobj->anchorPopular($uids, $month);
        foreach ($data['res'] as $v) {
            $temp['uid'] = $v['uid'];
            $temp['nick'] = array_key_exists($v['uid'], $anchorInfo) ? $anchorInfo[$v['uid']]['nick'] : 0;
            $temp['bean'] = $v['bean'];
            $temp['coin'] = $v['coin'];
            if ($anchorInfo[$v['uid']]['pic']) {
                $temp['pic'] = array_key_exists($v['uid'], $anchorInfo) ? 'http://img.huanpeng.com' . $anchorInfo[$v['uid']]['pic'] : 0;
            } else {
                $temp['pic'] = '';
            }
            if (array_key_exists($v['uid'], $length)) {
                $temp['length'] = SecondFormat($length[$v['uid']]['length']);
            } else {
                $temp['length'] = 0;
            }
            $temp['roomID'] = array_key_exists($v['uid'], $roomids) ? $roomids[$v['uid']]['roomid'] : 0;
            $temp['popularoty'] = array_key_exists($v['uid'], $popular) ? $popular[$v['uid']]['popular'] : 0;
            array_push($list, $temp);
        }
        return array('list' => $list, 'total' => $data['total']);
    } else {
        return array('list' => array(), 'total' => 0);
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
$cid = isset($_POST['cid']) ? (int)$_POST['cid'] : 0;
$month = isset($_POST['month']) ? trim($_POST['month']) : 0;
$incomeType = isset($_POST['incomeType']) ? (int)$_POST['incomeType'] : 0;
$order = isset($_POST['order']) ? (int)$_POST['order'] : 0;

if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
if (empty($cid)) {
    error(-1007);
}
if (!in_array($incomeType, array(0, 1))) {
    error(-1007);
}
$checkisExist = checkCompanyIsExist($cid, $db);
if (!$checkisExist) {
    error(-1007);
}

$result = companyAnchorIncome($cid, $month, $incomeType, $order, $page, $size, $db);
succ($result);

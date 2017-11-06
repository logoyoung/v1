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

function getCompangInfo($cids, $db)
{
    if (is_array($cids)) {
        $cids = implode(',', $cids);
    }
    $res = $db->field('id,name')->where("id in ($cids)")->select('company');
    if (false !== $res) {
        if (!empty($res)) {
            foreach ($res as $v) {
                $list[$v['id']] = $v;
            }
            return $list;
        } else {
            return array();
        }
    } else {
        return false;
    }

}

function getCompangPeople($cids, $db)
{
    if (is_array($cids)) {
        $cids = implode(',', $cids);
    }
    $res = $db->field('cid,count(*) as  total')->where("cid in ($cids) group by cid")->select('company_anchor');
    if (false !== $res) {
        if (!empty($res)) {
            foreach ($res as $v) {
                $list[$v['cid']] = $v;
            }
            return $list;
        } else {
            return array();
        }
    } else {
        return false;
    }
}

function get_company($db)
{
    $res = $db->field('id')->select('company');
    if (false !== $res) {
        return array('res' => $res, 'total' => count($res));
    } else {
        return array('res' => array(), 'total' => 0);
    }
}

/**
 * 获取数据
 * @param obj $db
 * @return array()
 */
function Company_Income($month, $incomeType, $order, $page, $size, $db)
{
    $data = get_company($db);
    if ($data['res']) {
        $cids = array_column($data['res'], 'id');
        $info = getCompangInfo($cids, $db);
        $people = getCompangPeople($cids, $db);
        $list = array();
        foreach ($data['res'] as $v) {
            $temp['id'] = $v['id'];
            $temp['coin'] = $v['coin'] ? $v['coin'] : 0;
            $temp['bean'] = $v['bean'] ? $v['bean'] : 0;
            $temp['crmb'] = $v['crmb'] ? $v['crmb'] : 0;
            $temp['brmb'] = $v['brmb'] ? $v['brmb'] : 0;
            $temp['people'] = array_key_exists($v['id'], $people) ? $people[$v['id']]['total'] : 0;
            $temp['name'] = array_key_exists($v['id'], $info) ? $info[$v['id']]['name'] : 0;
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
$month = isset($_POST['month']) ? trim($_POST['month']) : '';
$incomeType = isset($_POST['incomeType']) ? trim($_POST['incomeType']) : 0;
$order = isset($_POST['order']) ? (int)$_POST['order'] : 0;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$size = isset($_POST['size']) ? (int)$_POST['size'] : 10;

//if (empty($uid) || empty($encpass) || empty($type)) {
//    error(-1007);
//}
//
//$adminHelp = new AdminHelp($uid, $type);
//$err = $adminHelp->loginError($encpass);
//if ($err) {
//    error($err);
//}


$res = Company_Income($month, $incomeType, $order, $page, $size, $db);
if ($res) {
    succ($res);
} else {
    error(-1014);
}

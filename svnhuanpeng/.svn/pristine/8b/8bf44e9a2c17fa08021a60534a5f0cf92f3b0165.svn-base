<?php
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
/**
 * 主播列表
 * @author yandong@6room.com
 * date 2016-06-15  11:11
 */
ini_set('display_errors', 'On');
error_reporting(E_ALL);
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_admin();

function getAnchorList($keyword, $nickname, $type, $page, $size, $db)
{
    if ($type == 0) {
        $where = RN_WAIT;
        $order = "ctime DESC";
    }
    if ($type == 1) {
        $where = RN_PASS;
        $order = "ctime DESC";
    }
    if ($type == 2) {
        $where = RN_UNPASS;
        $order = "passtime DESC";
    }
    if ($keyword) {
        $where .= " and name like '%$keyword%'";
    }
    if($nickname) {
        $where .= " and uid in(select uid from userstatic where nick like '%$nickname%')";
    }
    $res = $db->where("status=$where")->limit($page, $size)->order("$order")->select('userrealname');
    $count = $db->field('count(*) as total')->where("status=$where ")->select('userrealname');
    if (false !== $res && !empty($res)) {
        return array('res' => $res, 'total' => $count[0]['total']);
    } else {
        return array('res' => array(), 'total' => 0);
    }
}

function getList($keyword, $nickname, $type, $page, $size, $conf, $db)
{
    $res = getAnchorList($keyword, $nickname, $type, $page, $size, $db);
    if ($res['res']) {
        $userInfo = getUserInfo(array_column($res['res'], 'uid'), $db);
        if ($res) {
            $list = array();
            $url = "http://" . $conf['domain-avatar'] . '/';
            foreach ($res['res'] as $v) {
                $temp['uid'] = $v['uid'];
                $temp['name'] = $v['name'];
                $temp['nick'] = array_key_exists($v['uid'], $userInfo) ? $userInfo[$v['uid']]['nick'] : '';
                $temp['mobile'] = array_key_exists($v['uid'], $userInfo) ? $userInfo[$v['uid']]['phone'] : '';
                $temp['pid'] = $v['papersid'];
                $temp['ptime'] = $v['papersetime'];
                $temp['face'] = $v['face'] ? $url . $v['face'] : '';
                $temp['back'] = $v['back'] ? $url . $v['back'] : '';
                $temp['hand'] = $v['handheldPhoto'] ? $url . $v['handheldPhoto'] : '';
                $temp['passtime'] = $v['passtime'];
                $temp['ctime'] = $v['ctime'];
                if ($v['status'] == 1) {
                    $temp['status'] = '待审核';
                }
                if ($v['status'] == 100) {
                    $temp['status'] = '未通过';
                }
                if ($v['status'] == 101) {
                    $temp['status'] = '已通过';
                }

                array_push($list, $temp);
            }
            $lists = array('data' => $list, 'total' => $res['total']);
        } else {
            $lists = array('data' => array(), 'total' => '0');
        }
        return $lists;
    } else {
        return array('data' => array(), 'total' => '0');
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$utype = isset($_POST['utype']) ? (int)($_POST['utype']) : 1;//管理员类型
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$size = isset($_POST['size']) ? (int)($_POST['size']) : 10;
$type = isset($_POST['type']) ? (int)($_POST['type']) : 0; //0 待审核 ,1 审核通过,2 审核未通过
$keyword = isset($_POST['keyword']) ? trim(($_POST['keyword'])) : '';
$nickname = isset($_POST['nickname']) ? trim(($_POST['nickname'])) : '';
$page = checkInt($page);
$size = checkInt($size);
if (!in_array($type, array(0, 1, 2))) {
    error(-1005);
}
if (!is_numeric($type)) {
    error(-1023);
}
if (empty($uid) || empty($encpass)) {
    error(-1005);
}
$adminHelp = new AdminHelp($uid, $utype);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = getList($keyword, $nickname, $type, $page, $size, $conf, $db);
exit(json_encode($res));

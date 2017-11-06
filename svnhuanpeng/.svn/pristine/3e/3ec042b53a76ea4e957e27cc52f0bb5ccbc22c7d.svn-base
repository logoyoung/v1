<?php
/**
 * 获取禁言列表
 * jiantao@6.cn
 * date 2016-10-12 13:55
 * 
 */
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();

/**
 * 读取禁言列表
 * @param type $db
 * @param int $page  管理员id
 * @return array
 */
function getUser($db, $page, $size)
{
    $where = '';
    if($luid = isset($_POST['luid']) ? (int) $_POST['luid'] : 0) {
        $where .= ' and uid=' . $luid;
    }
    if($nickname = isset($_POST['nickname']) ? $_POST['nickname'] : '') {
        $where .= ' and nick like "%' . $nickname . '%"';
    }
    
    $sql = 'select uid,nick,pic,password from userstatic 
            where 1 ' . $where . ' 
            order by uid desc 
            limit ' . ($page-1)*$size . ',' . $size;
    $data['list'] = $db->doSql($sql);
    if($data['list']) {
        foreach($data['list'] as $k=>$v) {
            $data['list'][$k]['pic'] = ($v['pic']) ? ('http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain-avatar'] . $v['pic']) : DEFAULT_PIC;
            $data['list'][$k]['isBlocked'] = $v['password'] == '' ? 1 : 0;
        }
    }
    
    $sql_count = 'SELECT count(*) as total from userstatic where 1 ' . $where;
    $res_count = $db->doSql($sql_count);
    $data['total'] = $res_count[0]['total'];
    
    return $data;
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

$res = getUser($db, $page, $size);
if ($res) {
    succ($res);
} else {
    error(-1014);
}

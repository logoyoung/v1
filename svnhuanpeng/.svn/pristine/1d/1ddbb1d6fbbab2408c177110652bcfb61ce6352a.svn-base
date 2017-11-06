<?php

require '../../../includeAdmin/init.php';
require '../../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();

/**获取资讯详情
 * @param $tid
 * @param $db
 * @return bool
 */
function getInforMation($id, $db)
{
    $field = 'id,tid,title,content,poster,status,ctime,click,isrecommend';
    $res = $db->field($field)->where("id=$id")->select('admin_information');
    if ($res !== false && !empty($res)) {
        return $res;
    } else {
        return false;
    }
}

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$id = isset($_POST['id']) ? trim($_POST['id']) : '';
if (empty($uid) || empty($encpass) || empty($id)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = getInforMation($id, $db);
if ($res) {
    foreach ($res as $v) {
        $list['id'] = $v['id'];
        $list['tid'] = $v['tid'];
        $list['id'] = $v['id'];
        $list['title'] = $v['title'];
        $list['poster'] = $v['poster'] ? "http://" . $conf['domain-img'] . '/' . $v['poster'] : '';
        $list['content'] = $v['content'];
        $list['ctime'] = $v['ctime'];
        $list['status'] = $v['status'];
        $list['click'] = $v['click'];
        $list['isRecommend'] = $v['isrecommend'];
    }
    succ(array('list' => $list));
} else {
    succ(array('list' => array()));
}




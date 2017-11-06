<?php

require '../../../includeAdmin/init.php';
require '../../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 修改资讯
 * @author yandong@6room.com
 * date 2016-11-23  20:08
 */

/**修改资讯
 * @param $id 资讯ID、
 * @param $itype 1焦点 2列表
 * @param $title  标题
 * @param $poster  封面
 * @param $url  链接
 * @param $db
 * @return bool
 */
function updateInforMation($id, $itype, $title, $poster, $url, $db)
{
    if ($itype == 1) {
        $data = array(
            'title' => $title,
            'poster' => $poster,
            'url' => $url
        );
    }
    if ($itype == 2) {
        $data = array(
            'title' => $title,
            'url' => $url
        );
    }

    $res = $db->where("id=$id")->update('admin_information', $data);
    if ($res !== false) {
        return true;
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
$itype = isset($_POST['itype']) ? trim($_POST['itype']) : '';
$id = isset($_POST['id']) ? trim($_POST['id']) : '';
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$poster = isset($_POST['poster']) ? trim($_POST['poster']) : '';
$url = isset($_POST['url']) ? trim($_POST['url']) : '';

if (empty($uid) || empty($encpass)) {
    error(-1006);
}
if (empty($id)) {
    error(-1007);
}

if (!in_array($itype, array(0, 1))) {
    error(-1023);
}
if ($itype == 1) {
    if (empty($poster) || empty($url)) {
        error(-1030);
    }
}
if ($itype == 2) {
    if (empty($title) || empty($url)) {
        error(-1030);
    }
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$title = filterWords($title);
$poster = filterWords($poster);
$url = filterWords($url);
$res = updateInforMation($id, $itype, $title, $poster, $url, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}



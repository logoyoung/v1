<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
/**
 * 实名认证审核
 * @author yandong@6room.com
 * date 2016-06-15  11:11
 */
$db = new DBHelperi_admin();

function passAnchor($id, $type, $db) {
    if (empty($id)) {
        return false;
    }
    if ($type == 1) {
        $status = RN_PASS;//通过
    }
    if ($type == 2) {
        $status = RN_UNPASS;//驳回
    }
    $data = array(
        'status' => $status,
        'passtime'=>date('Y-m-d h:i:s',time())
    );
    $res = $db->where('id=' . $id . '')->update('userrealname', $data);
    if ($res !== false) {
        $succ = array('isSuccess' => '1');
    } else {
        $succ = array('isSuccess' => '0');
    }
    return $succ;
}

$id = isset($_POST['id']) ? (int) ($_POST['id']) : '';
$type = isset($_POST['type']) ? (int)($_POST['type']) : '';
if (empty($id) || !in_array($type, array(1, 2))) {
    error(-4013);
}
$res = passAnchor($id, $type, $db);
exit(json_encode($res));

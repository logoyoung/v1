<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();

/**add
 * @param $uid
 * @param $adtype
 * @param $location
 * @param $url
 * @param $poster
 * @param $luid
 * @param $status
 * @param $db
 * @return bool
 */
function addAdvertisement($uid, $adtype,$location,$url,$poster,$luid, $status, $db)
{
    $data = array(
        'type'=>$adtype,
        'location'=>$location,
        'url'=>$url,
        'poster'=>$poster,
        'luid'=>$luid,
        'adminid' => $uid,
        'status' => $status
    );
    $res = $db->insert('admin_advertisement', $data);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$id = isset($_POST['id']) ? (int)($_POST['id']) : 0;
$adtype = isset($_POST['adtype']) ? (int)($_POST['adtype']) : 0;
$location = isset($_POST['location']) ? (int)($_POST['location']) : 0;
$url = isset($_POST['url']) ? trim($_POST['url']) : '';
$poster = isset($_POST['poster']) ? trim($_POST['poster']) : '';
$luid = isset($_POST['luid']) ? (int)($_POST['luid']) : 0;
$status = isset($_POST['status']) ? (int)($_POST['status']) : 0;
if (empty($uid) || empty($encpass)) {
    error(-1005);
}

if (!in_array($adtype, array(0, 1))) {
    error(-1027);
}
if (!in_array($location, array(0, 1))) {
    error(-1028);
}
if(empty($url)){
    error(-1007);
}
if (empty($poster)) {
    error(-1029);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$url=filterData($url);
$poster=filterData($poster);
$res = addAdvertisement($uid, $adtype,$location,$url,$poster,$luid, $status, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}
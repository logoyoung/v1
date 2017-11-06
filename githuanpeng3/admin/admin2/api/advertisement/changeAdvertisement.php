<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();

/**发布｜关闭｜删除 广告
 * @param $id
 * @param $adtype
 * @param $location
 * @param $luid
 * @param $status
 * @param $db
 * @return bool
 */
function addRecommendAd($id, $adtype, $location, $luid, $status, $db)
{
    $data = array(
        'aid'=>$id,
        'type'=>$adtype,
        'location'=>$location,
        'luid'=>$luid,
        'status' => $status
    );
    $res = $db->insert('recommend_advertisement', $data);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * @param $id
 * @param $status
 * @param $db
 * @return bool
 */
function updateRecommendStatus($id, $status, $db)
{
    if (empty($id)) {
        return false;
    }
    $res = $db->where("aid=$id")->update('recommend_advertisement', array('status' => $status));
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

function updateAdStatus($id, $status, $db)
{
    if (empty($id)) {
        return false;
    }
    $res = $db->where("id=$id")->update('admin_advertisement', array('status' => $status));
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

function getRecomAdCount($db)
{
    $res = $db->field('count(*)as num')->where('status=1  and location=0')->select('recommend_advertisement');
    if (false !== $res) {
        return $res[0]['num'];
    } else {
        return false;
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$id = isset($_POST['id']) ? (int)($_POST['id']) : 0;
$status = isset($_POST['status']) ? (int)($_POST['status']) : 0;
$adtype = isset($_POST['adtype']) ? (int)($_POST['adtype']) : 0;
$isrecommend = isset($_POST['isRecommend']) ? (int)($_POST['isRecommend']) : 0;
$location = isset($_POST['location']) ? (int)($_POST['location']) : 0;
$luid = isset($_POST['luid']) ? (int)($_POST['luid']) : '';

if (empty($uid) || empty($encpass)) {
    error(-1005);
}
if (empty($id)) {
    error(-1007);
}
if (!in_array($status, array(0, 1, 2))) {
    error(-1023);
}
if (!in_array($adtype, array(0, 1))) {
    error(-1027);
}
if (!in_array($location, array(0, 1))) {
    error(-1028);
}
if (!in_array($isrecommend, array(0, 1))) {
    error(-1023);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
if ($status == 1) {//public
    if ($isrecommend) {//tuijian
        $count = getRecomAdCount($db);
        if ($count >= ADVERTISEMENT_RECOMMENT_NUMBER) {
            error(-1020);
        }
        $res = addRecommendAd($id, $adtype, $location, $luid, $status, $db);
        if (false !== $res) {
            $adresult = updateAdStatus($id, $status, $db);
            if (false !== $adresult) {
                succ();
            } else {
                error(-1014);
            }
        } else {
            error(-1014);
        }
    } else {
        $adresult = updateAdStatus($id, $status, $db);
        if (false !== $adresult) {
            succ();
        } else {
            error(-1014);
        }
    }
} else {//delete  or  cancel
    $result = updateRecommendStatus($id, $status, $db);
    $adresult = updateAdStatus($id, $status, $db);
    if ($result && $adresult) {
        succ();
    } else {
        error(-1014);
    }
}







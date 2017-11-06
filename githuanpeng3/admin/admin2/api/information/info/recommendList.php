<?php

require '../../../includeAdmin/init.php';
require '../../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
/**获取资讯详情
 * @param $tid
 * @param $db
 * @return bool
 */

function getInfor($ids, $db)
{
    if (empty($ids)) {
        return false;
    }
    $field = 'id,tid,title,poster,status,type,url,ctime';
    $res = $db->field($field)->where("id in ($ids)")->select('admin_information');
    if ($res !== false) {
        if ($res) {
            foreach ($res as $v) {
                $temp[$v['id']] = $v;
            }
            return $temp;
        } else {
            return array();
        }
    } else {
        return false;
    }
}

function getRecommendInfor($id, $db)
{
    if (empty($id)) {
        return false;
    }
    $res = $db->field('list')->where("id=$id")->select('recommend_information');
    if (false !== $res) {
        if (isset($res[0]['list'])) {
            return $res[0]['list'];
        } else {
            return array();
        }
    } else {
        return false;
    }
}

function getInfoType($tids, $db)
{
    if (empty($tids)) {
        return false;
    }
    $res = $db->field('id,name')->where("id in ($tids)")->select('admin_information_type');
    if (false !== $res) {
        if ($res) {
            foreach ($res as $v) {
                $temp[$v['id']] = $v['name'];
            }
            return $temp;
        } else {
            return array();
        }
    } else {
        return false;
    }
}


$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$id = isset($_POST['id']) ? trim($_POST['id']) : '1';
if (empty($uid) || empty($encpass)) {
    error(-1007);
}
if (empty($id)) {
    error(-1026);
}
if (!in_array($id, array(1, 2))) {
    error(-1023);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = getRecommendInfor($id, $db);
if ($res) {
    $infos = getInfor($res, $db);
    $typename = getInfoType(implode(',', array_column($infos, 'tid')), $db);
    $rc = explode(',', $res);
    $list = array();
    for ($i = 0, $k = count($rc); $i < $k; $i++) {
        $temp['id'] = $infos[$rc[$i]]['id'];
        $temp['tid'] = $infos[$rc[$i]]['tid'];
        $temp['tname'] = isset($typename[$temp['tid']]) ? $typename[$temp['tid']] : '';
        $temp['title'] = $infos[$rc[$i]]['title'];
        $temp['poster'] = $infos[$rc[$i]]['poster'] ? "http://" . $conf['domain-img'] . '/' . $infos[$rc[$i]]['poster'] : '';
        $temp['ctime'] = $infos[$rc[$i]]['ctime'];
        $temp['status'] = $infos[$rc[$i]]['status'];
        $temp['url'] = $infos[$rc[$i]]['url'];
        $temp['type'] = $infos[$rc[$i]]['type'];
        array_push($list, $temp);
    }
    succ(array('list' => $list));
} else {
    succ(array('list' => array()));
}




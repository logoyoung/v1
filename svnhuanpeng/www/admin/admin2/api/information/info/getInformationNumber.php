<?php

require '../../../includeAdmin/init.php';
require '../../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 删除、发布、取消发布资讯
 * @author yandong@6room.com
 * date 2016-11-24  14:23
 */

/**获取待发布，已发布，已删除数量
 * @param $status
 * @param $db
 * @return bool
 */
function getInforMationNumber($tid, $status, $db)
{
    if ($tid) {
        $res = $db->field('status,count(*) as num')->where("tid=$tid  group by status")->select('admin_information');
    } else {
        $res = $db->field('tid,status,count(*) as num')->where("status=$status group by tid")->select('admin_information');
    }
    if ($res !== false) {
        if ($tid) {
            return $res;
        } else {
            foreach ($res as $v) {
                $res[$v['tid']] = $v;
            }
        }
        return $res;
    } else {
        return false;
    }
}

function getInformationType($db)
{
    $res = $db->field('id,name')->select('admin_information_type');
    if (false !== $res && !empty($res)) {
        return $res;
    } else {
        return array();
    }
}


$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$tid = isset($_POST['tid']) ? trim($_POST['tid']) : '';
$status = isset($_POST['status']) ? (int)($_POST['status']) : '0';
if (empty($uid) || empty($encpass)) {
    error(-1007);
}
if (!in_array($status, array(0, 1, 2))) {
    error(-1023);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = getInforMationNumber($tid, $status, $db);
$typelist = getInformationType($db);
if ($res) {
    $list = array();
    if ($tid) {
        $wait=$publish=$delete=0;
        for ($i = 0, $k = count($res); $i < $k; $i++) {
            if ($res[$i]['status'] == 0) {
                $wait = $res[$i]['num'];
            }
            if ($res[$i]['status'] == 1) {
                $publish = $res[$i]['num'];
            }
            if ($res[$i]['status'] == 2) {
                $delete = $res[$i]['num'];
            }
        }
        $list['wait']=$wait;
        $list['publish']=$publish;
        $list['delete']=$delete;
    } else {
        for ($i = 0, $k = count($typelist); $i < $k; $i++) {
            $arr['id'] = $typelist[$i]['id'];
            $arr['name'] = $typelist[$i]['name'];
            $arr['number'] = $res[$typelist[$i]['id']]['num'] ? $res[$typelist[$i]['id']]['num'] : '0';
            array_push($list, $arr);
        }

    }
    succ(array('list' => $list));
} else {
    error(-1014);
}




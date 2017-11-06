<?php

require '../../../includeAdmin/init.php';
require '../../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 删除、发布、取消发布资讯
 * @author yandong@6room.com
 * date 2016-11-24  14:23
 */

/**删除、发布、取消发布
 * @param $id   资讯id
 * @param $status 状态
 * @param $db
 * @return bool
 */
function changeInforMation($id, $status, $isrecommend, $db)
{
    $res = $db->where("id in ($id)")->update('admin_information', array('status' => $status, 'isrecommend' => $isrecommend));
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

function getRecomment($type, $db)
{
    if ($type) {
        $res = $db->field('id,list')->where("id = $type")->select('recommend_information');
    } else {
        $res = $db->field('id,list')->select('recommend_information');
    }
    if (false !== $res) {
        if (empty($res)) {
            return array();
        } else {
            foreach ($res as $v) {
                $list[$v['id']] = $v['list'];
            }
            return $list;
        }
    } else {
        return false;
    }
}

/**获取推荐状态
 * @param $id  资讯id
 * @param $db
 * @return bool
 */
function getRStatus($id, $db)
{
    if (empty($id)) {
        return false;
    }
    $res = $db->field('isrecommend')->where("id=$id")->select('admin_information');
    if (false !== $res) {
        return $res[0]['isrecommend'];
    } else {
        return false;
    }
}

function checkPoster($id, $db)
{
    if (empty($id)) {
        return false;
    }
    $res = $db->field('poster')->where("id=$id")->select('admin_information');
    if (false !== $res) {
        return $res[0]['poster'];
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
$id = isset($_POST['id']) ? trim($_POST['id']) : '';
$status = isset($_POST['status']) ? (int)($_POST['status']) : '0';
$isrecommend = isset($_POST['isRecommend']) ? (int)($_POST['isRecommend']) : '0';
if (empty($uid) || empty($encpass) || empty($id)) {
    error(-1007);
}
if (!in_array($status, array(0, 1, 2, 3))) {
    error(-1023);
}
if (!in_array($isrecommend, array(0, 1, 2))) {
    error(-1023);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
if ($status == 1) {//发布
    if ($isrecommend == 1) {
        $isposter = checkPoster($id, $db);
        if (empty($isposter)) {
            error(-1031);
        }
    }
    if (!empty($isrecommend)) {//是否需要推荐
        $list = getRecomment($isrecommend, $db);
        if (empty($list)) {
            $list = $id;
        } else {
            $list = explode(',', $list[$isrecommend]);
            if (count($list) >= INFORMATION_RECOMMENT_NUMBER) {
                unset($list[count($list) - 1]);
            }
            array_push($list, "$id");
            $ulist = array_unique($list);
            $list = implode(',', $ulist);
        }
        addRecommentData($list, $isrecommend, $db);//更新列表
    }
} else {//取消发布或者删除
    $isrecommend = 0;
    $del = explode(',', trim($id, ','));
    for ($i = 0, $k = count($del); $i < $k; $i++) {
        $isRes = getRStatus($del[$i], $db);//是否是已推荐
        if (false !== $isRes && !empty($isRes)) {
            $rlist = getRecomment($isRes, $db);
            $picList = implode(',', array_diff(explode(',', $rlist[$isRes]), array($del[$i])));
            addRecommentData($picList, $isRes, $db);//更新推荐列表
        }
    }
    if ($status == 3) {
        $status = 1;
        $isrecommend = 0;
    }
}
$res = changeInforMation($id, $status, $isrecommend, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}




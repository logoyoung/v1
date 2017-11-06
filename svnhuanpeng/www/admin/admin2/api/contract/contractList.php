<?php

/**
 * 获取经纪公司列表
 * yandong@6rooms.com
 * date 2017-01-22 16:43
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();

/**
 * 获取数据
 * @param obj $db
 * @return array()
 */
function getCompanyList($page,$size,$db)
{
    $list = array();
    $count=$db->field('count(*) as  total')->where("cid !=0")->select('anchor');
    if(empty($count)){
        return  array('list'=>array(),'total'=>0);
    }else{
        $res = $db->field('uid,cid')->where("cid !=0")->limit($page,$size)->select('anchor');
        if (false !== $res && !empty($res)) {
            foreach ($res as $v) {
                $temp['uid'] = $v['uid'];
                $temp['cid'] = $v['cid'];
                array_push($list, $temp);
            }
        }

    }
    return array('list'=>$list,'total'=>$count[0]['total']);
}

function get_companyInfo($cids, $db)
{
    if (empty($cids)) {
        return false;
    }
    $res = $db->field('id,name,status,type')->where("id in ( $cids )")->select('company');
    if (false !== $res && !empty($res)) {
        foreach ($res as $v) {
            $temp[$v['id']] = $v;
        }
        return $temp;
    } else {
        return false;
    }

}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
$page = isset($_POST['page']) ? (int)($_POST['page']) : 1;
$size = isset($_POST['size']) ? (int)$_POST['size'] : 10;

if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}


$res = getCompanyList($page,$size,$db);
if (false !== $res['list']) {
    $cids = array_unique(array_column($res['list'], 'cid'));
    $list = array();
    if ($cids) {
        $cidlist = implode(',', $cids);
        $cinfo = get_companyInfo($cidlist, $db);
        foreach ($res['list'] as $v) {
            $temp['uid'] = $v['uid'];
            $temp['cid'] = $v['cid'];
            $temp['name'] = array_key_exists($v['cid'],  $cinfo) ?  $cinfo[$v['cid']]['name'] : '暂无数据';
            $temp['type'] = array_key_exists($v['cid'],  $cinfo) ?  $cinfo[$v['cid']]['type']  : 0;
            $temp['status'] = array_key_exists($v['cid'],  $cinfo) ?  $cinfo[$v['cid']]['status']  : 0;
            array_push($list,$temp);
        }
    }
    succ(array('list'=>$list,'total'=>$res['total']));
} else {
    error(-1014);
}

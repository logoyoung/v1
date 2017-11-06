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
function getInfor($tid, $status, $keyword, $stime, $etime, $page, $size, $db)
{
    $where = "1 and status =$status ";
    if ($tid) {
        $where .= " and tid = $tid ";
    }
    if ($keyword) {
        $where .= "and binary title like '%$keyword%' ";
    }
    if ($stime) {
        $where .= "and  ctime >= '$stime' ";
    }
    if ($etime) {
        $where .= "and  ctime <='$etime'";
    }
    $num = $db->field('count(*) as num')->where("$where")->select('admin_information');
    $count = $num[0]['num'] ? $num[0]['num'] : 0;
    $page = Page($count, $size, $page);
    $field = 'id,tid,title,content,poster,status,ctime,click,adminid,type,url';
    $res = $db->field($field)->where("$where")->order('ctime desc')->limit($page, $size)->select('admin_information');
    if ($res !== false && !empty($res)) {
        return array('res'=>$res,'total'=>$count);
    } else {
        return false;
    }
}

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$tid = isset($_POST['tid']) ? trim($_POST['tid']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '0';
$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
$stime = isset($_POST['stime']) ? trim($_POST['stime']) : '';
$etime = isset($_POST['etime']) ? trim($_POST['etime']) : '';
$page = isset($_POST['page']) ? trim($_POST['page']) : '1';
$size = isset($_POST['size']) ? trim($_POST['size']) : '10';
if (empty($uid) || empty($encpass)) {
    error(-1007);
}
if (empty($tid)) {
    error(-1026);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = getInfor($tid, $status, $keyword, $stime, $etime, $page, $size, $db);
if ($res['res']) {
    $adminids = implode(',', array_unique(array_column($res['res'], 'adminid')));
    $info = getAdminNick($adminids, $db);
    $list = array();
    foreach ($res['res'] as $v) {
        $temp['id'] = $v['id'];
        $temp['tid'] = $v['tid'];
        $temp['id'] = $v['id'];
        $temp['title'] = $v['title'];
        $temp['poster'] = $v['poster'] ? "http://" . $conf['domain-img'] . '/' . $v['poster'] : '';
        $temp['content'] = $v['content'];
        $temp['ctime'] = $v['ctime'];
        $temp['url'] = $v['url']?$v['url']:'';
        $temp['adtype'] = $v['type'];
        $temp['status'] = $v['status'];
        $temp['nick'] = isset($info[$v['adminid']]) ? $info[$v['adminid']] : '管理员';
        $temp['click'] = $v['click'];
        array_push($list, $temp);
    }
    succ(array('list' => $list,'total'=>$res['total']));
} else {
    succ(array('list' => array(),'total'=>'0'));
}




<?php

/**
 * 获取待推荐主播列表
 * yandong@6rooms.com
 * date 2016-11-19 14:52
 *
 */
require '../../../includeAdmin/init.php';
require INCLUDE_DIR . "Admin.class.php";
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];


/**获取待推荐主播列表
 * @param $searchType /1昵称,2uid
 * @param $keyword  昵称｜｜ uid
 * @param $stime  开始时间
 * @param $etime
 */
function getWaitAnchorList($searchType, $keyword, $stime, $etime, $page, $size, $db)
{
    $where = '1 and status = 0 ';
    if ($searchType == 1) {//按昵称
        if(!empty($keyword)){
            $where .= " and binary nick like '%$keyword%'";
        }
    }
    if ($searchType == 2) {//按UID
        $where .= " and uid=$keyword";
    }
    if ($stime) {
        $where .= " and ctime >= '$stime'";
    }
    if ($etime) {
        $where .= " and ctime <= '$etime'";
    }
    $count = $db->field('count(*) as total')->where($where)->select('admin_recommend_live');
    if (!empty($count) && isset($count[0]['total'])) {
        $count = $count[0]['total'];
    } else {
        $count = 0;
    }
    if($count==0){
        return  array('res'=>array(),'total'=>0);
    }
    //$page = Page($count, $size, $page);
    $res = $db->field('uid,nick,head,poster,ctime,status')
            ->where($where)
            ->limit($page,$size)
            ->order('ctime DESC')
            ->select('admin_recommend_live');
    if (false !== $res) {
        if($res){
            return array('res'=>$res,'total'=>$count);
        }else{
            return array('res'=>array(),'total'=>0);
        }
    } else {
        return false;
    }
}


function checkIsOnLive($uids, $db)
{
    if (empty($uids)) {
        return false;
    }
    $res = $db->field('uid,status')->where("uid in ($uids) and status=100")->select('live');
    if (false !== $res) {
        foreach ($res as $v) {
            $temp[$v['uid']] = $v['status'];
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
$searchType = isset($_POST['searchType']) ? (int)$_POST['searchType'] : '1';//1昵称,2uid  默认0 （string，非必填）
$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
$stime = isset($_POST['stime']) ? trim($_POST['stime']) : '';
$etime = isset($_POST['etime']) ? trim($_POST['etime']) : '';
$page = isset($_POST['page']) ? (int)($_POST['page']) : 1;
$size = isset($_POST['size']) ? (int)($_POST['size']) : 10;

if (empty($uid) || empty($encpass)) {
    error(-1007);
}
if ($searchType == 2) {
    if (preg_match("/[^\d-., ]/", $param)) {
        error(-1019);
    }
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$list = getWaitAnchorList($searchType, $keyword, $stime, $etime, $page, $size, $db);
if (isset($list['res'])) {
    $waitList = array();
    $uids = implode(',', array_column($list['res'], 'uid'));
    $isOnLiving = checkIsOnLive($uids, $db);
    $url = "http://" . $conf['domain-avatar'] . '/';
    foreach ($list['res'] as $v) {
        $temp['uid'] = $v['uid'];
        $temp['nick'] = $v['nick'];
        if (isset($isOnLiving[$v['uid']])){
            $temp['isLiving'] = 1;
        }else{
            $temp['isLiving'] = 0;
        }
        $temp['head'] = $v['head'] ? $url . $v['head'] : DEFAULT_PIC;
        $temp['poster'] = $v['poster'] ? $url . $v['poster'] : CROSS;
        $temp['ctime'] = $v['ctime'];
        $temp['status'] = $v['status'];
        array_push($waitList, $temp);
    }
    succ(array('list' => $waitList,'total'=>$list['total']));
} else {
    error(-1013);
}

<?php

/**
 * 获取已推荐主播列表
 * yandong@6rooms.com
 * date 2016-11-21 15:30
 *
 */
require '../../../includeAdmin/init.php';
require INCLUDE_DIR . "Admin.class.php";
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];


/**获取已推荐主播列表
 * @param $client 1app端  2WEB端
 * @param $page  页码
 * @param $size 条数
 * @param $db
 * @return array
 */
function getRecommendAnchorList( $uids,$page, $size, $db)
{
        $result = $db->field('uid,nick,head,poster,ctime,status')->where("uid in ($uids)")->limit($page, $size)->select('admin_recommend_live');
        if (!empty($result) && false !== $result) {
            foreach($result as $v){
                $list[$v['uid']]=$v;
            }
            return $list;
        } else {
            return array();
        }

}

function  getRecommendlist($client,$db){
    $res = $db->field('list')->where("client=$client")->select('recommend_live');
    if(false !==$res){
        if($res){
            return $res[0]['list'];
        }else{
            return array();
        }
    }else{
        return false;
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
$client = isset($_POST['client']) ? (int)$_POST['client'] : 2;// 1 获取app端推荐  2获取web端推荐
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$size = isset($_POST['size']) ? (int)$_POST['size'] : 10;

if (empty($uid) || empty($encpass)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$rlist=getRecommendlist($client,$db);
if ($rlist) {
    $waitList = array();
    $list = getRecommendAnchorList($rlist, $page, $size, $db);
    $order=explode(',',$rlist);
    $url = "http://" . $conf['domain-img'] . '/';
    for($i=0,$k=count($order);$i<$k;$i++){
        $temp['uid'] = $list[$order[$i]]['uid'];
        $temp['nick'] = $list[$order[$i]]['nick'];
        $temp['head'] = $list[$order[$i]]['head'] ? $url . $list[$order[$i]]['head'] : DEFAULT_PIC;
        $temp['poster'] = $list[$order[$i]]['poster'] ? $url . $list[$order[$i]]['poster'] : CROSS;
        $temp['ctime'] = $list[$order[$i]]['ctime'];
        $temp['status'] = $list[$order[$i]]['status'];
        array_push($waitList, $temp);
    }
    succ(array('list' => $waitList));
} else {
    error(-1013);
}

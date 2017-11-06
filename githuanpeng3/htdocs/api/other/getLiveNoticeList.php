<?php

include '../../../include/init.php';
/**
 * App端直播开播提醒列表
 * date 2016-05-27 14:59
 * author yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

/**
 * 
 * @param type $uid   用户uid
 * @param type $page  页数
 * @param type $size  数量
 * @param type $db
 * @return array
 */
function getfollowUser($uid, $page, $size, $db) {
    $total = $db->field('count(*) as total')->where('uid1 =' . $uid)->select('userfollow');
    $tcount = !empty($total[0]['total'])  ?  $total[0]['total']:0;
    if(!empty($tcount)){
        $page = returnPage($tcount, $size, $page);
        $rows = $db->field('uid2')->where('uid1 =' . $uid . '')->order('tm desc')->limit($page, $size)->select('userfollow');
        return array('res'=>$rows,'page'=>$page,'total'=>$tcount);
    }else{
        return array('res'=>array(),'page'=>$page,'total'=>0);
    }
}


function getLiveNoticeList($uid, $page, $size, $conf, $db) {
    $isnotice = checkUserIsOpenLiveNotice($uid, $db); //检测用户是否开启直播提醒
    if ($isnotice[0]['isnotice'] == 0) {//关闭
        $res = array('status' => '0', 'list' => array(), 'total' => '0','page'=>0, 'pageTotal'=>0);
    }
    if ($isnotice[0]['isnotice'] == 1) {//已开启
        $followlist = getfollowUser($uid, $page, $size, $db); //关注列表
        if ($followlist['res']) {
            $list = array();
            $luids = array_column($followlist['res'], 'uid2');
            $isnotice = getLiveNoticeAnchor($uid, implode(',', $luids), $db); // 检测是否在live_notice表中
            $anchor = getUserInfo($luids, $db); //主播信息
            $fans = batchGetFansCount(implode(',', $luids), $db); //粉丝数
            for ($i = 0, $k = count($luids); $i < $k; $i++) {
                $temp['uid'] = $luids[$i];
                $temp['nick'] = array_key_exists($luids[$i], $anchor) ? $anchor[$luids[$i]]['nick'] : '';
                $temp['head'] = (array_key_exists($luids[$i], $anchor) && !empty($anchor[$luids[$i]]['pic'])) ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $anchor[$luids[$i]]['pic'] : DEFAULT_PIC;
                $temp['fansCount'] = array_key_exists($luids[$i], $fans) ? $fans[$luids[$i]] : '0';
                $temp['status'] = in_array($luids[$i], $isnotice) ? '1' : '0';
                array_push($list, $temp);
            }

            $res = array('status' => '1', 'list' => $list, 'total' => $followlist['total'],'page'=>$followlist['page'],'pageTotal'=>ceil($followlist['total'] / $size));
        } else {
            $res = array('status' => '1', 'list' => array(), 'total' => '0','page'=>0,'pageTotal'=>0);
        }
    }
    return $res;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$page = isset($_POST['page']) ? (int) ($_POST['page']) : 1;
$size = isset($_POST['size']) ? (int) ($_POST['size']) : 10;
if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$page = checkInt($page);
$size = checkInt($size);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error2(-4067,2);
}
$res = getLiveNoticeList($uid, $page, $size, $conf, $db);
succ($res);


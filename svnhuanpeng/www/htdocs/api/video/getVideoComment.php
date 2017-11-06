<?php

include '../../../include/init.php';
/**
 * 获取录像评论
 * date 2015-12-31 10:47 am
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

/**
 * 获取评论
 * @param int $videoID
 * @param int $lastID
 * @param int $size
 * @param int $page
 * @param objict $db
 * @param array $conf
 * @return array
 */
function getComment($videoID, $size, $page, $db,$status) {
    $count = $db->field('count(*) as numb')->where("videoid=$videoID  and  status in ($status)")->select('videocomment');
    $tcount = ($count[0]['numb'] !=0) ? $count[0]['numb'] : 1;
    $page = returnPage($tcount, $size, $page);
    $comment = $db->field('id,uid,rate,tm,comment')->where("videoid=$videoID  and  status in ($status)")->order('id DESC')->limit($page, $size)->select('videocomment');
    return array('comments' => $comment ? $comment : array(), 'total' => $tcount);
}

/**
 * 批量获取评论者昵称头像
 * @param array $uids
 * @param object $db
 * @return array
 */
function getNicksAndPic($uids, $db) {
    $s = implode(',', $uids);
    $ret = array();
    $res = $db->field('uid,nick,pic')->where('uid in (' . $s . ')')->select('userstatic');
    foreach ($res as $key => $val) {
        $ret[$val['uid']] = $val;
    }
    return $ret;
}

/**
 * start
 */
$videoID = isset($_POST['videoID']) ? (int) $_POST['videoID'] : '';
$size = isset($_POST['size']) ? trim($_POST['size']) : 4;
$page = isset($_POST['page']) ? trim($_POST['page']) : 1;
if(empty($videoID)){
    error2(-4031,2);
}
$videoID = checkInt($videoID);
$checkCommentMode=checkMode(CHECK_COMMENT,$db);
if($checkCommentMode){
    $status='1,3';
}else{
    $status='1';
}
$comments = getComment($videoID, $size, $page, $db,$status);
if ($comments['comments']) {
    $uids = array_column($comments['comments'], 'uid');
    $nicksAndPic = getNicksAndPic($uids, $db);
    $lists = $commentLists = array();
    foreach ($comments['comments'] as $k => $v) {
        $lists['commentID'] = $v['id'];
        $lists['uid'] = $v['uid'];
        $lists['nick'] = $nicksAndPic[$v['uid']]['nick'] ? $nicksAndPic[$v['uid']]['nick'] : '';
        $lists['head'] = $nicksAndPic[$v['uid']]['pic'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $nicksAndPic[$v['uid']]['pic'] : DEFAULT_PIC;
        $lists['ctime'] = strtotime($v['tm']);
        $lists['overTime'] = time() - (strtotime($v['tm']));
        $lists['rate'] = $v['rate'];
        $lists['comment'] = $v['comment'] ? $v['comment'] : '';
        array_push($commentLists, $lists);
    }
    if ($commentLists) {
        succ(array('total' => $comments['total'], 'list' => $commentLists));
    } else {
        succ(array('total' => '0', 'list' => array()));
    }
} else {
    succ(array('total' => '0', 'list' => array()));
}





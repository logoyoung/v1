<?php

include '../../includeAdmin/init.php';
/**
 * 获取直播列表
 * @author yandong@6rooms.com
 * @data  2016-07-14 16:06
 * @copyright 6.cn
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_admin();
static $keepwhere = '';
/**
 * 获取直播总数
 * @param int $liveid
 * @param object $db
 * @return string
 */
function getTotalCount($where, $db) {
    $res = $db->field('count(*) as count')->where('status=' . LIVE . '' . $where . '')->select('live');
    return $res[0]['count'];
}

/**
 * 获取直播列表
 * @param int $gametid
 * @param int $gameid
 * @param int $uid
 * @param int $lastID
 * @param int $page
 * @param int $size
 * @param object $db
 * @return array
 */
function getLiveLists($gametid, $gameid, $uid, $page, $size, $db) {
    $where = '';
    if ($gametid) {
        $gametid = checkInt($gametid);
        $where .=' and gametid=' . $gametid . '';
    }
    if ($gameid) {
        $gameid = checkInt($gameid);
        $where .=' and gameid=' . $gameid . '';
    }
    if ($uid) {
        $uid = checkInt($uid);
        $where .=' and uid=' . $uid . '';
    }
        $rows = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')
                  ->where('status=' . LIVE . '' . $where . '')      
                        ->order('liveid desc')->limit($page, $size)->select('live');

    return array('rows' => $rows, 'where' => $where);
}

/**
 * start
 */
$gametid = isset($_POST['gameTypeID']) ? trim($_POST['gameTypeID']) : '';
$gameid = isset($_POST['gameID']) ? trim($_POST['gameID']) : '';
$uid = isset($_POST['userID']) ? trim($_POST['userID']) : '';
$lastID = isset($_POST['lastID']) ? trim($_POST['lastID']) : '';
$size = isset($_POST['size']) ? trim($_POST['size']) : 8;
$page = isset($_POST['page']) ? trim($_POST['page']) : 1;
//必须参数验证
$row = getLiveLists($gametid, $gameid, $uid, $page, $size, $db);
$arr = $data = array();
if ($row['rows']) {
    foreach ($row['rows'] as $rk => $rv) {
        $arr['lid'] = $rv['liveid'];
        $arr['gid'] = $rv['gameid'];
        $arr['gtid'] = $rv['gametid'];
        $arr['gname'] = $rv['gamename'];
        $arr['uid'] = $rv['uid'];
        $arr['title'] = $rv['title'];
        $arr['ctime'] = $rv['ctime'];
        $arr['poster'] = ($rv['poster']) ? (DOMAIN_PROTOCOL . $conf['domain-img'] .'/'. $rv['poster']) : CROSS;
        $arr['angle'] = $rv['orientation'];
        $autheInfo = getUserInfo($rv['uid'], $db);
        $arr['nick'] = $autheInfo[0]['nick'] ? $autheInfo[0]['nick'] : '';
        $arr['pic'] = $autheInfo[0]['pic'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . "/" . $autheInfo[0]['pic'] : DEFAULT_PIC;
        array_push($data, $arr);
    }
    if ($data) {
        if ($page) {
            $count = getTotalCount($row['where'], $db);
        } 
        succ(array("liveList" => $data, 'total'=> $count));
    }
} else {
   succ(array("liveList" => array(), 'total'=> array()));
}

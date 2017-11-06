<?php

include '../init.php';
/**
 * 获取推荐咨询列表
 * date 2015-12-17 10:03 AM
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * 这个资讯列表觉得应该单独放到一张表里面,由后台去制定管理,下面的这个接口算是临时的
 * 随机获取5条直播信息
 * @param int $size
 * @param object $db
 * @return array
 */
function getLiveLists($size, $db) {
    $rows = $db->field('liveid,gameid,stream,gametid,gamename,uid,title,ctime,poster,orientation')
                    ->where('status=' . LIVE . '')->limit('' . $size . '')->select('live');
    return $rows;
}

function getRecommentLists($db) {
    $rows = $db->field('liveid')->select('index_recommend_Live');
    if ($rows !== false) {
        $rows = array_column($recommend, 'liveid');
    } else {
        $rows = array();
    }
    return $rows;
}

/**
 * start
 */
$size = isset($_POST['size']) ? trim($_POST['size']) : '4';
$recommend = getRecommentLists($db);
var_dump($recommend);
exit;
if (count($recommend) == 0) {
    $rows = getLiveLists($size, $db);
} else {
    if (count($recommend) < $size) {
        $size = $size - count($recommend);
    }
}

$arr = $recommendList = array();
foreach ($rows as $rk => $rv) {
    $arr['luid'] = $rv['uid'];
    $arr['posterUrl'] = ($rv['poster']) ? ("http://" . $conf['domain-img'] . $rv['poster']) : '';
    $arr['livestream'] = $rv['stream'];
    $arr['liveTitle'] = $rv['title'];
    $arr['gameName'] = $rv['gamename'];
    $arr['gameType'] = getGameTypeName($rv['gametid'], $db);
    $author = getUserInfo($rv['uid'], $db);
    $arr['nick'] = $author[0]['nick'];
    $arr['viewerCount'] = getViewerCount($rv['liveid'], $db);
    array_push($recommendList, $arr);
}
if ($recommendList) {
    exit(jsone(array('relist' => $recommendList)));
} else {
    exit(jsone(array('relist' => '')));
}


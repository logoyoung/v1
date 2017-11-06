<?php
header("Access-Control-Allow-Origin: *");
/**
 * 获取当前直播列表
 * date 2017-02-24 09:45
 * author yandong@6rooms.com
 */
include '../../../include/init.php';
$db = new DBHelperi_huanpeng();
$redisObj = new RedisHelp();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$gameArray = array(
    '190' => 'game_011.png',
    '150' => 'game_022.png',
    '215' => 'game_044.png',
    '62' => 'game_033.png'
);
/**
 * 获取最新列表
 * @param type $redisObj
 * @param type $db
 * @return array
 */
function getlistTocn($redisObj, $gameIds, $db)
{
    $cacheKey = '6.cn_showList';
    $getCatch = $redisObj->get($cacheKey); //取缓存
    if ($getCatch) {
        $list = json_decode($getCatch, true);
    } else {
        $list = $db->field('liveid,title,ctime,uid,poster,gameid,gamename')->where("gameid in ( $gameIds) " . " and  status=" . LIVE)->select('live');
        $redisObj->set($cacheKey, json_encode($list), 90); //写缓存
    }
    return $list;
}


/**
 * start
 */
$size = isset($_POST['size']) ? (int)$_POST['size'] : 10;
if (!is_numeric($size)) {
    error2(-4013);
}
$cacheKey = '6.cn_showListCache';
$getCatch = $redisObj->get($cacheKey); //取缓存
if ($getCatch) {
	$list = json_decode($getCatch, true);
	succ(array('list' => $list));
} else {
	$gameid = array_keys($gameArray);
	$res = getlistTocn($redisObj, implode(',', $gameid), $db);
	if ($res) {
		$list = array();
//    $liveuser = batchGetLiveRoomUserCount(implode(',', array_column($res, 'uid')), $db); //获取在线用户数
		$liveRoomObj=new \lib\LiveRoom(1);
		foreach ($res as $v) {
			$temp['title'] = $v['title'];
			if ($GLOBALS['env'] == "DEV") {
				$temp['poster'] = 'https://vi0.6rooms.com/huanpeng/recGame/'.$gameArray[$v['gameid']];//"https://dev.huanpeng.com/static/img/src/recGame/" . $gameArray[$v['gameid']];
			} else {
				$temp['poster'] = 'https://vi0.6rooms.com/huanpeng/recGame/'.$gameArray[$v['gameid']];//"https://dev.huanpeng.com/static/img/src/recGame/" . $gameArray[$v['gameid']];
			}
			$temp['gameName'] = $v['gamename'];
			$temp['ctime'] = strtotime($v['ctime']);
			$temp['viewCount'] = $liveRoomObj->getLiveRoomUserCountFictitiousByLuid( $v['uid'] );
			$temp['url'] = "http://www.huanpeng.com/sharer.php?luid=" . $v['uid'] . "&datamain=6cn";
			array_push($list, $temp);
		}
		if($list){
			$list=dyadicArray($list, 'viewCount', SORT_DESC);
			if (count($list) > $size) {
				$list = array_slice($list, 0, $size);
			}
			$redisObj->set($cacheKey, json_encode($list), 60); //写缓存
		}else{
			$list=array();
		}
		succ(array('list' => $list));
	} else {
		succ(array('list' => array()));
	}
}




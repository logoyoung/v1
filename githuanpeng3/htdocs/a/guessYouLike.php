<?php

include '../init.php';
/**
 * 猜你喜欢
 * date 2015-12-17 11:30 AM
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * 获取最近的8条历史纪录
 * @param int $uid
 * @param object $db
 * @return array
 */
function gethistory($uid, $db) {
    $hluid = array();
    $historyluids = $db->field('luid')->order('stime DESC')->limit(10)->where('uid=' . $uid . '')->select('history');
    foreach ($historyluids as $historyluid) {
        $hluid[] = $historyluid['luid'];
    }
    return $hluid;
}

/**
 * 随机获取8位正在直播中的主播
 * @param object $db
 * @return array
 */
function getLiveUid($db) {
    $arr = array();
    if(DEBUG){
          $uids = $db->field('uid')->order('rand()')->limit('6')->select('live'); 
    }else{
         $uids = $db->field('uid')->where('status=' . LIVE . ' group by uid')->order('rand()')->limit('6')->select('live');  
    }
    foreach ($uids as $uid) {
        $arr[] = $uid['uid'];
    }
    return $arr;
}

/**
 * 随机获取8位已经关注的主播
 * @param int $uid
 * @param object $db
 * @return array
 */
//function userfollow($uid,$db){
//    $followId=$db->field('uid2')->order('rand()')->limit(8)->where('uid1='.$uid.'')->select('userfollow');
//    return $followId;
//}

/**
 * 随机获取8个已关注的游戏
 * @param int $uid
 * @param object $db
 * @return array
 */
function gamefollow($uid, $db) {
    $gameids = $db->field('gameid')->where('uid=' . $uid . '')->select('gamefollow');
    return $gameids;
}

/**
 * 获取符合已关注游戏类型的直播
 * @param int $gameid
 * @param object $db
 * @return array
 */
function getLiveByGameid($gameid, $db) {
    $listarray = $gid = $luids = array();
    foreach ($gameid as $gv) {
        $gid[] = $gv['gameid'];
    }
    $gids = implode(',', $gid);
    if(DEBUG){
       $luid = $db->field('uid')->where('gameid in (' . $gids . ')')->select('live');  
    }else{
      $luid = $db->field('uid')->where('gameid in (' . $gids . ') and status =' . LIVE . '')->select('live');   
    } 
    foreach ($luid as $luidv) {
        $luids[] = $luidv['uid'];
    }
    return $luids;
}

/**
 * 获取猜到的数据
 * @param type $luids
 * @param type $size
 * @param type $db
 * @return type
 */
function getGuessData($luids, $size, $db) {
    if (DEBUG) {
        $rows = $db->field('liveid,gameid,stream,gametid,gamename,uid,title,ctime,poster,orientation')
                        ->where('uid in (' . $luids . ')')
                        ->order('rand()')->limit('' . $size . '')->select('live');
    } else {
        $rows = $db->field('liveid,gameid,stream,gametid,gamename,uid,title,ctime,poster,orientation')
                        ->where('uid in (' . $luids . ') and status=' . LIVE . '')
                        ->order('rand()')->limit('' . $size . '')->select('live');
    }

    return $rows;
}

/**
 * 
 * 猜你喜欢,获取八条数据
 * @param int $size
 * @param object $db
 * @return array
 */
function getVideoLists($uid, $size, $db) {
    if (!empty($uid)) {
        $total = array();
        $gameid = gamefollow($uid, $db);
        $luids = '';
        if (!empty($gameid)) {
            $luids = getLiveByGameid($gameid, $db);
        }
        $historyluid = gethistory($uid, $db);
        if (!empty($luids)) {
            $total = array_merge($total, $luids);
        }
        if (!empty($historyluid)) {
            $total = array_merge($total, $historyluid);
        }
        if (count(array_unique($total)) >= $size) {
            $luidlistss = array_rand($total, $size);
            if ($luidlistss) {
                $ids = implode(',', $luidlistss);
                $rows = getGuessData($ids, $size, $db);
            } else {
                $rows = array();
            }
            if (count($rows) < $size) {
                if (!empty($rows)) {
                    foreach ($rows as $k => $v) {
                        $rowsRes[$k] = $v['uid'];
                    }
                    $uids = getLiveUid($db);
                    $total = array_merge($rowsRes, $uids);
                    $luidlists = array_rand(array_flip(array_unique($total)), $size);
                    if ($luidlists) {
                        $ids = implode(',', $luidlists);
                        $rows = getGuessData($ids, $size, $db);
                    } else {
                        $rows = array();
                    }
                } else {
                    $uids = getLiveUid($db);
                    if ($uids) {
                        $ids = implode(',', $uids);
                        $rows = getGuessData($ids, $size, $db);
                    } else {
                        $rows = array();
                    }
                }
            }
        } else {
            $uids = getLiveUid($db);
            $luidlists = array_merge($total, $uids);         
//            $luidlists = array_rand(array_flip(array_unique($total)), $size);
//              echo 'wwww';
//                var_dump($luidlists);exit;
            if ($luidlists) {
                $ids = implode(',', $luidlists);
                $rows = getGuessData($ids, $size, $db);
            } else {
                $rows = array();
            }
        }
    } else {
        $ids = getLiveUid($db);
        if ($ids) {
            $ids = implode(',', $ids);
            $rows = getGuessData($ids, $size, $db);
        } else {
            $rows = array();
        }
    }
    return $rows;
}

/**
 * 拼装数据
 * @param type $rows
 * @return type
 */
function makeData($uid, $rows, $conf, $db) {
    $arr = $guessList = array();
    foreach ($rows as $rk => $rv) {
        $arr['luid'] = $rv['uid'];
        $arr['livestream'] = $rv['stream'];
        $arr['liveTitle'] = $rv['title'];
        $arr['gameName'] = $rv['gamename'];
        $arr['angle'] = $rv['orientation'];
        if ($rv['poster']) {
            $arr['posterUrl'] = "http://" . $conf['domain-img'] . "/" . $rv['poster'];
            $arr['ispic'] = '1';
        } else {
            $arr['posterUrl'] = CROSS;
            $arr['ispic'] = '0';
        }
        $arr['gameType'] = getGameTypeName($rv['gametid'], $db);
        $author = getUserInfo($rv['uid'], $db);
        $arr['nick'] = $author[0]['nick'];
        $arr['viewerCount'] = getViewerCount($rv['liveid'], $db);
        $arr['fansCount'] = getFansCount($rv['uid'], $db);
        if ($uid) {
            $isfollow = isOneFollowOne($uid, $rv['uid'], $db);
            if ($isfollow) {
                $arr['isFollow'] = 1;
            } else {
                $arr['isFollow'] = 0;
            }
        } else {
            $arr['isFollow'] = 0;
        }
        array_push($guessList, $arr);
    }
    return $guessList;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encapass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$size = isset($_POST['size']) ? trim($_POST['size']) : 6;
$rows = getVideoLists($uid, $size, $db);
if (empty($rows)) {
    error(-994);
}
$guessList = makeData($uid, $rows, $conf, $db);
if ($guessList) {
    exit(jsone(array('guessList' => $guessList)));
} else {
    exit(jsone(array('guessList' => '')));
}


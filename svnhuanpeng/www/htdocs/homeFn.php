<?php
/**
 * 首页操作函数
 *   
 *   */
/**
 * 输出js变量
 * @param unknown $jsStr
 * @return boolean|string  */
function echoJS($jsStr)
{
    $echoJs = '<script>';
    if (! is_array($jsStr))
        return false;
    foreach ($jsStr as $k => $v) {
        if (! $v)
            $v = "''";
        $echoJs .= ' var ' . $k . '=' . $v . ";";
    }
    $echoJs .= '</script>';
    return $echoJs;
}
/**
 * 首页推荐直播
 * @param unknown $db
 * @param number $size
 * @return multitype:  */
function getRecommend($db, $conf, $size = 6)
{
    //$conf = $GLOBALS['env-def'][$GLOBALS['env']];
    $rows = $db->field('liveid,gameid,stream,gametid,gamename,uid,title,ctime,poster,orientation,status')
    ->where('status=' . LIVE . '')
    ->order('liveid desc')
    ->limit('' . $size . '')
    ->select('live');
    $uidList = array();
    $uidstr = '';
    foreach ($rows as $uidk=>$uidv)
        $uidList[] = $uidv['uid'];
    if(count($uidList)!=0){
        $uidList = implode($uidList, ',');
        $uidstr = "and uid not in ({$uidList})";
    }
    $rows2 = array();
    if(count($rows)<$size){
        $rows2 = $db->field('liveid,gameid,stream,gametid,gamename,uid,title,ctime,poster,orientation,status')
        ->where('status=' . LIVE_VIDEO . " and poster<>'' $uidstr")
        ->order('liveid desc')
        ->limit('' . $size-count($rows) . '')
        ->select('live');
    }
    //var_dump($uidList);
    $rows = array_merge($rows,$rows2);
    $arr = $recommendList = array();
    foreach ($rows as $rk => $rv) {
        $arr['luid'] = $rv['uid'];
        $arr['posterUrl'] = ($rv['poster']) ? ("http://" . $conf['domain-img'] .'/'. $rv['poster']) : '';
        $arr['livestream'] = $rv['stream'];
        $arr['liveTitle'] = $rv['title'];
        $arr['gameName'] = $rv['gamename'];
        $arr['orientation'] = $rv['orientation'];
        $arr['status'] = $rv['status'];
        //var_dump($rv['gameid']);
        $arr['gameType'] = getGameTypeName($rv['gametid'], $db);
        $author = getUserInfo($rv['uid'], $db);
        $arr['nick'] = $author[0]['nick'];
        $arr['pic'] = ($author[0]['pic']) ? ("http://" . $conf['domain-img'] . $author[0]['pic']) : '';
        $arr['viewerCount'] = getViewerCount($rv['liveid'], $db);
        array_push($recommendList, $arr);
    }
    return $recommendList;
}
/**
 * 获取历史
 * @param unknown $uid
 * @param unknown $db
 * @return multitype:unknown  */
function gethistory($uid, $db)
{
    $hluid = array();
    $historyluids = $db->field('luid')
    ->order('stime DESC')
    ->limit(10)
    ->where('uid=' . $uid . '')
    ->select('history');
    foreach ($historyluids as $historyluid) {
        $hluid[] = $historyluid['luid'];
    }
    return $hluid;
}

/**
 * 随机获取8位正在直播中的主播
 *
 * @param object $db
 * @return array
 */
function getLiveUid($db)
{
    $arr = array();
    $uids = $db->field('uid')
    ->where('status=' . LIVE . '')
    ->order('rand()')
    ->limit('10')
    ->select('live');
    foreach ($uids as $uid) {
        $arr[] = $uid['uid'];
    }
    return $arr;
}
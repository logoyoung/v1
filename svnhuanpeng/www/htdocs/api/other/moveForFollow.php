<?php

include '../../../include/init.php';
/**
 * App端获取收藏视频滑动列表
 * date 2016-10-25 11:38
 * anchor yandong@6rooms.com
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * 获取收藏的录像id
 * @param int $uid 主播id
 * @param type $conf
 * @param type $db
 * @return type
 */
function getFollowVideoList($uid, $db)
{
    $res = $db->field('videoid')->where('uid=' . $uid . '')->order('tm DESC')->select('videofollow');
    if (false !== $res) {
        return $res;
    } else {
        return array();
    }
}

/**
 * 根据videoid获取录像详情
 * @param type $videoId
 * @param type $db
 * @return boolean
 */
function getVideoInfoByVideoid($videoId, $db)
{
    if (empty($videoId)) {
        return false;
    }
    $res = $db->field('videoid,uid,poster,vfile')->where("videoid in ($videoId)")->select('video');
    if (false !== $res) {
        foreach ($res as $v) {
            $temp[$v['videoid']] = $v;
        }
        return $temp;
    } else {
        return array();
    }
}

function getData($uid, $conf, $db)
{
    $res = getFollowVideoList($uid, $db);
    if ($res) {
        $followVideo = array_column($res, 'videoid');
        $videoId = implode(',', $followVideo);
        $list = getVideoInfoByVideoid($videoId, $db);
        if ($list) {
            for ($i = 0, $k = count($followVideo); $i < $k; $i++) {
                $temp[$i]['uid'] = $list[$followVideo[$i]]['uid'];
                $temp[$i]['videoID'] = $list[$followVideo[$i]]['videoid'];
                $temp[$i]['poster'] = sposter($list[$followVideo[$i]]['poster']);
                $temp[$i]['videoUrl'] = sfile($list[$followVideo[$i]]['vfile']);
            }
            return $temp;
        } else {
            return array();
        }
    } else {
        return array();
    }

}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
if (!$uid || !$encpass) {
    error2(-4013);
}
//检查用户登陆状态
$userState = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $userState) {
    error2(-4067, 2);
}
$res = getData($uid, $conf, $db);
succ(array('list' => $res));
 

<?php

include '../../../include/init.php';
/**
 * App端获取主播主页录像滑动列表
 * date 2016-10-25 11:38
 * anchor yandong@6rooms.com
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();


/**获取主播录像
 * @param int $uid 主播id
 * @param $db
 * @return array
 */
function getUserVideoid($uid, $type, $db)
{
    if ($type) {
        $status = VIDEO_UNPUBLISH . ',' . VIDEO;
    } else {
        $status = VIDEO;
    }
    $res = $db->field('videoid,uid,poster,vfile')->where("uid=$uid  and status in ($status)")->order('videoid DESC')->limit(20)->select('video');
    if (false !== $res) {
        return $res;
    } else {
        return array();
    }
}

function getData($uid, $type, $conf, $db)
{
    $list = array();
    $res = getUserVideoid($uid, $type, $db);
    if ($res) {
        foreach ($res as $v) {
            $temp['uid'] = $v['uid'];
            $temp['videoID'] = $v['videoid'];
            if($v['poster']){
                $vposter=sposter($v['poster']);
            }else{
                $vposter=CROSS;
            }
            $temp['poster'] = $vposter;
            $temp['videoUrl'] = sfile($v['vfile']);
            array_push($list, $temp);
        }
        return $list;
    } else {
        return array();
    }


}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 0;
if ($type) {
    if (!in_array($type, array(0, 1))) {
        error2(-4013);
    }
    if (empty($uid) || empty($encpass)) {
        error2(-4013);
    }
    //检查用户登陆状态
    $userState = CheckUserIsLogIn($uid, $encpass, $db);
    if (true !== $userState) {
        error2(-4067,2);
    }

} else {
    if (empty($uid)) {
        error2(-4013);
    }
}

$res = getData($uid, $type,$conf, $db);
succ(array('list' => $res));
 

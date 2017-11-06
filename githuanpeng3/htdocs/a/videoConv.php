<?php

include '../init.php';
/**
 * 直播结束回调入库
 * date 2016-05-09 14:35
 * author yandong@6rooms.com 
 * copyright 6.cn version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 添加一条数据到video表
 * @param type $uid 用户uid
 * @param type $vfile  录像路径
 * @param type $length  录像长度
 * @param type $db  
 * @return boolean
 */
function addVideo($liveid, $vfile, $poster, $length, $db) {
    if (empty($liveid) || empty($vfile)) {
        return false;
    }
    $liveInfo = getLiveInfoByUid($liveid, $db);
    if ($liveInfo) {
        $add = array(
            'uid' => $liveInfo[0]['uid'],
            'gametid' => $liveInfo[0]['gametid'],
            'gameid' => $liveInfo[0]['gameid'],
            'gamename' => $liveInfo[0]['gamename'],
            'title' => $liveInfo[0]['title'],
            'poster' => $poster ? '/' . $poster : $liveInfo[0]['poster'],
            'length' => $length,
            'liveid' => $liveid,
            'ip' => $liveInfo[0]['ip'],
            'port' => $liveInfo[0]['port'],
            'vfile' => '/' . $vfile,
            'orientation' => $liveInfo[0]['orientation']
        );
        if ($liveInfo[0]['antopublish'] == 1) {//校验是否自动发布
            $add['status'] = VIDEO_UNPUBLISH;
        }
        $res = $db->insert('video', $add);
        if ($liveInfo[0]['antopublish'] == 1 && false !==$res) {
            synchroAdminWiatPassVideo($res, $db); //如果是自动发布的添加到admin_wait_pass_video
            return true;
        } else {
            return false;
        }
    } else {
        error(-5002); //查询出错
    }
}

/**
 * 
 * @param type $filename 原文件
 * @param type $dstdir  目标目录
 * @return boolean
 */
function moveTmpFile($filename, $dstdir = IMAGE_DIR) {
    if (!file_exists($filename))
        return false;
    $file = md5($filename);
    $dstfile = $file[0] . '/' . $file[1] . '/';
    $re = mkdirs($dstdir . $dstfile);
    if (!$re)
        return false;
    $dstfilename = $dstdir . $dstfile . $file . '.' . pathinfo($filename, PATHINFO_EXTENSION);
    $reall = $dstfile . $file . '.' . pathinfo($filename, PATHINFO_EXTENSION);
    $r = rename($filename, $dstfilename);
    if (!$r)
        return false;
    return $reall;
}

/**
 * 同步admin_videomerge_failed表
 * @param type $db
 */
function changeVideoMerge($liveid, $status, $db) {
    $res = $db->where("liveid=$liveid")->update('admin_videomerge_failed', array('status' => $status));
    return $res;
}

/**
 * start
 */
//if (!verifySign($_GET,VIDEO_CONV_SECRET_KEY)) {
//    exit(json_encode(array('err' => -111, 'desc' => '认证失败')));
//}
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$liveid = isset($_GET['liveid']) ? (int) $_GET['liveid'] : '';
$vfile = isset($_GET['vfile']) ? trim($_GET['vfile']) : '';
$poster = isset($_GET['poster']) ? trim($_GET['poster']) : '';
$length = isset($_GET['length']) ? trim($_GET['length']) : '';

if (empty($liveid) || empty($vfile) || empty($length)) {
    return false;
}
$vfile = moveTmpFile($vfile, $conf['video-dir'] . '/');
$poster = moveTmpFile($poster, $conf['img-dir'] . '/');
$res = addVideo($liveid, $vfile, $poster, $length, $db);
if ($res) {
    changeVideoMerge($liveid, 2, $db);
    echo 1;
} else {
    changeVideoMerge($liveid, 1, $db);
    echo 0;
}
exit();

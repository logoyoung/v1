<?php

/**
 * 获取一条待审核录像信息
 * yandong@6rooms.com
 * date 2016-06-30 16:25
 * 
 */
require '../../includeAdmin/Video.class.php';
require '../../includeAdmin/Admin.class.php';
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_admin();


/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 6;
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : 'b312a3363e4c5ac32a9caadcdd4b0220';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
$Vtype = isset($_POST['vtype']) ? (int) $_POST['vtype'] : 2;
$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
$size = isset($_POST['size']) ? (int) $_POST['size'] : 10;


//if (empty($uid) || empty($encpass) || empty($type)) {
//    error(-4013);
//}
//$adminHelp = new AdminHelp($uid, $type);
//$err = $adminHelp->loginError($encpass);
//if ($err) {
//    error($err);
//}

$videoObj = new Video();
if ($Vtype == 1) {//待审核
    $count = $videoObj->waitPass();
}
if ($Vtype == 2) {//已审核
    $count = $videoObj->Pass();
}
if ($Vtype == 3) {//审核中
    $count = $videoObj->pending();
}
if ($Vtype == 4) {//未通过
    $count = $videoObj->unPass();
}
if(!empty($count)){
    $page = Page($count, $size, $page); 
    $videoid = $videoObj->getvlistBytype($Vtype, $page, $size);
    if ($videoid) {
        $list = array();
        $vid = implode(',', array_column($videoid, 'videoid'));
        $vlist = $videoObj->getMostVideoInfo($vid);
        
        $liveid = array_column($vlist, 'liveid');
        $live = $videoObj->getLiveTime($liveid);
        $url = "http://" . $conf['domain-avatar'] . '/';
        $video = $conf['domain-video'] . '/';
        foreach ($videoid as $v) {
            $data['videoId'] = $vlist[$v['videoid']]['videoid'];
            $data['uid'] = $vlist[$v['videoid']]['uid'];
            $anchorInfo = getUserInfo($vlist[$v['videoid']]['uid'], $db);
            $data['nick'] = $anchorInfo[0]['nick'];
            $data['pic'] = $anchorInfo[0]['pic'] ? $url . $anchorInfo[0]['pic'] : '';
            $data['gamename'] = $vlist[$v['videoid']]['gamename'];
            if ($vlist[$v['videoid']]['gametid']) {
                $data['gametype'] = getGameTypeName($vlist[$v['videoid']]['gametid'], $db);
            } else {
                $data['gametype'] = '其他';
            }
            $data['length'] = SecondFormat($vlist[$v['videoid']]['length']);
            $data['ctime'] = $vlist[$v['videoid']]['ctime'];
            $data['poster'] = $vlist[$v['videoid']]['poster'] ? $video . $vlist[$v['videoid']]['poster'] : '';
            $data['title'] = strCut($vlist[$v['videoid']]['title'], 8, $suffix = true);
            $data['file'] = sfile($vlist[$v['videoid']]['vfile']);
            $data['livetime'] = $live[$vlist[$v['videoid']]['liveid']];
            array_push($list, $data);
        }
        succ(array('data'=>$list,'total'=>$count));
    } else {
        error(-1013);
    }
} else {
    error(-1013);
}


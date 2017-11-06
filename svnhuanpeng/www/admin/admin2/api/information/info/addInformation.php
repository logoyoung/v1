<?php

require '../../../includeAdmin/init.php';
require '../../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 添加资讯
 * @author yandong@6room.com
 * date 2016-11-23  14:23
 */

/**添加资讯
 * @param $uid  管理员id
 * @param $tid  资讯类型id
 * @param $title  标题
 * @param $poster  封面图
 * @param $content  内容
 * @param $status  状态
 * @param $db
 * @return bool
 */
function addInforMation($uid, $tid,$title,$poster,$content,$status,$isrecommend,$adtype,$db)
{
    $data = array(
        'tid'=>$tid,
        'title'=>$title,
        'poster'=>$poster,
        'content'=>$content,
        'adminid' => $uid,
        'type'=>$adtype,
        'status' => $status,
        'isrecommend'=>$isrecommend
    );
    $res = $db->insert('admin_information', $data);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$tid = isset($_POST['tid']) ? trim($_POST['tid']) : '';
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$poster = isset($_POST['poster']) ? trim($_POST['poster']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '0';
$adtype = isset($_POST['adtype']) ? trim($_POST['adtype']) : '0';
$isrecommend = isset($_POST['isRecommend']) ? trim($_POST['isRecommend']) : '0';
if (empty($uid) || empty($encpass)) {
    error(-1007);
}
if(empty($tid)){
    error(-1026);
}
if (empty($title)) {
    error(-1024);
}
if (empty($content)) {
    error(-1025);
}
if(!in_array($isrecommend,array(0,1,2))){
    error(-1023);
}
if(!in_array($adtype,array(0,1))){
    error(-1023);
}
if($isrecommend==1){
    if(empty($poster)){
        error(-1030);
    }
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$title = filterWords($title);
$content=filterWords($content);
$res = addInforMation($uid, $tid,$title,$poster,$content, $status,$isrecommend,$adtype, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}




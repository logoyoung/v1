<?php
require '../../includeAdmin/init.php';
require('../../includeAdmin/Redis.class.php');
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 游戏类型列表
 * @author yandong@6room.com
 * date 2016-06-27  15:11
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
//$redis=new RedisHelp();
$redis = '';
function gameTypeList($conf, $db,$redis) {
    $list = array();
//    $isexit = $redis->get('gametype');
    $isexit='';
    if ($isexit) {
        $res = json_decode($isexit,true);
    } else {
        $res = $db->field('gametid,name,icon')->select('gametype'); //以后加缓存  
//        $redis->set('gametype', json_encode($res), 3600); //存入redis
    }
    if ($res !== false) {
        foreach ($res as $v) {
            $temp['gametid'] = $v['gametid'];
            $temp['name'] = $v['name'];
            $temp['icon'] = !empty($v['icon']) ? "http://" . $conf['domain-img'] . '/' . $v['icon'] : '';
            array_push($list, $temp);
        }
      
    } else {
        $list = array();
    }
    return $list;
}
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
if(!is_numeric($type) || !is_numeric($uid)){
    error(-1023);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = gameTypeList($conf, $db,$redis);
exit(json_encode(array('data' => $res)));



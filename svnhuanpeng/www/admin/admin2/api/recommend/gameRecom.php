<?php
/**
 * 分类游戏推荐
 * yandong@6rooms.com
 * date 2016-07-13 15:00
 * 
 */
require '../../includeAdmin/init.php';

$db = new DBHelperi_admin();
/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;


if (empty($uid) || empty($encpass) || empty($type)) {
    error(-4013);
}

function getRecommendGame($size, $db) {
    $gameids = array();
    $res = $db->field('gameid')->where("type=1")->select('admin_recommend_game');
    if ($res) {
        $re = explode(',', $res[0]['gameid']);
        $after = array_slice(explode(',', $res[0]['gameid']), 0, $size);
        if ($after) {
            $gameids = $after;
        }
    }
    return $gameids;
}
 $res=getRecommendGame($size, $db) ;
 var_dump($res);
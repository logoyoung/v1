<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 添加推荐游戏
 * @param  string $gameist 推荐的游戏id
 * @param $recomType  推荐的类型  1:导航分类推荐游戏  2:推荐游戏 3:楼层游戏
 * @param $db
 * @return bool
 */
function addRecomGame($recomType, $gameID, $number, $db)
{
    $sql = "insert into admin_recommend_game (`type`, `gameid`,`number`) value($recomType, '$gameID','$number') on duplicate key update gameid='$gameID',number='$number'";
    $res = $db->query($sql);
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
$recomType = isset($_POST['recomType']) ? (int)$_POST['recomType'] : '';
$gameID = isset($_POST['gameID']) ? trim($_POST['gameID']) : '';
$number = isset($_POST['number']) ? trim($_POST['number']) : 0;
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
if (empty($gameID)) {
    error(-1015);
}
$gameID=array_filter(explode(',',$gameID));
if ($recomType == 3) {
    $numcount=array_filter(explode(',',$number));
    if(count($gameID) != count($numcount)){
        error(-1023);
    }
}

if (!in_array($recomType, array(1, 2, 3))) {
    error(-1023);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$gameID=implode(',',$gameID);
$res = addRecomGame($recomType, $gameID, $number, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}

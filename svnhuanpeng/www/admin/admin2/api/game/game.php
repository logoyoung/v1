<?php
require '../../includeAdmin/init.php';
require '../../includeAdmin/Redis.class.php';
require '../../includeAdmin/Admin.class.php';
/**
 * 游戏列表
 * @author yandong@6room.com
 * date 2016-06-23  15:11
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_admin();

//$res=get_GameList($db);
/**获取游戏列表
 * @param $status
 * @param $db
 * @return array
 */
function gameList($status, $db)
{
    $where = "1 and status=$status";
    $res = $db->field('gameid,name')->where("$where ")->select('game'); //以后加缓存
    if ($res !== false) {
        return array('res' => $res);
    } else {
        return array('res' => array());
    }
}

function gameZoneList($gameid, $db)
{
    if ($gameid) {
        $gameid = implode(',', $gameid);
        $result = $db->where("gameid in ($gameid)")->select('game_zone');
        if ($result !== false) {
            foreach ($result as $v) {
                $list[$v['gameid']] = $v;
            }
        } else {
            $list = array();
        }
    } else {
        $list = array();
    }
    return $list;
}

function getGameList($status, $conf, $db)
{
    $url = "http://" . $conf['domain-img'] . '/';
    $list = array();
    $res = gameList($status, $db);
    if ($res['res']) {
        $gameid = array_column($res['res'], 'gameid');
        $gzone = gameZoneList($gameid, $db);
        foreach ($res['res'] as $v) {
            $temp['gameID'] = $v['gameid'];
            $temp['gameName'] = $v['name'];
            $temp['poster'] = (array_key_exists($v["gameid"], $gzone) && !empty($gzone[$v["gameid"]]["poster"])) ? $url . $gzone[$v["gameid"]]["poster"] : CROSS;
            array_push($list, $temp);
        }
    }
    return array('res' => $list);
}

/**
 * start
 */
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


$status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
$res = getGameList($status, $conf, $db);
if ($res) {
    succ(array('list' => $res['res']));
} else {
    succ(array('list' => array()));
}




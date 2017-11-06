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
/**
 * 获取游戏详情
 * @param string $gameIds 游戏ids
 * @param object $db
 * @return array
 */
function gameList($gameName, $page, $size, $db) {
    $where = '1 and status=0';
    if (!empty($gameName)) {
        $where.=" and name  like binary '%$gameName%'";
        $total =$db->field('count(*) as total')->where("$where")->select('game');
        $res = $db->field('*')->where("$where ")->limit($page, $size)->select('game'); //以后加缓存
    } else {
        $total = $db->field('count(*) as total')->where("$where ")->select('game');
        $res  = $db->field('*')->where("$where ")->limit($page, $size)->select('game'); //以后加缓存 
    }
    if ($res !== false) {
        return array('res' => $res, 'total' => $total ? $total[0]['total'] : '0');
    } else {
        return array('res' => array(), 'total' => '0');
    }
}

function gameZoneList($gameid, $db) {
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

function getGameList($gameName, $page, $size, $conf, $db) {
    $url = "http://" . $conf['domain-img'];
    $list = array();
    $res = gameList($gameName, $page, $size, $db);
    if ($res['res']) {
        foreach ($res['res'] as $v) {
			$v['gid'] = $v['gameid'];
            $v['gtid'] = $v['gametid'];
            $v['desc'] = $v["description"] ? $v['description'] : '暂无介暂';
            $v['icon'] = $v['icon'] ? $url . $v['icon'] : CROSS;
            $v['bg'] = $v['bgpic'] ? $url . $v['bgpic'] : CROSS;
            $v['pic'] = $v['poster'] ? $url . $v['poster'] : CROSS;
            array_push($list, $v);
        }
    }
    return array('res' => $list, 'total' => $res['total']);
}

/**
 * start
 */
$gameName = isset($_POST['gameName']) ? trim($_POST['gameName']) : '';
$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
$size = isset($_POST['size']) ? (int) $_POST['size'] : 10;
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

$page = checkInt($page);
$size = checkInt($size);
$res = getGameList($gameName, $page, $size, $conf, $db);
if ($res) {
    exit(json_encode(array('data' => $res['res'], 'total' => $res['total'])));
} else {
    exit(json_encode(array('data' => array(), 'total' => '0')));
}




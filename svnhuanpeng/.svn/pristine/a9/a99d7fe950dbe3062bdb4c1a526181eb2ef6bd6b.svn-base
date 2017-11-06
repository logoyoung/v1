<?php

/**
 * 重审直播标题
 * yandong@6rooms.com
 * date 2016-10-20 17:15
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

/**
 * 审核通过 更改live表中的字段
 * @param type $row
 * @param type $db
 * @return boolean
 */
function changeLiveTitle($row, $db)
{
    if (empty($row)) {
        return false;
    }
    foreach ($row as $v) {
        $data[$v['liveid']] = $v['title'];
    }
    $ids = implode(',', array_keys($data));
    $sql = "UPDATE live SET title = CASE liveid ";
    foreach ($data as $id => $nick) {
        $sql .= "WHEN $id THEN '$nick' ";
    }
    $sql .= "END WHERE liveid IN ($ids)";
    $res = $db->query($sql);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}


/*直播标题重新审核接口
 * @param string $succluid  //需要重审的直播I
 * @param $db
 * @return bool
 */
function UpdateLiveTitleStatus($succluid, $db)
{
    $temp=$list=array();
    if (empty($succluid)) {
        return false;
    }
    $tostatic = $db->where("liveid in ($succluid) ")->update('admin_live_title', array('status' => LIVE_TITLE_WAIT, 'utime' => date('Y-m-d H:i:s', time()))); //淇敼瀹℃牳鐘舵€侀€氳繃
    if ($tostatic !== false) {
        $res = getUidByLiveId($succluid, $db);
        if($res) {
            $nick = getUserInfo(array_unique(array_values($res)), $db);
            if(!empty($nick) && false !==$nick) {
                foreach ($res as $k => $v) {
                    $temp['liveid'] = $k;
                    $temp['title'] = isset($nick[$v]['nick']) ? $nick[$v]['nick'] . '的直播间' : '';
                    array_push($list, $temp);
                }
                if(!empty($list) && false !==$list){
                    $changeres=changeLiveTitle($list, $db);
                    if(!empty($changeres) && false !==$changeres){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    } else {
        return false;
    }


}

/**根据直播id获取uid
 * @param string $liveid  直播id
 * @param $db
 * @return array|bool
 */
function getUidByLiveId($liveid, $db)
{
    $list =array();
    if (empty($liveid)) {
        return false;
    }
    $res = $db->field('liveid,uid')->where("liveid in ($liveid)")->select('live');
    if (!empty($res) && false !== $res) {
        foreach ($res as $v) {
            $list[$v['liveid']]=$v['uid'];
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
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
$succluid = isset($_POST['succList']) ? trim($_POST['succList']) : ''; //liveid列表批量可用逗号隔开(重审的)

if (empty($uid) || empty($encpass) || empty($type) || empty($succluid)) {
    error(-1007);
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = UpdateLiveTitleStatus($succluid, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}
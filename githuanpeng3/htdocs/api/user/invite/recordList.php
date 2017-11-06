<?php
/**
 * 获取
 * 奖励列表
 * auchor  Dylan
 * date 2016-12-07 16:17
 */
include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();

/**获取抽奖列表
 * @param $uid
 * @param $db
 * @return array|bool
 */
function recordList($uid, $db)
{
    if (empty($uid)) {
        return false;
    }
    $res = $db->field("suid,ruid,ctime,status")->where("suid=$uid")->select("invite_record");
    if (false !== $res) {
        if ($res) {
            foreach ($res as $v) {
                $temp[$v['ruid']] = $v;
            }
            return $temp;
        } else {
            return array();
        }
    } else {
        return false;
    }
}

function getRecordNumber($uid, $db)
{
    if (empty($uid)) {
        return false;
    }
    $res = $db->field("count(*) as total")->where("suid =$uid  and status=1")->select('invite_record');
    if (false !== $res) {
        return $res[0]['total'] ? $res[0]['total'] : 0;
    } else {
        return false;
    }
}


/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error2(-4067,2);
}

$res = recordList($uid, $db);
if (false !== $res) {
    if ($res) {
        $list = array();
        $uids = array_column($res, 'ruid');
        $nick = getUserNicks($uids, $db);
        for ($i = 0, $k = count($uids); $i < $k; $i++) {
            $tmp['ruid'] = isset($res[$uids[$i]]['ruid']) ? $res[$uids[$i]]['ruid'] : '';
            $tmp['nick'] = isset($nick[$uids[$i]]) ? $nick[$uids[$i]] : '';
            $tmp['record'] = 500;
            $tmp['ctime'] = isset($res[$uids[$i]]) ? $res[$uids[$i]]['ctime'] : '0';
            $tmp['status'] = isset($res[$uids[$i]]) ? $res[$uids[$i]]['status'] : '0';
            array_push($list, $tmp);
        }
        $total = getRecordNumber($uid, $db);
        succ(array('list' => $list, 'total' => $total));
    } else {
        succ(array('list' => array(), 'total' => 0));
    }
} else {
    error(-5017);
}

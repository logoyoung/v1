<?php
/**
 * 获取邀请奖励排行
 * auchor  Dylan
 * date 2016-12-07 15:17
 */
include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();
$size = isset($_POST['size']) ? (int)$_POST['size'] : 5;
function ranking($size, $db)
{
    $res = $db->field("uid,sum(reward) as total ")->where("1 group by uid")->order("total desc ")->limit($size)->select("invite_reward_record");
    if (false !== $res) {
        if ($res) {
            foreach ($res as $v) {
                $temp[$v['uid']] = $v;
            }
            return $temp;
        } else {
            return array();
        }
    } else {
        return false;
    }
}
$res = ranking($size, $db);
if (false !== $res) {
    if ($res) {
        $list = array();
        $uids = array_column($res, 'uid');
        $nick = getUserNicks($uids, $db);
        for ($i = 0, $k = count($uids); $i < $k; $i++) {
            $tmp['nick'] = isset($nick[$uids[$i]]) ? $nick[$uids[$i]] : '';
            $tmp['total'] = isset($res[$uids[$i]]) ? $res[$uids[$i]]['total'] : '';
            array_push($list, $tmp);
        }
        $mark=md5(json_encode($list));
        succ(array('list' => $list,'mark'=>$mark));
    } else {
        succ(array('list' => array(),'mark'=>0));

    }
} else {
    error(-5017);
}



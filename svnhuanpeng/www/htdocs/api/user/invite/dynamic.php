<?php
/**
 * 奖励动态
 * auchor  Dylan
 * date 2016-12-07 16:17
 */
include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();
$time = 2;  //2小时
$size = isset($_POST['size']) ? (int)$_POST['size'] : 6;
$mark = isset($_POST['mark']) ? (int)$_POST['mark'] : 0;
function dynamic($size, $time, $db)
{
    $etime = date('Y-m-d H:i:s', time());
    $stime = date('Y-m-d H:i:s', time() - ($time * 3600));
    $res = $db->field("id,uid,reward")->where("ctime >= '$stime' and ctime <= '$etime'")->order('id DESC')->limit($size)->select("invite_reward_record");

    if (false !== $res) {
        if ($res) {
            return $res;
        } else {
            return array();
        }
    } else {
        return false;
    }
}

$res = dynamic($size, $time,$db);
if (false !== $res) {
    if ($res) {
        $list = array();
        $uids = array_column($res, 'uid');
        $nick = getUserNicks($uids, $db);
        foreach($res as $v){
            $tmp['lastId'] = $v['id'];
            $tmp['nick'] = isset($nick[$v['uid']]) ? $nick[$v['uid']] : '';
            $tmp['total'] = $v['reward'];
            array_push($list, $tmp);
        }
        $Lid=array_pop(array_column($list,'lastId'));
        if($mark==$Lid){
            succ(array('list' => array(),'mark'=>$Lid));
        }else{
            succ(array('list' => $list,'mark'=>$Lid));
        }

    } else {
        succ(array('list' => array(),'mark'=>0));
    }
} else {
    error(-5017);
}


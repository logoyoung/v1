<?php

include '../../../include/init.php';
/**
 * 获取首页直播推荐列表
 * date 2016-10-17 10:03 AM
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();


//function getLiveLists($uids, $size, $db)
//{
//    $rows = $db->field('liveid,uid,status,stream,server,orientation')
//        ->where("uid in($uids) and status=" . LIVE . "")->limit('' . $size . '')->select('live');
//    if (false !== $rows && !empty($rows)) {
//        foreach ($rows as $v) {
//            $temp[$v['uid']] = $v;
//        }
//        return $temp;
//    } else {
//        return array();
//    }
//}
//
//function getRecommentLists($db)
//{
//    $rows = $db->field('list')->where('client=2')->select('recommend_live');
//    if ($rows !== false && !empty($rows)) {
//        $rows = $rows[0]['list'];
//    } else {
//        $rows = array();
//    }
//    return $rows;
//}
//
//function getInfoByUidList($uids, $db)
//{
//    if (empty($uids)) {
//        return false;
//    }
//    $res = $db->field('uid,poster')->where("uid in ($uids)  and status=1")->select('admin_recommend_live');
//    if (false !== $res && !empty($res)) {
//        foreach ($res as $v) {
//            $temp[$v['uid']] = $v['poster'];
//        }
//        return $temp;
//    } else {
//        return array();
//    }
//}
//
//function getWaitAuthorList($db)
//{
//    $res = $db->field('uid,poster')->where('status=0')->order('ctime DESC')->select('admin_recommend_live');
//    if (false !== $res && !empty($res)) {
//        foreach ($res as $v) {
//            $temp[$v['uid']] = $v['poster'];
//        }
//        return $temp;
//    } else {
//        return array();
//    }
//}
//
///**获取已推荐主播最后一次的直播信息
// * @param string $uids 主播id串
// * @param $db
// * @return array|bool
// */
//function getRecommendAnchorLast($uids, $db)
//{
//    if (empty($uids)) {
//        return false;
//    }
//    $sql = "select liveid,uid,status,stream,server,orientation from (select * from live  order by ctime  desc) live where uid in ($uids)  group by uid order by ctime desc";
//    $res = $db->doSql($sql);
//    if (false !== $res && !empty($res)) {
//        foreach ($res as $v) {
//            $lives[$v['uid']] = $v;
//        }
//        return $lives;
//    } else {
//        return array();
//    }
//}
//
///**
// * start
// */
//$size = isset($_POST['size']) ? trim($_POST['size']) : LIVE_RECOMMENT_NUMBER;
//
//function recommendLiveList($db)
//{
//    $recommend = getRecommentLists($db);
//    if ($recommend) {
//        $info = getInfoByUidList($recommend, $db);
//        $order = explode(',', $recommend);
//        $res = getRecommendAnchorLast($recommend, $db);
//    } else {
//        $res = array();
//    }
//    if ($res) {
//        $recommendList = array();
//        for ($i = 0, $k = count($order); $i < $k; $i++) {
//            if (isset($res[$order[$i]])) {
//                $arr['uid'] = $res[$order[$i]]['uid'];
//                $arr['liveID'] = $res[$order[$i]]['liveid'];
//                $arr['stream'] = $res[$order[$i]]['stream'];
//                $arr['server'] = $res[$order[$i]]['server'];
//                if ($res[$order[$i]]['status'] == 100) {
//                    $arr['isLiving'] = 1;
//                } else {
//                    $arr['isLiving'] = 0;
//                }
//                $arr['poster'] = !empty($info[$order[$i]]) ? "http://" . $conf['domain-img'] . '/' . $info[$order[$i]] : CROSS;
//                $arr['orientation'] = $res[$order[$i]]['orientation'];
//            }
//
//            array_push($recommendList, $arr);
//        }
//        if ($recommendList) {
//            succ(array('list' => $recommendList));
//        } else {
//            succ(array('list' => array()));
//        }
//
//    } else {
//        succ(array('list' => array()));
//    }
//}
$res=recommendLiveList($db);
//var_dump($res);
succ($res);



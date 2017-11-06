<?php

include '../../../include/init.php';
/**
 * 获取广告列表
 * date 2016－11-30 11:21
 * author yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
/**获取广告
 * @param $type
 * @param $luid
 * @param $db
 * @return array|bool
 */
function getAdList($location, $luid, $size, $db)
{
    $where = "status=1 and location=$location";
    if ($luid) {
        $where .= "luid=$luid";
    }

    $res = $db->field('aid')->where("$where")->order("id DESC")->limit($size)->select('recommend_advertisement');
    if ($res !== false) {
        if (empty($res)) {
            return array();
        } else {
            return $res;
        }
    } else {
        return false;
    }
}

/**广告详情
 * @param $ids
 * @param $db
 * @return array|bool
 */
function getInfoByIds($ids, $db)
{
    if (empty($ids)) {
        return false;
    }
    $res = $db->field('id,url,poster')->where("id  in ($ids)")->select('admin_advertisement');
    if (false !== $res) {
        if (empty($res)) {
            return array();
        } else {
            foreach ($res as $v) {
                $temp[$v['id']] = $v;
            }
            return $temp;
        }
    } else {
        return false;
    }
}

/**
 * start
 */
$location = isset($_POST['location']) ? (int)($_POST['location']) : 0;//0首页广告 1直播间广告
$luid = isset($_POST['luid']) ? (int)($_POST['luid']) : 0;
$size = isset($_POST['size']) ? (int)($_POST['size']) : 2;
$res = getAdList($location, $luid, $size, $db);
if ($res) {
    $aids = array_column($res, 'aid');
    if ($aids) {
        $info = getInfoByIds(implode(',', $aids), $db);
        if ($info) {
            $list = array();
            for ($i = 0, $k = count($aids); $i < $k; $i++) {
                $temp['id'] = $info[$aids[$i]]['id'];
                $temp['url'] = $info[$aids[$i]]['url'];
                $temp['poster'] = $info[$aids[$i]]['poster'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $info[$aids[$i]]['poster'] : '';
                array_push($list, $temp);
            }
            succ(array('list' => $list));
        } else {
            succ(array('list' => array()));
        }
    } else {
        succ(array('list' => array()));
    }
} else {
    succ(array('list' => array()));
}
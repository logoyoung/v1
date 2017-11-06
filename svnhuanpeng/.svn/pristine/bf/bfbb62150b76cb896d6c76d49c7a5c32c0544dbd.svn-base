<?php

/**
 * 获取经纪公司列表
 * yandong@6rooms.com
 * date 2017-01-22 16:43
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
require '../../includeAdmin/Anchor.class.php';
$db = new DBHelperi_admin();
$anchorobj = new Anchor();
/**
 * 获取数据
 * @param obj $db
 * @return array()
 */
function Company_Income($month, $incomeType, $order, $keyword, $page, $size, $anchorobj, $issign, $db)
{
    $list = array();
    if ($issign) {
        $cid = 15;
    } else {
        $cid = 0;
    }
    if ($keyword) {
        $slist=array();
        $res = searchUserInfokeyWord($keyword, $db);
        if ($res['res']) {
            $ids = array_column($res['res'], 'uid');
            $checkIs = checkUserIsCompany(implode(',', $ids), $db);
            if ($checkIs) {
                for ($i = 0, $k = count($ids); $i < $k; $i++) {
                    if(array_key_exists($ids[$i], $checkIs)){
                        if ($cid == $checkIs[$ids[$i]]) {
                            $singid['uid'] = $ids[$i];
                            array_push($slist,$singid);
                        }
                    }
                }
                $uids = implode(',', array_column($slist,'uid'));
                $data['res']=$slist;
                $data['total']=count($slist);
            } else {
                $uids = 0;
            }
        } else {
            $uids = 0;
        }
    } else {
        $data = getCompanyAnchorInfo($cid, $page, $size, $db);
        $uids = array_column($data['res'], 'uid');
    }
    $length = $anchorobj->anchorLiveLength($uids, $month);
    if (!empty($length)) {
        $anchorInfo = $anchorobj->anchorInfo($uids);
        $roomids = $anchorobj->anchorRoomID($uids);
        $length = $anchorobj->anchorLiveLength($uids, $month);
        $popular = $anchorobj->anchorPopular($uids, $month);
        foreach ($data['res'] as $v) {
            $temp['uid'] = $v['uid'];
            $temp['nick'] = array_key_exists($v['uid'], $anchorInfo) ? $anchorInfo[$v['uid']]['nick'] : 0;
            $temp['bean'] = $v['bean'];
            $temp['coin'] = $v['coin'];
            if ($anchorInfo[$v['uid']]['pic']) {
                $temp['pic'] = array_key_exists($v['uid'], $anchorInfo) ? 'http://img.huanpeng.com' . $anchorInfo[$v['uid']]['pic'] : 0;
            } else {
                $temp['pic'] = '';
            }
            if (array_key_exists($v['uid'], $length)) {
                $temp['length'] = SecondFormat($length[$v['uid']]['length']);
            } else {
                $temp['length'] = '0天0小时0分钟0秒';
            }
//        $temp['length']=$length[$v['uid']]['length'];
            $temp['roomID'] = array_key_exists($v['uid'], $roomids) ? $roomids[$v['uid']]['roomid'] : 0;
            $temp['popularoty'] = array_key_exists($v['uid'], $popular) ? $popular[$v['uid']]['popular'] : 0;
            array_push($list, $temp);
        }
        return array('list' => $list, 'total' => $data['total'], 'keyword' => $keyword,);
    } else {
        return array('list' => array(), 'total' => 0, 'keyword' => $keyword);
    }

}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
$month = isset($_POST['month']) ? trim($_POST['month']) : '';
$issign = isset($_POST['issign']) ? (int)$_POST['issign'] : 0;
$incomeType = isset($_POST['incomeType']) ? trim($_POST['incomeType']) : 0;
$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
$order = isset($_POST['order']) ? (int)$_POST['order'] : 0;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$size = isset($_POST['size']) ? (int)$_POST['size'] : 10;

//if (empty($uid) || empty($encpass) || empty($type)) {
//    error(-1007);
//}
//
//$adminHelp = new AdminHelp($uid, $type);
//$err = $adminHelp->loginError($encpass);
//if ($err) {
//    error($err);
//}

$keyword = filterWords($keyword);
$res = Company_Income($month, $incomeType, $order, $keyword, $page, $size, $anchorobj, $issign, $db);
if ($res) {
    succ($res);
} else {
    error(-1014);
}



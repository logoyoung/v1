<?php

include '../init.php';
require(INCLUDE_DIR . 'LiveRoom.class.php');
include_once INCLUDE_DIR . 'User.class.php';

$conf = $GLOBALS['env-def'][$GLOBALS['env']];
/**
 * App端直播间信息
 * date 2016-05-09 11:04
 * anthor yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();

/**
 * 获取直播信息
 * @param int $anchorUserID
 * @param object $db
 * @return array
 */
function lastLive($anchorUserID, $uid, $conf, $db)
{
    getLiveServerList($streamServer, $notifyServer);
    $Anchor = new AnchorHelp($anchorUserID);
    $row = $db->where('uid =' . $anchorUserID . '  AND  status=' . LIVE . '')->limit('1')->select('live');
    if (empty($row)) {
        $anchorInfo = getUserInfo($anchorUserID, $db);
        $count = getLiveRoomUserCount($anchorUserID, $db);
        $fanes = getFansCount($anchorUserID, $db);
        $succ['anchorNickName'] = $anchorInfo[0]['nick'];
        $succ['anchorUserID'] = "$anchorUserID";
        $succ['angle'] = '0';
//        $succ['anchorLevel'] = getUserLevelByUid($anchorUserID, $db);
        $succ['anchorLevel'] = getAnchorLevel($anchorUserID, $db);
        $succ['anchorUserPicURL'] = $anchorInfo[0]['pic'] ? "http://" . $conf['domain-img'] . '/' . $anchorInfo[0]['pic'] : DEFAULT_PIC;
        $succ['streamInfo'] = array('streamList' => array(), 'orientation' => "0", 'stream' => "");
        $succ['viewerCount'] = $count;
        $succ['fansCount'] = $fanes;
    } else {
        $anchorInfo = getUserInfo($anchorUserID, $db);
        $count = getLiveRoomUserCount($anchorUserID, $db);
        $fanes = getFansCount($anchorUserID, $db);
        $succ['liveID'] = $row[0]['liveid'];
        $succ['anchorNickName'] = $anchorInfo[0]['nick'];
        $succ['anchorUserID'] = $row[0]['uid'];
        $succ['angle'] = $row[0]['orientation'] ? $row[0]['orientation'] : '0';
//        $succ['anchorLevel'] = getUserLevelByUid($row[0]['uid'], $db);
        $succ['anchorLevel'] = getAnchorLevel($row[0]['uid'], $db);
        $succ['anchorUserPicURL'] = $anchorInfo[0]['pic'] ? "http://" . $conf['domain-img'] . '/' . $anchorInfo[0]['pic'] : DEFAULT_PIC;
        $succ['streamInfo'] = array('streamList' => array($streamServer), 'orientation' => $row[0]['orientation'], 'stream' => $row[0]['stream']);
        $succ['viewerCount'] = $count;
        $succ['fansCount'] = $fanes;
    }
    $bean = $Anchor->getProperty();
    $anchorBean = $Anchor->exchangeToBean($bean['bean']);
    $succ['anchorBean'] = $anchorBean ? $anchorBean : 0;
    //直播等级
    $isCertify = $Anchor->getCertifyInfo();
    if ($isCertify['emailstatus'] == EMAIL_PASS && $isCertify['identstatus'] == RN_PASS && $isCertify['phonestatus'] == 1) {
        $succ['isCertify'] = "1";
    } else {
        $succ['isCertify'] = "0";
    }
    //宝箱
    $succ['treasure'] = array(
        'total' => 0,
        'list' => array(),
        'timeOut' => TREASURE_TIME_OUT
    );
    if($uid){
        $userHelp = new UserHelp($uid);
    }else{
        $userHelp = new UserHelp($anchorUserID);
    }
        $treasureList = $userHelp->getUnPickTreasureBoxInfoList($anchorUserID);
        if ($treasureList && is_array($treasureList)) {
            $succ['treasure']['total'] = count($treasureList);
            foreach ($treasureList as $key => $val) {
                $tmp['uid'] = $val['suid'];
                $tmp['trid'] = $val['id'];
                $tmp['ctime'] = strtotime($val['ctime']);
                $tmp['nick'] = getUserInfo($val['suid'], $db)[0]['nick'];
                array_push($succ['treasure']['list'], $tmp);
            }
        }

    return $succ;
}

/**
 * 获取直播中的主播列表
 * @param type $redisObj
 * @param type $db
 * @return array
 */
function getLiveLists($redisObj, $db)
{
    $cacheKey = 'GETLIVELISTS_YD';
    $getCatch = $redisObj->get($cacheKey); //取缓存
    if ($getCatch) {
        $luid = json_decode($getCatch, true);
    } else {
        $row = $db->field('uid,poster')->where("status=" . LIVE)->order('ctime DESC')->select('live');
        if ($row) {
            foreach ($row as $v) {
                $uid[] = $v['uid'];
                $pic[$v['uid']] = $v['poster'];
            }
            $luid = array('uid' => $uid, 'pic' => $pic);
            $redisObj->set($cacheKey, json_encode($luid), 60); //写缓存
        } else {
            $luid = array('uid' => array(), 'pic' => array());
        }
    }
    return $luid;
}

/**
 * 获取欢朋币&豆
 * @param type $uid
 * @param type $db
 * @return type
 */
function getCoinAndBean($uid, $db)
{
    $res = $db->field('hpbean,hpcoin')->where('uid=' . $uid)->select('useractive');
    return $res;
}

/**
 * 判断是不是房管
 * @param type $uid 用户id
 * @param type $anchorUserID 主播id
 * @param type $db
 * @return boolean
 */
function isHomeAdmin($uid, $anchorUserID, $db)
{
    $res = $db->field('uid')->where("luid=$anchorUserID and uid = $uid")->select('roommanager');
    if (isset($res[0]['uid'])) {
        return true;
    } else {
        return false;
    }
}

function is_Silenced($uid, $luid, $db)
{
    $time = date('Y-m-d H:i:s', time() - 3600);
    $sql = "select ctime from silencedlist where uid = $uid and luid=$luid and ctime >= '$time'";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();
    if ($row['ctime']) {
        return strtotime($row['ctime']);
    } else {
        return false;
    }
}

//开始
$anchorUserID = isset($_POST['luid']) ? (int)$_POST['luid'] : '';
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$user = '';
if (empty($anchorUserID)) {
    error(-4013);
}
if (!empty($uid) && !empty($encpass)) {
    //检查用户登陆状态
    $userState = checkUserState($uid, $encpass, $db);
    if (true !== $userState) {
        error($userState);
    }
    $user = 'user';
}
$succList = lastLive($anchorUserID, $uid, $conf, $db);
if ($user == 'user') {
    //判断目标uid是不是本身uid
    if ($uid == $anchorUserID) {
        $succList['groupid'] = '5'; //主播自己
        exit(jsone($succList));
    }
}
if ($uid) {
    $isFollow = isOneFollowOne($uid, $anchorUserID, $db);
    if ($isFollow) {
        $succList['isFollow'] = 1;
    } else {
        $succList['isFollow'] = 0;
    }
    $isadmin = isHomeAdmin($uid, $anchorUserID, $db);
    if ($isadmin) {
        $succList['groupid'] = '4'; //房管
    } else {
        $succList['groupid'] = '1'; //普通用户
    }
    $beanCoin = getCoinAndBean($uid, $db);
    if ($beanCoin) {
        $succList['bean'] = $beanCoin[0]['hpbean'] ? $beanCoin[0]['hpbean'] : '0';
        $succList['coin'] = $beanCoin[0]['hpcoin'] ? $beanCoin[0]['hpcoin'] : '0';
    }

    $Silenced = is_speakOk($uid, $anchorUserID, $db);
    if ($Silenced) {
        $succList['user_isSilence'] = '1'; //1:已经被禁言，0正常状态
        $succList['user_silenceOffTimestamp'] = "$Silenced"; //解禁的时间戳
    } else {
        $succList['user_isSilence'] = '0'; //1:已经被禁言，0正常状态
        $succList['user_silenceOffTimestamp'] = '0'; //解禁的时间戳
    }
} else {
    $succList['isFollow'] = 0;
    $succList['groupid'] = '1'; //普通用户
}
if ($succList) {
    exit(jsone($succList));
} else {
    exit(jsone(array('succList' => '')));
}

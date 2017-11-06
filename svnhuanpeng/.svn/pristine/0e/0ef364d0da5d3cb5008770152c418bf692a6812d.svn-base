<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/5/23
 * Time: 下午3:05
 */

require_once 'init.php';
include_once INCLUDE_DIR.'Anchor.class.php';
include_once INCLUDE_DIR.'User.class.php';
include_once INCLUDE_DIR .'LiveRoom.class.php';

/**
 * 通过房间号获取主播ID
 *
 * @param $rid
 * @param $db
 * @return bool
 */
function getLuidByRid($rid, $db)
{
    if(!$rid || !$db)
        return false;
    $sql = "select uid from roomid where roomid={$rid}";
    $res = $db->query($sql);
    if(!$res)
        return false;
    $row = mysqli_fetch_row($res);
    return isset($row[0])?(int)$row[0]:false;
}

header("Content-type:text/html;charset=utf-8");

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$db = new DBHelperi_huanpeng();

//roomid to luid


/*if(!(int)$_GET['luid'] && !(int)$_GET['roomid']){
    exit;
}*/
if((int)$_GET['luid'])
    $luid = (int)$_GET['luid'];
else if((int)$_GET['roomid'])
    $luid = getLuidByRid((int)$_GET['roomid'],$db);
else
    exit;

$anchorHelp = new AnchorHelp($luid, $db);
//if(!$anchorHelp->isAnchor()){
//    exit;
//}


if(!$anchorHelp->isAnchor()){
    include WEBSITE_TPL.'error-404.php';
}

if($anchorHelp->isBlack()){
    include WEBSITE_TPL.'error-404.php';
//    echo "该主播已被封禁";
//    echo '<meta http-equiv="Refresh" content="3; url='.WEB_ROOT_URL.'" />';
//    exit();
}

$userHelp = null;
$userInfo['isLogin'] = false;
$userInfo['isAnchor'] = false;

if((int)$_COOKIE['_uid'] && trim($_COOKIE['_enc'])){
    $userHelp = new UserHelp($_COOKIE['_uid'], $db);
    if(!$userHelp->checkStateError($_COOKIE['_enc'])){
        $userInfo['isLogin'] = true;
        $a = new AnchorHelp($_COOKIE['_uid'], $db);
        $userInfo['isAnchor'] = $a->isAnchor();
    }
}

if($userInfo['isLogin']){
    $userInfo['user'] = call_user_func(function($u, $luid, $db){
        $user['userID'] = $u->uid;

        $info = $u->getUsers();
        $user['nickName'] = $info['nick'];
        $user['pic'] = $info['pic'];

        $level = $u->getLevelInfo();
        $user['level'] = $level['level'];
        $user['integral'] = $level['integral'];

        $property = $u->getProperty();
        $user['hpbean'] = $property['hpbean'];
        $user['hpcoin'] = $property['hpcoin'];

        $user['levelIntegral'] = call_user_func(function($lvl, $db){
            $sql = "select integral from userlevel where level= $lvl";
            $res = $db->query($sql);
            $row = $res->fetch_assoc();

            return (int)$row['intrgral'];

        }, $user['level'], $db);

        $user['phonestatus'] = $u->getPhoneCertifyInfo()['status'];

        //$user['silenceTime'] = $u->isSilenced($_GET['luid']);
        $user['silenceTime'] = $u->isSilenced($luid);
        $user['isSilence'] = $user['silenceTime'] > 0 ? 1 : 0;

        if($user['userID'] == $luid){
            $user['groupid'] = 5;
        }else{
            if($u->isRoomAdmin($luid)){
                $user['groupid'] = 4;
            }else{
                $user['groupid'] = 1;
            }
        }

        $user['readsign'] = call_user_func(function($uid, $db){
            $sql = "select readsign from useractive where uid = $uid";
            $res = $db->query($sql);
            $row = $res->fetch_assoc();

            return (int)$row['readsign'];
        }, $u->uid, $db);

        return $user;
    }, $userHelp, $luid, $db);
}

//$anchorHelp = new AnchorHelp($_GET['luid'], $db);
//$lroom = new LiveRoom($_GET['luid'], $db);;
$lroom = new LiveRoom($luid, $db);

$room = call_user_func(function($anchorHelp, $lroom, $userHelp, $luid, $db){

    $room['anchorIncome'] = $anchorHelp->exchangeToBean((int)$anchorHelp->getProperty()['bean']);
    $level = $anchorHelp->getLevelInfo();

    $room['anchorLevel'] = $level['level'];
    $room['anchorIntegral']    = $level['integral'];

    $room['anchorLevelList'] = array();

    $anchorLevelList = $anchorHelp->getLevelInfoList();
    foreach($anchorLevelList as $key => $val){
        $room['anchorLevelList'][$val['level']] = $val['integral'];
    }

    $info = $anchorHelp->getUsers();
    $room['anchorNickName']   = $info['nick'];
    $room['anchorUserPicURL'] = $info['pic'];

    $room['anchorUserID'] = $anchorHelp->uid;

    $room["chatServer"] = call_user_func(function(){
        $conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $serverList = $conf['socket'];
        shuffle($serverList);

        return $serverList;
    });

    $room['fansCount'] = $anchorHelp->fansCount();

    $room['giftExp'] = call_user_func(function($a){
        $list = $a->getGiftInfo();
        $ret = array();
        foreach($list as $key => $val){
            $ret[$val['id']]['exp'] = $val['exp'];
            $ret[$val['id']]['money'] = $val['money'];
        }
        return $ret;
    }, $anchorHelp);

    $liveInfo = $anchorHelp->getMyLivingInfo();
    $room['liveID']      = $liveInfo['liveid'];
    $room['gameID']      = $liveInfo['gameid'];
    $room['gammeTypeID'] = $liveInfo['gametid'];
    $room['gameName']    = $liveInfo['gamename'] ? $liveInfo['gamename'] : '其他游戏';
    $room['status']      = $liveInfo['status'];
    $room['liveTitle']   = $liveInfo['title'] ? $liveInfo['title']:$room['anchorNickName']."的直播间";
    $room['isLiving']    = $liveInfo['status'] == 100 ? 1 : 0;

    $room['viewerCount'] = $lroom->getLiveUserCountFictitious();

    $room['manageList'] = $anchorHelp->myRoomManagerIdList();

    $room['isFollow'] = 0;

    $room['gameHistory'] = gameNameHistory($room['anchorUserID'], $db);
    $room['gameList'] = gameNameList($db);
    if($userHelp){
        $room['isFollow'] = (int)$userHelp->isFollow($room['anchorUserID']);
    }else{
        $userHelp = new UserHelp(LIVEROOM_ANONYMOUS);
    }

    $room['userLevelList'] = call_user_func(function($u){
        $list = $u->getLevelInfoList();
        $ulist = array();
        foreach($list as $key => $val){
            $ulist[$val['level']] = $val['integral'];
        }
        return $ulist;
    },$userHelp);

    $room['treasure'] = array(
        'total' => 0,
        'list'  => array(),
        'timeOut' => TREASURE_TIME_OUT
    );
	$mredis = new RedisHelp();
    $treasureList = $userHelp->getUnPickTreasureBoxInfoList($luid);
    if($treasureList && is_array($treasureList)){
		foreach ($treasureList as $key => $val) {
			if((int)$_COOKIE['_uid']){
				//检测是否失败领取过该宝箱
				$egmap = "envelope_map_".$val['id'];
				if($mredis->hget($egmap, (int)$_COOKIE['_uid']) != 0){
					mylog("box".$val['id']." is received", LOGFN_SENDGIFT_LOG);
					continue;
				}
			}

			$tmp['uid'] = $val['suid'];
			$tmp['trid'] = $val['id'];
			$tmp['ctime'] = strtotime($val['ctime']);
			$tmp['nick'] = getUserInfo($val['suid'], $db)[0]['nick'];
			array_push($room['treasure']['list'], $tmp);
		}

//		$succ['treasure']['total'] = count($succ['treasure']['list']);
//        $room['treasure']['total'] = count($treasureList);
//        foreach($treasureList as $key => $val){
//            $tmp['uid']   = $val['suid'];
//            $tmp['trid']  = $val['id'];
//            $tmp['ctime'] = strtotime($val['ctime']);
//            $tmp['nick']  = getUserInfo($val['suid'], $db)[0]['nick'];
//            array_push($room['treasure']['list'], $tmp);
//        }
		$room['treasure']['total'] = count($room['treasure']['list']);
    }
    return $room;
}, $anchorHelp, $lroom, $userHelp, $luid, $db);


function gameNameList($db){
    $sql = "select `name` from game";
    $res = $db->query($sql);
    $gamelist = array();
    while($row = $res->fetch_assoc()){
        array_push($gamelist, $row['name']);
    }

    return $gamelist;
}

$room['client']['bitrate'] = '1000';
$room['client']['width'] = '960';
$room['client']['height'] = '540';
$room['client']["needUpdateVersion"] = '20170228100230';

$room = '<script>var $ROOM = '.json_encode($room).';</script>';
$pageUser = '<script>var pageUser='.json_encode($userInfo).';</script>';
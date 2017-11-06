<?php
include '../init.php';
include INCLUDE_DIR . 'LiveRoom.class.php';
include_once INCLUDE_DIR . 'redis.class.php';
include_once INCLUDE_DIR . 'LRRank.class.php';
$db = new DBHelperi_huanpeng();

function is_hasLiving($luid, $liveid, $db)
{
    $sql = "select liveid from live where uid = $luid order by liveid desc limit 1";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();

    if (!$row['liveid'])
        return false;

    if ($liveid != $row['liveid'])
        return false;

    return true;
}

function is_gift($gid, $type, $db)
{
    $sql = "select id,money, giftname, exp from gift where id = $gid and type = $type";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();

    if (!$row['id'])
        return false;

    return $row;

}

function updateUserLevel($uid, $exp, $db)
{
    $res = $db->query("select level, integral from useractive where uid = $uid");
    $lv = $res->fetch_assoc();

    $res = $db->query('select max(level) as level from userlevel');
    $row = $res->fetch_assoc();
    $maxLevel = $row['level'];

    $exp = $exp + $lv['integral'];

    if ($lv == $maxLevel) {
        $db->query("update useractive set integral=$exp where uid = $uid");
        return true;
    }

    $res = $db->query("select * from userlevel where integral >= $exp order by level limit 1");
    $row = $res->fetch_assoc();
    $level = $row['level'];
    if ($level) {
        $db->query("update useractive set integral=$exp, level=$level where uid = $uid");
        return true;
    } else {
        $db->query("update useractive set integral=$exp, level=$maxLevel where uid = $uid");
        return true;
    }
}

function getMyProperty($uid, $db)
{
    $sql = "select hpcoin, hpbean from useractive where uid = $uid";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();

    return $row;
}

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$luid = isset($_POST['luid']) ? (int)$_POST['luid'] : 0;
$liveid = isset($_POST['liveid']) ? (int)$_POST['liveid'] : 0;
$gid = isset($_POST['gid']) ? (int)$_POST['gid'] : 0;
$num = isset($_POST['num']) ? (int)$_POST['num'] : 0;


if (!$uid || !$enc || !$luid || !$liveid || !$gid)
    exit(json_encode(array('code' => -1, 'desc' => '参数错误')));

$code = checkUserState($uid, $enc, $db);
if ($code !== true)
    exit($code);

$type = 2;
$num = 1;
$property = getMyProperty($uid, $db);


if (!is_hasLiving($luid, $liveid, $db))
    exit(jsone(array('code' => -2, 'desc' => '无次直播')));

$gift = is_gift($gid, $type, $db);
if (!$gift)
    exit(jsone(array('code' => -3, 'desc' => '礼物类型错误')));

$money = $gift['money'];

$amount = $money * $num;
$myBalance = $property['hpcoin'];

if ($myBalance < $amount)
    exit(jsone(array('code' => -4, 'desc' => '余额不足')));

$phone = get_userPhoneCertifyStatus($uid, $db);
if ($phone['phonestatus'] == 0) {
//	exit(josne(array('code' => -5, 'desc' => '请先认证手机')));
}

//白名单  //测试账户不能给平台用户送礼，只能给测试用户送礼
if (in_array($uid, array(WHITE_LIST))) {
    if (in_array($luid, array(WHITE_LIST))) {
        $isWhite = false;
    } else {
        $isWhite = true;
    }
} else {
    $isWhite = false;
}

if ($isWhite) {
    exit(jsone(array('isSuccess' => 1, 'cost' => $amount)));
} else {
    $sendGiftID = time() . random(6, 1);

//验证完毕 进行费用扣除以及纪录的填写
    $db->autocommit(false);
    $db->query('begin');

    $giftRecord = $db->query("insert into giftrecordcoin(id,luid,liveid,uid,giftid,giftnum) values('$sendGiftID',$luid,$liveid,$uid,$gid,$num)");
//	更新交易纪录表
    $income = $amount;
    $transactionRecord = $db->query("insert into billdetail(customerid, purchase, beneficiaryid,income,type,info) values($uid,$amount,$luid,$income,0,'$sendGiftID')");
    $updateBalance = $db->query("update useractive set hpcoin = hpcoin - $amount where uid = $uid and hpcoin >= $amount and hpcoin = $myBalance");

    $insertTreasure = true;

    if ($gid == 35) {
        $sql = "insert into treasurebox(uid, luid) value($uid, $luid)";
        if (!$db->query($sql)) {
            $insertTreasure = false;
        } else {
            $treasureid = $db->insertID;
        }

    }

    if (!$giftRecord || !$transactionRecord || !$updateBalance || !$insertTreasure) {
        $db->rollback();
        exit(jsone(array('code' => -5, desc => '系统繁忙，请稍后再试')));
    } else {
        $db->commit();
        $db->autocommit(true);

        $res = $db->query("select nick from userstatic where uid = $uid");
        $row = $res->fetch_assoc();
        $nick = $row['nick'];

        $lroom = new LiveRoom($luid);

        $msg = array(
            't' => 504,
            'tm' => time(),
            'ouid' => $uid,
            'ounn' => $nick,
            'gid' => $gid,
            'gnum' => $num,
            'gnm' => $gift['giftname']
        );

        $lroom->sendRoomMsg(json_encode($msg));

        $exp = $num * $gift['exp'];
        updateUserLevel($uid, $exp, $db);
        //更新主播的贡献值
        updateAnchorLevel($luid, $exp, $db);

        $redis = new RedisHelp();
        $rRank = new RankUpdate($luid, $redis, $db, $lroom);
        $rRank->intoRankList($uid, $amount);

        if ($gid == 35) {
            $res = $db->query("select nick from userstatic where uid = $luid");
            $row = $res->fetch_assoc();
            $lunick = $row['nick'];
            $content = array(
                't' => 535,
                'tm' => time(),
                'uid' => $uid,
                'unick' => $nick,
                'luid' => $luid,
                'lunick' => $lunick,
                'gname' => $gift['giftname'],
                'treasureid' => $treasureid
            );
            $lroom->sendAllMsg(json_encode($content));
        }
        exit(jsone(array('isSuccess' => 1, 'cost' => $amount)));
    }
}

function updateAnchorLevel($luid, $exp, $db)
{
    $res = $db->query("select level, integral from anchor where uid = $luid");
    $lv = $res->fetch_assoc();

    $res = $db->query('select max(level) as `level` from anchorlevel');
    $row = $res->fetch_assoc();

    $maxLevel = $row['level'];
    $exp = $exp + $lv['integral'];

    if ($lv == $maxLevel) {
        $db->query("update anchor set inegral=$exp where uid = $luid");
        $res = $db->query("select integral from anchorlevel where `level` =  $lv");
        $row = $res->fetch_assoc();
        $level = $lv;
    } else {
        $res = $db->query("select * from anchorlevel where integral >= $exp order by level limit 1");
        $row = $res->fetch_assoc();
        $level = $row['level'];
        if ($level) {
            $db->query("update anchor set integral=$exp, level=$level where uid = $luid");
        } else {
            $db->query("update anchor set integral=$exp, level=$maxLevel where uid = $luid");
        }
    }

    $anchorlevel['level'] = $level;
    $anchorlevel['percent'] = $exp / $row['integral'];

    return $anchorlevel;
}

function sendall($content, $luid, $db)
{
    $sql = "select uid from live where uid != $luid group by uid";
    $res = $db->query($sql);
    while ($row = $res->fetch_assoc()) {
        $sluid = $row['uid'];
        $lroom = new LiveRoom($sluid);
        $lroom->sendRoomMsg(json_encode($content));
    }
}

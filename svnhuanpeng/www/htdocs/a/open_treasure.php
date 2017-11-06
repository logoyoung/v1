<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/7
 * Time: 上午10:06
 */

include '../init.php';
include_once INCLUDE_DIR . 'redis.class.php';
include_once INCLUDE_DIR . 'LiveRoom.class.php';

define('TREASURE_BOX', LOG_DIR."/openTreasure.log");
$TIME_LIMIT = '600';
//设置当前总人数
//设置中奖率
$db = new DBHelperi_huanpeng();
$mredis = new redishelp();

$debug = true;

$pre = array(
    'box_status' => 'open_treasure_',
    'envelope_status' => 'envelope_stat_',
    'envelope_list' => 'envelope_list_',
    'envelope_get_map' => 'envelope_map_'
);

$treasureid = isset($_POST['treasureid']) ? (int)$_POST['treasureid'] : 0;
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$luid = isset($_POST['luid']) ? (int)$_POST['luid'] : 0;


if (!$uid || !$treasureid || !$enc || !$luid) {
    error(-4013);
}

$userHple = new UserHelp($uid, $db);
if ($err = $userHple->checkStateError($enc)) {
    error($err);
}

if (!isTreasureBox($treasureid, $db)) {
    error(-4049);
}

if ($luid != getLuidFromTreasure($treasureid, $db)) {
    error(-4050);
}

if (!isTreasurePickTime($treasureid, $db)) {
    error(-4048);
}


$treasureObj = array(
    'status' => $pre['box_status'] . $treasureid,
    'estatus' => $pre['envelope_status'] . $treasureid,
    'elist' => $pre['envelope_list'] . $treasureid,
    'egmap' => $pre['envelope_get_map'] . $treasureid
);

//检测当前宝箱状态，是否已经领取完
$tr_status = (int)$mredis->get($treasureObj['status']);
mylog('check tr status is :'. $tr_status, TREASURE_BOX);
if (!$tr_status) {
    if (!treasureStatus($treasureid, $db)) {
        $mredis->set($treasureObj['status'], '1');
    } else {
        error(-4055);
    }
}

$lroom = new LiveRoom($luid, $db);

//生成红包列表
$envelope_stat = (int)$mredis->get($treasureObj['estatus']);
mylog('check tr envelope_stat is :'. $envelope_stat, TREASURE_BOX);
if (!$envelope_stat) {
    $ret = redEnvelopeList($lroom);
    if (!$ret || !is_array($ret)) {
//        exit(json_encode("-4"));
        error(-5025);
    }
    mylog('check tr redEnvelopeList is :'. json_encode($ret), TREASURE_BOX);
    foreach ($ret as $k => $v) {
        $mredis->rpush($treasureObj['elist'], "$v");
    }
    $mredis->set($treasureObj['estatus'], "1");
}


//检查长度
$red_list = $mredis->lranges($treasureObj['elist'], 0, -1);
mylog('check tr red_list is :'. json_encode($red_list), TREASURE_BOX);
$red_list_length = count($red_list);
if ($red_list_length <= 0) {
    setTreasureStatusClosed($treasureid, $treasureObj, $TIME_LIMIT, $mredis, $db);
    error(-4055);
}


$tryGetRedEnvelope = "if redis.call('hexists',KEYS[2],KEYS[3]) ~= 0 then\n" . "return '-1'\n" . "else\n" . "local hongbao = redis.call('lpop', KEYS[1])\n" . "if hongbao then\n" . "redis.call('hset', KEYS[2], KEYS[3], hongbao)\n" . "return hongbao\n" . "else\n" . "redis.call('hset', KEYS[2], KEYS[3], '-1')\n" . "return nil\n" . "end\n" . "end\n" . "return nil";

$ret = $mredis->evals($tryGetRedEnvelope, array($treasureObj['elist'], $treasureObj['egmap'], "$uid"), 3);

mylog('check tr get num is :'. $ret, TREASURE_BOX);

$ret = (int)$ret;
if ($ret < 0) {
    error(-4055);
}
if (setTreasureGetNum($uid, $luid, $treasureid, $ret, $db)) {
    //领取成功
    if ($ret >= 0) {
        if($red_list_length - 1 <= 0){
            setTreasureStatusClosed($treasureid, $treasureObj, $TIME_LIMIT, $mredis, $db);
        }
        $db->query("update useractive set hpbean=hpbean+$ret where uid=$uid");
        $nick = getUserInfo($uid, $db);
        $content = array(
            "t" => 511,
            "tm" => time(),
            "num" => "$ret",
            "uid" => $uid,
            "unick" => $nick[0]['nick']
        );

        $lroom->sendRoomMsg(json_encode(toString($content)));

    }
    $snick = call_user_func(function($id, $db){
        $sql = "select uid from treasurebox where id = $id";
        $res = $db->query($sql);
        $row = $res->fetch_assoc();
        $uid = $row['uid'];

        $nick = getUserInfo($uid, $db);
        return $nick[0]['nick'];
    },$treasureid, $db);
    $property = $userHple->getProperty();
    exit(json_encode(array('isSuccess' => 1, 'num' => "$ret", 'nick' => $snick,'coin'=>(int)$property['hpcoin'], 'bean'=>(int)$property['hpbean'])));
}


//------------------------------------------
function setTreasureStatusClosed($treasureid, $treasureobj, $timeLimit, $redis, $db){
    $redis->set($treasureobj['status'], '0');
    setTreasureStatus($treasureid, 1, $db);
    closedStatusClearRedis($treasureobj, $timeLimit, $redis);
}

function closedStatusClearRedis($treasureObj, $timeLimit, $redis){
    foreach($treasureObj as $value){
        $redis->expire($value, $timeLimit);
    }
}

function isTreasureBox($treasureid, $db)
{
    $sql = "select id from treasurebox WHERE  id = $treasureid";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();

    if ((int)$row['id'])
        return true;
    else
        return false;
}

function getLuidFromTreasure($treasureid, $db)
{
    $sql = "select luid from treasurebox WHERE  id = $treasureid";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();

    return (int)$row['luid'];
}

function isTreasurePickTime($treasureid, $db)
{
    $sql = "select ctime from treasurebox where id = $treasureid";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();

    if (time() - strtotime($row['ctime']) >= TREASURE_TIME_OUT) {
        return true;
    }

    return false;
}

function treasureStatus($treasureid, $db)
{
    $sql = "select status from treasurebox where id = $treasureid";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();

    return (int)$row['status'];
}

function setTreasureStatus($treasureid, $status, $db)
{
    $sql = "update treasurebox set status=$status where id = $treasureid";
    return $db->query($sql);
}


function redEnvelopeList($lroom)
{
    $money = 5000;
    $member = $lroom->getRoomUserCount();
    $percent = 4;
    $count = (int)($member / $percent);

    if ($count < 100) {
        $count = 100;
    } elseif ($count > 1000) {
        $count = 1000;
    }

    $count = 2;

    $avg = (int)($money / $count);
    $rang = (int)($avg / 10);

    $max = $avg + $rang;
    $min = $avg - $rang;

    return generate($money, $count, $max, $min);
}

function setTreasureGetNum($uid, $luid, $treasureid, $num, $db)
{
    return $db->query("insert into pickTreasure(uid, luid,treasureid, getNum) value($uid, $luid, $treasureid, $num)");
}

/**
 * [generate description]
 *
 * @param  [type] $total [红包总额]
 * @param  [type] $count [红包个数]
 * @param  [type] $max   [每个小红包最大额]
 * @param  [type] $min   [每个小红包最小额度]
 *
 * @return [type]        [岑房声称的每个小红包值的数组]
 */
function generate($total, $count, $max, $min)
{
    $average = (int)($total / $count);
    $result = array();

    for ($i = 0; $i < $count; $i++) {
        //因为小红包的数量通常是要比大红包的数量要多的，因为这里的概率要调换过来。
        //当随机数>平均值，则产生小红包
        //当随机数<平均值，则产生大红包
        if (random_nextLong($min, $max) > $average) {
            $temp = $min + xRandom($min, $average);
            array_push($result, $temp);
            $total -= $temp;
        } else {
            $temp = $min + xRandom($average, $max);
            array_push($result, $temp);
            $total -= $temp;
        }

    }
    // return $result;
    //如果还有余钱，则尝试加到小红包里，如果加不进去，则尝试下一个。
    while ($total > 0) {
        for ($i = 0; $i < $count; $i++) {
            if ($total > 0 && $result[$i] < $max) {
                $result[$i]++;
                $total--;
            }
        }
    }

    // 如果钱是负数了，还得从已生成的小红包中抽取回来
    while ($total < 0) {
        for ($i = 0; $i < $count; $i++) {
            if ($total < 0 && $result[$i] > $min) {
                $result[$i]++;
                $total--;
            }
        }
    }

    return $result;
}

function xRandom($min, $max)
{
    return (int)sqrt(rand(0, pow($max - $min, 2)));
}

function random_nextLong($min, $max)
{
    return rand($min, $max + 1);
}



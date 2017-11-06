<?php

include '../init.php';
$db = new DBHelperi_huanpeng();

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/3/30
 * Time: 下午7:54
 */


/**
 * 检测用户是否存在
 * @param string $userName
 * @param object $db
 * @return bool
 */
function isUserNameExist($userName, $db) {
    $res = $db->field('username')->where("username = '$userName'")->select('userstatic');
    return $res ? true : false;
}

/**
 * 检测昵称是否被占用
 * @param string $userName
 * @param object $db
 * @return array
 */
function theNickCanBeUsed($userNick, $db) {
    $res = $db->field('nick')->where("nick = '$userNick'")->select('userstatic');
    return $res;
}

/**
 * 检测用户是否设置昵称
 * @param string $userName
 * @param object $db
 * @return array
 */
function isSetNick($userName, $db) {
    $res = $db->field('uid,nick,encpass')->where("username = '$userName'")->select('userstatic');
    return $res;
}

/**
 * 获取用户uid and encpass
 * @param int $staticRes
 * @param object $db
 * @return array
 */
function getUserUidandEncByUid($staticRes, $db) {
    $res = $db->field('uid,encpass')->where("uid = $staticRes")->select('userstatic');
    return $res;
}

/**
 * 第三方用户入库
 * @param type $userName
 * @param type $openid
 * @param type $userPic
 * @param type $userNick
 * @param type $db
 * @return boolean
 */
function createUser($userName, $openid, $userPic, $userNick, $db) {
    //检测昵称是否被占用
    $checkres = theNickCanBeUsed($userNick, $db);
    if ($checkres) {
        error(-4006);
    } else {
        $nick = $userNick;
    }
    $userPic=GrabImage($userPic);//头像入库
    $staticData = array(
        'username' => $userName,
        'password' => md5password($openid),
        'nick' => $nick,
        'pic' => $userPic,
        'rip' => ip2long(fetch_real_ip($rport)),
        'rport' => $rport,
        'rtime' => get_datetime(),
        'encpass' => md5(md5($openid)),
        'sex' => 1
    );
    $staticRes = $db->insert('userstatic', $staticData);
    if ($staticRes) {
        $activeDate = array(
            'uid' => $staticRes,
            'lip' => ip2long(fetch_real_ip($lport)),
            'lport' => $lport,
            'ltime' => get_datetime()
        );
        $activeRes = $db->insert('useractive', $activeDate);
        if ($activeRes) {
            if (empty($checkres)) {
                $userInfo = getUserUidandEncByUid($staticRes, $db);
                if ($userInfo) {
                    $result = array('code' => 200, 'uid' => $userInfo[0]['uid'], 'encpass' => $userInfo[0]['encpass']);
                } else {
                    return false;
                }
            } else {
                $result = array('code' => -200, 'uid' => $staticRes);
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
    return $result;
}
/**
 * 回调函数
 * @param string $channel
 * @param string $client
 * @param string $openid
 * @param string $userNick
 * @param string $userPic
 * @param object $db
 */
function callback($channel, $client, $openid, $userNick, $userPic, $db) {
    $userName = strtolower($channel) . "_" . $openid;
    if (isUserNameExist($userName, $db)) {
        $issetRes = isSetNick($userName, $db);
        if (!empty($issetRes[0]['nick'])) {
            $res = array('errCode' => '0', 'uid' => $issetRes[0]['uid'], 'encpass' => $issetRes[0]['encpass']);
        } else {
            $res = array('errCode' => '1', 'uid' => $issetRes[0]['uid']);
        }
    } else {
        $callRes = createUser($userName, $openid, $userPic, $userNick, $db);
        if ($callRes) {
            if ($callRes['code'] == 200) {
                $res = array('errCode' => '0', 'uid' => $callRes['uid'], 'encpass' => $callRes['encpass']);
            }
            if ($callRes['code'] == -200) {
                $res = array('errCode' => '1', 'uid' => $callRes['uid']);
            }
        } else {
            error(-984);
        }
    }
    exit(json_encode($res));
}
$data = $_GET;
$res=verifySign($data,SECRET_KEY);
exit(json_encode(array('pdata'=>$data,'old'=>$data['sign'],'new'=>$res['sign'],'data'=>$res['data'])));
if (!verifySign($data,SECRET_KEY)) {
	exit(json_encode(array('err' => -111 , 'desc' => '认证失败')));
}

$channel = isset($_GET['channel']) ? $_GET['channel'] : '';
$client = isset($_GET['client']) ? trim($_GET['client']) : '';
$openid = isset($_GET['openid']) ? trim($_GET['openid']) : '';
$userNick = isset($_GET['userNick']) ? trim($_GET['userNick']) : '';
$userPic = isset($_GET['userPic']) ? trim($_GET['userPic']) : '';


if (!$channel || !$client || !$openid || !$userNick || !$userPic) {
    error(-993);
}
if (!in_array(strtolower($channel), array('weibo', 'weixin', 'qq'))) {
    error(-983);
}
callback($channel, $client, $openid, $userNick, $userPic, $db);

<?php

session_start();
include '../init.php';
require_once INCLUDE_DIR . 'redis.class.php';
require_once INCLUDE_DIR . 'class.geetestlib.php';
$db = new DBHelperi_huanpeng();
$redisObj = new RedisHelp();
$passWord = isset($_POST['password']) ? trim($_POST['password']) : '';
$userName = isset($_POST['userName']) ? trim($_POST['userName']) : '';
$client = isset($_POST['client']) ? (int) ($_POST['client']) : '';
$code = isset($_POST['identCode']) ? trim(($_POST['identCode'])) : '';
if (empty($userName) || empty($passWord)) {
    error(-4013);
}
$userNameRes = checkMobile($userName);
if (true !== $userNameRes) {
    error(-4058);
}
$passWord = filterData($passWord);
$mkey="LogInNumber:$userName";
if ($redisObj->get($mkey) >= 3) {//连续登录三次失败,开启验证码校验
    $conf = $GLOBALS['env-def'][$GLOBALS['env']];
//    setcookie('_login_identCode_open',1,0,'/main', $conf['domain']);
        if($_POST['type']=='gt'){
            $GtSdk = $_POST['client'] =='1' ? new GeetestLib(CAPTCHA_APP_ID, PRIVATE_APP_KEY) : new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
            $user_id = $_SESSION['user_id'];
            if ($_SESSION['gtserver'] == 1) {
                $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $user_id);
                if (!$result) {
                    error(-4031);
                }
            }else{
                if (!$GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
                    error(-4031);
                }
            }
        }else{
            if (empty($code) || $code != $_SESSION['logIn_code'] ) {
                error(-4031);
            }
        }

}
$row = $db->field('uid,password')->where("phone =$userName")->select('userstatic');
if (empty($row)) {
    error(-4059);
} else {
    //验证密码
    if ($row[0]['password'] === md5password($passWord)) {
        $staticData = array('encpass' => md5(md5($passWord)));
        $activeData = array('lip' => ip2long(fetch_real_ip($lport)), 'lport' => $lport, 'ltime' => get_datetime());
        $staticRes = $db->where('uid=' . $row[0]['uid'] . '')->update('userstatic', $staticData);
        $activeRes = $db->where('uid=' . $row[0]['uid'] . '')->update('useractive', $activeData);
        if ($staticRes && $activeRes) { //同步任务
            if (in_array($client, array(1))) {
                $keys = "IsFirstLoginfromApp:" . $row[0]['uid'];
                $res = $redisObj->get($keys);
                if (!$res) {
                    $redisObj->set($keys, 1); //设置标志
                    synchroTask($row[0]['uid'], 36, 0, 200, $db); //同步到task表中
                }
            }
          
                $redisObj->del($mkey); //清空登录计数
                setcookie($mkey, '', time() - 1);
     
            curl_post(array('uid'=>$row[0]['uid'],'encpass'=>$staticData['encpass']), "http://dev.huanpeng.com/main/a/checkUserExistNewMsg.php");//同步系统消息(待优化)
            setUserLoginCookie($row[0]['uid'], $staticData['encpass']);
            exit(jsone(array('uid' => $row[0]['uid'], 'encpass' => $staticData['encpass'])));
        } else {
            error(-5017);
        }
    } else {
        $redisObj->increment($mkey); //登录失败计数
        $number = $redisObj->get($mkey);
        $redisObj->expire($mkey, 60);//先设置为60秒
        setcookie($mkey, $number);
        if ($number >= 3) {
            error(-4061);
        } else {
            error(-996);
        }
    }
}

   
        
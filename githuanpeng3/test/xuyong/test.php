<?php
require __DIR__.'/../../include/init.php';
//

class UserRegister
{

    public function getDb() {
        return new DBHelperi_huanpeng();
    }

    public function getUserBindStatus($uid = '69348',$channel = '')
    {
        $s  = getUserBindStatus($uid,$channel,$this->getDb());
        var_dump($s);
    }

    public function threeSideLogin( $channel='qq', $channelID = 999)
    {

        include_once INCLUDE_DIR . 'loginSDK/qq/qqConnectAPI.php';
        include_once INCLUDE_DIR . "User.class.php";
        include_once INCLUDE_DIR . "redis.class.php";
        include_once INCLUDE_DIR . 'loginSDK/ThreePartyLogin.php';
        //qq
        include_once INCLUDE_DIR . 'loginSDK/qq/qqConnectAPI.php';
        //weibo
        include INCLUDE_DIR . 'loginSDK/weibo/config.php';
        include INCLUDE_DIR . 'loginSDK/weibo/saetv2.ex.class.php';

        $openid = uniqid();
        $res = [
            'unionid'  => 't_nick1',
            // 'nickname' => 't_three'.mt_rand(1, 100),
            'nickname' => 't_three',
            'nickname' => 't_three17',
        ];
        $db = $this->getDb();
        $result = threeSideLogin($openid, $channel, $res, $db,$channelID );
        var_dump($result);
    }
}

$obj = new UserRegister();
$obj->threeSideLogin();

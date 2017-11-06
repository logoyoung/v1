<?php
namespace service\cookie;
use service\user\UserDataService;
use lib\User;
use service\user\UserAuthService;

class CookieService {

    /**
     *  获取用户uid
     * @return int
     */
    public static function getUid()
    {
        return isset($_COOKIE['_uid']) ? (int) $_COOKIE['_uid'] : 0;
    }

    /**
     * 获取 enc
     * @return string
     */
    public static function getEnc()
    {
        return isset($_COOKIE['_enc']) ? trim($_COOKIE['_enc']) : '';
    }


    public static function setUid($uid)
    {
        hpsetCookie('_uid',$uid);
    }

    public static function setUserNick($nick)
    {
        hpsetCookie('_unick',$nick);
    }

    public static function getUserNick()
    {
        return isset($_COOKIE['_unick']) ? trim($_COOKIE['_unick']) : '';
    }

    public static function setUserFace($uface)
    {
        hpsetCookie('_uface', $uface);
    }

    public static function getUserFace()
    {
        return isset($_COOKIE['_uface']) ? trim($_COOKIE['_uface']) : '';
    }

    public static function setUserProperty($property)
    {
        hpsetCookie('_uproperty',$property);
    }

    public static function getUserProperty()
    {
        return isset($_COOKIE['_uproperty']) ? trim($_COOKIE['_uproperty']) : '';
    }

    public static function getUserPhoneStatus()
    {
        return isset($_COOKIE['_phonestatus']) ? (int) $_COOKIE['_phonestatus'] : 0;
    }

    public static function setUserPhoneStatus($status)
    {
        hpsetCookie('_phonestatus',$status);
    }

    //获取用户信息
    public static function getUserInfo()
    {
        return isset($_COOKIE['_uinfo']) ? trim($_COOKIE['_uinfo']) : '';
    }

    //设置用户信息
    public static function setUserInfo($userinfo)
    {
         hpsetCookie('_uinfo',$userinfo);
    }

    public static function get($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : false;
    }

    public static function set($name,$val)
    {
        hpsetCookie($name,$val);
    }

    public static function init()
    {
        if(!isset($_COOKIE['_uid']))
        {
            hpsetCookie('_uid',LIVEROOM_ANONYMOUS + rand(200000000,299999999));
        }

        if(!isset($_COOKIE['_enc']))
        {
            hpsetCookie('_enc','');
        }

        if(!self::getUid() || !self::getEnc())
        {
            return true;
        }

        $auth = new UserAuthService();
        $auth->setUid(self::getUid());
        $auth->setEnc(self::getEnc());
        //校验encpass、用户 登陆状态
        if($auth->checkLoginStatus() !== true)
        {
            //获取校验结果
            $result    = $auth->getResult();
            //错误码
            $errorCode = $result['error_code'];
            //错误消息
            $errorMsg  = $result['error_msg'];
            //假如是封禁的，可以获取禁时间
            $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
            $uid       = self::getUid();
            write_log("notice|uid:{$uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|class:".__CLASS__,'auth_access');
            hpsetCookie('_uid',LIVEROOM_ANONYMOUS + rand(200000000,299999999));
            hpsetCookie('_enc','');
            return ;
        }

        $userDataService = new UserDataService();
        $userDataService->setCaller('class:'.__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__);
        $userDataService->setUid(self::getUid())->setEnc(self::getEnc());
        $userInfo = $userDataService->setUserInfoDetail(UserDataService::USER_STATIC_ACTICE_BASE)->getUserInfo();
        if(!$userInfo){
            return;
        }

        //设置用户昵称
        if(!self::getUserNick())
        {
            self::setUserNick($userInfo['nick']);
        }

        //用户头像
        if(!self::getUserFace() || self::getUserFace() != $userInfo['pic'])
        {
            self::setUserFace($userInfo['pic']);
        }

        //用户资产
        if(!self::getUserProperty())
        {

            $coin = $userInfo['hpcoin'];
            $bean = $userInfo['hpbean'];
            self::setUserProperty($bean.':'.$coin);
        }

        //用户手机认证状态
        if(!self::getUserPhoneStatus())
        {
            self::setUserPhoneStatus((isset($userInfo['phone']) && $userInfo['phone']) ? 1 : 0);
        }

        $cookieUserInfo = [$userInfo['level'], $userInfo['integral'],$userInfo['readsign'],$userInfo['sex']];
        $cookieUserInfo = implode(':', $cookieUserInfo);
        self::setUserInfo($cookieUserInfo);

        if(!isset($_GET['login']) || (int) $_GET['login'] != 1)
        {
           return true;
        }

        if($_GET['ref_url'])
        {
            $ref_url = urldecode(trim($_GET['ref_url']));
            header("Location: ".$ref_url);
            return true;
        }

        header("Location:".WEB_ROOT_URL);
        return true;
    }


}
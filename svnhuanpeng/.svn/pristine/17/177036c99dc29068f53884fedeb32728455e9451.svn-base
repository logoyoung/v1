<?php

namespace service\home;

use lib\User;
use service\user\UserDataService;
use service\game\GameService;
use service\user\FollowService;
use service\user\HistoryService;
use service\user\UserAuthService;

/**
 * 头部header服务类
 * @author longgang@6.cn
 * @date 2017-04-13 17:19:25
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */
class HeaderService
{

    //默认页数
    const DEFAULT_PAGE = 1;
    //头部用户观看历史记录
    const HEADER_HISTORY_NUM = 5;
    //头部用户关注记录
    const HEADER_FOLLOW_NUM = 3;
    //头部游戏分类游戏个数
    const HEADER_GAME_NUM = 12;
    //获取头部游戏分类列表失败
    const ERROR_HEADER_GAME = 730001;
    //获取用户信息失败
    const ERROR_USER_INFO = 730002;
    //获取用户财产失败
    const ERROR_USER_PREPERTY = 730003;
    //获取历史记录失败
    const ERROR_HISTORY_LIST = 730004;
    //获取关注失败
    const ERROR_FOLLOW_LIST = 730005;

    public static $errorMsg = [
        self::ERROR_HEADER_GAME => '获取头部游戏分类列表失败',
        self::ERROR_USER_INFO => '获取用户信息失败',
        self::ERROR_USER_PREPERTY => '获取用户财产失败',
        self::ERROR_HISTORY_LIST => '获取历史记录失败',
        self::ERROR_FOLLOW_LIST => '获取关注失败',
    ];
    //头部分类游戏字段
    private static $gameColumns = [
        'gameID' => 'gameid',
        'gameName' => 'name'
    ];
    private $_uid;
    private $_enc;

    public function setUid($uid)
    {
        $this->_uid = $uid;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setEnc($enc)
    {
        $this->_enc = $enc;
        return $this;
    }

    /**
     * 头部游戏分类列表
     * @return array
     */
    public function getGameList()
    {
        $gameDataService = new GameService();
        $gameDataService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);
        $gameDataService->setColumn(self::$gameColumns);
        $gameDataService->setSize(self::HEADER_GAME_NUM);
        $allGameList = $gameDataService->getAllGameList();
        if (!$allGameList)
        {
            $code = self::ERROR_HEADER_GAME;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        } else
        {
            return $allGameList;
        }
    }

    /**
     * 查看用户是否为主播
     * @return bool
     */
    public function isAnchor()
    {
        $userDataService = new UserDataService();
        $userDataService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);
        $userDataService->setUid($this->_uid);
        return $userDataService->isAnchor();
    }

    /**
     * 获取用户信息
     * @return array
     */
    public function getUserInfo()
    {
        $data = [];
        $userDataService = new UserDataService();
        $userDataService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);
        $userDataService->setUid($this->_uid);
        $userDataService->setUserInfoDetail(UserDataService::USER_STATIC_ACTICE_BASE);
        $userInfo = $userDataService->getUserInfo();

        if (!$userInfo)
        {
            $code = self::ERROR_USER_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:" . $this->_uid . "|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }

        $data['uid'] = isset($userInfo['uid']) ? (int) $userInfo['uid'] : 0;
        $data['head'] = isset($userInfo['pic']) ? trim($userInfo['pic']) : '';
        $data['hpbean'] = isset($userInfo['hpbean']) ? (int) $userInfo['hpbean'] : 0;
        $data['hpcoin'] = isset($userInfo['hpcoin']) ? (int) $userInfo['hpcoin'] : 0;
        $data['integral'] = isset($userInfo['integral']) ? $userInfo['integral'] : '';
        $data['level'] = isset($userInfo['level']) ? (int) $userInfo['level'] : 0;
        $data['levelIntegral'] = isset($userInfo['level_to_integral']) ? (int) $userInfo['level_to_integral'] : 0;
        $data['nick'] = $userInfo['nick'];
        $data['unreadMsg'] = $userInfo['readsign'];
        $lastLevelIntegral = ($data['level'] - 1) > 0 ? get_user_integral_by_level($data['level'] - 1) : 0;
        $data['levelIntegral'] = $data['levelIntegral'] - $lastLevelIntegral;
        $data['integral'] = $data['integral'] - $lastLevelIntegral;
        $data['gapIntegral'] = ceil($data['levelIntegral'] - $data['integral']);
        return $data;
    }

    /**
     * 获取历史记录
     * @return array
     */
    public function getHistoryList()
    {
        $historyService = new HistoryService();
        $historyService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);
        $historyService->setUid($this->_uid);
        $historyService->setPage(self::DEFAULT_PAGE);
        $historyService->setSize(self::HEADER_HISTORY_NUM);
        $historyList = $historyService->getHistoryList();

        if (!$historyList)
        {
            $code = self::ERROR_HISTORY_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:" . $this->_uid . "|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        } else
        {
            return $historyList;
        }
    }

    /**
     * 获取关注
     * @return array
     */
    public function getFollowList()
    {
        $followDataService = new FollowService();
        $followDataService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);
        $followDataService->setUid($this->_uid);
        $followDataService->setSize(self::HEADER_FOLLOW_NUM);
        $followList = $followDataService->getFollowList();

        if (!$followList)
        {
            $code = self::ERROR_FOLLOW_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:" . $this->_uid . "|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        } else
        {
            return $followList;
        }
    }

    /**
     * 获取首页所有数据
     * @return array
     */
    public function getAll()
    {
        $data = [];
        $data['gameList'] = $this->getGameList();

        if (!$this->checkLogin())
        {
            $data['LoginStatus'] = 0;
            return $data;
        }

        $data['LoginStatus'] = 1;
        $data['isAnchor'] = $this->isAnchor() ? 1 : 0;
        $data['userInfo'] = $this->getUserInfo();
        $data['history'] = $this->getHistoryList();
        $data['follow'] = $this->getFollowList();

        return $data;
    }

    /**
     * 检验用户是否登录
     * @return bool
     */
    public function checkLogin()
    {
        if(!$this->_uid || !$this->_enc)
        {
            return false;
        }

        $auth = new UserAuthService();
        $auth->setUid($this->_uid);
        $auth->setEnc($this->_enc);
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
            write_log("notice|uid:{$this->_uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|class:".__CLASS__,'auth_access');

            return false;
        }

        return true;
    }

}

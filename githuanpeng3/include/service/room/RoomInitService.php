<?php
namespace service\room;
use service\common\AbstractService;
use service\anchor\AnchorDataService;
use service\room\RoomManagerService;
use service\cookie\CookieService;
use service\user\UserDataService;
use service\room\LiveRoomService;
use service\gift\GiftService;
use service\game\GameService;
use service\task\TreasureService;
use Exception;
use service\user\UserAuthService;
use service\anchor\AnchorGetDataService;
use service\live\StreamDataService;
use service\follow\FollowManagerService;
use service\follow\FollowDataService;
use service\live\LiveService;

/**
 * 直播间服务
 * @author xuyong <[xuyong@6.cn]>
 * @data 2017-4-18
 * @version 1.0.1
 */

class RoomInitService extends AbstractService
{

    //pc调用平台
    const PLATFORM_PC            = 1;
    //客户端使用平台
    const PLATFOMR_MOB           = 2;

    //rid转uid error
    const ERROR_CODE_RID_TO_LUID = -20001;
    //获取用户信息异常
    const ERROR_USER_INFO        = -20002;
    //获取用户个人资产异常
    const ERROR_USER_PROPERTY    = -20003;
    //获取等级积分异常
    const ERROR_LEVEL_INTEGRAL   = -20004;
    //无效的房间号
    const ERROR_ROOM_INVALID     = -20005;

    private $luid;
    private $anchor;
    private $anchorDataService;
    private $roomManagerService;
    private $userDataService;
    private $liveRoomService;
    private $giftService;
    private $roomid;
    private $_uid;
    private $_enc;
    private $_platform;
    private $_liveService;

    public $pcUserInfo = [
        'isLogin'  => false,
        'isAnchor' => false
    ];

    public $mobData = [];

    public $pcRoomInfo = [];

    public static $errorMsg = [
        self::ERROR_CODE_RID_TO_LUID => 'rid转luid error',
        self::ERROR_USER_INFO        => '获取用户信息异常',
        self::ERROR_USER_PROPERTY    => '获取用户个人资产异常',
        self::ERROR_LEVEL_INTEGRAL   => '获取等级积分异常',
        self::ERROR_ROOM_INVALID     => '无效的房间号',
    ];

    /**
     * 设置访问uid
     * @param
     */
    public function setUuid($uid)
    {
        $this->_uid = $uid;
    }

    /**
     * 获取访客uid
     * @return int
     */
    public function getUuid()
    {
        return $this->_uid;
    }

    public function setEnc($enc)
    {
        $this->_enc = $enc;
        return $this;
    }

    public function getEnc()
    {
        return $this->_enc;
    }

    //主播luid
    public function setLuid($luid)
    {
        $this->luid = $luid;
        return $this;
    }

    public function getLuid()
    {
        return $this->luid;
    }

    public function setRoomid($roomid)
    {
        $this->roomid = $roomid;
        return $this;
    }

    public function getRoomid()
    {
        return $this->roomid;
    }

    /**
     * 设置使用平台 pc |
     * @param int
     */
    public function setPlatform($platform)
    {
        $this->_platform = $platform;
        return $this;
    }

    /**
     * 获取使用平台
     * @return int
     */
    public function getPlatform()
    {
        return $this->_platform ? $this->_platform : self::PLATFORM_PC;
    }

    public function init()
    {

        $caller = __CLASS__.'func:'.__FUNCTION__;
        $this->anchorDataService  = new AnchorDataService();
        $this->anchorDataService->setCaller($caller);
        $this->roomManagerService = new RoomManagerService();
        $this->roomManagerService->setCaller($caller);
        $this->userDataService    = new UserDataService();
        $this->userDataService->setCaller($caller);
        $this->liveRoomService    = new LiveRoomService();
        $this->liveRoomService->setCaller($caller);
        $this->giftService        = new GiftService();
        $this->giftService->setCaller($caller);

        if(!$this->luid)
        {
            //如果是roomid的转换为uid
            $this->luid = $this->roomManagerService->setRoomid($this->roomid)->getUidByRoomid();
            if(!$this->luid)
            {
                $code = self::ERROR_ROOM_INVALID;
                $msg  = self::$errorMsg[$code];
                $log  = "notice|error_code:{$code};msg:{$msg};luid:{$this->luid}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
                write_log($log,'room_init_service_access');
                return false;
            }
        }

        $this->setLuid($this->luid);
        //设置主播uid
        $this->anchorDataService->setUid($this->luid);

        switch ($this->getPlatform())
        {
            case self::PLATFORM_PC:
                //是否是认证主播
                if(!$this->_checkIsAnchor())
                {
                    return false;
                }

                break;

            case self::PLATFOMR_MOB:
            default:
                break;
        }

        return true;
    }

    /**
     * 获取pc 用户信息
     * @return array
     */
    public function getPcUserInfo()
    {
        //初始化获取 cookie 一些信息
        CookieService::init();
        $this->_uid = CookieService::getUid();
        $this->_enc = CookieService::getEnc();
        if($this->_uid == 0 || !$this->_enc)
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

        //设置用户uid
        $this->userDataService->setUid($this->_uid);
        //设置获取详细资料
        $this->userDataService->setUserInfoDetail(UserDataService::USER_STATIC_ACTICE_BASE);
        //用户登陆状态
        $this->pcUserInfo['isLogin']  = true;
        //是否为主播
        $this->pcUserInfo['isAnchor'] = $auth->checkAnchorCertStatus() ? 1 : 0;

        $userInfo = $this->userDataService->getUserInfo();
        if(!$userInfo)
        {
            $code = self::ERROR_USER_INFO;
            $msg  = self::$errorMsg[$code];
            $log  = "error|error_code:{$code};msg:{$msg};uid:{$this->_uid}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log,'room_init_service_access');
            return false;
        }

        $pcUserInfo             = [];
        $pcUserInfo['userID']   = $this->_uid;
        $pcUserInfo['nickName'] = $userInfo['nick'];
        $pcUserInfo['pic']      = $userInfo['pic'];
        $pcUserInfo['level']    = $userInfo['level'];
        $pcUserInfo['integral'] = $userInfo['integral'];
        $pcUserInfo['hpbean']   = $userInfo['hpbean'];
        $pcUserInfo['hpcoin']   = $userInfo['hpcoin'];
        $pcUserInfo['levelIntegral'] = $userInfo['level_to_integral'];

        //手机是否认证
        $pcUserInfo['phonestatus'] = $userInfo['phone'] ? 1 : 0;
        $this->roomManagerService->setUid($this->luid);
        $this->roomManagerService->setTargetUid($this->_uid);
        $this->roomManagerService->setManagerUid($this->_uid);
        //获取用户禁言状态
        $userSilencedStatus        = $this->roomManagerService->isSilenced();
        //是否被禁言
        $pcUserInfo['isSilence']   = ($userSilencedStatus === true) ? 0 : 1;
        //禁言时间
        $pcUserInfo['silenceTime'] = $pcUserInfo['isSilence'] ? $userSilencedStatus : 0;
        //分组 角色
        $pcUserInfo['groupid']     = $this->roomManagerService->getGroupId();
        $pcUserInfo['readsign']    = $userInfo['readsign'];
        $this->pcUserInfo['user']  = $pcUserInfo;

        return $this->pcUserInfo;
    }

    //获取房间信息
    public function getPcRoomInfo()
    {
        $anchorService  = new AnchorGetDataService();
        $anchorService->setUid($this->getLuid());
        $anchorData     = $anchorService->getAnchorData();
        //获取主播金豆数
        $this->pcRoomInfo['anchorIncome']     = isset($anchorData['bean']) ? round($anchorData['bean'], 1) : 0;
        //获取主播等级
        $this->pcRoomInfo['anchorLevel']      = $anchorData['level'];
        //经验值
        $this->pcRoomInfo['anchorIntegral']   = $anchorData['integral'];
        //获取所有等级的列表
        $this->pcRoomInfo['anchorLevelList']  = $anchorService->getAnchorLevelList();
        unset($anchorData);
        //获取主播信息
        $anchorInfo = $this->userDataService->setUid($this->luid)->getUserInfo();
        if($anchorInfo === false)
        {
            $log  = "error |获取主播信息异常;uid:{$this->luid}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log,'room_init_service_access');
        }
        $this->pcRoomInfo['anchorNickName']   = $anchorInfo['nick'];
        $this->pcRoomInfo['anchorUserPicURL'] = $anchorInfo['pic'];
        $this->pcRoomInfo['anchorUserID']     = $anchorInfo['uid'];
        $followDataService               = new FollowDataService;
        //粉丝人数
        $this->pcRoomInfo['fansCount']   = $followDataService->setUid($this->luid)->getFansTotalNum();
        //所有礼品
        $this->pcRoomInfo['giftExp']     = $this->giftService->getGiftList();
        //获取最后一场直播信息
        $multiStream                     = StreamDataService::getMultiStreamByAnchorUid($this->getLuid());
        $lastLive                        = isset($multiStream['master']) ? $multiStream['master'] : [];
        $this->pcRoomInfo['liveID']      = isset($lastLive['liveid'])    ? $lastLive['liveid']    : '';
        $this->pcRoomInfo['gameID']      = isset($lastLive['gameid'])    ? $lastLive['gameid']    : '';
        $this->pcRoomInfo['gammeTypeID'] = isset($lastLive['gametid'])   ? $lastLive['gametid']   : '';
        $this->pcRoomInfo['gameName']    = isset($lastLive['gamename'])  ? $lastLive['gamename']  : '其他游戏';
        $this->pcRoomInfo['status']      = isset($lastLive['status'])    ? $lastLive['status']    : '';
        $this->pcRoomInfo['liveTitle']   = isset($lastLive['title'])     ? $lastLive['title']    : $this->pcRoomInfo['anchorNickName']."的直播间";
        //是否直播
        $this->pcRoomInfo['isLiving']    = (isset($lastLive['playtype']) && $lastLive['playtype'] == 1) ? 1 : 0;
        $lastLive                        = null;
        $multiStream                     = null;
        $this->liveRoomService->setCaller(__CLASS__.'func:'.__FUNCTION__.';line:'.__LINE__);
        $this->liveRoomService->setLuid($this->getLuid());
        $socketServer                    = $this->liveRoomService->getSocketServer();
        //聊天socket服务
        $this->pcRoomInfo["chatServer"]  = $socketServer['serverList'];
        //虚拟观看人数
        $this->pcRoomInfo['viewerCount'] = $this->liveRoomService->getLiveUserCountFictitious();
        //获取房管列表
        $this->pcRoomInfo['manageList']  = $this->getManageList();
        //获取最近直播的五场游戏名称
        //$this->pcRoomInfo['gameHistory'] = $this->anchorDataService->getHistoryGameName();

        $gameService = new GameService();
        $gameService->setCaller(__CLASS__.'func:'.__FUNCTION__.';line:'.__LINE__);
        $this->pcRoomInfo['gameList']    = $gameService->setColumn('name')->getAllGameList();
        $this->pcRoomInfo['isFollow']    = 0;

        if($this->pcUserInfo['isLogin'])
        {
            $followManagerService         = new FollowManagerService;
            //用户对主播关注状态
            $this->pcRoomInfo['isFollow'] = $followManagerService->setUid($this->_uid)->setObjectUid($this->luid)->isFollow();
        }

        //所有的用户等级信息
        $this->pcRoomInfo['userLevelList'] = $this->userDataService->getUserLevelInfoList();
        //获取该房间尚未领取的宝箱ID列表
        $this->pcRoomInfo['treasure']      = $this->getUsableTreasureBoxList();
        //统计主播时间使用
        $this->pcRoomInfo['basetime']      = LiveRoomService::getBasetime();
        //统计主播时间使用
        $this->pcRoomInfo['randtime']      = LiveRoomService::getRandtime();
        //获取直播间活动信息
        $this->pcRoomInfo['promotions']    = RoomManagerService::getRoomPromotions();

        $this->pcRoomInfo['client'] = [
                'bitrate'           => '1000',
                'width'             => '960',
                'height'            => '540',
                'needUpdateVersion' => '20170228100230',
        ];

        return $this->pcRoomInfo;
    }

    //是否为主播
    private function _checkIsAnchor()
    {

        $auth = new UserAuthService();
        $auth->setUid($this->getLuid());
        //主播封禁状态
        if($auth->checkDisableLoginStatus() !== true)
        {
            //获取校验结果
            $result    = $auth->getResult();
            //错误码
            $errorCode = $result['error_code'];
            //错误消息
            $errorMsg  = $result['error_msg'];
            //假如是封禁的，可以获取禁时间
            $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
            write_log("notice|uid:{$this->getLuid()};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|class:".__CLASS__,'auth_access');

            return false;
        }

        //是否是认证主播
        if(!$auth->checkAnchorCertStatus())
        {
            write_log("notice|uid:{$this->getLuid()};非认证主播|class:".__CLASS__,'auth_access');
            return false;
        }

        return true;
    }

    /**
     * 获取客户端房间数据
     * @return [type] [description]
     */
    public function getMobRoomData()
    {
        $this->mobData = [];
        $this->mobData['isFollow']    = '0';
        //获取客户端主播信息
        $this->mobData['anchor']      = $this->getMobAnchorInfo();
        $this->roomManagerService->setCaller(__CLASS__.'func:'.__FUNCTION__.';line:'.__LINE__);
        //房间roomID
        $this->mobData['roomID']      = $this->roomManagerService->setUid($this->getLuid())->getRoomidByUid();
        ///获取主播收益
        $this->mobData['roomIncome']  = $this->mobData['anchor']['roomIncome'];
        //获取房管列表
        $this->mobData['manageList']  = $this->getManageList();
        $this->mobData['user']        = [
            'hpbean'              => '0',
            'hpcoin'              => '0',
            'groupid'             => '1',
            'isSilence'           => '0',
            'silenceOffTimestamp' => '0',
        ];

        $auth = new UserAuthService();
        if($this->getUuid() && $this->getEnc() && ($auth->setUid($this->getUuid())->setEnc($this->getEnc())->checkLoginStatus() === true))
        {
            //获取客户端访客信息
            $this->mobData['user']        = $this->getMobUserInfo();
            $followManagerService         = new FollowManagerService;
            //用户对主播关注状态
            $this->mobData['isFollow']    = $followManagerService->setUid($this->getUuid())->setObjectUid($this->getLuid())->isFollow();
        }

        //获取最后一场直播信息
        $multiStream               = StreamDataService::getMultiStreamByAnchorUid($this->getLuid());
        $lastLive                  = isset($multiStream['master']) ? $multiStream['master'] : [];
        $this->mobData['luid']     = isset($lastLive['uid'])       ? $lastLive['uid']       : $this->getLuid();
        $this->mobData['liveID']   = isset($lastLive['liveid'])    ? $lastLive['liveid']    : '0';

        if(isset($lastLive['title']))
        {
            $this->mobData['title'] = $lastLive['title'];
        } else
        {
            $this->mobData['title'] = isset($this->mobData['anchor']['nick']) ? $this->mobData['anchor']['nick'].'的直播间' : '';
        }

        //h5使用
        $this->mobData['poster']    = (isset($lastLive['poster']) && $lastLive['poster'])     ? $lastLive['poster']   : '';
        //摄像头直播信息
        $slaveLive                  = (isset($multiStream['slave']) && $multiStream['slave']) ? $multiStream['slave'] : [];
        //摄像头封面图
        $this->mobData['slave_poster'] = isset($slaveLive['poster']) ? $slaveLive['poster'] : '';
        //h5使用
        $this->mobData['web_socket'] = LiveService::getWebSocketServer();
        $this->mobData['gameName']   = isset($lastLive['gamename']) ? $lastLive['gamename'] : '其他游戏';
        //角度
        $this->mobData['orientation'] = isset($lastLive['orientation']) ? $lastLive['orientation'] : 0;
        $this->liveRoomService->setCaller(__CLASS__.'func:'.__FUNCTION__.';line:'.__LINE__);
        $this->liveRoomService->setLuid($this->getLuid());
        //观看人数
        $this->mobData['viewCount']  = $this->liveRoomService->getLiveUserCountFictitious();
        //直播流信息（流服务服务器地址+流信息）
        $stream                      = StreamDataService::getOldMasterStreamByMultiStream($multiStream);
        $stream['streamList']        = $stream['streamList'] ? $stream['streamList'] : [];
        $this->mobData['isLiving']   = $stream['isLiving'];
        //去掉客户羰不需要的liveID
        unset($stream['liveID']);
        $this->mobData['streamInfo'] = $stream;
        //这里有master(游戏直播流信息) slave(摄相头直播流信息)
        $this->mobData['streamMultiInfo'] = $multiStream;
        //用户未领取的宝箱ID列表
        $this->mobData['treasure']   = $this->getUsableTreasureBoxList();
        //统计主播时间使用
        $this->mobData['basetime']   = LiveRoomService::getBasetime();
        //统计主播时间使用
        $this->mobData['randtime']   = LiveRoomService::getRandtime();
        //获取直播间活动信息
        $this->mobData['promotions'] = RoomManagerService::getRoomPromotions();
        return $this->mobData;
    }

    /**
     * 获取客户端主播信息
     * @return array
     */
    public function getMobAnchorInfo()
    {
        $mobAnchorInfo = [
            'nick'      => '',     //主播昵称
            'level'     => '',     //主播等级
            'head'      => '',     //主播头像
            'fansCount' => '0', //关注人数
            'isCertify' => '0', //已认证1，未认证0
            'is_cert'   => '0', //是否开启陪玩
            'roomIncome' => '0',
        ];

        $auth = new UserAuthService();
        $auth->setUid($this->getLuid());

        //主播封禁状态
        if($auth->checkDisableLoginStatus() !== true)
        {
            //获取校验结果
            $result    = $auth->getResult();
            //错误码
            $errorCode = $result['error_code'];
            //错误消息
            $errorMsg  = $result['error_msg'];
            //假如是封禁的，可以获取禁时间
            $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
            write_log("notice|uid:{$this->getLuid()};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|class:".__CLASS__,'auth_access');

            return $mobAnchorInfo;
        }

        //获取主播信息
        $this->userDataService->setCaller(__CLASS__.'func:'.__FUNCTION__.';line:'.__LINE__);
        $anchorInfo    = $this->userDataService->setUid($this->getLuid())->getUserInfo();
        if(!$anchorInfo)
        {
            return $mobAnchorInfo;
        }

        //主播昵称
        $mobAnchorInfo['nick']  = $anchorInfo['nick'];
        //主播头像
        $mobAnchorInfo['head']  = $anchorInfo['pic'];
        $this->anchorDataService->setCaller(__CLASS__.'func:'.__FUNCTION__.';line:'.__LINE__);
        //获取主播等级｜经验值
        $anchorService          = new AnchorGetDataService();
        $anchorService->setUid($this->getLuid());
        $anchorData             = $anchorService->getAnchorData();
        $mobAnchorInfo['roomIncome'] =  isset($anchorData['bean']) ? round($anchorData['bean'], 1) : 0;
        //主播等级
        $mobAnchorInfo['level'] = isset($anchorData['level']) ? (int) $anchorData['level'] : 1;
        $followDataService      = new FollowDataService;
        //关注人数
        $mobAnchorInfo['fansCount'] = $followDataService->setUid($this->getLuid())->getFansTotalNum();
        //是否是认证主播 已认证1，未认证0
        $mobAnchorInfo['isCertify'] = $auth->checkAnchorCertStatus() ? 1 : 0;
        //是否开启陪玩
        $mobAnchorInfo['is_cert']   = $auth->checkIsDueAnchor()      ?  1 : 0;
        return $mobAnchorInfo;

    }

    /**
     * 获取客户端用户未领取的宝箱ID列表
     * @return array
     */
    public function getUsableTreasureBoxList()
    {
        $treasureService = new TreasureService();
        $treasureService->setCaller(__CLASS__.'func:'.__FUNCTION__.';line:'.__LINE__);
        $treasureService->setLuid($this->getLuid());
        $treasureService->setUid($this->getUuid());
        $treasureInfo    = $treasureService->getUnReceiveTreasureBoxInfoList();

        //相同的功能客户跟pc端字段相差很,请多注意了
        if($this->getPlatform() != self::PLATFOMR_MOB)
        {
            return $treasureInfo;
        }

        $mobTreasureInfo            = [];
        $mobTreasureInfo['total']   = isset($treasureInfo['count']) ? $treasureInfo['count'] : 0;
        $mobTreasureInfo['timeOut'] = $treasureInfo['timeOut'];
        $mobTreasureInfo['list']    = [];

        foreach ($treasureInfo['list'] as $k => $v)
        {
            $tmp = $v;
            unset($tmp['trid']);
            $tmp['treasureID']           = $v['trid'];
            $mobTreasureInfo['list'][$k] = $tmp;
            $tmp = [];
        }

        $treasureInfo = null;

        return $mobTreasureInfo;

    }

    /**
     * 获取房管list
     * @return array
     */
    public  function getManageList()
    {
        $this->roomManagerService->setCaller(__CLASS__.'func:'.__FUNCTION__.';line:'.__LINE__);
        $manageList = $this->roomManagerService->setUid($this->getLuid())->setPage(1)->setSize(1000)->getRoomManagerList();
        if(!$manageList)
        {
            return [];
        }

        return $manageList;
    }

    /**
     * 获取客户端访客信息
     * @return array
     */
    public function getMobUserInfo()
    {

        $data = [
            'hpbean'    => '0',
            'hpcoin'    => '0',
            'groupid'   => '1',
            'isSilence' => '0',
            'silenceOffTimestamp' => '0',
        ];

        $this->userDataService->setCaller(__CLASS__.'func:'.__FUNCTION__.';line:'.__LINE__);
        //获取用户财产（欢朋豆|欢朋币）
        $userInfo = $this->userDataService->setUid($this->getUuid())->setUserInfoDetail(UserDataService::USER_ACTICE_BASE)->getUserInfo();
        $data['hpbean'] = isset($userInfo['hpbean']) ? $userInfo['hpbean'] : 0;
        $data['hpcoin'] = isset($userInfo['hpcoin']) ? $userInfo['hpcoin'] : 0;
        $this->roomManagerService->setCaller(__CLASS__.'func:'.__FUNCTION__.';line:'.__LINE__);
        //获取用户禁言状态
        $userSilencedStatus = $this->roomManagerService->setUid($this->getLuid())->setTargetUid($this->getUuid())->isSilenced();
        //是否被禁言
        $data['isSilence']  = ($userSilencedStatus === true) ? 0 : 1;
        //禁言时间
        $data['silenceOffTimestamp'] = $data['isSilence'] ? $userSilencedStatus : 0;
          //分组 角色
        $data['groupid']    = $this->roomManagerService->setManagerUid($this->getUuid())->getGroupId();

        return $data;
    }
}
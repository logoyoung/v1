<?php

include '../../../include/init.php';

use service\user\UserDataService;
use service\user\UserAuthService;
use service\anchor\AnchorDataService;
use service\room\RoomManagerService;
use service\anchor\AnchorApplyService;
use service\live\LiveLengthService;

/**
 * 主播直播时长接口
 */
class anchorLiveTime
{

    private $_uid;
    private $_enc;
    private $_month;
    private $_day;

    //参数错误
    const ERROR_USER_PARAM = -10001;
    //登录状态不对
    const ERROR_USER_LOGIN = -10002;
    //不是主播
    const ERROR_IS_ANCHOR = -10003;
    //获取主播时长信息失败
    const ERROR_ANCHOR_LIVE_TIME = -10004;
    //获取主播信息异常
    const ERROR_ANCHOR_INFO = -10005;
    //获取主播直播时长信息异常
    const ERROR_ANCHOR_LIVE_LENGTH_INFO = -10006;


    public static $errorMsg = [
        self::ERROR_USER_PARAM => '参数错误',
        self::ERROR_USER_LOGIN => '登录状态不对',
        self::ERROR_IS_ANCHOR => '不是主播',
        self::ERROR_ANCHOR_LIVE_TIME => '获取主播时长信息失败',
        self::ERROR_ANCHOR_INFO => '获取主播信息异常',
        self::ERROR_ANCHOR_LIVE_LENGTH_INFO => '获取主播直播时长信息异常',
    ];

    private function _init()
    {

        $this->_uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
        $this->_enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->_month = isset($_POST['month']) ? trim($_POST['month']) : 'now';

        if (!$this->_uid || !$this->_enc)
        {
            $code = self::ERROR_USER_PARAM;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->_uid}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__;
            write_log($log);
            render_error_json(self::$errorMsg[$code]);
            exit;
        }

        $userAuthService = new UserAuthService();
        $userAuthService->setCaller('api:' . __FILE__);
        $userAuthService->setUid($this->_uid);
        $userAuthService->setEnc($this->_enc);
        if (!$userAuthService->checkLoginStatus())
        {
            $code = self::ERROR_USER_LOGIN;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->_uid}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__;
            write_log($log);
            render_error_json(self::$errorMsg[$code]);
            exit;
        }
        $userDataService = new UserDataService();
        $userDataService->setCaller('api:' . __FILE__);
        $userDataService->setUid($this->_uid);
        $userDataService->setEnc($this->_enc);
        if (!$userDataService->isAnchor())
        {
            $code = self::ERROR_IS_ANCHOR;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->_uid}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__;
            write_log($log);
            render_error_json(self::$errorMsg[$code]);
            exit;
        }
    }

    private function _getData()
    {

        $userDataService = new UserDataService();
        $userDataService->setCaller('api:' . __FILE__);
        $userDataService->setUid($this->_uid);
        $userDataService->setUserInfoDetail(UserDataService::USER_INFO_ALL);
        $anchorInfo = $userDataService->getUserInfo();

        if (!$anchorInfo)
        {
            $code = self::ERROR_ANCHOR_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->_uid}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__;
            write_log($log);
            render_error_json(self::$errorMsg[$code]);
            exit;
        }

        $result = [];
        $result['name'] = isset($anchorInfo['nick']) ? $anchorInfo['nick'] : '';

        $roomManagerService = new RoomManagerService();
        $roomManagerService->setCaller('api:' . __FILE__);
        $roomManagerService->setUid($this->_uid);
        $roomId = $roomManagerService->getRoomidByUid();

        $result['roomID'] = $roomId ? $roomId : 0;
        $result['head'] = isset($anchorInfo['pic']) ? $anchorInfo['pic'] : '';

        if ($this->_month == 'now')
        {
            $smonth = date('Y-m-01', time());
            $nextMonthDays = getNextMonthDays($smonth);
            $emonth = $nextMonthDays[0];
            $day = date('Y-m-d', time());
        } elseif ($this->_month == 'last')
        {
            $emonth = date('Y-m-01', time());
            $lastMonthDays = getLastMonthDays($emonth);            
            $smonth = $lastMonthDays[0];
            $day = '';
        }

        $anchorDataService = new AnchorDataService();
        $anchorDataService->setCaller('api:' . __FILE__);
        $anchorDataService->setUid($this->_uid);

        $anchorLevel = $anchorDataService->getAnchorLevel();
        $result['level'] = isset($anchorLevel['level']) ? $anchorLevel['level'] : 0;
        $levelIntegral = $anchorDataService->getIntegralByLevel();
        $result['integral'] = isset($anchorLevel['integral']) && $levelIntegral - $anchorLevel['integral'] ? round($levelIntegral - $anchorLevel['integral']) : 0;
        
        $liveLengthService = new LiveLengthService();
        $liveLengthService->setCaller('api:' . __FILE__);
        $liveLengthService->setLuid($this->_uid);
        $anchorLiveLengthInfo = $liveLengthService->getAnchorLiveLengthInfo($smonth, $emonth, $day);
        if(!$anchorLiveLengthInfo)
        {
            $result['livedHourMonth'] = 0.0;
            $result['noLivedHourMonth'] = 100.0;
            $result['livedTimeMonth'] = '0小时0分钟';
            $result['livedTimeToday'] = '0小时0分钟';
            $result['livedVaildDay'] = 0;
            $result['livedMaxVisit'] = 0;

            $code = self::ERROR_ANCHOR_LIVE_LENGTH_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->_uid}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__;
            write_log($log);
        } else
        {
            $result['livedHourMonth'] = isset($anchorLiveLengthInfo['monthLength']) ? $this->_changeTimeType($anchorLiveLengthInfo['monthLength'], 2) : 0.0;
            $result['noLivedHourMonth'] = isset($anchorLiveLengthInfo['noLiveLength']) ? $this->_changeTimeType($anchorLiveLengthInfo['noLiveLength'], 2) : 0.0;
            $result['livedTimeMonth'] = isset($anchorLiveLengthInfo['monthLength']) ? $this->_changeTimeType($anchorLiveLengthInfo['monthLength'], 1) : '0小时0分钟';
            $result['livedTimeToday'] = isset($anchorLiveLengthInfo['dayLength']) ? $this->_changeTimeType($anchorLiveLengthInfo['dayLength'], 1) : '0小时0分钟';
            $result['livedVaildDay'] = isset($anchorLiveLengthInfo['effectiveDays']) ? $anchorLiveLengthInfo['effectiveDays'] : 0;
            $result['livedMaxVisit'] = isset($anchorLiveLengthInfo['popuPeak']) ? $anchorLiveLengthInfo['popuPeak'] : 0;
        }
        //增加主播经纪公司状态
        $anchorApplySerivce = new AnchorApplyService();
        $anchorApplyInfo = $anchorApplySerivce->setUid($this->_uid)->getAnchorApplyStatusInfo();
        $result['aid'] = isset($anchorApplyInfo['aid']) ? $anchorApplyInfo['aid'] : '' ;
        $result['cid'] = isset($anchorApplyInfo['cid']) ? $anchorApplyInfo['cid'] : '' ;
        $result['cname'] = isset($anchorApplyInfo['cname']) ? $anchorApplyInfo['cname'] : '' ;
        $result['applyStatus'] = isset($anchorApplyInfo['apply_status']) ? $anchorApplyInfo['apply_status'] : '' ;
        $result['reason'] = isset($anchorApplyInfo['reason']) ? $anchorApplyInfo['reason'] : '' ;

        return $result;
    }

    private function _changeTimeType($seconds,$type)
    {
        $res = '';
        switch ($type){
            case 1:
                $res = floor($seconds/3600) . '小时';
                $res .= round($seconds%3600/60) . '分钟';
                break;
            case 2:
            default :
                $res = round($seconds/3600,1);
        }

        return $res;
    }

    /**
     * 输出
     */
    public function display()
    {
        $this->_init();

        $data = $this->_getData();

        if (!$data)
        {

            $code = self::ERROR_ANCHOR_LIVE_TIME;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->_uid}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__;
            write_log($log);
            render_error_json(self::$errorMsg[$code]);
            exit;
        }

        render_json($data);
    }

}

$do = new anchorLiveTime();
$do->display();



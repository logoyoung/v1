<?php

include '../../../include/init.php';

use service\statistics\UserLiveViewStatisticsService;
use service\user\UserAuthService;

class liveViewStatistics
{

    //用户观看数据存储异常
    const ERROR_SET_USER_VIEW_DATA = 810001;
    //观看时长最小值
    const VIEW_MIN_LENGTH = 0;
    //观看时长最大值
    const VIEW_MAX_LENGTH = 40;


    public static $errorMsg = [
        self::ERROR_SET_USER_VIEW_DATA => '用户观看数据存储异常',
    ];
    
    private $_param;

    private function _init()
    {

        $this->_param['enc']         = isset($_POST['encpass'])      ? trim($_POST['encpass'])       : '';
        $this->_param['uid']         = isset($_POST['uid'])          ? (int) $_POST['uid']           : 0;
        $this->_param['luid']        = isset($_POST['luid'])         ? (int) $_POST['luid']          : 0;
        $this->_param['liveid']      = isset($_POST['liveid'])       ? (int) $_POST['liveid']        : 0;
        $this->_param['realtime']    = isset($_POST['realtime'])     ? (int) $_POST['realtime']      : 0;
        $this->_param['appVersion']  = isset($_POST['appVersion'])   ? trim($_POST['appVersion'])    : '';
        $this->_param['deviceType']  = isset($_POST['deviceType'])   ? (int) $_POST['deviceType']    : 0;
        $this->_param['system']      = isset($_POST['system'])       ? trim($_POST['system'])        : '';
        $this->_param['deviceModel'] = isset($_POST['deviceModel'])  ? trim($_POST['deviceModel'])   : '';
        $this->_param['udid']        = isset($_POST['udid'])         ? trim($_POST['udid'])          : '';

        if (!$this->_param['uid'] || !$this->_param['enc'])
        {
            render_error_json(['LoginStatus' => 0]);
            exit;
        }

        if(!$this->_param['luid'] || !$this->_param['liveid'] || !$this->_param['appVersion'] || !$this->_param['realtime'])
        {
            render_error_json(['error' => 'Param Error!']);
            exit;
        }

        if($this->_param['realtime'] < self::VIEW_MIN_LENGTH || $this->_param['realtime'] > self::VIEW_MAX_LENGTH)
        {
            render_error_json(['error' => 'Param Error!']);
            exit;
        }
        
        if($this->_param['uid'] >= LIVEROOM_ANONYMOUS)
        {
            return true;
        }
        
        $auth = new UserAuthService();
        $auth->setUid($this->_param['uid']);
        $auth->setCaller('api:' . __FILE__);
        $auth->setEnc($this->_param['enc']);
        //校验encpass、用户 登陆状态
        if ($auth->checkLoginStatus() !== true)
        {
            //获取校验结果
            $result = $auth->getResult();
            //错误码
            $errorCode = $result['error_code'];
            //错误消息
            $errorMsg = $result['error_msg'];
            //假如是封禁的，可以获取禁时间
            $etime = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
            write_log("notice|uid:{$this->_param['uid']};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|class:" . __CLASS__, 'auth_access');
            render_error_json(['LoginStatus' => 0]);
            exit;
        }
    }

    private function _setViewLength()
    {
        $service = new UserLiveViewStatisticsService();
        $service->setCaller('api:' . __FILE__);
        $service->setParams($this->_param);

        return $service->setUserLiveViewData();
    }

    public function display()
    {
        $this->_init();
        $list = $this->_setViewLength();
        if (!$list)
        {
            $code = self::ERROR_SET_USER_VIEW_DATA;
            $msg = self::$errorMsg[$code];
            $log = "Notice | error_code:{$code};msg:{$msg};uid:{$this->_param['uid']}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            render_error_json(['desc'=>'Failed']);
            exit;
        }
        render_json(['desc'=>'success']);
    }

}

$obj = new liveViewStatistics();
$obj->display();

<?php

include '../../../include/init.php';
/**
 * 检测是否被禁言
 * date 2016-10-13 15:09
 * author yandong@6rooms.com
 * @update xuyong 2017-5-11
 *
 */
use service\room\RoomManagerService;
use service\user\UserAuthService;

class isSilenced
{

    //用户uid
    private $_uid;
    //主播ID
    private $_luid;
    //用户enc
    private $_enc;
    const ERROR_CODE_PARAM = -4013;
    public static $erroMsg = [
        self::ERROR_CODE_PARAM    => '缺少参数或者参数类型错误',
    ];

    public function display()
    {
        $this->_uid  = isset($_POST['uid'])     ? (int) $_POST['uid']  : 0;
        $this->_enc  = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->_luid = isset($_POST['luid'])    ? (int) $_POST['luid'] : 0;

        if(!$this->_uid || !$this->_enc || !$this->_luid )
        {
            $code = self::ERROR_CODE_PARAM;
            $msg  = self::$erroMsg[$code];
            render_error_json($msg,$code,2);
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
            write_log("notice|uid:{$this->_uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|api:".__FILE__,'auth_access');
            error2(-4067,2);
        }

        $result = [
            //0:未禁言，1:被禁言
            'isSilence'    => '0',
            //禁言到期时间戳
            'outTimeStamp' => '0'
        ];
        $roomManagerService = new RoomManagerService();
        $roomManagerService->setCaller('api:'.__FILE__.';line:'.__LINE__);
        $roomManagerService->setUid($this->_luid);
        $roomManagerService->setTargetUid($this->_uid);
        $silenceStatus = $roomManagerService->isSilenced();

        if($silenceStatus !== true)
        {
            $result['isSilence']    = '1';
            $result['outTimeStamp'] = $silenceStatus;
        }

        render_json($result);
    }
}

$obj = new isSilenced();
$obj->display();
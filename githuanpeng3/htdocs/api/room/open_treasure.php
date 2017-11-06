<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/7
 * Time: 上午10:06
 */

include '../../../include/init.php';
use service\user\UserDataService;
use service\task\TreasureService;
use lib\TreasureBox;
use service\user\UserAuthService;

class OpenTreasure
{

    private $_treasureID;
    private $_uid;
    private $_encpass;
    private $_luid;

    private function _init()
    {
        $this->_treasureID = isset($_POST['treasureID']) ? (int)$_POST['treasureID'] : 0;
        $this->_uid  = isset($_POST['uid'])     ? (int)$_POST['uid']  : 0;
        $this->_enc  = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->_luid = isset($_POST['luid'])    ? (int)$_POST['luid'] : 0;

        if(!$this->_treasureID || !$this->_uid || !$this->_luid || !$this->_enc)
        {
            $code = TreasureService::ERROR_OPEN_TREASURE_PARAM;
            $msg  = TreasureService::$errorMsg[$code];
            render_error_json($msg,$code);
        }

    }

    public function display()
    {
        $this->_init();

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
            $code = TreasureService::ERROR_OPEN_TREASURE_USER;
            $msg  = TreasureService::$errorMsg[$code];
            render_error_json($msg,$code,2);
        }

        $userService = new UserDataService();
        $userService->setCaller('api:'.__FILE__);
        $userService->setUid($this->_uid);
        $userData = $userService->getUserInfo();
        //校验手机认证情况
        if(!$userData['phone'])
        {

            $code = TreasureService::ERROR_OPEN_TREASURE_USER_PHONE;
            $msg  = TreasureService::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        try
        {
            $treasureService = new TreasureService();
            $treasureService->setCaller('api:'.__FILE__);
            $treasureService->setUid($this->_uid);
            $treasureService->setLuid($this->_luid);
            $treasureService->setTreasureID($this->_treasureID);
            $result = $treasureService->openTreasureBox();

        } catch (Exception $e)
        {
            render_error_json($e->getMessage(),$e->getCode(),2);
        }

        $userInfo = [];
        $tuid     = $treasureService->getTreasureOwnerUid();

        if($tuid)
        {
            $userService->setUid($tuid);
            $userInfo = $userService->getUserInfo();
        }
        $userService->setUid($this->_uid);
        $userService->setUserInfoDetail(UserDataService::USER_ACTICE_BASE);
        $userService->setFromDb(true)->setFromDbMaster(true);
        $property = $userService->getUserInfo();
        $data     = [
            'count'  => $result,
            'nick'   => isset($userInfo['nick']) ? $userInfo['nick'] :'',
            'hpcoin' => $property['hpcoin'],
            'hpbean' => $property['hpbean'],
         ];

        render_json($data);
    }

}

$obj = new OpenTreasure();
$obj->display();
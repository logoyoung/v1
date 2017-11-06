<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/24
 * Time: 下午4:38
 */

include '../../../../include/init.php';

use service\room\RoomManagerService;
use service\user\UserAuthService;

class anchorNotice
{

    //用户uid
    public $uid;
    //加密码
    public $encpass;
    //主要是区分 访问页 做用户权限校验用使用，0 不需要，1 需要 (没啥用)
    public $type;

    //不需要做用户权限校验
    const AUTH_TYPE_N = 0;
    //需要做用户权限校验用
    const AUTH_TYPE_Y = 1;
    //无效的uid
    const ERROR_USER_UID   = -4013;
    //无效的encpass
    const ERROR_USER_ENC   = -12918;
    //无效果的type值
    const ERROR_TYPE       = -12917;
    const ERROR_USER_AUTH  = -12919;

    public static $errorMsg = [
        self::ERROR_USER_UID  => '缺少参数或者参数类型错误',
        self::ERROR_TYPE      => '无效果的type值',
        self::ERROR_USER_ENC  => '请重新登陆',
        self::ERROR_USER_AUTH => '请重新登陆',
    ];

    public static $typeALl = [
        self::AUTH_TYPE_N,
        self::AUTH_TYPE_Y,
    ];

    private function _init()
    {
        $this->uid  = isset($_POST['uid'])     ? (int) $_POST['uid']      : 0;
        $this->enc  = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->type = isset($_POST['type'])    ? (int) $_POST['type']     : 0;

        if(!$this->uid)
        {
            $code = self::ERROR_USER_UID;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        if(!in_array($this->type,self::$typeALl))
        {
            $code = self::ERROR_TYPE;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        if($this->type == '1')
        {
            $this->_auth();
        }

        return true;
    }

    private function _auth() {

        if(!$this->enc)
        {
            $code = self::ERROR_USER_ENC;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        $auth = new UserAuthService();
        $auth->setUid($this->uid);
        $auth->setEnc($this->enc);
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
            write_log("notice|uid:{$this->getUuid()};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|class:".__CLASS__,'auth_access');
            $code = self::ERROR_USER_AUTH;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code,2);
        }

        return true;
    }


    public function display()
    {
        $this->_init();
        $service = new RoomManagerService();
        $service->setCaller('api:'.__FILE__);
        $service->setUid($this->uid);
        $notice  = $service->getAnchorNotice();
        $result  = [
            'status'   => '-1',
            'message'  => '',
        ];

        if(!$notice)
        {
            render_json($result);
        }

        $status = $notice['status'];
        $result['status'] = $status;

        if($this->type == '1')
        {
            $result['message'] =  isset($notice['message']) ? $notice['message'] : '';
            render_json($result);
        }

        switch ($status)
        {

            //1审核通过
            case RoomManagerService::STATUS_TYPE_F:
                $result['message'] = $notice['message'];
                break;

            //未通过
            case RoomManagerService::STATUS_TYPE_U:
            //-1 表示没有公告
            case RoomManagerService::STATUS_TYPE_N:
            default:
                break;
        }

        render_json($result);

    }

}

$obj = new anchorNotice();
$obj->display();

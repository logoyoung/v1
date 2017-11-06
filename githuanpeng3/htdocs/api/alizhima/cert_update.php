<?php
require '../../../include/init.php';
use service\zhima\CertService;
use service\user\UserDataService;
use service\user\UserAuthService;

class zhimaCrtUpdate
{
    const ERROR_INVALID_PARAM   = -4011;
     //请重新登陆
    const ERROR_USER_AUTH       = -4067;

    public static $errorMsg = [
          self::ERROR_INVALID_PARAM  => '缺少必要参数',
          self::ERROR_USER_AUTH      => '请重新登陆',
    ];

    public function run()
    {
        write_log("notice|收到客户端回写http请求;post:".hp_json_encode($_POST).';line:'.__LINE__.';file:'.__FILE__, 'zhima_cert');
        $uid           = isset($_POST['uid'])            ? (int) $_POST['uid']      : 0;
        $encpass       = isset($_POST['encpass'])        ? trim($_POST['encpass'])  : '';
        $transactionId = isset($_POST['transaction_id']) ? trim($_POST['transaction_id']) : '';
        $isPassed      = isset($_POST['is_passed'])      ? (int)$_POST['is_passed'] : 0;
        $errorCode     = isset($_POST['error_code'])     ? xss_clean(trim($_POST['error_code'])) : 0;

        if(!$uid || !$encpass || !$transactionId)
        {
            render_error_json(self::$errorMsg[self::ERROR_INVALID_PARAM],self::ERROR_INVALID_PARAM,2);
        }

        $auth = new UserAuthService();
        $auth->setUid($uid);
        $auth->setEnc($encpass);
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
            write_log("notice|uid:{$uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|api:".__FILE__,'auth_access');
            $code = self::ERROR_USER_AUTH;
            $msg  = self::$errorMsg[$code];
            write_log("warning|uid:{$uid},name:{$certName};身份证号:{$certNo};请重新登陆;line:".__LINE__.';file:'.__FILE__, 'zhima_cert');
            render_error_json($msg,$code,2);
        }

        $cert   = new CertService;
        $cert->setUid($uid);
        $cert->setTransactionId($transactionId);
        $cert->setErrorMsg($errorCode);
        $result = $isPassed ? $cert->zhimaCertSuccss() : $cert->zhimaCertError();

        if($result)
        {
            render_json([]);
        }

        render_error_json('系统异常',-40001,2);
    }
}

$obj = new zhimaCrtUpdate();
$obj->run();
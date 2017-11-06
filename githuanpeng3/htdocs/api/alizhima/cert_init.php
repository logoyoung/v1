<?php
require '../../../include/init.php';
use service\zhima\CertService;
use service\user\UserAuthService;
use service\user\helper\IdcardVerify;
use service\user\UserDataService;

/**
 * 芝麻认证初始化
 */
class zhimaCrtInit
{

    const ERROR_INVALID_PARAM   = -4011;
    const ERROR_INVALID_CERTNO  = -4012;
    //请重新登陆
    const ERROR_USER_AUTH       = -4067;
    //身份证已被认证
    const ERROR_PAPERSID_USERD  = -4068;

    const ERROR_SYSTEM_CODE     = -4100;

    public static $errorMsg = [
        self::ERROR_INVALID_PARAM  => '缺少必要参数',
        self::ERROR_INVALID_CERTNO => '无效的身份证号',
        self::ERROR_USER_AUTH      => '请重新登陆',
        self::ERROR_PAPERSID_USERD => '身份证已被认证',
        self::ERROR_SYSTEM_CODE    => '系统异常，请稍后再试',
    ];

    public static function getReturnUrl()
    {
        $host = DOMAIN_PROTOCOL.$GLOBALS['env-def'][strtoupper(get_hp_env())]['domain'];
        return [
            'success_url' => $host.'/mobile/beAnchor/index.html',
            'error_url'   => $host.'/mobile/beAnchor/index.html',
        ];
    }

    public function run()
    {
        write_log("notice|收到客户端发起芝麻认证http请求;post:".hp_json_encode($_POST).';line:'.__LINE__.';file:'.__FILE__, 'zhima_cert');
        $uid      = (int) $_POST['uid'];
        $encpass  = trim($_POST['encpass']);
        $certName = xss_clean(trim($_POST['cert_name']));
        $certNo   = trim($_POST['cert_no']);

        if(!$uid || !$encpass || !$certName || !$certNo)
        {
            render_error_json(self::$errorMsg[self::ERROR_INVALID_PARAM],self::ERROR_INVALID_PARAM,2);
        }

        if(!IdcardVerify::verfify($certNo))
        {
            render_error_json(self::$errorMsg[self::ERROR_INVALID_CERTNO],self::ERROR_INVALID_CERTNO,2);
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

        $userService  = new UserDataService;
        $userService->setPapersid($certNo);
        $userRealName = $userService->getUserCertByPapersid();
        //身份证是否已认证过
        if(isset($userRealName[$certNo]))
        {
            $code = self::ERROR_PAPERSID_USERD;
            $msg  = self::$errorMsg[$code];
            write_log("warning|uid:{$uid},name:{$certName};身份证号:{$certNo};身份证已认证过了;line:".__LINE__.';file:'.__FILE__, 'zhima_cert');
            render_error_json($msg,$code,2);
        }

        $cert   = new CertService;
        $cert->setUid($uid);
        $cert->setCertName($certName);
        $cert->setCertNo($certNo);
        $result = $cert->getZhimaInitBizno();
        if(!$result)
        {
            $code = self::ERROR_SYSTEM_CODE;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code,2);
        }

        $result['retrun_url'] = self::getReturnUrl();

        render_json($result);
    }

}

$obj = new zhimaCrtInit();
$obj->run();
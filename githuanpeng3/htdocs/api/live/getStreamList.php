<?php

include '../../../include/init.php';

use service\live\StreamDataService;
use service\user\UserAuthService;

class StreamList {

    const ERROR_CODE_LUID = -4013;
    const ERROR_LUID_CERT = -4014;

    public $luid;
    public static $errorMsg = [
        self::ERROR_CODE_LUID => '无效的luid',
        self::ERROR_LUID_CERT => '请申请认证',
    ];

    public function display()
    {
        $this->luid = isset($_POST['luid']) ? (int) $_POST['luid'] : 0;
        if(!$this->luid)
        {
            $code = self::ERROR_CODE_LUID;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        $auth = new UserAuthService;
        $auth->setUid($this->luid);
        if (!$auth->checkAnchorCertStatus())
        {
            $code = self::ERROR_LUID_CERT;
            $msg  = self::$errorMsg[$code];
            $log  = "error |获取直播流信息，非法主播luid，error_code:{$code};msg:{$msg};luid:{$this->luid};api: ".__FILE__."; line:". __LINE__ ;
            write_log($log, 'auth_access');
            render_json(['orientation' => 0, 'liveID' => 0, 'streamList'=> [], 'stream' => '', 'isLiving'=> 0,]);
        }

        $multiStream = StreamDataService::getMultiStreamByAnchorUid($this->luid);
        $list        = StreamDataService::getOldMasterStreamByMultiStream($multiStream);
        $list['streamMultiInfo'] = $multiStream;
        render_json($list);
    }

}

$obj = new StreamList();
$obj->display();
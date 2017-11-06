<?php
namespace dota\app\http;
use service\user\UserDisableStatusService;
class User {
    const UPS_STATUS_ADD     = 2;
    const UPS_STATUS_DELETE  = 1;
    const UPS_STATUS_INVALID = -2111;
    const UPS_SYSTEM_ERROR   = -2999;

    public static $upsStatus = [
        self::UPS_STATUS_ADD,
        self::UPS_STATUS_DELETE,
    ];


    /**
     * 用户解禁封禁
     * @return [type] [description]
     */
    public function ups()
    {
        try {

            write_log('notice|收到请求;param:'.hp_json_encode($_POST),'dota_user_ups_access');

            $uid     = isset($_POST['uid'])    ? (int) $_POST['uid']     : 0;
            $type    = isset($_POST['type'])   ? (int) $_POST['type']    : 0;
            $status  = isset($_POST['status']) ? (int) $_POST['status']  : 0;
            $scope   = isset($_POST['scope'])  ? (int) $_POST['scope']   : 0;
            $etime   = isset($_POST['etime'])  ? (int) $_POST['etime']   : 0;
            $acUid   = isset($_POST['ac_uid']) ? (int) $_POST['ac_uid']  : 0;
            $desc    = isset($_POST['ext_text']) ? trim($_POST['ext_text'])  : '';

            $service = new UserDisableStatusService();
            $service->setUid($uid);
            $service->setType($type);
            $service->setScope($scope);
            $service->setPlatform(1);
            $service->setAcUid($acUid);
            $service->setDesc($desc);
            $result  = false;

            switch ($status)
            {
                //加禁
                case self::UPS_STATUS_ADD:
                    $service->setEtime($etime);
                    $result = $service->addDisable();
                    break;

                //解禁
                case self::UPS_STATUS_DELETE:
                    $result = $service->deleteDisable();
                    break;

                default:
                    $msg = '无效的status 1解禁，2加禁';
                    write_log("error|{$msg}".hp_json_encode($_POST),'dota_user_ups_access');
                    render_error_json($msg,self::UPS_STATUS_INVALID,2);
                    break;
            }

            if($result)
            {
                write_log("success|param:".hp_json_encode($_POST),'dota_user_ups_access');
                render_json('success');
            }

            $msg = '系统异常';
            write_log("error|{$msg};error_code:".self::UPS_SYSTEM_ERROR.' ;param:'.hp_json_encode($_POST),'dota_user_ups_access');
            render_error_json($msg,self::UPS_SYSTEM_ERROR,2);

        } catch (Exception $e) {
            $msg = "error|error_code:{$e->getCode()}; error_msg:{$e->getMessage()}; param:".hp_json_encode($_POST);
            write_log($msg,'dota_user_ups_access');
            render_error_json($e->getMessage(),$e->getCode(),2);
        }

    }
}
<?php
require __DIR__.'/../../include/init.php';
use service\room\RoomManagerService;
use service\user\UserDisableStatusService;
use service\user\UserAuthService;

class test
{

    private $_roomManagerService;

    public function add($acUid,$anchorUid,$tuid,$etime)
    {
        $this->_roomManagerService = new RoomManagerService();
        $this->_roomManagerService->setCaller('api:'.__FILE__.';line:'.__LINE__);
        //主播uid
        $this->_roomManagerService->setUid($anchorUid);
        //操作者uid
        $this->_roomManagerService->setAcUid($acUid);
        //禁方用户uid
        $this->_roomManagerService->setTargetUid($tuid);
        //禁言时间
        $this->_roomManagerService->setTimeLength($etime);

        try {

            if($this->_roomManagerService->setSilence() === true)
            {

                exit('success');
            }

            exit('error');

        } catch (Exception $e) {

            echo $e->getCode();
            echo "\n";
            echo $e->getMessage();
            echo "\n";
        }
    }

    public function delete()
    {

    }

    public function getStatus($uid = 47420, $anchorUid = 1870)
    {

        $auth = new \service\user\UserAuthService;
        //用户uid
        $auth->setUid($uid);
        //主播uid
        $auth->setAnchorUid($anchorUid);
        //获取直播间禁言状态
        if($auth->checkSilencedStatus() === false)
        {
            //获取错误结果集
            $result = $auth->getResult();
            //错误码
            $code   = $result['error_code'];
            //错误消息
            $msg    = $result['error_msg'];
            //解禁时间 （时间戳）
            $etime  = $result['silenced_etime'];

            //do smt
            //
            //

            return false;
        }

        echo "ok\n";
    }


}
$acUid = 129748;
$anchorUid = 1870;
$tuid  = 47420;
$etime = 3600;
$obj = new test;
//$obj->add($acUid,$anchorUid,$tuid,$etime);
$obj->getStatus($uid = 47420, $anchorUid = 1870);
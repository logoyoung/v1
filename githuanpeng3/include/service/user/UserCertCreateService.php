<?php
namespace service\user;
use Exception;
use service\common\AbstractService;
use system\DbHelper;
use lib\anchor\Anchor;
use lib\room\Roomid;
use lib\user\UserRealName;
use lib\user\ZhimaCert;
use service\anchor\AnchorGetDataService;

class UserCertCreateService extends AbstractService
{
    //主播默认等级
    const DEFAULT_LEVEL      = 1;

    //实名认证通过 anchor 表的状态
    const ANCHOR_STATUS_SUCC = 1;

    //101 （用户实名认证通过）
    const USER_STATUS_SUCC    = RN_PASS;

    const DETAULT_ANCHOR_RATE = 60;

    private $_uid;
    private $_certName;
    private $_certno;
    private $_rate;
    private $_utime;
    private $_zhimaStatus;
    private $_zhimaLog = 'zhima_cert_access';

    public function setUid($uid)
    {
        $this->_uid = $uid;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setCertName($certName)
    {
        $this->_certName = $certName;
        return $this;
    }

    public function getCertName()
    {
        return $this->_certName;
    }

    public function setCertno($certno)
    {
        $this->_certno = $certno;
        return $this;
    }

    public function getCertno()
    {
        return $this->_certno;
    }

    public function setRate($rate)
    {
        $this->_rate = $rate;
        return $this;
    }

    public function getRate()
    {
        return $this->_rate ? $this->_rate : AnchorGetDataService::BASE_RATE;
    }

    public function setUtime($utime)
    {
        $this->_utime = $utime;
        return $this;
    }

    public function getUtime()
    {
        return $this->_utime ? $this->_utime : date('Y-m-d H:i:s');
    }

    public function setTransactionId($transactionId)
    {
        $this->_transactionId = $transactionId;
    }

    public function getTransactionId()
    {
        return $this->_transactionId;
    }

    public function setZhimaStatus($zhimaStatus)
    {
        $this->_zhimaStatus = $zhimaStatus;
        return $this;
    }

    public function getZhimaStatus()
    {
        return $this->_zhimaStatus;
    }

    public function zhimaCertSuccss()
    {
        try {

            $userRealNameDb = new UserRealName;
            $anchorDb       = new Anchor;
            $roomidDb       = new Roomid;
            $zhimaCertDb    = new ZhimaCert;
            $utime          = $this->getUtime();
            $log = "uid:{$this->getUid()};cert_name:{$this->getCertName()};cert_no:{$this->getCertno()};transaction_id:{$this->getTransactionId()};zhimaStatus:{$this->getZhimaStatus()}";

            $this->zhimaLog("info|收到写数据库请求;{$log};line:".__LINE__);

            DbHelper::beginMultiTrans([$userRealNameDb,$anchorDb,$roomidDb,$zhimaCertDb]);

            //写入用户实名认证信息
            $s = $userRealNameDb->add($this->getUid(), $this->getCertName(), $this->getCertno(), self::USER_STATUS_SUCC, '芝麻认证', $utime,0,0);
            if($s === false)
            {
                throw new Exception('userrealname Db error');
            }

            //写入主播数据
            $s = $anchorDb->add($this->getUid(), self::DEFAULT_LEVEL, self::ANCHOR_STATUS_SUCC, $this->getRate(), $utime);
            if($s === false)
            {
                throw new Exception('anchor Db error');
            }

            //写入roomid
            $roomid = self::createNewRoomid();
            $s = $roomidDb->add($roomid,$this->getUid(),$utime);
            if($s === false)
            {
                throw new Exception('roomid Db error');
            }

            $this->zhimaLog("info|写入数据库roomid:{$roomid};{$log};line:".__LINE__);

            //更新zhima认证表记录信息
            $s = $zhimaCertDb->updateStatusByTidUid($this->getTransactionId(),$this->getUid(),$this->getZhimaStatus());
            if($s === false)
            {
                throw new Exception('update zhima_cert db error');
            }

            DbHelper::commitMulti();
            $this->zhimaLog("success|数据库写入成功;{$log}");

            return true;

        } catch (Exception $e) {
            $this->zhimaLog("error|数据库异常;{$log};msg:{$e->getMessage()};line:".$e->getLine());
            DbHelper::rollbackMulti();
            return false;
        }

    }

    //现在前后台做一套，除非操作加表锁，不然很容易出问题，应该做成统一的发号器
    public static function createNewRoomid()
    {
        $roomidDb = new Roomid;
        $rid      = $roomidDb->getMaxRoomid();

        do {

            ++$rid;

        } while (self::rooidRule($rid) != true);

        return $rid;
    }

    public static function rooidRule($roomid)
    {
        //6位顺增、顺降
        $reg1 = '#(?:(?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){5}|(?:9(?=8)|8(?=7)|7(?=6)|6(?=5)|5(?=4)|4(?=3)|3(?=2)|2(?=1)|1(?=0)){5})\d#';

        //匹配3位及以上的重复数字
        $reg2 = '#([\d])\1{2,}#';

        //4-9位连续的数字
        $reg3 = '#(?:(?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){3,}|(?:9(?=8)|8(?=7)|7(?=6)|6(?=5)|5(?=4)|4(?=3)|3(?=2)|2(?=1)|1(?=0)){3,})\d#';

        //如ab ab、11111，22222，3333等
        $reg4 = '#(([\d]){1,}([\d]){1,})\1{1,}#';

        if (preg_match($reg1, $roomid) || preg_match($reg2, $roomid) || preg_match($reg3, $roomid) || preg_match($reg4, $roomid))
        {
            return false;
        }

        return true;
    }


    // 1显示普通认证渠道，2.显示芝麻认证渠道, 3.显示所有认证渠道, 0关必所有
    public static function getDisplayCertChannel()
    {
        return 1;
    }

    public function zhimaLog($msg)
    {
        write_log($msg.';class:'.__CLASS__,$this->_zhimaLog);
    }
}
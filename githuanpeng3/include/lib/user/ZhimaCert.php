<?php
namespace lib\user;
use Exception;
use system\DbHelper;

class ZhimaCert
{
    const DB_CONF = 'huanpeng';

    public static $fields = [
          'sid',
          'uid',
          'type',
          'cert_name',
          'cert_no',
          'transaction_id',
          'biz_code',
          'biz_no',
          'status',
          'biz_etime',
          'ctime',
          'utime',
    ];

    public function getTable()
    {
        return 'zhima_cert';
    }

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }

    public function add($uid,$type,$certName,$certNo,$transactionId,$bizCode,$bizNo,$status,$bizEtime,$utime)
    {

        $db  = $this->getDb();
        $bdParam = [
            'uid'        => $uid,
            'type'       => $type,
            'cert_name'  => $certName,
            'cert_no'    => $certNo,
            'transaction_id' => $transactionId,
            'biz_code'   => $bizCode,
            'biz_no'     => $bizNo,
            'status'     => $status,
            'biz_etime'  => $bizEtime,
            'utime'      => $utime,
        ];

        $sql = "INSERT INTO `{$this->getTable()}` (`uid`,`type`,`cert_name`,`cert_no`,`transaction_id`,`biz_code`,`biz_no`,`status`,`biz_etime`,`utime`) VALUES(:uid,:type,:cert_name,:cert_no,:transaction_id,:biz_code,:biz_no,:status,:biz_etime,:utime)";

        try {

            return $db->execute($sql,$bdParam,true);

        } catch (Exception $e) {
            return false;
        }
    }

    public function updateStatusByTidUid($transactionId,$uid,$status,$errMsg='0')
    {
        if(!$transactionId || !$uid || !$status)
        {
            return false;
        }

        $db  = $this->getDb();
        $bdParam = [
            'transaction_id' => $transactionId,
            'uid'            => $uid,
            'status'         => $status,
            'error_msg'      => $errMsg,
        ];

        $sql = "UPDATE `{$this->getTable()}` SET `status` = :status,`error_msg` = :error_msg WHERE `transaction_id` = :transaction_id AND `uid` = :uid LIMIT 1";

        try {

            return $db->execute($sql,$bdParam,true);

        } catch (Exception $e) {
            return false;
        }
    }

    public function getZhimaCertByYidUid($transactionId, $uid, array $fields = [])
    {
        if(!$transactionId || !$uid )
        {
            return false;
        }

        $db  = $this->getDb();
        $bdParam = [
            'transaction_id' => $transactionId,
            'uid'            => $uid,
        ];
        $fields  = $db->buildFieldsParam(($fields ? $fields : self::$fields));
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `transaction_id` = :transaction_id AND `uid` = :uid LIMIT 1";

        try {

            $result = $db->query($sql,$bdParam);
            if(!$result)
            {
                return [];
            }

            return $result[0];

        } catch (Exception $e) {
            return false;
        }
    }

    public function getZhimaCertByUidCertNoStatus($uid, $certNo, $status = 1, array $fields = [])
    {
        if(!$uid || !$certNo || !$status)
        {
            return false;
        }
        $db  = $this->getDb();
        $bdParam = [
            'uid'       => $uid,
            'cert_no'   => $certNo,
            'status'    => $status,
        ];

        $fields  = $db->buildFieldsParam(($fields ? $fields : self::$fields));
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `uid` = :uid  AND `cert_no` = :cert_no AND `status` = :status LIMIT 1";

        try {

            $result = $db->query($sql,$bdParam);
            if(!$result)
            {
                return [];
            }

            return $result[0];

        } catch (Exception $e) {
            return false;
        }

    }


    public function getZhimaCertByUidStatus($uid, $status = 2, array $fields = [])
    {
        if(!$uid || !$status)
        {
            return false;
        }

        $db  = $this->getDb();
        $bdParam = [
            'uid'       => $uid,
            'status'    => $status,
        ];

        $fields  = $db->buildFieldsParam(($fields ? $fields : self::$fields));
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `uid` = :uid AND `status` = :status LIMIT 1";

        try {

            $result = $db->query($sql,$bdParam);
            if(!$result)
            {
                return [];
            }

            return $result[0];

        } catch (Exception $e) {
            return false;
        }

    }
}

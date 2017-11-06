<?php
namespace lib\user;
use Exception;
use system\DbHelper;

class UserRealName
{
    const DB_CONF = 'huanpeng';

    public static $fields = [
        'id',
        'name',
        'papersid',
        'papersetime',
        'face',
        'back',
        'uid',
        'ctime',
        'status',
        'paperstype',
        'passtime',
        'handheldPhoto',
        'checkid',
        'reason',
        'adminid',
    ];

    public function getTable()
    {
        return 'userrealname';
    }

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }

    public function add($uid,$name,$papersid,$status,$reason,$ctime,$paperstype = 0, $adminid = 0)
    {
        if(!$uid || !$name || !$papersid)
        {
            return true;
        }

        $db  = $this->getDb();
        $bdParam = [
            'uid'        => $uid,
            'name'       => $name,
            'paperstype' => $paperstype,
            'papersid'   => $papersid,
            'status'     => $status,
            'ctime'      => $ctime,
            'reason'     => $reason,
            'adminid'    => $adminid,
        ];

        $sql = "INSERT INTO `{$this->getTable()}` (`uid`,`name`,`paperstype`,`papersid`,`status`,`ctime`,`reason`,`adminid`) VALUES(:uid,:name,:paperstype,:papersid,:status,:ctime,:reason,:adminid)";

        try {

            return $db->execute($sql,$bdParam,true);

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 根据证件号 获取数据
     * @param  $papersid 证件号
     * @param  $status 认证状态
     * @return
     */
    public function getDataByPapersid($papersid, $status = 101, array $fields = ['uid','papersid'])
    {
        if(!$papersid)
        {
            return false;
        }

        $papersid = (array) $papersid;
        $num      = count($papersid);
        $db       = $this->getDb();
        if($fields)
        {
            $fields[] = 'papersid';
            $fields   = array_unique($fields);
        } else {
            $fields = self::$fields;
        }

        $fields   = $db->buildFieldsParam($fields);
        $inStr    = $db->buildInPrepare($papersid);
        $papersid[] = $status;
        $sql      = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `papersid` IN($inStr) AND `status` = ? LIMIT {$num}";

        try {

            $result = $db->query($sql,$papersid);
            if(!$result)
            {
                return $result;
            }

            $data = [];
            foreach ($result as $v)
            {
                $data[$v['papersid']] = $v;
            }

            return $data;

        } catch (Exception $e) {
            return false;
        }
    }

    public function getDataByUid($uid, array $fields = [])
    {
        if(!$uid)
        {
            return false;
        }

        $uid = (array) $uid;
        $num = count($uid);
        if($fields)
        {
            $fields[] = 'uid';
            $fields   = array_unique($fields);
        } else {
            $fields = self::$fields;
        }

        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam($fields);
        $inStr   = $db->buildInPrepare($uid);
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `uid` IN($inStr) LIMIT {$num}";

        try {

            $result = $db->query($sql,$uid);
            if(!$result)
            {
                return $result;
            }

            $data = [];
            foreach ($result as $v)
            {
                $data[$v['uid']] = $v;
            }

            return $data;

        } catch (Exception $e) {
            return false;
        }

    }
}
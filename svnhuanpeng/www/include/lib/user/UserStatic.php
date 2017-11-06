<?php
namespace lib\user;
use Exception;
use system\DbHelper;
use due\systemMsg;

class UserStatic
{

    const DB_CONF = 'huanpeng';

    public static $fields = [
        'uid', //int(10) unsigned NOT NULL AUTO_INCREMENT,
        'username', //char(40) NOT NULL DEFAULT '',
        'nick', //varchar(50) DEFAULT NULL,
        'password',// varchar(32) NOT NULL DEFAULT '',
        'pic',//varchar(200) NOT NULL DEFAULT '',
        'rip',// int(10) unsigned NOT NULL DEFAULT '0',
        'rport',// int(10) unsigned NOT NULL DEFAULT '0',
        'rtime',// timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        'encpass',// varchar(100) NOT NULL DEFAULT '',
        'phone',// char(11) NOT NULL DEFAULT '',
        'mail',// varchar(30) NOT NULL DEFAULT '',
        'sex',// tinyint(3) unsigned NOT NULL,
        'mailstatus',// tinyint(3) unsigned NOT NULL DEFAULT '0',
        'isfree',// tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'æ˜¯å¦æœ‰å…è´¹æ”¹åæœºä¼š',
        'first_recharge_time',// timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        'first_sendgift_time', // timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        'first_live_time', // timestamp NOT NULL DEFAULT '0000-00-00 00:00:00','
    ];

    public static $unUpdateFields = ['uid','rtime'];

    private $_master = false;


    public function getUserStaticData($uid, array $fields = [])
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

    //注意没有用到索引，千万不要乱用，需要从库里加上phone 索引才行
    public function getUidByPhone($phone, array $fields = ['uid','phone'])
    {
        if(!$phone)
        {
            return false;
        }
        $phone = (array) $phone;
        $num = count($phone);
        if($fields)
        {
            $fields[] = 'uid';
            $fields   = array_unique($fields);
        } else {
            $fields = self::$fields;
        }

        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam($fields);
        $inStr   = $db->buildInPrepare($phone);
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `phone` IN($inStr) LIMIT {$num}";

        try {

            $result = $db->query($sql,$phone);
            if(!$result)
            {
                return $result;
            }

            $data = [];
            foreach ($result as $v)
            {
                $data[$v['phone']] = $v;
            }

            return $data;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 供校验脚本使用
     * @return init
     */
    public function getUserTotalNum()
    {
        $sql = "SELECT COUNT(*) AS total_num FROM `{$this->getTable()}`";

        try {
            $db     = $this->getDb();
            $result = $db->query($sql);
            if(!$result)
            {
                return 0;
            }

            return (int) $result[0]['total_num'];

        } catch (Exception $e) {
            return 0;
        }

    }

    /**
     * 供校验脚本使用
     * @param  [type] $page   [description]
     * @param  [type] $size   [description]
     * @param  array  $fields [description]
     * @return [type]         [description]
     */
    public function getUserStaticList($page, $size, array $fields = [])
    {
        if($fields)
        {
            $fields[] = 'uid';
            $fields   = array_unique($fields);
        } else {
            $fields = self::$fields;
        }
        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam($fields);
        $page    = (int) $page;
        $offset  = ($page - 1) * $size;
        $bdParam = ['offset' => $offset, 'size' => $size];
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` ORDER BY `uid` ASC LIMIT :offset,:size";

        try {
            $result = $db->query($sql,$bdParam);
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

    public function getTable()
    {
        return 'userstatic';
    }

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }

}
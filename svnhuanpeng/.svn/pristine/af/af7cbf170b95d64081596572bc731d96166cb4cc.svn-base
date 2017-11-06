<?php
namespace lib\user;
use Exception;
use system\DbHelper;

/**
 * log
 */
class SystemLog
{
    const DB_CONF = 'huanpeng';

    public static $fields = [
        'mid',
        'uid',
        'type',
        'ac_uid',
        'content',
        'ctime',
    ];

    public function getTable()
    {
        return 'system_log';
    }

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }

    public function add($uid,$type,$acUid,$content)
    {
        if(!$uid || !$type || !$acUid || !$content)
        {
            return false;
        }

        $db      = $this->getDb();
        $bdParam = [
            'uid'       => $uid,
            'type'      => $type,
            'ac_uid'    => $acUid,
            'content'   => $content,
        ];

        $sql = "INSERT INTO `{$this->getTable()}` (`uid`,`type`,`ac_uid`,`content`) VALUES(:uid,:type,:ac_uid,:content)";

        try {

            return $db->execute($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }

    }
}
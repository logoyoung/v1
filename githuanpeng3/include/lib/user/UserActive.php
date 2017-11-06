<?php
namespace lib\user;
use Exception;
use system\DbHelper;

class UserActive
{
    const DB_CONF = 'huanpeng';

    public static $fields = [
        'uid', // int(10) unsigned NOT NULL AUTO_INCREMENT,
        'lip', // int(10) unsigned NOT NULL DEFAULT '0',
        'lport', // int(10) unsigned NOT NULL DEFAULT '0',
        'ltime', // timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        'level', // int(10) unsigned NOT NULL DEFAULT '1',
        'readsign', // int(10) unsigned NOT NULL DEFAULT '0',
        'hpbean', // float(13,3) NOT NULL DEFAULT '0.000',
        'hpcoin', // float(13,3) NOT NULL DEFAULT '0.000',
        'integral', // float(14,3) NOT NULL DEFAULT '0.000',
        'isnotice', // tinyint(3) unsigned NOT NULL DEFAULT '1',
        'province', // tinyint(3) unsigned NOT NULL DEFAULT '0',
        'city', //tinyint(3) unsigned NOT NULL DEFAULT '0',
        'address', // varchar(200) NOT NULL DEFAULT '',
        'enterroom', //int(10) unsigned NOT NULL DEFAULT '0',
    ];

    public static $unUpdateFields = ['uid'];

    public function getUserActiveData($uid, array $fields = [])
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
    public function getUserActiveList($page, $size, array $fields = [])
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
        $size    = $size > 1000 ? 1000 : $size;
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

    public function updateUserIntegral(int $uid, int $integral = 0,int $level = 0)
    {
        if(!$uid)
        {
            return false;
        }
        
        $bdParam = ['integral' => $integral,'level' => $level,'uid' => $uid];
        $sql = "UPDATE `{$this->getTable()}` SET integral=:integral,level=:level WHERE uid=:uid";
        
        try
        {
            return $this->getDb()->execute($sql,$bdParam);
        } catch (Exception $exc)
        {
            return false;
        }
    }

    public function getTable()
    {
        return 'useractive';
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
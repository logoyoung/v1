<?php
namespace lib\user;
use Exception;
use system\DbHelper;

class UserDisableStatus
{

    const DB_CONF = 'huanpeng';

    public static $fields = [
        'sid', //bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        'uid', //bigint(20) unsigned NOT NULL,
        'type', //tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '10(登陆操作)，20（禁言操作), 30(直播操作)',
        'scope', //bigint(20) unsigned NOT NULL DEFAULT '1' COMMENT '1(全站)，或直播间号',
        'etime', //int(11) unsigned NOT NULL DEFAULT '0' COMMENT '过期可选,默认2114438400',
        'utime', //DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ];


    public function addUserDisableStatus($uid, $type, $scope, $etime)
    {
        $db      = $this->getDb();
        $bdParam = [
            'uid'       => $uid,
            'type'      => $type,
            'scope'     => $scope,
            'etime'     => $etime,
            'oetime'    => $etime,
        ];

        $sql = "INSERT INTO `{$this->getTable()}` (`uid`,`type`,`scope`,`etime`) VALUES(:uid,:type,:scope,:oetime) ON DUPLICATE KEY UPDATE `etime` = :etime";

        try {

            return $db->execute($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }

    }

    public function deleteUserDisableStatus($uid, $type, $scope)
    {
        if(!$uid || !$type  || !$scope)
        {
            return false;
        }

        $db      = $this->getDb();
        $bdParam = [
            'uid'       => $uid,
            'type'      => $type,
            'scope'     => $scope,
        ];

        $sql     = "DELETE FROM `{$this->getTable()}` WHERE `uid` = :uid AND `type` = :type AND `scope` = :scope LIMIT 1";

        try {

            return $db->execute($sql,$bdParam,true);

        } catch (Exception $e) {
            return false;
        }

    }

    public function getDisableStatusByUidTypeScope($uid,$type,$scope,array $fields = [])
    {
        if(!$uid || !$type || !$scope)
        {
            return false;
        }

        $fields  = $fields ? $fields : self::$fields;
        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam($fields);
        $bdParam = [
            'uid'    => $uid,
            'type'   => $type,
            'scope'  => $scope,
        ];
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `uid` = :uid AND `type` = :type AND `scope` = :scope";

        try {

            return $db->query($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }

    }

    public function getSilencedStatusByUidType($uid,$type,array $fields = [])
    {
        if(!$uid || !$type)
        {
            return false;
        }

        $fields  = $fields ? $fields : self::$fields;
        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam($fields);
        $bdParam = [
            'uid'    => $uid,
            'type'   => $type,
        ];
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `uid` = :uid AND `type` = :type ORDER BY `sid` DESC";

        try {

            $result = $db->query($sql,$bdParam);
            if(!$result)
            {
                return $result;
            }

            $silencedData = [];
            foreach ($result as $v)
            {
                $silencedData[$v['scope']] = $v;
            }
            unset($result);

            return $silencedData;

        } catch (Exception $e) {
            return false;
        }
    }

    public function getTable()
    {
        return 'user_disable_status';
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
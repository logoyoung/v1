<?php
require __DIR__.'/../../bootstrap/i.php';
use lib\user\UserStatic;
use lib\user\UserActive;
use lib\anchor\Anchor;
use system\DbHelper;
use lib\room\Roomid;
use lib\user\UserRealName;
use lib\user\ZhimaCert;

/**
 * 添加测式用户
 *   (仅测式使用，不要在线上使用)
 */
class test {

    public static $staticTable = '';
    public static $activeTable = '';

    public static function getDb()
    {
        $db = new UserStatic;
        return $db->getDb();
    }

    public static function add($phone,$password)
    {
        $db = self::getDb();

        try {

            $testName = 'auto_'.$phone;
            $staticParam = [
                'username' => $testName,
                'nick'     => $testName,
                'phone'    => $phone,
                'password' => md5password( $password ),
                'encpass'  => md5( md5( $password . time() ) ),
                'sex' => 1
            ];

            $staticSql   = "INSERT INTO `userstatic`(`username`,`nick`,`phone`,`password`,`encpass`,`sex`) VALUES(:username,:nick,:phone,:password,:encpass,:sex)";
            $db->beginTransaction();
            $db->execute($staticSql,$staticParam,true);
            $uid = $db->getLastInsertId();

            $activeParam = ['uid'   => $uid];
            $activeSql   = "INSERT INTO `useractive`(`uid`) VALUES(:uid)";
            $s = $db->execute($activeSql,$activeParam,true);
            var_dump($s);
            $db->commit();
            return true;

        } catch (Exception $e)
        {
            echo $e->getMessage();
            $db->rollback();
            return false;
        }
    }

    public static function deleteAnchorRoomidRealNameByPhone($phone)
    {
        if(!$phone)
        {
            echo "empty phone \n";
            return false;
        }

        $db   = new UserStatic;
        $data = $db->getUidByPhone($phone,UserStatic::$fields);
        $uid = isset($data[$phone]['uid']) ? $data[$phone]['uid'] : 0;
        if(!$uid)
        {
            echo "invalid phone\n";
            return false;
        }

        $anchorDb   = new Anchor();
        $anchorData = $anchorDb->getAnchorDataByUid($uid);
        write_log('anchor:'.hp_json_encode($anchorData),'delete_anchor_data');

        $roomidDb   = new Roomid();
        $roomidData = $roomidDb->getRoomidByUid($uid);
        write_log('roomid:'.$roomidData,'delete_anchor_data');

        $realNameDb    = new UserRealName;
        $realNameData  = $realNameDb->getDataByUid($uid);
        write_log('UserRealName:'.hp_json_encode($realNameData),'delete_anchor_data');

        try {

            $db = self::getDb();
            $db->beginTransaction();
            $bdParam     = ['uid' => $uid];

            if($anchorData)
            {
                $anchorSql   = "DELETE FROM `anchor` WHERE `uid` = :uid LIMIT 1";
                $s =  $db->execute($anchorSql,$bdParam,true);
                var_dump('delete anchor:'.$s);
            }

            if($roomidData)
            {
                $roomidSql   = "DELETE FROM `roomid` WHERE `uid` = :uid LIMIT 1";
                $s =  $db->execute($roomidSql,$bdParam,true);
                var_dump('delete roomid:'.$s);
            }

            if($realNameData)
            {
                $realNameSql = "DELETE FROM `userrealname` WHERE `uid` = :uid LIMIT 1";
                $s =  $db->execute($realNameSql,$bdParam,true);
                var_dump('delete UserRealName:'.$s);
            }

            $zhimaDb = new ZhimaCert;
            $cert = $zhimaDb->getZhimaCertByUidStatus($uid);
            if(isset($cert['sid']) && $cert['sid'])
            {

                $s = $db->execute("DELETE FROM `zhima_cert` WHERE `sid` = :sid LIMIT 1",['sid' => $cert['sid']],true);
                var_dump('delete ZhimaCert:'.$s);
            }

            $db->commit();

            echo "delete success\n";

            return true;

        } catch (Exception $e)
        {
            echo "delete error\n";
            echo $e->getMessage();
            $db->rollback();
            return false;
        }
    }

    public static function addTestUser($user)
    {
        foreach ($user as $phone => $password)
        {
            $s = self::add($phone,$password);
            var_dump($s);
        }
    }

    public static function getUser916()
    {
        return ['18000000021' => '123123',];
    }

    public static function getUser915()
    {
        return [
            '18000000000' => '123123',
            '18000000001' => '123123',
            '18000000002' => '123123',
            '18000000003' => '123123',
            '18000000004' => '123123',
            '18000000005' => '123123',
            '18000000006' => '123123',
            '18000000007' => '123123',
            '18000000008' => '123123',
            '18000000009' => '123123',
            '18000000010' => '123123',
            '18000000011' => '123123',
            '18000000012' => '123123',
            '18000000013' => '123123',
            '18000000014' => '123123',
            '18000000015' => '123123',
            '18000000016' => '123123',
            '18000000017' => '123123',
            '18000000018' => '123123',
            '18000000019' => '123123',
            '18000000020' => '123123',
        ];
    }
}

//test::addTestUser(test::getUser916());
//test::deleteAnchorRoomidRealNameByPhone(13269691568);
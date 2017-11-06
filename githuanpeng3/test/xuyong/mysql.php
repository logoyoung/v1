<?php
require __DIR__.'/../../include/init.php';
use system\DbHelper;
use system\Timer;

/**
 */
class test
{
    //db 配置文件的key
    public static $dbConfName = 'huanpeng';


    /**
     * 查询
     * @return [type] [description]
     */
    public function testSelect($uid = '69312') {
        //根据配置文件获取数据实列
        $db    = DbHelper::getInstance(self::$dbConfName);

        //真实环境使用应该做成单独的方法
        $table = 'userstatic';

        //:uid,:sex 查询参数占位
        $sql   = "SELECT  `nick`,`username` FROM `{$table}`  LIMIT :sn,:en";
        //参数绑定
        $bdParam = [
        //'uid' => $uid,
        'sn'=> '0','en' => '1'];

        try {

            print_r($db->query($sql,$bdParam));

        } catch (Exception $e) {
            // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
            // 使用者根据自己的情况酌情处理
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";

            return false;
        }

    }

    /**
     * 修改
     * @return [type] [description]
     */
    public function testUpdate() {
        //根据配置文件获取数据实列
        $db    = DbHelper::getInstance(self::$dbConfName);
        //真实环境使用应该做成单独的方法
        $table = 'userstatic';
        $sql   = "UPDATE `{$table}` SET `nick` = :nick WHERE `uid` = :uid LIMIT 1";
        //参数绑定
        $bdParam = [
            'uid'  => '69312',
            'nick' => 'test'.mt_rand(1, 100),
        ];

        try {

            return $db->execute($sql,$bdParam);

        } catch (Exception $e) {

            // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
            // 使用者根据自己的情况酌情处理
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";

            return false;
        }

    }

    /**
     * 写入
     * @return [type] [description]
     */
    public function testInsert() {
        //根据配置文件获取数据实列
        $db    = DbHelper::getInstance(self::$dbConfName);

        //真实环境使用应该做成单独的方法
        $table = 'userstatic';

        $sql   = "INSERT INTO `{$table}`(`username`,`nick`) VALUES(:username,:nick)";

        $bdParam = [
            'username' => 'test_pdo',
            'nick'     => 'test_nick'.mt_rand(1, 100),
        ];

        try {

            $result = $db->execute($sql,$bdParam);

            //return $result;

            //假如是自增主键，可获取抛入的key
            if($result) {
                return $db->getLastInsertId();
            }

            return false;

        } catch (Exception $e) {

            // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
            // 使用者根据自己的情况酌情处理
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";

            return false;
        }
    }

    /**
     * 删除
     * @param  string $uid [description]
     * @return [type]      [description]
     */
    public function testDelete($uid = '') {

        $uid   = $uid ? $uid : $this->testInsert();
        if(!$uid) {
            die('empty uid');
        }

        //根据配置文件获取数据实列
        $db    = DbHelper::getInstance(self::$dbConfName);

        //真实环境使用应该做成单独的方法
        $table = 'userstatic';

        $bdParam = [
            'uid' => $uid,
        ];

        $sql   = "DELETE FROM `{$table}` WHERE `uid` = :uid LIMIT 1";

        try {

            return  $db->execute($sql,$bdParam);

        } catch (Exception $e) {

            // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
            // 使用者根据自己的情况酌情处理
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";

            return false;
        }

    }

    /**
     * 事务
     * @return [type] [description]
     */
    public function testTransaction() {

       // $this->testSelect($uid = '69312');
        //构造删除测式数据
        $deleteUid = $this->testInsert();

        //根据配置文件获取数据实列
        $db   = DbHelper::getInstance(self::$dbConfName);
       // $this->testSelect($uid = '69312');
        //真实环境使用应该做成单独的方法
        $table = 'userstatic';

        try {

            //开启事务
            $db->beginTransaction();

            //插入
            $insetSql    = "INSERT INTO `{$table}`(`username`,`nick`) VALUES(:username,:nick)";
            $insertParam = [
                'username' => 't1',
                'nick' => 'tn1'
            ];

            $db->execute($insetSql,$insertParam);

            //更新
            $updateSql   = "UPDATE `{$table}` SET `nick` = :nick WHERE `uid` = :uid LIMIT 1";
            $updateParam = [
                'uid'  => '69312',
                'nick' => 'test'.mt_rand(1, 100),
            ];

            $db->execute($updateSql,$updateParam);

            //删除
            $deleteSql   = "DELETE FROM `{$table}` WHERE `uid` = :uid LIMIT 1";
            $deleteParam = [
                'uid' => $deleteUid,
            ];
            $db->execute($deleteSql,$deleteParam);

           // $this->testSelect($uid = '69312');

            //:uid,:sex 查询参数占位
            $sql   = "SELECT  `nick`,`username` FROM `{$table}`  LIMIT :sn,:en";
            //参数绑定
            $bdParam = [
            //'uid' => $uid,
            'sn'=> '0','en' => '1'];

            try {
                print_r($db->query($sql,$bdParam));
                print_r($db->setMaster(true)->query($sql,$bdParam));

            } catch (Exception $e) {
                // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
                // 使用者根据自己的情况酌情处理
                echo $e->getCode(),"\n";
                echo $e->getMessage(),"\n";

                return false;
            }

            //提交
            $db->commit();

            //:uid,:sex 查询参数占位
            $sql   = "SELECT  `nick`,`username` FROM `{$table}`  LIMIT :sn,:en";
            //参数绑定
            $bdParam = [
            //'uid' => $uid,
            'sn'=> '0','en' => '1'
            ];

            try {

                print_r($db->setMaster(true)->query($sql,$bdParam));
                print_r($db->query($sql,$bdParam));
            } catch (Exception $e) {
                // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
                // 使用者根据自己的情况酌情处理
                echo $e->getCode(),"\n";
                echo $e->getMessage(),"\n";

            }

            //$this->testSelect($uid = '69312');

            return true;

        } catch (Exception $e) {

            //回滚
            $db->rollback();

            // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
            // 使用者根据自己的情况酌情处理
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";
            return false;
        }

    }

    /**
     * 强读主库
     * @return [type] [description]
     */
    public function testSelectFromMaster($uid = '69312') {
        //根据配置文件获取数据实列
        $db    = DbHelper::getInstance(self::$dbConfName);

        //真实环境使用应该做成单独的方法
        $table = 'userstatic';

        //:uid,:sex 查询参数占位
        $sql   = "SELECT  `nick`,`username` FROM `{$table}` WHERE `uid` = :uid";
        //参数绑定
        $bdParam = ['uid' => $uid];

        try {
            //强读主库
            $db->setMaster(true);
            return $db->query($sql,$bdParam);

        } catch (Exception $e) {
            // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
            // 使用者根据自己的情况酌情处理
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";

            return false;
        }
    }

    /**
     *  select in
     */
    public function testSelectIn($uid=[69312,69318,69315,69305]) {
                //根据配置文件获取数据实列
        $db    = DbHelper::getInstance(self::$dbConfName);

        //真实环境使用应该做成单独的方法
        $table = 'userstatic';

        //绑定占位符
        $in    = $db->buildInPrepare($uid);

        $sql   = "SELECT  `nick`,`username` FROM `{$table}` WHERE `uid` IN({$in})";

        try {
            return $db->query($sql,$uid);

        } catch (Exception $e) {
            // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
            // 使用者根据自己的情况酌情处理
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";

            return false;
        }
    }

       /**
     * 查询
     * @return [type] [description]
     */
    public function testSelectDaemon($uid = '69312') {

        //根据配置文件获取数据实列
        $db    = DbHelper::getInstance(self::$dbConfName);
        while (true)
        {
              //真实环境使用应该做成单独的方法
                $table = 'userstatic';

                //:uid,:sex 查询参数占位
                $sql   = "SELECT  `nick`,`username` FROM `{$table}`  LIMIT :sn,:en";
                //参数绑定
                $bdParam = ['sn'=> '0','en' => '1'];

                try {

                    print_r($db->query($sql,$bdParam));

                } catch (Exception $e) {
                    // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
                    // 使用者根据自己的情况酌情处理
                    echo $e->getCode(),"\n";
                    echo $e->getMessage(),"\n";

                }
                sleep(3);
        }



    }

    public function testMultiTrans()
    {
// $this->testSelect($uid = '69312');
        //构造删除测式数据
        $deleteUid = $this->testInsert();

        //根据配置文件获取数据实列
        $db   = DbHelper::getInstance(self::$dbConfName);
       // $this->testSelect($uid = '69312');
        //真实环境使用应该做成单独的方法
        $table = 'userstatic';

        try {

            //开启事务
            $db->beginTransaction();

            //插入
            $insetSql    = "INSERT INTO `{$table}`(`username`,`nick`) VALUES(:username,:nick)";
            $insertParam = [
                'username' => 't1',
                'nick' => 'tn1'
            ];

            $db->execute($insetSql,$insertParam);

            //更新
            $updateSql   = "UPDATE `{$table}` SET `nick` = :nick WHERE `uid` = :uid LIMIT 1";
            $updateParam = [
                'uid'  => '69312',
                'nick' => 'test'.mt_rand(1, 100),
            ];

            $db->execute($updateSql,$updateParam);

            //删除
            $deleteSql   = "DELETE FROM `{$table}` WHERE `uid` = :uid LIMIT 1";
            $deleteParam = [
                'uid' => $deleteUid,
            ];
            $db->execute($deleteSql,$deleteParam);

           // $this->testSelect($uid = '69312');

            //:uid,:sex 查询参数占位
            $sql   = "SELECT  `nick`,`username` FROM `{$table}`  LIMIT :sn,:en";
            //参数绑定
            $bdParam = [
            //'uid' => $uid,
            'sn'=> '0','en' => '1'];

            try {
                print_r($db->query($sql,$bdParam));
                print_r($db->setMaster(true)->query($sql,$bdParam));

            } catch (Exception $e) {
                // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
                // 使用者根据自己的情况酌情处理
                echo $e->getCode(),"\n";
                echo $e->getMessage(),"\n";

                return false;
            }
            if(!$this->testTransaction())
            {
                $db->rollback();
                return false;
            }

            //提交
            $db->commit();

            //:uid,:sex 查询参数占位
            $sql   = "SELECT  `nick`,`username` FROM `{$table}`  LIMIT :sn,:en";
            //参数绑定
            $bdParam = [
            //'uid' => $uid,
            'sn'=> '0','en' => '1'
            ];

            try {

                print_r($db->setMaster(true)->query($sql,$bdParam));
                print_r($db->query($sql,$bdParam));
            } catch (Exception $e) {
                // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
                // 使用者根据自己的情况酌情处理
                echo $e->getCode(),"\n";
                echo $e->getMessage(),"\n";

            }

            //$this->testSelect($uid = '69312');

            return true;

        } catch (Exception $e) {

            //回滚
            $db->rollback();

            // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
            // 使用者根据自己的情况酌情处理
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";

        }

    }


    public function testM1()
    {
        $db      = DbHelper::getInstance(self::$dbConfName);
        $bdParam = [
            'msg' => 'cat',
        ];

        try {

             //开启事务
            $db->beginTransaction();
            $sql = "INSERT INTO `test`(`msg1`) VALUES(:msg)";
            $db->execute($sql,$bdParam);
            //提交
            $db->commit();

            return true;

        } catch (Exception $e)
        {
            $db->rollback();
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";
            return false;
        }
    }

    public function testM2()
    {
        $db   = DbHelper::getInstance(self::$dbConfName);
        $bdParam = [
            'msg' => 'cat',
        ];

        try {

            //开启事务
            $db->beginTransaction();
            $sql = "INSERT INTO `test_1`(`msg`) VALUES(:msg)";
            $db->execute($sql,$bdParam);

            //从主库查出数据
            print_r($db->setMaster(true)->query('SELECT * FROM `test_1` WHERE `msg` = :msg',$bdParam));

            //嵌套事务
            if (!$this->testM1()) {
                 echo 'rollback()';
                 $db->rollback();
                 return false;
            }

            $bdParam = [
                'msg' => 'cat1',
            ];

            $sql = "INSERT INTO `test_1`(`msg1`) VALUES(:msg)";
            $db->execute($sql,$bdParam);

            //提交
            $db->commit();

            return true;

        } catch (Exception $e)
        {
            $db->rollback();
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";
            return false;
        }

    }

     public function testM3()
    {
        $db   = DbHelper::getInstance(self::$dbConfName);
        $bdParam = [
            'msg' => 'dog',
        ];

        try {

            //开启事务
            $db->beginTransaction();
            $sql = "INSERT INTO `test_1`(`msg`) VALUES(:msg)";
            $db->execute($sql,$bdParam);

            //从主库查出数据
            print_r($db->setMaster(true)->query('SELECT * FROM `test_1` WHERE `msg` = :msg',$bdParam));

            //提交
            $db->commit();

            return true;

        } catch (Exception $e)
        {
            $db->rollback();
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";
            return false;
        }

    }

}

$t = new test();
//var_dump($t->testSelectIn());
//die;
//var_dump($t->testSelectFromMaster());
var_dump($t->testM2());

//var_dump($t->testSelectDaemon());
var_dump($t->testM3());
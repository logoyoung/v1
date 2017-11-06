<?php
require __DIR__.'/../../include/init.php';
use system\DbHelper;
use service\common\LogDbService;

class test  {

    const DB_CONF = 'huanpeng';

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function insertData($n = 500000)
    {
        $db = $this->getDb();

        for ($i = 0; $i < $n; $i++ )
        {
            $param = [
                'uid'    => mt_rand(10000, 500000),
                'type'   => mt_rand(1,9),
                'ac_uid' => mt_rand(10000, 500000),
                'content' => json_encode([mt_rand(10000, 500000),mt_rand(10000, 500000),mt_rand(10000, 500000),mt_rand(10000, 500000),str_pad('aaddweewewewewew', 100,'c')]),
            ];
            $sql = 'insert into `system_log`(`uid`,`type`,`ac_uid`,`content`) values(:uid,:type,:ac_uid,:content)';
            $s = $db->execute($sql,$param);
            var_dump($s);
        }
    }

    public function testAdd()
    {
        $param = [
                'uid'    => mt_rand(10000, 500000),
                'type'   => mt_rand(1,9),
                'ac_uid' => mt_rand(10000, 500000),
                'content' => json_encode([mt_rand(10000, 500000),mt_rand(10000, 500000),mt_rand(10000, 500000),mt_rand(10000, 500000),str_pad('aaddweewewewewew', 100,'c')]),
            ];
        $s = LogDbService::log($param['uid'],$param['content'],$param['type'],$param['ac_uid']);
        var_dump($s);

        $s = LogDbService::log($param['uid'],$param['content'],$param['type']);
        var_dump($s);

        $s = LogDbService::log($param['uid'],$param['content']);
        var_dump($s);

        $s = LogDbService::log($param['uid'],'hihiaa');
        var_dump($s);
    }
}

$obj = new test;
$obj->testAdd();
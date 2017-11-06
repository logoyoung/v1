<?php
require __DIR__.'/../../include/init.php';
use system\RedisHelper;
use system\Timer;

/**
 */
class test
{
    public static $redisConfName = 'huanpeng';

    public function testSet() {
        $redis  = RedisHelper::getInstance(self::$redisConfName);
        $key    = 'hp_set_test';
        $r      = $redis->set($key,'val'.uniqid());
        var_dump($r);


        $result = $redis->setMaster(1)->get($key);
        var_dump($result);

        $r = $redis->delete($key);
        var_dump($r);
    }

    public function testHset()
    {
        $key    = 'name_01';
        $redis  = RedisHelper::getInstance(self::$redisConfName);
        $r      = $redis->hset($key,'age',hp_json_encode([123,456]));
        var_dump($r);

        $result = $redis->hget($key,'age');
        var_dump($result);
    }

    public function testHhset()
    {
        $key    = 'name_01';
        $redis  = RedisHelper::getInstance(self::$redisConfName);
        $r      = $redis->hmset($key,['age',hp_json_encode(['name'=>'dog','sex'=>1])]);
        var_dump($r);

        $result = $redis->hget($key,'age');
        var_dump($result);
    }


    public function hMsetHash1000() {
        $key = 'test_hash_1000';
        $arr = [];
        for ($i = 1; $i <= 1000; $i++)
        {
            $arr[$i] = json_encode(['name'=>uniqid(),'sex'=>uniqid()]);
        }

        $redis  = RedisHelper::getInstance(self::$redisConfName);

        $r      = $redis->hmset($key,$arr);
        var_dump($r);
    }
}


$t = new test();
$t->hMsetHash1000();
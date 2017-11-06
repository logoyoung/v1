<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/8/9
 * Time: 15:47
 */
require __DIR__.'/../../include/init.php';
class test
{
    const redis_CONF = 'huanpeng';
    private $_redis;
    public $arr = [1,2,3,4,5];
    public $key = '_testkey';
    public function getRedis()
    {
        if (is_null($this->_redis))
        {
            $redis = new RedisHelp();
            $this->_redis = $redis->getMyRedis();
        }
        return $this->_redis;
    }
    public function getData()
    {
        $data = var_dump($this->getRedis()->sMembers($this->key));
        if($data)
        {
            var_dump($data);
        }else
        {
            $status = $this->getRedis()->sAddArray($this->key,$this->arr);
            if($status)
            {
                echo 'success';
            }else
            {
                echo 'failled';
            }
        }
    }
}
$test = new test();
$test->getData();

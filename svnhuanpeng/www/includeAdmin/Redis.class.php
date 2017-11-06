<?php

/*
 * redis操作类 
 * date 2015-12-03
 * auther yandong@6rooms.com
 * copyright@六间房version 0.0 
 */

class RedisHelp {

    private $redis = null; //静态实例

    public function __construct($host = '172.20.28.147', $port = 9981) {
        $this->redis = new Redis();
        $this->redis->connect($host, $port);
        return $this->redis;
    }

//    private function __construct() { //私有的构造方法
//        self::$_instance = new Redis();
//        self::$_instance->connect($host = '172.20.28.147', $port = 9981);
//    }
//
//    //获取静态实例
//    public static function getRedis() {
//        if (!self::$_instance) {
//            new self;
//        }
//        return self::$_instance;
//    }
//
//    /*
//     * 禁止clone
//     */
//
//    private function __clone() {
//        
//    }

    /**
     * 设置值  构建一个字符串
     * @param string $key KEY名称
     * @param string $value  设置值
     * @param int $timeOut 时间  0表示无过期时间
     */
    public function set($key, $value, $timeOut = 0) {
        $retRes = $this->redis->set($key, $value);
        if ($timeOut > 0)
            $this->expire($key, $timeOut);
        return $retRes;
    }

    /**
     * 设置过期时间
     * @param type $key 键名
     * @param type $timeOut  过期时间(单位:秒)
     */
    public function expire($key, $timeOut) {
        $this->redis->expire($key, $timeOut);
    }

    /**
     * 通过key获取数据
     * @param string $key KEY名称
     */
    public function get($key) {
        $result = $this->redis->get($key);
        return $result;
    }

    /*
     * 构建一个集合(无序集合)
     * @param string $key 集合Y名称
     * @param string|array $value  值
     */

    public function sadd($key, $value) {
        return $this->redis->sadd($key, $value);
    }

    /*
     * 构建一个集合(有序集合)
     * @param string $key 集合名称
     * @param string|array $value  值
     */

    public function zadd($key, $score, $member) {
        return $this->redis->zadd($key, $score, $member);
    }

    public function zRemRangeByRank($key, $start, $end) {
        return $this->redis->zRemRangeByRank($key, $start, $end);
    }

    public function zRank($key, $member) {
        return $this->redis->zRank($key, $member);
    }

    public function zScore($key, $member) {
        return $this->redis->zScore($key, $member);
    }

    /**
     * 移除有序集key中的一个或多个成员，不存在的成员将被忽略。
     * @param type $key
     * @param type $member
     * @return type
     */
    public function zRem($key, $member) {
        return $this->redis->zRem($key, $member);
    }

    /**
     * 给集合key中的元素$member加上$number，值针对整型  $member存在就在其基础上加,反之相当于执行zadd
     * @param type $key
     * @param type $number
     * @param type $member
     * @return type
     */
    public function zincrby($key, $number, $member) {
        return $this->redis->zIncrBy($key, $number, $member);
    }

    /**
     * 如果$member是有序集 key 的成员，返回$member的排名。 如果成员不是有序集 key 的成员，返回 nill
     * @param type $key
     * @param type $member
     * @return type
     */
    public function zrevrank($key, $member) {
        return $this->redis->zrevrank($key, $member);
    }

    /**
     * 返回有序集中指定区间内的成员,从高到底
     * @param type $key
     * @param type $start
     * @param type $end
     * @param type $withscore
     * @return type
     */
    public function zRevRange($key, $start, $end, $withscore = null) {
        return $this->redis->zRevRange($key, $start, $end, $withscore);
    }

    /**
     * 返回有序集key的基数
     * @param type $key
     * @return type
     */
    public function zcard($key) {
        return $this->redis->ZCARD($key);
    }

    /**
     * 取集合对应元素
     * @param string $setName 集合名字
     */
    public function smembers($setName) {
        return $this->redis->smembers($setName);
    }

    /**
     * 构建一个列表(先进后去，类似栈)
     * @param sting $key KEY名称
     * @param string $value 值
     */
    public function lpush($key, $value) {
//        echo "$key - $value \n";
        return $this->redis->LPUSH($key, $value);
    }

    /**
     * 构建一个列表(先进先去，类似队列)
     * @param sting $key KEY名称
     * @param string $value 值
     */
    public function rpush($key, $value) {
//        echo "$key - $value \n";
        return $this->redis->rpush($key, $value);
    }

    /**
     * 获取所有列表数据（从头到尾取）
     * @param sting $key KEY名称
     * @param int $head  开始
     * @param int $tail  结束
     */
    public function lranges($key, $head, $tail) {
        return $this->redis->lrange($key, $head, $tail);
    }

    /**
     * HASH类型
     * @param string $tableName  表名字key
     * @param string $key        字段名字
     * @param sting $value       值
     */
    public function hset($tableName, $field, $value) {
        return $this->redis->hset($tableName, $field, $value);
    }

    public function hget($tableName, $field) {
        return $this->redis->hget($tableName, $field);
    }

    public function hgetAll($key) {
        return $this->redis->hGetAll($key);
    }

    public function hdel($key, $hashKey1) {
        return $this->redis->hDel($key, $hashKey1);
    }

    /**
     * 设置多个值
     * @param array $keyArray KEY名称
     * @param string|array $value 获取得到的数据
     * @param int $timeOut 时间
     */
    public function sets($keyArray, $timeout) {
        if (is_array($keyArray)) {
            $retRes = $this->redis->mset($keyArray);
            if ($timeout > 0) {
                foreach ($keyArray as $key => $value) {
                    $this->expire($key, $timeout);
                }
            }
            return $retRes;
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }

    /**
     * 同时获取多个值
     * @param array $keyArray 获key数值
     */
    public function gets($keyArray) {
        if (is_array($keyArray)) {
            return $this->redis->mget($keyArray);
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }

    /**
     * 获取所有key名，不是值
     */
    public function keyAll() {
        return $this->redis->keys('*');
    }

    /**
     * 删除一条数据key
     * @param string $key 删除KEY的名称
     */
    public function del($key) {
        return $this->redis->delete($key);
    }

    /**
     * 同时删除多个key数据
     * @param array $keyArray KEY集合
     */
    public function dels($keyArray) {
        if (is_array($keyArray)) {
            return $this->redis->del($keyArray);
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }

    /**
     * 数据自增
     * @param string $key KEY名称
     */
    public function increment($key) {
        return $this->redis->incr($key);
    }

    /**
     * 数据自减
     * @param string $key KEY名称
     */
    public function decrement($key) {
        return $this->redis->decr($key);
    }

    /**
     * 判断key是否存在
     * @param string $key KEY名称
     */
    public function isExists($key) {
        return $this->redis->exists($key);
    }

    /**
     * 重命名- 当且仅当newkey不存在时，将key改为newkey ，当newkey存在时候会报错哦RENAME
     *  和 rename不一样，它是直接更新（存在的值也会直接更新）
     * @param string $Key KEY名称
     * @param string $newKey 新key名称
     */
    public function updateName($key, $newKey) {
        return $this->redis->RENAMENX($key, $newKey);
    }

    /**
     * 获取KEY存储的值类型
     *
     * none(key不存在) int(0)  string(字符串) int(1)   list(列表) int(3)  set(集合) int(2)   zset(有序集) int(4)    hash(哈希表) int(5)
     *
     * @param string $key KEY名称
     *
     */
    public function dataType($key) {
        return $this->redis->type($key);
    }

    /**
     * 清空数据
     */
    public function flushAll() {
        return $this->redis->flushAll();
    }

    /**
     * Evaluate a LUA script serverside

     *  @param  string  $script

     *  @param  array   $args

     *  @param  int     $numKeys

     *  @return Mixed.  What is returned depends on what the LUA script itself returns, which could be a scalar value

     *  (int/string), or an array. Arrays that are returned can also contain other arrays, if that's how it was set up in

     *  your LUA script.  If there is an error executing the LUA script, the getLastError() function can tell you the

     *  message that came back from Redis (e.g. compile error).
     */
    public function evals($script, $args = array(), $numKeys = 0) {
        return $this->redis->evaluate($script, $args, $numKeys);
    }

    public function getLastError() {
        return $this->redis->getLastError();
    }

}

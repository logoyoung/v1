<?php
namespace system;
use redis;
use Exception;
use system\Timer;
use system\Crc;

class RedisHelper
{
    private static $instance  = [];
    private static $config    = [];

    /**
     * 获取redis实列
     * @param  string $redisName [description]
     * @param  boolean $debug
     * @return redis
     */
    public static function getInstance($redisName,$debug = false) {
        if(isset(self::$instance[$redisName]) && (self::$instance[$redisName] instanceof RedisConnection)){
            return self::$instance[$redisName];
        }
        self::$instance[$redisName] = new RedisConnection(self::getConf($redisName),$redisName,$debug);
        return self::$instance[$redisName];
    }

    public static function getConf($redisName) {
        if(!self::$config){
            self::initRedisConf();
        }
        if(!isset(self::$config[$redisName]['master']) || !isset(self::$config[$redisName]['slave'])) {
            throw new Exception("redis {$redisName} doesn't exist");
        }

        return self::$config[$redisName];
    }

    public static function clear($key = '') {
        if($key && isset(self::$instance[$key])){
            unset(self::$instance[$key]);
            return true;
        }
        self::$instance = [];
        return true;
    }

    private static function initRedisConf() {
        self::$config = get_redis_conf();
        return;
    }

}

class RedisConnection {

    protected $masterConfig = [];
    protected $slaveConfig  = [];
    protected $master       = 0;
    protected $debug        = false;
    protected $redisName;
    protected $prefixCommon;
    protected $writeRedisCount;
    protected $readRedisCount;
    protected $connection   = [];
    protected $retryNum     = 1;
    protected $qt           = 0.2;

    public function __construct(array $config,$name,$debug = false) {
        $this->redisName    = $name;
        $this->prefixCommon = $config['prefix'];
        $this->masterConfig = $config['master'];
        $this->slaveConfig  = $config['slave'];
        $this->debug        = $debug ? true : ($config['debug'] ? true : false);
        $config             = null;
        $name               = null;
    }

    public function connect($config) {
        $connectionKey = $this->getConnectionKey($config);
        $redisConfig   = $config['redisConfig'];

        if(isset($this->connection[$connectionKey])){
            $redis = $this->connection[$connectionKey];

            if(!IS_CLI) {
                return $redis;
            }

            try {
                if($redis->ping()) {
                    return $redis;
                }
            } catch (Exception $e) {
                $this->log('redis_access','notice|connect timeout'.$e->getMessage(),$config);
            }
            unset($this->connection[$connectionKey]);
        }


        $timer = new Timer();
        $timer->start();

        $redis  = new redis();
        $status = $redis->connect($redisConfig['host'],$redisConfig['port'],$redisConfig['timeout'],$redisConfig['auth']);
        $timer->end();
        $t      = $timer->getTime();
        $timer  = null;

        if(!$status){
            $this->log('redis_error',"error|connect error connection_time:{$t}s",$config);
            $status = null;
            return false;
        }

        $this->connection[$connectionKey] = $redis;

        if($this->debug || $t >= $this->qt){
            $this->log('redis_access',"connection_time:{$t}s",$config);
        }


        return $redis;
    }

    public function setMaster($master = 0) {
        $this->master = (int) $master;
        return $this;
    }

    public function __call($method, $args){
        $args[0] = isset($args[0]) ? ($this->prefixCommon ? $this->prefixCommon.$args[0] : $args[0]) : $method;
        $retry   = $this->retryNum;
        $result  = false;
        $timer   = new Timer();
        $timer->start();

        do {

            try {

                $config  = $this->getConfigByKeyCmd($args[0],$method,$this->master);
                $redis   = $this->connect($config);

                if(!$redis){
                    $timer = null;
                    return $result;
                }

                $result  = call_user_func_array([$redis, $method], $args);
                $timer->end();
                $t       = $timer->getTime();

                if($this->debug || $t >= $this->qt){
                    $this->log('redis_access',"excute time:{$t} method:{$method} master:{$config['master']}",$config,$args);
                }

                break;

            } catch (Exception $e) {

                $connectionKey = $this->getConnectionKey($config);
                if( isset($this->connection[$connectionKey]) ){
                    unset($this->connection[$connectionKey]);
                }

                if($retry <= 0 || Cmd::isWriteCmd($method)){
                    $this->log('redis_error',"error|excute error msg:{$e->getMessage()},master:{$config['master']}",$config,$args);
                    break;
                }
                $this->log('redis_error',"warning|retry msg:{$e->getMessage()},master:{$config['master']}",$config,$args);
            }

        } while( $retry-- > 0);

        $timer   = null;
        $this->setMaster(0);

        return $result;
    }

    public function getConnectionKey($config) {
        return $config['master'].'_'.$config['serverNum'].'_'.$config['redisNum'];
    }

    public function getConfigByKeyCmd($key,$cmd,$master = 0) {
        $keyHashInt   = Crc::x_crc32($key);
        $cluster      = [];
        $clusterCount = 0;

        if(Cmd::isWriteCmd(strtolower($cmd)) || $master) {
            $master                = 1;
            $cluster               = $this->masterConfig;
            $this->writeRedisCount = $this->writeRedisCount ?: count($cluster);
            $clusterCount          = $this->writeRedisCount;
        } else {
            $cluster               = $this->slaveConfig;
            $this->readRedisCount  = $this->readRedisCount ?: count($cluster);
            $clusterCount          = $this->readRedisCount;
        }

        if($clusterCount < 1 ) {
            $this->log('redis_error',"empty redis config key:{$key},cmd:{$cmd},master:{$master}");
            throw new Exception("empty redis config key:{$key},cmd:{$cmd},master:{$master}");
        }

        $serverNum   = ($keyHashInt % $clusterCount);
        $config      = isset($cluster[$serverNum]) ? $cluster[$serverNum] : $cluster[0];
        $redisNum    = mt_rand(0, count($config) - 1);
        $redisConfig = $config[$redisNum];

        return  [
                    'master'       => $master,
                    'serverNum'    => $serverNum,
                    'redisNum'     => $redisNum,
                    'redisConfig'  => $redisConfig,
        ];
    }

    public function log($name,$msg='',$config=[],$param=[]) {
        $msg = 'redis name:'.$this->redisName.' '.$msg;
        if($config){
            $redisConfig = $config['redisConfig'];
            $msg .= " master:{$config['master']};host:{$redisConfig['host']},port:{$redisConfig['port']},timeout:{$redisConfig['timeout']},auth:{$redisConfig['auth']}";
        }
        if($param){
            $msg .= '; param:'.json_encode($param);
        }

        write_log($msg,APP_NAME.'_'.$name);
    }
}

class Cmd
{

    public static function isWriteCmd($cmd) {
        $cmds = [
                'del'         => 1,
                'delete'      => 1,
                'expire'      => 1,
                'expireat'    => 1,
                'move'        => 1,
                'persist'     => 1,
                'rename'      => 1,
                'renamenx'    => 1,
                'sort'        => 1,
                'append'      => 1,
                'set'         => 1,
                'decr'        => 1,
                'decrby'      => 1,
                'getset'      => 1,
                'incr'        => 1,
                'incrby'      => 1,
                'mset'        => 1,
                'msetnx'      => 1,
                'setbit'      => 1,
                'setex'       => 1,
                'setnx'       => 1,
                'setrange'    => 1,
                'hdel'        => 1,
                'hincrby'     => 1,
                'hmset'       => 1,
                'hset'        => 1,
                'hsetnx'      => 1,
                'blpop'       => 1,
                'brpop'       => 1,
                'brpoplpush'  => 1,
                'linsert'     => 1,
                'lpop'        => 1,
                'lpush'       => 1,
                'lpushx'      => 1,
                'lrem'        => 1,
                'lremove'     => 1,
                'lset'        => 1,
                'rpop'        => 1,
                'rpoplpush'   => 1,
                'rpush'       => 1,
                'rpushx'      => 1,
                'sadd'        => 1,
                'smove'       => 1,
                'spop'        => 1,
                'srem'        => 1,
                'sunionstore' => 1,
                'zadd'        => 1,
                'zincrby'     => 1,
                'zinterstore' => 1,
                'zrem'        => 1,
                'zremrangebyrank'  => 1,
                'zremrangebyscore' => 1,
                'zunionstore' => 1,
                'multi'       => 1,
                'exec'        => 1,
                'ltrim'       => 1,
        ];

        return isset($cmds[$cmd]);
    }

}

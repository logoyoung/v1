<?php
namespace system;
use PDO;
use Exception;
use PDOException;
use system\Timer;

class DbHelper {

    private static $instance      = [];
    private static $inTransaction = [];
    private static $config        = [];

    /**
     * 获取数据库连接
     * @param  string  $dbKey 数据库名 如 huanpeng
     * @param  boolean $debug 开启调式模式（默认走配置文件）
     * @return pdo         [description]
     */
    public static function getInstance($dbKey,$debug = false)
	{
        if(isset(self::$instance[$dbKey]) && (self::$instance[$dbKey] instanceof MysqlConnection)){
            return self::$instance[$dbKey];
        }
        self::$instance[$dbKey] = new MysqlConnection(self::getConfig($dbKey),$dbKey,$debug);
        return self::$instance[$dbKey];
    }

    /**
     *  开启 多库事务
     * @param  array  $instance
     * @return [type]           [description]
     */
    public static function beginMultiTrans(array $instance = []) {
        self::freeTrans();
        self::$inTransaction =  $instance;
        try {
            foreach ( self::$inTransaction as $connection) {
                $connection->beginTransaction();
            }
            return true;
        } catch ( Exception $e) {
            throw $e;
        }
    }

    /**
     * 提交 多库事务
     * @return [type] [description]
     */
    public static function commitMulti() {
        if(!self::$inTransaction){
            return false;
        }
        foreach (self::$inTransaction as $connection){
            $connection->commit();
        }
        self::freeTrans();
        return true;
    }

    /**
     * 回滚多库事务
     * @return [type] [description]
     */
    public static function rollbackMulti() {
        if(!self::$inTransaction){
            return false;
        }
        foreach ( self::$inTransaction as $connection) {
            $connection->rollback();
        }
        self::freeTrans();
        return true;
    }

    /**
     *  移除某个数据连接
     * @param  string $key 数据库名
     * @return [type]      [description]
     */
    public static function clear($key = '') {
        if($key && isset(self::$instance[$key])){
            unset(self::$instance[$key]);
            return true;
        }
        self::$instance = [];
        return true;
    }

    /**
     * 获取某个数据库配置
     * @param  [type] $dbKey 数据库名
     * @return [type]        [description]
     */
    public static function getConfig($dbKey) {
        if(!self::$config){
            self::initDbConfig();
        }
        if(!isset(self::$config[$dbKey]['master']) || !isset(self::$config[$dbKey]['slave'])) {
            throw new Exception("mysql database {$dbKey} doesn't exist");
        }
        return self::$config[$dbKey];
    }

    /**
     * 移除所有数据连接
     * @return [type] [description]
     */
    private static function freeTrans() {
        if(self::$inTransaction){
            foreach ( self::$inTransaction as $connection) {
                if( $connection instanceof MysqlConnection){
                    $connection->close();
                }
            }
        }
        self::$inTransaction = [];
        return;
    }

    /**
     * 根据环境初始化数据库配置
     * @return void
     */
    private static function initDbConfig() {
        self::$config = [];
        self::$config = get_hp_mysql_conf();
        return;
    }
}

class MysqlConnection {

    protected  $options = [
        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES  => false,
    ];

    protected $fetchMode    = PDO::FETCH_ASSOC;
    protected $config       = [];
    protected $readPdo      = null;
    protected $writePdo     = null;
    protected $resource     = null;
    protected $transactions = 0;
    protected $masterConfig = [];
    protected $slaveConfig  = [];
    protected $master       = false;
    protected $retryNum     = 1;
    protected $debug        = false;
    protected $database;
    protected $writeConfig;
    protected $readConfig;
    protected $qt           = 0.3;

    public function __construct(array $config,$database,$debug = false) {
        $this->database     = $database;
        $this->masterConfig = $config['master'];
        $this->slaveConfig  = $config['slave'];
        $this->debug        = $debug ? true : ($config['debug'] ? true : false);
        $config             = null;
        $database           = null;
    }

    /**
     * 强制使用主库
     * @param boolean $master [description]
     */
    public function setMaster($master = true) {
        $this->master = (bool) $master;
        return $this;
    }

    public function buildFieldsParam(array $fields) {
        $fields  = array_map(function ($v) {
            return '`'.$v.'`';
        }, $fields);

        return implode(',', $fields);
    }

    /**
     *  构建in查询 占位符
     * @param  array  $param
     * @return string
     */
    public function buildInPrepare(array $param) {
        return implode(',', (array_fill(0, count($param), '?')));
    }

    /**
     * 执行查询
     * @param  string $sql
     * @param  array  $bindParameters 绑定参数
     * @param  boolean $debug 强制开启debug模式会记录sql 执行时间 （默认走数据库配置）
     * @param  int $fetchMode  返回数据类型
     *                      PDO::FETCH_OBJ   返回一个属性名对应结果集列名的匿名对象
     *                      PDO::FETCH_ASSOC 返回一个索引为结果集列名的数组
     * @return array |obj |false [description]
     */
    public function query($sql, array $bindParameters = [], $debug = false, $fetchMode = PDO::FETCH_ASSOC) {

        $timer  = new Timer();
        $timer->start();
        $retry  = $this->retryNum;

        do {

            try {

                $this->createPdo($this->master);
                $result = false;

                $statement = $this->resource->prepare($sql);
                $this->bindParameters($statement,$bindParameters);
                $statement->execute();
                $result = $statement->fetchAll($fetchMode);

                $timer->end();
                $t      = $timer->getTime();

                if($this->debug || $debug || $t >= $this->qt) {
                    $this->logSql(
                        $sql,
                        [
                            'bindParam'  => $bindParameters,
                            'master'     => $this->master,
                            'config'     => ($this->master  ? $this->writeConfig : $this->readConfig),
                            'run_time'   => $t.'s',
                            'fetchMode'  => $fetchMode,
                        ]
                    );
                 }

                $timer = null;
                $t     = 0;

                break;

            } catch (PDOException $e) {
                if($this->transactions !== 0 || $retry <= 0 || !$this->hasGoneAway($e)){
                    $timer->end();
                    $this->logError(
                        $e,
                        [
                             'sql'        => $sql,
                             'bindParam'  => $bindParameters,
                             'master'     => $this->master,
                             'run_time'   => $timer->getTime().'s',
                             'try_num'    => ($this->retryNum - $retry),
                             'config'     => ($this->master ? $this->writeConfig : $this->readConfig),
                        ]
                     );
                    $timer = null;
                    $this->setMaster(false);
                    throw $e;
                }
                $this->close();
                $this->createPdo($this->master);
            }

        } while( $retry-- > 0);
        $this->setMaster(false);

        return $result;

    }

    public function execute($sql,array $bindParameters = [],$debug = false) {

        $timer  = new Timer();
        $timer->start();
        $retry  = $this->retryNum;

        do {

            try {

                $this->createPdo(true);
                $statement = $this->resource->prepare($sql);
                $this->bindParameters($statement,$bindParameters);
                $statement->execute();

                $timer->end();
                $t     = $timer->getTime();
                if($this->debug || $debug || $t >= $this->qt) {
                    $this->logSql(
                        $sql,
                        [
                            'bindParam'    => $bindParameters,
                            'master'       => true,
                            'run_time'     => $t.'s',
                            'config'       => $this->writeConfig,
                        ]
                    );
                }

                $timer = null;
                $t     = 0;

                return $statement->rowCount();

            } catch (PDOException $e) {
                if($this->transactions !== 0 || $retry <= 0 || !$this->hasGoneAway($e)){
                    $timer->end();
                    $this->logError(
                        $e,
                        [
                            'sql'          => $sql,
                            'bindParam'    => $bindParameters,
                            'master'       => true,
                            'run_time'     => $timer->getTime().'s',
                            'try_num'      => ($this->retryNum - $retry),
                            'config'       => $this->writeConfig,
                        ]
                    );
                    $timer = null;

                    throw $e;
                }

                $this->close();
                $this->createPdo(true);
            }

        } while( $retry-- > 0);

    }

    public function getLastInsertId() {
        if ($this->resource instanceof PDO) {
            return $this->resource->lastInsertId();
        }
        throw new Exception('Invalid PDO connection');
    }

    public function beginTransaction() {
        ++$this->transactions;
        if($this->transactions === 1)
        {
            $this->resource    = null;
            $this->writePdo    = null;
            $this->writeConfig = null;
            try {
                $this->createPdo(true);
                $this->resource->beginTransaction();
            } catch (PDOException $e) {
                --$this->transactions;
                $this->logError(
                    $e,
                    [
                        'sql'    => 'beginTransaction',
                        'master' => true,
                        'config' => $this->writeConfig,
                    ]
                );
                throw $e;
            }
        }

    }

    public function commit() {
        --$this->transactions;
        if ($this->transactions === 0) {
            $this->createPdo(true);
            $this->resource->commit();
        }
    }

    public function rollback() {
        --$this->transactions;
        if ($this->transactions === 0) {
            $this->createPdo(true);
            $this->resource->rollBack();
        }
    }

    public function commitAll() {
        if ($this->transactions !== 0) {
            $this->createPdo(true);
            $this->resource->commit();
            $this->transactions = 0;
        }
    }

    public function rollbackAll() {
        if($this->transactions !== 0) {
            $this->createPdo(true);
            $this->resource->rollBack();
            $this->transactions = 0;
        }
    }

    public function close() {
        $this->writePdo    = null;
        $this->readPdo     = null;
        $this->resource    = null;
        $this->readConfig  = null;
        $this->writeConfig = null;
        $this->transactions = 0;
        return;
    }

    protected function createPdo($master) {
        try {
            $this->resource = $this->connect($master);
        } catch (PDOException $e) {
            throw $e;
        }
        return;
    }

    protected function connect($master = false) {
        $config  = [];
        if($master) {
            if($this->writePdo instanceof PDO){
                return $this->writePdo;
            }
            $config = $this->masterConfig;
        } else {
            if($this->readPdo instanceof PDO){
                return $this->readPdo;
            }
            $config = $this->slaveConfig;
        }

        try {

            $config  = $config[mt_rand(0, count($config) - 1)];
            $dns     = $this->constructPdoDsn($config);
            if(!IS_CLI)
            {
                static $_s  = [];
                static $_e  = '';
                $_sk = Crc::x_crc32($dns);
                if (isset($_s[$_sk]) && $_s[$_sk] === false)
                {
                    if($_e instanceof PDOException)
                    {
                        throw $_e;
                    }

                    throw new PDOException('SQLSTATE[HY000] [2002] Connection timed out');
                }
            }

            $options = (array) $config['options'] + $this->options;

            $timer   = new Timer();
            $timer->start();

            $pdo     = new PDO($dns, $config['username'], $config['password'], $options);

            $timer->end();
            $t       = $timer->getTime();
            $log     = [
                    'db'              => $this->database,
                    'connection_time' =>  $t.'s',
                    'master'          => $master ? true : false,
                    'config'          => $config,
                    'options'         => $options,
                    'sql'             => 'connect mysql',
            ];

            if($this->debug || $t > $this->qt ) {
                $this->log($log,'mysql_connect');
                $this->log($log,'mysql_sql');
            }

            $t     = 0;
            $timer = null;
            $log   = null;

            if($master){
                $this->writePdo    = $pdo;
                $this->writeConfig = $config;
            } else {
                $this->readPdo     = $pdo;
                $this->readConfig  = $config;
            }
            $config = [];
            return $pdo;
        } catch(PDOException $e) {
            if (!IS_CLI)
            {
                $_s[$_sk] = false;
                $_e       = $e;
            }

            $this->logError($e,$config);
            throw $e;
        }

    }

    protected function bindParameters($statement,array $bindParameters = []) {
        foreach ($bindParameters as $name => &$value) {
            $type      = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $parameter = is_int($name)  ? ($name + 1)    : $name;
            $statement->bindParam($parameter, $value, $type);
        }
        return;
    }

    protected function constructPdoDsn(array $config) {
        $dsn = 'mysql:';
        if (isset($config['host'])) {
            $dsn .= 'host=' . $config['host'] . ';';
        }
        if (isset($config['port'])) {
            $dsn .= 'port=' . $config['port'] . ';';
        }
        if (isset($config['dbname'])) {
            $dsn .= 'dbname=' . $config['dbname'] . ';';
        }
        if (isset($config['unix_socket'])) {
            $dsn .= 'unix_socket=' . $config['unix_socket'] . ';';
        }
        if (isset($config['charset'])) {
            $dsn .= 'charset=' . $config['charset'] . ';';
        }
        return $dsn;
    }

    protected function hasGoneAway(PDOException $e) {
        return ( $e->getCode() == 'HY000' || stristr($e->getMessage(), 'server has gone away'));
    }

    public function __call($method, $args){
        if ($this->resource instanceof PDO) {
            return call_user_func_array([$this->resource, $method], $args);
        }
        throw new Exception("Invalid {$method} function");
    }

    protected function logError($e, array $config = []) {
        $message     = [];
        $message['db']            = $this->database;
        $message['error_code']    = $e->getCode();
        $message['error_message'] = $e->getMessage();
        if($config){
            $sql            = isset($config['sql']) ? $this->debugRawSql($config['sql'], $config['bindParam']) : '';
            $message        = array_merge($message,$config);
            $message['sql'] = $sql;
            $config         = [];
        }
        $message['trace'] = $e->getTraceAsString();
        $this->log($message,'mysql_error');
        $message     = [];
    }

    protected function logSql($sql, array $param = []) {
        $message        = [];
        $message['db']  = $this->database;
        $message['sql'] = $sql;
        if($param){
            $message['sql'] = $this->debugRawSql($sql,$param['bindParam']);
            $message = array_merge($message,$param);
            $param   = [];
        }

        $this->log($message,'mysql_sql');
        $message = [];
    }

    /**
     *  组装成原始sql 主要用于调式记录sql使用
     * @param  string $query
     * @param  array $params 绑定参数
     * @return string
     */
    public function debugRawSql($sql, $params) {
        if (!$params)
        {
            return $sql;
        }

        $keys   = [];
        $params = array_map(function ($v){
            return addcslashes($v,'$');
        }, $params);
        $values = $params;

        foreach ($params as $key => $value) {

            $keys[] = is_string($key) ? '#:'.$key.'#' : '#[?]#';

            if (is_array($value)) {
                $values[$key] = implode(',', $value);
            }

            if (is_null($value)) {
                $values[$key] = "NULL";
            }
        }

        array_walk($values, function (&$v,$k) {
            if ($v != "NULL" ) {
                $v = is_numeric($v) ? $v : "'{$v}'";
            }
        });

        $sql = preg_replace($keys, $values, $sql, 1, $count);

        return $sql;
    }

    private function log($msg,$logName) {
        write_log($msg,$logName);
        return ;
    }

    public function __destruct() {
        $this->close();
    }
}
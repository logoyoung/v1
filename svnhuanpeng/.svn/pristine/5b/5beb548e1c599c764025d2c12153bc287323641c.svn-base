<?php
/**
 * MySQLi连接管理
 *
 * class DBHelperi: MySQLi读写分离连接管理类
 *
 * @author Lance Li <lance@6rooms.com>
 * @copyright 6.cn
 */

/**
 * 读库类型ID
 */
define("DBHELPERI_DBR", 1);

/**
 * 写库类型ID
 */
define( "DBHELPERI_DBW", 2 );

define( "DBHELPERI_DBALL", DBHELPERI_DBR|DBHELPERI_DBW );

/**
 * DBHelperi: MySQLi连接管理类
 *
 * 本类实现了读写分离的数据库连接管理，用户可以不用关心操作的是哪个连接或者哪个库。
 * 查询的结果返回给客户管理，这主要是为了提供更大的灵活性。
 *
 */
class DBHelperi
{

    /**
     * 最后一次写操作影响的行数
     * @link http://www.php.net/manual/en/mysqli.affected-rows.php
     * @access public
     */
    public $affectedRows;

    /**
     * 最后一次 INSERT ID
     * @link http://php.chinaunix.net/manual/en/mysqli.insert-id.php
     * @access public
     */
    public $insertID;

    /**
     * 调试模式(boolean)：true 调试；false 不调试(默认)
     * @access private
     */
    protected $_debug;

    /**
     * 执行模式(boolean)：true 执行SQL范围检查(默认) false 不执行
     * @access private
     */
    protected $_strict;

    /**
     * 是否自动连接(boolean)：true (默认) false
     * @access private
     */
    protected $_auto_connect;

    /**
     * 是否自动检查连接状态，并在断开时重新连接(boolean)：true 是 false 否（默认）
     * @access private
     */
    protected $_auto_reconnect;

    /**
     * MySQLi 读库连接对象：默认NULL
     * @access private
     */
    protected $_dbr;

    /**
     * MySQLi 写库连接对象：默认NULL
     * @access private
     */
    protected $_dbw;

    /**
     * 读库连接信息(string)：形如 mysql://username:password@host:port/database
     * @access private
     */
    protected $_dbr_url;

    /**
     * 写库连接信息(string)：形如 mysql://username:password@host:port/database
     * @access private
     */
    protected $_dbw_url;

    /**
     * 错误编号(integer)：负值表示错误行号，正值表示MySQLi错误号
     * @access private
     */
    protected $_errno;

    /**
     * 错误描述(string)：MySQLi错误描述或者类代码错误
     * @access private
     */
    protected $_errstr;

    /**
     * 最近一次调用的SQL语句类型
     * @access private
     */
    protected $_lastQueryType;

    /**
     * DBHelper构造函数
     *
     * @access public
     * @param debug bool 是否启动调试模式 default false
     * @param strict bool 是否启动强制SQL范围检查 default true
     * @param auto_connect bool 是否自动根据查询类型连接数据库 default true
     * @param auto_reconnect bool 是否自动重新连接断掉的数据库连接 default false，此选项同时被 mysqli.reconnect 限制
     */
    public function __construct( $debug=false, $strict=true, $auto_connect=true,
                                 $auto_reconnect=false )
    {
        $this->_debug = $debug;
        $this->_strict = $strict;
        $this->_auto_connect = $auto_connect;
        if ( $auto_connect and ini_get('mysqli.reconnect')=='0' )
        {
            $this->_setError(-__LINE__, "cannot set auto_reconnect w/o mysqli.reconnect");
            return false;
        }
        $this->_auto_reconnect = $auto_reconnect;

        $this->affectedRows = 0;
        $this->insertID = 0;

        $this->_dbr = NULL;
        $this->_dbw = NULL;
        $this->_dbr_url = NULL;
        $this->_dbw_url = NULL;

        $this->_errno = 0;
        $this->_errstr = NULL;

        $this->_lastQueryType = NULL;

        return true;
    }

    /**
     * 连接MySQL数据库
     * 如果设置了 $this->_auto_connect，本函数一般不需要在程序中直接调用
     *
     * @access public
     * @param type int 要连接的数据库类别
     * @param permanent bool 是否打开永久连接（实际没用）
     * @return bool
     */
    public function connect($type = DBHELPERI_DBALL, $permanent = false )
    {
        $dbr = NULL;
        $dbw = NULL;

        if (!$this->_prepareConnection($type)) return false;

        if ($type & DBHELPERI_DBR)
        {
            $dbr = $this->_connect($permanent, $this->_dbr_url);
            if (!$dbr) return false;
        }

        if ( $type & DBHELPERI_DBW )
        {
            $dbw = $this->_connect($permanent, $this->_dbw_url);
            if (!$dbw)
            {
                if ($dbr) $dbr->close();
                return false;
            }
        }

        if ( $dbr ) $this->_dbr = $dbr;
        if ( $dbw ) $this->_dbw = $dbw;

        return true;
    }

    /**
     * 断开数据库连接
     *
     * @access public
     * @param type int 断开的数据库连接类型
     * @return 无返回值
     */
    function disconnect($type = DBHELPERI_DBALL)
    {
        if ($type & DBHELPERI_DBR)
        {
            if ($this->_dbr) @$this->_dbr->close();
            $this->_dbr = NULL;
        }

        if ($type & DBHELPERI_DBW)
        {
            if ($this->_dbw) @$this->_dbw->close();
            $this->_dbw = NULL;
        }

    }

    /**
     * 选择数据库连接
     *
     * @access public
     * @param dbname 数据库名
     * @param type 选择数据库连接类型，默认选择所有连接类型
     * @return bool
     */
    function selectDB($dbname, $type = DBHELPERI_DBALL)
    {
        if ( ($type & DBHELPERI_DBR) && !@$this->_dbr->select_db($dbname) )
        {
            $this->_setError($this->_dbr->errno(), $this->_dbr->error());
            return false;
        }

        if ( ($type & DBHELPERI_DBW) && !@$this->_dbw->select_db($dbname) )
        {
            $this->_setError($this->_dbw->errno(), $this->_dbw->error());
            return false;
        }

        return true;
    }

    /**
     * 获取MySQL连接对象
     *
     * @access public
     * @param type int 获取的数据库连接类型，不填时默认使用 $this->_lastQueryType
     * @return 返回数据库连接对象
     */
    public function getDB( $type=NULL )
    {
        if ( is_null($type) ) $type = $this->_lastQueryType;
        if ( is_null($type) ) $type = DBHELPERI_DBR;

        if ($type == DBHELPERI_DBR)
        {
            if ( is_null($this->_dbr) ) // 如果没连
            {
                if ( $this->_auto_connect ) // 如果设置了自动连接
                {
                    if ( !$this->connect(DBHELPERI_DBR) )
                    {
                        $this->_setError(-__LINE__, "DBHELPERI_DBR connect failed");
                        return false; // 自动连接失败
                    }
                    $dbObj = $this->_dbr;
                }
                else
                {
                    $this->_setError(-__LINE__, "DBHELPERI_DBR connection needed");
                    return false;
                }
            }
            else
            {
                $dbObj = $this->_dbr;
                if ( $this->_auto_reconnect and !$this->ping(DBHELPERI_DBR) )
                {
                    if ( !$this->connect(DBHELPERI_DBR) )   // 如果设置了自动重连就连接，不使用mysqli自动的重连机制
                    {
                        $this->_setError(-__LINE__, "DBHELPERI_DBR connect failed");
                        return false;
                    }
                    $dbObj = $this->_dbr;
                }
            }
        }
        elseif ($type == DBHELPERI_DBW)
        {
            if ( is_null($this->_dbw) )
            {
                if ( $this->_auto_connect )
                {
                    if ( !$this->connect(DBHELPERI_DBW) )
                    {
                        $this->_setError(-__LINE__, "DBHELPERI_DBW connect failed");
                        return false;
                    }
                    $dbObj = $this->_dbw;
                }
                else
                {
                    $this->_setError(-__LINE__, "DBHELPERI_DBW connection needed");
                    return false;
                }
            }
            else
            {
                $dbObj = $this->_dbw;
                if ( $this->_auto_reconnect and !$this->ping(DBHELPERI_DBW) )
                {
                    if ( !$this->connect(DBHELPERI_DBW) )
                    {
                        $this->_setError(-__LINE__, "DBHELPERI_DBW connect failed");
                        return false;
                    }
                    $dbObj = $this->_dbw;
                }
            }
        }
        else
        {
            $this->_setError(-__LINE__, "DBObj type illegal: $type");
            return false;
        }

        return $dbObj;
    }

    /**
     * 检查 MySQLi 连接是否正常
     * 包装 mysqli::ping
     *
     * @link http://www.php.net/manual/en/mysqli.ping.php
     * @access public
     * @param type 数据库连接类型
     * @return 返回数据库连接对象
     */
    public function ping( $type=NULL )
    {
        if ( is_null($type) )
            $type = $this->_lastQueryType;

        if ($type == DBHELPERI_DBR)
        {
            return $this->_dbr->ping();
        }
        elseif ($type == DBHELPERI_DBW)
        {
            return $this->_dbw->ping();
        }
        else
        {
            $this->_setError(-__LINE__, "DBObj type illegal: $type");
            return false;
        }
    }

    /**
     * 包装 mysqli::autocommit
     *
     * @link http://www.php.net/manual/en/mysqli.autocommit.php
     * @access public
     * @param mode bool
     * @param type int 数据库连接类型
     * @return bool
     */
    public function autocommit( $mode, $type=DBHELPERI_DBW )
    {
        if ( !$dbObj = $this->getDB($type) ) return false;
        return $dbObj->autocommit($mode);
    }

    /**
     * 包装 mysqli::commit
     *
     * @link http://www.php.net/manual/en/mysqli.commit.php
     * @access public
     * @param type int 数据库连接类型
     * @return bool
     */
    public function commit( $type=DBHELPERI_DBW )
    {
        if ( !$dbObj = $this->getDB($type) ) return false;
        return $dbObj->commit();
    }

    /**
     * 包装 mysqli::rollback
     *
     * @link http://www.php.net/manual/en/mysqli.rollback.php
     * @access public
     * @param type int 数据库连接类型
     * @return bool
     */
    public function rollback( $type=DBHELPERI_DBW )
    {
        if ( !$dbObj = $this->getDB($type) ) return false;
        return $dbObj->rollback();
    }

    /**
     * 查询数据库
     * 包装 mysqli::query
     *
     * @link http://www.php.net/manual/en/mysqli.query.php
     * @access public
     * @param sql string 待查询的SQL语句
     * @param type int 数据库连接类型，默认自动检测
     * @param resultmode int 对应 mysqli_query 中参数
     * @return 对于更新语句，返回true或者false，否则返回执行结果
     */
    public function query($sql, $type=NULL, $resultmode=MYSQLI_STORE_RESULT )
    {
        $this->clear();

        if ( !$check = $this->checkSQLType($sql) ) return false;

        if ( is_null($type) ) $type = $check;

        if ( !$dbObj = $this->getDB($type) ) return false;

        $this->_lastQueryType = $type;
        $res = $dbObj->query( $sql, $resultmode );
        $this->affectedRows = $dbObj->affected_rows;
        $this->insertID = $dbObj->insert_id;

        if (!$res)
            $this->_setError($dbObj->errno, $dbObj->error.":SQL:$sql");
        elseif ($this->_debug)
            $this->_debugMessage("QUERY:TYPE $type:SQL:$sql");

        return $res;
    }

    /**
     * 多 SQL 查询数据库
     * 包装 mysqli::multi_query
     *
     * @link http://www.php.net/manual/en/mysqli.multi-query.php
     * @access public
     * @param $sql 待查询的SQL语句
     * @param $type 数据库连接类型，默认自动检测
     * @return 对于更新语句，返回true或者false，否则返回执行结果
     */
    public function multiQuery( $sql, $type=NULL )
    {
        $this->clear();

        if ( !$check = $this->checkMultiSQLType($sql) ) return false;

        if ( is_null($type) ) $type = $check;

        if ( !$dbObj = $this->getDB($type) ) return false;

        $this->_lastQueryType = $type;
        $r = $dbObj->multi_query( $sql );
        $this->affectedRows = $dbObj->affected_rows;
        $this->insertID = $dbObj->insert_id;

        if ( !$r )
            $this->_setError($dbObj->errno, $dbObj->error.":SQL:$sql");
        elseif ( $this->_debug )
            $this->_debugMessage("multiQuery:TYPE $type:SQL:$sql");

        return $r;
    }

    /**
     * 多重 SQL 查询时生成结果集
     * 包装 mysqli::store_result
     *
     * @link http://www.php.net/manual/en/mysqli.store-result.php
     * @access public
     * @param type 数据库连接类型，默认自动检测
     * @return mixed 成功时返回 MySQLi_STMT object，否则返回 false
     */
    public function storeResult( $type=NULL )
    {
        if ( !$dbObj = $this->getDB($type) ) return false;
        $r = $dbObj->store_result();

        if ( !$r )
            $this->_setError($dbObj->errno, $dbObj->error);
        elseif ( $this->_debug )
            $this->_debugMessage("storeResult: $type");

        return $r;
    }

    /**
     * 多重 SQL 查询时生成结果集
     * 包装 mysqli::use_result
     *
     * @link http://www.php.net/manual/en/mysqli.use-result.php
     * @access public
     * @param type 数据库连接类型，默认自动检测
     * @return mixed 成功时返回 MySQLi_STMT object，否则返回 false
     */
    public function useResult( $type=NULL )
    {
        if ( !$dbObj = $this->getDB($type) ) return false;
        $r = $dbObj->use_result();

        if ( !$r )
            $this->_setError($dbObj->errno, $dbObj->error);
        elseif ( $this->_debug )
            $this->_debugMessage("useResult: $type");

        return $r;
    }

    /**
     * 多 SQL 查询时准备下一结果集
     * 包装 mysqli::next_result
     *
     * @link http://www.php.net/manual/en/mysqli.next-result.php
     * @access public
     * @param type 数据库连接类型，默认自动检测
     * @return bool 成功时返回 true，否则返回 false
     */
    public function nextResult( $type=NULL )
    {
        if ( !$dbObj = $this->getDB($type) ) return false;
        $r = $dbObj->next_result();

        if ( !$r )
            $this->_setError($dbObj->errno, $dbObj->error);
        elseif ( $this->_debug )
            $this->_debugMessage("nextResult: $type");

        return $r;
    }

    /**
     * 多 SQL 查询时检查是否还有下一结果集
     * 包装 mysqli::more_results
     *
     * @link http://www.php.net/manual/en/mysqli.more-results.php
     * @access public
     * @param type 数据库连接类型，默认自动检测
     * @return bool 成功时返回 true，否则返回 false
     */
    public function moreResults( $type=NULL )
    {
        if ( !$dbObj = $this->getDB($type) ) return false;
        return $dbObj->more_results();
    }

    /**
     * 准备资源查询数据库
     * 包装 mysqli::prepare
     *
     * @link http://www.php.net/manual/en/mysqli.prepare.php
     * @access public
     * @param sql string 待查询的SQL语句
     * @param type int 数据库连接类型，默认自动检测
     * @return 失败时返回 false，成功时返回 statement handle
     */
    public function prepare($sql, $type = NULL )
    {
        $this->clear();

        if ( !$check = $this->checkSQLType($sql) ) return false;

        if ( is_null($type) ) $type = $check;

        if ( !$dbObj = $this->getDB($type) ) return false;

        $this->_lastQueryType = $type;
        $res = $dbObj->prepare( $sql );

        if (!$res)
            $this->_setError($dbObj->errno, $dbObj->error.":SQL:$sql");
        elseif ($this->_debug)
            $this->_debugMessage("prepare: $type:SQL:$sql");

        return $res;
    }

    /**
     * 检查SQL语句的类型
     *
     * 本函数检查SQL语句的类型，如果是更新操作且strict模式打开，则做WHERE检查
     * 成功返回数据库连接类型，否则返回false
     *
     * @access public
     * @param sql 待检查的SQL
     * @return 数据库连接类型, 或者false
     */
    public function checkSQLType($sql)
    {
        $act_update = array("update", "delete", "replace", "insert",
            "create", "drop", "alter", "load", "flush", "kill", "lock",
            "grant", "revoke", "optimize");
        $act_check = array("update", "delete");

        $act = substr($sql, 0, 20);
        list($act) = explode(" ", trim($act));
        $act = strtolower($act);

        foreach ($act_update as $key)
        {
            if ($key == $act)
            {
                if ($this->_strict && (array_search($act, $act_check) !== false) &&
                    !stristr($sql, 'where'))
                {
                    $this->_setError(-__LINE__, "SQL checking failed:$act:$sql");
                    return false;
                }

                return DBHELPERI_DBW;
            }
        }

        return DBHELPERI_DBR;
    }

    /**
     * 检查多重SQL语句的类型
     *
     * 本函数检查多重SQL语句(以;分隔的多句sql)的类型，如果其中包含更新
     * 操作且strict模式打开，则做WHERE检查
     *
     * 多句sql中只要包含一句写操作，即返回数据库写库，否则返回读库
     *
     * 成功返回数据库连接类型，否则返回false
     *
     * @access public
     * @param sqls 待检查的多重SQL
     * @return mixed 数据库连接类型, 或者false
     */
    public function checkMultiSQLType($sqls)
    {
        $sqls = trim($sqls);
        if ( substr($sqls,-1)!=';' ) $sqls .= ';';  // 最后需要以 ; 结尾

        $pattern = '/(.*?(([\'"])[^\\3]*\\3.*?)?;)/';   // sql 匹配模板

        $mat = array();
        $type = DBHELPERI_DBR;
        preg_match_all( $pattern, $sqls, $mat );    // 将多句 sql 拆成单独的语句
        foreach ( $mat[1] as $sql )
        {
            $r = $this->checkSQLType(trim($sql));
            if ( !$r ) return false;
            if ( $r == DBHELPERI_DBW ) $type = $r;  // 只要有一个是写结果就是写
        }

        return $type;
    }

    /**
     * 转义字符串
     * 包装 mysqli::real_escape_string
     *
     * @link http://www.php.net/manual/en/mysqli.real-escape-string.php
     * @access public
     * @param string string 待转义的字符串
     * @param type int 数据库连接类型
     * @return mixed 失败时返回 false，成功时返回字符串
     */
    public function realEscapeString( $string, $type=NULL )
    {
        if ( !$string ) return '';
        if ( !$dbObj = $this->getDB($type) ) return false;
        return $dbObj->real_escape_string( $string );
    }

    /**
     * 获取最后一次查询的类型
     *
     * @access public
     * @return mixed 数据库连接类型, 或者NULL
     */
    public function getLastQueryType()
    {
        return $this->_lastQueryType;
    }

    /**
     * 设置或查询调试模式
     *
     * @access public
     * @param mode bool 将要设置的调试模式，不提供时为查询当前模式
     * @return bool 调试模式
     */
    public function debug($mode = NULL)
    {
        if (!is_null($mode)) $this->_debug = $mode;
        return $this->_debug;
    }

    /**
     * 设置或查询执行模式
     *
     * @access public
     * @param mode bool 将要设置的执行模式，不提供时为查询当前模式
     * @return bool 执行模式
     */
    public function strict($mode = NULL)
    {
        if (!is_null($mode)) $this->_strict = $mode;
        return $this->_strict;
    }

    /**
     * 设置或查询自动连接状态
     *
     * @access public
     * @param mode bool 将要设置的自动连接状态，不提供时为查询当前状态
     * @return bool 执行模式
     */
    public function autoConnect($mode = NULL)
    {
        if (!is_null($mode)) $this->_auto_connect = $mode;
        return $this->_auto_connect;
    }

    /**
     * 设置或查询自动重连接状态
     *
     * @access public
     * @param mode bool 将要设置的自动重连接状态，不提供时为查询当前状态
     * @return bool 执行模式
     */
    public function autoReconnect($mode = NULL)
    {
        if (!is_null($mode)) $this->_auto_reconnect = $mode;
        return $this->_auto_reconnect;
    }

    /**
     * 是否发生错误
     *
     * @access public
     * @return 发生错误返回true，否则返回false
     */
    public function error()
    {
        return ($this->_errno != 0);
    }

    /**
     * 取得错误代码
     *
     * @access public
     * @return int 错误代码
     */
    public function errno()
    {
        return $this->_errno;
    }

    /**
     * 取得错误描述
     *
     * @access public
     * @return string 错误描述
     */
    public function errstr()
    {
        return $this->_errstr;
    }

    /**
     * 清除发生的错误信息和上次查询信息
     *
     * @access public
     * @return bool(true)
     */
    public function clear()
    {
        $this->affectedRows = 0;
        $this->insertID = 0;
        $this->_errno = 0;
        $this->_errstr = NULL;
        $this->_lastQueryType = NULL;
        return true;
    }

    /**
     * 连接数据库
     *
     * @access protected
     * @param permanent bool 是否永久连接
     * @param urlstring string 数据库连接描述
     * @return mixed 成功时返回连接 handler，失败时返回 false
     */
    protected function _connect($permanent, $urlstring)
    {
        $url = parse_url($urlstring);

        if ( $url['scheme'] == 'mysql' )
        {
            if ( $permanent ) $url['host'] = 'p:'.$url['host'];
            $dbname = substr($url['path'], 1);
            $timer  = new \system\Timer();
            $timer->start();

            $dbh = new mysqli( $url['host'], $url['user'], $url['pass'], $dbname, $url['port'] );

            $timer->end();
            $t   = $timer->getTime();

            if($t > 0.3)
            {
                write_log("warning|连接用时{$t}秒,db_host:{$url['host']},db_port:{$url['port']}",'mysqli_connect');
            }

            if ($dbh->connect_error)
            {
                $this->_setError($dbh->connect_errno, $dbh->connect_error. " Failed connecting db: $urlstring");
                return false;
            }

            return $dbh;
        }

        return NULL;
    }

    /**
     * 设置出错信息
     *
     * @access protected
     * @param errno int 编号
     * @param errstr string 描述
     */
    protected function _setError($errno, $errstr)
    {
        $this->_errno = $errno;
        $this->_errstr = $errstr;
        write_log("error|{$this->_errno};{$this->_errstr}",'mysqli_error');
        if ($this->_debug) $this->_debugMessage("ERROR:[$errno]$errstr");
    }

    /**
     * DEBUG 输出信息
     *
     * @access protected
     * @param msg string
     */
    protected function _debugMessage($msg)
    {
        print "DEBUG MESSAGE:$msg\n";
    }

    /**
     * 准备数据库连接
     *
     * @access protected
     * @param type int 数据库连接类型
     * @return bool
     */
    protected function _prepareConnection($type = DBHELPERI_DBALL)
    {
        if ($type & DBHELPERI_DBR) $this->_dbr_url = DBHELPERI_DBR_URL;
        if ($type & DBHELPERI_DBW) $this->_dbw_url = DBHELPERI_DBW_URL;

        if ($this->_debug)
        {
            if ($type & DBHELPERI_DBR) $this->_debugMessage("DBR URL:".$this->_dbr_url);
            if ($type & DBHELPERI_DBW) $this->_debugMessage("DBW URL:".$this->_dbw_url);
        }

        return true;
    }
}

?>
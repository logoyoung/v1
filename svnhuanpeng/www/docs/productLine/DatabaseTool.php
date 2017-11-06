<?php

/**
 * 
 * @author liupeng
 * @since version
 */
require_once __DIR__ . '/../../include/init.php';

use system\DbHelper;

abstract class lineBase {

    protected $logFile;

    abstract public function __invoke();

    abstract public function __toString();

    public function line() {
        $arguments = func_get_args();
        if (!empty($arguments)) {
            $this->text(call_user_func_array('sprintf', $arguments));
        }
        $this->output(PHP_EOL);

        return $this;
    }

    public function text($text) {
        $text = preg_replace('~<r>(.+)</r>~', chr(27) . '[41m' . ' $1 ' . chr(27) . '[0m', $text);
        $text = preg_replace('~<g>(.+)</g>~', chr(27) . '[42m' . ' $1 ' . chr(27) . '[0m', $text);
        $text = preg_replace('~<b>(.+)</b>~', chr(27) . '[44m' . ' $1 ' . chr(27) . '[0m', $text);
        $this->output($text);

        return $this;
    }

    protected function output($output) {
        echo $output;

        if ($this->logFile) {
            if (!file_exists($this->logFile)) {
                touch($this->logFile);
                @chmod($this->logFile, 0777);
                clearstatcache();
            }
            file_put_contents($this->logFile, $output, FILE_APPEND);
        }
    }

}

class DatabaseTool extends lineBase {

    /**
     * 文件名格式规则
     */
//    const FILE_NAME_RULE = '/^\d{4}_\d{2}_\d{2}_\d{6}_(.*)\.php/';
    const FILE_NAME_RULE = '/^((?<dir>\d{1,}_\d{1,}_\d{1,})_(?<file>\d{4,10}_(?<class>.*)))\.php/';

    /**
     * 新建数据库语句文件目录
     * @var type 
     */
    protected $storagePath;
    protected $jsonLog = LOG_DIR . 'database_json.log';
    protected $logArray = '';

    /**
     * 数据库配置
     * @var type 
     */
    protected $dbConfig = 'huanpeng';
    public $db = null;

    public function __construct() {


        if (!$this->getDb()) {
            throw new Exception('数据库支持失败');
        }




        $this->storagePath = __DIR__ . '/';
        $this->logFile = LOG_DIR . 'database_exec.log';
    }

    public function getDb() {
        if (!$this->db) {
            $this->db = DbHelper::getInstance($this->dbConfig);
        }
        return $this->db;
    }

    public function getRunTimes($key) {
        $message = @file_get_contents($this->storagePath . '.limitNum');
        $message = empty($message) ? "[]" : $message;
        $arr = json_decode($message, TRUE);
        return $arr[$key] ?? 0;
    }

    public function addRunTimes($key) {
        $message = @file_get_contents($this->storagePath . '.limitNum');
        $message = empty($message) ? "[]" : $message;
        $arr = json_decode($message, TRUE);
        if (!isset($arr[$key])) {
            $arr[$key] = 0;
        }
        $arr[$key] ++;
        $content = json_encode($arr);
        file_put_contents($this->storagePath . '.limitNum', $content);
    }

    public function __invoke() {
        $help = <<<EOF
********************************************************************************           
使用方法格式:  php DatabaseTool.php method [param] 
method: ls、 status、 execute、help
        
********************************************************************************
sql文件规范:
    1. 建表语句必须需加上:IF NOT EXISTS
    2. 注释必须顶头书写,以3个# 开头
    3. 字段修改单独放在一个文件中执行,防止重复执行不通过
********************************************************************************              
                
EOF;
//         create  filename    创建数据执行文件模板
#create filename
        $this->line($help);
    }

    public function help() {
        $this();
    }

    public function __toString() {
        return __CLASS__;
    }

    public function ls() {

        $fileList = $this->getSqlFileList();

        if (empty($fileList)) {
            $this->line('数据创建文件列表为空');
            return;
        }

        $last = $this->getStatus();

        $index = 0;
        foreach ($fileList as $name => $file) {
            $index += 1;

            if ($name == $last) {
                $format = '#%-4d<g>%s</g>';
            } else {
                $format = '#%-4d %s';
            }

            $this->line($format, $index, $name);
        }
    }

    public function status() {
        $last = $this->getStatus();
        if ($last === false) {
            $this->line('没有数据创建记录');
            return;
        }

        $this->line('已执行的最后记录为: %s.', $last);
    }

    public function create($name = '') {
        $this->line('暂时不支持');
        return;
        if (empty($name)) {
            $this->line('数据创建文件名为空');
            return;
        }

        if ($this->index($name) !== false) {
            $this->line('数据创建文件名  %s 已经存在', $name);
            return;
        }

        $example = file_get_contents($this->storagePath . '/example.php');
        if (empty($example)) {
            $this->line('数据创建模板文件不存在');
            return;
        }
        $name = $this->checkName($name);
        if (empty($name)) {
            $this->line('文件名称不合法,请用纯英文(最好是驼峰规则)');
            return;
        }

        $example = str_replace('example', $name, $example);

        $filename = sprintf($this->getFileNamePrefix() . '_%s.php', strtolower($name));
        file_put_contents($this->storagePath . '/' . $filename, $example);

        $this->line('数据文件 %s 创建成功', $filename);
    }

    protected function getFileNamePrefix() {
        return date('Y_m_d_His');
    }

    /**
     * 名称检测
     * @param type $name
     */
    protected function checkName($name) {
        $res = preg_match('/^[A-z]+$/', $name);
        if ($res) {
            return $name;
        } else {
            return '';
        }
    }

    protected function index($name) {
        $list = $this->getSqlFileList();
        return array_search($name, array_keys($list));
    }

    protected function getStatus() {
        $lockfile = $this->storagePath . '/.lock';
        if (is_file($lockfile)) {
            $last = trim(file_get_contents($lockfile));
            if (!empty($last)) {
                return $last;
            }
        }
        return false;
    }

    protected function getSqlFileList() {
        $regex = self::FILE_NAME_RULE;
        $handler = opendir($this->storagePath);
        $files = array();
        while ($file = readdir($handler)) {
            if ('dir' == filetype($this->storagePath . $file) && !in_array($file, ['.', '..']) && $file > '1_0_4') {

                $versionDir = $this->storagePath . $file;
                $versionDirHandler = opendir($versionDir);
                while ($sonfile = readdir($versionDirHandler)) {
                    if (in_array($sonfile, ['.', '..'])) {
                        continue;
                    }
                    $tmpFileName = $file . '_' . $sonfile;
                    if (preg_match($regex, $tmpFileName, $matches)) {
                        $files[] = $tmpFileName;
                    }
                }
            } else {
                #file
                #空,此目录禁止写文件
            }
        }

        closedir($handler);

        $resultFile = array();
        natsort($files);
        foreach ($files as $file) {
            preg_match($regex, $file, $matches);
            $resultFile[$matches[1]] = $file;
        }
        return $resultFile;
    }

    public function execute($sqlfile = null) {
        $last = $this->getStatus();
        if (empty($sqlfile) && !$last) {
            $this->line('初次使用,请指定SQL文件');
            return;
        }

        $sqlList = $this->getSqlFileList();

        if ($sqlfile) {
            $index = $this->index($sqlfile);
            if ($index === false) {
                $this->line('数据文件 %s 不存在', $sqlfile);
                return;
            }
            $sqlList = array_slice($sqlList, $index, 1);
        } else {
            $last = $this->getStatus();
            if ($last) {
                $index = $this->index($last);
                if ($index === false) {
                    $this->line('数据创建文件名存在,但是文件已不存在');
                } else {
                    $sqlList = array_slice($sqlList, $index + 1);
                    if (!empty($sqlList)) {
                        $this->line('执行创建文件 %s 之后的文件', $last);
                    }
                }
            }
        }

        if (empty($sqlList)) {
            $this->line('没有文件需要执行');
            return;
        }

        $this->line('执行创建文件数量:(%d):', count($sqlList));

        $index = $this->exec($sqlList);

        $this->line();
        $this->line(str_repeat('=', 60));

        if ($index >= count($sqlList)) {
            $this->line('已经完成');
        } else {
            $this->line('中途跳出');
        }

        if ($this->logFile) {
            $this->line('记录日志保存路径: %s', realpath($this->logFile));
        }
    }

    protected function exec($sqlList) {
        $index = 0;
        foreach ($sqlList as $name => $sqlfile) {
            $index += 1;
            $this->line(str_repeat('_', 60));
            $this->line("%-60.60s", '#' . $index . ' ' . $name);
            if ($this->up($sqlfile)) {
                $this->addRunTimes($sqlfile);
                $this->line('<g>SUCCESS</g>');
                file_put_contents($this->storagePath . '/.lock', $name);
            } else {
                $this->line('<r>FAILED</r>');
            }
        }

        return $index;
    }

    protected function up($sqlfile) {
        preg_match(self::FILE_NAME_RULE, $sqlfile, $matchs);
        $filePath = $this->storagePath . DIRECTORY_SEPARATOR . $matchs['dir'] . DIRECTORY_SEPARATOR . $matchs['file'] . '.php';
        try {

            require_once $filePath;
            $todo = new $matchs['class']($this);
            $num = $this->getRunTimes($sqlfile);
            if ($todo->limitNum > 0 && $num >= $todo->limitNum) {
                $this->line('<r>超过了规定的执行次数:' . $todo->limitNum . '</r>');
                return FALSE;
            }
            $res = $todo->up();
            return $res;
        } catch (Exception $exc) {
            $this->line($exc->getMessage());
            return false;
        }
    }

    public function execSql($sqls) {
        try {
            $db = $this->getDb();

//开启事务
            $db->beginTransaction();
            $array = explode(';', $sqls);
            foreach ($array as $sql) {
                $sql = trim($sql);
                if (empty($sql)) {
                    continue;
                }
                $db->execute($sql);
            }
            $db->commit();
            $res = TRUE;
        } catch (Exception $e) {
            $this->line($sql);
//回滚
            $db->rollback();
            $this->line($e->getMessage());
            $res = FALSE;
        }

        if ($res) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function __call($method, $param) {
        throw new Exception("方法{$method}不存在");
    }

}

try {
    if (!isset($argv[0]) || !isset($argv[1])) {
        throw new Exception("命令错误");
    } else {
        $method = $argv[1];
    }
    $arguments = [];
    if (isset($argv[2])) {
        $arguments = [$argv[2]];
    }
    $dbCon = new DatabaseTool();
    call_user_func_array([$dbCon, $method], $arguments);
} catch (Exception $exc) {
    $message = $exc->getMessage();
    $format = chr(27);
    $help = <<<EOF
-----------------------------------------------------------------           
help: php DatabaseTool.php help
{$format}[41m {$message} {$format}[0m
-----------------------------------------------------------------            
\n
EOF;
    echo $help;
}


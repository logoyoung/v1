<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年8月18日
 * Time: 下午4:58:28
 * Desc: 每天查下月表是否生成
 */
namespace tools;

$tableSql = include 'monthTableConfig.php';
include __DIR__."/../../include/init.php";
use service\weixin\WeiXinEnterpriseService;
use system\DbHelper;
// return $tableSql; 要生成的数据表前缀、对应的创表SQL
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

ignore_user_abort();
set_time_limit(0);

class createMonthTables
{ 
    private $wxEnter = NULL;
    private $db      = NULL;
    private $dbConfName = 'huanpeng';
    
    public function __construct(){
        if(is_null($this->wxEnter))
            $this->wxEnter = new WeiXinEnterpriseService();
        if(is_null($this->db))
            $this->db = DbHelper::getInstance($this->dbConfName);
    }
    //各个月表前缀 
    public function executeNow(array $data){
        try {
            foreach($data as $k => $v){
                $suffix = $this->makeMonth();
                $table = $k.$suffix;
                if($this->issetTable($table) == false){
                    $sql = str_replace("$$",$table, $v);
                    $res = $this->db->query($sql);
//                     var_dump($res);
//                     if(!$res) throw new \Exception("DB warning ；运行环境： dev； 近10秒内，创建{$table}表失败");
                    usleep(1);
                }else{
                    write_log("DB warning ；运行环境： dev； 近10秒内，{$table}表已存在",'createMonthTable');
                }
            }
        } catch (\Exception $e) {
            write_log($e->getMessage(),'createMonthTable');
//             $this->popWarning($e->getMessage());  //注释 预留>>调用微信 报警机制   xuyong@6.cn 做了db错误报警，如果有问题，徐勇脚本会监控统一报错
        }
    }
    //月份处理
    private function makeMonth(){
        $month = date("m")+1; //当前月份 +1个月
        $year  = date("Y");
        if(mb_strlen($month)==1)
        {
            return $year."0". $month;
        }else
        {
            if($month> 12)
            {
                $year+=1;
                return $year."01";
            }else
            {
                return $year.$month;
            }
        }
    }
    //检查表是否存在
    private function issetTable($table){
        $data = $this->db->query("select TABLE_NAME from INFORMATION_SCHEMA.TABLES where TABLE_NAME='".$table."';"); 
        return isset($data[0]['TABLE_NAME']) && $data[0]['TABLE_NAME'] == $table ? true : false;
    }
    //邢伟 同志的接口报警
    private function popWarning($msg){
        $this->wxEnter->sendTextByDepartmentId($msg);
    }
}
$cretObj = new createMonthTables();
$cretObj->executeNow($tableSql);


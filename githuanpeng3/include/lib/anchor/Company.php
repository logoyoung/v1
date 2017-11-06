<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/7/14
 * Time: 12:03
 */

namespace lib\anchor;
use system\DbHelper;
/**
 * 经济公司
 * Class Company
 * @package lib\anchor
 */
class Company
{
    //db 配置文件的key
    const DB_CONF = 'huanpeng';

    public static $fields = [
        'id',// int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '经纪公司id',
        'name',// varchar(100) NOT NULL DEFAULT '' COMMENT '经纪公司名称',
        'status',// tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0使用,1关闭',
        'ctime',// timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        'type',// tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1经纪公司,2家族',
        'owner_id',// int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经纪公司或工会所有者id',
        'rate',//tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '公司汇率 如:70表示3:7分',
        'txtrate',//tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '税率',
        'bankid',// int(10) unsigned NOT NULL DEFAULT '0' COMMENT '银行id',
        'cardid',// varchar(20) NOT NULL DEFAULT '' COMMENT '银行卡号',
        'ownername',// varchar(30) NOT NULL DEFAULT '' COMMENT '姓名',
        'papersid',// varchar(18) NOT NULL DEFAULT '' COMMENT '身份证号',
        'bankaddress',// varchar(255) NOT NULL DEFAULT '' COMMENT '开户行',
    ];
    /**
     * 定义表名
     * @return string
     */
    public function tableName()
    {
        return 'company';
    }

    /**
     * 获取所有经济公司信息
     * @return bool|\PDOStatement
     */
    public function getAllCompany()
    {
        $db      = $this->getDb();
        $fields = self::$fields;
        $table = $this->tableName();
        $fields  = $db->buildFieldsParam($fields);
        $sql   = "SELECT {$fields} FROM `{$table}` ";
        try {
            return  $db->query($sql);

        } catch (Exception $e) {
            return false;
        }
    }

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }
}
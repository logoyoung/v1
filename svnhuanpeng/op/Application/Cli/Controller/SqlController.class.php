<?php
/*
 * 新建、修改数据库表
 */
namespace Cli\Controller;
class SqlController extends \Think\Controller{

	static  $table = [
		'red_packet' => "
			### 红包表
			CREATE TABLE IF NOT EXISTS  `red_packet` (
			`mid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '红包id',
			`name` varchar(50) NOT NULL DEFAULT '' COMMENT '红包名称',
			`price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '红包金额',
			`condition` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用条件',
			`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
			`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
			`utime` timestamp NOT NULL DEFAULT '0000-00-00' COMMENT '修改时间',
			PRIMARY KEY(mid)
			)ENGINE=InnoDB DEFAULT CHARSET=latin1;
			",
		'red_record' =>"
			### 红包记录
			CREATE TABLE IF NOT EXISTS  `red_record` (
			`rid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录id',
			`mid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '红包id',
			`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
			`nick` varchar(100) NOT NULL DEFAULT '' COMMENT '用户昵称',
			`mname` varchar(50) NOT NULL DEFAULT '' COMMENT '红包名称',
			`price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '红包金额',
			`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
			`orderid`  bigint(20) NOT NULL DEFAULT '0' COMMENT '订单id',
			`channel` int(10) NOT NULL DEFAULT '0' COMMENT '发放渠道', 
			`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
			`stime` timestamp NOT NULL DEFAULT '0000-00-00' COMMENT '开始时间',
			`etime` timestamp NOT NULL DEFAULT '0000-00-00' COMMENT '结束时间',
			PRIMARY KEY(rid),
			KEY(mid),
			KEY(uid)
			)ENGINE=InnoDB DEFAULT CHARSET=latin1;
			",
		'log_due_appeal' => "
			###约单日志
			CREATE TABLE IF NOT EXISTS `log_due_appeal`(
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录id',
			`rid`  int(10) NOT NULL DEFAULT '0' COMMENT 'id',
			`uid`  int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
			`uname`  varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
			`adminid`  int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理id',
			`aname`  varchar(50) NOT NULL DEFAULT '' COMMENT '管理名',
			`opt` varchar(50) NOT NULL DEFAULT '' COMMENT '操作',
			`reason` varchar(200) NOT NULL DEFAULT '' COMMENT '理由',
			`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
			`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
			PRIMARY KEY(`id`)
			)
		",
		'log_due_comment' => "
			###约单日志
			CREATE TABLE IF NOT EXISTS `log_due_comment`(
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录id',
			`rid`  int(10) NOT NULL DEFAULT '0' COMMENT 'id',
			`uid`  int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
			`uname`  varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
			`adminid`  int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理id',
			`aname`  varchar(50) NOT NULL DEFAULT '' COMMENT '管理名',
			`opt` varchar(50) NOT NULL DEFAULT '' COMMENT '操作',
			`reason` varchar(200) NOT NULL DEFAULT '' COMMENT '理由',
			`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
			`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
			PRIMARY KEY(`id`)
			)
		",
		'log_due_cert' => "
			###约单日志
			CREATE TABLE IF NOT EXISTS `log_due_cert`(
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录id',
			`rid`  int(10) NOT NULL DEFAULT '0' COMMENT 'id',
			`uid`  int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
			`uname`  varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
			`adminid`  int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理id',
			`aname`  varchar(50) NOT NULL DEFAULT '' COMMENT '管理名',
			`opt` varchar(50) NOT NULL DEFAULT '' COMMENT '操作',
			`reason` varchar(200) NOT NULL DEFAULT '' COMMENT '理由',
			`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
			`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
			PRIMARY KEY(`id`)
			)
		",

	];

	public function createtable($table = null){
		if(empty($table)){
			echo "input incorret table name\n";
			return false;
		}
		if(!in_array($table,array_keys(self::$table))){
			echo "table name not defined\n";
			return false;
		}
		$r = D()->execute(self::$table[$table]);
		var_dump($r);
		var_dump(D()->getLastSql());
		return $r;
	}

}
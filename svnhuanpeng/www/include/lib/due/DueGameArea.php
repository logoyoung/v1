<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/6
 * Time: 14:08
 */

namespace lib\due;
use system\DbHelper;

class DueGameArea
{
    //db 配置文件的key
    public static $dbConfName = 'huanpeng';
    private $_db = null;
    /**
     * 初始化类
     * @param $uid
     * @param string $db
     */
    public function __construct( $db = '' )
    {
        if( $db )
        {
            $this->_db = $db;
        }
        else
        {
            $this->_db = DbHelper::getInstance(self::$dbConfName);
        }
        return true;
    }
    /**
     * 定义表名
     * @return string
     */
    public function tableName()
    {
        return 'due_game_area';
    }
    /**
     * 查询游戏区 按游戏id
     * @return array|bool
     */
    public function getArea($game_id)
    {
        $table = $this->tableName();
        //查询主播约玩资质
        $sql   = "SELECT `id`,  `game_id`,`area`  FROM `{$table}`  WHERE `game_id` = :game_id ORDER BY ctime DESC ";
        //参数绑定
        $bdParam = [
            'game_id' =>  $game_id,
        ];

        try {
            return  $this->_db->query($sql);

        } catch (Exception $e) {
            // sql 语法错误或者数据库连接异常，这种情况 mysql_error.log都会有日志记录的，
            // 使用者根据自己的情况酌情处理
            echo $e->getCode(),"\n";
            echo $e->getMessage(),"\n";
            return false;
        }
    }
}
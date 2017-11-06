<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/8/8
 * Time: 11:53
 */

namespace lib\robot;

use Exception;
use system\DbHelper;

class Robot
{
    const DB_CONF = 'huanpeng';
    const USERNAME ='hpRobot';
    private $_page = 1;
    private $_pageSize = 20;
    public static $table2_fields = [
        'id',
        'uid',
        'num',
        'time',
        'status',
        ];
    public static $table3_fields = [
        'id',
        'msg',
    ];
    public function setPage($page)
    {
        if($page)
        {
            $this->_page = $page;
            return $this;
        }
    }
    public function setPageSize($pageSize)
    {
        if($pageSize)
        {
            $this->_pageSize = $pageSize;
            return $this;
        }
    }
    public function getAllRobotUids()
    {
        $sql = "SELECT `uid`  FROM `{$this->getTable()}` WHERE `username`= :username";
        $bdParam = [
            'username' => self::USERNAME ,
        ];
        $db = $this->getDb();
        try
        {
            return  $db->query($sql,$bdParam);
        }catch (Exception $e) {
            return false;
        }
    }

    /**
     * 获取所有后台主播观众设定
     * @return array|bool|false|\PDOStatement|\system\obj
     */
    public function getAllAdminAnchorViewerInfo()
    {
        $fields = self::$table2_fields;
        $db = $this->getDb();
        $fields = $db->buildFieldsParam($fields);
        $sql = "SELECT {$fields} FROM `{$this->getTable2()}`";
        try
        {
            return  $db->query($sql);
        }catch (Exception $e) {
            return false;
        }
    }

    public function selectAdminAnchorViewerInfo()
    {
        $fields = self::$table2_fields;
        $db = $this->getDb();
        $fields = $db->buildFieldsParam($fields);
        $sql = "SELECT `id`,`uid`,`num`,`status` FROM `{$this->getTable2()}` ORDER BY id DESC  LIMIT :page,:pageSize";
        $this->_page = ($this->_page-1)*$this->_pageSize;
        $bdParam = [
            'page'=> $this->_page,
            'pageSize'=> $this->_pageSize,
        ];
        try
        {
            return  $db->query($sql,$bdParam);
        }catch (Exception $e) {
            return false;
        }

    }
    public function getTotalRoomRobotViewer()
    {
        $fields = self::$table2_fields;
        $db = $this->getDb();
        $fields = $db->buildFieldsParam($fields);
        $sql = "SELECT count(*) as total FROM `{$this->getTable2()}`";
        try
        {
            return  $db->query($sql);
        }catch (Exception $e) {
            return false;
        }
    }
    public function getTotalSearchViewerInfo($data)
    {
        if(is_array($data['uid']))
        {
            $in    = implode(',',$data['uid']);
        }else
        {
            $in = $data['uid'];
        }
        $fields = self::$table2_fields;
        $db = $this->getDb();
        $fields = $db->buildFieldsParam($fields);
        $sql = "SELECT count(*) as total FROM `{$this->getTable2()}` WHERE uid in ({$in})";
        $this->_page = ($this->_page-1)*$this->_pageSize;
        try
        {
            return  $db->query($sql);
        }catch (Exception $e) {
            return false;
        }

    }
    public function searachAdminAnchorViewerInfo($data)
    {
        if(is_array($data['uid']))
        {
            $in    = implode(',',$data['uid']);
        }else
        {
            $in = $data['uid'];
        }
        $fields = self::$table2_fields;
        $db = $this->getDb();
        $fields = $db->buildFieldsParam($fields);
        $sql = "SELECT `id`,`uid`,`num`,`status` FROM `{$this->getTable2()}` WHERE uid in ({$in}) ORDER BY uid DESC  LIMIT :page,:pageSize";
        $this->_page = ($this->_page-1)*$this->_pageSize;
        $bdParam = [
            'page'=> $this->_page,
            'pageSize'=> $this->_pageSize,
        ];
        try
        {
            return  $db->query($sql,$bdParam);
        }catch (Exception $e) {
            return false;
        }

    }
   public function insertAdminAnchorViewerInfo($data)
   {
       $fields = self::$table2_fields;
       $db = $this->getDb();
       $fields = $db->buildFieldsParam($fields);
       $sql = "INSERT INTO `{$this->getTable2()}` (`uid`,`num`,`time`,`status`) VALUE (:uid,:num,:time,:status)";
       //参数绑定
       $bdParam = [
           'uid'=> $data['uid'],
            'num'=>$data['num'],
           'time'=>$data['time'],
           'status'=>$data['status'],
       ];

       try
       {
           return  $db->execute($sql,$bdParam);
       }catch (Exception $e) {
           return false;
       }
   }
   public function updateAdminAnchorViewerInfo($data)
   {
       $fields = self::$table2_fields;
       $db = $this->getDb();
       $fields = $db->buildFieldsParam($fields);
       $sql = "UPDATE `{$this->getTable2()}` SET `uid` = :uid,`num` = :num,`time` = :time,`status`=:status,`utime`=:utime WHERE `id`=:id";
       $time = date("Y-m-d H:i:s");
       //参数绑定
       $bdParam = [
           'id'=> $data['id'],
           'uid'=> $data['uid'],
           'num'=>$data['num'],
           'time'=>$data['time'],
           'status'=>$data['status'],
           'utime'=>$time,
       ];

       try
       {
           return  $db->execute($sql,$bdParam);
       }catch (Exception $e) {
           return false;
       }
   }
   //批量修改主播设置状态
   public function updateAdminAnchorViewerStatus($data)
   {
       $fields = self::$table2_fields;
       $db = $this->getDb();
       if(is_array($data['id']))
       {
           $in    = implode(',',$data['id']);
       }else
       {
           $in = $data['id'];
       }
       $sql = "UPDATE `{$this->getTable2()}` SET `status`=:status,`utime`=:utime WHERE `id` in ({$in})";
       $time = date("Y-m-d H:i:s");
       //参数绑定
       $bdParam = [
           'status'=>$data['status'],
           'utime'=>$time,
       ];

       try
       {
           return  $db->execute($sql,$bdParam);
       }catch (Exception $e) {
           return false;
       }
   }
    public function deleteRoomRobotViewer($data)
    {
        $db = $this->getDb();
        if(is_array($data['id']))
        {
            $in    = implode(',',$data['id']);
        }else
        {
            $in = $data['id'];
        }
        $sql = "DELETE FROM `{$this->getTable2()}` WHERE `id` in ({$in})";
        try
        {
            return  $db->execute($sql);
        }catch (Exception $e) {
            return false;
        }

    }
    /**
     * 获取机器人聊天信息
     * @return array|bool|false|\PDOStatement|\system\obj
     */
    public function getAllRobotChatMsg()
    {
        $fields = self::$table3_fields;
        $db = $this->getDb();
        $fields = $db->buildFieldsParam($fields);
        $sql = "SELECT {$fields} FROM `{$this->getTable3()}`";
        try
        {
            return  $db->query($sql);
        }catch (Exception $e) {
            return false;
        }
    }
    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }
    public function getTable()
    {
        return 'userstatic';
    }
    public function getTable2()
    {
        return 'admin_robot_viewer';
    }
    public function getTable3()
    {
        return 'robot_chat_msg';
    }
}
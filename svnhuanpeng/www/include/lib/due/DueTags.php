<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/6
 * Time: 10:07
 */

namespace lib\due;
use system\DbHelper;

/**
 * 约玩标签表类
 * Class DueTags
 * @package lib\due
 */
class DueTags
{
//db 配置文件的key
    public static $dbConfName = 'huanpeng';
    private $_db = null;
    public  $param = [];
    
    private $commentTb = 'due_comment';
    private $userTagsTb = 'due_user_tags';
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
        return 'due_tags';
    }
    /**
     * 查询所有标签
     * @return bool|\PDOStatement
     */
    public function getAllTags()
    {
        $table = $this->tableName();
        //查询主播约玩资质
        $sql   = "SELECT `id`,`star`, `tag`,`ctime`  FROM `{$table}`  ORDER BY id";

        try {
            return  $this->_db->query($sql);

        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * 查询标签按ids
     * @param $ids
     * @return bool|\PDOStatement
     */
    public function getTags($ids)
    {
        $table = $this->tableName();
        $in     = $this->_db->buildInPrepare($ids);
        $order_field = implode(",", array_unique($ids));
        $sql    = "SELECT `id`, `tag`  FROM `{$table}` WHERE `id` in ({$in})  order by field(`id`,{$order_field})";
        try {

            return  $this->_db->query($sql,$ids);

        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * 通过用户uid 拉去最近一条被评论的tag
     * ----------------------------
     * @author yalongSun
     * @return array
     */
    public function getLastSqlByUid($uid){
        $tableName = $this->commentTb;
        $sql = "select tag_ids from {$tableName} where `cert_uid` = {$uid} and tag_ids<>'' and `status` in (".implode(",", DueComment::$showStatus[DueComment::COMMENT_AUDIT_MODEL_SHOW_FIRST]).") order by id desc limit 0,1";
        return $this->_db->query($sql);
    }
    public function getUserTagsFromDb(int $uid){
        $tableName = $this->userTagsTb;
        $sql = "select tagid from {$tableName} where uid = {$uid} limit 1";
        return $this->_db->query($sql);
    }
    //分页获取 被评论的用户数
    public function getUidByPage(int $page,int $size):array{
        $tableName = $this->userTagsTb;
        $limit = ($page-1) * $size;
        $sql = "select distinct uid from {$tableName} limit {$limit},{$size}";
        return $this->_db->query($sql);
    }
    //获取用户 tagids
    public function _getUserTagsByUids(int $uid):array{
        $tableName = $this->userTagsTb; 
        $sql = "select uid,tagid from {$tableName} where `uid` = {$uid} order by nums desc";
        return $this->_db->query($sql);
    }
    //添加评论时 主播新标签入库
    public function _insertUserTags($cert_uid,$tags){
        $table = $this->userTagsTb;
        $sql = "insert into {$table}(`uid`,`tagid`,`nums`) values";
        foreach($tags as $v){
            $sql.="({$cert_uid},{$v},1),";
        }
        $sql = mb_substr($sql,0,mb_strlen($sql)-1);
        return $this->_db->execute($sql);
    }
    
    //添加评论时 更新已有标签频数
    public function _updateUserTags(int $cert_uid,array $tags){
        $table = $this->userTagsTb;
        $tagids = implode(",", $tags);
        $sql = "update {$table} set `nums` = `nums`+1 where uid = {$cert_uid} and tagid in ({$tagids})";
        return $this->_db->execute($sql);
    }

}
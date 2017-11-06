<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017/6/8
 * Time: 11:26
 * Desc: 陪玩页面 游戏分类相关 底层支持
 */
namespace lib\due;

use system\DbHelper;
use service\common\UploadImagesCommon;

class DueRecGame
{
 
    // 数据表前缀
    const DBPRX = 'huanpeng';

    public static $dblink;

    private $ord = 1;

    /**
     * --------------------------------
     * 新人参数
     */
    const NEW_SKILL_SIZE = 15; // 每页展示新技能条数

    const TABLE_SKILL = 'due_skill';

    private $newSkillPage;

    /**
     * --------------------------------
     * 评分平均分排序参数
     */
    const COM_SKILL_SIZE = 15; // 每页展示新技能条数

    private $comSkillPage;
    
    private $size;
    
    /**
     * --------------------------------
     * 约玩游戏参数
     */
    const TABLE_GAME = 'due_game';

    public function __construct($db = '')
    {
        if (empty(self::$dblink))
            self::$dblink = DbHelper::getInstance(self::DBPRX);
    }

    /**
     * 设置分类排序
     * ---------
     */
    public function setSkillOrd($ord)
    {
        $this->ord = $ord;
    }

    /**
     * 获取排序规则
     * ---------
     * 
     * @return String
     */
    public function getSkillOrd()
    {
        if ($this->ord == 1)
            return "ord asc";
    }

    /**
     * 获取陪玩页面 推荐的游戏分类
     * --------------
     * 
     * @return array
     */
    public function getGame($page=1,$size=6)
    { 
        $limit = ($page-1) * $size;
        $table = self::TABLE_GAME;
        $sql = "SELECT gameid as game_id FROM `{$table}` where status=1 order by " . $this->getSkillOrd()." limit {$limit},{$size}";
        $result = self::$dblink->query($sql);
        return $result;
    }
    public function setPageSize($size){
        $this->size=$size;
    }
    /**
     * 设置新技能请求页
     * ------------
     * 
     * @param unknown $page            
     */
    public function setNewSkillPage($page)
    {
        $this->newSkillPage = $page != '' ? $page : 1;
    }

    /**
     * 设置某游戏分类记录 请求页
     * ------------------
     * 
     * @param unknown $page            
     */
    public function setNewSkillPageCom($page)
    {
        $this->comSkillPage = $page != '' ? $page : 1;
    }

    /**
     * 获取主播技能列表 时间倒序
     * ------------------
     * 
     * @return array
     */
    public function getNewSkillList(int $game_id)
    {
        $limit = ($this->newSkillPage - 1) * $this->size;
        $table = self::TABLE_SKILL;
        $sql = "select id as skillId,uid from {$table} where `game_id` = {$game_id} and switch=1 order by ctime desc limit $limit," . $this->size;
        return self::$dblink->query($sql);
    }

    /**
     * GameId 获取 评分平均分排序的技能
     * -------------------------
     * 
     * @param $game_id 游戏分类id            
     * @return array
     */
    public function getGameByCom($game_id,$type=0)
    {
        if($type===0){  //多分类进入  每层  * 2
            $this->size = $this->size*2;//楼层 转 展示记录
        }
        $game_id = intval($game_id);
        $limit = ($this->comSkillPage - 1) * $this->size;
        $table = self::TABLE_SKILL;
        $sql = "select id as skillId,uid from {$table} where game_id={$game_id} and switch=1 order by avg_score desc limit $limit," . $this->size;
        return self::$dblink->query($sql);
    }
    /**
     * 通过gameId获取 技能列表总数
     * ---------------------
     */
    public function getGameCountByGameId($game_id)
    {
        $game_id = intval($game_id);
        $table = self::TABLE_SKILL;
        $sql = "select count(*) as total from {$table} where game_id={$game_id} and switch=1";
        return self::$dblink->query($sql);
    }
    /**
     * 通过gameID 获取游戏信息
     * -------------------
     */
    public function getGameInfoByGameId($game_id){
        $table = self::TABLE_GAME;
        if(is_array($game_id))
            $where = " gameid in (".implode(",", $game_id).") ";
        else $where = " gameid = ".intval($game_id);
        $sql = "select gameid,gametid,name,icon,iconx,status,number from {$table} where {$where}";
        $datas = self::$dblink->query($sql);
        $info = [];
        foreach($datas as $vo){
            $vo['icon'] = UploadImagesCommon::getImageDomainUrl().$vo['icon'];
            $vo['iconx'] = UploadImagesCommon::getImageDomainUrl().$vo['iconx'];
            $info[$vo['gameid']] = $vo;
        }
        return $info;
    }

    /**
     * 获取全部游戏列表
     * @return bool|\PDOStatement
     */
    public function getAllGameList()
    {
        $table = self::TABLE_GAME;
        $sql = "SELECT gameid,gametid,name,icon,poster,ord,ctime,status,number FROM {$table}" ;
        try {
            return  self::$dblink->query($sql);

        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * 通过游戏id获取技能列表
     * -----------------
     */
    public function getSkillByGameId($page, $game_id,$size){
        $game_id = intval($game_id);
        $limit = ($page - 1) * $size;
        $table = self::TABLE_SKILL;
        $sql = "select id as skillId,uid,cert_id,game_id,price,unit,avg_score from {$table} where game_id={$game_id} and switch=1 order by avg_score desc,comment_num desc,id asc limit $limit," . $size;
        return self::$dblink->query($sql);
    }
}

?>
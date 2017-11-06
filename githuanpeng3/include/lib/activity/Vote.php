<?php
namespace lib\activity;

use system\DbHelper;
 
class Vote
{
    public static $dbConfig = 'huanpeng';
    private static $Db;
    
    public function __construct() {
        if (is_null(self::$Db))
            self::$Db = DbHelper::getInstance(self::$dbConfig);
    }
    private function getVoteLogTable() {
        return 'vote_log';
    }
    private function getVoteActivityTable() {
        return 'vote_activity';
    }
    private function getVoteHeroTable() {
        return 'vote_nums';
    }
    private function getEnrollTable() {
        return 'enroll';
    }
    private function activityTable() {
        return 'admin_information';
    }
    public function _isVote(int $uid,int $activity_id){
        $table = $this->getVoteLogTable();
        $sql = "SELECT count(*) as voteNum FROM  {$table} WHERE `uid`=:uid and `activity_id`=:activity_id";
        $bindParam = [
            'uid' => $uid,
            'activity_id' => $activity_id,
        ];
        return self::$Db->query($sql, $bindParam);
    }
    //处理活动 看是否生成如果没有生成则生成
    public function checkActivity($activity,$hero,$game_id){
        try {
            $table = $this->getVoteActivityTable();
            $sql = "SELECT count(*) as activityNum FROM  {$table} WHERE `activity_id`=:activity_id";
            $bindParam = [
                'activity_id' => $activity['id'],
            ];
            $data = self::$Db->query($sql, $bindParam);
            if($data[0]['activityNum'] == 0){ 
                self::$Db->beginTransaction();
                //生成投票活动
                $sql = "insert into {$table} values(".$activity['id'].",".$game_id.",'".$activity['title']."','".$activity['content']."','".$activity['stime']."','".$activity['etime']."',1)";  
                $data = self::$Db->execute($sql);
                if(!$data){   
                    throw new \Exception(__CLASS__."第".__LINE__."行写入投票活动失败");
                }
                //初始化该活动下的投票英雄
                $table1 = $this->getVoteHeroTable();
                $sql1 = "insert into {$table1} values ";
                foreach ($hero as $vo){
                    $sql1 .= "(".$vo['hero_id'].",".$activity['id'].",'".$vo['hero']."','".$vo['img']."','".$vo['bgImg']."',0),";
                }
                $sql1 = mb_substr($sql1, 0,mb_strlen($sql1)-1);
                $data1 = self::$Db->execute($sql1);
                if(!$data1){
                    throw new \Exception(__CLASS__."第".__LINE__."行写入投票活动相应英雄失败");
                }
                self::$Db->commit();
            }
            return true;
        } catch (\Exception $e) {
            self::$Db->rollback();
            write_log($e->getMessage(),"voteActivity");
            return false;
        }
    } 
    //投票
    public function _vote(int $uid,int $activity_id,int $hero_id){
        try {
            self::$Db->beginTransaction();
            $table = $this->getVoteLogTable();
            $sql = "insert into {$table}(`uid`,`hero_id`,`activity_id`) values(".$uid.",".$hero_id.",".$activity_id.")";
            $data = self::$Db->execute($sql);
            if(!$data){
                throw new \Exception(__CLASS__."第".__LINE__."行写入投票日志记录失败");
            }
            $table = $this->getVoteHeroTable();
            $sql = "update {$table} set nums=nums+1 where activity_id=:activity_id and hero_id=:hero_id";
            $bindParam = [
                'activity_id' => $activity_id,
                'hero_id' => $hero_id,
            ];
            $data = self::$Db->execute($sql, $bindParam);
            if(!$data){
                throw new \Exception(__CLASS__."第".__LINE__."行 更新英雄投票数失败");
            }
            self::$Db->commit();
            return true;
        } catch (\Exception $e) {
            self::$Db->rollback();
            write_log($e->getMessage(),"voteActivity");
            return false;
        }
    }
    //校验是否已报名
    public function _isEnroll(int $uid,int $game_id,int $activity_id){
        $table = $this->getEnrollTable();
        $sql = "SELECT count(*) as enrollNum FROM  {$table} WHERE `uid`=:uid and `game_id`=:game_id and `activity_id`=:activity_id";
        $bindParam = [
            'uid'=>$uid,
            'game_id'=>$game_id,
            'activity_id' => $activity_id,
        ];
        $data = self::$Db->query($sql, $bindParam);
        return $data[0]['enrollNum']!=0 ? true : false;
    }
    //报名入库
    public function enroll(int $uid,array $data){
        try {
            $table = $this->getEnrollTable();
            $sql = "insert into {$table}(`uid`,`activity_id`,`game_id`,`game_nick`,`qq`,`level`,`img`) values(".$uid.",".$data['activity_id'].",".$data['game_id'].",'".$data['game_nick']."',".$data['qq'].",'".$data['level']."','".$data['img']."')";
            $data = self::$Db->execute($sql);
            if(!$data){
                throw new \Exception(__CLASS__." 第".__LINE__."行 报名入库出错，错误SQL语句：{$sql}");
            }
            return true;
        } catch (\Exception $e) {
            write_log($e->getMessage(),"voteActivity");
            return false;
        }
    }
    //获取英雄投票总数
    public function getVoteNums(int $activity_id){
        try {
            $table = $this->getVoteHeroTable();
            $sql = "select hero_id,nums from {$table} where activity_id = ".$activity_id;
            $data = self::$Db->query($sql);
            if(!$data){
                throw new \Exception(__CLASS__." 第".__LINE__."行 没有查询到该活动英雄投票数，错误SQL语句：{$sql}");
            }
            return $data;
        } catch (\Exception $e) {
            write_log($e->getMessage(),"voteActivity");
            return false;
        }
    }
    //获取 活动信息
    public function _getVoteActivity(int $activity_id){
        try{
            $table = $this->activityTable();
            //获取已发布的活动
            $sql = "select id,tid,title,status,url,ispublish,is_login,stime,etime,type,poster,thumbnail,content from {$table} where `id`=:activity_id";
            $bindParam = [
                'activity_id'=>$activity_id
            ];
            $data = self::$Db->query($sql,$bindParam);
            if(!empty($data)) return $data[0];
            throw new \Exception("未获取到投票活动，请检查数据库  活动id：{$activity_id}");
        } catch (\Exception $e){
            write_log($e->getMessage(),"voteActivity");
            return false;
        }
    }
    //获取 活动信息
    public function _voteActivity(int $activity_id){
        try{
            $table = $this->getVoteActivityTable();
            //获取已发布的活动
            $sql = "select activity_id,game_id,stime,etime,status from {$table} where `activity_id`=:activity_id";
            $bindParam = [
                'activity_id'=>$activity_id
            ]; 
            $data = self::$Db->query($sql,$bindParam); 
            if(!empty($data)) return $data[0];
            throw new \Exception("未获取到投票活动，请检查数据库vote_activity表  活动id：{$activity_id}");
        } catch (\Exception $e){
            write_log($e->getMessage(),"voteActivity");
            return false;
        }
    }
}


<?php
namespace lib\activity;

use system\DbHelper; 

class InviteActivityLib
{
    public static $dbConfig = 'huanpeng';
    private static $Db;
    
    public function __construct() {
        if (is_null(self::$Db))
            self::$Db = DbHelper::getInstance(self::$dbConfig);
    }
    private function getInviteActivityTable(){
        return 'invite_activity';
    }
    private function getInviteReceiveTable(){
        return 'invite_receive';
    }
    public function activityTable(){
        return 'admin_information';
    }
    public function _checkInviteLink(int $uid){
        try {
            $table = $this->getInviteActivityTable();
            $sql = "select uid,invite_code from {$table} where uid=:uid limit 1";
            $bindParam = [
                'uid'=>$uid
            ];
            return self::$Db->query($sql,$bindParam);
        } catch (Exception $e) {
            write_log(__CLASS__." 第".__LINE__."行 查询出现问题：参数 uid：{$uid} 错误：".$e->getMessage(),'inviteActivity');
        }
    }
    //生成活动 并初始化 领取
    public function _makeInviteCode(int $uid,$inviteCode,$nums,$channer_id=0,$package_id,$beans){
        try { 
            $table = $this->getInviteActivityTable();
            $sql = "insert into {$table}(`uid`,`invite_code`,`channer_id`,`nums`) values(:uid,:invite_code,:channer_id,:nums)";
            $bindParam = [
                'uid'=>$uid,
                'invite_code' =>$inviteCode,
                'channer_id'=>$channer_id,
                'nums'=>$nums
            ];
            $data = self::$Db->execute($sql,$bindParam);
            if(!empty($data)){ 
                return $data;
            }else{
                throw new Exception("生成活动记录出现问题 SQL：".$sql."  |   ".$sql1);
            }
        } catch (Exception $e) {
            write_log(__CLASS__." 第".__LINE__."行 查询出现问题：参数 ".serialize($bindParam)." 错误：".$e->getMessage(),'inviteActivity'); 
            return false;
        }
    }
    //点击邀请活动 预生成领取记录
    public function _preCreateInviteReceive(int $package_id,$beans,$inviteCode,int $times){
        //redis 锁 防止并发  如有并发 inre 锁，操作完成后  解锁  
        //获取生成的记录数
        $nums = $this->_isReceiveMore($inviteCode,1); 
        if($nums[0]['nums'] == $times){
            write_log(__CLASS__." 第 ".__LINE__." 行 该邀请码（".$inviteCode."）已 生成了".$times."条记录",'inviteActivity');
            return false;
        }
        $table = $this->getInviteReceiveTable();
        $sql = "insert into {$table}(`uid`,`phone`,`package_id`,`beans`,`invite_code`) values (0,0,".$package_id.",".$beans.",:inviteCode)"; 
        $bindParam = [
            'inviteCode'=>$inviteCode
        ]; 
        $data = self::$Db->execute($sql,$bindParam);
        if(!empty($data)){
            return true;
        }else{
            write_log(__CLASS__." 第 ".__LINE__." 行 预生成领取记录失败 SQL为：".$sql,'inviteActivity');
            return false;
        } 
    }
    //查码
    public function _checkInviteCode($code){
        try {
            $table = $this->getInviteActivityTable();
            $sql = "select count(*) as nums from {$table} where invite_code = :invite_code limit 1";
            $bindParam = [
                'invite_code'=>$code, 
            ];
            return self::$Db->query($sql,$bindParam);
        } catch (Exception $e) {
            write_log(__CLASS__." 第".__LINE__."行 邀请码code：{$code} 错误：".$e->getMessage(),'inviteActivity');
        }
    }
    /**
     * 查是否还可以领取
     * $type : 1 统计邀请码的记录数；0查看是否可以领取
     */
    public function _isReceiveMore($code,$type = 0){
        try {
            $table = $this->getInviteReceiveTable();
            $sql = "select count(*) as nums from {$table} where invite_code = :invite_code";
            if($type == 0){
                $sql .= ' and phone = 0';
            }
            $bindParam = [
                'invite_code'=>$code,
            ];
            return self::$Db->query($sql,$bindParam);
        } catch (Exception $e) {
            write_log(__CLASS__." 第".__LINE__."行 邀请码code：{$code} 错误：".$e->getMessage(),'inviteActivity');
        }
    } 
    //查是否已经领取
    public function _checkIsReceive($phone){
        try {
            $table = $this->getInviteReceiveTable();
            $sql = "select count(*) as nums from {$table} where  phone = :phone";
            $bindParam = [
                'phone' => $phone
            ];
            return self::$Db->query($sql,$bindParam);
        } catch (Exception $e) {
            write_log(__CLASS__." 第".__LINE__."行 领取手机号：{$phone} 错误：".$e->getMessage(),'inviteActivity');
        }
    } 
    //领取活动
    public function _getReward($phone,$code){
        try {
            $table = $this->getInviteReceiveTable();
            $sql = "update {$table} set phone=:phone where `invite_code` = :invite_code and phone = 0 limit 1";
            $bindParam = [
                'phone' => $phone,
                'invite_code' => $code,
            ];
            return self::$Db->execute($sql,$bindParam);
        } catch (Exception $e) {
            write_log(__CLASS__." 第".__LINE__."行 领取手机号：{$phone} 邀请码：{$code} 错误：".$e->getMessage(),'inviteActivity');
        }
    }
    //通过uid 获取礼物信息、以及邀请人信息
    public function _inviteRewardInfo(int $phone){
        //新用户领取奖励记录
        try {
            $table = $this->getInviteReceiveTable();
            $sql = "select uid,phone,package_id,beans,invite_code from {$table} where phone =:phone limit 1";
            $bindParam = [
                'phone' => $phone
            ];
            return self::$Db->query($sql,$bindParam);
        } catch (Exception $e) {
            write_log(__CLASS__." 第".__LINE__."行 错误：".$e->getMessage(),'inviteActivity');
        }
    }
    //邀请码获取邀请人
    public function inviteFrom($code){
        try {
            $table = $this->getInviteActivityTable();
            $sql = "select uid from {$table} where  invite_code = :code";
            $bindParam = [
                'code' => $code
            ];
            return self::$Db->query($sql,$bindParam);
        } catch (Exception $e) {
            write_log(__CLASS__." 第".__LINE__."行 错误：".$e->getMessage(),'inviteActivity');
        }
    }
    //回写uid
    public function _callbackInsertUid(int $phone,int $uid){
        try {
            $table = $this->getInviteReceiveTable();
            $sql = "update {$table} set uid = {$uid},utime = '".date("Y-m-d H:i:s")."' where  `phone` = {$phone}"; 
            write_log(__CLASS__." 第".__LINE__."行 下发奖励uid：".$uid,'inviteActivity');
            return self::$Db->execute($sql);
        } catch (Exception $e) {
            write_log(__CLASS__." 第".__LINE__."行 错误：".$e->getMessage(),'inviteActivity');
        }
    }
    //拉去邀请活动信息
    public function _inviteActivityInfo(int $activity_id){
        try{
            $table = $this->activityTable();
            //获取已发布的活动
            $sql = "select id,tid,title,status,url,ispublish,is_login,stime,etime,type,poster,thumbnail,content from {$table} where `id`=:activity_id";
            $bindParam = [
                'activity_id'=>$activity_id
            ];
            $data = self::$Db->query($sql,$bindParam);
            if(!empty($data)) return $data[0];
            throw new \Exception("未获取到邀请活动，请检查数据库  活动id：{$activity_id}");
        } catch (\Exception $e){
            write_log($e->getMessage(),"recharge");
            return false;
        }
    }
}


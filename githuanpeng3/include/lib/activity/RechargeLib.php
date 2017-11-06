<?php

/**
 * Created by NetBeans.
 * User: yalongSun <yalong_2017@6.cn> 
 * Desc: 充值活动底层 
 */

namespace lib\activity; 

use system\DbHelper;

class RechargeLib {
    public static $dbConfig = 'huanpeng';
    private static $Db;
    public function __construct() {
        if (is_null(self::$Db))
            self::$Db = DbHelper::getInstance(self::$dbConfig);
    }
    public function activityTable(){
        return 'admin_information';
    }
    //获取首充活动信息
    public function _onceDayActivity(int $activity_id){
        try{
            $table = $this->activityTable();
            //获取已发布的活动
            $sql = "select id,tid,title,status,url,ispublish,is_login,stime,etime,type,poster,thumbnail from {$table} where `id`=:activity_id";
            $bindParam = [
                'activity_id'=>$activity_id
            ];
            $data = self::$Db->query($sql,$bindParam);
            if(!empty($data)) return $data[0];
            throw new Exception("未获取到开启的充值活动  活动id：{$activity_id}");
        } catch (\Exception $e){
            write_log($e->getMessage(),"recharge");
            return false;
        }
    }
}

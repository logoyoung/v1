<?php

namespace lib\due;

use system\DbHelper;

class DueCoupon {

    public static $dbConfig = 'huanpeng';
    private static $Db;

    public function __construct() {
        if (is_null(self::$Db))
            self::$Db = DbHelper::getInstance(self::$dbConfig);
    }

    private function getUserCouponTable() {
        return 'due_user_coupon';
    }

    private function getCouponTable() {
        return 'due_coupon';
    }

    //获取我的优惠券所有优惠券
    public function _returnCouponList(int $uid, int $page, int $size) {
        $mycouponTable = $this->getUserCouponTable();
        $limit = ($page - 1) * $size;
        $fields = 'id,code,uid,price,status as isuse,type,orderid,share_uuid,coupon_id,activity_id,ctime,stime,etime';
        $sql = "SELECT {$fields} FROM  {$mycouponTable} WHERE `uid`=:uid ORDER BY isuse asc,etime desc limit {$limit},{$size}";
        $bindParam = [
            'uid' => $uid,
        ];
        return self::$Db->query($sql, $bindParam);
    }

    //获取我的优惠券记录数
    public function _returnCouponCount(int $uid, $status = 1) {
        $mycouponTable = $this->getUserCouponTable();
        $sql = "SELECT count(*) as myCouponCount FROM  {$mycouponTable} WHERE `uid`=:uid ";
        $bindParam = [
            'uid' => $uid,
//             'status' => $status,
        ];
        return self::$Db->query($sql, $bindParam);
    }

    //获取我的可用优惠券 | status=1 etime > time()
    public function _getUsableCouponList(int $uid, $status = 1) {
        $mycouponTable = $this->getUserCouponTable();
        $fields = 'id,code,uid,price,status,orderid,type,coupon_id,share_uuid,activity_id,ctime,stime,etime';
        $now = date("Y-m-d H:i:s");
        $sql = " SELECT {$fields} FROM  {$mycouponTable} WHERE `uid`=:uid and `status`=:status and `stime` <= '{$now}' and `etime` >= '{$now}' ORDER BY id DESC ";
        $bindParam = [
            'uid' => $uid,
            'status' => $status,
        ];
        return self::$Db->query($sql, $bindParam);
    }

    //获取优惠券信息（多张）
    public function _getCouponInfo($couponId) {
        if (is_array($couponId)) {
            $couponId = array_values($couponId);
            $in = self::$Db->buildInPrepare($couponId);
            $bindParam = $couponId;
            $where = "`cid` in ({$in})  ORDER BY cid DESC";
        } else {
            $couponid = $couponId;
            $where = "`cid` = :cid  limit 1";
            $bindParam = [
                'cid' => $couponid,
            ];
        }
        $couponTable = $this->getCouponTable();
        $fields = 'cid,price,`condition`,max_number,send_number,expire,status,stime,etime';
        $sql = "SELECT {$fields} FROM  {$couponTable} WHERE {$where}";

        return self::$Db->query($sql, $bindParam);
    }

    //获取用户当天已使用优惠券次数
    public function _todayUseCouponNum(int $uid, $status = 1) {
        $mycouponTable = $this->getUserCouponTable();
        $now = date("Y-m-d");
        $sql = "SELECT count(*) as todayUseNum FROM  {$mycouponTable} WHERE `uid`=:uid and `status`=:status and `utime` between '{$now} 00:00:00' and '{$now} 23:59:59'";
        $bindParam = [
            'uid' => $uid,
            'status' => 2,
        ];
        return self::$Db->query($sql, $bindParam);
    }

    //获取 我的优惠券是否可用参数
    public function _getCouponInfoByCouponId(int $uid, int $couponId, $status = 1) {
        $mycouponTable = $this->getUserCouponTable();
        $fields = 'code,uid,price,status,orderid,coupon_id,share_uuid,activity_id,ctime,stime,etime';
        $sql = "SELECT {$fields} FROM  {$mycouponTable} WHERE `uid`=:uid and `id` = :coupon_id  AND  `status`=:status limit 1 ";
        $bindParam = [
            'uid' => $uid,
            'status' => $status,
            'coupon_id' => $couponId
        ];
        return self::$Db->query($sql, $bindParam);
    }

    /**
     * 获取用户未使用、未过期的优惠券列表
     * @param int $uid
     */
    public function _getUnusedCouponList(int $uid, $status = 1) {
        $mycouponTable = $this->getUserCouponTable();
        $fields = 'code,uid,price,status,orderid,share_uuid,coupon_id,activity_id,ctime,stime,etime';
        $now = date("Y-m-d H:i:s");
        $sql = " SELECT {$fields} FROM  {$mycouponTable} WHERE `uid`=:uid and `status`=:status  and `stime` <= '{$now}' and `etime` >= '{$now}' ";
        $bindParam = [
            'uid' => $uid,
            'status' => $status,
        ];
        return self::$Db->query($sql, $bindParam);
    }

    /**
     * 获取活动中领取的优惠券
     * @param int $uid
     * @param int $type
     * @return type
     */
    public function getCouponsByType(int $uid, int $type, $status = 1) {
        $mycouponTable = $this->getUserCouponTable();
        $fields = 'code,uid,price,status,orderid,coupon_id,share_uuid,activity_id,type,ctime,stime,etime';
        $sql = " SELECT {$fields} FROM  {$mycouponTable} WHERE `uid`=:uid and `type` = :type AND status=:status ";
        $bindParam = [
            'uid' => $uid,
            'status' => $status,
            'type' => $type
        ];
        return self::$Db->query($sql, $bindParam);
    }

    /**
     * 获取活动中领取的优惠券
     * @param int $uid
     * @param int $type
     * @return type
     */
    public function getCouponsByUidOrPhone($uid, $phone, $status = 1, $limit = 1) {
        $mycouponTable = $this->getUserCouponTable();
        $fields = 'code,uid,price,phone,status,orderid,coupon_id,share_uuid,activity_id,type,ctime,stime,etime';
        $bindParam = [
            'limit' => $limit
        ];
        $where = [];
        if ($uid) {
            $where[] = "`uid`=:uid";
            $bindParam['uid'] = $uid;
        }
        if ($status) {
            $where[] = "`status`>=:status";
            $bindParam['status'] = $status;
        }
        if ($phone) {
            $where[] = "`phone`=:phone";
            $bindParam['phone'] = $phone;
        }
        $whereString = implode(" AND ", $where);
        $sql = " SELECT {$fields} FROM  {$mycouponTable} WHERE  {$whereString}  ORDER BY `utime` DESC   LIMIT :limit";

        return self::$Db->query($sql, $bindParam);
    }

    /**
     * 获取活动中领取的优惠券
     * @param int $uid
     * @param int $activityId
     * @return type
     */
    public function getCouponsByActivityId($uid, $phone, $activityId, $status = 0) {

        $mycouponTable = $this->getUserCouponTable();
        $fields = 'code,uid,price,status,orderid,coupon_id,share_uuid,activity_id,type,ctime,stime,etime';
        $sql = " SELECT {$fields} FROM  {$mycouponTable} WHERE  `activity_id` = :activity_id AND status>:status ";
        $bindParam = [
            'activity_id' => $activityId,
            'status' => $status
        ];



        if ($uid && $phone) {
            $bindParam['uid'] = $uid;
            $bindParam['phone'] = $phone;
            $sqlAdd = " AND  ( uid=:uid OR phone=:phone) ";
        }
        if ((!$uid && $phone) || ($uid && !$phone)) {
            if ($uid) {
                $bindParam['uid'] = $uid;
                $sqlAdd = "  AND uid=:uid ";
            }
            if ($phone) {
                $bindParam['phone'] = $phone;
                $sqlAdd = "  AND phone=:phone ";
            }
        }
        $sql = $sql . $sqlAdd;

        return self::$Db->query($sql, $bindParam);
    }

    /**
     * 获取活动中领取后的优惠券
     * @param type $shareUuid
     * @param type $uid
     * @return type
     */
    public function getCouponsByShareId($shareUuid, $uid = null) {
        $mycouponTable = $this->getUserCouponTable();
        $fields = 'code,uid,price,status,phone,orderid,coupon_id,share_uuid,activity_id,type,ctime,stime,etime';
        $bindParam = [
            'share_uuid' => $shareUuid
        ];
        $where = "`share_uuid` = :share_uuid  AND status >0 ";
        if (!empty($uid)) {
            $bindParam['uid'] = $uid;
            $where = " `uid`=:uid AND " . $where;
        }
        $sql = " SELECT {$fields} FROM  {$mycouponTable} WHERE {$where} ";

        return self::$Db->query($sql, $bindParam);
    }
    
      /**
       * 查看来源信息
       * @param type $uid
       * @return type
       */
    public function getRowPromocodeByUid($uid) {
        $mycouponTable = $this->getUserCouponTable();
        $fields = 'uid,promocode';
        $bindParam = [
            'uid' => $uid
        ];
        $where = "`uid` = :uid  AND promocode  != ''  LIMIT 1";
        $sql = " SELECT {$fields} FROM  {$mycouponTable} WHERE {$where} ";

        $res = self::$Db->query($sql, $bindParam);
        $row = isset($res[0]) ? $res[0] : [];
        
        return $row;
    }

    /**
     * 获取活动中领取后的优惠券
     * @param type $shareUuid
     * @param type $uid
     * @return type
     */
    public function getCouponsByShareIdAndStatus($shareUuid, $uid = null, $status = 1) {
        $mycouponTable = $this->getUserCouponTable();
        $fields = 'code,uid,price,status,orderid,coupon_id,share_uuid,activity_id,type,ctime,stime,etime';
        $bindParam = [
            'share_uuid' => $shareUuid,
            'status' => $status
        ];
        $where = "`share_uuid` = :share_uuid  AND status =:status ";
        if (!empty($uid)) {
            $bindParam['uid'] = $uid;
            $where = " `uid`=:uid AND " . $where;
        }
        $sql = " SELECT {$fields} FROM  {$mycouponTable} WHERE {$where}  ORDER BY `id`";

        return self::$Db->query($sql, $bindParam);
    }

    /**
     * 插入数据
     * @param type $data
     * @return boolean
     */
    public function insertCouponData($data) {
        try {
            $field = "`code`, `uid`, `phone`, `price`,`status`, `share_uuid`, `coupon_id`, `activity_id`, `type`, `ctime`, `stime`, `etime`, `utime`,`promocode`";
            $result = $this->formatInsertData($data, $field);
            $table = $this->getUserCouponTable();
            $sql = "INSERT INTO {$table}({$result['field']})VALUES({$result['value']})";
            $res = self::$Db->execute($sql, $result['bind']);
            return $res;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * 插入数据
     * @param type $data
     * @return boolean
     */
    public function updateCouponData($data) {
        try {
            $table = $this->getUserCouponTable();
            $sql = "UPDATE {$table}  SET `uid`=:uid, `phone`=:phone, `stime`=:stime, `etime`=:etime, `utime`=:utime, status =:status,promocode=:promocode WHERE `share_uuid`=:share_uuid AND `coupon_id`=:coupon_id  AND uid = 0 AND phone = '' LIMIT 1";
            $res = self::$Db->execute($sql, $data);
            return $res;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * 插入数据
     * @param type $data
     * @return boolean
     */
    public function updateCouponNumberById($id) {
        try {
            $table = $this->getCouponTable();
            $sql = "UPDATE {$table}  SET `send_number` = `send_number` +1 WHERE `cid` =:cid";
            $bindParam['cid'] = $id;
            $res = self::$Db->execute($sql, $bindParam);
            return $res;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * 
     * @param type $phone
     * @param type $uid
     * @return boolean
     */
    public function updateCouponUidByPhone($phone, $uid) {
        try {
            $table = $this->getUserCouponTable();
            $sql = "UPDATE {$table}  SET `uid` =:uid  WHERE  `phone`=:phone AND `uid`=0 ";
            $bindParam['phone'] = $phone;
            $bindParam['uid'] = $uid;
            $res = self::$Db->execute($sql, $bindParam);
            return $res;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * 格式化
     * @param type $data
     * @return type
     */
    public function formatInsertData($dataParam, $fieldParam) {
        $fields = explode(',', str_replace('`', '', $fieldParam));

        foreach ($fields as $key) {
            $key = trim($key);
            if (empty($dataParam[$key])) {
                continue;
            }
            $field[] = "`{$key}`";
            $value[] = ":{$key}";
            $bind[$key] = $dataParam[$key];
        }
        return ['field' => implode(',', $field), 'value' => implode(',', $value), 'bind' => $bind];
    }

}

?>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace lib\pack;

use Exception;
use system\DbHelper;

class UserPack {

    //
    ### type
    const PACK_TYPE_01_GIFT = 1;
    ### size
    const DEFAULT_PACK_SPACE_NUM = 100;

    /**
     * 物品的时间线 默认、未使用、已使用、过期
     */
    const GOODS_STATUS_00_DEFAULT = 0;
    const GOODS_STATUS_01_UNUSED = 1;
    const GOODS_STATUS_02_USED = 2;
    const GOODS_STATUS_03_OVERDUE = 3;


    ### userpack status
    const USERPACK_STATUS_DEFAULT = 0; //默认
    const USERPACK_STATUS_OPEN = 1; //正常开启
    const USERPACK_STATUS_CLOSE = 2; //关闭

    /**
     * 操作包类型
     * @var type 
     */

    public $packType = '';
    public $packTypeMap = [
        self::PACK_TYPE_01_GIFT => '我的背包',
    ];

    ### userpack
    public $baseTable = 'userpack';
    public $baseTableFiled = '`id`,`uid` ,`type` ,`size` ,`free` ,`status` ,`ctime`';
    public $baseTablePrimary = '`id`';

    ### packs  

    const TABLE_LIMIT_NUM = 500000;

    public $packTablePrefix = '';
    public $packTable = '';
    public $packTableFiled = "`id`, `otid`,`uid`, `type`, `sourceid`, `goodsid`, `status`, `ctime`, `stime`, `etime`, `utime`";
    public $packTablePrimary = '`id`';

    ### db
    public $_dbConfig = 'huanpeng';
    public $_db = NULL;

    public function __construct() {
        if (empty($this->packTable) || empty($this->packType) || empty($this->packTablePrefix)) {
            throw new Exception(-9001);
        }
    }

    public function getDb(): \system\MysqlConnection {
        if (is_null($this->_db)) {
            $this->_db = DbHelper::getInstance($this->_dbConfig);
        }
        return $this->_db;
    }

    protected function setPackTableName($uid) {
        if (empty($this->packTablePrefix)) {
            throw new Exception(-9002);
        }
        $table = sprintf($this->packTablePrefix . "_%04d", ceil($uid / self::TABLE_LIMIT_NUM));
        $this->packTable = $table;
    }

    ### [ userpack start] ###

    /**
     * 增加用户的背包
     * @param type $uid  用户的id
     * @param type $type 背包类型
     */
    public function addPack($uid, $type) {
        if (!in_array($type, array_keys($this->packTypeMap))) {
            write_log(__FILE__ . '#' . __LINE__ . ':背包类型错误');
            return FALSE;
        }
        $sql = "SELECT `id` FROM {$this->baseTable} WHERE `uid` =:uid AND `type` =:type ;";
        $bindParam = [
            'uid'  => $uid,
            'type' => $type,
        ];
        $tmpRes = $this->getDb()->query($sql, $bindParam);
        if (empty($tmpRes)) {
            $insertSql = "INSERT INTO {$this->baseTable}($this->baseTableFiled)VALUES(NULL,:uid,:type,:size,:free,:status,:ctime);";
            $bindParam = [
                'uid'    => $uid,
                'type'   => $type,
                'size'   => self::DEFAULT_PACK_SPACE_NUM,
                'free'   => self::DEFAULT_PACK_SPACE_NUM,
                'status' => self::USERPACK_STATUS_OPEN,
                'ctime'  => date("Y-m-d H:i:s")
            ];
            return $this->getDb()->execute($insertSql, $bindParam);
        }
        return FALSE;
    }

    /**
     * 获取背包
     * @param type $uid
     * @param type $type
     * @return type
     */
    public function getPack($uid, $type) {
        $sql = "SELECT {$this->baseTableFiled} FROM {$this->baseTable} WHERE `uid` =:uid AND `type` =:type ;";
        $bindParam = [
            'uid'  => $uid,
            'type' => $type,
        ];
        $res = $this->getDb()->query($sql, $bindParam);
        if (empty($res)) {
            try {

                $this->addPack($uid, $type);
                $this->getDb()->setMaster();
                $res = $this->getPack($uid, $type); //主从数据库数据同步需要时间,有可能没有数据返回
                $this->getDb()->setMaster(FALSE);
            } catch (Exception $exc) {
                $res = [];
                write_log(__FILE__ . '#' . __LINE__ . ":背包创建失败(uid:{$uid};type:{$type})");
            }
        }
        return $res[0] ?? [];
    }

    /**
     * 获取剩余空间
     * @param type $uid
     * @param type $type
     * @return type
     */
    public function getPackFree($uid, $type) {
        $sql = " SELECT `free` FROM {$this->baseTable} WHERE `uid` = :uid AND `type` = :type ";
        $bindParam = [
            'uid'  => $uid,
            'type' => $type,
        ];
        $res = $this->getDb()->query($sql, $bindParam);
        return $res[0]['free'] ?? 0;
    }

    public function minusFree($uid, $type, $incr = 1) {
        $sql = "UPDATE {$this->baseTable} SET `free` = `free` - :incr WHERE `uid`= :uid AND `type` = :type LIMIT 1";
        $bind = [
            'incr' => $incr,
            'uid'  => $uid,
            'type' => $type,
        ];
        return $this->getDb()->execute($sql, $bind);
    }

    public function addFree($uid, $type, $incr = 1) {
        $sql = "UPDATE {$this->baseTable} SET `free` = `free` + :incr WHERE `uid`= :uid AND `type` = :type LIMIT 1";
        $bind = [
            'incr' => $incr,
            'uid'  => $uid,
            'type' => $type,
        ];
        return $this->getDb()->execute($sql, $bind);
    }

    public function setFree($uid, $type, $free) {
        $sql = "UPDATE {$this->baseTable} SET `free` =  :free WHERE `uid`= :uid AND `type` = :type LIMIT 1";
        $bind = [
            'free' => $free,
            'uid'  => $uid,
            'type' => $type,
        ];
        return $this->getDb()->execute($sql, $bind);
    }

    public function addSize($uid, $type, $incr = 1) {
        $sql = "UPDATE {$this->baseTable} SET `size` = `size` - :incr WHERE `uid`= :uid AND `type` = :type LIMIT 1";
        $bind = [
            'incr' => $incr,
            'uid'  => $uid,
            'type' => $type,
        ];
        return $this->getDb()->execute($sql, $bind);
    }

    ### [ userpack end] ###
    //
    
    //
    ### [ packs start] ###
    # packs  的所有方法使用前必须指定查询表
    # $this->setPackTableName($uid);
    ###

    /**
     * 更新礼物状态(使用礼物)
     * @param array $id
     * @param type $status
     * @return boolean
     */
    public function updateStatusById($uid, array $id, $status) {
        $this->setPackTableName($uid);
        $in = $this->getDb()->buildInPrepare($id);
        $bind = $id;
        $count = count($id);
        if ($count < 1) {
            return FALSE;
        }
        $sql = "UPDATE {$this->packTable} SET `status` = ? ,`utime` = ? WHERE `id` IN($in) LIMIT {$count}";
        array_unshift($bind, $status, date('Y-m-d H:i:s'));
        return $this->getDb()->execute($sql, $bind);
    }

    /**
     * 增加物品
     * @param array $id
     * @param type $status
     * @return boolean
     */
    public function addGoods($data) {
        $this->getPack($data['uid'], $this->packType);
        $this->setPackTableName($data['uid']);
        $sql = "INSERT INTO {$this->packTable}({$this->packTableFiled})VALUE(NULL,:otid,:uid,:type, :sourceid, :goodsid, :status, :ctime, :stime, :etime,NULL)";
        $bindParam = [
            'otid'     => getOtid(),
            'uid'      => $data['uid'],
            'type'     => $data['type'],
            'sourceid' => $data['sourceid'],
            'goodsid'  => $data['goodsid'],
            'status'   => $data['status'],
            'ctime'    => $data['ctime'],
            'stime'    => $data['stime'],
            'etime'    => $data['etime'],
        ];
        foreach ($bindParam as $value) {
            if (empty($value)) {
                return FALSE;
            }
        }
        $list = $this->getGoodsBySomeIf($data['uid'], NULL, $data['goodsid']);
        $num = count($list);
        $res = $this->getDb()->execute($sql, $bindParam);
        if ($res && $num == 0) {
            $this->minusFree($data['uid'], $this->packType);
        }
        return $res;
    }

    /**
     * 数据过滤,剔除过期数据
     * @param type $list 数据
     * @param type $isUnset 是否剔除过期数据
     * @return type
     */
    protected function dataFilter($list, $isUnset = FALSE) {
        $time = date("Y-m-d H:i:s");
        $id = [];
        foreach ($list as $key => $value) {
            $uid = $value['uid'];
            if ($time > $value['etime']) {
                $id[] = $value['id'];
                $list[$key]['status'] = self::GOODS_STATUS_03_OVERDUE;
                if ($isUnset) {
                    unset($list[$key]);
                }
            }
        }

        if (!empty($id)) {
            $this->updateStatusById($uid, $id, self::GOODS_STATUS_03_OVERDUE);
        }
        ksort($list);
        return $list;
    }

    /**
     * 获取用户的礼物
     * @param type $uid
     * @param type $status
     * @return type
     */
    public function getGoodsThatCanBeUsed($uid, $page = 0, $size = 8) {
        $this->setPackTableName($uid);
        $limit = '';
        if ($page > 0) {
            $start = ($page - 1) * $size;
            $limit = " LIMIT {$start},{$size} ";
        }
        $sql = "SELECT {$this->packTableFiled} FROM {$this->packTable} WHERE `uid` =:uid AND `status`=:status ORDER BY `id` {$limit}";
        $bind = [
            'uid'    => $uid,
            'status' => self::GOODS_STATUS_01_UNUSED
        ];
        $list = $this->getDb()->query($sql, $bind);
        $result = $this->dataFilter($list, TRUE);
        return $result;
    }

    /**
     * 获取用户的礼物
     * @param type $otid
     * @param type $status
     * @return type
     */
    public function getGoodsByOtid($uid, $otid, $status = NULL) {
        $this->setPackTableName($uid);
        $sql = "SELECT {$this->packTableFiled} FROM {$this->packTable}  ";
        $bind = [
            'otid' => $otid,
        ];
        $where = " WHERE `uid` =:uid ";
        if (!is_null($status)) {
            $bind['status'] = $status;
            $where .= " AND `status`=:status ";
        }
        $where .= " ORDER BY `id` ;";
        $list = $this->getDb()->query($sql . $where, $bind);
        $result = $this->dataFilter($list, TRUE);
        return $result;
    }

    /**
     * 获取用户的礼物
     * @param type $id
     * @param type $status
     * @return type
     */
    public function getGoodsById($uid, $id, $status = NULL) {
        $this->setPackTableName($uid);
        $sql = "SELECT {$this->packTableFiled} FROM {$this->packTable}  ";
        $bind = [
            'id' => $id,
        ];
        $where = " WHERE `id` =:id ";
        if (!is_null($status)) {
            $bind['status'] = $status;
            $where .= " AND `status`=:status ";
        }
        $where .= " ORDER BY `id` ;";
        $list = $this->getDb()->query($sql . $where, $bind);
        $result = $this->dataFilter($list, TRUE);
        return $result;
    }

    /**
     * 获取用户的礼物
     * @param type $id
     * @param type $status
     * @return type
     */
    public function getGoodsBySomeIf($uid, $type = null, $goodsId = null, $status = NULL) {
        $this->setPackTableName($uid);
        $sql = "SELECT {$this->packTableFiled} FROM {$this->packTable}  ";
        $bind = [
            'uid' => $uid,
        ];
        $where = " WHERE `uid` =:uid ";
        if (!is_null($type)) {
            $bind['type'] = $type;
            $where .= " AND `type`=:type ";
        }
        if (!is_null($status)) {
            $bind['status'] = $status;
            $where .= " AND `status`=:status ";
        }
        if (!is_null($goodsId)) {
            $bind['goodsid'] = $goodsId;
            $where .= " AND `goodsid`=:goodsid ";
        }
        $where .= " ORDER BY `id` ;";
        $list = $this->getDb()->query($sql . $where, $bind);
        $result = $this->dataFilter($list, TRUE);
        return $result;
    }

    /**
     * 背包空间整理
     */
    public function packUp($uid) {
        $this->setPackTableName($uid);
        //同类东西存放同一空间
        $list = $this->getGoodsThatCanBeUsed($uid);
        //过期物品不占用空间
        $row = $this->_sortOut($list);

        $packInfo = $this->getPack($uid, $this->packType);

        $usedNum = count($row);
        $free = $packInfo['size'] - $usedNum;
        return $this->setFree($uid, $this->packType, $free);
    }

    /**
     * 归类
     * @param type $list
     */
    protected function _sortOut($list) {
        $row = [];
        foreach ($list as $value) {
            # 归类方式
//            $key = $value['goodsid'] . '_' . $value['etime'];
            $key = $value['goodsid'];
            $row[$key][] = $value;
        }
        return $row;
    }

    /**
     * 清理单个物品
     * @param type $uid
     * @param type $gid
     * @return boolean
     */
    public function packUpGoodsByGoodsId($uid, $goodsid) {
        $this->setPackTableName($uid);
        $sql = "SELECT {$this->packTableFiled} FROM {$this->packTable} WHERE `uid`=:uid AND `status`=:status AND `goodsid`=:goodsid ;";
        $res = FALSE;
        $bindParam = [
            'uid'     => $uid,
            'status'  => self::GOODS_STATUS_01_UNUSED,
            'goodsid' => $goodsid,
        ];
        $list = $this->getDb()->query($sql, $bindParam);
        if (is_array($list) && count($list) > 0) {
            $list = $this->dataFilter($list, TRUE);
            if (count($list) == 0) {
                $res = $this->addFree($uid, $this->packType);
            }
        } else {
            $res = $this->packUp($uid);
        }
        return $res;
    }

}

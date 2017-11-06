<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace lib\pack;

use Exception;
use system\DbHelper;
use lib\pack\UserPack;

class Backpack extends UserPack {

    //日充值送
    const TYPE_01_FIRST_DAY_RECHARGE = 1;
    //月充值送
    const TYPE_02_FIRST_MONTH_RECHARGE = 2;
    //邀请用户送
    const TYPE_03_INVITATION = 3;
    //物品ID
    /**
     * 星星
     */
    const GOODSID_STARS = 39;

    /**
     * 浪
     */
    const GOODSID_WAVE = 37;

    /**
     * 666
     */
    const GOODSID_SIX_SIX_SIX = 38;

    public $packType = self::PACK_TYPE_01_GIFT;

    const TABLE_LIMIT_NUM = 500000;

    public $packTable = '';
    public $packTablePrefix = 'userpack_gift';
    public $uid = 0;

//1）我的背包内容：
//2）使用（减去）背包礼物
//3）获得（增加）背包礼物


    public function __construct($uid = 0) {
        $this->setUid($uid);
    }

    public function setUid($uid) {
        if ($uid > 0) {
            $this->uid = $uid;
            $this->setPackTableName($uid);
        }
    }

    /**
     * 获取用户的礼物
     * @param type $uid
     * @param type $status
     * @return type
     */
    public function getGoodsListByStatus($uid, $status) {
        $this->setPackTableName($uid);
        $this->getGoodsThatCanBeUsed($uid);
        $bind = (array) $status;
        $in = $this->getDb()->buildInPrepare($status);
        $sql = "SELECT {$this->packTableFiled} FROM {$this->packTable} WHERE `uid` = ? AND `status` IN($in) ORDER BY `id`";
        array_unshift($bind, $uid);
        $list = $this->getDb()->query($sql, $bind);
//        $result = $this->dataFilter($list);
        return $list;
    }

    /**
     * 获取用户,按状态获取物品
     * @param type $uid
     * @param type $gid
     * @return type
     */
    public function getGoodsByGoodsIdAndStatus($uid, $gid, $status) {
        $this->setPackTableName($uid);
        $this->getGoodsThatCanBeUsed($uid);
        $sql = "SELECT {$this->packTableFiled} FROM {$this->packTable} WHERE `uid`=:uid AND `status` = :status AND `goodsid`=:goodsid ORDER BY `etime` ASC";
        $bind = array(
            'uid'     => $uid,
            'goodsid' => $gid,
            'status'  => $status,
        );
        $list = $this->getDb()->query($sql, $bind);
        if ($status == self::GOODS_STATUS_01_UNUSED) {
            $list = $this->dataFilter($list, TRUE);
        }
        return $list;
    }

    /**
     * 指定渠道获取物品 ,获得的记录
     * @param type $uid
     * @param type $type
     * @param type $stime
     * @param type $etime
     * @return type
     */
    public function getLogList($uid, $type, $stime, $etime) {
        $this->setPackTableName($uid);
        $sql = "SELECT {$this->packTableFiled} FROM {$this->packTable} WHERE `uid`=:uid AND `type` = :type AND `ctime` > :stime AND `ctime` < :etime ";
        $bind = array(
            'uid'   => $uid,
            'type'  => $type,
            'stime' => $stime,
            'etime' => $etime,
        );
        $list = $this->getDb()->query($sql, $bind);
        return $list;
    }

}

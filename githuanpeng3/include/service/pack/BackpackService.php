<?php

/**
 * 背包系统
 * @author Timon 
 * @since 2017-09-01 16:38:00
 */

namespace service\pack;

use service\common\AbstractService;
use lib\pack\Backpack;
use lib\Gift;
use system\RedisHelper;
use service\room\RoomGift;
use service\user\UserCenterCountService;
use Exception;

/**
 * 背包系统:背包属性.给我100格的大背包...
 */
class BackpackService extends AbstractService {

    const ERROR_STATUS_CODE_DEFAULT = 0;
    const ERROR_STATUS_CODE_PARAM_ERROR = 11001;
    const ERROR_STATUS_CODE_USER_NOT_HAVE_THIS_GOODS = 11002;
    const ERROR_STATUS_CODE_USE_GOODS_FAIL = 11003;
    const ERROR_STATUS_CODE_WRONG_SELECTION = 11004;
    const ERROR_STATUS_CODE_NOT_HAVE_ENOUGH_GOODS = 11005;
    const ERROR_STATUS_CODE_NOT_HAVE_GOODS = 11006;
    const ERROR_STATUS_CODE_SYSTEM_SERVICE_BUSY = 11007;
    const ERROR_STATUS_CODE_SELECT_GOODS_IS_EMPTY = 11008;
    const ERROR_STATUS_CODE_ADD_GOODS_FAIL = 11009;

    public static $error_status_message = [
        self::ERROR_STATUS_CODE_DEFAULT                  => '无问题',
        self::ERROR_STATUS_CODE_PARAM_ERROR              => '参数错误',
        self::ERROR_STATUS_CODE_USER_NOT_HAVE_THIS_GOODS => '您没有此张礼物',
        self::ERROR_STATUS_CODE_USE_GOODS_FAIL           => '物品使用失败',
        self::ERROR_STATUS_CODE_WRONG_SELECTION          => '选择物品所属归类不匹配',
        self::ERROR_STATUS_CODE_NOT_HAVE_ENOUGH_GOODS    => '您没有足够数量的礼物',
        self::ERROR_STATUS_CODE_NOT_HAVE_GOODS           => '您没有礼物',
        self::ERROR_STATUS_CODE_SYSTEM_SERVICE_BUSY      => '系统繁忙',
        self::ERROR_STATUS_CODE_SELECT_GOODS_IS_EMPTY    => '请正确选择礼物',
        self::ERROR_STATUS_CODE_ADD_GOODS_FAIL           => '添加礼品失败',
    ];

    const BACKPACK_CLASS_01_PROP = 1;

    /**
     * 默认空间
     */
    const DEFAULT_BACKPACK_SPACE = 10;

    public static $default_attribute = [
        self::BACKPACK_CLASS_01_PROP => [
            'space' => self::DEFAULT_BACKPACK_SPACE,
        ],
    ];
    public $errorCode = 0;
    public $backpackModel = null;

    public function getBackPackModel(): \lib\pack\Backpack {
        if (is_null($this->backpackModel)) {
            $this->backpackModel = new Backpack();
        }
        return $this->backpackModel;
    }

    public function getErrorCode() {
        return $this->errorCode;
    }

    public function getErrorMessage() {
        return self::$error_status_message[$this->errorCode];
    }

    /**
     * 获取背包信息
     * @param type $uid
     * @return type
     */
    public function getBackPackInfo($uid) {

        $res = $this->getBackPackModel()->getPack($uid, $this->getBackPackInfo()->packType);
        return $res;
    }

    /**
     * 添加物品
     * @param type $type      来源类型
     * @param type $sourceid  回源id
     * @param type $goodsid   物品ID
     * @param type $num       数量
     * @param type $days      有效期
     */
    public function addBackpackGift($uid, $type, $sourceid, $goodsid, $days = 7) {
        $ctime = time();
        $stime = date('Y-m-d 00:00:00', $ctime);
        $etime = date('Y-m-d 23:59:59', $ctime + 86400 * ($days - 1));
        $data = [
            'uid'      => $uid,
            'type'     => $type,
            'sourceid' => $sourceid,
            'goodsid'  => $goodsid,
            'status'   => Backpack::GOODS_STATUS_01_UNUSED,
            'ctime'    => date("Y-m-d H:i:s", $ctime),
            'stime'    => $stime,
            'etime'    => $etime,
        ];
        try {
            $lockString = $uid . '_' . $sourceid;
            $lockStatus = $this->addLock($lockString, 500);
            if (!$lockStatus) {
                throw new Exception(self::ERROR_STATUS_CODE_SYSTEM_SERVICE_BUSY);
            }

            $res = $this->getBackPackModel()->addGoods($data);
            if (!$res) {
                throw new Exception(self::ERROR_STATUS_CODE_ADD_GOODS_FAIL);
            }
            UserCenterCountService::addValue($uid, UserCenterCountService::HASH_TABLE_FIELD_BACKPACK_NUM);
            return TRUE;
        } catch (Exception $exc) {
            write_log(self::$error_status_message[$exc->getMessage()]);
            $this->errorCode = $exc->getMessage();
            return FALSE;
        }
    }

    /**
     * 使用礼物
     * @param type $uid  用户
     * @param type $ids  礼物识别ID
     * @param type $goodsid  礼物类型ID
     * @return boolean
     * @throws Exception
     */
    public function UseBackpackGiftByIds($uid, $ids, $goodsid = null) {


        try {
            if (empty($uid) || empty($ids)) {
                throw new Exception(self::ERROR_STATUS_CODE_PARAM_ERROR);
            }

            $lockString = md5($uid . '_' . json_encode($ids));
            $lockStatus = $this->addLock($lockString);
            if (!$lockStatus) {
                throw new Exception(self::ERROR_STATUS_CODE_SYSTEM_SERVICE_BUSY);
            }

            $list = $this->getGoodsList($uid);
            foreach ($list as $value) {
                $userList[$value['id']] = $value;
            }

            if (!is_array($ids)) {
                $ids = [intval($ids)];
            }
            $num = 0;
            $giftId = [];
            foreach ($ids as $id) {
                if (empty($id)) {
                    continue;
                }
                if (isset($userList[$id]) && $userList[$id]['uid'] == $uid) {
                    if (!is_null($goodsid) && $goodsid != $userList[$id]['goodsid']) {
                        throw new Exception(self::ERROR_STATUS_CODE_WRONG_SELECTION);
                    }
                    $giftId[] = $id;
                    $num++;
                } else {
                    throw new Exception(self::ERROR_STATUS_CODE_USER_NOT_HAVE_THIS_GOODS);
                }
            }
            if (empty($giftId)) {
                throw new Exception(self::ERROR_STATUS_CODE_SELECT_GOODS_IS_EMPTY);
            }
        } catch (Exception $exc) {
            write_log(self::$error_status_message[$exc->getMessage()]);
            $this->errorCode = $exc->getMessage();
            return FALSE;
        }

        try {
            $this->getBackPackModel()->getDb()->beginTransaction();
            $res = $this->getBackPackModel()->updateStatusById($uid, $giftId, Backpack::GOODS_STATUS_02_USED);
            if (!$res) {
                throw new Exception(self::ERROR_STATUS_CODE_USE_GOODS_FAIL);
            }
            if ($num > 0) {
                $this->getBackPackModel()->packUpGoodsByGoodsId($uid, $goodsid);
            }
            $this->getBackPackModel()->getDb()->commit();
            UserCenterCountService::addValue($uid, UserCenterCountService::HASH_TABLE_FIELD_BACKPACK_NUM, -$num);
        } catch (Exception $exc) {
            write_log(self::$error_status_message[$exc->getMessage()]);
            $this->errorCode = $exc->getMessage();
            $this->getBackPackModel()->getDb()->rollback();
            return FALSE;
        }
        $data = $this->surplusGoods($uid, $goodsid);
        $data['sendGiftId'] = $giftId;
        return $data;
    }

    /**
     * 使用礼物
     * @param type $uid      用户
     * @param type $goodsid  礼物类型ID
     * @param type $num      赠送数量
     * @return boolean
     * @throws Exception
     */
    public function UseBackpackGiftByGoodsId($uid, $goodsid, $num = 1) {


        try {
            $lockString = md5($uid . '_' . $goodsid);
            $lockStatus = $this->addLock($lockString);
            if (!$lockStatus) {
                throw new Exception(self::ERROR_STATUS_CODE_SYSTEM_SERVICE_BUSY);
            }
            $list = $this->getBackPackModel()->getGoodsByGoodsIdAndStatus($uid, $goodsid, Backpack::GOODS_STATUS_01_UNUSED);
            if (empty($list)) {
                throw new Exception(self::ERROR_STATUS_CODE_NOT_HAVE_GOODS);
            }
            if (count($list) < $num) {
                throw new Exception(self::ERROR_STATUS_CODE_NOT_HAVE_ENOUGH_GOODS);
            }
            $giftId = [];
            for ($i = 0; $i < $num; $i++) {
                if (isset($list[$i]['id'])) {
                    $giftId[] = $list[$i]['id'];
                } else {
                    throw new Exception(self::ERROR_STATUS_CODE_NOT_HAVE_ENOUGH_GOODS);
                }
            }
            if (count($giftId) < $num) {
                throw new Exception(self::ERROR_STATUS_CODE_NOT_HAVE_ENOUGH_GOODS);
            }
        } catch (Exception $exc) {
            write_log(self::$error_status_message[$exc->getMessage()]);
            $this->errorCode = $exc->getMessage();
            return FALSE;
        }

        try {
            $this->getBackPackModel()->getDb()->beginTransaction();
            $res = $this->getBackPackModel()->updateStatusById($uid, $giftId, Backpack::GOODS_STATUS_02_USED);

            if (!$res) {
                throw new Exception(self::ERROR_STATUS_CODE_USE_GOODS_FAIL);
            }
            if ($num > 0) {
                $this->getBackPackModel()->packUpGoodsByGoodsId($uid, $goodsid);
            }
            $this->getBackPackModel()->getDb()->commit();
            UserCenterCountService::addValue($uid, UserCenterCountService::HASH_TABLE_FIELD_BACKPACK_NUM, -$num);
        } catch (Exception $exc) {
            write_log(self::$error_status_message[$exc->getMessage()]);
            $this->errorCode = $exc->getMessage();
            $this->getBackPackModel()->getDb()->rollback();
            return FALSE;
        }
        $data = $this->surplusGoods($uid, $goodsid);
        $data['sendGiftId'] = $giftId;
        return $data;
    }

    /**
     * 余额数量查询
     * @param type $uid
     * @param type $goodsid
     * @return type
     */
    public function surplusGoods($uid, $goodsid) {
        $resultList = $this->getGoodsListByWhere($uid, null, $goodsid);
        if (empty($resultList)) {
            $result = [
                "id"        => [],
                "goodsNum"  => 0,
                "validTime" => [],
            ];
        } else {
            $sortList = $this->toolSortOut($resultList);

            $formatList = $this->toolFormatData($sortList);

            $result = [
                "id"        => $formatList[$goodsid]['id'],
                "goodsNum"  => $formatList[$goodsid]['goodsNum'],
                "validTime" => $formatList[$goodsid]['validTime'],
            ];
        }
        return $result;
    }

    /**
     * 获得奖品记录
     * 
     * @param type $uid
     * @param type $type
     * @param type $etime
     * @param type $stime
     * @param type $isMasterDb
     * @return type
     */
    public function getGetGoodsLog($uid, $type, $etime, $stime, $isMasterDb = false) {
        if ($isMasterDb) {
            $this->getBackPackModel()->getDb()->setMaster();
        }

        $list = $this->getBackPackModel()->getLogList($uid, $type, $stime, $etime);

        if ($isMasterDb) {
            $this->getBackPackModel()->getDb()->setMaster(FALSE); //关闭
        }

        return $list;
    }

    public function getGoodsList($uid, $page = 0, $size = 8) {
        $list = $this->getBackPackModel()->getGoodsThatCanBeUsed($uid, $page, $size);
        return $list;
    }

    public function getGoodsListByOtid($uid, $otid) {
        $list = $this->getBackPackModel()->getGoodsByOtid($uid, $otid);
        return $list;
    }

//    public function getGoodsListById($id) {
//        $list = $this->getBackPackModel()->getGoodsById($id);
//        return $list;
//    }

    /**
     * 多条件查询数据
     * @param type $uid
     * @param type $type
     * @param type $goodsId
     * @param type $status
     * @return type
     */
    public function getGoodsListByWhere($uid, $type = null, $goodsId = null, $status = Backpack::GOODS_STATUS_01_UNUSED) {
        $list = $this->getBackPackModel()->getGoodsBySomeIf($uid, $type, $goodsId, $status);
        return $list;
    }

    /**
     * 数据格式化归类
     * @param type $list
     */
    public function toolSortOut($list) {
        $row = [];
        foreach ($list as $value) {
            # 归类方式
//            $key = $value['goodsid'] . '_' . $value['etime'];
            $key = $value['goodsid'];
            $validTime = ceil((strtotime($value['etime']) - time()) / 86400);
            $row[$key][] = [
                'id'         => $value['id'],
                'goodsid'    => $value['goodsid'],
                'validTime'  => $validTime,
                'ctimeStamp' => strtotime($value['ctime']),
            ];
        }
        return $row;
    }

    /**
     * 数据格式化归类
     * @param type $list
     */
    public function toolFormatData($list) {
        $row = [];
        foreach ($list as $key => $value) {

            foreach ($value as $v) {
                if (isset($row[$key])) {
                    $row[$key]['id'][] = $v['id'];
                    $row[$key]['goodsNum'] ++;

                    $row[$key]['validTime'][] = $v['validTime'];
//                    $row[$key]['ctimeStamp'][] = $v['ctimeStamp'];

                    $row[$key]['validTimeMap']["{$v['id']}"] = $v['validTime'];
                } else {
                    $row[$key] = [
                        'id'           => [$v['id']],
                        'goodsid'      => $v['goodsid'],
                        'goodsNum'     => 1,
                        'validTime'    => [$v['validTime']],
//                        'ctimeStamp'   => [$v['ctimeStamp']],
                        'validTimeMap' => [$v['id'] => $v['validTime']],
                    ];
                }
            }
            $row[$key]['validTime'] = array_values(array_unique($row[$key]['validTime']));
//            $row[$key]['ctimeStamp'] = array_unique($row[$key]['ctimeStamp']);
        }
        return $row;
    }

    /**
     * 锁设置(最大锁定时间1秒;最小0秒,刚跳秒)
     * @param type $lockString  锁定key
     * @param type $lockTime    锁定时间(单位:毫秒级   :1s = 1000ms)
     * @param type $isWait 是否等待解锁
     * @return boolean
     */
    public function addLock($lockString, $lockTime = 100, $isWait = FALSE) {
        $redis = RedisHelper::getInstance("huanpeng");
        $key = md5('BackpackService_lock_' . $lockString);
        $now = time();
        $maxLockTime = 1;
        do {
            $num = $redis->incr($key);
            if ($num == 1) {
                $redis->pexpire($key, $lockTime);
                return TRUE;
            } else {
                if ($isWait) {
                    usleep(20000);
                } else {
                    return FALSE;
                }
            }
            $checkTime = time() - $now;
        } while ($checkTime < $maxLockTime);
        return FALSE;
    }

    public function getGoodsInfo(array $ids) {
        $m = new RoomGift();
        $result = [];
        foreach ($ids as $goodsId) {
            $goodsId = intval($goodsId);
            if (empty($goodsId)) {
                continue;
            }
            $res = [];
            $one = $m->getGiftInfo([$goodsId]);
            if (empty($one)) {
                continue;
            }
            $res[$goodsId] = $one[$goodsId];
            $res[$goodsId]['mark'] = md5(json_encode($one));
            $res[$goodsId]['num'] = 1;
            $res[$goodsId]['cost'] = $res[$goodsId]['money'] * $res[$goodsId]['num'];
            if ($res[$goodsId]['type'] == Gift::SEND_TYPE_COIN) {
                $res[$goodsId]['unit'] = '欢朋币';
            } else {
                $res[$goodsId]['unit'] = '个';
            }
            $result[$goodsId] = $res[$goodsId];
        }
        return $result;
    }

}

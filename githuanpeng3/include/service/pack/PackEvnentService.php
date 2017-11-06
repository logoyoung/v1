<?php

namespace service\pack;

use service\common\AbstractService;
use service\pack\BackpackService;
use service\pack\BackpackLogService;
use lib\User;
use lib\pack\Backpack;
use lib\pack\BackpackLog;
use lib\Finance;
use lib\FinanceBase;
use system\RedisHelper;
use Exception;
use service\activity\ShareActivityConfig;
use service\activity\InviteActivityService;

/**
 * 领取规则判断
 */
class PackEvnentService extends AbstractService {

    const EVENT_EXCHANGE_DAY_PRICE = 1;
    const EVENT_EXCHANGE_MONTH_PRICE = 10;

    public static $error_status_message = [
        '0'      => '无错误',
        '-10001' => '活动时间错误',
        '-10002' => '参数错误',
        '-10003' => '系统繁忙',
        '-10004' => '送豆失败',
        '-10005' => '送礼失败',
        '-10006' => '更新失敗',
        '-10007' => '不满足活动条件',
        '-10008' => '已经送过礼物了',
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
        $message = self::$error_status_message[$this->errorCode];
        $this->errorCode = 0;
        return $message;
    }

    public function checkActivityTime($activityId) {

        $model = new InviteActivityService();
        $info = $model->inviteActivityInfo($activityId);

        if (!empty($info)) {
            $init_stime = $info['stime'];
            $init_etime = $info['etime'];
        } else {
            return FALSE;
        }
        $time = time();

        $currTimeString = date("Y-m-d H:i:s", $time);
        if ($currTimeString < $init_stime || $currTimeString > $init_etime) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * 检测订单是否是首冲订单
     * @param type $sourceid
     * @return type
     */
    public function isDayOrMonthExchange($sourceid) {
        $isDayFirst = $isMonthFirst = FALSE;
        do {
            if (empty($sourceid)) {
                break;
            }
            if (!$this->publicLock('isDayOrMonthExchange_' . $sourceid)) {
                break;
            }
            
            $packlog = new BackpackLogService();
            $list = $packlog->getRowBySourceid($sourceid);

            foreach ($list as $value) {
                if ($value['type'] == Backpack::TYPE_01_FIRST_DAY_RECHARGE) {
                    $isDayFirst = TRUE;
                }
                if ($value['type'] == Backpack::TYPE_02_FIRST_MONTH_RECHARGE) {
                    $isMonthFirst = TRUE;
                }
            }
        } while (FALSE);

        return [
            'isDayFirst'   => $isDayFirst,
            'isMonthFirst' => $isMonthFirst
        ];
    }

    public function dayExchange($uid, $price, $sourceid) {


        try {
            $content = sprintf("uid:%s price:%s  sourceid:%s", $uid, $price, $sourceid);
            write_log($content, 'packavtivity');
            if (!$this->checkActivityTime(ShareActivityConfig::PAY_ACTIVITY_ID)) {
                throw new Exception(-10001);
            }
            if (empty($uid) || empty($price) || empty($sourceid)) {
                throw new Exception(-10002);
            }
            if (!$this->publicLock('dayExchange_' . $sourceid)) {
                throw new Exception(-10003);
            }

            $time = time();
            $backPack = new BackpackService();
            $packlog = new BackpackLogService();
            $finance = new Finance();

            $list = $packlog->getRowBySourceid($sourceid);
            $isHaveDay = FALSE;
            foreach ($list as $value) {
                if ($value['type'] == Backpack::TYPE_01_FIRST_DAY_RECHARGE) {
                    $isHaveDay = TRUE;
                }
            }

            if ($price >= self::EVENT_EXCHANGE_DAY_PRICE && !$isHaveDay) {
                $stime = date("Y-m-d 00:00:00", $time);
                $etime = date("Y-m-d 23:59:59", $time);
                $list = $backPack->getGetGoodsLog($uid, Backpack::TYPE_01_FIRST_DAY_RECHARGE, $etime, $stime, TRUE);
                if (empty($list)) {
                    ## log
                    $desc = "用户:{$uid} 获得 超值礼包-每日首充" . self::EVENT_EXCHANGE_DAY_PRICE . "元礼[1个星星+100欢朋豆]";
                    $otid = $packlog->addpacklog($uid, $desc, BackpackLog::ACTIVITY_TYPE_DAY_EXCHANGE, $sourceid, BackpackLog::GOODS_STARTS, 1);
                    ## send
                    $Fresult = $finance->addUserBean($uid, 100, FinanceBase::GET_BEAN_CHANNEL_RECHARGE_ACTIVITY_DAY, $desc, $otid);
                    $sendHp = $finance->checkBizResult($Fresult);
                    if ($sendHp) {
                        $this->updateUserBean($uid);
                        $addGift = $backPack->addBackpackGift($uid, Backpack::TYPE_01_FIRST_DAY_RECHARGE, $otid, Backpack::GOODSID_STARS);
                        if (!$addGift) {
                            throw new Exception(-10005);
                        }
                        $up = $packlog->updateStatus($otid, BackpackLogService::PACK_LOG_STATUS_SUCCESS);
                        if (!$up) {
                            throw new Exception(-10006);
                        }
                    } else {
                        throw new Exception(-10004);
                    }
                } else {
                    throw new Exception(-10008);
                }
            } else {
                throw new Exception(-10007);
            }
        } catch (Exception $exc) {
            $this->errorCode = $exc->getMessage();
            return FALSE;
        }

        return TRUE;
    }

    public function monthExchange($uid, $price, $sourceid) {

        try {
            $content = sprintf("uid:%s price:%s  sourceid:%s", $uid, $price, $sourceid);
            write_log($content, 'packavtivity');
            if (!$this->checkActivityTime(ShareActivityConfig::PAY_ACTIVITY_ID)) {
                throw new Exception(-10001);
            }
            if (empty($uid) || empty($price) || empty($sourceid)) {
                throw new Exception(-10002);
            }
            if (!$this->publicLock('monthExchange_' . $sourceid)) {
                throw new Exception(-10003);
            }
            $time = time();
            $backPack = new BackpackService();
            $packlog = new BackpackLogService();
            $finance = new Finance();


            $list = $packlog->getRowBySourceid($sourceid);
            $isHaveMonth = FALSE;
            foreach ($list as $value) {
                if ($value['type'] == Backpack::TYPE_02_FIRST_MONTH_RECHARGE) {
                    $isHaveMonth = TRUE;
                }
            }
            if ($price >= self::EVENT_EXCHANGE_MONTH_PRICE && !$isHaveMonth) {
                $stime = date("Y-m-01 00:00:00", $time);
                $n = date("n");
                $t = date("t", $n);
                $etime = date("Y-m-{$t} 23:59:59", $time);
                $list = $backPack->getGetGoodsLog($uid, Backpack::TYPE_02_FIRST_MONTH_RECHARGE, $etime, $stime, TRUE);
                if (empty($list)) {
                    $desc = "用户:{$uid} 获得 尊贵礼包-每月单笔充值" . self::EVENT_EXCHANGE_MONTH_PRICE . "元礼[1个浪+200欢朋豆]";
                    $otid = $packlog->addpacklog($uid, $desc, BackpackLog::ACTIVITY_TYPE_MONTH_EXCHANGE, $sourceid, BackpackLog::GOODS_WAVE, 1);
                    $Fresult = $finance->addUserBean($uid, 200, FinanceBase::GET_BEAN_CHANNEL_RECHARGE_ACTIVITY_MONTH, $desc, $otid);

                    $sendHp = $finance->checkBizResult($Fresult);
                    if ($sendHp) {
                        $this->updateUserBean($uid);
                        $addGift = $backPack->addBackpackGift($uid, Backpack::TYPE_02_FIRST_MONTH_RECHARGE, $otid, Backpack::GOODSID_WAVE);
                        if (!$addGift) {
                            throw new Exception(-10005);
                        }
                        $up = $packlog->updateStatus($otid, BackpackLogService::PACK_LOG_STATUS_SUCCESS);
                        if (!$up) {
                            throw new Exception(-10006);
                        }
                    } else {
                        throw new Exception(-10004);
                    }
                } else {
                    throw new Exception(-10008);
                }
            } else {
                throw new Exception(-10007);
            }
        } catch (Exception $exc) {
            $this->errorCode = $exc->getMessage();
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 邀请奖励
     * @param type $uid
     * @param type $sourceid
     */
    public function invitation($uid, $sourceid, $isNewUser = false) {

        try {
            $content = sprintf("uid:%s isNewUser:%s  sourceid:%s", $uid, $sourceid, $isNewUser);
            write_log($content, 'packavtivity');
            if (!$this->checkActivityTime(ShareActivityConfig::INVITE_ACTIVITY_ID)) {
                throw new Exception(-10001);
            }
            if (empty($uid) || empty($sourceid)) {
                throw new Exception(-10002);
            }
            $lockString = 'invitation_' . intval($isNewUser) . '_' . $sourceid;
            if (!$this->publicLock($lockString)) {
                throw new Exception(-10003);
            }
            $backPack = new BackpackService();
            $packlog = new BackpackLogService();
            $finance = new Finance();
            if ($isNewUser) {
                $desc = "被邀请用户:{$uid}";
            } else {
                $desc = "邀请主用户:{$uid}";
            }
            $desc .= " 获得 新人礼包[1个666礼物+100欢朋豆]";
            $otid = $packlog->addpacklog($uid, $desc, BackpackLog::ACTIVITY_TYPE_INVITE_ACTIVITY, $sourceid, BackpackLog::GOODS_SIX_SIX_SIX, 1);
            $Fresult = $financeRes = $finance->addUserBean($uid, 100, FinanceBase::GET_BEAN_CHANNEL_INVITE_ACTIVITY, $desc, $otid);

            $sendHp = $finance->checkBizResult($Fresult);
            if ($sendHp) {
                $this->updateUserBean($uid);
                $addGift = $backPack->addBackpackGift($uid, Backpack::TYPE_03_INVITATION, $otid, Backpack::GOODSID_SIX_SIX_SIX);
                if (!$addGift) {
                    throw new Exception(-10005);
                }
                $up = $packlog->updateStatus($otid, BackpackLogService::PACK_LOG_STATUS_SUCCESS);
                if (!$up) {
                    throw new Exception(-10006);
                }
            } else {
                throw new Exception(-10004);
            }
        } catch (Exception $exc) {
            $this->errorCode = $exc->getMessage();
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 补发
     * @param type $otid
     */
    public function fixEvent($otid) {
        return FALSE;
        $backPack = new BackpackService();
        $packlog = new BackpackLogService();
        $finance = new Finance();

        $list = $packlog->getRowByOtid($otid);
        if (isset($list[0]['status']) && $list[0]['status'] == 0) {
            $send = $list[0];
            switch ($send['type']) {
                case BackpackLog::ACTIVITY_TYPE_DAY_EXCHANGE:
                    $bean = 100;
                    $financeType = FinanceBase::GET_BEAN_CHANNEL_RECHARGE_ACTIVITY_DAY;
                    $goods = Backpack::GOODSID_STARS;
                    $goodsType = Backpack::TYPE_01_FIRST_DAY_RECHARGE;
                    break;
                case BackpackLog::ACTIVITY_TYPE_MONTH_EXCHANGE:
                    $bean = 200;
                    $financeType = FinanceBase::GET_BEAN_CHANNEL_RECHARGE_ACTIVITY_MONTH;
                    $goods = Backpack::GOODSID_WAVE;
                    $goodsType = Backpack::TYPE_02_FIRST_MONTH_RECHARGE;
                    break;
                case BackpackLog::ACTIVITY_TYPE_INVITE_ACTIVITY:
                    $bean = 100;
                    $financeType = FinanceBase::GET_BEAN_CHANNEL_INVITE_ACTIVITY;
                    $goods = Backpack::GOODSID_SIX_SIX_SIX;
                    $goodsType = Backpack::TYPE_03_INVITATION;
                    break;
                default :
                    $bean = 0;
                    $goods = 0;
            }
            $desc = "补发";
            if ($bean > 0 && $goods > 0) {
                $giftList = $backPack->getGoodsListByOtid($send['uid'], $otid);
                if (empty($giftList)) {
                    $addGift = $backPack->addBackpackGift($send['uid'], $goodsType, $otid, $goods);
                } else {
                    $addGift = TRUE;
                }
                $Fresult = $res = $finance->addUserBean($send['uid'], $bean, $financeType, $desc, $otid);
                $this->updateUserBean($send['uid']);
                $sendHp = $finance->checkBizResult($Fresult);
                if ($sendHp && $addGift) {
                    return $packlog->updateStatus($otid, BackpackLogService::PACK_LOG_STATUS_SUCCESS);
                }
            }
            return FALSE;
        }
    }

    public function publicLock($lockString, $lockTime = 100) {

        write_log($lockString, 'back_lock');

        $redis = RedisHelper::getInstance("huanpeng");
        $key = md5('PackEvnentService_' . $lockString);
        $num = $redis->incr($key);
        if ($num == 1) {
            $now = time(NULL); // current timestamp
            $redis->pexpire($key, $lockTime);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function updateUserBean($uid) {
        $user = new User($uid);
        $finance = new Finance();
        $res = $finance->getBalance($uid);
        $user->updateUserHpBean($res['hd']);
    }

}

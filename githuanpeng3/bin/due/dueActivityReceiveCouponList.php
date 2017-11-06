<?php

/**
 * 计划任务:领取优惠券
 * 
 * * * * * * 
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since
 * @version 1.0
 * @Description
 */
set_time_limit(0);

include __DIR__ . "/../../include/init.php";

use system\RedisHelper;
use system\DbHelper;
use service\due\DueActivityConfigService;
use service\due\DueActivityService;
use lib\due\DueCoupon;

//use Exception;

class dueActivityReceiveCouponList {

    public $redisConfig = 'huanpeng';
    public $_redis = null;
    public $activityConfigServiceModel = null;
    public $activityServiceModel = null;
    public $couponModel = null;
    public $logfileName = 'dueActivityList';

    /**
     * 获取redis资源
     * @return type
     */
    public function getRedis() {
        if (is_null($this->_redis)) {
            $this->_redis = RedisHelper::getInstance($this->redisConfig);
        }
        return $this->_redis;
    }

    public function getActivityConfigServiceModel() {
        if (is_null($this->activityConfigServiceModel)) {
            $this->activityConfigServiceModel = new DueActivityConfigService();
        }
        return $this->activityConfigServiceModel;
    }

    public function getActivityServiceModel() {
        if (is_null($this->activityServiceModel)) {
            $this->activityServiceModel = new DueActivityService();
        }
        return $this->activityServiceModel;
    }

    public function getCouponModel() {
        if (is_null($this->couponModel)) {
            $this->couponModel = new DueCoupon();
        }
        return $this->couponModel;
    }

    /**
     * 死循环挂起执行（上线使用）
     * -------------------
     */
    public function todo() {

        $rongIsAlive = $this->checAlive();
        if (!$rongIsAlive) {

            write_log("进程存活 运行中...", $this->logfileName);
            exit;
        }

        while (1) {
            try {
                $errorCode = 0;
                $redis = $this->getRedis();
                $json = $redis->rPop(DueActivityConfigService::ACTIVITY_CACHE_KEY);
                if (empty($json)) {
                    usleep(1000000);
                    DbHelper::getInstance('huanpeng')->close();
                    continue;
                }

                $param = json_decode($json, TRUE);

                ## 活动检查
                $this->getActivityConfigServiceModel()->clear();
                $activityData = $this->getActivityServiceModel()->getActivityDataById($param['activityId']);
                if (!empty($activityData)) {
                    $data = $activityData[0];
                    ## 活动规则分析
                    $config = json_decode($data['configure'], TRUE);
                    $this->getActivityConfigServiceModel()->activityAnalysis($config, $param['uid'], $param['isAnchor'], $data, $param['shareUuid']);

                    if ($this->getActivityConfigServiceModel()->selectedId == 0) {
                        throw new Exception(errorDesc(-8024), -8024);
                    }
                    ## 根据具体领取类型进行发放优惠券
                    $receiveType = $this->getActivityConfigServiceModel()->rule[DueActivityConfigService::CONFIG_RECEIVE_TYPE];

                    if (in_array($receiveType, [DueActivityConfigService::RECEIVE_TYPE_01_RECEIVE, DueActivityConfigService::RECEIVE_TYPE_02_SHARE])) {
                        $errorCode = $this->getActivityConfigServiceModel()->receiveActivityCoupon($param['uid'], $param['shareUuid'], $data, $param['phone']);
                    } else {
                        throw new Exception(errorDesc(-8015), -8015);
                    }
                } else {
                    throw new Exception(errorDesc(-8011), -8011);
                    usleep(1000000);
                }
            } catch (\Exception $exc) {
                $errorCode = $exc->getCode();
            }
            $redis->set($param['returnKey'], $errorCode, 10);
            write_log("listResult:" . $errorCode . ' :' . errorDesc($errorCode), $this->logfileName);
        }
    }

    /**
     * PHP调用shell环境 检查 融云脚本是否存活
     * ------------------------------
     * @return boolean
     */
    private function checAlive() {
        $cmd = 'ps axu|grep "dueActivityReceiveCouponList"|grep -v "grep"|wc -l';
        $ret = shell_exec("$cmd");
        $ret = intval(rtrim($ret, "rn"));

        if ($ret > 1) {
            return false;
        } else {
            return true;
        }
    }

}

$obj = new dueActivityReceiveCouponList();
$obj->todo();

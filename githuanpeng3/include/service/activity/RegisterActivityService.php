<?php

namespace service\activity;

use service\user\UserDataService;
use system\RedisHelper;
use service\activity\InviteActivityService;
use service\pack\PackEvnentService;
use lib\activity\RegisterActivityTaskLib;

/**
 * 注册后需要做的事情
 * 需求:
 * 将注册后需要做的事情提取出来,防止注册过慢
 * 
 * 分析:
 * 触发事件:注册
 * 
 * 功能:检测现有的任务,做相应的处理
 */
class RegisterActivityService {

    const VERSION = '2017-09-13 16:17:36';
    const REGITER_LIST_CACHE_KEY = 'register_list_20170912';
    const AUTO_EXEC_METHOD_PREFIX = 'todo';

    ## action
    const REGISTER_ACTIVITY_TASK_LIB_TYPE_REGISTER = 1; // 注册

    public static $todoMap = [
        self::REGISTER_ACTIVITY_TASK_LIB_TYPE_REGISTER => [
            'todoInviteActivity',
        ],
    ];


    ##
    //uid_type
    public static $listValueFormat = "%d_%d";
    public static $fileMd5 = null;
    public $redis = null;
    public $userData = null;
    public $currentUid = null;
    public $funName = null;

    public function getRedis() {
        if (is_null($this->redis)) {
            $this->redis = RedisHelper::getInstance("huanpeng");
        }
        return $this->redis;
    }

    public function getUserData(): UserDataService {
        if (is_null($this->userData)) {
            $this->userData = new UserDataService();
        }
        return $this->userData;
    }

    public function setValue($uid, $todoType) {
        return sprintf(self::$listValueFormat, $uid, $todoType);
    }

    public function addUser($uid, $todoType = self::REGISTER_ACTIVITY_TASK_LIB_TYPE_REGISTER) {
        $model = new RegisterActivityTaskLib();
        $res = $model->insertRowData(['uid' => $uid, 'todotype' => $todoType]);

        $redis = $this->getRedis();
        if ($redis) {
            $redisRes = $redis->rPush(self::REGITER_LIST_CACHE_KEY, $this->setValue($uid, $todoType));
            $redisRes && $model->updateDataByUidAndType($uid, $todoType, RegisterActivityTaskLib::REGISTER_ACTIVITY_TASK_LIB_STATUS_IN_QUEUE);
        }
        return $res;
    }

    public function addUserOnlyRedis($uid, $todoType = self::REGISTER_ACTIVITY_TASK_LIB_TYPE_REGISTER) {
        $model = new RegisterActivityTaskLib();
        $redis = $this->getRedis();
        $redisRes = $redis->rPush(self::REGITER_LIST_CACHE_KEY, $this->setValue($uid, $todoType));
        $redisRes && $model->updateDataByUidAndType($uid, $todoType, RegisterActivityTaskLib::REGISTER_ACTIVITY_TASK_LIB_STATUS_IN_QUEUE);

        return $redisRes;
    }

    public function getUser() {
        return $this->getRedis()->lPop(self::REGITER_LIST_CACHE_KEY);
    }

    /**
     * 执行
     */
    public function execRegisterTodo() {
        $this->currentUid = 0;
        $methods = get_class_methods($this);
        $popStr = $this->getUser();
        if (empty($popStr)) {
            return FALSE;
        }
        list($uid, $todotype) = explode('_', $popStr);

        $model = new RegisterActivityTaskLib();

        if ($uid > 0) {
            $this->currentUid = $uid;
            $result = TRUE;
            foreach ($methods as $value) {
                if (strpos($value, self::AUTO_EXEC_METHOD_PREFIX) === 0) {
                    $this->funName = $value;
                    $res = $this->$value();
                    $status = $res ? 'SUCCESS' : 'ERROR';
                    $this->writelog($status);
                    $this->funName = '';
                    if (!$res) {
                        $result = false;
                        break;
                    }
                }
            }
            if ($result) {
                $model->updateDataByUidAndType($uid, $todotype, RegisterActivityTaskLib::REGISTER_ACTIVITY_TASK_LIB_STATUS_DONE);
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * @param type $uid
     */
    public function todoInviteActivity() {
        $inviteActivity = new InviteActivityService();
        $userData = $this->getUserData();
        $userData->setUid($this->currentUid);
        do {
            $userInfo = $userData->getUserInfo();
            if (!empty($userInfo) && isset($userInfo['phone'])) {
                $phone = $userInfo['phone'];
            }
            if (empty($phone)) {
                $this->writelog("手机号不存在");
                break;
            }
            $row = $inviteActivity->inviteRewardInfo($phone);
            if (!empty($row) && $row['reward']['uid'] == 0) {
                $packEvent = new PackEvnentService();
                $res = $packEvent->invitation($row['fromUid'], $phone, FALSE);
                if ($res) {
                    $this->writelog($row['fromUid'] . "     送礼成功");
                } else {
                    $mess = $packEvent->getErrorMessage();
                    $this->writelog($row['fromUid'] . "     送礼失败:" . $mess);
                }
                $res = $packEvent->invitation($this->currentUid, $phone, TRUE);
                if ($res) {
                    $this->writelog($this->currentUid . "     送礼成功");
                } else {
                    $mess = $packEvent->getErrorMessage();
                    $this->writelog($this->currentUid . "     送礼失败:" . $mess);
                }
                $inviteActivity->callbackInsertUid($phone, $this->currentUid);
            } else {
                $this->writelog("已经送过了");
                return TRUE;
            }
            return TRUE;
        } while (0);
        return FALSE;
    }

    public function writelog($content) {
        $fileName = "registeredTodoList";
        $log = sprintf("% 8s %s %s", $this->currentUid, $this->funName, $content);
        write_log($log, $fileName);
    }

    /**
     * 查看代码是否有变动
     * @staticvar string $md5
     * @return boolean
     */
    public static function checkCodeStatus() {
        //2017-09-18 15:09:58

        if (!self::$fileMd5) {
            self::$fileMd5 = md5_file(__FILE__);

            return true;
        }

        return (md5_file(__FILE__) == self::$fileMd5 );
    }

}

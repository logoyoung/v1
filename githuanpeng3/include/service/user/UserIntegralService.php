<?php

namespace service\user;

use service\common\AbstractService;
use lib\user\UserActive;

/**
 * 用户经验等级服务
 * @author longgang <longgang@6.cn>
 * @date 2017-09-11 15:10:53
 * @version 1.0.0
 */
class UserIntegralService extends AbstractService
{

    //更新用户经验信息异常
    const ERROR_UPDATE_USER_INTEGRAL = -37001;

    public static $errorMsg = [
        self::ERROR_UPDATE_USER_INTEGRAL => '更新用户经验信息异常',
    ];
    private $_uid;

    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $this->_uid = $uid;
        return $this;
    }

    public function updateUserIntegral(int $integral, int $level)
    {
        $userActiveDao = $this->getUserActiveDao();
        $res = $userActiveDao->updateUserIntegral($this->_uid, $integral, $level);
        if (!$res)
        {
            $code = self::ERROR_UPDATE_USER_INTEGRAL;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->_uid}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';caller:' . $this->getCaller();
            write_log($log);
            return false;
        }
        return true;
    }

    public function getUserActiveDao()
    {
        return new UserActive();
    }

}

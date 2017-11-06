<?php

ini_set('memory_limit', '512M');
require_once __DIR__ . '/../../include/init.php';

use lib\statistics\ViewLength;
use system\Timer;
use service\statistics\helper\UserViewLengthRedis;

/**
 *  用户观看时长入库
 * @date 2017-09-08 11:14:38
 * @author longgang <longgang@6.cn>
 * @copyright (c) 2017, longgang
 * @version 1.0.0
 */
class userViewLength
{

    const PER_NUM = 1;
    const QUENE_NUM = 3;

    private $_accessLog = 'user_view_length_into_mysql_acess';
    private $_errorLog = 'user_view_length_into_mysql_error';

    public function run()
    {
        $this->accessLog('start|用户观看时长入库操作开始');
        $timer = new Timer();
        $timer->start();
        $viewLengthDao = new ViewLength();
        $userViewLengthRedis = new UserViewLengthRedis();

        $date = date('Y-m-d');
        $date2 = date('Ymd');
        for ($mod = 0; $mod < self::QUENE_NUM; $mod ++)
        {
            $total = $userViewLengthRedis->getUserLiveViewLengthUidsCount($date2, $mod);
            if (!$total)
            {
                $this->accessLog("notice|从redis中mod{$mod}获取uids数量异常");
                continue;
            }

            $totalPage = ceil($total / self::PER_NUM);
            $page = 1;

            while ($totalPage > 0)
            {
                $totalPage --;
                $uids = $userViewLengthRedis->getUserLiveViewLengthUids($date2, $mod, $page, self::PER_NUM);
                if (!$uids)
                {
                    $this->errorLog("error|从redis获取观看用户uid异常,停止脚本");
                    return false;
                }

                foreach ($uids as $uid)
                {
                    $userViewLengthData = $userViewLengthRedis->getUserLiveViewData($date2, $uid);

                    if (!$userViewLengthData)
                    {
                        $this->accessLog("notice|从redis获取用户{$uid}观看时长信息异常");

                        $userViewLengthRedis->rmUserLiveViewLengthUid($date2, $mod, $uid);

                        continue;
                    }

                    $userViewLengthData = json_decode($userViewLengthData, true);

                    if ($viewLengthDao->checkUserLiveViewLengthByUidDate($uid, $date) === false)
                    {
                        $res = $viewLengthDao->createViewLength($uid, $userViewLengthData['viewTime'], $date);
                    } else
                    {
                        $res = $viewLengthDao->updateViewLength($uid, $date, $userViewLengthData['viewTime']);
                    }

                    if ($res === false)
                    {
                        $this->errorLog("error | 用户:{$uid} 写入数据库异常,脚本停止");
                        return false;
                    }

                    $userViewLengthRedis->rmUserLiveViewLengthUid($date2, $mod, $uid);
                }
            }
        }
        $timer->end();
        $t = $timer->getTime();
        $this->accessLog("end |success| 脚本执行完成; 耗时:{$t}s;");
        return true;
    }

    public function accessLog($msg)
    {
        return write_log($msg, $this->_accessLog);
    }

    public function errorLog($msg)
    {
        return write_log($msg, $this->_errorLog);
    }

}

$obj = new userViewLength();

$obj->run();

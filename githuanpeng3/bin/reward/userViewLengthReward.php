<?php

ini_set('memory_limit', '512M');
require_once __DIR__ . '/../../include/init.php';

use lib\statistics\ViewLength;
use lib\MsgPackage;
use lib\SocketSend;
use system\Timer;
use service\event\EventManager;
use service\statistics\UserLiveViewStatisticsService;
use service\user\UserDataService;
use service\user\UserIntegralService;
use service\chatRoom\ChatRoomService;

/**
 *  符合观看直播奖励条件的进行奖励
 * @date 2017-09-08 17:37:30
 * @author longgang <longgang@6.cn>
 * @copyright (c) 2017, longgang
 * @version 1.0.0
 */
class userViewLengthReward
{

    const PER_NUM = 50;
    const USER_REWARD = __DIR__ . '/../../include/config/reward/user.php';
    const REQUIRE_MINUTES_01 = 900;//60;
    const REQUIRE_MINUTES_02 = 36009;//90;
    const REQUIRE_MINUTES_03 = 10800;//100;
    const REWARD_LEVEL_01 = 1;
    const REWAED_LEVEL_02 = 5;
    const REWARD_LEVEL_03 = 10;

    private $_accessLog = 'user_view_length_reward_access';
    private $_errorLog = 'user_view_length_reward_error';

    public function run()
    {
        $this->accessLog('start|用户观看时长奖励操作开始');
        $timer = new Timer();
        $timer->start();
        $viewLengthDao = new ViewLength();

        $date = date('Y-m-d');

        $total = $viewLengthDao->getUserLiveViewCountByDate($date);

        if ($total == 0)
        {
            $this->errorLog("error|从数据库获取{$date}观看时长异常,停止脚本");
            return false;
        }

        $totalPage = ceil($total / self::PER_NUM);
        $page = 1;

        $userLiveViewStatisticsService = new UserLiveViewStatisticsService();
        $userLiveViewStatisticsService->setCaller('script:' . __FILE__);

        $userDataService = new UserDataService();
        $userDataService->setCaller('script:' . __FILE__);

        $userIntegralService = new UserIntegralService();
        $userIntegralService->setCaller('script:' . __FILE__);

        $userRewardConfig = require self::USER_REWARD;

        $chatRoomService = new ChatRoomService();

        $event = new EventManager();

        while ($totalPage > 0)
        {
            $userViewLengthData = $viewLengthDao->getUserLiveViewDataByDate($date, $page, self::PER_NUM);

            if (!$userViewLengthData)
            {
                $this->errorLog("error|从数据库获取观看时长数据异常,停止脚本");
                return false;
            }
            $page ++;
            $totalPage --;


            foreach ($userViewLengthData as $v)
            {
                $userDataService->setUid($v['uid']);
                $userDataService->setUserInfoDetail(UserDataService::USER_ACTICE_DETAIL);
                $userData = $userDataService->getUserInfo();

                if (!$userData)
                {
                    $this->errorLog("error|获取用户{$v['uid']}信息异常,停止脚本");
                    return false;
                }

                if ($v['view_length'] >= self::REQUIRE_MINUTES_03)
                {
                    $upIntegral = $userData['integral'] + $userRewardConfig[(ViewLength::REWARD_STATUS_04 - 1)];
                    $nIntegral = get_user_integral_by_level($userData['level'] + 1);
                    $rewardStatus = ViewLength::REWARD_STATUS_04;
                } elseif ($v['view_length'] >= self::REQUIRE_MINUTES_02 && $v['reward_status'] < ViewLength::REWARD_STATUS_03)
                {
                    $upIntegral = $userData['integral'] + $userRewardConfig[(ViewLength::REWARD_STATUS_03 - 1)];
                    $nIntegral = get_user_integral_by_level($userData['level'] + 1);
                    $rewardStatus = ViewLength::REWARD_STATUS_03;
                } elseif ($v['view_length'] >= self::REQUIRE_MINUTES_01 && $v['reward_status'] < ViewLength::REWARD_STATUS_02)
                {
                    $upIntegral = $userData['integral'] + $userRewardConfig[(ViewLength::REWARD_STATUS_02 - 1)];
                    $nIntegral = get_user_integral_by_level($userData['level'] + 1);
                    $rewardStatus = ViewLength::REWARD_STATUS_02;
                } else
                {
                    continue;
                }

                if ($upIntegral >= $nIntegral)
                {
                    $upLevel = $userData['level'] + 1;
                } else
                {
                    $upLevel = $userData['level'];
                }

                $userIntegralService->setUid($v['uid']);

                $db = $viewLengthDao->getDb();
                $db->beginTransaction();

                $res = $userIntegralService->updateUserIntegral($upIntegral, $upLevel);

                if (!$res)
                {
                    $this->errorLog("error|更新用户{$v['uid']}经验信息异常,停止脚本");
                    return false;
                }

                $res = $userLiveViewStatisticsService->updateUserLiveViewStatus($v['uid'], $rewardStatus, $date);

                if (!$res)
                {
                    $db->rollback();
                    $this->errorLog("error|更新用户{$v['uid']}观看时长奖励状态异常,停止脚本");
                    return false;
                }
                $db->commit();
                //通知用户缓存
                $event->trigger($event::ACTION_USER_MONEY_UPDATE, ['uid' => $v['uid']]);

                //发送socket消息
                switch ($rewardStatus)
                {
                    case ViewLength::REWARD_STATUS_02:
                        $rewardLevel = self::REWARD_LEVEL_01;
                        break;
                    case ViewLength::REWARD_STATUS_03:
                        $rewardLevel = self::REWAED_LEVEL_02;
                        break;
                    case ViewLength::REWARD_STATUS_04:
                        $rewardLevel = self::REWARD_LEVEL_03;
                        break;
                    default :
                        $rewardLevel = self::REWARD_LEVEL_01;
                }

                $chatRoomService->setUid($v['uid']);
                $luid = $chatRoomService->getChatRoomIdByUid();

                if ($luid)
                {
                    $msg = MsgPackage::getLiveLengthExpRewardMsgSocketPackage($luid, $v['uid'], 1, $rewardLevel, $userRewardConfig[($rewardStatus - 1)]);
                    SocketSend::sendMsg($msg);
                }
            }
        }

        $event = null;

        $timer->end();
        $t = $timer->getTime();
        $this->accessLog("end |success| 脚本执行完成; 耗时:{$t}s;");
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

$obj = new userViewLengthReward();

$obj->run();

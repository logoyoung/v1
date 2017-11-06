<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/8/7
 * Time: 16:48
 */
include __DIR__ . "/../../include/init.php";
use service\robot\RoomRobotService;
class roomRobot
{
    //休眠范围
    const SLEEP_RANGE = [ 5, 10 ];
    //增加机器人数量间隔(影响直播间观众数)
    const VIEWER_NUMBER_SPACE = [ 1, 4 ];
    //增加机器人激活间隔(影响进入直播间机器人及其发言)
    const ROBOT_ACTIVE_SPACE = [ 4, 7 ];
    //清理机器人时间
    const CLEAR_TIMER = 3;
    //房间机器人最大值(可配置)
    const ROOM_MAX_ROBOT_COUNT = 20;
    public function getRoomRobot()
    {
        if($this->_checkAlive() === false)
        {
            exit();
        }
        $robotSerivce  = new RoomRobotService();
        while (true)
        {
            //配置休眠时间
            $robotSerivce->setSleepTime(self::SLEEP_RANGE);
            //配置观众数量间隔
            $robotSerivce->setViewerNumSpaceValue(self::VIEWER_NUMBER_SPACE);
            //配置机器人激活间隔
            $robotSerivce->setRobottActiveSpaceValue(self::ROBOT_ACTIVE_SPACE);
            //配置房间机器人最大值
            $robotSerivce->setRobotMax(self::ROOM_MAX_ROBOT_COUNT);
            //执行房间规则
            $robotSerivce->runRoomRuleAction();
        }
    }
    private function _checkAlive(){
        $cmd = 'ps axu|grep "roomRobot"|grep -v "grep"|wc -l';
        $ret = shell_exec("$cmd");
        $ret = intval(rtrim($ret, "rn"));

        if($ret > 1) {
            return false;
        }else{
            return true;
        }
    }
    public function action()
    {
            $this->getRoomRobot();
    }
}
$robotObj = new roomRobot();
$robotObj->action();

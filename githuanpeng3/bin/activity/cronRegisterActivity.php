<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include __DIR__ . "/../../include/init.php";

use service\activity\RegisterActivityService;
use lib\activity\RegisterActivityTaskLib;

class cronRegisterActivity {

    public function run() {

        //不管进程存不存在   新数据还是要加的

        $mod = new RegisterActivityTaskLib();
        $service = new RegisterActivityService();

        $list = $mod->getRowDataByStatus(RegisterActivityTaskLib::REGISTER_ACTIVITY_TASK_LIB_STATUS_DEFAULT);
        foreach ($list as $key => $value) {
            $service->addUser($value['uid'], $value['todotype']);
        }
        $time = date('Y-m-d H:i:s', time() - 86400 * 2);
        $list2 = $mod->getRowDataByStatus(RegisterActivityTaskLib::REGISTER_ACTIVITY_TASK_LIB_STATUS_IN_QUEUE, 30, $time);
        foreach ($list2 as $key => $value) {
            $service->addUserOnlyRedis($value['uid'], $value['todotype']);
        }

        if (!$this->checkIsAlive()) {
            die("进程存活 运行中..." . PHP_EOL);
        }
        RegisterActivityService::checkCodeStatus();
        $i = 0 ;
        while ($i<100) {
            $i++;
            if (!RegisterActivityService::checkCodeStatus()) {
                die("文件有变动 ..." . PHP_EOL);
            }
            $model = new RegisterActivityService();
            $res = $model->execRegisterTodo();
        }
    }

    /**
     * PHP调用shell环境 检查 融云脚本是否存活
     * ------------------------------
     * @return boolean
     */
    private function checkIsAlive() {
        $cmd = 'ps axu|grep "cronRegisterActivity"|grep -v "grep"|wc -l';
        $ret = shell_exec("$cmd");
        $ret = intval(rtrim($ret, "rn"));

        if ($ret > 1) {
            return false;
        } else {
            return true;
        }
    }

}

$m = new cronRegisterActivity();
$m->run();

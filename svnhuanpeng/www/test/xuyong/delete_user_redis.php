<?php
require __DIR__.'/../../include/init.php';

use service\user\UserDataService;
use service\user\helper\UserRedis;
/**
 *  删除用户所有的 redis数据
 */
class test {

    public $uids = [ 1870, 47420,];

    private function deleteRedisData($uid)
    {
        $user     = new UserDataService;
        $user->setUid($uid);
        $user->setFromDb(true);
        $userData = $user->getUserInfo();
        if(!$userData)
        {
            exit('empty user data');
        }

        $redis    = new UserRedis();
        $keys['dataKey']  = $redis->getUserDataKey($uid);
        $keys['nickKey']  = $redis->getNickToUidKey($userData['nick']);
        $keys['phoneKey'] = $redis->getPhoneToUidKey($userData['phone']);
        $keys['loginKey'] = $redis->getUserDisbleLoginKey($uid);
        $rd = $redis->getRedis();

        foreach ($keys as $v)
        {

            if($rd->exists($v))
            {
                $msg = "\tdelete redis key:{$v} ";
                $msg.= ($rd->delete($v) ? "删除成功\n" : "删除失败\n");
            } else
            {
                $msg = "\tredis key:{$v} 不存在,无需删除\n";
            }

            echo $msg;
        }

    }

    public function run()
    {
        foreach ($this->uids as $uid)
        {
            echo "\n\n";

            $this->deleteRedisData($uid);

            echo "\n\n";
        }

    }
}

$obj = new test;
$obj->run();
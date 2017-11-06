<?php
ini_set('memory_limit', '1024M');
require __DIR__.'/../../bootstrap/i.php';
use service\user\helper\UserRedis;
use lib\user\UserStatic;

/**
 *    (测式使用)
 *
 *   清除用户redis缓存 （没有必要，请不要使用这个，可以先尝式运行rebuildUserRedis.php重写redis缓存）
 */
class deleteUserRedisData
{
    private $_log = 'delete_user_redis_data';

    public function getUid()
    {
        fwrite(STDOUT, "请输入uid; 输入完毕请按回车键确认\n");
        $stdin = fopen('php://stdin', 'r');
        $uid   = trim(fgets($stdin));
        if(!$uid || !is_numeric($uid))
        {
            fwrite(STDOUT, "无效的uid \n");
            fclose($stdin);
            exit;
        }

        fclose($stdin);

        return (int) $uid;
    }

    private function _deleteUserRedisData($uid)
    {
        $userRedis   = new UserRedis;
        $redis       = $userRedis->getRedis();
        $userRedis->setUid($uid);
        $staticData  = $userRedis->setGetUserStatic(true)->getUserData();

        if(!isset($staticData['userstatic']) || !$staticData['userstatic'])
        {
            $this->log("notice|reids里没有相关数据;uid:{$uid}");
            return true;
        }

        $phone       = isset($staticData['userstatic']['phone']) ? $staticData['userstatic']['phone'] : 0;
        $keys        = [];
        if($phone)
        {
              $keys[] = $userRedis->getPhoneToUidKey($phone);
        }

        $keys[] = $userRedis->getNickToUidKey($staticData['userstatic']['nick']);
        $keys[] = $userRedis->getUserDataKey($uid);
        $keys[] = $userRedis->getUserDisbleLoginKey($uid);
        $keys[] = $userRedis->getSilencedKey($uid);
        $keys[] = $userRedis->getDisableLiveKey($uid);

        $r = [];
        foreach ($keys as $k)
        {
            $r[] = $redis->delete($k);
        }

        if(array_search(false, $r, true))
        {
            $this->log("error|清除redis数据失败;uid:{$uid}");
            return false;
        }

        $this->log("success|清除redis数据成功;uid:{$uid}");
        return true;
    }

    public function run()
    {
        $uid = $this->getUid();
        $r = $this->_deleteUserRedisData($uid);
        if($r)
        {
            exit("清除redis数据成功;uid:{$uid}\n");
        }

        exit("清除redis数据失败;uid:{$uid}\n");
    }

    public function log($msg)
    {
        write_log($msg,$this->_log);
    }
}

$obj = new deleteUserRedisData;
$obj->run();
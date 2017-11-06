<?php
require __DIR__.'/../../include/init.php';
use service\user\UserDataService;
use service\user\helper\UserRedis;
class user
{

    public function getUserInfo($uid='69456'){
        $s = new UserDataService();

        //$s->setUid([69355,69376,68748,68724,68640,68620,68616,68632,68684]);
        $s->setUid($uid);
         // $s->setFromDb(true);
        $s->setUserInfoDetail(UserDataService::USER_STATIC_ACTICE_BASE);
        $r = $s->getUserInfo();

        print_r($r);
        die;
        $s->setUid($uid);
        $s->setUserInfoDetail(UserDataService::USER_STATIC_ACTICE_BASE);
        $s->setFromDb(true);
        $r = $s->getUserInfo();

        print_r($r);

    }

    public function exist($uid='')
    {
        $uid = $uid?:69355;
        $s = new UserDataService();
        $s->setUid($uid);
         $r = $s->isExist();
        var_dump($r);
    }


    public function getEncpass()
    {

        $uid = 69456;
        $redis   = new UserRedis();
        $redis->setUid($uid);
        $result  = $redis->getEncpass();
        print_r($result);
    }

    public function t1()
    {
        $event = new \service\event\EventManager();
        //用户财产变动事件
        $event->trigger(\service\event\EventManager::ACTION_USER_MONEY_UPDATE,['uid' => 69355]);
        $event = null;
    }


    public function checkStatus($uid='69367',$encpass='a4a8656ad2a53387caf411bb40bbeb2a')
    {
        $s = new UserDataService();
        $s->setUid($uid);
        $s->setEnc($encpass);
        var_dump($s->checkUserState());
    }
}

$user = new user();
// $user->checkStatus();
// die;
$user->getUserInfo([69456]);
//$user->exist(12585);
//$user->getEncpass();
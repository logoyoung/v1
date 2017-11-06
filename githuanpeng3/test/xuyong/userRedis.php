<?php
require __DIR__.'/../../include/init.php';
use service\user\helper\UserRedis;
use lib\user\UserStatic;
use lib\user\UserActive;
class testUserRedis
{

    private $redis;

    public function __construct() {
        $this->redis = new UserRedis();
    }

    public function bindPhoneToUid($phone,$uid) {
        $s = $this->redis->setUid($uid)->setPhone($phone)->bindPhoneToUid();
        if(!$s || $this->getUidByPhone($phone) != $uid) {
            echo "bindPhoneToUid error \n";
        } else {
            echo "bindPhoneToUid success \n";
        }
    }

    public function getUidByPhone($phone,$testUid='') {
        $s = $this->redis->setPhone($phone)->getUidByPhone();
        if(!$testUid) {
            return $s;
        }
        if($s != $testUid){
            echo "get getUidByPhone error \n";
        } else {
            echo "get getUidByPhone success \n";
        }
    }

    public function unsetPhone($phone) {
        $s = $this->redis->setPhone($phone)->unsetPhone();
        if(!$s || $this->getUidByPhone($phone)) {
            echo "unsetPhone error\n";
        }  else {
            echo "unsetPhone success\n";
        }
    }

    public function setUserStaticData($uid,$data) {
        $s = $this->redis->setUid($uid)->setUserStaticData($data);
        if(!$s){
            echo "setUserStaticData error \n";
        } else {
            echo "setUserStaticData success \n";
        }
    }

    public function setUserActiveData($uid,$data){
        $s = $this->redis->setUid($uid)->setUserActiveData($data);
        if(!$s){
            echo "setUserActiveData error \n";
        } else {
            echo "setUserActiveData success \n";
        }
    }
    public function getUserData($uid) {
        $s = $this->redis->setUid($uid)->setGetUserStatic(true)->setGetUserActive(true)->getUserData();
        print_r($s);
    }


    public function runTest() {
        $phone = '13269691568';
        $uid   = 1815;
        $this->bindPhoneToUid($phone,$uid);
        $this->getUidByPhone($phone,$uid);
        // //$this->unsetPhone($phone);
        $this->getUidByPhone($phone);

        $user = new UserStatic();;

        $userStaticData = $user->getUserStaticData(['1815']);
        $this->setUserStaticData($uid,$userStaticData);

        $activeObj = new UserActive();
        $userActiveData = $activeObj->getUSerActiveData($uid);
        $this->setUserActiveData($uid,$userActiveData);

        $this->getUserData($uid);
    }
}

$o = new testUserRedis();
$o->runTest();

<?php
require __DIR__.'/../../include/init.php';

use lib\user\UserStatic;
use lib\user\UserActive;

class testUserStatic {

    private $user;

    public function __construct() {
        $this->user = new UserStatic();
    }

    public function getUsertStaticFromMaster($uid,$fields = [])
    {
        $this->user->setMaster(true);
        $data = $this->user->getUserStaticData($uid,$fields);
        print_r($data);
    }

    public function getUsertStatic($uid,$fields = [])
    {
        $data = $this->user->getUserStaticData($uid,$fields);
        print_r($data);
    }

}

class testUserActive {
    private $user;

    public function __construct() {
        $this->user = new UserActive();
    }

    public function getUserActiveDataFromMaster($uid,$fields=[]) {
        $this->user->setMaster(true);
        $data = $this->user->getUserActiveData($uid,$fields);
        print_r($data);
        return $data;
    }

    public function getUserActiveData($uid,$fields=[]) {
        $data = $this->user->getUserActiveData($uid,$fields);
        print_r($data);
        return $data;
    }
}

$t1 = new testUserStatic();
$t1->getUsertStaticFromMaster(['1815'],$fields = ['username','uid','nick']);
$t1->getUsertStatic($uid='1815',$fields = ['username','uid','nick']);

$t2 = new testUserActive();
$t2->getUserActiveDataFromMaster(['1815']);
$t2->getUserActiveData(['1815']);

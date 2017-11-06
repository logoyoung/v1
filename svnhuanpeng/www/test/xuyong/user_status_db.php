<?php
require __DIR__.'/../../include/init.php';
use lib\user\UserStatus;
class test
{
    public function add($uid, $type,$scope, $etime)
    {
        $statusDb = new UserStatus();
        return $statusDb->addUserDisableStatus($uid, $type,$scope, $etime);
    }

    public function delete($uid, $type,$scope)
    {
        $statusDb = new UserStatus();
        return $statusDb->deleteUserDisableStatus($uid, $type,$scope);
    }
}

$uid   = 888;
$type  = 10;
$scope = 1;
$etime = 10;

$obj = new test;
//var_dump($obj->add($uid, $type, $scope, $etime));
//var_dump($obj->delete($uid, $type, $scope));
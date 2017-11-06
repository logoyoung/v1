<?php
require __DIR__.'/../../include/init.php';

use lib\information\AdminInformation;
use lib\information\AppInformation;

class test
{

    public function getDataByStatus($status=1)
    {
        $db = new AppInformation;
        $data = $db->getDataByStatus($status);
        print_r($data);
        $adminDb = new AdminInformation;

        $data = $adminDb->getInfomationDataById($data['info_id']);
        print_r($data);

    }
}

$obj = new test;
$obj->getDataByStatus($status=1);
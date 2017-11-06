<?php
namespace yalong;
include __DIR__."/../../../include/init.php";
use service\push\SmsPush;

class testSMS
{
    public function display(){
        $obj = new SmsPush(); 
        $data = $obj->sendSMS("73163,12068",'测试短信部分用户下发',"欢朋短信下发测试 ing");
        echo '<pre>';
        var_dump($data);  
        echo '</pre>';
    }
}

$obj = new testSMS();
$obj->display();


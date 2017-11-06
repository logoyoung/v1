<?php
require __DIR__.'/../../include/init.php';

use lib\due\DueOrder;
use service\live\LiveService;

/**
 * 个人测试类
 * @author longgang chen <longgang@6.cn>
 * @date 2017-06-06 15:41:53
 * @copyright (c) 2017, 6.cn
 * @version 1.0.0
 */

class longgang_test{
    
    public function f1()
    {
        $obj = new DueOrder();
        $data = ['uid'=>1,'page'=>0,'size'=>5];
        $list = $obj->getUserOrderList($data);
         
        var_dump($list);
    }
    
    public function f2()
    {
        $liveService = new LiveService();
        $liveId = 657446;
        
        $res = $liveService->updateLivePosterRedis($liveId, 'Y-657446-20170915161348-big-heng.jpg?1505465357',false);
        var_dump($res);
    }
}

$obj =  new longgang_test();
$obj->f2();
<?php

/**
 * flase 推荐
 * date 2016-05-30 11:01
 * author yandong@6rooms.com
 */
include '../../includeAdmin/init.php';
include '../../includeAdmin/publicRequist.class.php';
$res=publicRequist::outside_setRate(array('20'=>13),60,'demo');
var_dump($res);

//function updateUserCoin($db){
//    $sql="update  useractive  set  hpbean=hpbean -1000 ,hpcoin=hpcoin-100  where uid=3600";
//    $res=$db->query($sql);
//    return $res;
//}

//updateUserCoin($db);





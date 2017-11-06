<?php

include "../../includeAdmin/init.php";
include INCLUDE_DIR . 'statistics/Userstatistic.class.php';

$db = new DBHelperi_admin();
$userStatistic = new UserStatistic($db);
$newUsers = array(
    'today' => $userStatistic->todayNewUsers(),
    'yesterday' => $userStatistic->yestNewUsers()
);

$newCustomers = array(
    'today' => $userStatistic->todayNewCustomers(),
    'yesterday' => $userStatistic->yestNewCustomers()
);

$totalRecharge = array(
    'today' => $userStatistic->todayTotalRecharge(),
    'yesterday' => $userStatistic->yestTotalRecharge()
);

$totalConsumption = array(
    'today' => $userStatistic->todayTotalConsumption(),
    'yesterday' => $userStatistic->yestTotalConsumption()
);

$ret_key = array('newUsers', 'newCustomers', 'totalRecharge', 'totalConsumption');
foreach($ret_key as $val){
    $ret[$val] = $$val;
}

succ($ret);
?>
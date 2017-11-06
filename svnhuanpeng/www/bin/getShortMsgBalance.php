<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/2/10
 * Time: 17:00
 */

$url = 'http://liveuser/api/getBalanceInfo.php?appid=102';

$ret = file_get_contents($url);

$ret = json_decode($ret, true);

$result = array();

//foreach ($ret['resuData'] as $key => $val){
//    $result[]['server'] = $key['lib'];
//
//    $data = $val['data'];
//
//    $result[]['current_ver'] = $data['current_ver'];
//    $result[]['ver'] = $data['ver'];
//    $result[]['balance'] = $data['balance'];
//    $result[]['account'] = $data['account'];
//}

print_r($ret);
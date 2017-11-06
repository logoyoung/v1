<?php

include "/usr/local/huanpeng/htdocs/init.php";

use \system\HttpHelper;


$data = [
	'list' => [1860=>123123,1870=>123123123123],
	'rate' => 80,
	'desc' => 'test change rate'
];

$data['token'] = token_create( $data, DOTA_AUTHORIZE_KEY, false);

var_dump($data);

$HttpHelper = new HttpHelper();

$url = "http://dota.huanpeng.com/FinanceApi/setGiftRate";

$HttpHelper->addPost( $url, $data, 5 );

$result = $HttpHelper->getResult();

var_dump($result);



test // git branch
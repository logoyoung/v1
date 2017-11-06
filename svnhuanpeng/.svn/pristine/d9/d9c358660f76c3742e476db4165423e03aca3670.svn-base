<?php

require __DIR__.'/../../include/init.php';

function create($data=[])
{
    return token_create($data,DOTA_AUTHORIZE_KEY);
}

function check($token,$data)
{

}


$data =[
    'uid'    => 555,
    'type'   => 10,
    'status' => 2,
    'scope'  => 1,
    'etime'  => 0,
];

//curl -d'uid=555&type=10&status=2&scope=1&etime=3600&token=ff58aefa7a692e7af9da6b30cd72679d' 'dota.huanpeng.com/user/ups'
echo create($data);
<?php

$arr = explode('.', $_SERVER['HTTP_HOST']);
array_shift($arr);
$domain = implode('.', $arr);
return array(
    //'INTRA_IP'=>1,#强制内网模式;本地开发开启
    //'ACL_SUPER'=>1,#权限验证超级用户;本地开发开启
    'SESSION_EXPIRE'=>18000,
    'DATA_CACHE_PREFIX'=>'',
    'DEFAULT_CONTROLLER'=>'Public',
    'SESSION_OPTIONS'=>array(
        'name'=>'OP_S',
        'type'=>'redis',
        //'expire'=>8640000,
        'domain'=>'.'.$domain,
    ),
    'TMPL_ACTION_ERROR'=>APP_PATH.'Admin/View/Base/jump.html',
    'TMPL_ACTION_SUCCESS'=>APP_PATH.'Admin/View/Base/jump.html',
);

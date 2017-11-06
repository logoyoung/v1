<?php
return [

    'APP_NAME'             => 'hpFrame',

    'LOG_DIR'              => '/data/logs/dev_user_name/',

    //http 访问命名空间，只要在该命名空间内的接口才能被外部访问
    //命名空间 可自行修改
    'APP_NAMESPACE'        => '\\app\\http\\',

    'API_DOMAIN_URL'       => DOMAIN_PROTOCOL.'dev_user_name.hpframe.huanpeng.com',

    'STATIC_DOMAIN_URL'    => DOMAIN_PROTOCOL.'dev_user_name.static.hpframe.huanpeng.com',

    //dev 服务器必写
    'DEV_SERVER'           => ['huanp-node-1.novalocal' => 1],

    //pre 服务器必写
    'PRE_SERVER'           => ['huanp-node-3.novalocal' => 1],

    //线上服务器（建义不需要配置）
    'PRO_SERVER'           => ['HangpengW28_11' => 1, 'HangpengW28_118' => 1, 'Huanpeng_adm_nfs_28_11' => 1],

];
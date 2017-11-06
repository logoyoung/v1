<?php
//生产环境mysql 配置
return [

    'huanpeng' => [

        //调式模式开关 (开启会记录每条sql及运行时间，建义开发环境下开启)
        'debug'  => false,

        //主库
        'master' => [
            [
                'host'     => '172.20.100.210',
                'port'     => 3306,
                'username' => 'hppro',
                'password' => 'huanpro123peng',
                'dbname'   => 'huanpeng',
                'charset'  => 'latin1',
                'options'  => [PDO::ATTR_TIMEOUT => 5],
            ],
        ],

        //从库
        'slave'  => [
            [
                'host'     => '172.20.100.200',
                'port'     => 3306,
                'username' => 'hppro',
                'password' => 'huanpro123peng',
                'dbname'   => 'huanpeng',
                'charset'  => 'latin1',
                'options'  => [PDO::ATTR_TIMEOUT => 5],
            ],

        ],

    ],

    'due' => [

        //调式模式开关 (开启会记录每条sql及运行时间，建义开发环境下开启)
        'debug'  => false,

        //主库
        'master' => [
            [
                'host'     => '172.20.100.210',
                'port'     => 3306,
                'username' => 'hppro',
                'password' => 'huanpro123peng',
                'dbname'   => 'huanpeng',
                'charset'  => 'latin1',
                'options'  => [],
            ],
        ],

        //从库
        'slave'  => [
            [
                'host'     => '172.20.100.200',
                'port'     => 3306,
                'username' => 'hppro',
                'password' => 'huanpro123peng',
                'dbname'   => 'huanpeng',
                'charset'  => 'latin1',
                'options'  => [],
            ],

        ],

    ],
];
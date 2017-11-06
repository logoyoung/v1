<?php
$devScriptServer = ['huanp-node-1.novalocal'];

return [

        't-1' => [

            //执行服务器名 1
            'server'       => $devScriptServer,

            //crontab 定时
            'crontab'      => '*/1 * * * *',

            //执行的脚本
            'script'       => "/data/www/xuyong/hpFrame/task/cron/test/a.php 3",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],

        't-2' => [

            //执行服务器名
            'server'       => $devScriptServer,

            //crontab 定时
            'crontab'      => '*/2 * * * *',

            //执行的脚本
            'script'       => '/data/www/xuyong/hpFrame/task/cron/test/b.php 1',

            // 1正常运行，0关闭
            'status'       =>  0,

            //bash or php
            'cmd'          => 'php',
        ],

];
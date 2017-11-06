<?php
$proScriptSever = ['Huanpeng_adm_nfs_28_11'];

return [

        //校验用户redis数据与db数据一致性
        'cronCheckUserRedis' => [

            //执行服务器名
            'server'       => $proScriptSever,

            //crontab 定时
            'crontab'      => '0 2 */1 * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/task/cron/user/cronCheckUserRedis.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],

        //校验anchor与roomid redis与db一致性
        'cronCheckUserRedis' => [

            //执行服务器名
            'server'       => $proScriptSever,

            //crontab 定时
            'crontab'      => '0 3 */1 * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/task/cron/anchor/cronCheckAnchorRedis.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],

];
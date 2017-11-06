<?php

return [

        //校验用户redis数据与db数据一致性
        'cronCheckUserRedis' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

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
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '0 3 */1 * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/task/cron/anchor/cronCheckAnchorRedis.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],

        //redis 服务监控
        'redisMonitor' => [

            //执行服务器名
            'server'       => ['HangpengW28_119','HangpengW28_118'],

            //crontab 定时
            'crontab'      => '*/1 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/task/cron/monitor/redisMonitor.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],

        //db 服务监控
        'dbMonitor' => [

            //执行服务器名
            'server'       => ['HangpengW28_119','HangpengW28_118'],

            //crontab 定时
            'crontab'      => '*/1 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/task/cron/monitor/dbMonitor.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',
        ],

        //php Error
        'phpErrorMonitor' => [

            //执行服务器名
            'server'       => ['HangpengW28_119','HangpengW28_118'],

            //crontab 定时
            'crontab'      => '*/1 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/task/cron/monitor/phpErrorMonitor.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',
        ],

        //重置 更新用户被评论标签 每天凌晨3点执行       yalong2017@6.cn
        'resetUserTags' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '0 3 * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/due/resetUserTags.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],
        //融云消息下发守护脚本，防止脚本 挂掉 用作 唤醒作用       yalong2017@6.cn
        'rongCloudSendMsg' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '*/2 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/due/rongCloudSendMsg.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],

        //系统消息 socket推送    yalong2017@6.cn
        'systemMsg' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '*/1 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/due/systemMsg.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],

        //创建月表 脚本  yalong2017@6.cn
        'createMonthTables' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '0 4 25-31 * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/tools/createMonthTables.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],

        //陪玩订单处理定时脚本 liupeng@6.cn
        'orderCronTable' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '* * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/due/orderCronTable.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],
        //主播时长统计缓存脚本 longgang@6.cn
        'cacheAnchorLiveLength' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '0 */1 * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/anchor/cacheAnchorLiveLength.php",

            // 1正常运行，0关闭
            'status'       =>  0,

            //bash or php
            'cmd'          => 'php',

        ],
        //直播列表缓存定时刷新 longgang@6.cn
        'cronLiveListRedis' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '*/1 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/live/cronLiveListRedis.php",

            // 1正常运行，0关闭
            'status'       =>  0,

            //bash or php
            'cmd'          => 'php',

        ],
        //直播信息缓存定时刷新 longgang@6.cn
        'cronLiveInfoRedis' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '*/5 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/live/cronLiveInfoRedis.php",

            // 1正常运行，0关闭
            'status'       =>  0,

            //bash or php
            'cmd'          => 'php',

        ],
        //游戏信息缓存定时刷新 longgang@6.cn
        'cronGameDataRedis' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '0 */1 * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/game/cronGameDataRedis.php",

            // 1正常运行，0关闭
            'status'       =>  0,

            //bash or php
            'cmd'          => 'php',

        ],
        //游戏直播列表缓存定时刷新 longgang@6.cn
        'cronGameLiveListRedis' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '*/1 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/game/cronGameLiveListRedis.php",

            // 1正常运行，0关闭
            'status'       =>  0,

            //bash or php
            'cmd'          => 'php',

        ],
        //首页缓存信息定时刷新 longgang@6.cn
        'cronIndexDataRedis' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '*/1 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/index/cronIndexDataRedis.php",

            // 1正常运行，0关闭
            'status'       =>  0,

            //bash or php
            'cmd'          => 'php',

        ],    
];
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
        //注册活动回调 liupeng@6.cn
        'cronRegisterActivity' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '* * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/activity/cronRegisterActivity.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],
      //自动建表 liupeng@6.cn
        'createTableByUid' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '0 1 * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/tools/createTableByUid.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],
        //主播时长统计缓存脚本 longgang@6.cn
        'lastMonthAnchorLiveLength' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '0 1 1 * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/anchor/lastMonthAnchorLiveLength.php",

            // 1正常运行，0关闭
            'status'       =>  0,

            //bash or php
            'cmd'          => 'php',

        ],
        //主播时长统计缓存脚本 longgang@6.cn
        'curMonthAnchorLiveLength' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '*/10 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/anchor/curMonthAnchorLiveLength.php",

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
            'crontab'      => '* */1 * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/live/cronLiveInfoRedis.php",

            // 1正常运行，0关闭
            'status'       =>  1,

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
        //用户观看时长统计 longgang@6.cn
        'userViewLength'=>[
            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '*/1 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/statistics/userViewLength.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',
        ],
        //用户观看时长奖励 longgang@6.cn
        'userViewLengthReward'=>[
            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '*/1 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/reward/userViewLengthReward.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',
        ],
        //主播在线观众数以及机器人发言 xingwei@6.cn
        'cronRoomRobot' => [

            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_1l'],

            //crontab 定时
            'crontab'      => '*/1 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/robot/roomRobot.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',

        ],
        //主播每日直播时长经验奖励 xingwei@6.cn
        'cronAnchorDayReward'=>[
            //执行服务器名
            'server'       => ['Huanpeng_adm_nfs_28_11'],

            //crontab 定时
            'crontab'      => '*/5 * * * *',

            //执行的脚本
            'script'       => "/usr/local/huanpeng/bin/anchor/anchorDayLengthExpReward.php",

            // 1正常运行，0关闭
            'status'       =>  1,

            //bash or php
            'cmd'          => 'php',
        ],
		'liveheartmaster' => [

			//执行服务器名
			'server'       => ['Huanpeng_adm_nfs_28_11'],

			//crontab 定时
			'crontab'      => '*/1 * * * *',

			//执行的脚本
			'script'       => '/usr/local/huanpeng/bin/live/liveheart.php  master',

			// 1正常运行，0关闭
			'status'       =>  1,

			//bash or php
			'cmd'          => 'php',
		],
		'liveheartslave' => [

			//执行服务器名
			'server'       => ['Huanpeng_adm_nfs_28_11'],

			//crontab 定时
			'crontab'      => '*/1 * * * *',

			//执行的脚本
			'script'       => '/usr/local/huanpeng/bin/live/liveheart.php slave',

			// 1正常运行，0关闭
			'status'       =>  1,

			//bash or php
			'cmd'          => 'php',
		],
		'liveheartcache' => [

			//执行服务器名
			'server'       => ['Huanpeng_adm_nfs_28_11'],

			//crontab 定时
			'crontab'      => '*/1 * * * *',

			//执行的脚本
			'script'       => '/usr/local/huanpeng/bin/live/livestatus.php',

			// 1正常运行，0关闭
			'status'       =>  1,

			//bash or php
			'cmd'          => 'php',
		],
		'videoheartmaster' => [

			//执行服务器名
			'server'       => ['Huanpeng_adm_nfs_28_11'],

			//crontab 定时
			'crontab'      => '*/1 * * * *',

			//执行的脚本
			'script'       => '/usr/local/huanpeng/bin/live/videoheart.php master',

			// 1正常运行，0关闭
			'status'       =>  1,

			//bash or php
			'cmd'          => 'php',
		],
		'videoheartslave' => [

			//执行服务器名
			'server'       => ['Huanpeng_adm_nfs_28_11'],

			//crontab 定时
			'crontab'      => '*/1 * * * *',

			//执行的脚本
			'script'       => '/usr/local/huanpeng/bin/live/videoheart.php slave',

			// 1正常运行，0关闭
			'status'       =>  1,

			//bash or php
			'cmd'          => 'php',
		],
		//财务担保交易订单
		"cronGuaranteePay" => [

			'server'       => ['Huanpeng_adm_nfs_28_11'],

			'crontab' 	   => "* * * * *",

			'script'       => "/usr/local/huanpeng/bin/finance/deamon/cronGuaranteePay.php",

			'status'       => 1,

			"cmd"          => 'php'
		],
		//经济公司提现
		"companyWithdraw" => [
			'server'       => ['Huanpeng_adm_nfs_28_11'],

			'crontab' 	   => "0 0 1 * *",

			'script'       => "/usr/local/huanpeng/bin/finance/deamon/companyWithdraw.php",

			'status'       => 1,

			"cmd"          => 'php'
		],
		//直播观看人数统计
		"liveViewerStatistic" => [
			'server'       => ['Huanpeng_adm_nfs_28_11'],

			'crontab' 	   => "*/5 * * * *",

			'script' 	   => "/usr/local/huanpeng/bin/statistics/liveViewer.php PRO",

			'status'       => 1,

			'cmd'          => 'php'
		],
];
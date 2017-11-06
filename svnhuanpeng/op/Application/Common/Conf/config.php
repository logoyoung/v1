<?php
$config = array(
    
    'DEV'=>array(
        'URL_MODEL'=>2,
        'URL_HTML_SUFFIX'=>false,
        'URL_DENY_SUFFIX'=>false,
        'VAR_PATHINFO'=>'_path_info_',
        'TMPL_L_DELIM'=>'{{',
        'TMPL_R_DELIM'=>'}}',
        'AUTOLOAD_NAMESPACE' => array(
            'HP'     => COMMON_PATH.'HP',
        ),
        'SHOW_PAGE_TRACE' =>false,
        'TMPL_PARSE_STRING'  =>array(
            '__RES__'=>'/',
            '__NAME__'=>'欢朋',
        ),
        
        //***********DB**************//
        'DB_TYPE'   => 'mysqli', // 数据库类型
        'DB_HOST'   => '192.168.21.64', // 服务器地址
        'DB_NAME'   => 'huanpeng', // 数据库名
        'DB_RW_SEPARATE'=>false,//读写分离
		'DB_DEPLOY_TYPE'=>false,//分布式部署
        'DB_USER'   => 'hpdev', // 用户名
        'DB_PWD'    => 'hhppddeevv112233',  // 密码
        'DB_PORT'   => '3306', // 端口
        'DB_CHARSET'   => 'latin1', // 编码
        'DB_PREFIX' => '', // 数据库表前缀
        
        //***********Cache************//
        'DATA_CACHE_TYPE' => 'redis',
        'REDIS_HOST' => '192.168.21.64',
        'REDIS_PORT' => '6379',
        
        
        //********** upload **********//
        'FILE_UPLOAD_PATH'      =>  '/data/huanpeng-img',
        'FILE_PUBLIC_MODE'      =>  1,
        'FILE_BUCKET_START'      =>  10,
        'FILE_BUCKET_END'      =>  99,
        #图片自动剪裁设定
        'FILE_PIC_THUMB'        => [
            '1'=>['type'=>'thumb','width'=>200,'height'=>200],
            '2'=>['type'=>'watermark'],
        ],
        
        //图片路径 zwq 2017年5月12日
        'APK_DIR' => '/app',
        'GAME_I_DIR' => 'gimg',
        'GAME_DIR' => 'game',
        'ACTIVE_DIR' => 'active',
        'INFORMATION_DIR' => 'information'.'/'.date('Ym'),
		'GIFT_DIR' => 'gift',
		'EXAMINE_DIR' => 'examine',
        
        
        //********* cronlog **********//
        'HP_LOG_PATH' => '/data/logs/op/',
    ),
    
    'PRE'=>array(
        'URL_MODEL'=>2,
        'URL_HTML_SUFFIX'=>false,
        'URL_DENY_SUFFIX'=>false,
        'VAR_PATHINFO'=>'_path_info_',
        'TMPL_L_DELIM'=>'{{',
        'TMPL_R_DELIM'=>'}}',
        'AUTOLOAD_NAMESPACE' => array(
            'HP'     => COMMON_PATH.'HP',
        ),
        'SHOW_PAGE_TRACE' =>false,
        'TMPL_PARSE_STRING'  =>array(
            '__RES__'=>'/',
            '__NAME__'=>'欢朋',
        ),
    
        //***********DB**************//
        'DB_TYPE'   => 'mysqli', // 数据库类型
        'DB_HOST'   => '192.168.21.65', // 服务器地址
        'DB_NAME'   => 'huanpeng', // 数据库名
        'DB_RW_SEPARATE'=>false,//读写分离
		'DB_DEPLOY_TYPE'=>false,//分布式部署
        'DB_USER'   => 'hpdev', // 用户名
        'DB_PWD'    => 'hhppddeevv112233',  // 密码
        'DB_PORT'   => '3306', // 端口
        'DB_CHARSET'   => 'latin1', // 编码
        'DB_PREFIX' => '', // 数据库表前缀
    
        //***********Cache************//
        'DATA_CACHE_TYPE' => 'redis',
        'REDIS_HOST' => '192.168.21.65',
        'REDIS_PORT' => '6379',
    
    
        //********** upload **********//
        'FILE_UPLOAD_PATH'      =>  '/data/huanpeng-img',
        'FILE_PUBLIC_MODE'      =>  1,
        'FILE_BUCKET_START'      =>  10,
        'FILE_BUCKET_END'      =>  99,
        #图片自动剪裁设定
        'FILE_PIC_THUMB'        => [
            '1'=>['type'=>'thumb','width'=>200,'height'=>200],
            '2'=>['type'=>'watermark'],
        ],
    
        //图片路径 zwq 2017年5月12日
        'APK_DIR' => '/app',
        'GAME_I_DIR' => 'gimg',
        'GAME_DIR' => 'game',
        'ACTIVE_DIR' => 'active',
        'INFORMATION_DIR' => 'information'.'/'.date('Ym'),
		'GIFT_DIR' => 'gift',
		'EXAMINE_DIR' => 'examine',
    
        //********* cronlog **********//
        'HP_LOG_PATH' => '/data/logs/op/',
    ),
    
    'PRO'=>array(
        'URL_MODEL'=>2,
        'URL_HTML_SUFFIX'=>false,
        'URL_DENY_SUFFIX'=>false,
        'VAR_PATHINFO'=>'_path_info_',
        'TMPL_L_DELIM'=>'{{',
        'TMPL_R_DELIM'=>'}}',
        'AUTOLOAD_NAMESPACE' => array(
            'HP'     => COMMON_PATH.'HP',
        ),
        'SHOW_PAGE_TRACE' =>false,
        'TMPL_PARSE_STRING'  =>array(
            '__RES__'=>'/',
            '__NAME__'=>'欢朋',
        ),
    
        //***********DB**************//
        'DB_TYPE'   => 'mysqli', // 数据库类型
        'DB_HOST'   => '172.20.100.210,172.20.100.200', // 服务器地址
        'DB_NAME'   => 'huanpeng', // 数据库名
        'DB_RW_SEPARATE'=>true,//读写分离
		'DB_DEPLOY_TYPE'=>true,//分布式部署
        'DB_USER'   => 'hppro,hppro', // 用户名
        'DB_PWD'    => 'huanpro123peng,huanpro123peng',  // 密码
        'DB_PORT'   => '3306,3306', // 端口
        'DB_CHARSET'   => 'latin1', // 编码
        'DB_PREFIX' => '', // 数据库表前缀
    
        //***********Cache************//
        'DATA_CACHE_TYPE' => 'redis',
        'REDIS_HOST' => '172.20.28.147',
        'REDIS_PORT' => '9981',
    
    
        //********** upload **********//
        'FILE_UPLOAD_PATH'      =>  '/leofs/i',
        'FILE_PUBLIC_MODE'      =>  1,
        'FILE_BUCKET_START'      =>  10,
        'FILE_BUCKET_END'      =>  99,
        #图片自动剪裁设定
        'FILE_PIC_THUMB'        => [
            '1'=>['type'=>'thumb','width'=>200,'height'=>200],
            '2'=>['type'=>'watermark'],
        ],
    
        //图片路径 zwq 2017年5月12日
        'APK_DIR' => '/app',
        'GAME_I_DIR' => 'gimg',
        'GAME_DIR' => 'game',
        'ACTIVE_DIR' => 'active',
        'INFORMATION_DIR' => 'information'.'/'.date('Ym'),
		'GIFT_DIR' => 'gift',
		'EXAMINE_DIR' => 'examine',
    
        //********* cronlog **********//
        'HP_LOG_PATH' => '/data/logs/op/',
    ),
);

return $config[$GLOBALS['env']];

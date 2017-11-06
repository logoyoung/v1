<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/15
 * Time: 下午5:33
 */
define('WEBSITE_ROOT', '/usr/local/huanpeng/');
define('INCLUDE_DIR', WEBSITE_ROOT . 'include/');
define('LOG_DIR', '/data/logs/');

define("ADMIN_ROOT",WEBSITE_ROOT.'/htdocs/admin2/');
define("ADMIN_MODULE",ADMIN_ROOT.'module/');
// 域名
define('DOMAINNAME_PRO', 'www.huanpeng.com');
define('DOMAINNAME_PRO_IMG', 'img.huanpeng.com');
define('DOMAINNAME_DEV', 'dev.huanpeng.com');
define('DOMAINNAME_DEV_IMG', 'dev-img.huanpeng.com');


date_default_timezone_set('Asia/Shanghai');

if (isset($_SERVER["HTTP_HOST"]) and $_SERVER["HTTP_HOST"] == DOMAINNAME_DEV)
    $GLOBALS['env'] = 'DEV';
elseif (isset($argv[1]) and $argv[1] == 'DEV')
    $GLOBALS['env'] = 'DEV';
else
    $GLOBALS['env'] = 'PRO';

// 环境相关配置
$GLOBALS['env-def'] = array(
    'DEV' => array(
        'domain' => DOMAINNAME_DEV,
    ),
    'PRO' => array(
        'domain' => DOMAINNAME_PRO,
    ),
);


define('ADMIN_URL_ROOT', 'http://' . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/admin2/');
define("__ADMIN_STATIC__", ADMIN_URL_ROOT.'common/');

?>
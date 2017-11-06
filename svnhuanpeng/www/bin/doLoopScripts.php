<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/27
 * Time: 15:36
 */


/**
 *
 * (php /usr/local/huanpeng/bin/robot/robotReduce.php PRE >> /data/logs/doScript.log  2>&1 &)
 * (php /usr/local/huanpeng/bin/robot/robot.php PRE >> /data/logs/doScript.log  2>&1 &)
 * (php /usr/local/huanpeng/bin/robot/robot.php PRE >> /data/logs/doScript.log  2>&1 &)
 * (php /usr/local/huanpeng/bin/robot/realRobotActive.php PRE >> /data/logs/doScript.log  2>&1 &)
 *
 * (php /usr/local/huanpeng/bin/robot/new/robot.php PRE >> /data/logs/doScript.log 2>&1 &)
 * (php /usr/local/huanpeng/bin/pushmsg/livestart.php PRE >> /data/logs/doScript.log  2>&1 &)
 *
 *
 *
 *
 *
 *
 */
include( __DIR__ . '/../include/init.php' );

$bin = WEBSITE_ROOT . 'bin/';

$scripts = [
	"{$bin}robot/robotReduce.php {$GLOBALS['env']}",
	"{$bin}robot/robot.php {$GLOBALS['env']}",
	"{$bin}robot/realRobotActive.php {$GLOBALS['env']}",
	"{$bin}pushmsg/liveStart.php"
];
$cmd = '(php ';
foreach ( $scripts as $k => $script )
{
	echo "执行:$script\n";
	$cmd .= " $script";
}
$cmd .= ' &)';
var_dump($cmd);
//`$cmd`;
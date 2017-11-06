<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/3/21
 * Time: 15:16
 */

include __DIR__.'/../include/init.php';

$env = strtoupper( $GLOBALS['env'] );

$dir = __DIR__;

$cmd =
	"(php $dir/robot/robot.php $env &)\n" .
	"(php $dir/robot/robotReduce.php $env &)\n" .
	"(php $dir/robot/realRobotActive.php $env &)\n" .
	"(php $dir/saveWcsPoster.php $env &)\n" .
	"(php $dir/saveWcsVideo.php $env &)\n" .
	"(php $dir/downvideo.php $env &)\n" ;

var_dump(`$cmd`);
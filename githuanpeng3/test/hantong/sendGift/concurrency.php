<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/9/12
 * Time: 下午5:29
 */
// declare(strict_types = 1);

declare(ticks=1);
error_reporting(0);
include __DIR__."/../../../include/init.php";

pcntl_signal(SIGCHLD, "garbage");

echo "parent start, pid ", getmypid(), "\n";

for($i=0; $i< 60; ++$i)
{
	$pid = pcntl_fork();
	if($pid == -1)
	{
		die("cannot fork");
	}
	elseif($pid > 0)
	{
		echo "parent continue \n";
	}
	else
	{
		echo "child start, pid", getmypid(), "\n";
		$cmd = "php ". __DIR__."/../ranktest.php 8560 32";
		`$cmd`;
		exit(0);
	}
}

while (1)
{
	sleep(5);
}

function garbage ( $signal ){
	echo "signle $signal received\n";
	while(($pid = pcntl_waitpid(-1, $status, WNOHANG)) >0 )
	{
		echo "\t child end pid $pid , status $status\n";
	}
}

function beep()
{
	echo getmypid(), "\t", date("Y-m-d H:i:s", time()), "\n";
//	sleep(1);
}
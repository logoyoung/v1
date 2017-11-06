<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/8
 * Time: 下午2:01
 */

function getCurEnv($hostname)
{
//	$hostname = gethostname();
//	echo $hostname."\n";
	$rule = [
		"PRO" => "/^HangpengW/",
		'PRE' => "/^huanp-node-[3-4].novalocal/",
		"DEV" => "/^huanp-node-1.novalocal/"
	];

	$cur_env = "PRO";

	foreach ( $rule as $env => $pattern)
	{
		if(preg_match($pattern, $hostname))
		{
			$cur_env = $env;
		}
	}

	return $cur_env;
}


echo getCurEnv("HangpengW28_118")."\n";
echo getCurEnv("HangpengW28_119")."\n";
echo getCurEnv("huanp-node-1.novalocal")."\n";
echo getCurEnv("huanp-node-4.novalocal")."\n";
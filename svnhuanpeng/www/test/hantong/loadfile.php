<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/5
 * Time: 上午11:30
 */



class Test
{
	public function load($data)
	{
		if($data)
		{
			include_once "./webconfig.php";
		}else
		{
			include_once "./iosconfig.php";
		}

		echo $config['title'];
	}
}

$test = new Test();

$test->load(0);
$test->load(1);
echo $config['title'];

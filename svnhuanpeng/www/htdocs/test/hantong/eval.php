<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/15
 * Time: 下午8:49
 */

exit();
$str = '@param/@param';

function compile( $str )
{
	$params = func_get_args();
	array_shift($params);
	if ( preg_match_all( "/@param/", $str, $match ) )
	{
		foreach ($params as $param)
		{
			$str = preg_replace('/@param/',$param,$str,1);
		}

		print_r($str);
		return eval("return ".$str.";;");
	}
}

echo compile($str,16,2);
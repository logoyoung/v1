<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/4/27
 * Time: 18:21
 */

exit();
$handle1 = popen("php testpopen.php php1", 'w');
$handle2 = popen("php testpopen.php phg2", 'w');
pclose($handle1);
pclose($handle2);

print_r(`ps -aux | grep "php testpopen"`);

exit();
//$handle2= popen("php testpopen.php php2", 'r');
//$handle3 =popen("php testpopen.php php3","r");


echo "handle 1 >>>>".gettype($handle1)."\n";
echo "handle 2 >>>>".gettype($handle2)."\n";
echo "handle 3 >>>>".gettype($handle3)."\n";

while(!feof($handle1) || !feof($handle2) || !feof($handle3))
{
	$read = fgets($handle1);
	echo "$read";
	$read = fgets($handle2);
	echo "$read";
	$read = fgets($handle3);
	echo "$read";
}

pclose($handle1);
pclose($handle2);
pclose($handle3);
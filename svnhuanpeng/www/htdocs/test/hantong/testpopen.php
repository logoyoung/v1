<?php
exit();
$arg = $argv[1];

while(true)
{
	file_put_contents(__DIR__.'/test.log',"$arg\n",FILE_APPEND);
	sleep(rand(1,2));
}




if($argc == 1)
{
	echo "argv\n";
}

$arg = $argv[1];

for($i=0;$i<10;$i++)
{
	echo "$i===1===".time()." exec $arg \n";
	if($arg == 'php2')
	{
		sleep(2);
		echo "$i===2===".time()." exec $arg \n ";
		sleep(1);
	}else{
		sleep(1);
	}
}
<?php

/**
 * 供后台改变比率时调用的方法
 * date 2017-02-24 09:45
 * author yandong@6rooms.com
 */
include '../../../../include/init.php';
$fobj=new lib\Finance();
foreach ($_POST as $k=>$v){
	echo $k;
}
//$res=$fobj->setRate();
//echo $res;

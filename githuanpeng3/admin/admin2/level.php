<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/3/21
 * Time: 下午8:29
 */
include '../../include/init.php';

$db = new DBHelperi_huanpeng(true);
exit('404 not found');

$lvl = array();
$x = 0;
for($i=1;$i<=30;$i++){
	if(($i % 5) == 1){
		$x = pow(10, ((int)($i/5)+1));
		$lvl[$i] = $x;
	}else{
		$y = ($i % 5) ? ($i % 5) : 5;
		$lvl[$i] = ($y-1) * 2 * $x;
	}
}

foreach($lvl as $key => $value){
	$str[$key] = "(".$key . "," .$value .")";
}
$lvl = implode(',', $str);
//exit($lvl);
$sql = "insert into anchorlevel (`level`, integral) values ".$lvl."";
if($db->query($sql)){
	echo '+ok';
}
exit;

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="common/css/bootstrap.min.css"/>
		<style>
			.sidebar{
				background-color: #34323e;
				overflow-y:scroll;
				position: fixed;
				top: 0;
				bottom: 0;
				left: -220px;
				width: 220px;
			}
			.content{
				position: fixed;
				overflow-y: scroll;
				top: 0;
				bottom: 0;
				left: 0;
				right: 0;
			}
			.sidebar.push{
				left: 0;
			}
			.content.push{
				left: 220px;
			}
			.sidebar .sidebar-top{
				background: rgba(255,255,255,0.05);
				height: 56px;
				/*margin-bottom: 20px;*/
			}
			.sidebar .sidebar-list {
				margin: 0px;
			}
			.sidebar .sidebar-list li a{
				display: block;
				padding: 12px 10px 12px 17px;
				border-left: 2px solid transparent;
				color: rgba(255,255,255,0.7);
				font-size: 13px;
				font-weight: 600;
				-webkit-transition: all 0.15s linear;
				-o-transition: all 0.15s linear;
				transition: all 0.15s linear;
				text-decoration:none;
			}
			.sidebar .sidebar-list li a.active, .sidebar .sidebar-list li a:active{
				color: #fff;
				background: rgba(0,0,0,0.25);
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="row-fluid">
				<div class="sidebar push">
					<div class="sidebar-top"></div>
					<ul class="sidebar-list">
						<li><a href="" class="active">等级管理</a></li>

					</ul>
				</div>
				<div class="content push">

				</div>
			</div>
		</div>
	</body>
</html>
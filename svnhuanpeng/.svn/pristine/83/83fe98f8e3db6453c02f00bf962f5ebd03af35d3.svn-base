<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/28
 * Time: 15:16
 */
include 'liveData.php';
/****************************直播、录像监控****************************/

?>
<html>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css"
		  integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"
			integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
			crossorigin="anonymous"></script>
</head>
<style>
	.contain{
		width:1300px;
		margin: auto;
	}
	.left-user{
		width: 20%;
		float: left;
		height: 700px;
		overflow: scroll;
	}
	.right-live{
		width: 70%;
		float: right;
	}
	.title{
		width: 100%;
		float: left;
		text-align: center;
	}
	</style>
<body>
<div class="contain">
	<div title="title"><h2>直播监控</h2></div>
	<div class="left-user">
		<table class="table">
			<?php
			echo "<tr>
				<td>用户ID</td>
				<td>用户昵称</td>
				</tr>";
			foreach ( $users as $k=>$user )
			{
				if($_GET['uid']==$user[0]['uid'])
				echo "<tr class='success'>
				<td>{$user[0]['uid']}</td>
				<td>{$user[0]['username']}</td>
				<td><a  href=\"index.php?uid={$user[0]['uid']}&k={$k}\">详情</a></td>
				</tr>";
				else
					echo "<tr>
				<td>{$user[0]['uid']}</td>
				<td>{$user[0]['username']}</td>
				<td><a  href=\"index.php?uid={$user[0]['uid']}&k={$k}\" > 详情</a ></td >
				</tr>";
			}
			?>
		</table>
	</div>
	<div class="right-live">
		<h3 style="text-align: center">直播<?php echo $lives[$_GET['k']]['liveid']; ?>信息</h3>
		<p>直播ID：<?php echo $lives[$_GET['k']]['liveid'];  ?></p>
		<p>直播标题：<?php echo $lives[$_GET['k']]['title'];  ?></p>
		<p>创建时间：<?php echo $lives[$_GET['k']]['ctime'];  ?></p>
		<p><h5>流追踪：<h5></p>
		<p>
		</p>
		<table class="table">
			<tr><td>流名称</td><td>创建时间</td><td>推流时间</td><td>断流时间</td><td>流状态</td><td>流时长（秒）</td></tr>
			<?php
			$liveTime = 0;
			foreach ($streams[$_GET['k']]  as $stream)
			{
				if(strtotime($stream['etime'])>0)
				{
					$time = strtotime( $stream['etime'] ) - strtotime( $stream['stime'] );
					$liveTime += $time;
				}
				elseif($stream['status']==LIVE){
					$time = time() - strtotime( $stream['stime'] );
					$liveTime += $time;
				}
				else{
					$time = '-';
				}
				echo "<tr><td>{$stream['stream']}</td><td>{$stream['ctime']}</td><td>{$stream['stime']}</td><td>{$stream['etime']}</td><td>{$stream['status']}</td><td>{$time}</td></tr>";
			}
			?>
		</table>

	</div>
</div>
</body>
</html>



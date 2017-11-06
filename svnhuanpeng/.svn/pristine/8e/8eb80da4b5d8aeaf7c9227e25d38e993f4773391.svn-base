<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/1/6
 * Time: 14:24
 */
/**
 * 问题反馈页面
 *   */
include ('../include/init.php');

use lib\Live;

$db = new DBHelperi_huanpeng();
// $db->realEscapeString($string)
// $db->affectedRows
$uid = $_COOKIE['_uid'];
$enc = $_COOKIE['_enc'];

$login = 0; // 未登录
$upStatus = 0; // 未提交

if ($uid && $enc) {
	if (CheckUserIsLogIn($uid, $enc, $db) === true)
		$login = 1; // 登录
	else
		$login = 2; // 异常
}//var_dump( isset($_POST['sbt']) );var_dump( $contact );

//echo "<script> var login='{$login}';var upStatus='{$upStatus}'  </script>";
$path = realpath(__DIR__);
?>
<!DOCTYPE html>
<html>
<head>
	<title>欢朋直播-OBS直播</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset='utf-8'>
	<?php include $path.'/tpl/commSource.php';?>
	<link rel="stylesheet" type="text/css" href="./static/css/home_v3.css?v=1.0.4">


	<style>
		body {
			background-color: #f2f2f2;
			color: #333;
		}
	</style>
</head>
<body>

<?php
include ($path.'/head.php');
$ref = urlencode('../activitylive.php');
echo '<script>var head = new head(null,false);</script>';
if($upStatus==10000000){
	echo '<div class="error_page"><div class="pic"><img src="./static/img/src/bg_nodata.png" alt=""></div>'
		.'<span>举报成功</span><a href="index.php" class="sub">返回首页</a></div>';
	include ('./footerSub.php');
	exit;
}
if(!$login){
	echo '<div class="error_page"><div class="pic"><img src="./static/img/src/bg_nodata.png" alt=""></div>'
		.'<span>请先登录</span><a href="./personal/login.php?ref_url='.$ref.'" class="sub">前往登录</a></div>';
	include ($path.'/footer.php');
	exit;
}

$live = Live::getLastLive($uid,$db);
$livedata = [
	'title' => '',
	'gamename' => '',
	'status' => -1,
	'stream' => '',
	'server' => '',
	'liveid' => 0,
];
if(!empty($live)&&$live['status'] <= LIVE)
{
	$livedata['title'] = $live['title'];
	$livedata['gamename'] = $live['gamename'];
	$livedata['status'] = $live['status'];
	$livedata['stream'] = $_COOKIE['streamKey'];
	$livedata['server'] = "rtmp://" . $live['server'];
	$livedata['liveid'] = $live['liveid'];
}


?>
<style>
	#feedback{
		width: 1000px;
		margin: 60px auto;
		padding-top: 30px;
	}
	.fb-head{
		height: 50px;
		margin-bottom: 20px;
		border-bottom: 2px #ddd solid;
	}
	.fb-head h1{
		width: 167px;
		height: 49px;
		font-size: 26px;
		color: #555;
		border-bottom: 2px #ff7800 solid;
		text-align: center;
	}
	.fb-body{
		background: none;
		width: 100%;
	}
	.fb-lable{
		width: 90px;
		display: inline-block;
		font-size: 14px;
		padding: 5px 0px;
		float: left;
	}
	#feedback .textarea, input[type="text"] {
		border: 1px #ccc solid;
		height: 25px;
		line-height: 25px;
		padding: 5px;
		width: 988px;
		margin: 5px 0px;
		outline: 0px;
	}
	#feedback .textarea:hover, input[type="text"]:hover {
		border: 1px solid #ff7800;
	}
	#feedback .textarea{
		width: 988px;
		height: 180px;
		resize: none;
		float: left;
	}
	#feedback .content,#feedback .content{
		margin: 20px 0px;
	}
	#feedback .connect,#feedback .content,#feedback .content .text{
		float: left;
	}
	#feedback .btn-sbt,#feedback .btn-sbt2{
		width: 160px;
		height: 44px;
		-webkit-border-radius: 6px;
		-moz-border-radius: 6px;
		-ms-border-radius: 6px;
		-o-border-radius: 6px;
		border-radius: 6px;
		background: #FF7800;
		text-align: center;
		color: #fff;
		line-height: 32px;
		cursor: pointer;
		height: 44px;
		border-radius: 6px;
		margin-right: 209px;
		outline: none;
		/* list-style-type: none; */
		border-style: none;
	}
	#feedback .btn-sbt2{
		background: #FF4500;
	}
</style>
<div class="contain">
	<div id="feedback">
		<div class="fb-head">
			<h1>OBS直播</h1>
		</div>

		<div class="fb-body">
			<form method="post" action="api/live/liveLaunch.php" onsubmit="return false;">
				<input id = "uid"  name = "uid" value="<?php echo $_COOKIE['_uid']; ?>" type="hidden"/>
				<input id = "encpass" name = "encpass" value="<?php echo $_COOKIE['_enc']; ?>" type="hidden"/>
				<input id = "quality" name = "quality" value="2"  placeholder="直播质量" type="hidden"/>
				<input id = "orientation" name = "orientation" value="1"  placeholder="角度" type="hidden"/>
				<input id = "deviceID" name = "deviceID" value="88888888"  placeholder="设备" type="hidden"/>

				<div class="connect">
					<label class="fb-lable">直播标题：</label>
					<input id = "title" name = "title"  type="text" value="<?php echo $livedata['title']; ?>" placeholder="直播标题">
					<label class="fb-lable">游戏名称：</label>
					<input id = "gameName" name = "gameName" type="text" value="<?php echo $livedata['gamename']; ?>" placeholder="游戏名称">
				</div>


			</form>
		<br/>
			<label class="fb-lable">推流服务器：</label>
			<input id="server" type="text" placeholder="在这里获取推流服务器" value="<?php echo $livedata['server']; ?>" readonly style="background: #ddd;">
			<label class="fb-lable">流名称：</label>
			<input id="stream" type="text" placeholder="在这里获取流名称" value="<?php echo $livedata['stream']; ?>" readonly style="background: #ddd;">
		<br/><br/><br/>
			<?php

				if($livedata['status'] == -1)
					echo "<input id=\"start\" class=\"btn-sbt\" type=\"submit\" value=\"发起直播\" >";
				else
					echo "<input type=\"button\" id=\"stop\" class=\"btn-sbt2\"  value=\"停止直播\">";
			?>
			<!--<input id="start" class="btn-sbt" type="submit" value="发起直播" >
			<input type="button" id="stop" class="btn-sbt2"  value="停止直播">-->
		</div>
	</div>
</div>
<?php include $path.'/footerSub.php';?>
<script>
	/*function checkMobile(mobile){
		var mobile = ( (typeof mobile) == 'undefined' || mobile==null )?'':mobile;
		//if( mobile.length !=  11 )
		var match = /^(13|15|18)\d{9}$|^17(6|7|8)\d{8}$/;
		var emailpat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
		if(match.test(mobile)||emailpat.test(mobile)){
			return true;
		}
		else{
			return false;
		}
	}
	function errfn(data) {
		tips(data.desc);
	}

	function sucfn() {
		var htmlsuc = '<div class="error_page"><div class="pic"><img src="./static/img/src/bg_nodata.png" alt=""></div>'
			+ '<span>反馈成功</span><a href="index.php" class="sub">返回首页</a></div>';
		$('.contain').html(htmlsuc);
	}
	function check() {
		var contact = $('#contact').val();
		if(!checkMobile(contact)){
			tips('联系方式不合法')
			return false;
		}
		var comment = $('#cont').val();
		if(comment.length==0){
			tips('请输入反馈内容')
			return false;
		}
		ajaxRequest({
			url:$conf.api+'other/feedBack.php',
			data:{uid:getCookie('_uid'),encpass:getCookie('_enc'),contact:contact,comment:comment}
		},sucfn,errfn)
	}
	$('.btn-sbt').click(function () {
		check();
	})*/
	var start = function(){
		var uid = $('#uid').val();
		var encpass = $('#encpass').val();
		var quality = $('#quality').val();
		var orientation = $('#orientation').val();
		var deviceID = $('#deviceID').val();
		var title = $('#title').val();
		var gameName = $('#gameName').val();

		$.ajax({
			url:'/api/live/liveLaunch.php',
			type:'post',
			dataType:'json',
			data:{
				uid:uid,
				encpass:encpass,
				quality:quality,
				orientation:orientation,
				deviceID:deviceID,
				title:title,
				gameName:gameName,
			},
			success:function (data) {
				if(data.status == '0')
					tips(data.content.desc);
				else{
					var content = data.content;
					var rtmp = 'rtmp://'+content.liveUploadAddressList[0]+'/'+content.stream;
					//$('#stream').val(rtmp);
					setCookie('streamKey',content.stream);
					tips('已成功发起直播，请在3分钟内将流地址复制到推流器进行推流');
					setTimeout(function () {
						location.href = "";
					},1000);

				}

			}
		})

	}

	var stop = function(){
		var uid = $('#uid').val();
		var encpass = $('#encpass').val();
		var liveID = "<?php echo $livedata['liveid'];  ?>";
		$.ajax({
			url:'/api/live/stopLive.php',
			type:'post',
			dataType:'json',
			data:{
				uid:uid,
				encpass:encpass,
				liveID:liveID,
			},
			success:function (data) {
				if(data.status == '0')
					tips(data.content.desc);
				else{
					tips('直播已经停止');
					setCookie('streamKey',null);
					setTimeout(function () {
						location.href = "";
					},1000);
				}

			}
		})
	}

	$('#stop').click(function () {
		stop();
	})

	$('#start').click(function () {
		start();
	})

</script>
</body>
</html>
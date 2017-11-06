<?php
include_once '/usr/local/huanpeng/include/init.php';
?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<?php include WEBSITE_TPL . 'commSource.php'; ?>
	<title>404你所访问的页面找不到了~</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			border:0;
		}
		html,
		body {
            width: 100%;
			margin:0;
			padding: 0;
			font-family: 微软雅黑;
			background-color: #dddae3;
			overflow-y:hidden;
		}

		html .container-404,
		body .container-404 {
			width: 100%;
            height: 750px;
			min-width: 1200px;
			margin: 49px 0 30px 0;
		}

		html .container-404 .content-404,
		body .container-404 .content-404 {
			width: 100%;
			height: 760px;
			overflow: hidden;
			background: url("<?php echo WEB_ROOT_URL; ?>static/img/404/404.png") top center no-repeat;
            background-size: cover;
			background-color: #f0f0f0;
		}

		html .container-404 .content-404 .show-404,
		body .container-404 .content-404 .show-404 {

			width: 290px;
			height: 137px;
			margin: 230px auto 0;
			padding-left: 30px;
			background-color: transparent;
		}
		html .container-404 .content-404 .show-404 p,
		body .container-404 .content-404 .show-404 p {
			width: 100%;
			font-size: 18px;
			color: #999999;
			text-align: center;
			margin: 20px auto 30px;
			font-family: 微软雅黑;
			font-weight: 500;
		}
		html .container-404 .content-404 .show-404 a,
		body .container-404 .content-404 .show-404 a {
			width: 140px;
			height: 40px;
			line-height: 40px;
			display: block;
			margin: 0 auto;
			font-size: 24px;
			text-align: center;
			background-color: #ffa04c;
			text-decoration: none;
			color: #fff;
			border-radius: 5px;
		}

	</style>
</head>
<body>
<?php include WEBSITE_MAIN.'head.php'; ?>
<script>
	new head(null, false);
</script>
<div class="container-404">
	<div class="content-404">
		<div class="show-404">
			<p>啊哦~您要寻找的页面不存在~</p>
			<a href="<?php echo WEB_ROOT_URL; ?>">返回首页</a>
		</div>
	</div>
</div>
<?php include WEBSITE_MAIN.'footerSub.php'; ?>
</body>
</html>
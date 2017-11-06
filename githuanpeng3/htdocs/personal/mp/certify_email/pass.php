<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/1/5
 * Time: 上午9:55
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>邮箱验证</title>
	<meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
	<meta name="format-detection" content="telephone=no"/>
	<meta content="email=no" name="format-detection" />
<!--	<link rel="stylesheet" href="css/reset.css">-->

	<style>
		html, body, div, span, applet, object, iframe, h1, h2, h3,
		h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address,
		big, cite, code, del, dfn, em, font, img, ins, kbd, q, s, samp,
		small, strike, strong, sub, sup, tt, var, dl, dt, dd, ol, ul, li,
		fieldset, form, label, legend, table, caption, tbody, tfoot,
		thead, tr, th, td {
			margin: 0;
			padding: 0;
			border: 0;
			outline: 0;
			font-weight: inherit;
			font-style: inherit;
			font-size: 100%;
			font-family: inherit;
			vertical-align: baseline;
		}
		:focus {
			outline: 0;
		}
		table {
			border-collapse: separate;
			border-spacing: 0;
		}
		caption, th, td {
			text-align: left;
			font-weight: normal;
		}
		a img, iframe {
			border: none;
		}
		ol, ul {
			list-style: none;
		}
		input, textarea, select, button {
			font-size: 100%;
			font-family: inherit;
		}
		select {
			margin: inherit;
		}
		/* Fixes incorrect placement of numbers in ol’s in IE6/7 */
		ol { margin-left:2em; }
		/* == clearfix == */
		.clearfix:after {
			content: ".";
			display: block;
			height: 0;
			clear: both;
			visibility: hidden;
		}
		.clearfix {display: inline-block;}
		* html .clearfix {height: 1%;}
		.clearfix {display: block;}

		* {
			-webkit-touch-callout:none;
			-webkit-user-select:none;
			-khtml-user-select:none;
			-moz-user-select:none;
			-ms-user-select:none;
			user-select:none;
			-webkit-tap-highlight-color:transparent;
			-webkit-text-size-adjust: 100%;
			-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
			-webkit-overflow-scrolling : touch;
			-webkit-transform: translate3d(0,0,0);
			-moz-transform: translate3d(0,0,0);
			-ms-transform: translate3d(0,0,0);
			transform: translate3d(0,0,0);
		}
		input, textarea{-webkit-appearance: none;}
		html,
		body {
			width: 100%;
			height: 100%;
			font-family: 微软雅黑;
		}
		html .emailVerify-container,
		body .emailVerify-container {
			width: 100%;
			height: 100%;
			background-color: #fff;
			margin-top: -1.5rem;
			padding-top: 5.5rem;
		}
		html .emailVerify-container .verify-title,
		body .emailVerify-container .verify-title {
			width: 55%;
			height: 1.5rem;
			font-weight: 500;
			margin: 1.5rem auto;
			text-align: center;
			letter-spacing: 0.0625rem;
			background: url("right.png") no-repeat;
			background-size: contain;
		}
		html .emailVerify-container .verify-content,
		body .emailVerify-container .verify-content {
			font-size: 0.75rem;
			color: #666;
			width: 90%;
			margin: 0 auto;
			text-align: center;
			line-height: 1.5rem;
		}

	</style>
</head>
<body>

<div class="emailVerify-container">

	<p class="verify-title">
		邮箱验证完成!
	</p>
	<p class="verify-content">
		已成功绑定邮箱 <span class="email-cotent">qcystudio@qq.com</span> , 你可以网站或者手机客户端查看!
	</p>



</div>

</body>
<script>

	/*rem*/
	(function(){

		size();
		window.onresize = function (){

			size();

		};

		function size(){

			var winW = document.documentElement.clientWidth || document.documentElement.body.clientWidth;

			document.documentElement.style.fontSize = winW / 20 +'px';



		}

	})();


</script>
</html>

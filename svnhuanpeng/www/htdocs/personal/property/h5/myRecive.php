<?php
include '../../../../include/init.php';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<meta name="format-detection" content="telephone=no"/>
    	<meta content="email=no" name="format-detection" />
		<title>我的收益</title>
		<link rel="stylesheet" href="css/reset.css?v=1.0.4">
		<link rel="stylesheet" href="css/myRec.css?t=<?php echo time();?>">
		<script src="<?php echo STATIC_JS_PATH; ?>jquery-1.9.1.min.js" type="text/javascript"></script>
		<script src="lib/countUp.js" type="text/javascript"></script>
		<script src="<?php echo STATIC_JS_PATH; ?>common.js?v=1.0.4" type="text/javascript"></script>
		<script src="js/myRecive.js?t=<?php echo time();?>" type="text/javascript"></script>
		<script type="text/javascript">
		/*rem*/
		(function(){
			size();
			window.onresize = function (){
				
				size();
				
			};
			function size(){
				
				var winW = document.documentElement.clientWidth || document.documentElement.body.clientWidth;
				
				document.documentElement.style.fontSize = winW / 22.5 +'px';
				
			}
			
		})();

	</script>
	</head>
	<body>

	<div class="myRecive-container">

		<!--今日收益-->
		<div class="myRecive-todayContent">

			<div class="todayEarning">
				<p>收益余额</p>
			</div>

			<div class="earningBlock">

				<div class="goldEarning">

					<p class="goldValue" id="goldAll">0</p>
					<p class="earning">金币收益</p>

				</div>

				<div class="orangeAdd">
					<p class="add">+</p>
				</div>

				<div class="beanEarning">

					<p class="beanValue" id="beanAll">0</p>
					<p class="earning">金豆收益</p>

				</div>

			</div>

		</div>

		<!--余额区-->
		<div class="myRecive-allMoney">

			<div class="moneyTitle">
				<p>本月收益</p>
			</div>

			<div class="moneyShow">

				<div class="moneyBox">

					<div class="moneyBalance">

						<p class="goldDesc">金币</p>
						<p class="moenyCount" id="moneyCount">0</p>
					</div>

					<div class="beanBalance">

						<p class="beanDesc">金豆</p>
						<p class="moenyCount" id="beanCount">0</p>

					</div>

				</div>

<!--				<p>活动期间平台奖励<span class="awardGold">20%</span>金币收益</p>-->
                <p>平台奖励<span class="awardGold">20%</span>的金币，已加入每笔收益</p>
			</div>

		</div>

		<!--操作区-->
		<div class="myRecive-operationLoc">

			<div class="change-box">
                <button id="toHPCoin" class="exchange">兑换欢朋币</button>
                <button id="toCoin" class="exchange">兑换金币</button>
            </div>
			<button id="withdraw" class="withdraw">提现</button>

		</div>

		<!--提示区-->
		<div class="myRecive-tips">

			<p class="tip-title">提示:</p>
			<p>
                金豆可以兑换为金币用于提现，兑换比例： 1 金豆 = 1 金币；
			</p>
			<p>
                金币可以兑换为欢朋币，兑换比例： 1 金币 = 10 欢朋币；
			</p>
            <p>
                金币可在每月固定日期（25日~月底）可提现1次，本月没兑换可累积到下月。
            </p>

		</div>

	</div>

	<div class="modal-box">
        <div class="modal-loading">

            <div class="icon_loading"></div>
        </div>
		<div class="error-modal">
			<img src="img/icon_fail.png">
			<p id="error-content"></p>
		</div>
	</div>

	</body>

</html>

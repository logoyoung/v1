<div class="page-head" style="text-align: center">
	<?php include 'h5-header.php';?>
	<div class="cert-step active">
		<div class="step active">
			<span><span>1</span></span>
			<p>验证手机</p>
		</div>
		<div class="horizontal-line" style="width: <?php if( !RN_MODEL ) echo $isMobile ?  '280px' : '690px'; ?>"></div>
		<div class="step">
			<span><span>2</span></span>
			<p>身份认证</p>
		</div>
		<div class="horizontal-line"></div>
		<div class="step">
			<span><span>3</span></span>
			<p>审核</p>
		</div>
	</div>
</div>
<div class="page-body check-phone" style="">

	<div class="form-horizontal">
		<div class="white-block">
			<div class="control-group phone-number label-hide">
				<div class="control-label">手机号</div>
				<div class="controls">
					<span class="icon"></span>
					<span class="number"><?php echo $phoneNumber;?></span>
					<a href="javascript:;" class="btn" id="get-mobile-code">获取验证码</a>
				</div>
				<div class="clear"></div>
			</div>
			<div class="control-group pass-code label-hide">
				<div class="control-label">验证码</div>
				<div class="controls">
					<span class="icon"></span>
					<input id="pass-code" type="text" placeholder="请输入验证码">
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="control-group agree-rule label-hide">
			<div class="controls">
				<div class="check-box-block">
					<label class="check-box"></label>
					<span>我已阅读并同意<a target="<?php if(!$isMobile) echo '_blank'; ?>" style="font-size: <?php if($isMobile) echo '26px'; ?>" href="<?php if($isMobile)echo WEB_ROOT_URL.'protocol/anchorLiveProtocol.html'; else echo WEB_ROOT_URL.'protocol/protocolAnchor.php'; ?>" class="protocol">《欢朋直播主播协议》</a></span>
					<div class="clear"></div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="control-group commit">
			<div class="controls">
				<a href="javascript:;" class="btn disabled">下一步</a>
			</div>
		</div>
	</div>
</div>

<!--<input type="checkbox" id="agree-check-box">-->
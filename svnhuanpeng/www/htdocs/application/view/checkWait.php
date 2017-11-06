<div class="page-head" style="text-align: center">
	<?php include 'h5-header.php';?>
	<div class="cert-step active">
		<div class="step active">
			<span><span>1</span></span>
			<p>验证手机</p>
		</div>
		<div class="horizontal-line active"></div>
		<div class="step active">
			<span><span>2</span></span>
			<p>身份认证</p>
		</div>
		<div class="horizontal-line active"></div>
		<div class="step active">
			<span><span>3</span></span>
			<p>审核</p>
		</div>
	</div>
</div>
<div class="page-body">
	<div class="block-insert <?php if(!$isMobile) echo 'horizontal'; ?>">
		<div class="logo"><img src="image/check-wait-logo.png" alt=""></div>
		<p class="notice">主播认证已经提交，请耐心等待</p>
	</div>
	<?php if(!$isMobile){?>
	<div class="commit">
		<a href="<?php echo WEB_ROOT_URL ;?>index.php" class="btn">返回首页</a>
	</div>
	<?php }?>
</div>

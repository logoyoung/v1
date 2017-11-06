<div class="page-head">
	<?php include 'h5-header.php'?>
</div>
<div class="page-body">
	<?php if($currentPage == 'start' || $currentPage == "unCertPhone"){?>
		<div class="block-insert commit" style="text-align: center; margin-top: 50px;">
			<div class="logo" style="width: 300px;height: 300px;"><img src="<?php echo WEB_ROOT_URL; ?>application/image/check-start-logo.png" alt=""></div>
			<p class="logo-title">认证欢朋主播，起航明星之旅</p>
<!--			<div class="invite-content">-->
<!--				<div class="control-label">邀请码</div>-->
<!--				<div class="controls">-->
<!--					<input id="i_code" type="text">-->
<!--				</div>-->
<!--				<div class="clear"></div>-->
<!--			</div>-->

			<a id="vertify_anchor" href="javascript:;" class="btn">开始认证</a>
<!--			--><?php //echo WEB_ROOT_URL; ?><!--application/index.php?page=phone&time=--><?php //echo time();?>

			<script type="application/javascript">
				$('#vertify_anchor').click(function(){
					var iCode = $("#i_code").val();
					if(iCode){
						location.href = "<?php echo WEB_ROOT_URL?>"+'application/index.php?page=phone&time=<?php echo time(); ?>';
//						var requestUrl = $conf.api + 'user/invite/inviteTest.php';
//						var requestData = {
//							uid:getCookie('_uid'),
//							encpass:getCookie('_enc'),
//							icode:iCode
//						};
//						ajaxRequest({url:requestUrl,data:requestData},function(responseDtat){
//							location.href = "<?php //echo WEB_ROOT_URL?>//"+'application/index.php?page=phone&time=<?php //echo time(); ?>//';
//						},function(responseData){
//							if(responseData.type == 2)
//								alertMsg(responseData.desc);
//							else
//								alertMsg('邀请码错误');
//							return;
//						});
					}else{
						location.href = "<?php echo WEB_ROOT_URL?>"+'application/index.php?page=phone&time=<?php echo time(); ?>';
					}
				});
			</script>
		</div>
	<?php }elseif($currentPage == 'failed'){ ?>
		<div class="block-insert commit" style="text-align: center">
			<div class="logo"><img src="<?php echo WEB_ROOT_URL; ?>application/image/check-failed-logo.png" alt=""></div>
			<p class="logo-title">您的审核失败，请重新认证</p>
			<a id="vertify_anchor" href="<?php echo WEB_ROOT_URL?>application/index.php?page=phone&time=<?php echo time(); ?>" class="btn">开始认证</a>
<!--			should check if the user has set invite code or update user invite icode by api-->
			<script type="application/javascript">
//				$('#vertify_anchor').click(function(){
//					var iCode = $("#i_code").val();
//					if(iCode){
//						var requestUrl = $conf.api + 'user/invite/inviteTest.php';
//						var requestData = {
//							uid:getCookie('_uid'),
//							encpass:getCookie('_enc'),
//							icode:iCode
//						};
//						ajaxRequest({url:requestUrl,data:requestData},function(responseDtat){
//							location.href = "<?php //echo WEB_ROOT_URL?>//"+'application/index.php?page=phone&time=<?php //echo time(); ?>//';
//						},function(responseData){
//							alert(responseData.desc);
//							return;
//						});
//					}else{
//						location.href = "<?php //echo WEB_ROOT_URL?>//application/index.php?page=phone&time=<?php //echo time(); ?>//";
//					}
//				});
			</script>
		</div>
	<?php }elseif($currentPage == 'finish'){
		$href = $isMobile ? WEB_PERSONAL_URL.'homepage/index.php' : 'javascript:;'; ?>
		<div class="block-insert commit">
			<div class="logo"><img src="<?php echo WEB_ROOT_URL; ?>application/image/check-succ-logo.png" alt=""></div>
			<p>审核已通过</p>

			<a href="<?php echo $href; ?>" class="btn">进入我的主页</a>
		</div>
	<?php }elseif( !$isMobile && $currentPage == 'checkWait'){?>
		<div class="block-insert <?php //if(!$isMobile) echo 'horizontal'; ?>">
			<div class="logo"><img src="<?php echo WEB_ROOT_URL; ?>application/image/check-wait-logo.png" alt=""></div>
			<p class="notice">主播认证已经提交，请耐心等待</p>
		</div>
		<?php if(!$isMobile){?>
			<div class="commit">
				<a href="<?php echo WEB_ROOT_URL; ?>index.php" class="btn">返回首页</a>
			</div>
		<?php }?>
	<?php }elseif($currentPage == 'unCertPhone'){ ?>

	<?php } ?>
</div>
<div class="page-foot">
	<div class="show-block"><span class="item-1"></span><p>游戏炫技,展现才华</p></div>
	<div class="show-block"><span class="item-2"></span><p>成名机会,万千粉丝</p></div>
	<div class="show-block"><span class="item-3"></span><p>额外收入,月入百万</p></div>
</div>

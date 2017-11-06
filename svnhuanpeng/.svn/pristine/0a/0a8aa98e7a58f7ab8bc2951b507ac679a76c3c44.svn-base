<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title>更改密码</title> 
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<?php $this->load->view('adminuser/include/static'); ?>
</head>
<body>
<?php $this->load->view('adminuser/include/header'); ?>
<!-- Main content starts -->
<div class="content">
	<?php $this->load->view('adminuser/include/sidebar', array('nav' => 4)); ?>
  	<!-- Main bar -->
  	<div class="mainbar">
        <!-- Page heading -->
	    <div class="page-head">
			<h2 class="pull-left">更改密码<span class="page-meta"></span></h2>
			<div class="clearfix"></div>
	    </div>
	    <!-- Page heading ends -->
	    <!-- Matter -->
	    <div class="matter">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="widget wgreen">
							<div class="widget-content">
								<div class="padd">
								<!-- Form starts.  -->
									<form class="form-horizontal" role="form" action="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=modify" method="post">
										<div class="form-group">
											<label class="col-lg-4 control-label">昵称</label>
											<div class="col-lg-8">
												<input type="text" class="form-control" name="nickname" id="nickname" value="<?php echo $_COOKIE['admin_name']; ?>"/>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-4 control-label">当前密码</label>
											<div class="col-lg-8">
												<input type="password" class="form-control" name="old_password" id="old_password" placeholder="请输入当前密码"/>
											</div>
										</div>  
										<div class="form-group">
											<label class="col-lg-4 control-label">新密码</label>
											<div class="col-lg-8">
												<input type="password" class="form-control" name="new_password" id="new_password" placeholder="请输入6-12位的新密码"/>
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="col-lg-4 control-label">确认新密码</label>
                                            <div class="col-lg-8">
                                                <input type="password" class="form-control" name="new_password2" id="new_password2" placeholder="请确认新密码"/>
                                            </div>
                                        </div>
										<div class="form-group">
											<div class="col-lg-offset-1 col-lg-9">
												<input type="submit" class="btn btn-success" value="提交" />
                                                <label style="color:red" id="error_info"><?php echo isset($error) ? $error : ''; ?></label>
											</div>
										</div>						
									</form>
								</div>
							</div>
						</div>  
					</div>
				</div>
			</div>
		</div>
		<!-- Matter ends -->
    </div>
   <!-- Mainbar ends -->	    	
   <div class="clearfix"></div>
</div>
<!-- Content ends -->
<?php $this->load->view('adminuser/include/footer'); ?>
<script>
	$("form").submit(function(e){
		if($("#nickname").val() == '') {
			$("#error_info").text("昵称不能为空");
			return false;
		}
		if($("#old_password").val() == '') {
			$("#error_info").text("原密码不能为空");
			return false;
		}
		var newpass = $("#new_password").val();
		if(newpass.length < 6 || newpass.length > 12) {
			$("#error_info").text("密码长度需要在6-12位之间");
			return false;
		}
		if(newpass != $("#new_password2").val()) {
			$("#error_info").text("两次密码输入不一致");
			return false;
		}
		return true;
	});
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title>添加/更新用户</title> 
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<?php $this->load->view('adminuser/include/static'); ?>
</head>
<body>
<?php $this->load->view('adminuser/include/header'); ?>
<!-- Main content starts -->
<div class="content">
	<?php $this->load->view('adminuser/include/sidebar', array('nav' => 1)); ?>
  	<!-- Main bar -->
  	<div class="mainbar">
        <!-- Page heading -->
	    <div class="page-head">
			<h2 class="pull-left">添加/更新用户<span class="page-meta"></span></h2>
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
									<form class="form-horizontal" role="form" action="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=user&m=edit" method="post">
										<div class="form-group">
											<label class="col-lg-4 control-label">邮箱</label>
											<div class="col-lg-8">
												<input type="text" class="form-control" name="email" value="<?php echo isset($user['email']) ? $user['email'] : '' ?>">
											</div>
										</div>
                              
										<div class="form-group">
											<label class="col-lg-4 control-label">真实姓名</label>
											<div class="col-lg-8">
												<input type="text" class="form-control" name="real_name" value="<?php echo isset($user['real_name']) ? $user['real_name'] : '' ?>">
											</div>
										</div>  
										<div class="form-group">
											<label class="col-lg-4 control-label">昵称</label>
											<div class="col-lg-8">
												<input type="text" class="form-control" name="nickname" value="<?php echo isset($user['nickname']) ? $user['nickname'] : '' ?>">
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="col-lg-4 control-label">手机号</label>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control" name="mobile" value="<?php echo isset($user['mobile']) ? $user['mobile'] : '' ?>">
                                            </div>
                                        </div>
										<div class="form-group">
											<label class="col-lg-4 control-label">密码(6-12位)</label>
											<div class="col-lg-8">
												<input type="text" class="form-control" name="password" value="">
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-4 control-label">帐号类型</label>
											<div class="col-lg-8">
												<select class="form-control" name="type" id="user_type">
													<?php if($config['type']) { ?>
													<?php foreach($config['type'] as $k=>$v) { ?>
													<option value="<?php echo $k; ?>" <?php if(isset($user['type']) && $user['type'] == $k) {?>selected<?php } ?>>
														<?php echo $v; ?>
													</option>
													<?php } ?>						
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group" id="company" style="<?php if(!isset($user['type']) || $user['type'] != 2) {?>display:none<?php } ?>">
											<label class="col-lg-4 control-label">经纪公司</label>
											<div class="col-lg-8">
												<select class="form-control" name="company_id">
													<?php if($company_list) { ?>
													<?php foreach($company_list as $k=>$v) { ?>
													<option value="<?php echo $v['id']; ?>" <?php if(isset($user['company_id']) && $user['company_id'] == $v['id']) {?>selected<?php } ?>>
														<?php echo $v['name']; ?>
													</option>
													<?php } ?>						
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-lg-4 control-label">所属组</label>
											<div class="col-lg-8">
												<select class="form-control" name="role">
													<?php if($role_list) { ?>
													<?php foreach($role_list as $k=>$v) { ?>
													<option value="<?php echo $v['id']; ?>" <?php if(isset($user['role']) && $user['role'] == $v['id']) {?>selected<?php } ?>>
														<?php echo $v['des']; ?>
													</option>
													<?php } ?>						
													<?php } ?>
												</select>
											</div>
										</div>
										<input type="hidden" name="id" value="<?php echo isset($user['id']) ? $user['id'] : 0 ?>">
										<div class="form-group">
											<div class="col-lg-offset-1 col-lg-9">
												<input type="submit" class="btn btn-success" value="提交" />
                                                <label style="color:red"><?php echo isset($error) ? $error : '' ?></label>
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
$("#user_type").change(function(){
	if($(this).val() == 2) {
		$("#company").show();
	} else {
		$("#company").hide();
	}
});
</script>
</body>
</html>
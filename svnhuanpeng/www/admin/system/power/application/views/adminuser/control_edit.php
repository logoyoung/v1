<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title>添加/更新权限</title> 
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<?php $this->load->view('adminuser/include/static'); ?>
</head>
<body>
<?php $this->load->view('adminuser/include/header'); ?>
<!-- Main content starts -->
<div class="content">
	<?php $this->load->view('adminuser/include/sidebar', array('nav' => 3)); ?>
  	<!-- Main bar -->
  	<div class="mainbar">
        <!-- Page heading -->
	    <div class="page-head">
			<h2 class="pull-left">添加/更新权限<span class="page-meta"></span></h2>
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
									<form class="form-horizontal" role="form" action="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=control&m=edit" method="post">
										<div class="form-group">
											<label class="col-lg-4 control-label">名称</label>
											<div class="col-lg-8">
												<input type="text" class="form-control" name="name" value="<?php echo isset($control['name']) ? $control['name'] : '' ?>">
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="col-lg-4 control-label">Url</label>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control" name="url" value="<?php echo isset($control['url']) ? $control['url'] : '' ?>">
                                            </div>
                                        </div>
										<div class="form-group">
											<label class="col-lg-4 control-label">所属项目</label>
											<div class="col-lg-8">
												<select class="form-control" name="parent_id">
													<option value="0">如果是添加项目就不选择</option>
													<?php if($parent_list) { ?>
													<?php foreach($parent_list as $pk=>$pv) { ?>
													<?php if(!isset($control['id']) || $control['id'] != $pv['id']) { ?>
													<option value="<?php echo $pv['id']; ?>" <?php if(isset($control['parent_id']) && $control['parent_id'] == $pv['id']) {?>selected<?php } ?>>
														<?php echo $pv['name']; ?>
													</option>
													<?php } ?>
													<?php } ?>						
													<?php } ?>
												</select>
											</div>
										</div>   
										<input type="hidden" name="id" value="<?php echo isset($control['id']) ? $control['id'] : 0 ?>">
										<div class="form-group">
											<div class="col-lg-offset-1 col-lg-9">
												<input type="submit" class="btn btn-success" value="提交" />											
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
</body>
</html>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title>添加/更新组</title> 
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<?php $this->load->view('adminuser/include/static'); ?>
</head>
<body>
<?php $this->load->view('adminuser/include/header'); ?>
<!-- Main content starts -->
<div class="content">
	<?php $this->load->view('adminuser/include/sidebar', array('nav' => 2)); ?>
  	<!-- Main bar -->
  	<div class="mainbar">
        <!-- Page heading -->
	    <div class="page-head">
			<h2 class="pull-left">添加/更新组<span class="page-meta"></span></h2>
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
									<form class="form-horizontal" role="form" action="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=role&m=edit" method="post">
										<div class="form-group">
											<label class="col-lg-4 control-label">组名称</label>
											<div class="col-lg-8">
												<input type="text" class="form-control" name="des" value="<?php echo isset($role['des']) ? $role['des'] : '' ?>">
											</div>
										</div>
                              
										<div class="form-group">
											<label class="col-lg-4 control-label">拥有权限</label>
											<div class="col-lg-8">
												<?php if($parent_list) { ?>
												<?php foreach($parent_list as $pk=>$pv) { ?>
												
												<label class="checkbox-inline">
													<input type="checkbox" id="" value="<?php echo $pv['id']; ?>" <?php if(!empty($role['control']) && in_array($pv['id'], $role['control'])){ ?>checked<?php } ?> name="control[]"><b><?php echo $pv['name']; ?></b>
												</label>
												
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
												<!--
												<hr/>
												<label class="checkbox-inline">
												  <input type="checkbox" id="" class="selectall" value="<?php echo $pv['id']; ?>">全选
												</label><br/>
												-->
													<?php if($child_list) { ?>
													<?php foreach($child_list as $ck=>$cv) { ?>
													<?php if($cv['parent_id'] == $pv['id']) { ?>
													<label class="checkbox-inline">
													<input type="checkbox" id="" value="<?php echo $cv['id']; ?>" <?php if(!empty($role['control']) && in_array($cv['id'], $role['control'])){ ?>checked<?php } ?> name="control[]"><?php echo $cv['name']; ?>(<?php echo $cv['url']; ?>)
													</label>
													<?php } ?>						
													<?php } ?>
													<?php } ?>
													<hr/>
												<?php } ?>						
												<?php } ?>
											</div>
										</div>   
										<input type="hidden" name="id" value="<?php echo isset($role['id']) ? $role['id'] : 0 ?>">
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
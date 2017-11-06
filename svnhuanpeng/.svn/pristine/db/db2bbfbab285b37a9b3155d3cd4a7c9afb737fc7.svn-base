<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<!-- Title and other stuffs -->
	<title>用户列表</title> 
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
        <h2 class="pull-left">
			<i class="icon-table"></i> 
			用户列表
			<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=user&m=edit"class="btn btn-success">添加用户</a>
		</h2>
        <div class="clearfix"></div>
      </div>
      <!-- Page heading ends -->

	    <!-- Matter -->
	    <div class="matter">
        <div class="container">
          <!-- Table -->
            <div class="row">
              <div class="col-md-12">
                <div class="widget">
					<div class="widget-head">
						<div class="pull-left">共有（<?php echo $total; ?>）条结果</div>
						<br/>
						<form class="navbar-form navbar-left" action="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=user" method="post">
							<div class="form-group">
								<input type="text" class="form-control" name="id" placeholder="ID">
							</div>
							<div class="form-group">
								<input type="text" class="form-control" name="email" placeholder="邮箱">
							</div>
							<div class="form-group">
								<input type="text" class="form-control" name="nickname" placeholder="昵称">
							</div>
							<div class="form-group">
								<input type="text" class="form-control" name="real_name" placeholder="真实姓名">
							</div>
							<div class="form-group">
								<select class="form-control" name="role">
									<option value="0">所属组</option>
									<?php if($role) { ?>
									<?php foreach($role as $k=>$v) { ?>
									<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
									<?php } ?>						
									<?php } ?>
								</select>
							</div>
						 <input type="submit" class="btn btn-success" value="搜索" />
						</form>
						<div class="clearfix"></div>
					</div>
                  <div class="widget-content">
                    <table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>用户ID</th>
								<th>邮箱</th>
								<th>手机号</th>
								<th>真实姓名</th>
								<th>昵称</th>
								<th>所属组</th>
								<th>操作</th>
							</tr>
						</thead>
						
                      <tbody>
						<?php if($user_list) { ?>
						<?php foreach($user_list as $k=>$v) { ?>
                        <tr>
                          <td><?php echo $v['id']; ?></td>
                          <td><?php echo $v['email']; ?></td>
						  <td><?php echo $v['mobile']; ?></td>
						  <td><?php echo $v['real_name']; ?></td>
						  <td><?php echo $v['nickname']; ?></td>
						  <td><?php echo $role[$v['role']]; ?></td>
                          <td>
							<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=user&m=edit&id=<?php echo $v['id']; ?>" class="btn btn-xs btn-warning" title="修改"><i class="icon-pencil"></i></a>
							<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=user&m=del&id=<?php echo $v['id']; ?>" class="btn btn-xs btn-danger" title="删除"><i class="icon-remove"></i></a>
                          </td>
                        </tr> 
						<?php } ?>						
						<?php } ?>	
                      </tbody>
                    </table>
					<?php if($page) { ?>
                    <div class="widget-foot">
						<?php echo $page; ?>
						<div class="clearfix"></div> 
                    </div>
					<?php } ?>
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
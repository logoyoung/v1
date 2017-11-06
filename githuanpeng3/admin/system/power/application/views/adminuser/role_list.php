<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<!-- Title and other stuffs -->
	<title>用户组列表</title> 
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
        <h2 class="pull-left">
			<i class="icon-table"></i> 
			用户组列表
			<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=role&m=edit"class="btn btn-success">添加组</a>
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
                  <div class="widget-content">
                    <table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>组名称</th>
								<th>添加时间</th>
								<th>操作</th>
							</tr>
						</thead>
						
                      <tbody>
						<?php if($role_list) { ?>
						<?php foreach($role_list as $k=>$v) { ?>
                        <tr>
                          <td><?php echo $v['des']; ?></td>
                          <td><?php echo $v['ctime']; ?></td>
                          <td>
								<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=role&m=edit&id=<?php echo $v['id']; ?>" class="btn btn-xs btn-warning" title="修改"><i class="icon-pencil"></i></a>
								<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=role&m=del&id=<?php echo $v['id']; ?>" class="btn btn-xs btn-danger" title="删除"><i class="icon-remove"></i></a>
                          </td>
                        </tr> 
						<?php } ?>						
						<?php } ?>	
                      </tbody>
                    </table>
					<!--
                    <div class="widget-foot">
                        <ul class="pagination pull-right">
                          <li><a href="#">Prev</a></li>
                          <li><a href="#">1</a></li>
                          <li><a href="#">2</a></li>
                          <li><a href="#">3</a></li>
                          <li><a href="#">4</a></li>
                          <li><a href="#">Next</a></li>
                        </ul>
                      <div class="clearfix"></div> 
                    </div>
					-->

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
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<!-- Title and other stuffs -->
	<title>权限列表</title> 
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
        <h2 class="pull-left">
			<i class="icon-table"></i> 
			权限表
			<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=control&m=edit"class="btn btn-success">添加权限</a>
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
								<th>项目名称</th>
								<th>ID</th>
								<th>权限名称</th>
                                <th>URL</th>
								<th>添加时间</th>
								<th>操作</th>
							</tr>
						</thead>
						
                      <tbody>
						<?php if($parent_list) { ?>
						<?php foreach($parent_list as $pk=>$pv) { ?>
                        <tr>
                          <td><?php echo $pv['name']; ?></td>
                          <td><?php echo $pv['id']; ?></td>
                          <td></td>
                          <td></td>
                          <td><?php echo $pv['ctime']; ?></td>
                          <td>
							<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=control&m=edit&id=<?php echo $pv['id']; ?>" class="btn btn-xs btn-warning" title="修改"><i class="icon-pencil"></i></a>
							<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=control&m=del&id=<?php echo $pv['id']; ?>" class="btn btn-xs btn-danger" title="删除"><i class="icon-remove"></i></a>
                          </td>
                        </tr> 
							<?php if($child_list) { ?>
							<?php foreach($child_list as $ck=>$cv) { ?>
							<?php if($cv['parent_id'] == $pv['id']) { ?>
							<tr>
							  <td></td>
							  <td><?php echo $cv['id']; ?></td>
							  <td><?php echo $cv['name']; ?></td>
                              <td><?php echo $cv['url']; ?></td>
							  <td><?php echo $cv['ctime']; ?></td>
							  <td>
								<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=control&m=edit&id=<?php echo $cv['id']; ?>" class="btn btn-xs btn-warning" title="修改"><i class="icon-pencil"></i></a>
								<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=control&m=del&id=<?php echo $cv['id']; ?>" class="btn btn-xs btn-danger" title="删除"><i class="icon-remove"></i></a>
							  </td>
							</tr> 
							<?php } ?>
							<?php } ?>						
							<?php } ?>
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
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<!-- Title and other stuffs -->
	<title>欢朋直播后台管理-<?php echo $company['name']; ?></title> 
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<?php $this->load->view('company/include/static'); ?>
</head>

<body>
<?php $this->load->view('company/include/header'); ?>
<!-- Main content starts -->
<div class="content">
	<?php $this->load->view('company/include/sidebar', array('nav' => 1)); ?>
  	<!-- Main bar -->
  	<div class="mainbar">

      <!-- Page heading -->
      <div class="page-head">
        <h2 class="pull-left">
			<i class="icon-table"></i>主播列表--<?php echo $company['name']; ?>
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
						<div class="pull-left">共有（<?php echo $total; ?>）位主播 
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;选择时间：
							<select style="pading-left:20px" id="month">
							<?php foreach($pass_month as $k=>$v) { ?>
								<option value="<?php echo $v; ?>" <?php if($date==$v){?>selected<?php }?>><?php echo $v; ?></option>
							<?php } ?>
							</select>
						</div>
						<div class="clearfix"></div>
					</div>
                  <div class="widget-content">
                    <table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>头像</th>
								<th>昵称</th>
								<th>UID</th>
								<!--
								<th>金币收入
									<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=company&c=anchor&orderby=totalmoney_1" <?php if($orderby == 'totalmoney_1' || $orderby == ''){ ?>class="rank" <?php } ?> title="按金币收入降序">↓</a>
									<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=company&c=anchor&orderby=totalmoney_2" <?php if($orderby == 'totalmoney_2'){ ?>class="rank" <?php } ?> title="按金币收入正序">↑</a>
								</th>
								<th>金豆收入
									<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=company&c=anchor&orderby=totalbeans_1" <?php if($orderby == 'totalbeans_1'){ ?>class="rank" <?php } ?> title="按金豆收入降序">↓</a>
									<a href="<?php echo $this->config->config['adminuser_url']; ?>?d=company&c=anchor&orderby=totalbeans_2" <?php if($orderby == 'totalbeans_2'){ ?>class="rank" <?php } ?> title="按金豆收入正序">↑</a>
								</th>
								-->
								<th>直播总时长</th>
								<th>人气峰值</th>
								<th>直播间</th>
								<th>详情</th>
							</tr>
						</thead>
						
                      <tbody>
						<?php if($anchor_list) { ?>
						<?php foreach($anchor_list as $k=>$v) { ?>
                        <tr>
                          <td><img style="width:100px; height: 100px;"src="<?php echo $v['pic']; ?>" /></td>
                          <td><?php echo $v['nick']; ?></td>
						  <td><?php echo $v['uid']; ?></td>
						  <!--
						  <td><?php echo $v['money']; ?></td>
						  <td><?php echo $v['beans']; ?></td>
						  -->
						  <td><?php echo $v['length']; ?></td>
						  <td><?php echo $v['popular']; ?></td>
						  <td><?php echo $v['roomid']; ?></td>
                          <td><a href="<?php echo $url2; ?>&uid=<?php echo $v['uid']; ?>"><span class="label label-success">查看详情</span></a></td>
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
<?php $this->load->view('company/include/footer'); ?>
<script>
	$("#month").change(function(){
		window.location = '<?php echo $url; ?>' + "&date=" + $(this).val();
	})
</script>
</body>
</html>
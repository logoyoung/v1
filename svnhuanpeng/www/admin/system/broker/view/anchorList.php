<?php
	//ini_set('display_errors', 'On');
	//error_reporting(E_ALL);
	require('../config/config.php');
	require_once(INCLUDE_PATH . 'commonFunction.php');
	require_once(INCLUDE_PATH . 'Pagination.class.php');
	require('../api/Anchor.class.php');

	$anchor = new Anchor(null, 65);
	$data = $anchor->anchorList();

	$pagination = new Pagination($data['count']);
	$data['page'] = $pagination->page_html();
	$data['pass_month'] = getPastMonth();
	extract($data);
	$domain = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<!-- Title and other stuffs -->
	<title>欢朋直播后台管理-<?php echo $company['name']; ?></title> 
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<?php include('include/static.php'); ?>
</head>

<body>
<?php include('include/header.php'); ?>
<!-- Main content starts -->
<div class="content">
	<?php include('include/sidebar.php'); ?>
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
						<div class="pull-left">共有（<?php echo $count; ?>）位主播 
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
									<a href="<?php echo $domain; ?>?d=company&c=anchor&orderby=totalmoney_1" <?php if($orderby == 'totalmoney_1' || $orderby == ''){ ?>class="rank" <?php } ?> title="按金币收入降序">↓</a>
									<a href="<?php echo $domain; ?>?d=company&c=anchor&orderby=totalmoney_2" <?php if($orderby == 'totalmoney_2'){ ?>class="rank" <?php } ?> title="按金币收入正序">↑</a>
								</th>
								<th>金豆收入
									<a href="<?php echo $domain; ?>?d=company&c=anchor&orderby=totalbeans_1" <?php if($orderby == 'totalbeans_1'){ ?>class="rank" <?php } ?> title="按金豆收入降序">↓</a>
									<a href="<?php echo $domain; ?>?d=company&c=anchor&orderby=totalbeans_2" <?php if($orderby == 'totalbeans_2'){ ?>class="rank" <?php } ?> title="按金豆收入正序">↑</a>
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
                          <td><a href="<?php echo $domain . 'system/broker/view/anchorDetail.php?date=' . $date . '&uid=' . $v['uid']; ?>"><span class="label label-success">查看详情</span></a></td>
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
<?php include('include/footer.php'); ?>
<script>
	$("#month").change(function(){
		window.location = "<?php echo $domain . 'system/broker/view/anchorList.php?' . filterArg('date'); ?>"
					+ "date=" + $(this).val();
	})
</script>
</body>
</html>
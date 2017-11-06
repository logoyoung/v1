<?php
	//ini_set('display_errors', 'On');
	//error_reporting(E_ALL);
	require('../config/config.php');
	require_once(INCLUDE_PATH . 'commonFunction.php');
	require('../api/Anchor.class.php');

	$anchor = new Anchor(null, 65);
	$data = $anchor->anchorDetail();

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
        	<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						主播昵称：<?php echo $userinfo['nick']; ?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						UID：<?php echo $userinfo['uid']; ?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<select style="pading-left:20px" id="month">
						<?php foreach($pass_month as $k=>$v) { ?>
							<option value="<?php echo $v; ?>" <?php if($selected_date==$v){?>selected<?php }?>><?php echo $v; ?></option>
						<?php } ?>
						</select>
					</tr>
				</thead>
			</table	>
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
                <!--
                  <div class="widget-content">
					<div class="widget-head">
						<div class="pull-left">直播收益</div>
						<div class="clearfix"></div>
					</div>
					<div class="table-responsive" id="icon">
                
            		</div>
                  </div>
                -->
                  <div class="widget-content">
					<div class="widget-head">
						<div class="pull-left">直播时长共计<?php echo $total_length; ?></div>
						<div class="clearfix"></div>
					</div>
					<div class="table-responsive" id="length">
                
            		</div>
                  </div>
                  <div class="widget-content">
					<div class="widget-head">
						<div class="pull-left">直播人气峰值<?php echo $top_popular; ?></div>
						<div class="clearfix"></div>
					</div>
					<div class="table-responsive" id="popular">
                
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
<?php include('include/footer.php'); ?>
<script>
	$(function () {
		$('#popular').highcharts({
			chart: {
				type: 'line'
			},
			title: {
				text: '直播人气曲线图'
			},
			subtitle: {
				text: '<?php echo $userinfo['nick']; ?>'
			},
			xAxis: {
				categories: [<?php echo $date; ?>]
			},
			yAxis: {
				title: {
					text: '人气(人)'
				}
			},
			plotOptions: {
				line: {
					dataLabels: {
						enabled: true
					},
					enableMouseTracking: false
				}
			},
			series: [{
				name: "<?php echo $userinfo['nick']; ?>",
				data: [<?php echo $popular; ?>]
				}]
		});
		$('#length').highcharts({
			chart: {
				type: 'line'
			},
			title: {
				text: '直播时间曲线图'
			},
			subtitle: {
				text: '<?php echo $userinfo['nick']; ?>'
			},
			xAxis: {
				categories: [<?php echo $date; ?>]
			},
			yAxis: {
				title: {
					text: '时间(小时)'
				}
			},
			plotOptions: {
				line: {
					dataLabels: {
						enabled: true
					},
					enableMouseTracking: false
				}
			},
			series: [{
				name: "<?php echo $userinfo['nick']; ?>",
				data: [<?php echo $length; ?>]
				}]
		});
	});
</script>
<script>
	$("#month").change(function(){
		window.location = "<?php echo $domain . 'system/broker/view/anchorDetail.php?' . filterArg('date'); ?>"
					+ "date=" + $(this).val();
	})
</script>
</body>
</html>
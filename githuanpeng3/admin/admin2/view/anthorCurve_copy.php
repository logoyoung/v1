<?php
/**
 * 游戏类型
 * Author yandong@6room.com
 * Date 2016-6-20 11:41
 */
ini_set('display_errors', 'On');
error_reporting(E_ALL);
include '../module/checkLogin.php';
require('../lib/Anchor_copy.class.php');

$anchor = new Anchor();
$data = $anchor->anchorDetail();

$data['month'] = getPastMonth();
extract($data);

?>

<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title><?php echo $userinfo['nick']; ?>主播时长&收益统计曲线图</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <?php include '../module/mainStyle.php'; ?>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content page-style-square content_body page-sidebar-fixed">
<?php include '../module/head.php'; ?>
<div class="clearfix"></div>
<div class="page-container">
    <?php include '../module/sidebar.php'; ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <!--<h3 class="page-title">游戏类型列表</h3>-->
            <div class="row">
                <div class="col-md-12">

                    <div class="portlet box bg-yellow-gold">
                        <div class="portlet-title">
                            <div class="caption">
                                <h4 class="page-title"><b><?php echo $userinfo['nick']; ?></b>：主播时长&收益统计曲线图</h4>
                            </div>
                            <div class="tools">
                                <select id='datetime' class="table-group-action-input form-control input-inline input-small input-sm">
                                    <?php foreach($month as $k=>$v) { ?>
                                    <option value="<?=$v; ?>" <?php if($selected_date==$v){?>selected<?php }?>><?=$v; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="portlet box green">
                        	<div class="portlet-title">
                        		<div class="caption">
                        			<i class="fa fa-cogs"></i>直播时长共计<?php echo $total_length; ?>
                        		</div>
                        	</div>
                        	<div class="portlet-body flip-scroll" id="length"></div>
                        </div>
                        <div class="portlet box green">
                        	<div class="portlet-title">
                        		<div class="caption">
                        			<i class="fa fa-cogs"></i>直播人气峰值<?php echo $top_popular; ?>
                        		</div>
                        	</div>
                        	<div class="portlet-body flip-scroll" id="popular"></div>
                        </div>
                    </div>
                    <!-- END PORTLET-->
                </div>
            </div>

        </div>

        <?php include '../module/footer.php'; ?>
        <?php include '../module/mainScript.php'; ?>
        <script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
        <script type="text/javascript" src="../common/global/plugins/xcConfirm.js"></script>
        <script type="text/javascript" src="../common/global/scripts/common.js"></script>
        <script type="text/javascript" src="../common/global/plugins/highcharts/highcharts.js"></script>
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
    function getQueryString(name) { 
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
        var r = window.location.search.substr(1).match(reg); 
        if (r != null) {
            return unescape(r[2]);
        }
        return null; 
    } 
	$("#datetime").change(function(){
        var url = window.location.href;
		window.location = url.replace(getQueryString('date'), $(this).val());
	})
</script>
</body>
</html>
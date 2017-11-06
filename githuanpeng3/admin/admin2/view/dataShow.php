<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/16
 * Time: 上午10:40
 */
include '../module/checkLogin.php';
//include INCLUDE_DIR . "statistics/UserStatistic.class.php";

//$UserStatistic = new UserStatistic();

//$newUser = $UserStatistic->todayNewUser();
//$newCustomer = $UserStatistic->todayNewCustomer();
//$totalConsumption = $UserStatistic->todayTotalConsumption();
//$totalRecharge = $UserStatistic->todayTotalRecharge();

?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>数据统计</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!--    --><?php //include ADMIN_MODULE.'mainStyle.php';?>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="../common/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="../common/global/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGIN STYLES -->

    <!-- BEGIN THEME STYLES -->
    <link href="../common/global/css/components.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="../common/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="../common/admin/layout/css/themes/light.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="../common/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->

    <link href="../common/admin/pages/css/vertifyRealName.css" rel="stylesheet" type="text/css"/>
</head>
<body class="page-header-fixed page-sidebar-closed-hide-logo  page-sidebar-fixed">
<?php include ADMIN_MODULE.'head.php';?>
<div class="clearfix"></div>
<div class="page-container">
    <?php include ADMIN_MODULE.'sidebar.php';?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <h3 class="page-title">
                Dashboard <small>reports & statistics</small>
            </h3>
              <div class="row">
                        <div class="col-md-12">
   <div id="container_div"></div> 
        </div>
    </div>
    </div>
    </div>
</div>
<?php include ADMIN_MODULE.'footer.php';?>

<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/jquery.min.js" type="text/javascript"></script>


<script src="http://cdn.hcharts.cn/highcharts/highcharts.js"></script>
<script src="http://cdn.hcharts.cn/highcharts/highcharts-3d.js"></script>
<script src="http://cdn.hcharts.cn/highcharts/modules/exporting.js"></script>
	<script type="text/javascript">
$(function () {
    // Set up the chart
    var chart = new Highcharts.Chart({
        chart: {
            renderTo: 'container_div',
            type: 'column',
            options3d: {
                enabled: true,
                alpha: 0,
                beta: 2,
                depth: 50,
                viewDistance: 25
            }
        },
        title: {
            text: '主播分布图'
        },
        subtitle: {
            text: '主播数最多的前16个省份分布'
        },
         xAxis: { //X轴选项 
            categories: [ //设置X轴分类名称 
            '北京', '天津', '上海', '重庆','黑龙江','吉林','山东','广东','江西','河南','海南','湖南','福建','辽宁','云南','安徽', ] 
        }, 
         yAxis: {
            tickPositions: [0, 50, 100,150, 200,250,300,350,400,450,500],
            title: {
                text: '主播数(每格数*10)'
            },
          
        },
        plotOptions: {
            column: {
                depth: 25
            }
        },
        series: [{
            name: '主播数',
            data: [299, 715, 106, 129, 144, 176, 135, 148, 216, 194, 956, 544,547,780,11,23]
        }],
  
    });
});
		</script>
</body>
</html>
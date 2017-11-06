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
        <!--    --><?php //include ADMIN_MODULE.'mainStyle.php';       ?>
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
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-sidebar-fixed">
        <?php include '../module/head.php'; ?>
        <div class="clearfix"></div>
        <div class="page-container">
            <?php include '../module/sidebar.php'; ?>
            <div class="page-content-wrapper">
                <div class="page-content">
                    <h3 class="page-title">
                        用户数据统计 <small></small>
                    </h3>
                    <div id="today-statistic" class="row margin-top-10">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="dashboard-stat2">
                                <div class="display">
                                    <div class="number">
                                        <h3 class="font-purple-soft"><span style="color:#ff7800">0</span></h3>
                                        <small>新增用户数量</small>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-user"></i>
                                    </div>
                                </div>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span  class="progress-bar " style="width: 57%;background:#ff7800">
                                            <!--<span class="sr-only" >56% change</span>-->
                                        </span>
                                    </div>
<!--                                                                        <div class="status">
                                                                            <div class="status-title">
                                                                                change
                                                                            </div>
                                                                            <div class="status-number">
                                                                                57%
                                                                            </div>
                                                                        </div>-->
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="dashboard-stat2">
                                <div class="display">
                                    <div class="number">
                                        <h3 class="font-blue-sharp"><span style="color:#ff7b81">0</span></h3>
                                        <small>新增消费用户</small>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-basket"></i>
                                    </div>
                                </div>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span  class="progress-bar" style="width: 45%; background:#ff7b81">
                                            <span class="sr-only">45% grow</span>
                                        </span>
                                    </div>
                                    <!--                                    <div class="status">
                                                                            <div class="status-title">
                                                                                grow
                                                                            </div>
                                                                            <div class="status-number">
                                                                                45%
                                                                            </div>
                                                                        </div>-->
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="dashboard-stat2">
                                <div class="display">
                                    <div class="number">
                                        <h3 class="font-green-sharp"><span style="color:#5fd597">0</span><small class="font-green-sharp">¥</small></h3>
                                        <small>今日消费总数</small>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-pie-chart"></i>
                                    </div>
                                </div>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span  class="progress-bar " style="width: 76%; background:#5fd597">
                                            <span class="sr-only">76% progress</span>
                                        </span>
                                    </div>
                                    <!--                                    <div class="status">
                                                                            <div class="status-title">
                                                                                progress
                                                                            </div>
                                                                            <div class="status-number">
                                                                                76%
                                                                            </div>
                                                                        </div>-->
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="dashboard-stat2">
                                <div class="display">
                                    <div class="number">
                                        <h3 class="font-red-haze"><span style="color:#ffa85b">0</span><small class="font-red-haze">¥</small></h3>
                                        <small>今日充值总额</small>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-like"></i>
                                    </div>
                                </div>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span  class="progress-bar " style="width: 85%; background:#ffa85b">
                                            <span class="sr-only">85% change</span>
                                        </span>
                                    </div>
                                    <!--                                    <div class="status">
                                                                            <div class="status-title">
                                                                                change
                                                                            </div>
                                                                            <div class="status-number">
                                                                                85%
                                                                            </div>
                                                                        </div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 sol-sm-12">
                            <div class="portlet light">
                                <div class="portlet-title">
                                    <div class="caption caption-md">
                                        <i class="icon-bar-chart theme-font-color hide"></i>
                                        <!--<span class="caption-subject theme-font-color bold uppercase">用户区域分布</span>-->
                                    </div>
                                    <div class="actions">
                                        <!--<a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="" title=""></a>-->
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="container_div"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="portlet light">
                                <div class="portlet-title">
                                    <div class="actions">
                                        <div class="btn-group btn-group-devided" data-toggle="buttons">
                                            <label class="btn btn-transparent grey-salsa btn-circle btn-sm">
                                                <input type="radio" name="options" class="toggle" id="option1">今天</label>
                                            <label class="btn btn-transparent grey-salsa btn-circle btn-sm">
                                                <input type="radio" name="options" class="toggle" id="option2">昨日</label>
                                            <label class="btn btn-transparent grey-salsa btn-circle btn-sm active">
                                                <input type="radio" name="options" class="toggle" id="option3">最近7天</label>
                                            <label class="btn btn-transparent grey-salsa btn-circle btn-sm ">
                                                <input type="radio" name="options" class="toggle" id="option4">最近30天</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-body">

                                    <div id="trend-statistic" style="height: 260px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--            <div class="row">
                                    <div class="col-md-6 sol-sm-6">
                                        <div class="portlet light">
                                            <div class="portlet-title">
                                                <div class="caption caption-md">
                                                    <i class="icon-bar-chart theme-font-color hide"></i>
                                                    <span class="caption-subject theme-font-color bold uppercase">用户区域分布</span>
                                                </div>
                                                <div class="actions">
                                                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="" title=""></a>
                                                </div>
                                            </div>
                                            <div class="portlet-body">

                                            </div>

                                        </div>
                                    </div>
                                </div>-->

                    <div class="row">
                        <div class="col-md-12 sol-sm-12">
                            <div class="portlet light">
                                <div class="portlet-title">
                                    <div class="caption caption-md">
                                        <i class="icon-bar-chart theme-font-color hide"></i>
                                        <!--<span class="caption-subject theme-font-color bold uppercase">用户区域分布</span>-->
                                    </div>
                                    <div class="actions">
                                        <!--<a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="" title=""></a>-->
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="container_user_pie"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 sol-sm-12">
                            <div class="portlet light">
                                <div class="portlet-title">
                                    <div class="caption caption-md">
                                        <i class="icon-bar-chart theme-font-color hide"></i>
                                        <!--<span class="caption-subject theme-font-color bold uppercase">用户区域分布</span>-->
                                    </div>
                                    <div class="actions">
                                        <!--<a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="" title=""></a>-->
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="container_pie"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 sol-sm-12">
                            <div class="portlet light">
                                <div class="portlet-title">
                                    <div class="caption caption-md">
                                        <i class="icon-bar-chart theme-font-color hide"></i>
                                        <!--<span class="caption-subject theme-font-color bold uppercase">用户区域分布</span>-->
                                    </div>
                                    <div class="actions">
                                        <!--<a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="" title=""></a>-->
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="container_week"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 sol-sm-12">
                            <div class="portlet light">
                                <div class="portlet-title">
                                    <div class="caption caption-md">
                                        <i class="icon-bar-chart theme-font-color hide"></i>
                                        <!--<span class="caption-subject theme-font-color bold uppercase">用户区域分布</span>-->
                                    </div>
                                    <div class="actions">
                                        <!--<a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="" title=""></a>-->
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="container_day"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 sol-sm-12">
                            <div class="portlet light">
                                <div class="portlet-title">
                                    <div class="caption caption-md">
                                        <i class="icon-bar-chart theme-font-color hide"></i>
                                        <!--<span class="caption-subject theme-font-color bold uppercase">用户区域分布</span>-->
                                    </div>
                                    <div class="actions">
                                        <!--<a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="" title=""></a>-->
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="container_pay"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php include '../module/footer.php'; ?>

        <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
        <!-- BEGIN CORE PLUGINS -->
        <!--[if lt IE 9]>
        <script src="../common/global/plugins/respond.min.js"></script>
        <script src="../common/global/plugins/excanvas.min.js"></script>
        <![endif]-->
        <script src="../common/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/jqPaginator.js" type="text/javascript"></script>
        <script src="../common/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
        <!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
        <script src="../common/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="../common/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/bootstrap-daterangepicker/moment.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
        <!-- IMPORTANT! fullcalendar depends on jquery-ui-1.10.3.custom.min.js for drag & drop support -->
        <script src="../common/global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
        <script src="../common/global/plugins/gritter/js/jquery.gritter.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="../common/global/scripts/metronic.js" type="text/javascript"></script>
        <script src="../common/admin/layout/scripts/layout.js" type="text/javascript"></script>
        <script src="../common/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
        <script src="../common/admin/layout/scripts/navi-sidebar.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->

        <script src="../common/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="../common/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
        <script src="../common/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
        <script src="../common/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script>
        <script src="../common/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
        <script src="../common/global/plugins/amcharts/amcharts/themes/patterns.js" type="text/javascript"></script>
        <script src="../common/global/plugins/amcharts/amcharts/themes/chalk.js" type="text/javascript"></script>
        <script src="../common/global/plugins/amcharts/ammap/ammap.js" type="text/javascript"></script>
        <script src="../common/global/plugins/amcharts/ammap/maps/js/worldLow.js" type="text/javascript"></script>
        <script src="../common/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script>

        <script src="../common/global/scripts/common.js"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                Metronic.init(); // init metronic core componets
                Layout.init(); // init layout
                QuickSidebar.init(); // init quick sidebar
            });

            $(document).ready(function () {
                requestTodayStatisticData();

                function requestTodayStatisticData() {
                    var dom = $('#today-statistic');
                    var tdDom = {
                        newUsers: {
                            display: dom.find('.dashboard-stat2:eq(0) .display'),
                            progress: dom.find('.dashboard-stat2:eq(0) .progress-info')
                        },
                        newCustomers: {
                            display: dom.find('.dashboard-stat2:eq(1) .display'),
                            progress: dom.find('.dashboard-stat2:eq(1) .progress-info')
                        },
                        totalConsumption: {
                            display: dom.find('.dashboard-stat2:eq(2) .display'),
                            progress: dom.find('.dashboard-stat2:eq(2) .progress-info')
                        },
                        totalRecharge: {
                            display: dom.find('.dashboard-stat2:eq(3) .display'),
                            progress: dom.find('.dashboard-stat2:eq(3) .progress-info')
                        }
                    };
                    $.ajax({
                        url: $conf.statistic.api + 'todayStatistic.php',
                        type: 'post',
                        dataType: 'json',
                        success: function (d) {
                            console.log(d.stat);
                            if (d.stat == 1) {
                                console.log(d);
                                initToadyStatisticInfo(d.resuData);
                            }
                        }
                    });


                    function initToadyStatisticInfo(d) {
                        console.log(d);
                        for (var i in tdDom) {
                            var percent = growth(d[i].today, d[i].yesterday);

                            initNum(tdDom[i].display, d[i].today);
                            initProgress(tdDom[i].progress, percent);
                        }

                        function initNum(dom, num) {
                            dom.find('.number h3 span').text(num);
                        }

                        function initProgress(dom, percent) {
                            dom.find('.progress .progress-bar').css('width', percent + '%').find('.sr-only').text(percent + "% grow");
                            dom.find('.status .status-number').text(percent + "%");
                        }
                    }

                }


            });
        </script>

        <script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
        <script>

            $(document).ready(function () {
                var chartData = generateChartData();

                var chart = AmCharts.makeChart("trend-statistic", {
                    "type": "serial",
                    "theme": "light",
                    "pathToImages": $conf.domain + 'common/global/plugins/amcharts/amcharts/images/',
                    "dataDateFormat": "YYYY-MM-DD",
                    "legend": {
                        "useGraphSettings": true
                    },
                    "dataProvider": chartData,
                    "synchronizeGrid": true,
                    "valueAxes": [{
                            "id": "v1",
                            "axisColor": "#FF6600",
                            "axisThickness": 2,
                            "axisAlpha": 1,
                            "position": "left"
                        }, {
                            "id": "v2",
                            "axisColor": "#FCD202",
                            "axisThickness": 2,
                            "axisAlpha": 1,
                            "position": "right"
                        }, {
                            "id": "v3",
                            "axisColor": "#B0DE09",
                            "axisThickness": 2,
                            "gridAlpha": 0,
                            "offset": 50,
                            "axisAlpha": 1,
                            "position": "left"
                        }],
                    "graphs": [{
                            "valueAxis": "v1",
                            "lineColor": "#FF6600",
                            "bullet": "round",
                            "bulletBorderThickness": 1,
                            "hideBulletsCount": 30,
                            "title": "red line",
                            "valueField": "visits",
                            "fillAlphas": 0
                        }, {
                            "valueAxis": "v2",
                            "lineColor": "#FCD202",
                            "bullet": "square",
                            "bulletBorderThickness": 1,
                            "hideBulletsCount": 30,
                            "title": "yellow line",
                            "valueField": "hits",
                            "fillAlphas": 0
                        }, {
                            "valueAxis": "v2",
                            "lineColor": "#B0DE09",
                            "bullet": "triangleUp",
                            "bulletBorderThickness": 1,
                            "hideBulletsCount": 30,
                            "title": "green line",
                            "valueField": "views",
                            "fillAlphas": 0
                        }],
                    "chartScrollbar": {},
                    "chartCursor": {
                        "cursorPosition": "mouse"
                    },
                    "categoryField": "date",
                    "categoryAxis": {
                        "parseDates": true,
                        "axisColor": "#DADADA",
                        "minorGridEnabled": true
                    },
                    "export": {
                        "enabled": true,
                        "position": "bottom-right"
                    }
                });

                chart.addListener("dataUpdated", zoomChart);
                zoomChart();


                // generate some random data, quite different range
                function generateChartData() {
                    var chartData = [];
                    var firstDate = new Date();
                    firstDate.setDate(firstDate.getDate() - 100);

                    for (var i = 1; i < 30; i++) {
                        // we create date objects here. In your data, you can have date strings
                        // and then set format of your dates using chart.dataDateFormat property,
                        // however when possible, use date objects, as this will speed up chart rendering.
                        var newDate = new Date(firstDate);
                        //            newDate.setDate(newDate.getDate() + i);
                        var day = i < 10 ? '0' + i : i;
                        newDate = '2014-03-' + day;
                        var visits = Math.round(Math.sin(i * 5) * i);
                        var hits = Math.round(Math.random() * 80) + 500 + i * 3;
                        var views = Math.round(Math.random() * 6000) + i * 4;

                        chartData.push({
                            date: newDate,
                            visits: visits,
                            hits: hits,
                            views: views
                        });
                    }
                    console.log(chartData);
                    return chartData;
                }

                function zoomChart() {
                    chart.zoomToIndexes(chart.dataProvider.length - 20, chart.dataProvider.length - 1);
                }
            });
        </script>
        <script src="http://cdn.hcharts.cn/highcharts/highcharts.js"></script>
        <script src="http://cdn.hcharts.cn/highcharts/highcharts-3d.js"></script>
        <script src="http://cdn.hcharts.cn/highcharts/modules/exporting.js"></script>
        <script type="text/javascript">
            $(function () {
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
                    credits: {
                        enabled: false
                    },
                    title: {
                        text: '主播分布图'
                    },
                    subtitle: {
                        text: '主播数最多的前16个省份分布'
                    },
                    xAxis: {//X轴选项
                        categories: [//设置X轴分类名称
                            '北京', '天津', '上海', '重庆', '黑龙江', '吉林', '山东', '广东', '江西', '河南', '海南', '湖南', '福建', '辽宁', '云南', '安徽', ]
                    },
                    yAxis: {
                        tickPositions: [0, 500, 1000, 1500, 2000, 2500, 3000, 3500, 4000, 4500, 5000],
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
                            data: [299, 715, 1064, 1292, 1440, 1760, 1356, 1485, 2164, 1941, 956, 544, 547, 708, 101, 230],
                            color: '#FFC796',
                            negativeColor: '#ffa04c'
                        }],
                });

                $('#container_pie').highcharts({
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45,
                            beta: 0
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    title: {
                        text: '2016年用户使用浏览器统计'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            depth: 35,
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}'
                            }
                        }
                    },
                    series: [{
                            type: 'pie',
                            name: 'Browser share',
                            data: [
                                ['Firefox', 45.0],
                                ['IE', 26.8],
                                {
                                    name: 'Chrome',
                                    y: 12.8,
                                    sliced: true,
                                    selected: true
                                },
                                ['Safari', 8.5],
                                ['Opera', 6.2],
                                ['Others', 0.7]
                            ]
                        }]
                });

                $('#container_user_pie').highcharts({
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45,
                            beta: 0
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    title: {
                        text: '2016年用户来源统计'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            depth: 35,
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}'
                            }
                        }
                    },
                    series: [{
                            type: 'pie',
                            name: 'Browser share',
                            data: [
                                ['网站', 26.8],
                                ['APP端', 25.8],
                                ['微信', 20.5],
                                ['微博', 4.2],
                                ['QQ', 10.7]
                            ]
                        }]
                });
                $('#container_pay').highcharts({
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    title: {
                        text: '2016年充值统计'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                            name: 'RMB:',
                            data: [
                                ['一月', 800000],
                                ['二月', 3000000],
                                ['三月', 5000000],
                                ['四月', 6000000],
                                ['五月', 80000000],
                                ['六月', 400000],
                                ['七月', 40000],
                                ['八月', 80000000],
                                ['九月', 10000000],
                                ['十月', 40000000],
                                ['十一月', 40000000],
                                ['十二月', 1000000]
                            ]
                        }]
                });
                $('#container_week').highcharts({
                    chart: {
                        type: 'spline'
                    },
                    credits: {
                        enabled: false
                    },
                    title: {
                        text: '上周用户在线情况'
                    },
                    xAxis: {
                        categories: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
                    },
                    yAxis: {
                        tickPositions: [0, 50, 100, 150, 200, 250, 300, 350, 400, 450, 500],
                        title: {
                            text: '人数'
                        }, labels: {
                            formatter: function () {
                                return this.value;
                            }
                        }
                    },
                    tooltip: {
                        crosshairs: true,
                        shared: true
                    },
                    plotOptions: {
                        spline: {
                            marker: {
                                radius: 4,
                                lineColor: '#666666',
                                lineWidth: 1
                            }
                        }
                    }, series: [{
                            name: '用户',
                            marker: {
                                symbol: 'square'
                            },
                            data: [70, 69, 95 , 183, 139, 96, 100],
                            color: '#ff7b81'

                        }, {
                            name: '主播',
                            marker: {
                                symbol: 'diamond'
                            },
                            data: [10, 42, 57, 142, 103, 106, 48],
                            color: '#ffa85b'
                        }]
                });
                $('#container_day').highcharts({
                    title: {
                        text: '一天各时段用户活跃情况'
                    },
                    credits: {
                        enabled: false
                    },
                    xAxis: {
                        categories: ['0', '1', '2', '3', '4', '5',
                            '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24']
                    },
                    yAxis: {
                        tickPositions: [0, 25, 50, 75, 100, 125, 150, 175, 200, 220, 250],
                        title: {
                            text: '人数'
                        },
                        plotLines: [{
                                value: 0,
                                width: 1,
                                color: '#808080'
                            }]
                    },
                    tooltip: {
                        valueSuffix: '人'
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series: [{
                            name: '主播',
                            data: [70, 69, 95, 15, 12, 25, 22, 25, 23, 183, 19, 96, 19, 20, 34, 56, 78, 43, 12, 61, 22, 32, 13, 29],
                            color: '#ffa85b'
                },
                        {name: '用户',
                            data: [39, 42, 57, 85, 19, 152, 17, 16, 12, 10., 66, 48, 31, 25, 22, 25, 23, 183, 19, 90, 32, 24, 56, 60],
                           color: '#ff7b81'
                }]
                });
            });
        </script>
    </body>
</html>
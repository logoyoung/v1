<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/16
 * Time: 上午10:40
 */
include '../../../include/adminInit.php';
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>Metronic | Admin Dashboard Template</title>
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
<body class="page-header-fixed page-sidebar-closed-hide-logo">
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

            </div>
        </div>
    </div>

</div>
<?php include ADMIN_MODULE.'footer.php';?>

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/respond.min.js"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/jqPaginator.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/bootstrap-daterangepicker/moment.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
<!-- IMPORTANT! fullcalendar depends on jquery-ui-1.10.3.custom.min.js for drag & drop support -->
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>global/plugins/gritter/js/jquery.gritter.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo __ADMIN_STATIC__;?>global/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo __ADMIN_STATIC__;?>admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<script type="text/javascript">
    $(document).ready(function(){
        Metronic.init(); // init metronic core componets
        Layout.init(); // init layout
        QuickSidebar.init(); // init quick sidebar
    });
</script>

<script type="text/javascript" src="../../main/static/js/jquery.form.js"></script>
<script>

</script>
</body>
</html>
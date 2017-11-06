<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/16
 * Time: 上午10:40
 */
include '../module/checkLogin.php';
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
    <?php include ADMIN_MODULE.'mainStyle.php';?>
</head>
<body class="page-header-fixed page-sidebar-closed-hide-logo page-sidebar-fixed">
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
                    <div class="portlet box green">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-gift"></i> Horizontal Form
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form id="form-up-app" name="form-up-app" action="../api/upApp.php" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">版本号</label>
                                        <div class="col-md-9">
                                            <input id="versionid" name="versionid" class="form-control" placeholder="Enter text" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">版本名称</label>
                                        <div class="col-md-9">
                                            <input id="versionName" name="versionName" class="form-control" placeholder="Enter text" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">版本描述</label>
                                        <div class="col-md-9">
                                            <textarea id="versionDesc" name="versionDesc" class="form-control" rows="18"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputFile" class="col-md-3 control-label">安装包</label>
                                        <div class="col-md-9">
                                            <input id="appFile" name="appFile" type="file">
                                            <p class="help-block">
                                                上传最新版本安装包
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <span id="form-submit" class="btn green">Submit</span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ADMIN_MODULE.'footer.php';?>
<?php include ADMIN_MODULE.'mainScript.php';?>
<script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
<script>
    $('#form-submit').bind('click', function(){
        var form = $('#form-up-app');

        $('input[type=text]').focus(function(){
            $(this).parents('.form-group').removeClass('has-error');
        });
        if(!$('#versionid').val()){
            $(this).parents('.form-group').removeClass('has-error');
        }
        if(!$('#versionName').val()){
            $(this).parents('.form-group').removeClass('has-error');
        }
        if(!$('#versionDesc').val()){
            $(this).parents('.form-group').removeClass('has-error');
        }
        form.ajaxSubmit({
            url:'../api/upApp.php',
            type:'post',
            dataType:'json',
            uploadProgress:function(event, position, total, percentComplete){
               console.log(percentComplete);
            },
            success:function(d){
                if(d.isSuccess == 1){
                    alert('上传成功');
                }
            }
        });
    });
</script>
</body>
</html>
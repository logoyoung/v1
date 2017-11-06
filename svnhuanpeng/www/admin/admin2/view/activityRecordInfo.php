<?php
/**
 * 活动下发记录详情
 * Author yandong@6room.com
 * Date 2016-6-20 11:41ß
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
        <title>活动&内部发放纪录详情</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta content="" name="description"/>
        <meta content="" name="author"/>
        <style type="text/css">
            .verticalAlign{ vertical-align:middle; display:inline-block; height:100%; margin-left:-1px;}
            .xcConfirm .xc_layer{position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: #666666; opacity: 0.5; z-index: 2147000000;}
            .xcConfirm .popBox{position: fixed; left: 50%; top: 50%; background-color: #ffffff; z-index: 2147000001; width: 570px; height: 300px; margin-left: -285px; margin-top: -150px; border-radius: 5px; font-weight: bold; color: #535e66;}
            .xcConfirm .popBox .ttBox{height: 30px; line-height: 30px; padding: 30px 30px; border-bottom: solid 1px #eef0f1;}
            .xcConfirm .popBox .ttBox .tt{font-size: 18px; display: block; float: left; height: 30px; position: relative;}
            .xcConfirm .popBox .ttBox .clsBtn{display: block; cursor: pointer; width: 12px; height: 12px; position: absolute; top: 22px; right: 30px; background: url(../common/global/img/icons.png) -48px -96px no-repeat;}
            .xcConfirm .popBox .txtBox{margin: 40px 100px; height: 100px; overflow: hidden;}
            .xcConfirm .popBox .txtBox .bigIcon{float: left; margin-right: 20px; width: 48px; height: 48px; background-image: url(../common/global/img/icons.png); background-repeat: no-repeat; background-position: 48px 0;}
            .xcConfirm .popBox .txtBox p{ height: 84px; margin-top: 16px; line-height: 26px; overflow-x: hidden; overflow-y: auto;}
            .xcConfirm .popBox .txtBox p input{width: 364px; height: 30px; border: solid 1px #eef0f1; font-size: 18px; margin-top: 6px;}
            .xcConfirm .popBox .btnArea{border-top: solid 1px #eef0f1;}
            .xcConfirm .popBox .btnGroup{float: right;}
            .xcConfirm .popBox .btnGroup .sgBtn{margin-top: 14px; margin-right: 10px;}
            .xcConfirm .popBox .sgBtn{display: block; cursor: pointer; float: left; width: 95px; height: 35px; line-height: 35px; text-align: center; color: #FFFFFF; border-radius: 5px;}
            .xcConfirm .popBox .sgBtn.ok{background-color: #FF7800; color: #FFFFFF;}
            .xcConfirm .popBox .sgBtn.cancel{background-color: #a0a0a0; color: #FFFFFF;}
            .table th, .table td { 
                text-align: center;
                vertical-align: middle!important;
            }
        </style>
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
                                        <h4 class="page-title">活动&内部发放记录详情</h4>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover line-height">
                                        <thead>
                                            <tr>
                                                <th width="10%">收益人ID</th>
                                                <th width="10%">欢朋币</th>
                                                <th width="10%">欢朋豆</th>
                                                <th width="10%">类型</th>
                                                <th width="10%">活动id</th>
                                                <th width="50%">下发描述</th>
                                            </tr>
                                        </thead>
                                        <tbody id="typebodys" class="line-height">
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                            <!-- END PORTLET-->
                        </div>
                    </div>

                </div>         


                <div class="modal" id="update_rate">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <h4 class="modal-title">修改比率</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="portlet-body form">
                                        <!-- BEGIN FORM-->
                                        <form id="upicon" name="upicon"  enctype="multipart/form-data" class="form-horizontal form-row-seperated">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">名称<span class="required">
                                                            * </span></label>
                                                    <div class="col-md-5">
                                                        <input class="form-control" id="cname" type="text" readOnly="true"/>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">经纪公司ID<span class="required">
                                                            * </span></label>
                                                    <div class="col-md-5">
                                                        <input class="form-control" id="cid" type="text" readOnly="true"/>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">比率<span class="required">
                                                            </span></label>
                                                    <div class="col-md-5">
                                                        <input class="form-control" id="now_rate" type="text"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--<div class="form-actions">-->
                                        </form>
                                        <!-- END FORM-->
                                    </div>
                                    <!--</div>-->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                <button type="button"  id="updateCompanyRate" class="btn btn-primary">保存</button>
                            </div>

                        </div>
                    </div>
                </div>



                <?php include '../module/footer.php'; ?>
                <?php include '../module/mainScript.php'; ?>
                <script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
                <script type="text/javascript" src="../common/global/plugins/xcConfirm.js"></script>
                <script type="text/javascript" src="../common/global/scripts/common.js" ></script>
                <script type="text/javascript">
                    $(document).ready(function () {
                        getActivityRecordInfo();
                    });

                    function GetRequest() {
                        var url = location.search;
                        if (url.indexOf("?") != -1) {
                            var str = url.substr(1);
                            strs = str.split("=");
                            return strs[1];
                        }
                    }

                    function getGameTypeList() {
                        var month = GetRequest();
                        alert(month);

                    }

                    function getGameTypeList() {
                        var domUlStr = '';
                        var domLiStr = '';
                        $("#typebodys tr").remove();
                        $.ajax({
                            url: '../api/statistic/activityRecord.php',
                            dataType: 'json',
                            data:{
                                uid:getCookie("admin_uid"),
                                encpass:getCookie('admin_enc'),
                                type: getCookie('admin_type')
                            },
                            type: 'POST',
                            cache: false,
                            success: function (data) {
                                if (data) {
                                    $.each(data.resuData, function (i, item) {
                                        $("#typebodys").append(
                                            "<tr id=" + item.date + ">"
                                            + "<td>" + item.date + "</td>"
                                            + "<td>" + item.hpbean + "</td>"
                                            + "<td>" + item.hpcoin + "</td>"
                                            + '<td><button class="btn btn-info btn-sm bg-yellow-gold " onclick="jump(this)">查看详情</button></td>'
                                            + "<tr>"
                                        );
                                    });
                                }
                            },
                            error: function () {

                            }

                        });
                    }

                    function jump(month){
                        var m = $(month).parents("tr").attr("id");
//                      location.href=encodeURI($conf.domain + "view/activityRecordInfo.php?m="+cid);
                    }


                </script>
                </body>
                </html>
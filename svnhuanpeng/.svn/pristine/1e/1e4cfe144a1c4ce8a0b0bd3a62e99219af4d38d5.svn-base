<?php
/**
 * 游戏类型
 * Author yandong@6room.com
 * Date 2016-6-20 11:41
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
        <title>经纪公司</title>
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
                                        <h4 class="page-title">经纪公司收益</h4>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover line-height">
                                        <thead>
                                            <tr>
                                                <th width="2%">CID</th>
                                                <th width="20%">经纪公司名称</th>
                                                <th width="4%">主播数量</th>
                                                <th width="10%">金币收益</th>
                                                <th width="10%">金豆收益</th>
                                                <th width="10%">操作</th>
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

                <?php include '../module/footer.php'; ?>
                <?php include '../module/mainScript.php'; ?>
                <script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
                <script type="text/javascript" src="../common/global/plugins/xcConfirm.js"></script>
                <script type="text/javascript" src="../common/global/scripts/common.js" ></script>
                <script type="text/javascript">


                    $(document).ready(function () {
                        getGameTypeList();
                    });
                    function getGameTypeList() {
                        $("#typebodys tr").remove();
                        $.ajax({
                            url: '../api/income/companyIncomeList.php',
                            dataType: 'json',
                            data:{
                                uid:getCookie("admin_uid"),
                                encpass:getCookie('admin_enc'),
                                type: getCookie('admin_type'),
                                page:1,
                                size:10,
                                month:'2017-02',
                                incomeType:'0',
                                order:'0'
                            },
                            type: 'POST',
                            cache: false,
                            success: function (data) {
                                if (data) {
                                    $.each(data.resuData.list, function (i, item) {
                                        $("#typebodys").append(
                                                "<tr id=" + item.id + ">"
                                                + "<td>" + item.id + "</td>"
                                                + "<td>" + item.name + "</td>"
                                                + "<td>" + item.people + "</td>"
                                                + "<td>" + "<span class='label label-danger'>" + '暂无数据' + "</span>" + "</td>"
                                                + "<td>" + "<span class='label label-danger'>" + '暂无数据' + "</span>"+ "</td>"
//                                                + "<td>" + item.coin +  "金币 &nbsp;&nbsp;（"+ item.crmb +"元）</td>"
//                                                + "<td>" + item.bean +  "金豆 &nbsp;&nbsp;（"+ item.brmb +"元）</td>"
                                                + '<td><button class="btn btn-info btn-sm bg-yellow-gold"  onclick="jump(this)" >查看旗下人员详情</button></td>'
                                                + "<tr>"
                                                );
                                    });
                                }
                            },
                            error: function () {

                            }

                        });
                    }

                function jump(cid){
                    var cid = $(cid).parents("tr").attr("id");
                    location.href=encodeURI($conf.domain + "view/companyAnchorIncome.php?cid="+cid);
                }

                </script>
                </body>
                </html>
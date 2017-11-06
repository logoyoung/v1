<?php
/**
 * 游戏类型
 * Author yandong@6room.com
 * Date 2016-6-20 11:41
 */
include '../module/checkLogin.php';
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
    <title>经纪公司旗下主播收益</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <style type="text/css">
        .verticalAlign {
            vertical-align: middle;
            display: inline-block;
            height: 100%;
            margin-left: -1px;
        }

        .xcConfirm .xc_layer {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #666666;
            opacity: 0.5;
            z-index: 2147000000;
        }

        .xcConfirm .popBox {
            position: fixed;
            left: 50%;
            top: 50%;
            background-color: #ffffff;
            z-index: 2147000001;
            width: 570px;
            height: 300px;
            margin-left: -285px;
            margin-top: -150px;
            border-radius: 5px;
            font-weight: bold;
            color: #535e66;
        }

        .xcConfirm .popBox .ttBox {
            height: 30px;
            line-height: 30px;
            padding: 30px 30px;
            border-bottom: solid 1px #eef0f1;
        }

        .xcConfirm .popBox .ttBox .tt {
            font-size: 18px;
            display: block;
            float: left;
            height: 30px;
            position: relative;
        }

        .xcConfirm .popBox .ttBox .clsBtn {
            display: block;
            cursor: pointer;
            width: 12px;
            height: 12px;
            position: absolute;
            top: 22px;
            right: 30px;
            background: url(../common/global/img/icons.png) -48px -96px no-repeat;
        }

        .xcConfirm .popBox .txtBox {
            margin: 40px 100px;
            height: 100px;
            overflow: hidden;
        }

        .xcConfirm .popBox .txtBox .bigIcon {
            float: left;
            margin-right: 20px;
            width: 48px;
            height: 48px;
            background-image: url(../common/global/img/icons.png);
            background-repeat: no-repeat;
            background-position: 48px 0;
        }

        .xcConfirm .popBox .txtBox p {
            height: 84px;
            margin-top: 16px;
            line-height: 26px;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .xcConfirm .popBox .txtBox p input {
            width: 364px;
            height: 30px;
            border: solid 1px #eef0f1;
            font-size: 18px;
            margin-top: 6px;
        }

        .xcConfirm .popBox .btnArea {
            border-top: solid 1px #eef0f1;
        }

        .xcConfirm .popBox .btnGroup {
            float: right;
        }

        .xcConfirm .popBox .btnGroup .sgBtn {
            margin-top: 14px;
            margin-right: 10px;
        }

        .xcConfirm .popBox .sgBtn {
            display: block;
            cursor: pointer;
            float: left;
            width: 95px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            color: #FFFFFF;
            border-radius: 5px;
        }

        .xcConfirm .popBox .sgBtn.ok {
            background-color: #FF7800;
            color: #FFFFFF;
        }

        .xcConfirm .popBox .sgBtn.cancel {
            background-color: #a0a0a0;
            color: #FFFFFF;
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle !important;
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
                                <h4 class="page-title">经纪公司旗下主播收益</h4>
                            </div>
                            <div class="tools">
                                <select id='datetime'
                                        class="table-group-action-input form-control input-inline input-small input-sm">
                                    <option value="2017-02">2017-02</option>
                                    <option value="2017-03">2017-03</option>
                                    <option value="2017-04">2017-04</option>
                                    <option value="2017-05">2017-05</option>
                                    <option value="2017-06">2017-06</option>
                                </select>
                                <button id="search" class="btn bg-yellow-gold">搜索</button>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover line-height">
                                <thead>
                                <tr>
                                    <th width="4%">UID</th>
                                    <th width="10%">昵称</th>
                                    <th width="10%">头像</th>
                                    <th width="13%">直播收益</th>
                                    <th width="13%">直播时长</th>
                                    <th width="10%">直播间</th>
                                    <th width="10%">人气峰值</th>
                                    <th width="10%">详情</th>
                                </tr>
                                </thead>
                                <tbody id="typebodys" class="line-height">
                                </tbody>
                            </table>
                            <nav style="text-align: center" id="msg_text">
                                <ul class="pagination" id="game_list"></ul>
                            </nav>

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
        <script type="text/javascript">


            $(document).ready(function () {
                getGameTypeList();
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
                var cid = GetRequest();
                var datetime = $("#datetime").attr('value');
                $.jqPaginator('#game_list', {
                    totalPages: 10,
                    visiblePages: 10,
                    currentPage: 1,
                    onPageChange: function (num, type) {
                        $("#typebodys tr").remove();
                        $.ajax({
                            url: '../api/income/companyAnchorIncome.php',
                            dataType: 'json',
                            data: {
                                uid: getCookie("admin_uid"),
                                encpass: getCookie('admin_enc'),
                                type: getCookie('admin_type'),
                                page: num,
                                size: 10,
                                cid: cid,
                                month: datetime,
                                incomeType: '0',
                                order: '0'
                            },
                            type: 'POST',
                            cache: false,
                            success: function (data) {
                                if (data) {
                                    $.each(data.resuData.list, function (i, item) {
                                        $("#typebodys").append(
                                            "<tr id=" + item.uid + ">"
                                            + "<td>" + item.uid + "</td>"
                                            + "<td>" + item.nick + "</td>"
                                            + "<td><img  height='50' width='50' src=" + item.pic + "></td>"
                                            + "<td>" + "<span class='label label-danger'>" + '暂无数据' + "</span>" + "</td>"
//                                                + "<td>" + item.coin +  "金币 &nbsp;+&nbsp;"+ item.bean +"金豆</td>"
                                            + "<td>" + item.length + "</td>"
                                            + "<td>" + item.roomID + "</td>"
                                            + "<td>" + item.popularoty + "</td>"
                                            + '<td><button class="btn btn-info btn-sm bg-grey-silver"  onclick="" >开发中...</button></td>'
                                            + "<tr>"
                                        );
                                    });
                                    total = data.resuData.total;
                                    totalPage = Math.ceil(total * 0.1);
                                    if (total == 0) {
                                        $('#game_list').jqPaginator('destroy');
                                        $("#msg_text").find("h2").text('');
                                        $('#msg_text').append("<h2>未找到相关数据!<h2>");
                                    } else {
                                        $("#msg_text").find("h2").text('');
                                        $('#game_list').jqPaginator('option', {
                                            totalCounts: parseInt(total),
                                            totalPages: totalPage
                                        });
                                    }
                                }
                            },
                            error: function () {

                            }

                        });
                    }
                });
            }


            $("#search").on('click', function () {
                var datetime = $("#datetime").attr('value');
                var cid = GetRequest();
                $.jqPaginator('#game_list', {
                    totalPages: 10,
                    visiblePages: 10,
                    currentPage: 1,
                    onPageChange: function (num, type) {
                        $("#typebodys tr").remove();
                        $.ajax({
                            url: '../api/income/companyAnchorIncome.php',
                            dataType: 'json',
                            data: {
                                uid: getCookie("admin_uid"),
                                encpass: getCookie('admin_enc'),
                                type: getCookie('admin_type'),
                                page: num,
                                size: 10,
                                cid: cid,
                                month: datetime,
                                incomeType: '0',
                                order: '0'
                            },
                            type: 'POST',
                            cache: false,
                            success: function (data) {
                                if (data) {
                                    $.each(data.resuData.list, function (i, item) {
                                        $("#typebodys").append(
                                            "<tr id=" + item.uid + ">"
                                            + "<td>" + item.uid + "</td>"
                                            + "<td>" + item.nick + "</td>"
                                            + "<td><img  height='50' width='50' src=" + item.pic + "></td>"
                                            + "<td>" + "<span class='label label-danger'>" + '暂无数据' + "</span>" + "</td>"
//                                                + "<td>" + item.coin +  "金币 &nbsp;+&nbsp;"+ item.bean +"金豆</td>"
                                            + "<td>" + item.length + "</td>"
                                            + "<td>" + item.roomID + "</td>"
                                            + "<td>" + item.popularoty + "</td>"
                                            + '<td><button class="btn btn-info btn-sm bg-grey-silver"  onclick="" >开发中...</button></td>'
                                            + "<tr>"
                                        );
                                    });
                                    total = data.resuData.total;
                                    totalPage = Math.ceil(total * 0.1);
                                    if (total == 0) {
                                        $('#game_list').jqPaginator('destroy');
                                        $("#msg_text").find("h2").text('');
                                        $('#msg_text').append("<h2>未找到相关数据!<h2>");
                                    } else {
                                        $("#msg_text").find("h2").text('');
                                        $('#game_list').jqPaginator('option', {
                                            totalCounts: parseInt(total),
                                            totalPages: totalPage
                                        });
                                    }
                                }
                            },
                            error: function () {

                            }

                        });
                    }
                });

            });


        </script>
</body>
</html>
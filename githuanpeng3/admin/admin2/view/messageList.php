<?php
/**
 * 站内信列表
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
        <title>站内信列表</title>
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
            .xcConfirm .popBox .sgBtn.ok{background-color: #ff7800; color: #FFFFFF;}
            .xcConfirm .popBox .sgBtn.cancel{background-color: #a0a0a0; color: #FFFFFF;}
            .table th, .table td { 
                text-align: center;
                vertical-align: middle!important;
            }
        </style>
        <?php include '../module/mainStyle.php'; ?>
    </head>
    <body class="page-header-fixed page-quick-sidebar-over-content page-style-square content_body  page-sidebar-fixed">
        <?php include '../module/head.php'; ?>
        <div class="clearfix"></div>
        <div class="page-container">
            <?php include '../module/sidebar.php'; ?>
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="portlet box bg-yellow-gold"> 
                                <div class="portlet-title">
                                    <div class="caption">
                                        <h4 class="page-title">站内信列表</h4>
                                    </div>
                                    <div class="tools">

                                        <form class="navbar-form pull-right" onsubmit="return false;">         
                                            <input type="text" class="form-control" id='keyword' placeholder="" onkeypress="if (event.keyCode == 13) {
                                                        enterSearch(keyword.value);
                                                    }"/>
                                            <button type="button" class="btn bg-yellow-gold" id='search'>搜索</button>&nbsp;&nbsp;&nbsp;
                                            <button type="button" class="btn bg-yellow-gold" id="addNewMsg">+发送新消息</button>
                                        </form>  
                                    </div>
                                </div>
                                <div class="portlet-body inner">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead style='text-align:center; vertical-align:middle;'>
                                            <tr style="text-align:center; vertical-align:middle;">
                                                <th width="30px">Id</th>
                                                <th>标题</th>
                                                <th>内容</th>
                                                <th>时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody id="gamebodys" style="text-align:center; vertical-align:middle;">
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

                <div class="modal" id="addnew">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <h4 class="modal-title"></h4>
                            </div>
                            <div class="modal-body">
                                <div class="portlet-body form">
                                    <!-- BEGIN FORM-->
                                    <form action="#" class="form-horizontal form-bordered">
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">标题<span class="required">
                                                        * </span></label>
                                                <div class="col-md-9">
                                                    <input class="form-control" id="add_title" type="text"/>
                                                </div>
                                            </div>
                                            <div class="form-group last">
                                                <label class="control-label col-md-3">消息内容<span class="required">
                                                        * </span></label>
                                                <div class="col-md-9">
                                                    <textarea id="add_msg" class="form-control" maxlength="225" rows="2" placeholder="请输入0-225个字符"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                    <!-- END FORM-->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="add" class="btn btn-primary ">确定</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                </div>
                            </div>  
                        </div> 
                    </div> 

                </div>
                <div class="modal" id="mymodal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <h4 class="modal-title">详情</h4>
                            </div>
                            <div class="modal-body">
                                <div class="portlet-body form">
                                    <!-- BEGIN FORM-->
                                    <form action="#" class="form-horizontal form-bordered">
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">标题<span class="required">
                                                        * </span></label>
                                                <div class="col-md-9">
                                                    <input class="form-control" id="title" type="text" readonly="true"/>
                                                </div>
                                            </div>
                                            <div class="form-group last">
                                                <label class="control-label col-md-3">消息内容</label>
                                                <div class="col-md-9">
                                                    <textarea id="msg_info" class="form-control" maxlength="225" rows="2" readonly="true"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                    <!-- END FORM-->
                                </div>
                                <div class="modal-footer">
                                    <!--                                    <button type="button" id="pass" class="btn btn-primary">确定</button>
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>-->
                                </div>
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
                                                    getMessageList();
                                                });

                                                function getMessageList() {
                                                    $.jqPaginator('#game_list', {
                                                        totalPages: 10,
                                                        visiblePages: 10,
                                                        currentPage: 1,
                                                        onPageChange: function (num, type) {
                                                            $("#gamebodys tr").remove();
                                                            $.ajax({
                                                                url: $conf.message.api + 'messageList.php',
                                                                dataType: 'json',
                                                                data: {
                                                                    page: num,
                                                                    uid:getCookie("admin_uid"),
                                                                    encpass:getCookie('admin_enc'),
                                                                    type: getCookie('admin_type')
                                                                },
                                                                type: 'POST',
                                                                cache: false,
                                                                success: function (data) {
                                                                    if (data) {
                                                                        $.each(data.resuData.list, function (i, item) {
                                                                            $("#gamebodys").append(
                                                                                    "<tr  id=" + item.id + ">"
                                                                                    + "<td >" + item.id + "</td>"
                                                                                    + "<td >" + item.title + "</td>"
                                                                                    + "<td >" + item.msg + "</td>"
                                                                                    + "<td >" + item.stime + "</td>"
                                                                                    + '<td ><button class="btn btn-sm bg-yellow-gold" style="color:#FFF;" onclick="messageInfo(this)">详情</button><button class="btn btn-sm bg-grey-silver" style="background:#a0a0a0;color:#FFF;"   onclick="deleteMessage(this)" >删除</button></td>'
                                                                                    + "<tr>"
                                                                                    );
                                                                        });
                                                                        total = data.resuData.total;
                                                                        totalPage = Math.ceil(total * 0.1);
                                                                        if (total == 0) {
                                                                            $('#game_list').jqPaginator('destroy');
                                                                        } else {
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


                                                function deleteMessage(del) {
                                                    var txt = "您确定要删除?";
                                                    var option = {
                                                        title: "您确定要删除?",
                                                        btn: parseInt("0011", 2),
                                                        onOk: function () {
                                                            var mid = $(del).parents("tr").attr('id');
                                                            $.ajax({
                                                                url: $conf.message.api + 'deleteMessage.php',
                                                                data: {
                                                                    mid: mid,
                                                                    uid:getCookie("admin_uid"),
                                                                    encpass:getCookie('admin_enc'),
                                                                    type: getCookie('admin_type')
                                                                },
                                                                type: 'POST',
                                                                dataType: 'json',
                                                                cache: false,
                                                                success: function (data) {
                                                                    if (data.stat == '1') {
                                                                        if (data.resuData == '1') {
                                                                            $("#gamebodys [id=" + mid + "]").remove();
                                                                            var txt = "删除消息成功!";
                                                                            window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.success);
                                                                            setTimeout(function () {
                                                                                $(".xcConfirm").remove();
                                                                            }, 1000);
                                                                        } else {
                                                                            var txt = "系统繁忙,请稍后再试!";
                                                                            window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.error);
                                                                        }

                                                                    } else {
                                                                        var txt = "系统繁忙,请稍后再试!";
                                                                        window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.error);
                                                                    }
                                                                },
                                                                error: function () {

                                                                }

                                                            });
                                                        }
                                                    }
                                                    window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.warning, option);
                                                }
                                                $("#search").on('click', function () {
                                                    var keyword = $("#keyword").attr('value');
                                                    if (keyword == "") {
                                                        var txt = "搜索关键字不能为空";
                                                        window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.confirm);
                                                        return;
                                                    }
                                                    $.jqPaginator('#game_list', {
                                                        totalPages: 10,
                                                        visiblePages: 10,
                                                        currentPage: 1,
                                                        onPageChange: function (num, type) {
                                                            $("#gamebodys tr").remove();
                                                            $.ajax({
                                                                url: $conf.message.api + 'messageList.php',
                                                                dataType: 'json',
                                                                data: {
                                                                    keyword: keyword,
                                                                    uid:getCookie("admin_uid"),
                                                                    encpass:getCookie('admin_enc'),
                                                                    type: getCookie('admin_type')
                                                                },
                                                                type: 'POST',
                                                                cache: false,
                                                                success: function (data) {
                                                                    if (data) {
                                                                        $.each(data.resuData.list, function (i, item) {
                                                                            $("#gamebodys").append(
                                                                                    "<tr id=" + item.id + ">"
                                                                                    + "<td >" + item.id + "</td>"
                                                                                    + "<td >" + item.title + "</td>"
                                                                                    + "<td>" + item.msg + "</td>"
                                                                                    + "<td>" + item.stime + "</td>"
                                                                                    + '<td><button class="btn  btn-sm bg-yellow-gold"  style="color:#FFF;" onclick="messageInfo(this)">详情</button><button class="btn  btn-sm bg-grey-silver" style="background:#a0a0a0;color:#FFF;"  onclick="gameDelete(this)" >删除</button></td>'
                                                                                    + "<tr>"
                                                                                    );
                                                                        });
                                                                        total = data.resuData.total;
                                                                        totalPage = Math.ceil(total * 0.1);
                                                                        if (total == 0) {
                                                                            $('#game_list').jqPaginator('destroy');
                                                                            $("#msg_text").find("h2").text('');
                                                                            $('#msg_text').append("<h2>暂无相关数据!<h2>");
                                                                        } else {
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
                                                function enterSearch(keyword) {
                                                    if (keyword == '') {
                                                        return;
                                                    }
                                                    $.jqPaginator('#game_list', {
                                                        totalPages: 10,
                                                        visiblePages: 10,
                                                        currentPage: 1,
                                                        onPageChange: function (num, type) {
                                                            $("#gamebodys tr").remove();
                                                            $.ajax({
                                                                url: $conf.message.api + 'messageList.php',
                                                                dataType: 'json',
                                                                data: {
                                                                    keyword: keyword,
                                                                    uid:getCookie("admin_uid"),
                                                                    encpass:getCookie('admin_enc'),
                                                                    type: getCookie('admin_type')
                                                                },
                                                                type: 'POST',
                                                                cache: false,
                                                                success: function (data) {
                                                                    if (data) {
                                                                        $.each(data.resuData.list, function (i, item) {
                                                                            $("#gamebodys").append(
                                                                                    "<tr id=" + item.id + ">"
                                                                                    + "<td >" + item.id + "</td>"
                                                                                    + "<td >" + item.title + "</td>"
                                                                                    + "<td>" + item.msg + "</td>"
                                                                                    + "<td>" + item.stime + "</td>"
                                                                                    + '<td><button "btn btn-sm bg-yellow-gold"  style="color:#FFF;" onclick="messageInfo(this)">详情</button><button class="btn btn-sm bg-grey-silver" style="background:#a0a0a0;color:#FFF; "  onclick="gameDelete(this)" >删除</button></td>'
                                                                                    + "<tr>"
                                                                                    );
                                                                        });
                                                                        total = data.resuData.total;
                                                                        totalPage = Math.ceil(total * 0.1);
                                                                        if (total == 0) {
                                                                            $('#game_list').jqPaginator('destroy');
                                                                            $("#msg_text").find("h2").text('');
                                                                            $('#msg_text').append("<h2>无相关数据!<h2>");
                                                                        } else {
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
                                                $("#addNewMsg").on('click', function () {
                                                    $("#addnew").modal("toggle");
                                                });
                                                $("#add").on('click', function () {
                                                    var title = $("#add_title").val();
                                                    var msg = $("#add_msg").val();
                                                    if (title == '') {
                                                        alert('标题不能为空~');
                                                        return;
                                                    }
                                                    if (msg == '') {
                                                        alert('内容不能为空~');
                                                        return;
                                                    }
                                                    $.ajax({
                                                        url: $conf.message.api + 'addMessage.php',
                                                        dataType: 'json',
                                                        type: 'POST',
                                                        data: {
                                                            title: title,
                                                            msg: msg,
                                                            uid:getCookie("admin_uid"),
                                                            encpass:getCookie('admin_enc'),
                                                            type: getCookie('admin_type')
                                                        },
                                                        cache: false,
                                                        success: function (data) {
                                                            if (data.resuData == '1') {
                                                                $("#addnew").modal("toggle");
                                                                var txt = "发送成功!";
                                                                window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.success);
                                                                getMessageList();
                                                                setTimeout(function () {
                                                                    $(".xcConfirm").remove();
                                                                }, 1000);

                                                            }
                                                        },
                                                        error: function () {

                                                        }

                                                    });
                                                });
                                                function  messageInfo(upd) {
                                                    $("#gametype").empty();
                                                    var mid = $(upd).parents("tr").attr("id");
                                                    $.ajax({
                                                        url: $conf.message.api + 'messageInfo.php',
                                                        dataType: 'json',
                                                        type: 'POST',
                                                        data: {
                                                            mid: mid,
                                                            uid:getCookie("admin_uid"),
                                                            encpass:getCookie('admin_enc'),
                                                            type: getCookie('admin_type')
                                                        },
                                                        cache: false,
                                                        success: function (data) {
                                                            if (data.stat == '1') {
                                                                $.each(data.resuData, function (i, item) {
                                                                    $("#title").val(item.title);
                                                                    $("#msg_info").text(item.msg);
                                                                });
                                                                $("#mymodal").modal("toggle");
                                                            }
                                                        },
                                                        error: function () {

                                                        }

                                                    });
                                                }

                </script>
                </body>
                </html>

<?php
/**
 * 游戏管理列表
 * Author yandong@6room.com
 * Dat 2016-6-20 11:41
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
        <title>游戏列表管理</title>
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
                                        <h4 class="page-title">游戏列表</h4>
                                    </div>
                                    <div class="tools">

                                        <form class="navbar-form pull-right"  onsubmit="return false;">         
                                            <input type="text" class="form-control" id='keyword' placeholder="" onkeypress="if (event.keyCode == 13) {
                                                        enterSearch(keyword.value);
                                                    }">
                                            <button type="button" class="btn bg-yellow-gold" id='search'/>搜索</button>&nbsp;&nbsp;&nbsp;
                                            <button type="button" class="btn bg-yellow-gold" id="addGame">+新增游戏</button>
                                        </form>  
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th width="30px">gameId</th>
                                                <th width="30px">gameTid</th>
                                                <th width="120px">游戏名称</th>
                                                <th>封面图</th>
                                                <th>ICON</th>
                                                <th>背景图</th>
                                                <th width="100px">时间</th>
                                                <th width="400px">描述</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody id="gamebodys">
                                        </tbody>
                                    </table>
                                    <nav style="text-align: center" id="game_text">
                                        <ul class="pagination" id="game_list"></ul>                                      
                                    </nav>

                                </div>
                            </div>
                            <!-- END PORTLET-->
                        </div>
                    </div>
                </div>                

                <div class="modal" id="mymodal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <h4 class="modal-title">修改游戏</h4>
                            </div>
                            <div class="modal-body">
                                <div class="portlet-body form">
                                    <!-- BEGIN FORM-->
                                    <form action="#" class="form-horizontal form-bordered" id="game_img">
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">游戏名称<span class="required">
                                                        * </span></label>
                                                <div class="col-md-4">
                                                    <input class="form-control" id="game_name" type="text" readOnly="true" />
                                                    <input id="game_id" type="hidden" value="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3">游戏类型 <span class="required">
                                                        * </span>
                                                </label>
                                                <div class="col-md-4">
                                                    <select class="form-control" name="selector" id="gametype">

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label class="control-label col-md-3">封面图</label>
                                                <div class="col-md-9">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview " id="upposter" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                            <img id="older_poster" height='150' width='200' src="">
                                                        </div>
                                                        <div>
                                                            <input type="file"  id="poster_img" name="file">   
                                                            <img style="display:none;" id="new_pic" src=""/>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label class="control-label col-md-3">ICON图</label>
                                                <div class="col-md-9">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview " id="upicon" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                            <img id="older_icon" height='150' width='200' src="">

                                                        </div>                                                        
                                                        <div>
                                                            <input type="file" id="icon_img" name="file">  
                                                            <img style="display:none;" id="new_icon" src=""/>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label class="control-label col-md-3">背景图</label>
                                                <div class="col-md-9">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview " id="upbackpic" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                            <img id="older_bg" height='150' width='200' src="">

                                                        </div>

                                                        <div>
                                                            <input type="file" id="bg_img" name="file"> 
                                                            <img style="display:none;" id="new_bg" src=""/>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="form-group last">
                                                <label class="control-label col-md-3">描述</label>
                                                <div class="col-md-9">
                                                    <textarea id="maxlength_textarea" class="form-control" maxlength="225" rows="2" placeholder="请输入0-225个字符"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                    <!-- END FORM  demo-->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="pass" class="btn btn-primary bg-yellow-gold">确定</button>
                                    <button type="button" class="btn btn-default bg-yellow-silver" data-dismiss="modal">关闭</button>
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
                                                document.onkeydown = function (event) {
                                                    var e = event || window.event || arguments.callee.caller.arguments[0];
                                                    if (e && e.keyCode == 13) {
                                                        
                                                    }
                                                };

                                                $(document).ready(function () {
                                                    getGameList();
                                                });

                                                function getGameList() {
                                                    $.jqPaginator('#game_list', {
                                                        totalPages: 10,
                                                        visiblePages: 10,
                                                        currentPage: 1,
                                                        onPageChange: function (num, type) {
                                                            $("#gamebodys tr").remove();
                                                            $.ajax({
                                                                url: $conf.game.api + 'gameList.php',
                                                                dataType: 'json',
                                                                data: {
                                                                    page:num,
                                                                    uid:getCookie("admin_uid"),
                                                                    encpass:getCookie('admin_enc'),
                                                                    type: getCookie('admin_type')
                                                                },
                                                                type: 'POST',
                                                                cache: false,
                                                                success: function (data) {
                                                                    if (data) {
                                                                        $.each(data.data, function (i, item) {
                                                                            $("#gamebodys").append(
                                                                                    "<tr id=" + item.gid + ">"
                                                                                    + "<td>" + item.gid + "</td>"
                                                                                    + "<td>" + item.gtid + "</td>"
                                                                                    + "<td>" + item.name + "</td>"
                                                                                    + "<td><img  height='50' width='50' src=" + item.pic + "></td>"
                                                                                    + "<td><img  height='50' width='50' src=" + item.icon + "></td>"
                                                                                    + "<td><img  height='50' width='50' src=" + item.bg + "></td>"
                                                                                    + "<td>" + item.ctime + "</td>"
                                                                                    + "<td>" + item.desc + "</td>"
                                                                                    + '<td><button class="btn btn-info btn-sm bg-yellow-gold" onclick="gameUpdate(this)">修改</button><button class="btn btn-info btn-sm bg-grey-silver"  onclick="gameDelete(this)" >删除</button></td>'
                                                                                    + "<tr>"
                                                                                    );
                                                                        });
                                                                        total = data.total;
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


                                                function gameDelete(del) {
                                                    var txt = "您确定要删除?";
                                                    var option = {
                                                        title: "您确定要删除?",
                                                        btn: parseInt("0011", 2),
                                                        onOk: function () {
                                                            var gid = $(del).parents("tr").attr('id');
                                                            $.ajax({
                                                                url: $conf.game.api + 'deleteGame.php',
                                                                data: {
                                                                    gameid: gid,
                                                                    uid:getCookie("admin_uid"),
                                                                    encpass:getCookie('admin_enc'),
                                                                    type: getCookie('admin_type')
                                                                },
                                                                type: 'POST',
                                                                dataType: 'json',
                                                                cache: false,
                                                                success: function (data) {
                                                                    if (data.data == '1') {
                                                                        $("#gamebodys [id=" + gid + "]").remove();
                                                                        var txt = "删除游戏成功!";
                                                                        window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.success);
                                                                        setTimeout(function () {
                                                                            $(".xcConfirm").remove();
                                                                        }, 1000);
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
                                                    var game = $("#keyword").attr('value');
                                                    if (game == "") {
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
                                                                url: $conf.game.api + 'gameList.php',
                                                                dataType: 'json',
                                                                data: {
                                                                    gameName: game,
                                                                    uid:getCookie("admin_uid"),
                                                                    encpass:getCookie('admin_enc'),
                                                                    type: getCookie('admin_type')
                                                                },
                                                                type: 'POST',
                                                                cache: false,
                                                                success: function (data) {
                                                                    if (data) {
                                                                        $.each(data.data, function (i, item) {
                                                                            $("#gamebodys").append(
                                                                                    "<tr id=" + item.gid + ">"
                                                                                    + "<td>" + item.gid + "</td>"
                                                                                    + "<td>" + item.gtid + "</td>"
                                                                                    + "<td>" + item.name + "</td>"
                                                                                    + "<td><img  height='50' width='50' src=" + item.pic + "></td>"
                                                                                    + "<td><img  height='50' width='50' src=" + item.icon + "></td>"
                                                                                    + "<td><img  height='50' width='50' src=" + item.bg + "></td>"
                                                                                    + "<td>" + item.ctime + "</td>"
                                                                                    + "<td>" + item.desc + "</td>"
                                                                                    + '<td><button class="btn btn-info btn-sm bg-yellow-gold" onclick="gameUpdate(this)">修改</button><button class="btn btn-info btn-sm bg-grey-silver"  onclick="gameDelete(this)" >删除</button></td>'
                                                                                    + "<tr>"
                                                                                    );
                                                                        });
                                                                        total = data.total;
                                                                        totalPage = Math.ceil(total * 0.1);
                                                                        if (total == '0') {
                                                                            $('#game_list').jqPaginator('destroy');
                                                                            $("#game_text").find("h2").text('');
                                                                            $('#game_text').append("<h2>暂无相关数据!<h2>");
//                                                $("#gamebodys p").remove();
//                                                $("#gamebodys").append(
//                                                      
//                                                        );
                                                                        } else {
                                                                            $("#game_text").find("h2").text('');
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

                                                function enterSearch(game) {
                                                    if (game == '') {
                                                        return;
                                                    }
                                                    $.jqPaginator('#game_list', {
                                                        totalPages: 10,
                                                        visiblePages: 10,
                                                        currentPage: 1,
                                                        onPageChange: function (num, type) {
                                                            $("#gamebodys tr").remove();
                                                            $.ajax({
                                                                url: $conf.game.api + 'gameList.php',
                                                                dataType: 'json',
                                                                data: {
                                                                    gameName: game,
                                                                    uid:getCookie("admin_uid"),
                                                                    encpass:getCookie('admin_enc'),
                                                                    type: getCookie('admin_type')
                                                                },
                                                                type: 'POST',
                                                                cache: false,
                                                                success: function (data) {
                                                                    if (data.data) {
                                                                        $.each(data.data, function (i, item) {          
                                                                            $("#gamebodys").append(
                                                                                    "<tr id=" + item.gid + ">"
                                                                                    + "<td>" + item.gid + "</td>"
                                                                                    + "<td>" + item.name + "</td>"
                                                                                    + "<td><img  height='50' width='50' src=" + item.pic + "></td>"
                                                                                    + "<td><img  height='50' width='50' src=" + item.icon + "></td>"
                                                                                    + "<td><img  height='50' width='50' src=" + item.bg + "></td>"
                                                                                    + "<td>" + item.ctime + "</td>"
                                                                                    + "<td>" + item.desc + "</td>"
                                                                                    + '<td><button class="btn btn-info btn-sm bg-yellow-gold" onclick="gameUpdate(this)">修改</button><button class="btn btn-info btn-sm bg-grey-silver"  onclick="gameDelete(this)" >删除</button></td>'
                                                                                    + "<tr>"
                                                                                    );
                                                                        });
                                                                        total = data.total;
                                                                        totalPage = Math.ceil(total * 0.1);
                                                                        if (total == '0') {
                                                                            $('#game_list').jqPaginator('destroy');
                                                                            $("#game_text").find("h2").text('');
                                                                            $('#game_text').append("<h2>暂无相关数据!<h2>");
                                                                        } else {
                                                                            $("#game_text").find("h2").text('');
                                                                            $('#game_list').jqPaginator('option', {
                                                                                totalCounts: parseInt(total),
                                                                                totalPages: totalPage
                                                                            });
                                                                        }
                                                                    }else{
                                                                    alert('no');
                                                                    }
                                                                },
                                                                error: function () {

                                                                }

                                                            });

                                                        }
                                                    });
                                                }



                                                $("#addGame").on('click', function () {
                                                    location.href = "./addGame.php";
                                                });
                                                function  gameUpdate(upd) {
                                                    $("#gametype").empty();
                                                    $("#new_pic").attr("src", '');
                                                    $("#new_icon").attr("src", '');
                                                    $("#new_bg").attr("src", '');
                                                    $.ajax({
                                                        url: $conf.game.api + 'getGameTypeList.php',
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
                                                                $.each(data.data, function (i, item) {
                                                                    $("#gametype").append(
                                                                        '<option value= ' + item.gametid + '>' + item.name + '</option>'
                                                                    );
                                                                });
                                                            }
                                                            var gameid = $(upd).parents("tr").attr("id");
                                                            var gametid = $(upd).parents("tr").find("td:eq(1)").html();
                                                            var name = $(upd).parents("tr").find("td:eq(2)").html();
                                                            var pic = $(upd).parents("tr").find("td:eq(3) img").attr("src");
                                                            var icon = $(upd).parents("tr").find("td:eq(4) img").attr("src");
                                                            var bg = $(upd).parents("tr").find("td:eq(5) img").attr("src");
                                                            var desc = $(upd).parents("tr").find("td:eq(7)").html();
                                                            $("#gametype").find('option[value=' + gametid + ']').attr("selected", true);
                                                            $("#mymodal").modal("toggle");
                                                            $("#game_name").val(name);
                                                            $("#game_id").val(gameid);
                                                            $("#maxlength_textarea").val(desc);
                                                            $("#upposter").find("img").attr('src', pic);
                                                            $("#upicon").find("img").attr('src', icon);
                                                            $("#upbackpic").find("img").attr('src', bg);
                                                        },
                                                        error: function () {

                                                        }

                                                    });
                                                }

                                                function getObjectURL(file) {
                                                    var url = null;
                                                    if (window.createObjectURL != undefined) { // basic
                                                        url = window.createObjectURL(file);
                                                    } else if (window.URL != undefined) { // mozilla(firefox)
                                                        url = window.URL.createObjectURL(file);
                                                    } else if (window.webkitURL != undefined) { // webkit or chrome
                                                        url = window.webkitURL.createObjectURL(file);
                                                    }
                                                    return url;
                                                }
                                                $("#poster_img").change(function () {
                                                    var objUrl = getObjectURL(this.files[0]);
                                                    if (objUrl) {
                                                        $("#older_poster").attr("src", objUrl);
                                                        $("#game_img").ajaxSubmit({
                                                            url: $conf.tool.api + 'upLoadPic.php',
                                                            //url: 'http://dev.huanpeng.com/admin2/api/tool/upLoadPic.php',
                                                            type: 'post',
                                                            dataType: 'json',
                                                            data:{
                                                                uid:getCookie("admin_uid"),
                                                                encpass:getCookie('admin_enc'),
                                                                type: getCookie('admin_type')
                                                            },
                                                            success: function (d) {
                                                                if (d.code == 1) {
                                                                    $("#new_pic").attr("src", d.data);
                                                                } else {
                                                                    alert("上传图片失败~");
                                                                }

                                                            }
                                                        });
                                                    }
                                                })
                                                $("#icon_img").change(function () {
                                                    var objUrl = getObjectURL(this.files[0]);
                                                    if (objUrl) {
                                                        $("#older_icon").attr("src", objUrl);
                                                        $("#game_img").ajaxSubmit({
                                                            url: $conf.tool.api + 'upLoadPic.php',
                                                            //url: 'http://dev.huanpeng.com/admin2/api/tool/upLoadPic.php',
                                                            type: 'post',
                                                            dataType: 'json',
                                                            data:{
                                                                uid:getCookie("admin_uid"),
                                                                encpass:getCookie('admin_enc'),
                                                                type: getCookie('admin_type')
                                                            },
                                                            success: function (d) {
                                                                console.log(d.data);
                                                                if (d.code == 1) {
                                                                    $("#new_icon").attr("src", d.data);
                                                                } else {
                                                                    alert("上传图片失败~");
                                                                }
                                                            }
                                                        });
                                                    }
                                                });
                                                $("#bg_img").change(function () {
                                                    var objUrl = getObjectURL(this.files[0]);
                                                    if (objUrl) {
                                                        $("#older_bg").attr("src", objUrl);
                                                        $("#game_img").ajaxSubmit({
                                                            url: $conf.tool.api + 'upLoadPic.php',
                                                            //url: 'http://dev.huanpeng.com/admin2/api/tool/upLoadPic.php',
                                                            type: 'post',
                                                            dataType: 'json',
                                                            data:{
                                                                uid:getCookie("admin_uid"),
                                                                encpass:getCookie('admin_enc'),
                                                                type: getCookie('admin_type')
                                                            },
                                                            success: function (d) {
                                                                console.log(d.data);
                                                                if (d.code == 1) {
                                                                    $("#new_bg").attr("src", d.data);
                                                                } else {
                                                                    alert("上传图片失败~");
                                                                }

                                                            }
                                                        });
                                                    }
                                                });
                                                $("#pass").on('click', function () {
                                                    var gid = $("#game_id").val();
                                                    var gtype = $("#gametype").val();
                                                    var poster = $("#new_pic").attr("src");
                                                    var icon = $("#new_icon").attr("src");
                                                    var bg = $("#new_bg").attr("src");
                                                    var desc = $("#maxlength_textarea").val();
                                                    $.ajax({
                                                        url: $conf.game.api + 'updateGame.php',
                                                        type: 'post',
                                                        dataType: 'json',
                                                        data: {
                                                            uid: getCookie('admin_uid'),
                                                            encpass: getCookie('admin_enc'),
                                                            type: getCookie('admin_type'),
                                                            gid: gid,
                                                            gtype: gtype,
                                                            poster: poster,
                                                            icon: icon,
                                                            bg: bg,
                                                            desc: desc
                                                        },
                                                        success: function (d) {
                                                            if (d.data) {
                                                                $("#mymodal").modal("toggle");
                                                                alert('修改成功');
                                                                cancelInit();
                                                            } else {
                                                                alert('修改失败,请稍候重试!');
                                                            }
                                                        }
                                                    });
                                                });
                </script>
                </body>
                </html>
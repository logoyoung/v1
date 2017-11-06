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
        <title>游戏类型管理</title>
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
                                        <h4 class="page-title">游戏类列表</h4>
                                    </div>
                                    <div class="tools">
                                        <button type="button" class="btn bg-yellow-gold" id="addGameType">+新增类型</button>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover line-height">
                                        <thead>
                                            <tr>
                                                <th width="2%">gameTid</th>
                                                <th width="10%">名称</th>                       
                                                <th width="20%">ICON</th>
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
                <div class="modal" id="mymodal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <h4 class="modal-title">添加游戏类型</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="portlet-body form">
                                        <!-- BEGIN FORM-->
                                        <form id="upicon" name="upicon" action="../api/tool/upLoadPic.php" method="post" enctype="multipart/form-data" class="form-horizontal form-row-seperated">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">游戏类型名称<span class="required">
                                                            * </span></label>
                                                    <div class="col-md-5">
                                                        <input class="form-control" id="mask_tin" type="text"/>
                                                    </div>
                                                </div>               
                                                <div class="form-group ">
                                                    <label class="control-label col-md-3">ICON图</label>

                                                    <div class="col-md-9">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <img id="imgShow" src="" width="200" height="150" />
                                                            <img style="display:none;" id="uploadback" src=""/>

                                                            <div>
                                                                <input type="file"  id="Icon_pic" name="file"> <br/> 
                                                                <!--<button type="button"  id="form-submit" class="btn btn-primary">上传图片</button>-->
                                                            </div>
                                                        </div>
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
                                <button type="button"  id="addnew" class="btn btn-primary">保存</button>
                            </div>

                        </div> 
                    </div> 
                </div> 

                <div class="modal" id="upmodal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <h4 class="modal-title">修改游戏类型</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="portlet-body form">
                                        <!-- BEGIN FORM-->
                                        <form action="#" class="form-horizontal form-bordered"  id="up_new">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">游戏类型名称<span class="required">
                                                            * </span></label>
                                                    <div class="col-md-5">
                                                        <input class="form-control" id="up_gname" type="text" readOnly="true" />
                                                    </div>
                                                </div>               
                                                <div class="form-group ">
                                                    <label class="control-label col-md-3">ICON图</label>
                                                    <div class="col-md-9">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <img id="uppic" src="" width="200" height="150" />
                                                            <img style="display:none;" id="new_back" src=""/>
                                                            <input style="display:none;"  id="gid" type="text"/>
                                                            <div>
                                                                <input type="file"  id="up_new_pic" name="file"> <br/> 
                                                                <!--<button type="button"  id="up_form-submit" class="btn btn-primary">上传图片</button>-->
                                                            </div>
                                                        </div>                                                
                                                    </div>
                                                </div>    
                                            </div>
                                        </form>
                                        <!-- END FORM-->
                                    </div>
                                    <!--</div>-->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="up_new_form" class="btn bg-yellow-gold" style="color:#FFF">保存</button>
                                <button type="button" class="btn bg-grey-silver"  style="background:#a0a0a0;color:#FFF;" data-dismiss="modal">关闭</button>

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
                    $("#addnew").on('click', function () {
                        var typename = $("#mask_tin").val();
                        var img = $("#uploadback").attr("src");
                        if (typename == '') {
                            alert("游戏类型名称不能为空!");
                            return;
                        }
                        if (img == '') {
                            alert("图片地址为空!");
                            return;
                        }
                        $.ajax({
                            url: $conf.game.api + 'addGameType.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                uid: getCookie('admin_uid'),
                                encpass: getCookie('admin_enc'),
                                type: getCookie('admin_type'),
                                typename: typename,
                                img: img
                            },
                            success: function (d) {
                                if (d.data) {
                                    $("#uploadback").attr("src", '');
                                    $("#imgShow").attr("src", '');
                                    $("#mymodal").modal("toggle");
                                    getGameTypeList();
                                } else {
                                    $("#uploadback").attr("src", '');
                                    $("#imgShow").attr("src", '');
                                    $("#mymodal").modal("toggle");
                                    alert("上传图片错误~");
                                }
                            }
                        });
                    });

                    $("#Icon_pic").change(function () {
                        var objUrl = getObjectURL(this.files[0]);
                        if (objUrl) {
                            $("#imgShow").attr("src", objUrl);
                            $("#upicon").ajaxSubmit({
                                url: $conf.tool.api + 'upLoadPic.php',
                                type: 'post',
                                dataType: 'json',
                                data:{
                                    uid:getCookie("admin_uid"),
                                    encpass:getCookie('admin_enc'),
                                    type: getCookie('admin_type')
                                },
                                success: function (d) {
                                    if (d.code == 1) {
                                        $("#uploadback").attr("src", d.data);
                                    } else {
                                        alert("上传图片失败~");
                                    }

                                }
                            });
                        }
                    });
                    $("#up_new_pic").change(function () {
                        var objUrl = getObjectURL(this.files[0]);
                        if (objUrl) {
                            $("#uppic").attr("src", objUrl);
                            $("#up_new").ajaxSubmit({
                                url: $conf.tool.api + 'upLoadPic.php',
                                type: 'post',
                                dataType: 'json',
                                data:{
                                    uid:getCookie("admin_uid"),
                                    encpass:getCookie('admin_enc'),
                                    type: getCookie('admin_type')
                                },
                                success: function (d) {
                                    if (d.code == 1) {
                                        $("#new_back").attr("src", d.data);
                                    } else {
                                        alert("上传图片失败~");
                                    }

                                }
                            });
                        }
                    });
                    $("#up_new_form").on('click', function () {
                        var gid = $("#gid").val();
                        var img = $("#new_back").attr("src");
                        if (gid == '') {
                            alert("游戏类型名称不能为空!");
                            return;
                        }
                        if (img == '') {
                            alert("图片地址为空!");
                            return;
                        }
                        $.ajax({
                            url: $conf.game.api + 'updateGameType.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                uid: getCookie('admin_uid'),
                                encpass: getCookie('admin_enc'),
                                type: getCookie('admin_type'),
                                gametid: gid,
                                img: img
                            },
                            success: function (d) {
                                if (d.data) {
                                    $("#uppic").attr("src", '');
                                    $("#new_back").attr("src", '');
                                    $("#upmodal").modal("toggle");
                                } else {
                                    $("#uppic").attr("src", '');
                                    $("#new_back").attr("src", '');
                                    $("#upmodal").modal("toggle");
                                    alert("上传图片错误~");
                                }
                            }
                        });
                    });

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
                    $(document).ready(function () {
                        getGameTypeList();
                    });
                    function getGameTypeList() {
                        $("#typebodys tr").remove();
                        $.ajax({
                            url: '../api/game/getGameTypeList.php',
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
                                        $("#typebodys").append(
                                                "<tr id=" + item.gametid + ">"
                                                + "<td>" + item.gametid + "</td>"
                                                + "<td>" + item.name + "</td>"
                                                + "<td><img  height='50' width='50' src=" + item.icon + "></td>"
                                                + '<td><button class="btn btn-info btn-sm bg-yellow-gold " onclick="gameTypeUpdate(this)">修改</button><button class="btn btn-info btn-sm bg-grey-silver"  onclick="gameTypeDelete(this)" >删除</button></td>'
                                                + "<tr>"
                                                );
                                    });
                                }
                            },
                            error: function () {

                            }

                        });
                    }
                    $("#addGameType").on('click', function () {
                        $("#mymodal").modal("toggle");
                    });
                    function gameTypeUpdate(upde) {
                        $("#upmodal").modal("toggle");
                        var gametid = $(upde).parents("tr").attr("id");
                        var name = $(upde).parents("tr").find("td:eq(1)").html();
                        var pic = $(upde).parents("tr").find("td:eq(2) img").attr("src");
                        $("#up_gname").val(name);
                        $("#uppic").attr('src', pic);
                        $("#gid").val(gametid);

                    }

                    function gameTypeDelete(del) {
                        var gameTid = $(del).parents("tr").attr("id");
                        var txt = "您确定要删除?";
                        var option = {
                            title: "您确定要删除?",
                            btn: parseInt("0011", 2),
                            onOk: function () {
                                $.ajax({
                                    url: '../api/game/deleteGameType.php',
                                    data: {
                                        gametid: gameTid,
                                        uid:getCookie("admin_uid"),
                                        encpass:getCookie('admin_enc'),
                                        type: getCookie('admin_type')
                                    },
                                    type: 'POST',
                                    dataType: 'json',
                                    cache: false,
                                    success: function (data) {
                                        if (data.data == '1') {
                                            getGameTypeList();
//                                            $("#gamebodys [id=" + gameTid + "]").remove();
                                            var txt = "删除游戏类型成功!";
                                            window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.success);
                                            setTimeout(function () {
                                                $(".xcConfirm").remove();
                                            }, 1000);
                                        } else {
                                            var txt = "系统繁忙,请稍后再试!";
                                            window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.error);
                                            setTimeout(function () {
                                                $(".xcConfirm").remove();
                                            }, 1000);
                                        }
                                    },
                                    error: function () {

                                    }

                                });
                            }
                        }
                        window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.warning, option);
                    }
                </script>
                </body>
                </html>
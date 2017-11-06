<?php
/**
 * 添加游戏
 * yandong@6rooms.com
 * date 2016-6-27 12:08
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
        <?php include '../module/mainStyle.php'; ?>
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-sidebar-fixed">
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
                                        <h4 class="page-title">新增游戏</h4>
                                    </div>
                                    <div class="tools">
                                    </div>
                                </div>
                                <div class="portlet-body form">
                                    <!-- BEGIN FORM-->
                                    <form action="#" id='newgame' class="form-horizontal form-bordered">
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">游戏名称<span class="required">
                                                        * </span></label>
                                                <div class="col-md-4">
                                                    <input class="form-control" id="mask_tin" type="text"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3">游戏类型 <span class="required">
                                                        * </span>
                                                </label>
                                                <div class="col-md-4">
                                                    <select class="form-control" name="select" id="gametype">

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label class="control-label col-md-3">海报</label>
                                                <div class="col-md-9">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <img id="poster_show_pic" src="" width="200" height="150" />
                                                        <img style="display:none;" id="poster_back" src=""/>
                                                        <div>
                                                            <input type="file"  id="poster_pic" name="file"> <br/> 
                                                        </div>
                                                    </div>                                                
                                                </div>
                                            </div> 
                                            <div class="form-group ">
                                                <label class="control-label col-md-3">游戏预览图</label>
                                                <div class="col-md-9">
                                                    <div class="fileinput fileinput-new"  data-provides="fileinput">
                                                        <div id="icon_show_pic_div"  >
                                                            <img id="icon_show_pic" src="" width="200" height="150" />
                                                        </div>
                                                        <div id="icon_pic_list">
                                                            <img  style="display:none;" id="icon_back" src=""/>
                                                            <input type="file"  id="icon_pic" name="file"> <br/> 
                                                        </div>
                                                    </div>    
                                                </div>
                                            </div> 
                                            <div class="form-group ">
                                                <label class="control-label col-md-3">背景图</label>
                                                <div class="col-md-9">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <img id="bg_show_pic" src="" width="200" height="150" />
                                                        <img style="display:none;" id="bg_back" src=""/>
                                                        <div>
                                                            <input type="file"  id="bg_pic" name="file"> <br/> 
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
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-3 col-md-9">
                                                    <button type="button" class="btn bg-yellow-gold"  style="color:#FFF; "id='sure_add'>提交</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- END FORM-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../module/footer.php'; ?>
        <?php include '../module/mainScript.php'; ?>
        <script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
        <script type="text/javascript" src="../common/global/scripts/common.js" ></script>
        <script>
            $(document).ready(function () {
                gameTypeList();
            });
            $("#poster_pic").change(function () {
                var objUrl = getObjectURL(this.files[0]);
                console.log(objUrl);
                if (objUrl) {
                    $("#poster_show_pic").attr("src", objUrl);
                    $("#newgame").ajaxSubmit({
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
                                $("#poster_back").attr("src", d.data);
                            } else {
                                alert("上传图片失败~");
                            }

                        }
                    });
                }
            });

            $("#icon_pic").change(function () {
                var objUrl = getObjectURL(this.files[0]);
                console.log(objUrl);
                if (objUrl) {
                    var isexit = $("#icon_show_pic").attr("src");
                    if (isexit == '') {
                        $("#icon_show_pic").attr("src", objUrl);
                    } else {
                        $("#icon_show_pic_div").append(
                                '<img  src=' + objUrl + ' width="200" height="150"/>'
                                );
                    }
                    $("#newgame").ajaxSubmit({
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
                                $("#icon_pic_list").append(
                                        '<img  src=' + d.data + ' style="display:none;" />'
                                        );
                            } else {
                                alert("上传图片失败~");
                            }

                        }
                    });
                }
            });
            $("#bg_pic").change(function () {
                var objUrl = getObjectURL(this.files[0]);
                console.log(objUrl);
                if (objUrl) {
                    $("#bg_show_pic").attr("src", objUrl);
                    $("#newgame").ajaxSubmit({
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
                                $("#bg_back").attr("src", d.data);
                            } else {
                                alert("上传图片失败~");
                            }

                        }
                    });
                }
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

            function cancelInit() {
                $("#mask_tin").val('');
                $("#poster_show_pic").attr("src", '');
                $('#poster_pic').attr("value", "");
                $("#poster_back").attr("src", '');
                $("#icon_show_pic").attr("src", '');
                $("#icon_pic").attr("value", '');
                $("#bg_show_pic").attr("src", '');
                $("#bg_pic").attr("value", '');
                $("#bg_back").attr("src", '');
                $("#maxlength_textarea").val('');
                $("#icon_pic_list img").remove();
                $("#icon_show_pic_div").find("img").nextAll().remove();

            }
            $("#sure_add").on('click', function () {
                var gamepic = '';
                var gname = $("#mask_tin").val();
                var gtype = $("#gametype").val();
                var poster = $("#poster_back").attr("src");
                var bg = $("#bg_back").attr("src");
                var desc = $("#maxlength_textarea").val();
                var domlist = $("#icon_pic_list img");
                for (var j = 0; j < domlist.length; j++) {
                    gamepic += $(domlist[j]).attr("src") + ",";
                }
                if (gname == '') {
                    alert('游戏名称不能为空!');
                    return;
                }
                if (poster == '') {
                    alert('海报不能为空!');
                    return;
                }
                $.ajax({
                    url: $conf.game.api + 'addGame.php',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        uid: getCookie('admin_uid'),
                        encpass: getCookie('admin_enc'),
                        type: getCookie('admin_type'),
                        gname: gname,
                        gtype: gtype,
                        poster: poster,
                        gamepic: gamepic,
                        bg: bg,
                        desc: desc
                    },
                    success: function (d) {
                        if (d.data) {
                            alert('添加成功');
                            cancelInit();
                            window.location.href="./gameList.php";
                        } else {
                            alert('添加失败,请稍候重试!');
                        }
                    }
                });
            });
            function gameTypeList() {
                $.ajax({
                    url: '../api/game/getGameTypeList.php',
                    dataType: 'json',
                    data: {
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
                    },
                    error: function () {

                    }

                });
            }
        </script>
    </body>
</html>
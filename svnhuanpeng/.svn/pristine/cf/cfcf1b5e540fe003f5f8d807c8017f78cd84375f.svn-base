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
        <title>经纪公司管理</title>
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
                                        <h4 class="page-title">经纪公司管理</h4>
                                    </div>
                                    <div class="tools">
                                        <button type="button" class="btn bg-yellow-gold" id="addGameType">+经纪公司</button>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover line-height">
                                        <thead>
                                            <tr>
                                                <th width="2%">CID</th>
                                                <th width="10%">名称</th>
                                                <th width="10%">类型</th>
                                                <th width="10%">当前比率</th>
                                                <th width="10%">状态</th>
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
                                <h4 class="modal-title">添加</h4>
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
                                                        <input class="form-control" id="mask_tin" type="text"/>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">类型<span class="required">
                                                            * </span></label>
                                                    <div class="col-md-4">
                                                        <select class="form-control" name="select" id="ctype">
                                                            <option value= '0'>请选择</option>
                                                            <option value= '1'>经纪公司 </option>
                                                            <option value= '2'>工会</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">所有者UID<span class="required">
                                                            </span></label>
                                                    <div class="col-md-5">
                                                        <input class="form-control" id="maskuid_tin" type="text"/>
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
                        getGameTypeList();
                    });
                    function getGameTypeList() {
                        var domUlStr = '';
                        var domLiStr = '';
                        $("#typebodys tr").remove();
                        $.ajax({
                            url: '../api/company/companyList.php',
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
                                    var data= data.resuData.list;
                                    for(var key in data){
                                        if(data[key].type ==0){
                                            var type="<span class='label label-success'>" + '官方' + "</span>";
                                        }
                                        if(data[key].type ==1){
                                            var type="<span class='label label-success'>" + '经纪公司' + "</span>";
                                        }
                                        if(data[key].type ==2){
                                            var type="<span class='label label-success'>" + '工会' + "</span>";;
                                        }
                                        if(data[key].status==0){
                                            var status="<span class='label label-success'>" + '正常' + "</span>";
                                        }else{
                                            var status="<span class='label label-danger'>" + '合同终止' + "</span>";
                                        }
                                        domLiStr =  "<tr id=" +data[key].id + ">"
                                            + "<td>" +data[key].id + "</td>"
                                            + "<td>" + data[key].name + "</td>"
                                            + "<td>" + type + "</td>"
                                            + "<td>" + data[key].rate + "</td>"
                                            + "<td>" + status + "</td>"
                                            + '<td><button class="btn btn-info btn-sm bg-grey-silver"  onclick="updateRate(this)" >修改比率</button></td>'
                                            + "<tr>"
                                        domUlStr += domLiStr;
                                    }//console.log(domUlStr);
                                    $('#typebodys').append(domUlStr);
                                }
                            },
                            error: function () {

                            }

                        });
                    }
                    $("#addGameType").on('click', function () {
                        $("#mymodal").modal("toggle");
                    });


                    function updateRate(up){
                        var name = $(up).parents("tr").find("td:eq(1)").html();
                        var cid = $(up).parents("tr").find("td:eq(0)").html();
                        var rate = $(up).parents("tr").find("td:eq(3)").html();
                        $("#cname").val(name);
                        $("#cid").val(cid);
                        $("#now_rate").val(rate);
                        $("#update_rate").modal("toggle");
                    }

                    $("#updateCompanyRate").on('click', function () {
                        var cid = $("#cid").val();
                        var rate = $("#now_rate").val();
                        if (cid == '') {
                            alert("cid不能为空!");
                            return;
                        }
                        if(rate < 0 || rate >100 || isNaN(rate)){
                            alert("请输入合法的值");
                            return;
                        }
                        $.ajax({
                            url:  '../api/company/updateCompany.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                uid: getCookie('admin_uid'),
                                encpass: getCookie('admin_enc'),
                                type: getCookie('admin_type'),
                                cid: cid,
                                rate: rate
                            },
                            success: function (d) {
                                if (d.stat) {
                                    $("#cname").attr("value", '');
                                    $("#cid").attr("value", '');
                                    $("#now_rate").val("value",'');
                                    $("#update_rate").modal("toggle");
                                    getGameTypeList();
                                } else {
                                    $("#update_rate").modal("toggle");
                                    alert("服务器繁忙，请稍后重试");
                                }
                            }
                        });
                    });





                    $("#addnew").on('click', function () {
                        var name = $("#mask_tin").val();
                        var ctype = $("#ctype").val();
                        var ownerid = $("#maskuid_tin").val();
                        if (name == '') {
                            alert("名称不能为空!");
                            return;
                        }
                        if(ctype == 0){
                            alert("请选择类型");
                            return;
                        }
                        $.ajax({
                            url:  '../api/company/addCompany.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                uid: getCookie('admin_uid'),
                                encpass: getCookie('admin_enc'),
                                type: getCookie('admin_type'),
                                name: name,
                                ctype: ctype,
                                ownerid:ownerid
                            },
                            success: function (d) {
                                if (d.stat) {
                                    $("#mask_tin").attr("value", '');
                                    $("#ctype").attr("value", '');
                                    $("#maskuid_tin").val("value",'');
                                    $("#mymodal").modal("toggle");
                                    getGameTypeList();
                                } else {
                                    $("#mymodal").modal("toggle");
                                    alert("服务器繁忙，请稍后重试");
                                }
                            }
                        });
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
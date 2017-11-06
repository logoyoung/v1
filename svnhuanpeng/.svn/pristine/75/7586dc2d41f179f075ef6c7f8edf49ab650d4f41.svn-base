<?php
/**
 * 签约管理
 * Author yandong@6room.com
 * Date 2016-6-20 11:41
 */
include '../module/checkLogin.php';

require('../lib/BrokerageCompany.class.php');
$bcompany = new BrokerageCompany();
$company = $bcompany->getList();
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8"/>
        <title>签约管理</title>
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
                                        <h4 class="page-title">签约管理</h4>
                                    </div>
                                    <div class="tools">
                                        <button type="button" class="btn bg-yellow-gold" id="addCompanyAnchor">+经纪公司主播</button>
                                        <button type="button" class="btn bg-yellow-gold" id="addGameType">+签约主播</button>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover line-height">
                                        <thead>
                                            <tr>
                                                <th width="2%">UID</th>
                                                <th width="2%">CID</th>
                                                <th width="10%">名称</th>
                                                <th width="10%">类型</th>
                                                <th width="10%">状态</th>
                                             <th width="10%">操作</th>
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
                <div class="modal" id="usermodal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <h4 class="modal-title">添加签约主播</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="portlet-body form">
                                        <!-- BEGIN FORM-->
                                        <form id="upicon" name="upicon" action="" method="post" enctype="multipart/form-data" class="form-horizontal form-row-seperated">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">主播UID<span class="required">*</span></label>
                                                    <div class="col-md-5">
                                                        <input class="form-control" id="mask_user" type="text"/>
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
                <div class="modal" id="companymodal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <h4 class="modal-title">添加经纪公司签约主播</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="portlet-body form">
                                        <!-- BEGIN FORM-->
                                        <form id="upicon" name="upicon" action="" method="post" enctype="multipart/form-data" class="form-horizontal form-row-seperated">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">经纪公司CID<span class="required">*</span></label>
                                                    <div class="col-md-5">
                                                        <select id='company_id' class="table-group-action-input input-inline input-small input-sm">
                                                            <option value="">选择经纪公司</option>
                                                            <?php foreach($company as $k=>$v) { ?>
                                                            <option value="<?=$v['id']; ?>"><?=$v['name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">主播UID<span class="required">*</span></label>
                                                    <div class="col-md-5">
                                                        <input class="form-control" id="user_id" type="text"/>
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
                                <button type="button"  id="useraddnew" class="btn btn-primary">保存</button>
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
                        var domLiStr = '';
                        $.jqPaginator('#game_list', {
                            totalPages: 10,
                            visiblePages: 10,
                            currentPage: 1,
                            onPageChange: function (num, type) {
                                $("#typebodys tr").remove();
                        $.ajax({
                            url: '../api/contract/contractList.php',
                            dataType: 'json',
                            data:{
                                uid:getCookie("admin_uid"),
                                encpass:getCookie('admin_enc'),
                                type: getCookie('admin_type'),
                                page:num,
                                size:10
                            },
                            type: 'POST',
                            cache: false,
                            success: function (data) {
                                var domUlStr = '';
                                if (data) {
                                    var total = data.resuData.total;
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
                                        domLiStr = "<tr id=" + data[key].uid + ">"
                                            + "<td>" + data[key].uid+ "</td>"
                                            + "<td>" + data[key].cid + "</td>"
                                            + "<td>" + data[key].name + "</td>"
                                            + "<td>" + type + "</td>"
                                            + "<td>" + status + "</td>"
                                            + '<td><button class="btn btn-info btn-sm bg-grey-silver"  onclick="signDelete(this)" >取消签约</button></td>'
                                            + "<tr>";
                                        domUlStr += domLiStr;
                                    }//console.log(domUlStr);
                                    $('#typebodys').html(domUlStr);
                                    totalPage = Math.ceil(total * 0.1);
                                    if (total == 0) {
                                        $('#game_list').jqPaginator('destroy');
                                        $("#msg_text").find("h2").text('');
                                        $('#msg_text').append("<h2>未找到相关数据!<h2>");
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
                    $("#addGameType").on('click', function () {
                        $("#usermodal").modal("toggle");
                    });
                    $("#addCompanyAnchor").on('click', function () {
                        $("#companymodal").modal("toggle");
                    });

                    $("#useraddnew").on('click', function () {
                        var cid = $("#company_id").val();
                        var uuid = $("#user_id").val();

                        if (isNaN(cid)  || cid =='') {
                            alert("经纪公司CID为空或非数字!");
                            return;
                        }
                        if (isNaN(uuid)  || uuid =='') {
                            alert("用户UID为空或非数字!");
                            return;
                        }
                        $.ajax({
                            url:  '../api/contract/signContract.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                uid: getCookie('admin_uid'),
                                encpass: getCookie('admin_enc'),
                                type: getCookie('admin_type'),
                                addtype: 0,
                                uuid: uuid,
                                cid:cid
                            },
                            success: function (d) {
                                if (d.stat) {
                                    $("#company_id").attr("value",'');
                                    $("#user_id").attr("value",'');
                                    $("#companymodal").modal("toggle");
                                    getGameTypeList();
                                } else {
                                    $("#companymodal").modal("toggle");
                                    alert(d.err.desc);
                                }
                            }
                        });
                    });

                    $("#addnew").on('click', function () {
                        var uuid = $("#mask_user").val();
                        if (isNaN(uuid)  || uuid =='') {
                            alert("用户UID为空或非数字!");
                            return;
                        }
                        $.ajax({
                            url:  '../api/contract/signContract.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                uid: getCookie('admin_uid'),
                                encpass: getCookie('admin_enc'),
                                type: getCookie('admin_type'),
                                addtype: 1,
                                uuid: uuid,
                                cid:0
                            },
                            success: function (d) {
                                if (d.stat) {
                                    $("#mask_user").attr("value",'');
                                    $("#usermodal").modal("toggle");
                                    getGameTypeList();
                                } else {
                                    $("#usermodal").modal("toggle");
                                    alert(-1049);
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
                    function signDelete(del) {
                        var uuid = $(del).parents("tr").attr("id");
                        var txt = "您确定要取消?";
                        var option = {
                            title: "您确定要取消?",
                            btn: parseInt("0011", 2),
                            onOk: function () {
                                $.ajax({
                                    url: '../api/contract/unsignContract.php',
                                    data: {
                                        uid: getCookie('admin_uid'),
                                        encpass: getCookie('admin_enc'),
                                        type: getCookie('admin_type'),
                                        uuid: uuid
                                    },
                                    type: 'POST',
                                    dataType: 'json',
                                    cache: false,
                                    success: function (data) {
                                        if (data.stat == '1') {
                                            getGameTypeList();
//                                            $("#gamebodys [id=" + gameTid + "]").remove();
                                            var txt = "取消成功!";
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
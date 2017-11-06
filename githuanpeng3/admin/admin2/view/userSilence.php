<?php
/**
 * 游戏类型
 * Author yandong@6room.com
 * Date 2016-6-20 11:41
 */
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);
include '../module/checkLogin.php';
$title = '禁言列表';
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
    <title><?=$title?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <?php include '../module/mainStyle.php'; ?>
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
        input, optgroup, select, textarea {
            color: #333;
        }
    </style>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content page-style-square content_body page-sidebar-fixed">
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
                                <h4 class="page-title"><?=$title?>(<span id="total"></span>)条</h4>
                            </div>
                            <div class="tools">
                                每页显示<input type="text" class="input-small input-sm" id='perpage' value="10" />条
								<input type="text" class="input-small input-sm" id='luid' placeholder="用户ID" />
                                <input type="text" class="input-small input-sm" id='nickname' placeholder="用户昵称" />
								<input type="text" class="input-small input-sm" id='roomid' placeholder="禁言房间" />
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
									<th width="10%">禁言房间</th>
                                    <th width="10%">禁言开始时间</th>
                                    <th width="10%">禁言结束时间</th>
                                    <th width="10%">原因</th>
                                    <th width="10%">操作人ID</th>
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

        <?php include '../module/footer.php'; ?>
        <?php include '../module/mainScript.php'; ?>
        <script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
        <script type="text/javascript" src="../common/global/plugins/xcConfirm.js"></script>
        <script type="text/javascript" src="../common/global/scripts/common.js"></script>
        <script type="text/javascript">


$(document).ready(function () {
    getGameTypeList();
});
var page = 1;
function getGameTypeList() {
    $("#typebodys tr").remove();
    $.jqPaginator('#game_list', {
        totalPages: 10,
        visiblePages: 10,
        currentPage: 1,
        onPageChange: function (num, type) {
			page = num;
            getData(num, type);
        }
    });
}

$("#search").on('click', function () {
    $.jqPaginator('#game_list', {
        totalPages: 10,
        visiblePages: 10,
        currentPage: 1,
        onPageChange: function (num, type) {
			page = num;
            $("#typebodys tr").remove();
            getData(num, type);
        }
    });

});

function getData(num, type)
{
    var nickname = $("#nickname").val();
    var roomid = $("#roomid").val();
    var luid = $("#luid").val();
    var size = $("#perpage").val();
    $.ajax({
        url: '../api/user/silenceGet.php',
        dataType: 'json',
        data: {
            uid : getCookie("admin_uid"),
            encpass : getCookie('admin_enc'),
            type : getCookie('admin_type'),
            page : num,
            size : size,
            nickname : nickname,
            roomid : roomid,
            luid : luid,
        },
        type: 'POST',
        cache: false,
        success: function (data) {
            if (data) {
                $("#typebodys").html('');
                $.each(data.resuData.list, function (i, item) {
                    $("#typebodys").append(
                        "<tr id=" + item.luid + ">"
                        + "<td>" + item.luid + "</td>"
                        + "<td>" + item.nick + "</td>"
                        + "<td><img  height='50' width='50' src=" + item.pic + "></td>"
                        + "<td>" + item.roomid + "</td>"
                        + "<td>" + item.stime + "</td>"
                        + "<td>" + item.etime + "</td>"
                        + "<td>" + item.reason + "</td>"
						+ "<td>" + item.uid + "</td>"
                        + '<td><button class="btn btn-info btn-sm bg-grey-silver" onclick="delSilence(' + item.luid + ',' + item.roomid + ')" >解除禁言</button></td>'
                        + "</tr>"
                    );
                });
                total = data.resuData.total;
                $("#total").html(total);
                totalPage = Math.ceil(total / size);
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

function delSilence(luid, roomid) {
    $.ajax({
        url: '../api/user/silenceDel.php',
        dataType: 'json',
        data: {
            uid : getCookie("admin_uid"),
            encpass : getCookie('admin_enc'),
            type : getCookie('admin_type'),
            luid : luid,
            roomid : roomid,
        },
        type: 'POST',
        cache: false,
        success: function (data) {
            if (data.stat == 1) {
                alert('解除禁言成功');
				getData(page, '');
            }
        },
        error: function () {
    
        }
    });
}
</script>
</body>
</html>
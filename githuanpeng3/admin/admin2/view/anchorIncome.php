<?php
/**
 * 游戏类型
 * Author yandong@6room.com
 * Date 2016-6-20 11:41
 */
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);
include '../module/checkLogin.php';
$month = getPastMonth();

$title = '主播时长&收益统计';
$cid = isset($_GET['cid']) ? $_GET['cid'] : 0;

require('../lib/BrokerageCompany.class.php');
$bcompany = new BrokerageCompany();
$list = $bcompany->getList();
$company = array();
foreach($list as $k=>$v) {
    $company[$v['id']] = $v['name'];
}
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
            <!--<h3 class="page-title">游戏类型列表</h3>-->
            <div class="row">
                <div class="col-md-12">

                    <div class="portlet box bg-yellow-gold">
                        <div class="portlet-title">
                            <div class="caption">
                                <h4 class="page-title"><?=$title?>(<span id="total"></span>)条</h4>
                            </div>
                            <div class="tools">
                                每页显示<input type="text" class="input-small input-sm" id='perpage' value="10" />条
								<input type="text" class="input-small input-sm" id='userid' placeholder="主播ID" />
                                <input type="text" class="input-small input-sm" id='name' placeholder="主播真实姓名" />
                                <input type="text" class="input-small input-sm" id='phone' placeholder="主播手机号" />
                                <input type="text" class="input-small input-sm" id='roomid' placeholder="主播房间号" />
                                <input type="text" class="input-small input-sm" id='nick' placeholder="输入主播昵称" />
                                <select id='cid' class="table-group-action-input input-inline input-small input-sm">
                                    <option value="-1">选择经纪公司</option>
                                    <option value="0">未签约主播</option>
                                    <?php foreach($company as $k=>$v) { ?>
                                    <option value="<?=$k; ?>"><?=$v; ?></option>
                                    <?php } ?>
                                </select>
                                <select id='datetime' class="table-group-action-input input-inline input-small input-sm">
                                    <?php foreach($month as $k=>$v) { ?>
                                    <option value="<?=$v; ?>"><?=$v; ?></option>
                                    <?php } ?>
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
                                    <!--  <th width="10%">直播收益</th>
                                    -->
                                    <th width="10%">直播时长</th>
                                    <th width="10%">直播间</th>
                                    <!--   <th width="10%">人气峰值</th>-->
                                    <th width="10%">认证日期</th>
                                    <th width="10%">首播日期</th>
                                    <th width="10%">有效天数</th>
                                    
									<th width="10%">真实姓名</th>
                                    <th width="10%">身份证号</th>
         
                                    <th width="10%">所属公司</th>
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
function getGameTypeList() {
    $("#typebodys tr").remove();
    $.jqPaginator('#game_list', {
        totalPages: 10,
        visiblePages: 10,
        currentPage: 1,
        onPageChange: function (num, type) {
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
            $("#typebodys tr").remove();
            getData(num, type);
        }
    });

});

function getData(num, type)
{
    var datetime = $("#datetime").val();
    var nick = $("#nick").val();
    var name = $("#name").val();
    var roomid = $("#roomid").val();
    var userid = $("#userid").val();
    var phone = $("#phone").val();
    var cid = $("#cid").val();
    var perpage = $("#perpage").val();
    $.ajax({
        url: '../api/income/income.php',
        dataType: 'json',
        data: {
            uid : getCookie("admin_uid"),
            encpass : getCookie('admin_enc'),
            type : getCookie('admin_type'),
            page : num,
            size : 10,
            nick : nick,
            name : name,
            roomid : roomid,
            userid : userid,
            phone : phone,
            month : datetime,
            cid : cid,
            incomeType : '0',
            order : '0',
            perpage : perpage
        },
        type: 'POST',
        cache: false,
        success: function (data) {
            if (data) {
                $("#typebodys").html('');
                $.each(data.resuData.list, function (i, item) {
                    $("#typebodys").append(
                        "<tr id=" + item.uid + ">"
                        + "<td>" + item.uid + "</td>"
                        + "<td>" + item.nick + "</td>"
                        + "<td><img  height='50' width='50' src=" + item.pic + "></td>"
                        //+ "<td>" + "<span class='label label-danger'>" + '暂无数据' + "</span>" + "</td>"
                        //+ "<td>" + item.coin + "金币 &nbsp;+&nbsp;" + item.bean + "金豆</td>"
                        + "<td>" + item.length + "</td>"
                        + "<td>" + item.roomID + "</td>"
                        //+ "<td>" + item.popularoty + "</td>"
                        + "<td>" + item.atime + "</td>"
						+ "<td>" + item.first + "</td>"
                        + "<td>" + item.valid + "</td>"
                        + "<td>" + item.realName + "</td>"
                        + "<td>'" + item.papersid + "</td>"
                        + "<td>" + item.company + "</td>"
                        + '<td><button class="btn btn-info btn-sm bg-grey-silver" onclick="showDetail(' + item.uid + ')" >查看</button></td>'
                        + "</tr>"
                    );
                });
                total = data.resuData.total;
                $("#total").html(total);
                totalPage = Math.ceil(total / perpage);
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

function showDetail(uid) {
    window.location = 'anthorCurve.php?date=' + $("#datetime").attr('value') + '&uid=' + uid;
}
</script>
</body>
</html>
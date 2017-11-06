<?php
/**
 * 实名认证认证列表
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
        <title>实名审核列表</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta content="" name="description"/>
        <meta content="" name="author"/>
        <style type="text/css">
        </style>
        <link href="../common/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet"
              type="text/css"/>
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
        <link href="../common/global/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet"
              type="text/css"/>
        <link href="../common/global/plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css"/>
        <!-- END PAGE LEVEL PLUGIN STYLES -->

        <!-- BEGIN THEME STYLES -->
        <link href="../common/global/css/components.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/css/plugins.css" rel="stylesheet" type="text/css"/>
        <link href="../common/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
        <link href="../common/admin/layout/css/themes/light.css" rel="stylesheet" type="text/css" id="style_color"/>
        <link href="../common/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
        <!-- END THEME STYLES -->

        <link href="../common/admin/pages/css/vertifyRealName.css" rel="stylesheet" type="text/css"/>
        <?php include '../module/mainStyle.php'; ?>
        <style type="text/css">
            .table th, .table td { 
                text-align: center;
                vertical-align: middle!important;
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
                                        <h4 class="page-title">实名审核</h4>
                                    </div>
                                    <div class="tools">
										<form class="navbar-form pull-right"  onsubmit="return false;">
                                            <input type="text" class="form-control" id='nickname' placeholder="昵称" />
											<input type="text" class="form-control" id='keyword' placeholder="真实姓名" />
											<button type="button" class="btn bg-yellow-gold" id='search'>搜索</button>&nbsp;&nbsp;&nbsp;
										</form>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="tabbable">
                                        <ul class="nav nav-tabs">
                                            <li class="active">
                                                <a href="#anchor_wait_tab" id="anchor_wait" data-toggle="tab">
                                                    待审核 <span class="badge badge-warning" style="background-color: #ffa04c;"></span></a>
                                            </li>
                                            <li >
                                                <a href="#anchor_pass_tab" id="anchor_pass" data-toggle="tab">
                                                    已通过 <span class="badge badge-success" style="background-color: #ffa04c;"></span></a>
                                            </li>
                                            <li>
                                                <a href="#anchor_unpass_tab" id="anchor_unpass" data-toggle="tab">
                                                    未通过 <span class="badge badge-danger" style="background-color: #ffa04c;"></span></a>
                                            </li>

                                        </ul>
                                        <div class="tab-content no-space">
                                            <div class="tab-pane active" id="anchor_wait_tab">
                                                <div class="">
                                                    <div class="col-md-12">
                                                        <div class="portlet ">
                                                            <div class="portlet-body">
                                                                <div class="row">
                                                                    <div class="realName hover-effect">
                                                                        <div class="realName-head"></div>
                                                                        <div class="realName-content row">
                                                                            <div class="col-md-3" id="baseInfo">
                                                                                <div class="anchor-info">
                                                                                    <div class="anchor-face">
                                                                                        <img src="" alt=""/>
                                                                                    </div>
                                                                                    <ul class="list-unstyled">
                                                                                        <li>
                                                                                            <i class="fa fa-user"  style="color: #a0a0a0;"></i>
                                                                                            <span></span>
                                                                                        </li>
                                                                                        <li>
                                                                                            <i class="fa fa-asterisk"  style="color: #a0a0a0;"></i>
                                                                                            <span></span>
                                                                                        </li>
                                                                                        <li>
                                                                                            <i class="fa fa-rmb"  style="color: #a0a0a0;"></i>
                                                                                            <span></span>
                                                                                        </li>
                                                                                        <li>
                                                                                            <i class="fa fa-clock-o"  style="color: #a0a0a0;"></i>
                                                                                            <span></span>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                                <div class="vertify-info">
                                                                                    <ul class="list-unstyled">
                                                                                        <li><i class="fa fa-user" style="color: #a0a0a0;"></i><span></span></li>
                                                                                        <li><i class="fa fa-list-alt"  style="color: #a0a0a0;"></i><span></span></li>
                                                                                        <li><i class="fa-calendar-o"  style="color: #a0a0a0;"></i>
                                                                                            <span></span>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6 vertify-content">
                                                                                <div id="myCarousel" class="carousel image-carousel slide" data-interval="false">
                                                                                    <div class="carousel-inner">
                                                                                        <div id="front" class="item active">
                                                                                            <img src="../static/img/identCard_front.png"/>
                                                                                            <div class="carousel-caption">
                                                                                                <h4>正面照</h4>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div id="back" class="item">
                                                                                            <img src="../static/img/identCard_back.png" alt=""/>
                                                                                            <div class="carousel-caption">
                                                                                                <h4>反面照</h4>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div id="handheld" class="item">
                                                                                            <img src="../static/img/identCard_handheld.png" alt=""/>
                                                                                            <div class="carousel-caption">
                                                                                                <h4>手持证件照</h4>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <a class="carousel-control left" href="#myCarousel" data-slide="prev">
                                                                                        <i class="m-icon-big-swapleft m-icon-white"></i>
                                                                                    </a>
                                                                                    <a class="carousel-control right" href="#myCarousel" data-slide="next">
                                                                                        <i class="m-icon-big-swapright m-icon-white"></i>
                                                                                    </a>
                                                                                </div>
                                                                                <div class="vertify-option">
                                                                                    <span id="turnDown" class="">
                                                                                        <i class="fa fa-ban"></i>
                                                                                    </span>
                                                                                    <span id="pass" class="">
                                                                                        <i class="fa fa-check-circle-o"></i>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <div class="vertify-result">
                                                                                    <div id="front-result" class="vertify-ret-view">
                                                                                        <img src="../static/img/identCard_front.png" alt=""/>
                                                                                    </div>
                                                                                    <div id="back-result" class="vertify-ret-view">
                                                                                        <img src="../static/img/identCard_back.png" alt=""/>
                                                                                    </div>
                                                                                    <div id="handheld-result" class="vertify-ret-view">
                                                                                        <img src="../static/img/identCard_handheld.png" alt=""/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="realName-footer">
                                                                            <a id="certify-submit" href="javascript:;" class="btn bg-yellow-gold" style="color:#FFF">
                                                                                提交
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="anchor_pass_tab">

                                                <table class="table table-striped table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>UID</th>
                                                            <th>昵称</th>
                                                            <th>电话号码</th>
                                                            <th>姓名</th>
                                                            <th>身份证号</th>
                                                            <th>有效期</th>
                                                            <th>正面照</th>
                                                            <th>背面照</th>
                                                            <th>手持照</th>
                                                            <th>审核时间</th>
                                                            <th>状态</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="anchor_pass_tbodys">
                                                    </tbody>
                                                </table>
                                                <nav style="text-align: center">
                                                    <ul class="pagination" id="pagination_pass"></ul>
                                                </nav>

                                            </div>
                                            <div class="tab-pane" id="anchor_unpass_tab">

                                                <table class="table table-striped table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>UID</th>
                                                            <th>昵称</th>
                                                            <th>电话号码</th>
                                                            <th>姓名</th>
                                                            <th>身份证号</th>
                                                            <th>有效期</th>
                                                            <th>正面照</th>
                                                            <th>背面照</th>
                                                            <th>手持照</th>
                                                            <th>审核时间</th>
                                                            <th>状态</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="anchor_unpass_tbodys">
                                                    </tbody>
                                                </table>
                                                <nav style="text-align: center">
                                                    <ul class="pagination" id="pagination_unpass"></ul>
                                                </nav>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
        <div class="modal" id="mymodal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                                class="sr-only">Close</span></button>
                        <h1 class="modal-title">详情</h1>
                    </div>
                    <div class="modal-body">
                        <div class="panel panel-default" id="mainbody">
                            <div class="panel-heading"><h2>UID:<strong id="uid"></strong></h2></div>


                            <div class="panel-heading"><h2>姓名:<strong id="name"></strong></h2></div>

                            <div class="panel-body">
                                <h2><span class="label label-info">正面照:</span></h2>
                                <img id="face" height='500' width='500' src="">
                            </div>
                            <div class="panel-heading"><h2>证件号:<strong id="code"></strong></h2></div>

                            <div class="panel-body">
                                <h2><span class="label label-info">反面照:</span></h2>
                                <img id="back" height='500' width='500' src="">
                            </div>
                            <div class="panel-heading"><h2>有效期:<strong id="abletime"></strong></h2></div>
                            <div class="panel-body">
                                <h2><span class="label label-info">手持照:</span></h2>
                                <img id="handle" height='500' width='500' src="">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            <button type="button" id="pass" class="btn btn-primary">通过</button>
                            <button type="button" id="unpass" class="btn btn-primary">驳回</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php include '../module/footer.php'; ?>
        <?php include '../module/mainScript.php'; ?>
        <script type="text/javascript" src="../common/global/scripts/common.js"></script>
        <script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
<script>
	var heartBeatType = 'realName';
	setInterval("heartBeat(heartBeatType)", 60000); //一分钟发送一次心跳，持续绑定审核项目
	function heartBeat(heartBeatType)
	{
		$.ajax({
			url: $conf.api + 'heartBeat.php',
			type: 'post',
			dataType: 'json',
			data: {
				uid : getCookie('admin_uid'),
				heartBeatType : heartBeatType,
			},
			success: function (data) {
			}
		});
	}
</script>
        <script type="text/javascript">
            $("#anchor_pass").on('click', function () {
                $.jqPaginator('#pagination_pass', {
                    totalPages: 20,
                    visiblePages: 10,
                    currentPage: 1,
                    onPageChange: function (num, type) {
                        $("#anchor_pass_tbodys tr").remove();
                        $.ajax({
                            url: '../api/anchor/anchorList.php',
                            data: {
                                type: 1,
                                page: num,
                                size: 10,
                                uid:getCookie("admin_uid"),
                                encpass:getCookie('admin_enc'),
                                utype: getCookie('admin_type')
                            },
                            dataType: 'json',
                            type: 'POST',
                            cache: false,
                            success: function (data) {
                                if (data) {
                                    $.each(data.data, function (i, item) {
                                        $("#anchor_pass_tbodys").append(
                                                "<tr id=" + item.uid + ">"
                                                + "<td>" + item.uid + "</td>"
                                                + "<td>" +item.nick + "</td>"
                                                + "<td>" + item.mobile  + "</td>"
                                                + "<td>" + item.name + "</td>"
                                                + "<td>" + item.pid + "</td>"
                                                + "<td>" + item.ptime + "</td>"
                                                + "<td><img  height='50' width='50' src=" + item.face + "></td>"
                                                + "<td><img  height='50' width='50' src=" + item.back + "></td>"
                                                + "<td><img  height='50' width='50' src=" + item.hand + "></td>"
                                                + "<td>" + item.passtime + "</td>"
                                                + "<td><span class='label label-success'>" + item.status + "</span></td>"
                                                + "<tr>"
                                                );
                                    });
                                    total = data.total;
                                    totalPage = Math.ceil(total / 10);
                                    if (total == 0) {
                                        $('#pagination_pass').jqPaginator('destroy');
                                    } else {
                                        $('#pagination_pass').jqPaginator('option', {
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
            $("#search").on('click', function () {
                var keyword = $("#keyword").val();
                var nickname = $("#nickname").val();
                $.jqPaginator('#pagination_pass', {
                    totalPages: 20,
                    visiblePages: 10,
                    currentPage: 1,
                    onPageChange: function (num, type) {
                        $("#anchor_pass_tbodys tr").remove();
                        $.ajax({
                            url: '../api/anchor/anchorList.php',
                            data: {
                                type: 1,
                                page: num,
                                size: 10,
                                keyword : keyword,
                                nickname : nickname,
                                uid : getCookie("admin_uid"),
                                encpass : getCookie('admin_enc'),
                                utype: getCookie('admin_type')
                            },
                            dataType: 'json',
                            type: 'POST',
                            cache: false,
                            success: function (data) {
                                if (data) {
                                    $.each(data.data, function (i, item) {
                                        $("#anchor_pass_tbodys").append(
                                            "<tr id=" + item.uid + ">"
                                            + "<td>" + item.uid + "</td>"
                                            + "<td>" +item.nick + "</td>"
                                            + "<td>" + item.mobile  + "</td>"
                                            + "<td>" + item.name + "</td>"
                                            + "<td>" + item.pid + "</td>"
                                            + "<td>" + item.ptime + "</td>"
                                            + "<td><img  height='50' width='50' src=" + item.face + "></td>"
                                            + "<td><img  height='50' width='50' src=" + item.back + "></td>"
                                            + "<td><img  height='50' width='50' src=" + item.hand + "></td>"
                                            + "<td>" + item.passtime + "</td>"
                                            + "<td><span class='label label-success'>" + item.status + "</span></td>"
                                            + "<tr>"
                                        );
                                    });
                                    total = data.total;
                                    totalPage = Math.ceil(total / 10);
                                    if (total == 0) {
                                        $('#pagination_pass').jqPaginator('destroy');
                                    } else {
                                        $('#pagination_pass').jqPaginator('option', {
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


            $("#anchor_unpass").on('click', function () {
                $.jqPaginator('#pagination_unpass', {
                    totalPages: 20,
                    visiblePages: 10,
                    currentPage: 1,
                    onPageChange: function (num, type) {
                        $("#anchor_unpass_tbodys tr").remove();
                        $.ajax({
                            url: '../api/anchor/anchorList.php',
                            data: {
                                type: 2,
                                page: num,
                                size: 10,
                                uid:getCookie("admin_uid"),
                                encpass:getCookie('admin_enc'),
                                utype: getCookie('admin_type')
                            },
                            dataType: 'json',
                            type: 'POST',
                            cache: false,
                            success: function (data) {
                                if (data) {
                                    $.each(data.data, function (i, item) {
                                        $("#anchor_unpass_tbodys").append(
                                                "<tr id=" + item.uid + ">"
                                                + "<td>" + item.uid + "</td>"
                                                + "<td>" +item.nick + "</td>"
                                                + "<td>" + item.mobile  + "</td>"
                                                + "<td>" + item.name + "</td>"
                                                + "<td>" + item.pid + "</td>"
                                                + "<td>" + item.ptime + "</td>"
                                                + "<td><img  height='50' width='50' src=" + item.face + "></td>"
                                                + "<td><img  height='50' width='50' src=" + item.back + "></td>"
                                                + "<td><img  height='50' width='50' src=" + item.hand + "></td>"
                                                + "<td>" + item.passtime + "</td>"
                                                + "<td><span class='label label-danger'>" + item.status + "</span></td>"
                                                + "<tr>"
                                                );
                                    });
                                    total = data.total;
                                    totalPage = Math.ceil(total / 10);
                                    if (total == 0) {
                                        $('#pagination_unpass').jqPaginator('destroy');
                                    } else {
                                        $('#pagination_unpass').jqPaginator('option', {
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

            $("#search").on('click', function () {
                var keyword = $("#keyword").val();
                var nickname = $("#nickname").val();
                $.jqPaginator('#pagination_unpass', {
                    totalPages: 20,
                    visiblePages: 10,
                    currentPage: 1,
                    onPageChange: function (num, type) {
                        $("#anchor_unpass_tbodys tr").remove();
                        $.ajax({
                            url: '../api/anchor/anchorList.php',
                            data: {
                                type: 2,
                                page: num,
                                size: 10,
                                keyword : keyword,
                                nickname : nickname,
                                uid : getCookie("admin_uid"),
                                encpass:getCookie('admin_enc'),
                                utype: getCookie('admin_type')
                            },
                            dataType: 'json',
                            type: 'POST',
                            cache: false,
                            success: function (data) {
                                if (data) {
                                    $.each(data.data, function (i, item) {
                                        $("#anchor_unpass_tbodys").append(
                                            "<tr id=" + item.uid + ">"
                                            + "<td>" + item.uid + "</td>"
                                            + "<td>" +item.nick + "</td>"
                                            + "<td>" + item.mobile  + "</td>"
                                            + "<td>" + item.name + "</td>"
                                            + "<td>" + item.pid + "</td>"
                                            + "<td>" + item.ptime + "</td>"
                                            + "<td><img  height='50' width='50' src=" + item.face + "></td>"
                                            + "<td><img  height='50' width='50' src=" + item.back + "></td>"
                                            + "<td><img  height='50' width='50' src=" + item.hand + "></td>"
                                            + "<td>" + item.passtime + "</td>"
                                            + "<td><span class='label label-danger'>" + item.status + "</span></td>"
                                            + "<tr>"
                                        );
                                    });
                                    total = data.total;
                                    totalPage = Math.ceil(total / 10);
                                    if (total == 0) {
                                        $('#pagination_unpass').jqPaginator('destroy');
                                    } else {
                                        $('#pagination_unpass').jqPaginator('option', {
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














            var certify = [];
            var base = $('#baseInfo');
            var baseInfoDom = {
                face: base.find('.anchor-face img'),
                nick: base.find('.anchor-info li:eq(0) span'),
                level: base.find('.anchor-info li:eq(1) span'),
                cost: base.find('.anchor-info li:eq(2) span'),
                ctime: base.find('.anchor-info li:eq(3) span'),
                name: base.find('.vertify-info li:eq(0) span'),
                identid: base.find('.vertify-info li:eq(1) span'),
                outtime: base.find('.vertify-info li:eq(2) span')
            };

            var show = $('.vertify-content');
            var showDom = {
                front: show.find('#front img'),
                back: show.find('#back img'),
                held: show.find('#handheld img')
            };
            var ret = $('.vertify-result');
            var retDom = {
                front: ret.find('#front-result img'),
                back: ret.find('#back-result img'),
                held: ret.find('#handheld-result img')
            };
            function initCertifyInfo(d) {
                $('.vertify-option').show();
                $('.vertify-result').find('.pass-tag').remove();
                certify = [];

                retDom['front'].attr('src', '../static/img/identCard_front.png');
                retDom['back'].attr('src', '../static/img/identCard_back.png');
                retDom['held'].attr('src', '../static/img/identCard_handheld.png');

                base.data('task-id', d.id);
                baseInfoDom.face.attr('src', d.pic);
                baseInfoDom.nick.text(d.nick);
                baseInfoDom.level.text(d.level);
                baseInfoDom.cost.text(d.cost);
                baseInfoDom.ctime.text(d.rtime);
                baseInfoDom.name.text(d.name);
                baseInfoDom.identid.text(d.papersid);
                baseInfoDom.outtime.text(d.outTime);

                showDom.front.attr('src', d.front);
                showDom.back.attr('src', d.back);
                showDom.held.attr('src', d.held);
            }

            function initCertifyInfo_nodata() {
                $('.vertify-option').hide();
                $('.vertify-result').find('.pass-tag').remove();
                certify = [];

                retDom['front'].attr('src', '../static/img/identCard_front.png');
                retDom['back'].attr('src', '../static/img/identCard_back.png');
                retDom['held'].attr('src', '../static/img/identCard_handheld.png');

                base.data('task-id', 0);
                baseInfoDom.face.attr('src', '');
                baseInfoDom.nick.text('');
                baseInfoDom.level.text('');
                baseInfoDom.cost.text('');
                baseInfoDom.ctime.text('');
                baseInfoDom.name.text('');
                baseInfoDom.identid.text('');
                baseInfoDom.outtime.text('');

                showDom.front.attr('src', '');
                showDom.back.attr('src', '');
                showDom.held.attr('src', '');
            }

            $('#pass').bind('click', function () {
                var item = $('#myCarousel').find('.carousel-inner .item.active');
                if (!item.get()[0])
                    return;

                var id = item.attr('id');
                var index = item.index();
                console.log(index);
                var src = item.find('img').attr('src');
                var passHtml = passTag(true);
                $('.vertify-result .vertify-ret-view').eq(index).find('.pass-tag').remove();
                $('.vertify-result .vertify-ret-view').eq(index).append(passHtml).find('img').attr('src', src);
                $('#myCarousel').carousel('next');
                certify[index] = 1;
            });
            $('#turnDown').bind('click', function () {
                var item = $('#myCarousel').find('.carousel-inner .item.active');
                if (!item.get()[0])
                    return;

                var id = item.attr('id');
                var index = item.index();
                var src = item.find('img').attr('src');
                var passHtml = passTag(false);
                $('.vertify-result .vertify-ret-view').eq(index).find('.pass-tag').remove();
                $('.vertify-result .vertify-ret-view').eq(index).append(passHtml).find('img').attr('src', src);
                $('#myCarousel').carousel('next');
                certify[index] = 0;
            });
            function passTag(pass) {
                if (pass) {
                    return '<span class="pass-tag pass">通过</span>';
                }
                return '<span class="pass-tag ban">驳回</span>';
            }

            $('#certify-submit').bind('click', function () {
                var id = base.data('task-id');
                if (certify.length < 3)
                    return;
                var type = 1;
                for (var i in certify) {
                    if (!certify[i]) {
                        type = 2;
                        break;
                    }
                }
                $.ajax({
                    url: $conf.anchor.api + 'setAnchorPass.php',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        id: id,
                        type: type,
                        uid: getCookie('admin_uid'),
                        encpass:getCookie('admin_enc'),
                        utype: getCookie('admin_type')
                    },
                    success: function () {
                        nextAnchorCertInfo();
                    }
                });
            });

            function nextAnchorCertInfo() {
                $.ajax({
                    url: $conf.anchor.api + 'getAnchorList.php',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        uid: getCookie('admin_uid'),
                        encpass: getCookie('admin_enc'),
                        type: getCookie('admin_type')
                    },
                    success: function (d) {
                        if (d.stat == 1) {
                            numberForAnchor();
                            initCertifyInfo_nodata();
                            initCertifyInfo(d.resuData);
                           	heartBeat(heartBeatType);
                        } else {
                            numberForAnchor();
                            if (d.err.code == -1009) {
                                initCertifyInfo_nodata();
                            }
                        }
                    }
                });
            }

            function numberForAnchor() {
                $.ajax({
                    url: $conf.anchor.api + 'numberForAnchor.php',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        uid: getCookie('admin_uid'),
                        encpass: getCookie('admin_enc'),
                        type: getCookie('admin_type')
                    },
                    success: function (d) {
                        if (d.stat == 1) {
                            cleanAnchorNumber();
                            anchorNumberInfo(d.resuData);
                        } else {
                            alert('服务器繁忙,请稍候刷新重试!');
                        }
                    }
                });
            }
            function  anchorNumberInfo(d) {
                $("#anchor_wait").find("span").text(d.wait);
                $("#anchor_pass").find("span").text(d.pass);
                $("#anchor_unpass").find("span").text(d.unpass);
            }
            function   cleanAnchorNumber() {
                $("#anchor_wait").find("span").text('');
                $("#anchor_pass").find("span").text('');
                $("#anchor_unpass").find("span").text('');

            }


            $(document).ready(function () {
                nextAnchorCertInfo();
            });
            //canvas 实现图片大小预览效果
            +function () {
                var radius = 50;

                var container = $('.carousel-inner');
                var canvasId = "zoom-image";

                var multiple = 2;

                //
                function createView(img) {
                    var div = null;
                    if (document.getElementById(canvasId)) {
                        div = document.getElementById(canvasId)
                    } else {
                        div = document.createElement('div');
                        div.id = canvasId;
                        div.style.width = 2 * radius + 'px';
                        div.style.height = 2 * radius + 'px';
                        div.style.position = 'absolute';
                        div.style.borderRadius = "100%";
                        div.style.border = "1px solid #e0e0e0";
                        div.style.overflow = 'hidden';

                        container.append(div);

                        var image = document.createElement('img');
                        image.style.width = multiple * img.width + 'px';
                        image.style.height = multiple * img.height + 'px';
                        image.style.position = 'absolute';
                        //                image.attributes.src.value = img.attributes.src.value;
                        $(div).append(image);
                    }
                    div.style.display = 'block';
                    return div;
                }

                function convertImageToDiv(img, x, y, div) {
                    x = x > 0 ? x : 0;
                    y = y > 0 ? y : 0;

                    div.style.left = x + 5 + 'px';
                    div.style.top = y - 2 * radius - 5 + 'px';

                    var image = $(div).find('img').get()[0];//每次移动都要获取 过于消耗资源
                    //TODO 减少每次的查找
                    var positionX = multiple * x - radius;
                    var positionY = multiple * y - radius;
                    image.style.left = -positionX + 'px';
                    image.style.top = -positionY + 'px';
                    $(image).attr('src', $(img).attr('src'));
                }

                function createCanvas() {
                    var canvas = '';
                    if (document.getElementById(canvasId)) {
                        canvas = document.getElementById(canvasId);
                    } else {
                        canvas = document.createElement('canvas');
                        canvas.id = canvasId;
                        canvas.width = 2 * radius;
                        canvas.height = 2 * radius;
                        canvas.style.position = 'absolute';
                        canvas.style.borderRadius = "100%";
                        container.append(canvas);
                    }

                    return canvas;
                }

                function converImageToCanvas(img, x, y, canvas) {
                    x = x > 0 ? x : 0;
                    y = y > 0 ? y : 0;

                    canvas.style.left = x - radius + 'px';
                    canvas.style.top = y - 2 * radius + 'px';


                    var view_width = Math.sqrt(2 * Math.pow(radius, 2));
                    var cut_width = view_width / multiple;
                    var vx = radius - view_width / 2;
                    var vy = radius - view_width / 2;

                    canvas.getContext('2d').drawImage(img, x, y, cut_width, cut_width, vx, vy, view_width, view_width);
                }


                $(".carousel-inner .item img").mousemove(function (e) {
                    //        console.log(e.clientY);
                    //        console.log(e.offsetY);
                    //        console.log(e.originalEvent.y);
                    //        console.log(e.originalEvent.layerY);
                    //        console.log('========================================');
                    var positionX = e.clientX - $(this).offset().left;
                    var positionY = e.clientY + $(document).scrollTop() - $(this).offset().top;
                    //        var positionX = e.originalEvent.x - $(this).offset().left || e.originalEvent.layerX - $(this).offset().left || 0;//获取当前鼠标相对img的x坐标
                    //        var positionY = e.originalEvent.y + $(document).scrollTop() - $(this).offset().top
                    //            || e.originalEvent.layerY + $(document).scrollTop() - $(this).offset().top || 0;//获取当前鼠标相对img的y坐标，（以下用不着，可删除）

                    var canvas = createView(this);
                    convertImageToDiv(this, positionX, positionY, canvas);
                });

                $(".carousel-inner .item img").hover(function () {
                    $('#zoom-image').show();
                }, function () {
                    $('#zoom-image').hide();
                });

            }();
        </script>
    </body>
</html>
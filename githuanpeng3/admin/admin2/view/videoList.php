<?php
/**
 * 录像审核
 * yandong@6room.com
 * data 2016-06-29 17:00
 */
include '../module/checkLogin.php';
$db = new DBHelperi_admin();
$sql = "select * from admin_liveReviewReason";
$res = $db->doSql($sql);

$reasonList = array();
foreach($res as $key=>$val){
    $reasonList[$val['id']] = $val['reason'];
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8"/>
        <title>录像审核</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta content="" name="description"/>
        <meta content="" name="author"/>
        <!--    --><?php //include ADMIN_MODULE.'mainStyle.php';?>
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="../common/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
        <link href="../common/global/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css"/>
        <!-- END PAGE LEVEL PLUGIN STYLES -->

        <!-- BEGIN THEME STYLES -->
        <link href="../common/global/css/components.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/css/plugins.css" rel="stylesheet" type="text/css"/>
        <link href="../common/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
        <link href="../common/admin/layout/css/themes/light.css" rel="stylesheet" type="text/css" id="style_color"/>
        <link href="../common/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
        <link href="../common/global/css/video-ui.css" rel="stylesheet" type="text/css"/>
        <!-- END THEME STYLES -->
        <link href="../common/admin/pages/css/vertifyRealName.css" rel="stylesheet" type="text/css"/>
        <style type='text/css'>
            .poster-container{
                width: 100%;
                border: black solid;
            }
            .poster-container video{
                /*修复尺寸*/
                width: 100%;
                height: auto;
                max-height: 100%;
                /*修复白边*/
                display: block;
                font-size:0;
            }
            .table th, .table td { 
                text-align: center;
                vertical-align: middle!important;
            }
            .row{
                margin-right: 0px; 
                 margin-left: 0px;
            }
        </style>
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
                            <div class="portlet box  bg-yellow-gold"> 
                                <div class="portlet-title">
                                    <div class="caption">
                                        <h4 class="page-title">录像审核</h4>
                                    </div>
                                    <div class="tools" style="display:none">
										<input type="text" class="input-small input-sm" id='userid' placeholder="主播ID" />
										<input type="text" class="input-small input-sm" id='nick' placeholder="主播昵称" />
										<button id="search" class="btn bg-yellow-gold">搜索</button>
									</div>
                                </div>
                                <div class="portlet-body">
                                    <div class="tabbable">
                                        <ul class="nav nav-tabs">
                                            <li class="active" id="v_pend">
                                                <a href="#anchor_wait_tab" id="anchor_wait" data-toggle="tab">
                                                    审核中 <span  class="badge badge-success" style="background-color: #ffa04c;"></span>
                                                </a>
                                            </li>
                                            <li  id="v_wait">
                                                <a href="#anchor_hold_tab" id="anchor_hold" data-toggle="tab">
                                                    待审核 <span  class="badge badge-success" style="background-color: #ffa04c;"></span>
                                                </a>
                                            </li>
                                            <li id="v_pass">
                                                <a href="#anchor_pass_tab" id="anchor_pass" data-toggle="tab">
                                                    已通过 <span  class="badge badge-success" style="background-color: #ffa04c;"></span></a>
                                            </li>
                                            <li id="v_unpass">
                                                <a href="#anchor_unpass_tab" id="anchor_unpass" data-toggle="tab">
                                                    未通过 <span  class="badge badge-success" style="background-color: #ffa04c;"></span></a>
                                            </li>
                                        </ul>
                                        <div class="tab-content no-space">
                                            <div class="tab-pane active" id="anchor_wait_tab">
                                                <div class="row">
                                                    <div class="realName">
                                                        <div class="realName-head">
                                                        </div>
                                                        <div class="realName-content ">
                                                            <div class="col-md-3" id="baseInfo">
                                                                <div class="anchor-info">
                                                                    <div class="anchor-face">
                                                                        <img class="img-circle"  src="http://img.huanpeng.com/5/e/5e49f1310263dae8f0bc3f484860f2ad.png" alt=""/>
                                                                    </div>
                                                                </div>
                                                                <div class="vertify-info">
                                                                    <ul class="list-unstyled">
                                                                        <li> <i class="fa fa-user" style="color: #a0a0a0;"></i><span></span></li>
                                                                        <li><i class="fa fa-tag" style="color: #a0a0a0;"></i><span></span></li>
                                                                        <li><i class="fa fa-laptop" style="color: #a0a0a0;"></i><span></span></li>
                                                                        <li><i class="fa fa-edit" style="color: #a0a0a0;"></i><span></span></li>
                                                                        <li><i class="fa fa-clock-o" style="color: #a0a0a0;"></i>直播时间：<span></span></li>
                                                                        <li><i class="fa fa-clock-o" style="color: #a0a0a0;"></i>录像生成时间：<span></span></li>
                                                                        <li><i class="fa fa-video-camera" style="color: #a0a0a0;"></i><span></span></li>
                                                                        <li hidden="hidden"><span ></span></li> 
                                                                        <li style="height: 135px;">
                                                                            <img id="poster_url"  src="" style="height: 100%; width: 100%;" ></li>   
                                                                    </ul>               
                                                                </div>
                                                            </div>
                                                            <div class="col-md-9" style="height: 470px; width: 836px; margin-bottom: 20px;">
                                                                <div id="videoPlayer"></div>
<!--                                                                <div class=" demo-video" id="videobox" style=" margin-top: 5px; min-height: 490px;">-->
<!--                                                                    <video class="video-js" controls-->
<!--                                                                           preload="auto"  height="427" src=""  data-setup="{}" style="width: 100%"-->
<!--                                                                    </video>-->
<!--                                                                </div>-->
                                                            </div>
                                                         
                                                        </div>
                                                        <div class="realName-footer" style="width:816">
                                                            <button type="button" class="btn bg-yellow-gold" id="pass" style="color:#FFF; margin-right: 5px;">通过</button>
                                                            <button type="button" class="btn bg-grey-silver" id="unpass" style="background:#a0a0a0;color:#FFF;margin-right: 5px; ">驳回</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="anchor_hold_tab">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>VideoId</th>
                                                            <th>UID</th>
                                                            <th>昵称</th>
                                                            <th>头像</th>
                                                            <th>游戏类型</th>
                                                            <th>游戏名称</th>
                                                            <th>标题</th>
                                                            <th>直播时间</th>
                                                            <th>录像生成时间</th>
                                                            <th>录像时长</th>
                                                            <th>封面图</th>
                                                            <th>播放</th>
                                                            <th>状态</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="anchor_hold_tbodys">
                                                    </tbody>
                                                </table>
                                                <nav style="text-align: center" id="hold_text">
                                                    <ul class="pagination" id="pagination_hold"></ul>
                                                </nav>
                                            </div>
                                            <div class="tab-pane" id="anchor_pass_tab">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>VideoId</th>
                                                            <th>UID</th>
                                                            <th>昵称</th>
                                                            <th>头像</th>
                                                            <th>游戏类型</th>
                                                            <th>游戏名称</th>
                                                            <th>标题</th>
                                                            <th>直播时间</th>
                                                            <th>录像生成时间</th>
                                                            <th>录像时长</th>
                                                            <th>封面图</th>
                                                            <th>播放</th>
                                                            <th>状态</th>
                                                            <th>操作</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="anchor_pass_tbodys">
                                                    </tbody>
                                                </table>
                                                <nav style="text-align: center" id="pass_text">
                                                    <ul class="pagination" id="pagination_pass"></ul>
                                                </nav>

                                            </div>
                                            <div class="tab-pane" id="anchor_unpass_tab">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>VideoId</th>
                                                            <th>UID</th>
                                                            <th>昵称</th>
                                                            <th>头像</th>
                                                            <th>游戏类型</th>
                                                            <th>游戏名称</th>
                                                            <th>标题</th>
                                                            <th>直播时间</th>
                                                            <th>录像生成时间</th>
                                                            <th>录像时长</th>
                                                            <th>封面图</th>
                                                            <th>播放</th>
                                                            <th>状态</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="anchor_unpass_tbodys">
                                                    </tbody>
                                                </table>
                                                <nav style="text-align: center" id="unpass_text">
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

                <div class="modal" id="big_pic" style="height:333; width:592;">
                    <div class="modal-dialog">
                        <div class="modal-content ">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            </div>
                            <div class="modal-body" style="padding: 0;">
                                <img height='333' width='596' src="">
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div> 
                    </div> 
                </div> 

                <div class="modal" id="back">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <h4 class="modal-title">驳回原因</h4>
                            </div>
                            <div class="modal-body">
                                <div class="portlet-body form">
                                    <form action="#" class="form-horizontal form-bordered">
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">原因类型 <span class="required"> * </span>
                                                </label>
                                                <div class="col-md-4">
                                                    <select class="form-control" name="selector" id="back_type">
                                                        <option value="0">请选择类型</option>
                                                        <?php
                                                            foreach($reasonList as $key => $val){
                                                                echo '<option value="'.$key.'">'.$val.'</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group last">
                                                <label class="control-label col-md-3">具体描述<span class="required">*</span></label>
                                                <div class="col-md-9">
                                                    <textarea id="back_desc" class="form-control" maxlength="225" rows="2" placeholder="请输入驳回的具体原因"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="back_Info" class="btn btn-primary">确定</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
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
<script type="text/javascript" src="../common/global/scripts/common.js"></script>
<script type="text/javascript" src="../common/global/plugins/swfobject.js"></script>
<script type="text/javascript" src="../common/global/plugins/video/video.js"></script>
<script>
	var heartBeatType = 'video';
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
    $(function(){
        var _domain = "http://www.huanpeng.com/";
        var id = 'videoPlayer';
        var file = _domain + 'static/flash/videoPlayer.swf';
        var version = '9.0.0';
        var install = 'expressInstall.swf';
		var nick = '';
		var userid = '';
        var flashvar = {
            'urlb':_domain + 'static/flash/barrage.swf',
            'urlw':_domain + 'static/flash/wait.swf',
            'urld':_domain + 'static/flash/dot.swf',
            'loadingURL':_domain + 'static/flash/loading.swf',
            'UIButtonURL':_domain + 'static/flash/UIButton.swf'
        };
        var param = {
            quality:'hight',
            bgcolor:'#000',
            allowScriptAccess:'always',
            allowFullScreen:'true',
            allowFullScreenInteractive:'true',
            WindowlessVideo:'1',
            wmode:'transparent'
        };
        var attribute = {
            allowScriptAccess:'always',
            allowFullScreen:'true',
            allowFullScreenInteractive:'true',
            name:id,
            align:'middle'
        };

        swfobject.embedSWF(file, id, '100%', "100%", version, install, flashvar, param, attribute);
    });

    $(document).ready(function () {
        nextWaitPassVideo();
        $('.video-js').css('width', '100%');
    });
    function  nextWaitPassVideo() {
        $.ajax({
            url: $conf.video.api + 'videoList.php',
            type: 'post',
            dataType: 'json',
            data: {
                uid: getCookie('admin_uid'),
                encpass: getCookie('admin_enc'),
                type: getCookie('admin_type')
            },
            success: function (d) {
                if (d.stat == 1) {
                    cleanVideoInfo();
                    videoNumber();
                    videoInfo(d.resuData);
                    heartBeat(heartBeatType);
                } else {
                    cleanVideoInfo();
                    videoNumber();
                }
            }
        });
    }
    function videoInfo(d) {
        $("#baseInfo").find("img:first").attr("src", d.pic);
        $("#baseInfo").find("img:last").attr("src", d.poster);
        $("#baseInfo").find("ul li:eq(0) span").text(d.nick);
        $("#baseInfo").find("ul li:eq(1) span").text(d.gametype);
        $("#baseInfo").find("ul li:eq(2) span").text(d.gamename);
        $("#baseInfo").find("ul li:eq(3) span").text(d.title);
        $("#baseInfo").find("ul li:eq(4) span").text(d.livetime);
        $("#baseInfo").find("ul li:eq(5) span").text(d.ctime);
        $("#baseInfo").find("ul li:eq(6) span").text(d.length);
        $("#baseInfo").find("ul li:eq(7) span").text(d.vid)
        initVideoPlayer(d.file, d.orientation);
    }

    function initVideoPlayer(videoUrl,orientation){
        runSwfFunction('videoPlayer', 'inputURL', videoUrl);
        var angle = 1;
        if(orientation == 1 || orientation == 4){
            angle = 0;
        }
        runSwfFunction('videoPlayer', 'angle', angle);
    }

    function  cleanVideoInfo() {
        $("#baseInfo").find("img:first").attr("src", '');
        $("#baseInfo").find("img:last").attr("src", '');
        $("#baseInfo").find("ul li:eq(0) span").text('');
        $("#baseInfo").find("ul li:eq(1) span").text('');
        $("#baseInfo").find("ul li:eq(2) span").text('');
        $("#baseInfo").find("ul li:eq(3) span").text('');
        $("#baseInfo").find("ul li:eq(4) span").text('');
        $("#baseInfo").find("ul li:eq(4) span").text('');
        $("#baseInfo").find("ul li:eq(5) span").text('');
        $("#baseInfo").find("ul li:eq(6) span").text('');
        $("#big_pic").find("img").attr("src", '');
        $("#videobox").find("video").attr("src", "");
    }

    $('#pass').on('click', function () {
        var videoid = $("#baseInfo").find("ul li:eq(7) span").html();
        $.ajax({
            url: $conf.video.api + 'setVideoPass.php',
            type: 'post',
            dataType: 'json',
            data: {
                videoid: videoid,
                uid: getCookie('admin_uid'),
                encpass: getCookie('admin_enc')
            },
            success: function () {
                cleanVideoInfo();
                nextWaitPassVideo();
            }
        });
    });
    $('#unpass').on('click', function () {
        $("#back").modal("toggle");
    });

    $("#poster_url").on('click', function () {
        var pic = $(this).attr("src");
        if (pic == '') {
            return;
        }
        $("#big_pic").find("img").attr("src", pic);
        $("#big_pic").modal("toggle");
    });
    $("#back_Info").on('click', function () {
        var id = $("#baseInfo").find("ul li:eq(6) span").html();
        var reason = $("#back_type").val();
        var describe = $("#back_desc").val();
        if (reason == 0) {
            alert("请选择类型!");
            return;
        }
        if (describe == '') {
            alert("请输入原因描述");
            return;
        }
        $.ajax({
            url: $conf.video.api + 'setVideoUnPass.php',
            type: 'post',
            dataType: 'json',
            data: {
                videoid: id,
                uid: getCookie('admin_uid'),
                encpass: getCookie('admin_enc'),
                type: getCookie('admin_type'),
                reason: reason,
                describe: describe

            },
            success: function () {
                $("#back").modal("toggle");
                cleanVideoInfo();
                nextWaitPassVideo();
            }
        });
    });
    function videoNumber() {
        $.ajax({
            url: $conf.video.api + 'videoStatus.php',
            type: 'post',
            dataType: 'json',
            data: {
                uid: getCookie('admin_uid'),
                encpass: getCookie('admin_enc'),
                type: getCookie('admin_type'),
				//userid: userid,
				//nick: nick
            },
            success: function (d) {
                if (d.stat == 1) {
                    cleanVideoNumber();
                    videoNumberInfo(d.resuData);
                } else {
                    alert('');
                }
            }
        });
    }
	
	$("#search").on('click', function () {
		//userid = $("#userid").val();
		//nick = $("#nick").val();
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
	
    function videoNumberInfo(d) {
        $("#v_wait").find("span").text(d.wait);
        $("#v_pass").find("span").text(d.pass);
        $("#v_pend").find("span").text(d.pend);
        $("#v_unpass").find("span").text(d.un_pass);
    }
    function cleanVideoNumber() {
        $("#v_wait").find("span").text('');
        $("#v_pass").find("span").text('');
        $("#v_pend").find("span").text('');
        $("#v_unpass").find("span").text('');
    }
    $("#anchor_hold").on('click', function () {
        $.jqPaginator('#pagination_hold', {
            totalPages: 20,
            visiblePages: 10,
            currentPage: 1,
            onPageChange: function (num, type) {
                $("#anchor_hold_tbodys tr").remove();
                $.ajax({
                    url: $conf.video.api + 'getListByStatus.php',
                    data: {
                        vtype: 1,
                        page: num,
                        size: 10,
                        uid: getCookie('admin_uid'),
                        encpass: getCookie('admin_enc'),
                        type: getCookie('admin_type')
                    },
                    dataType: 'json',
                    type: 'POST',
                    cache: false,
                    success: function (data) {
                        if (data.stat == 1) {
                            $.each(data.resuData.data, function (i, item) {
                                $("#anchor_hold_tbodys").append(
                                        "<tr id=" + item.videoId + ">"
                                        + "<td>" + item.videoId + "</td>"
                                        + "<td>" + item.uid + "</td>"
                                        + "<td>" + item.nick + "</td>"
                                        + "<td><img  height='50' width='50' src=" + item.pic + "></td>"
                                        + "<td>" + item.gametype + "</td>"
                                        + "<td>" + item.gamename + "</td>"
                                        + "<td>" + item.title + "</td>"
                                        + "<td>" + item.livetime + "</td>"
                                        + "<td>" + item.ctime + "</td>"
                                        + "<td>" + item.length + "</td>"
                                        + "<td><img  height='50' width='50' src=" + item.poster + "></td>"
                                        + "<td><a target='_bank' href='" + item.file + "'>播放</a></td>"
                                        + "<td><span class='label label-success'>" + '待审核' + "</span></td>"
                                        + "<tr>"
                                        );
                            });
                            total = data.resuData.total;
                            totalPage = Math.ceil(total / 10);
                            if (total == 0) {
                                $('#pagination_hold').jqPaginator('destroy');
                            } else {
                                $('#pagination_hold').jqPaginator('option', {
                                    totalCounts: parseInt(total),
                                    totalPages: totalPage

                                });
                            }
                        } else {
                            $('#pagination_hold').jqPaginator('destroy');
                            $("#hold_text").find("h2").text('');
                            $('#hold_text').append("<h2>暂无相关数据!<h2>");
                        }
                    },
                    error: function () {
                    }
                });
            }
        });
    });
    function  remove(del){
      var gameid = $(del).parents("tr").attr("id");
      alert('悟空,你又淘气喽!');
//                      alert(gameid);
    }
    
    
    $("#anchor_pass").on('click', function () {
        $.jqPaginator('#pagination_pass', {
            totalPages: 20,
            visiblePages: 10,
            currentPage: 1,
            onPageChange: function (num, type) {
                $("#anchor_pass_tbodys tr").remove();
                $.ajax({
                    url: $conf.video.api + 'getListByStatus.php',
                    data: {
                        vtype: 2,
                        page: num,
                        size: 10,
                        uid: getCookie('admin_uid'),
                        encpass: getCookie('admin_enc'),
                        type: getCookie('admin_type')
                    },
                    dataType: 'json',
                    type: 'POST',
                    cache: false,
                    success: function (data) {
                        if (data.stat == 1) {
                            $.each(data.resuData.data, function (i, item) {
                                $("#anchor_pass_tbodys").append(
                                        "<tr id=" + item.videoId + ">"
                                        + "<td>" + item.videoId + "</td>"
                                        + "<td>" + item.uid + "</td>"
                                        + "<td>" + item.nick + "</td>"
                                        + "<td><img  height='50' width='50' src=" + item.pic + "></td>"
                                        + "<td>" + item.gametype + "</td>"
                                        + "<td>" + item.gamename + "</td>"
                                        + "<td>" + item.title + "</td>"
                                        + "<td>" + item.livetime + "</td>"
                                        + "<td>" + item.ctime + "</td>"
                                        + "<td>" + item.length + "</td>"
                                        + "<td><img  height='50' width='50' src=" + item.poster + "></td>"
                                        + "<td><a target='_bank' href='" + item.file + "'>播放</a></td>"
                                        + "<td><span class='label label-success'>" + '已通过' + "</span></td>"
                                        + '<td ><a type="button" target="_blank" href="http://www.huanpeng.com/videoRoom.php?videoid=' + item.videoId + '" class="btn bg-yellow-gold" style="color:#FFF">观看</a>'
										//+ '<button type="button" onclick="remove(this)" class="btn bg-yellow-gold" style="color:#FFF">删除</button>'
										+ '</td>'
                                        + "<tr>"
                                        );
                            });
                            total = data.resuData.total;
                            totalPage = Math.ceil(total / 10);
                            if (total == 0) {
                                $('#pagination_pass').jqPaginator('destroy');
                            } else {
                                $('#pagination_pass').jqPaginator('option', {
                                    totalCounts: parseInt(total),
                                    totalPages: totalPage

                                });
                            }
                        } else {
                            $('#pagination_pass').jqPaginator('destroy');
                            $("#pass_text").find("h2").text('');
                            $('#pass_text').append("<h2>暂无相关数据!<h2>");
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
                    url: $conf.video.api + 'getListByStatus.php',
                    data: {
                        vtype: 4,
                        page: num,
                        size: 10,
                        uid: getCookie('admin_uid'),
                        encpass: getCookie('admin_enc'),
                        type: getCookie('admin_type')
                    },
                    dataType: 'json',
                    type: 'POST',
                    cache: false,
                    success: function (data) {
                        if (data.stat == 1) {
                            $.each(data.resuData.data, function (i, item) {
                                $("#anchor_unpass_tbodys").append(
                                        "<tr id=" + item.videoId + ">"
                                        + "<td>" + item.videoId + "</td>"
                                        + "<td>" + item.uid + "</td>"
                                        + "<td>" + item.nick + "</td>"
                                        + "<td><img  height='50' width='50' src=" + item.pic + "></td>"
                                        + "<td>" + item.gametype + "</td>"
                                        + "<td>" + item.gamename + "</td>"
                                        + "<td>" + item.title + "</td>"
                                        + "<td>" + item.livetime + "</td>"
                                        + "<td>" + item.ctime + "</td>"
                                        + "<td>" + item.length + "</td>"
                                        + "<td><img  height='50' width='50' src=" + item.poster + "></td>"
                                        + "<td><a target='_bank' href='" + item.file + "'>播放</a></td>"
                                        + "<td><span class='label label-danger'>" + '未通过' + "</span></td>"
                                        + "<tr>"
                                        );
                            });
                            total = data.resuData.total;
                            totalPage = Math.ceil(total / 10);
                            if (total == 0) {
                                $('#pagination_unpass').jqPaginator('destroy');
                            } else {
                                $('#pagination_unpass').jqPaginator('option', {
                                    totalCounts: parseInt(total),
                                    totalPages: totalPage
                                });
                            }
                        } else {
                            $('#pagination_unpass').jqPaginator('destroy');
                            $("#unpass_text").find("h2").text('');
                            $('#unpass_text').append("<h2>暂无相关数据!<h2>");
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
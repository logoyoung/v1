<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/16
 * Time: 上午10:40
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
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>直播管理</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <?php include '../module/mainStyle.php'; ?>
    <link href="../common/admin/pages/css/portfolio.css" rel="stylesheet" type="text/css"/>
    <link href="../common/admin/pages/css/liveVerify.css" rel="stylesheet" type="text/css"/>
    <style>
        .live-vertify-page #myModal_autocomplete .modal-dialog{
            width: 500px;
        }

        .live-vertify-page #myModal_autocomplete span.form-control{
            border: 0;
        }
    </style>
</head>
<body class="page-header-fixed page-sidebar-closed-hide-logo page-sidebar-fixed">
<?php include '../module/head.php'; ?>
<div class="clearfix"></div>
<div class="page-container live-vertify-page">
    <?php include '../module/sidebar.php'; ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">

                    <div class="portlet ">
                    <div class="portlet-title">
                <div class="caption">
                    <h4 class="page-title">直播管理</h4>
                </div>
                <div class="tools">
                    <button type="button" class="btn bg-yellow-gold" id="faster">快速处理</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row mix-grid thumbnails live-table">
                    </div>
                    <div id="myModal_autocomplete" class="modal fade" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="" aria-hidden="true"></button>
                                    <h4 class="modal-title">直播警告</h4>
                                </div>
                                <div class="modal-body form">
                                    <form action="#" class="form-horizontal form-row-seperated">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">直播房间</label>
                                            <div class="col-sm-8">
                                                <span id="user-live-title" class="form-control"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">主播昵称</label>
                                            <div class="col-sm-8">
                                                <span id="user-live-nick" class="form-control"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">警告原因</label>
                                            <div class="col-sm-8">
                                                <select id="stop-reason" class="bs-select form-control">
                                                    <?php
                                                        foreach($reasonList as $key => $val){
                                                            echo '<option value="'.$key.'">'.$val.'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
<!--                                        <div class="form-group">-->
<!--                                            <label class="col-sm-4 control-label">描述</label>-->
<!--                                            <div class="col-sm-8">-->
<!--                                                <div class="input-group">-->
<!--                                                    <input type="text" id="typeahead_example_modal_4" name="typeahead_example_modal_4" class="form-control"/>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button id="modal-submit" type="button" class="btn btn-primary yellow-crusta">确定</button>
                                    <button id="modal-close" type="button" class="btn btn-default" data-dismiss="">取消</button>
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
                                    <label class="control-label col-md-3">用户UID<span class="required">
                                                            * </span></label>
                                    <div class="col-md-5">
                                        <input class="form-control" id="anchorid" type="text"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">类型<span class="required">*</span></label>
                                    <div class="col-md-4">
                                        <select class="form-control" name="select" id="ctype">
                                            <option value= '0' selected = "selected">请选择</option>
                                            <option value= '1'>警告 </option>
                                            <option value= '2'>断流</option>
                                            <option value= '3'>封号</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">原因<span class="required">*</span></label>
                                    <div class="col-md-4">
                                        <select class="form-control" name="select" id="reason">
                                            <option value= '0' selected = "selected">请选择</option>
                                            <?php
                                                foreach($reasonList as $key => $val){
                                                    echo '<option value="'.$key.'">'.$val.'</option>';
                                                }
                                            ?>
                                        </select>
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
                <button type="button"  id="addnew" class="btn btn-primary">确定</button>
            </div>

        </div>
    </div>
</div>

<?php include '../module/footer.php'; ?>
<?php include '../module/mainScript.php'; ?>
<script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
<script type="text/javascript" src="../common/global/plugins/swfobject.js"></script>
<script type="text/javascript" src="../common/global/scripts/common.js"></script>
<script>
var submitLiveID = 0;
var submitType = 0;
var submitReason = 0;
var submitLuid = 0;
var anchorid = 0;
!function(){
    $('#myModal_autocomplete button.close').bind('click',function(){
        closeModal();
        $('#mix'+submitLuid + '-'+submitLiveID).hover();
    });

    $("#modal-close").bind('click',function(){
        closeModal();
        $('#mix'+submitLuid + '-'+submitLiveID).hover();
    });
    $('#modal-submit').bind('click', function(){
        submitReason = $('#stop-reason').val();
        request(1);
    });


    var source = new EventSource($conf.live.api + 'getLiveList.php');

    var livelist =[];
    source.addEventListener('myReviewList', function(event){
        var data = JSON.parse(event.data);
        var list = data.list;
        var count = data.count;
        console.log(livelist);
        livelist = rebuildViewer(setLuid(changeList(livelist, list)));
    });

    function changeList(livelist, list){
        var tmp = [];
        for(var i in livelist){
            tmp[livelist[i]] = {};
            tmp[livelist[i]]['save'] = 0;
        }

        for(var i in list) {
            if (livelist.indexOf(list[i].liveid) > -1) {
                tmp[list[i].liveid]['save'] = 1;
            } else {
                tmp[list[i].liveid] = {};
                tmp[list[i].liveid]['save'] = 2;
                tmp[list[i].liveid]['luid'] = list[i].luid;
            }
        }

        return tmp;
    }

    function setLuid(list){
        for(var i in list){
            if(!list[i]['luid']){
                list[i]['luid'] = getLuid(i);
            }
        }

        return list;

        function getLuid(liveid){
            var mixList = $('.mix').get();
            for(var i in mixList){
                if($(mixList[i]).data('liveid') == liveid)
                    return $(mixList[i]).data('luid');
            }
            return 0;
        }
    }

    function rebuildViewer(list){
        var ret = [];
        for(var i in list){
            if(list[i].save == 0){
                $("#"+getNodeID(list[i].luid, i)).remove();
            }else if(list[i].save == 1){
                ret.push(i);
            }else if (list[i].save == 2){
                ret.push(i);
                initViewer(list[i].luid, i);
            }
        }
        return ret;
    }

    function getNodeID(luid, liveid){
        return 'mix' + luid +'-'+liveid;
    }


    function initViewer(luid, i) {
        var id = getNodeID(luid, i);
        var playerId = 'viewer' + luid + '-' +i;
        var file = $conf.domain + "common/admin/pages/flash/livePlayer.swf";
        var html = '<div class="col-md-4 col-sm-6 mix" id="' + id + '">\
                    <div class="mix-inner"> <div class="live-viewer">\
                    <div id="' + playerId + '"></div></div>\
                    <div class="mix-details" style="height:40px;background:#ffa04c;">\
                    <a class="option">封号 </a>\
                    <a class="option" href="javascript:;">关流</a>\
                    <a class="option" href="javascript:;">警告</a>\
                    <a class="option" target="_blank" href="http://www.huanpeng.com/room.php?luid='+luid+'">进入直播间</a>\
                    </div></div></div>';
        $('.live-table').append(html);
        $('#'+id).data('luid', luid);
        $('#'+id).data('liveid', i);
        resize();

        runSwfFunction(playerId,'setVolumeAuthority', 0, 0);

        $('#'+id).hover(function(){
            runSwfFunction(playerId,'setVolumeAuthority', 0.5, 0);
        },function(){
            runSwfFunction(playerId,'setVolumeAuthority', 0, 0);
        });

        !function(){
            var freeze =   $('#'+id).find('.mix-details .option:eq(0)');
            var stopLive = $("#"+id).find('.mix-details .option:eq(1)');
            var complain = $("#"+id).find('.mix-details .option:eq(2)');
            console.log(freeze);
            freeze.bind('click', function(){
                var liveid = $(this).parents('.mix').data('liveid');
                var luid = $(this).parents('.mix').data('luid');
                submitType = 3;
                submitLiveID = liveid;
                submitLuid = luid;
                initModalInfo(liveid, luid);
            });

            stopLive.bind('click', function(){
                var liveid = $(this).parents('.mix').data('liveid');
                var luid = $(this).parents('.mix').data('luid');
                submitType = 2
                submitLiveID = liveid;
                submitLuid = luid;
                initModalInfo(liveid, luid);
            });

            complain.bind('click', function(){
                var liveid = $(this).parents('.mix').data('liveid');
                var luid = $(this).parents('.mix').data('luid');
                submitType = 1
                submitLiveID = liveid;
                submitLuid = luid;
                initModalInfo(liveid, luid);
            });

            var warningTitle = {1:'警告',2:'关流', 3:'封号'}

            function initModalInfo(liveid, luid){
                $.ajax({
                    url:$conf.live.api + 'getLiveInfo.php',
                    type:'get',
                    dataType:'json',
                    data:{
                        liveid:liveid,
                        uid:getCookie("admin_uid"),
                        encpass:getCookie('admin_enc'),
                        type: getCookie('admin_type')
                    },
                    success:function(data){
                        if(data.stat == 1){
                            $("#myModal_autocomplete .modal-title").text('直播'+warningTitle[submitType]);
                            var html = "<a style='color:#ff7800;text-decoration: underline;' target='_blank' href='http://www.huanpeng.com/room.php?luid="+luid+"'>"+data.resuData.title+"</a>";
                            $('#user-live-nick').text(data.resuData.nick + '(ID:'+luid+')');
                            $('#user-live-title').html(html);
                            showModal();
                        }
                    }
                });
            };

            function requestFreezeAnchor(){};

            function requestComplainAnchor(){}

            function requestStopLiveAnchor(){}
        }();

        !function () {
            $(id).data('luid', luid);

            var params = {
                quality: 'hight',
                bgcolor: '#869ca7',
                allowScriptAccess: 'always',
                allowFullScreen: 'true',
                allowFullScreenInteractive: 'true',
                WindowlessVideo: '1',
                wmode: 'transparent'
            };
            var attrbuite = {
                allowScriptAccess: 'always',
                allowFullScreen: 'true',
                allowFullScreenInteractive: 'true',
                name: id,
                align: 'middle'
            };
            swfobject.embedSWF(file, playerId, '100%', '100%', '9.0.0', 'expressInstall.swf', {}, params, attrbuite);
        }();

        !function () {
            var player = null;
            var playerInterval = setInterval(function () {
                if (swfobject.getObjectById(playerId)) {
                    player = swfobject.getObjectById(playerId);
                    clearInterval(playerInterval);
                    requestPlayUrl();
                }
            }, 100);

            function requestPlayUrl() {
                $.ajax({
                        url: $conf.live.api + 'getStreamList.php',
//                        url: 'http://www.huanpeng.com/api/live/getStreamList.php',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            luid: luid,
                            uid:getCookie("admin_uid"),
                            encpass:getCookie('admin_enc'),
                            type: getCookie('admin_type')
                        },
                        success: function (d) {
                            if(d.streamList){
                                var streamList = d.streamList;
                                var orientation = d.orientation;
                                var stream = d.stream;
                                var interval = setInterval(function () {//防止 chatProxy 未加载完而出现错误
                                    try {
                                        if (player.PercentLoaded() == 100) {
//                                            player.inputURL(stream, 'drtmp://' + streamList[0]);//alert(width)
                                            player.inputURL(stream, 'rtmp://'+streamList[0]);//alert(width)
                                            if(orientation == 0){
//                                                player.angle(1)
                                                player.setScreenDirection('vertical');
                                            }else{
                                                player.setScreenDirection('horizontal');
//                                                player.angle(0);
                                            }
                                        }
                                        clearInterval(interval);
                                    }
                                    catch (e) {
                                        console.log(e)
                                    }
                                }, 100);
                            }
                        }
                    }
                );
            }
        }();


    }

    function resize(){
        $('.live-viewer').each(function (i, element) {
            $(element).parents('.mix').css({
                display: 'block',
                opacity: 1
            });
            $(element).css('width', '100%');
            var height = parseInt($(element).width()) * 9 / 16;
            $(element).height(height);
        });
    }
}();

$("#faster").on('click', function () {
    $("#mymodal").modal("toggle");
});

$("#addnew").on('click', function () {
    anchorid = $("#anchorid").val();
    submitType = $("#ctype").val();
    submitReason = $("#reason").val();
    if (anchorid == '') {
        alert("UID不能为空!");
        return;
    }
    if(submitType == 0){
        alert("请选择类型");
        return;
    }
    if(submitReason == 0){
        alert("请选择原因");
        return;
    }
    request(2);
});


function closeModal(){
    $('#myModal_autocomplete').removeClass('in').hide();
    submitLiveID = 0;
    submitType = 0;
    submitReason = 0;
    submitLuid = 0;
    anchorid  = 0;
}

function showModal(){
    $('#myModal_autocomplete').addClass('in').show();
}

function request(type){
    $.ajax({
        url:$conf.live.api + 'liveStop.php',
        type:'post',
        dataType:'json',
        data:{
            uid:getCookie("admin_uid"),
            encpass:getCookie('admin_enc'),
            type: getCookie('admin_type'),
            liveid:submitLiveID,
            msgType:submitType,
            reason:submitReason,
            anchorid:anchorid
        },

        success:function(d){
            if(type == 1) {
                closeModal();
                $('#mix'+submitLuid + '-'+submitLiveID).hover();
            } else if (type == 2) {
                if (d.stat) {
                    $("#anchorid").attr("value", '');
                    $("#ctype").attr("value", '请选择');
                    $("#reason").val("value",'请选择');
                    $("#mymodal").modal("toggle");
                } else {
                    $("#mymodal").modal("toggle");
                    alert(d.err.desc);
                }
            }
        }
    });
}
</script>
</body>
</html>





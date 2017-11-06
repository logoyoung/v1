<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/16
 * Time: 上午10:40
 */
include '../../../include/adminInit.php';
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
    <!-- END THEME STYLES -->

    <link href="../common/admin/pages/css/vertifyRealName.css" rel="stylesheet" type="text/css"/>
    <style type="text/css">
        #zoom-image{
            display: none;
        }
        #zoom-image:hover{
            display: none;
        }
    </style>
</head>
<body class="page-header-fixed page-sidebar-closed-hide-logo">
<?php include ADMIN_MODULE.'head.php';?>
<div class="clearfix"></div>
<div class="page-container">
    <?php include ADMIN_MODULE.'sidebar.php';?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <h3 class="page-title">
                Dashboard <small>reports & statistics</small>
            </h3>
            <div class="row">
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
                                                    <img src="http://dev-img.huanpeng.com/5/e/5e49f1310263dae8f0bc3f484860f2ad.png" alt=""/>
                                                </div>
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <i class="fa fa-user"></i>
                                                        <span></span>
                                                    </li>
                                                    <li>
                                                        <i class="fa fa-asterisk"></i>
                                                        <span></span>
                                                    </li>
                                                    <li>
                                                        <i class="fa fa-rmb"></i>
                                                        <span></span>
                                                    </li>
                                                    <li>
                                                        <i class="fa fa-clock-o"></i>
                                                        <span></span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="vertify-info">
                                                <ul class="list-unstyled">
                                                    <li><i class="fa fa-user"></i><span></span></li>
                                                    <li><i class="fa fa-list-alt"></i><span></span></li>
                                                    <li><i class="fa-calendar-o"></i>
                                                        <span></span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6 vertify-content">
                                            <div id="myCarousel" class="carousel image-carousel slide" data-interval="false">
                                                <div class="carousel-inner">
                                                    <div id="front" class="item active">
                                                        <img src="http://dev-img.huanpeng.com//2825/realname/c3abe86c22e63ebd31ad1a3065554a22.png" alt=""/>
                                                        <div class="carousel-caption">
                                                            <h4>正面照</h4>
                                                        </div>
                                                    </div>
                                                    <div id="back" class="item">
                                                        <img src="http://dev-img.huanpeng.com//2825/realname/c3abe86c22e63ebd31ad1a3065554a22.png" alt=""/>
                                                        <div class="carousel-caption">
                                                            <h4>反面照</h4>
                                                        </div>
                                                    </div>
                                                    <div id="handheld" class="item">
                                                        <img src="http://dev-img.huanpeng.com//2825/realname/c3abe86c22e63ebd31ad1a3065554a22.png" alt=""/>
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
                                                    <img src="http://dev.huanpeng.com/static/img/identCard_front.png" alt=""/>
                                                </div>
                                                <div id="back-result" class="vertify-ret-view">
                                                    <img src="http://dev.huanpeng.com/static/img/identCard_back.png" alt=""/>
                                                </div>
                                                <div id="handheld-result" class="vertify-ret-view">
                                                    <img src="http://dev.huanpeng.com/static/img/identCard_handheld.png" alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="realName-footer">
                                        <a id="certify-submit" href="javascript:;" class="btn yellow-crusta">
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
    </div>

</div>
<?php include ADMIN_MODULE.'footer.php';?>
<?php include ADMIN_MODULE.'mainScript.php';?>

<script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
<script type="text/javascript" src="../common/global/scripts/common.js"></script>
<script>

    var certify = [];
    var base = $('#baseInfo');
    var baseInfoDom = {
        face:base.find('.anchor-face img'),
        nick:base.find('.anchor-info li:eq(0) span'),
        level:base.find('.anchor-info li:eq(1) span'),
        cost:base.find('.anchor-info li:eq(2) span'),
        ctime:base.find('.anchor-info li:eq(3) span'),
        name:base.find('.vertify-info li:eq(0) span'),
        identid:base.find('.vertify-info li:eq(1) span'),
        outtime:base.find('.vertify-info li:eq(2) span')
    };

    var show = $('.vertify-content');
    var showDom = {
        front:show.find('#front img'),
        back:show.find('#back img'),
        held:show.find('#handheld img')
    };
    var ret = $('.vertify-result');
    var retDom = {
        front:ret.find('#front-result img'),
        back:ret.find('#back-result img'),
        held:ret.find('#handheld-result img')
    };
    function initCertifyInfo(d){
        $('.vertify-option').show();
        $('.vertify-result').find('.pass-tag').remove();
        certify = [];

        retDom['front'].attr('src', 'http://dev.huanpeng.com/static/img/identCard_front.png');
        retDom['back'].attr('src', 'http://dev.huanpeng.com/static/img/identCard_back.png');
        retDom['held'].attr('src', 'http://dev.huanpeng.com/static/img/identCard_handheld.png');

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

    function initCertifyInfo_nodata(){
        $('.vertify-option').hide();
        $('.vertify-result').find('.pass-tag').remove();
        certify = [];

        retDom['front'].attr('src', 'http://dev.huanpeng.com/static/img/identCard_front.png');
        retDom['back'].attr('src', 'http://dev.huanpeng.com/static/img/identCard_back.png');
        retDom['held'].attr('src', 'http://dev.huanpeng.com/static/img/identCard_handheld.png');

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

    $('#pass').bind('click', function(){
        var item = $('#myCarousel').find('.carousel-inner .item.active');
        if(!item.get()[0]) return;

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
    $('#turnDown').bind('click', function(){
        var item = $('#myCarousel').find('.carousel-inner .item.active');
        if(!item.get()[0]) return;

        var id = item.attr('id');
        var index = item.index();
        var src = item.find('img').attr('src');
        var passHtml = passTag(false);
        $('.vertify-result .vertify-ret-view').eq(index).find('.pass-tag').remove();
        $('.vertify-result .vertify-ret-view').eq(index).append(passHtml).find('img').attr('src', src);
        $('#myCarousel').carousel('next');
        certify[index] = 0;
    });
    function passTag(pass){
        if(pass){
            return '<span class="pass-tag pass">通过</span>';
        }
        return '<span class="pass-tag ban">驳回</span>';
    }

    $('#certify-submit').bind('click', function(){
        var id = base.data('task-id');
        if(certify.length < 3)
            return;
        var type = 1;
        for(var i in certify){
            if(!certify[i]){
                type = 2;
                break;
            }
        }
        $.ajax({
            url:'http://dev.huanpeng.com/admin2/api/anchor/setAnchorPass.php',
            type:'post',
            dataType:'json',
            data:{
                id:id,
                type:type,
                uid:getCookie('admin_uid')
            },
            success:function(){
                nextAnchorCertInfo();
            }
        });
    });

    function nextAnchorCertInfo(){
        $.ajax({
            url:$conf.anchor.api + 'getAnchorList.php',
            type:'post',
            dataType:'json',
            data:{
                uid:getCookie('admin_uid'),
                encpass:getCookie('admin_enc'),
                type:getCookie('admin_type')
            },
            success:function(d){
                if(d.stat == 1){
                    initCertifyInfo(d.resuData);
                }else{
                    if(d.err.code == -1009){
                        initCertifyInfo_nodata();
                    }
                }
            }
        });
    }

    $(document).ready(function(){
       nextAnchorCertInfo();
    });



    //canvas 实现图片大小预览效果
    +function(){
        var radius = 50;

        var container = $('.carousel-inner');
        var canvasId = "zoom-image";

        var multiple = 2;

        //
        function createView(img){
            var div = null;
            if(document.getElementById(canvasId)){
                div = document.getElementById(canvasId)
            }else{
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

        function convertImageToDiv(img, x, y, div){
            x = x > 0 ? x : 0;
            y = y > 0 ? y : 0;

            div.style.left = x + 5 + 'px';
            div.style.top = y - 2*radius - 5 + 'px';

            var image = $(div).find('img').get()[0];
            var positionX = multiple * x - radius;
            var positionY = multiple * y - radius;
            image.style.left = -positionX + 'px';
            image.style.top  = -positionY + 'px';
            $(image).attr('src', $(img).attr('src'));
        }

        function createCanvas(){
            var canvas = '';
            if(document.getElementById(canvasId)){
                canvas = document.getElementById(canvasId);
            }else{
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

        function converImageToCanvas(img, x, y, canvas){
            x = x > 0 ? x : 0;
            y = y > 0 ? y : 0;

            canvas.style.left = x - radius + 'px';
            canvas.style.top =  y - 2*radius  + 'px';


            var view_width = Math.sqrt(2 * Math.pow(radius, 2));
            var cut_width = view_width / multiple;
            var vx = radius -  view_width / 2;
            var vy = radius -  view_width / 2;

            canvas.getContext('2d').drawImage(img, x, y, cut_width, cut_width, vx, vy, view_width, view_width);
        }

        $(".carousel-inner .item img").mousemove(function(e) {
            var positionX = e.originalEvent.x - $(this).offset().left || e.originalEvent.layerX - $(this).offset().left || 0;//获取当前鼠标相对img的x坐标
            var positionY = e.originalEvent.y + $(document).scrollTop() - $(this).offset().top
                || e.originalEvent.layerY + $(document).scrollTop() - $(this).offset().top || 0;//获取当前鼠标相对img的y坐标，（以下用不着，可删除）

            var canvas = createView(this);
            convertImageToDiv(this, positionX, positionY, canvas);
        });

        $(".carousel-inner .item img").hover(function(){
            $('#zoom-image').show();
        }, function(){
            $('#zoom-image').hide();
        });

    }();
//    var ret = convertImageToCanvas($('#front img').get()[0]);
//    console.log(convertCanvasToImage(ret));
</script>
</body>
</html>
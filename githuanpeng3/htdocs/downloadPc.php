<?php
include "../include/init.php";

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>下载-欢朋直播-精彩手游直播平台！</title>
    <link rel="stylesheet" type="text/css" href="./static/css/footer.css?v=1.0.4">
    <script type="text/javascript" src="static/requirejs/require.js" defer async = "true" data-main="static/requirejs/main"></script>
</head>
<body>
<style>
    html,body{
        width: 100%;
        height: 100%;
        /*overflow: hidden;*/
        background: #eee;
        font-family: "Microsoft YaHei UI","Microsoft YaHei",STHeiti,"PingFang SC",PingFang,Helvetica,Arial;
    }
   
    img{
        width: 100%;
        height:100%;
        overflow: hidden;
    }
    a{
        text-decoration: none;
    	    color:inherit;
    }
    a:hover{
        color: inherit;
    }
    a:visited{
        color: inherit;
    }
    #download-page-pc{
        width:100%;
        height: 100%;
    }
    #download-page-pc .nav{
        width:100%;
        height: 45px;
        padding:8px 0 0 8px;
        background: #fff;
    }
    #download-page-pc .nav .nav-content{
        width:100%;
        margin: auto;
        min-width: 650px;
    }
    .nav .nav-app,.nav .nav-pc,.nav .nav-logo{
        float:left;
        margin:0px 24px;
    }
    .nav .nav-app,.nav .nav-pc{
        font-size: 1rem;
        padding: 9px 18px 10px 18px;
        line-height: 1.5rem;
        text-align: center;
        border-bottom: 2px solid transparent;
        cursor:pointer;
    }
    .nav .nav-app:hover,.nav .nav-pc:hover{
        color:#ff7800;
    }
    .nav .nav-app.open,.nav .nav-pc.open{
        border-bottom-color:#ff7800;
        color:#ff7800;
    }
    .nav .nav-home{
        float: right;
        font-size: 14px;
        padding: 13px 20px 0px 17px;
        color: #666;
    }
    .nav .nav-home .icon-go-home{
        margin-right: 5px;
        width: 20px;
        height: 20px;
        float: left;
        background: url(static/img/download/btn-download.png) 2px -397px no-repeat;
    }
    .nav .nav-home:hover .icon-go-home{
        background-position: -19px -397px;
    }
    .nav .nav-home:hover{
        color:#ff7800;
    }
    .nav .nav-logo{
        margin-left: 0px;
    }
    @media screen and (max-height: 1000px){
        #download-page-pc .content{zoom:0.9;}
    }
    @media screen and (max-height: 900px){
        #download-page-pc .content{zoom:0.8;}
    }
    @media screen and (max-height: 800px){
        #download-page-pc .content{zoom:0.7;}
    }
    @media screen and (max-height: 700px){
        #download-page-pc .content{zoom:0.6;}
    }
    @media screen and (max-height: 600px){
        #download-page-pc .content{zoom:0.5;}
    }
    #download-page-pc .content{
        width: 100%;
        position: relative;
        background: url(static/img/download/bg-download.jpg);
        float: left;
    }
    #download-page-pc .content .content-app,#download-page-pc .content .content-pc{
        width: 1200px;
        margin: auto;
    }
    .content .content-left{
        width: 728px;
        height: 660px;
        margin:50px 0px;
        float: left;
    }
    .content .content-right{
        float: left;
        margin: 184px 0px 0px 0px;
    }
    .content .content-right .title-txt{
        width: 353px;
        height: 126px;
        margin-bottom:70px;
    }
    .content-right .content-right-codedown{
        width: 185px;
        height:185px;
        float:left;
    }
    .content-right .content-right-codedown .codedown-txt{
        color:#fff;
        font-size: 14px;
        text-align: center;
        padding: 15px 0px;
    }
    .content-right .content-right-btndown{
        float: left;
        width: 220px;
        height:220px;
    }
    .content-right .content-right-btndown .btndown{
        width: 137px;
        height: 60px;
        font-size: 20px;
        border-radius: 20px;
        margin: 25px 50px;
        text-align: left;
        padding-left:88px;
        line-height: 60px;
        color: #fff;
        cursor: pointer;
    }
    .content-right .content-right-btndown .btndown-ios{
        background: url(static/img/download/btn-download.png) 5px 0px no-repeat;
    }
    .content-right .content-right-btndown .btndown-ios:hover{
        background-position:5px -64px ;
    }
    .content-right .content-right-btndown .btndown-android{
        background: url(static/img/download/btn-download.png) 5px -128px no-repeat;
    }
    .content-right .content-right-btndown .btndown-android:hover{
        background-position: 5px -192px;
    }
    .content-left-pc{
        width: 747px;
        height:660px;
        margin-top: 50px;
        margin-bottom: 50px;
        float: left;
        position: relative;
        background: url(static/img/download/pc.png) no-repeat;
        overflow: hidden;
    }
    .content-left-pc .slider-list{
        position: absolute;
        left: -1px;
        top:0px;
        /*width: 2000px;*/
    }
    .content-left-pc .slider-list .slider-one{
        float: left;
        opacity: .1;
        filter:Alpha(opacity=10);
        -webkit-transition: opacity 1s;
        -moz-transition: opacity 1s;
        -ms-transition: opacity 1s;
        -o-transition: opacity 1s;
        transition: opacity 1s;
    }
    .content-left-pc .slider-list .slider-one.show{
        opacity:1;
        filter:Alpha(opacity=100);
    }
    .content-left-pc .circle-list{
        position: absolute;
        z-index: 99;
        left: 561px;
        bottom: 0px;
    }
    .content-left-pc .circle-list .circle-one{
        float: left;
        width:18px;
        height: 18px;
        margin: 1px;
        background: url(static/img/download/btn-download.png) -17px -381px no-repeat;
    }
    .content-left-pc .circle-list .circle-one.show{
        background: url(static/img/download/btn-download.png) 5px -381px no-repeat;
    }
    .content-right-pc{
        width: 377px;
        float: right;
        /* margin: 100px; */
        margin-top: 213px;
    }
    .content-right-pc .title-txt-pc{
        width: 100%;
        height: 144px;
    }
    .content-right-pc .description-pc{
        font-size: 16px;
        margin: 30px 0px;
        color: #fff;
        line-height: 35px;
    }
    .content-right-pc .btn-download-pc{
        text-align: center;
        width: 222px;
        height: 63px;
        background: url(static/img/download/btn-download.png) 1px -254px no-repeat;
        margin: auto;
        font-size: 24px;
        color: #fff;
        line-height: 60px;
        cursor: pointer;
    }
    .content-right-pc .btn-download-pc:hover{
        background-position: 1px -318px;
    }
    .content-right-pc .btn-txt-version{
        margin: auto;
        text-align: center;
        padding: 15px;
        color: #fff;
        font-size: 14px;
    }
    /*#download-page-pc .footer{
        width: 100%;
        height: 70px;
        bottom:0px;
        position: absolute;
        font-size: .8rem;
    }
    #download-page-pc .footer-copyright{
        width: 510px;
        margin: auto;
        color: #fff;
        line-height: 20px;
    }*/

</style>
<script>
	var uastr = window.navigator.userAgent.toLowerCase();
	if(uastr.indexOf('msie')>-1||uastr.indexOf('rv:')>-1)
		document.write('<style>#download-page-pc .content{zoom:1;};<\/style>');
</script>
<!--[if IE]>
<style>
    #download-page-pc .content{zoom:1;}
    </style>
<![endif]-->
<div id="download-page-pc">
    <div class="nav" >
        <div class="nav-content">
            <div class="nav-logo"><a href="./index.php"><img src="static/img/logo_v2.png"></a></div>
            <div class="nav-app ">App下载</div>
            <div class="nav-pc">直播助手下载</div>
            <div class="nav-home"><a href="./index.php"><div class="icon-go-home"></div><span>返回首页</span></a></div>
        </div>
    </div>
    <div class="content">
        <div class="content-app" style="display:none">
            <div class="content-left">
                <img src="static/img/download/iphone.png">
            </div>
            <div class="content-right">
                <div class="title-txt">
                    <img src="static/img/download/txt-1.png">
                </div>
                <div class="content-right-codedown">
                    <img src="static/img/src/qrcode/qrcode_home.png">
                    <div class="codedown-txt"><span>扫描二维码下载到手机</span></div>
                </div>
                <div class="content-right-btndown">
                    <a href="https://itunes.apple.com/us/app/huan-peng/id1191399310?ls=1&mt=8"><div class="btndown btndown-ios">iOS下载</div></a>
                    <a href="api/app/download.php"><div class="btndown btndown-android">安卓下载</div></a>
                </div>
            </div>
        </div>
        <div class="content-pc" style="display:none">
            <div class="content-left-pc">
                <div class="slider-list">
                    <div class="slider-one show"><img src="static/img/download/guide-1.png"></div>
                    <div class="slider-one"><img src="static/img/download/guide-2.png"></div>
                    <div class="slider-one"><img src="static/img/download/guide-3.png"></div>
                    <div class="slider-one"><img src="static/img/download/guide-4.png"></div>
                    <div class="slider-one"><img src="static/img/download/guide-5.png"></div>
                </div>
                <div class="circle-list">
                    <div class="circle-one show"></div>
                    <div class="circle-one"></div>
                    <div class="circle-one"></div>
                    <div class="circle-one"></div>
                    <div class="circle-one"></div>
                </div>
            </div>
            <div class="content-right-pc">
                <div class="title-txt-pc">
                    <img src="static/img/download/txt-2.png">
                </div>
                <div class="description-pc">
                    手机投屏直播｜PC游戏直播｜添加摄像头｜场景个性化自定义
                </div>
                <a href="api/client/download.php">
                    <div class="btn-download-pc">
                    立即下载
                    </div>
                </a>

                <div class="btn-txt-version">欢朋直播助手v&nbsp1.2</div>

            </div>

        </div>
        
    </div>
    <?php include_once WEBSITE_MAIN . 'footerSub.php';?>
    </div>

</body>
</html>

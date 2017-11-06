<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <title>直播APP下载</title>
    <script type="text/javascript" src="static/requirejs/require.js" defer async = "true" data-main="static/requirejs/main.js"></script>
</head>
<body>
<style>
    html,body{
        background: url(static/img/download/app-bg.jpg);
        width: 100%;
        /*overflow: hidden;*/
        font-family: "Microsoft YaHei UI","Microsoft YaHei",STHeiti,"PingFang SC",PingFang,Helvetica,Arial;
    }
    *{
        margin: 0;
        padding: 0;
    }
    img{
        width: 100%;
        height:100%;
        overflow: hidden;
    }
    a{
        text-decoration: none;
    }
    a:hover{
        color: inherit;
    }
    a:visited{
        color: inherit;
    }
    
    #download-page-app{
        width: 100%;
    }
    #download-page-app .logo-block{
        width: 10.8rem;
        height: 3.176rem;
        margin: 1.5rem auto;
    }
    #download-page-app .app-content{
        width: 20rem;
        height: 18.13rem;
        margin: auto
    }
    #download-page-app .app-txt{
        width: 13rem;
        height: 1.027rem;
        margin: .3rem auto;
    }
    #download-page-app .app-btn{
        width: 10rem;
        height: 3rem;
        line-height: 3rem;
        text-align: center;
        color: #fff;
        font-size: 1.3rem;
        margin: 2.2rem auto 0rem auto ;
        /*background: url(static/img/download/app-btn-01.png);*/
        background: #ff7800;
        border-radius: 2rem;
        background-size: 10rem;
        outline: none;
        -webkit-tap-highlight-color:rgba(0,0,0,0);
        -webkit-tap-highlight-color:transparent;
    }
    #download-page-app .app-btn:hover{
        /*background: url(static/img/download/app-btn-02.png);*/
        background: #ff8900;
        border-radius: 2rem;
        background-size: 10rem;
    }
    /*.h5-mask{*/
        /*width: 100%;*/
        /*height: 100%;*/
        /*left: 0;*/
        /*top:0;*/
        /*position: absolute;*/
        /*background: rgba(0,0,0,.8);*/
        /*display: none;*/
    /*}*/
    /*.h5-mask.show{*/
        /*display: block;*/
    /*}*/
    /*.h5-mask .mask-contain{*/
        /*width: 85%;*/
        /*height: 100%;*/
        /*margin: auto;*/
    /*}*/
    /*.h5-mask .mask-contain .mask-part{*/
        /*margin: auto;*/
    /*}*/
    /*.h5-mask .mask-contain .tip{*/

    /*}*/
    /*.h5-mask .mask-contain .known{*/
        /*width: 10rem;*/
        /*height: 4rem;*/
        /*margin-top:2rem ;*/
    /*}*/
</style>
<div id="download-page-app">
<!--    <div class="h5-mask">-->
<!--        <div class="mask-contain">-->
<!--            <div class="mask-part tip"><img src="static/img/download/h5-tip.png"></div>-->
<!--            <div class="mask-part known"><img src="static/img/download/h5-known.png"></div>-->
<!--        </div>-->
<!--    </div>-->
    <div class="logo-block">
        <img src="static/img/download/app-logo.png?v=1.0.4">
    </div>
    <div class="app-content">
        <img src="static/img/download/iphone.png?v=1.0.4">
    </div>
    <div class="app-txt">
        <img src="static/img/download/app-txt.png?v=1.0.4">
    </div>
    <a id="app-down-btn-mobile" href=""><div id="android-ios-download" class="app-btn">立即下载</div></a>
</div>
</body>
</html>

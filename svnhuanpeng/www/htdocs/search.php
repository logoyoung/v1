<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset='utf-8'>
    <?php include './tpl/commSource.php';?>
    <link rel="stylesheet" type="text/css" href="./static/css/common.css?v=1.0.4">
    <link rel="stylesheet" type="text/css" href="./static/css/home_v3.css?v=1.0.4">
    <link rel="stylesheet" type="text/css" href="./static/css/head.css?v=1.0.4">

    <script type="text/javascript" src="./static/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="./static/js/common.js?v=1.0.4"></script>
    <!--<script type="text/javascript" src="./static/js/page.js?v.1.0"></script>-->
    <script type="text/javascript" src="static/js/home_data_load.js?v=1.0.4"></script>
    <script type="text/javascript" src="static/js/head.js?v=1.0.4"></script>

    <style type="text/css">
        body {
            background-color: #eeeeee;
            color:#444;
        }
        key{
            color:#ff7800;
            padding:0px;
            margin:0px;
        }
        #block5 .block, #content .fav_rank, #block3{
            /* box-shadow: 0px 1px 1px 1px #FFF; */
        }
        #block5 .content{
            float:left;
            /* border:1px solid #eee; */
            margin-bottom: 100px;
        }
        #block5 .block{
            margin:25px 0px;
        }
        .w1180#liveHall .block_live a b{
            left: 9px;
            top: 10px;
        }
        .w1180#liveHall .block_live a i{
            left: 110px;
            top: 55px;
        }
        .w980#liveHall .block_live a b{
            left: 12px;
            top: 12px;
        }
        .w980#liveHall .block_live a i{
            left: 110px;
            top: 52px;
        }
        #liveHall .contain{
            display:block;
            margin: 0px auto;
            /*min-height: 400px;*/
            background: #fff;
            padding: 0px 0px 0px 0px;
            /* box-shadow: 0px 1px 1px 1px #c9c4c4; */
        }
        #liveHall .contain .more_live{
            width:160px;
            height:40px;
            background:#eeeeee;
            margin:0px auto;
            text-align:center;
            line-height:40px;
            cursor:pointer;
        }
        #liveHall.w1180 .contain{
            width:1180px;
        }
        #liveHall.w980 .contain{
            width:980px;
        }
        #liveHall input[type="text"]:focus{line-height: normal; line-height: 2.9em\9;}
        #liveHall .block .livecount{
            padding:8px;
            font-size:14px;
            color:#444;
            line-height:9px;
            margin:17px 7px 0px 7px ;
            display:block;
        }
        #liveHall .block_live{
            padding-top:8px;
        }
        #liveHall .block ul{
            border:0px;
            min-height:100px;
            width:100%;
        }
        #liveHall .block_title{
            padding: 0px;
            margin: 0px;
            float: left;
            line-height: 75px;
            font-size: 18px;
            color: #666;
            width:1120px;
        }
        #liveHall.w980 .block_title{
            width:920px;
        }
        #liveHall .block_title.underline{
            border-bottom:1px solid #ddd;
            margin:0px 10px;
        }

        #block5 .block_title .fl{
            margin-right:40px;
            margin-left: 0px;
        }
        #liveHall .block_title span{
            font-size:18px;
        }
        #liveHall .block_title span.more{
            float: right;
            font-size:14px;
            cursor:pointer;
            line-height: 55px;
        }
        #liveHall .block_title span.more:hover{
            color:#ff7800;
        }
        #liveHall .block_title span.cur{
            background:red;
            color:#fff;
        }
        #liveHall .block_title .tabcard{
            float: left;
            width: 90px;
            text-align: center;
            cursor: pointer;
            line-height: 63px;
            font-size:18px;
            height:49px;
            margin-right:30px;
        }
        #liveHall .block_title .tabcard:hover{
            color:#ff7800;
        }
        #liveHall .block_title .tabcard.cur{
            border-bottom:2px solid #ff7800;
            color:#ff7800;
        }
        #liveHall .block_live .liveinfo .icon1.v{
            background: url(./static/img/icon/icon_set_1.png) -202px -83px no-repeat;
        }
        #liveHall .block_live .liveinfo .icon2.v{
            background: url(./static/img/icon/icon_set_1.png) -172px -83px no-repeat;
        }
        /* #nodata .bg_nodata{
            margin-top:130px;
            width:100%;
            height:202px;
            background:url(./static/img/src/bg_nodata.png) center top no-repeat;
        }  */
        #nodata{
            margin-bottom: 62px;
        }
        #nodata .description{
            line-height: 60px;
            font-size: 18px;
            color: #666;
            margin: auto;
            width: 523px;
            height: 60px;
        }
        #nodata .description .des_div{
            width:100%;
            height:100%;
        }
        #nodata .description .no_icon{
            width: 40px;
            height: 40px;
            background-position: -329px -133px;
            float: left;
            margin-top: 8px;
        }
        #nodata .description .no_txt{
            float:left;
        }
        #nodata .description #key{
            color:#ff7800;
            max-width:149px;
            text-overflow:ellipsis;
            overflow:hidden;
            display: inline-block;
            vertical-align: middle;
            white-space: nowrap;
        }
        #recommend,#seprate{
            float:left;
            padding:20px;
            background: #fff;
            display:none;
        }
        #rec_link:hover {
            color:#ff7800;
        }

        /* new */
        .navi ul li.last{
            display:none;
        }
        #liveHall .search_block{
            width:100%;
            height:87px;
            margin-top:110px;
        }
        #liveHall .search_block .searchdiv{
            margin:auto;
            width:516px;
            height:44px;
        }
        .searchdiv input{
            width:356px;
            height:41px;
            padding:0px 20px;
            line-height:41px;
            outline:none;
            float:left;
            border:1px solid #ccc;
            border-right-width:0px;
            font-size:14px;
        }
        .searchdiv input.cur{
            border-color:#ff9e48;
        }
        .searchdiv .btn_div{
            float:left;
            background-color:#ff9e48;
            position:relative;
        }
        .searchdiv .btn_div:hover{
            background-color:#ff7800;
        }
        .searchdiv .btn{
            width:110px;
            height:42px;
            color:#fff;
            font-size:20px;
            margin:0px;
            float:left;
            padding-left:30px;
            background:transparent;
            border: 1px solid #ff9e48;
        }

        .searchdiv i{
            cursor:pointer;
            width: 32px;
            height: 22px;
            position: absolute;
            left: 14px;
            top: 12px;
            background: url(./static/img/icon/icon_set_1.png) -270px -146px no-repeat;
        }
        #liveHall .anchor_div{
            width:120px;
            height:175px;
            float:left;
            margin-right:10px;
        }
        .w1180 #block5 .block_live ul li.anchor_div{
            padding: 10px 7px;
        }
        #liveHall .anchor_div a{
            width:100%;
            height:100%;
        }
        #liveHall .anchor_div .avatar{
            width:100%;
            height:120px;
        }
        #liveHall .anchor_div .nick{
            width:100%;
            height:35px;
            margin-top:10px;
            font-size:14px;
        }
        #liveHall .anchor_div .mask{
            width: 120px;
            height: 120px;
            margin-top: -170px;
            background: rgba(255,255,255,.2);
            position: absolute;
            display:none;
        }
        #liveHall .anchor_div:hover .mask{
            display:block;
        }
        .mask .enter{
            width:100px;
            height:30px;
            margin-top:95px;
            background-color:#ff9e48;
            color:#fff;
            font-size:14px;
            line-height:30px;
            padding-left:20px;
        }
        #liveHall .anchor_div .live_flag{
            width: 32px;
            height: 32px;
            background-position: -783px -272px;
            position: absolute;
            margin-top: -167px;
            margin-left: 89px;
        }
        #liveHall #anchor li{
            margin:0px;
            padding: 10px;
        }
        #liveHall #anchor li:hover,
        #liveHall .anchor_ul li:hover {
            background: #fff;
            /* border: 1px solid #c8c8c8; */
            box-shadow: none;
            -webkit-box-shadow: none;
            -o-box-shadow: none;
        }
        #liveHall li .er{
            display: inline-block;
            width: 22px;
            height: 20px;
            background-position: -743px -268px;
        }
        #liveHall .result_txt{
            float: left;
            /* padding: 10px; */
            margin: 20px 10px 0px 10px;
            font-size: 14px;
        }
        #liveHall .result_key{
            color: #ff7800;
            padding:0px 10px;
            font-style: normal;
        }
        #liveHall .result_num{
            display: inline-block;
            color: #ff7800;
            padding:0px 10px
        }

    </style>
</head>

<body>
<?php  $path = realpath(__DIR__); ?>
<?php include $path.'/head.php';?>
<div id="liveHall" class="w1180">
    <div class="search_block">
        <div class="searchdiv">
            <input class="txt" type="text" onfocus="this.className='cur';" onblur="this.className='';"/>
            <div class="btn_div">
                <i></i>
                <input class="btn" type="button" value="搜索"/>
            </div>
        </div>
    </div>
    <div id="nodata" style="display:none;">
        <!-- <div class="bg_nodata">
        </div> -->

        <div class="description">
            <div class="des_div">
                <div class="icon_set no_icon"></div><div class="no_txt">暂无“<span id="key"><?php echo htmlspecialchars($_GET['key']) ?></span>”的相关内容，为您推荐以下内容</div></div>
        </div>
    </div>
    <!-- 有数据 -->
    <div  id="block5" class="contain">
        <!--  -->
        <div class="content">
            <div id="recommend" style="display:none;">
                <div class="block_title">
                    <span>推荐直播</span>
                    <span id="rec_link" style="float: right;font-size:14px;"><a href="LiveHall.php">更多>></a></span>
                </div>

                <div class="block_live">
                    <ul></ul>
                </div>
            </div>

            <!--  -->

            <div id="seprate" class="block">
                <div class="block_title underline">

                    <!-- <span class="livecount">目前有<p class="num">0</p>个主播正在直播</span> -->
                    <div class="tabcard cur">全部</div>
                    <div class="tabcard">直播</div>
                    <div class="tabcard">主播</div>
                    <div class="tabcard">视频</div>
                    <!-- <div class="tabcard cur">全部(<n>0</n>)</div>
                    <div class="tabcard">直播(<n>0</n>)</div>
                    <div class="tabcard">主播(<n>0</n>)</div>
                    <div class="tabcard">录像(<n>0</n>)</div> -->
                </div>
                <!--<div class="result_txt" >找到<em class="result_key"></em>相关内容共<div class="result_num"></div>个</div>-->
                <div id="all" class="block_live" style="margin-bottom:0px">

                    <div class="block_title">
                        <span>相关主播</span>
                        <span id="anchor" class="more">更多>></span>
                    </div>
                    <div id="anchor" class="block_live">
                        <ul>

                        </ul>
                    </div>

                    <div class="block_title">
                        <span>相关直播</span>
                        <span id="live" class="more">更多>></span>
                    </div>
                    <div class="block_live">
                        <ul>
                        </ul>
                    </div>
                    <div class="block_title">
                        <span>相关视频</span>
                        <span id="video" class="more">更多>></span>
                    </div>
                    <div class="block_live" style="margin-bottom:0px;">
                        <ul>
                        </ul>
                    </div>

                </div>
                <div id="sep" class="block_live" style="display: none;">
                    <ul>
                    </ul>
                </div>
                <div class="pageIndex"></div>
            </div>
            <!-- <div class="more_live">
            加载更多直播
            </div> -->
        </div>
    </div>
</div>
<?php
    include $path.'/footer.php';
?>
<?php

   include $path.'/tpl/toTop.php';
?>
<script>
    //var page = new page();
   // page.init();
    var head = new head();

    var keyc = keyc||function(){
            this._type = 0;//请求类型
            this._key = typeof($_GET['key'])!='undefined'?<?php echo "'".urlencode($_GET['key'])."'"; ?>:'';//关键字
            this._url = 'api/other/homeSearch.php';
            this._uid = getCookie('_uid');
            this._enc = getCookie('_enc');
            this._liveCount = 0;
            this._anchorLiveCount = 0;
            this._videoCount = 0;
            this._allCount = 0;
            this._pageCount = 6;//分页按钮显示个数
            this._pageCur = 1;//当前页
            this._pageLiveCount = 16;//每页显示16个
            this._everySize = 8;//请求全部是每类请求4个
            //this.init();
            this.animate();
            this.request();
            this.search();
        }
    keyc.prototype = {
        init:function(){
            var keyStr = this._key;
            keyStr = decodeURIComponent(keyStr);
            this.req(keyStr);
        },

        request:function(){
            var o = this;
            var size = parseInt(o._type)?o._pageLiveCount:o._everySize;
            $.ajax({
                url:o._url,
                data:{keyword:o._key,type:o._type,uid:o._uid,encpass:o._enc,page:o._pageCur,size:size},
                type:'post',
                dataType:'json',
                success:function(data){o.process(data,o)}
            });
        },
        _getVar:function(v){
            return (typeof(v)=='undefined'||v==null||parseInt(v.length)==0)?false:true;
        },
        process:function(data){
            data =data.content;
            var o = arguments[1];
            var lena = o._getVar(data['anchorList']);
            var lenl = o._getVar(data['liveList']);
            var lenv = o._getVar(data['videoList']);

            if(!lena && !lenl && !lenv && parseInt(data['type'])==0){
                $('#seprate').css("display","none");
                $('#nodata').css("display","block");
                o.loadRecommend(data['recommend']);
            }else{
                $('#nodata,#recommend').css("display","none");
                $('#seprate').css("display","block");
            }
            //console.log(data)
            //console.log(o);
            if(o._type==0){
                if(o._pageCur==1){
                    o._allCount = data['allCount'];
                    o._liveCount = data['liveCount'];
                    o._videoCount = data['videoCount'];
                    o._anchorLiveCount = data['anchorLiveCount'];
                    /* $('.tabcard n:eq(0)').text(o._allCount);
                     $('.tabcard n:eq(1)').text(o._liveCount);
                     $('.tabcard n:eq(2)').text(o._anchorLiveCount);//console.log(o._anchorLiveCount);
                     $('.tabcard n:eq(3)').text(o._videoCount); */
                    $('.result_key').text('"'+decodeURIComponent($_GET['key']+'"'));
                    $('title').text(decodeURIComponent($_GET['key'])+'-搜索结果-欢朋直播');
                    $('.result_num').text(data['allCount']);
                }
                o.loadAllList(data);
                $('.pageIndex').css('display','none');
            }
            else if(o._type==1) {console.log(o._type);
                //var obj = $('#liveHall .block ul')
                o.loadLiveList(data['liveList'],data['keyword'],data['type']);
                $('.pageIndex').css('display','block');
            }
            else if(o._type==2){console.log(o._type);
                o.loadAnchorList(data['anchorList'],data['keyword'],data['type']);
            }
            else if(o._type==3){console.log(o._type);
                o.loadVideoList(data['videoList'],data['keyword'],data['type']);
                $('.pageIndex').css('display','block');
            }
            var allPageCount=1;
            if(o._type==1) allPageCount=o._liveCount;
            else if(o._type==2) allPageCount=o._anchorLiveCount;
            else if(o._type==3) allPageCount=o._videoCount;
            //alert(Math.ceil(allPageCount/o._pageCount))
            var d = { pageCount:Math.ceil(allPageCount/o._pageLiveCount),
                current:o._pageCur,
                pageMax:o._pageCount,
                backFn:function(e){
                    o._pageCur=e;
                    o.request();}
            };
            //o.loadLiveList(data['liveList']);
            // o.loadLiveList(data['liveList']);
            //if(o._type)
            o.pageIndexRefresh();
            if(o._type!=0)
                $(".pageIndex").createPage(d);
        },
        loadRecommend:function(data){
            $('#recommend').css('display','block');
            var ulObj = $('#recommend ul');
            for(var key in data){
                data[key].poster = data[key].poster?data[key].poster:'static/img/src/default/260x150.png';
                var angleStr = (parseInt(data[key].orientation)==0 && parseInt(data[key].ispic))?$conf.angleImage:'';//alert(angleStr);
                liStr = '<li class="h_item"><a href="'+'room.php?luid='+data[key].uid+'"><i></i><b></b>'
                    +'<div class="img_block"><img class="'+angleStr+'" src="'+data[key].poster+'">'
                    +'</div><div class="liveinfo">'
                    +'<p>'+data[key].title+'</p>'
                    +'<div class="icon1"></div>'
                    +'<span class="fl">'+data[key].nick+'</span>'
                    +'<div class="icon2"></div>'
                    +'<span class="fl">'+data[key].viewCount+'</span>'
                    +'<span class="fr last">'+data[key].gameName+'</span></div></a></li>';
                ulObj.append(liStr);
            }
            angleImage(ulObj);
        },
        loadAllList:function(data){

            //if(data['liveList'].length>0)
            this.loadLiveList(data['liveList'],data['keyword'],0,1);
            //if(data['anchorList'].length>0)
            this.loadAnchorList(data['anchorList'],data['keyword'],0,0);
            //if(data['videoList'].length>0)
            this.loadVideoList(data['videoList'],data['keyword'],0,2);

        },
        loadAnchorList:function(anchorList,keyword,type,blocki){
            if(parseInt(type)>0){
                $('#sep').css('display','block');
                $('#all').css('display','none');
                var ulObj = $('#sep ul');
                ulObj.addClass('anchor_ul');
            }
            else{
                $('#sep').css('display','none');
                $('#all').css('display','block');
                var ulObj = $('#all ul:eq('+blocki+')');
            }
            if(!this._getVar(anchorList)){
                ulObj.parent().prev('.block_title').css('display','none');
                ulObj.parent().css('display','none');
            }else{
                ulObj.parent().prev('.block_title').css('display','block');
                ulObj.parent().css('display','block');
            }
            var liStr = '';
            ulObj.html('');
            for(var key in anchorList){
                //var isLive = '';
                if(anchorList[key].isLiving=="1")
                    var isLive = '<div class="icon_set live_flag"></div>';
                else
                    var isLive = '';
                //var nickStr = anchorList[key].nick.replace(/keyword/g,'<span style="color:red;">'+keyword+'</span>');
                var nickStr = anchorList[key].nick.replace(new RegExp("("+keyword+")","g"),'<span style="color:red;">'+keyword+'</span>');
                liStr = '<li class="anchor_div"><a target="_blank" href="'+'room.php?luid='+anchorList[key].uid+'">'
                    + '<div class="avatar"><span><img src="'+anchorList[key].head+'"  onerror="this.onerror="";this.src="static/img/src/default/260x150.png"></span>'
                    + '</div><div class="nick">'+nickStr+'</div>'
                    + '<div class="mask"><div class="enter">进入直播间<span class=" er icon_set"></span></div></div>'
                    + isLive
                    + '</a></li>'
                ulObj.append(liStr);
            }
        },
        loadLiveList:function(liveList,keyword,type,blocki){
            /* var liveList = data['liveList'];
             var keyWord = data['keyWord'];
             */

            if(parseInt(type)>0){
                $('#sep').css('display','block');
                $('#all').css('display','none');
                var ulObj = $('#sep ul');
                ulObj.removeClass('anchor_ul');
            }
            else{
                $('#sep').css('display','none');
                $('#all').css('display','block');
                var ulObj = $('#all ul:eq('+blocki+')');
            }

            if(!this._getVar(liveList)){
                ulObj.parent().prev('.block_title').css('display','none');
                ulObj.parent().css('display','none');
            }else{
                ulObj.parent().prev('.block_title').css('display','block');
                ulObj.parent().css('display','block');
            }

            var liStr = '';
            ulObj.html('');
            if( liveList.length>0 ){
                for(var key in liveList){
                    //console.log(liveList[key].liveTitle.replace(new RegExp("("+key+")","g"),"<key>$1</key>"));
                    var liveTitleStr = liveList[key].title.replace(new RegExp("("+keyword+")","gi"),"<key>$1</key>");
                    var nickStr = liveList[key].nick.replace(new RegExp("("+keyword+")","gi"),"<key>$1</key>");
                    liveList[key].poster = liveList[key].poster?liveList[key].poster:'static/img/src/default/260x150.png';
                    var angleStr = (parseInt(liveList[key].orientation)==0 && parseInt(liveList[key].ispic))?$conf.angleImage:'';
                    liStr = '<li class="h_item"><a href="'+'room.php?luid='+liveList[key].uid+'"><i></i><b></b>'
                        +'<div class="img_block"><img class="'+angleStr+'" src="'+liveList[key].poster+'">'
                        +'</div><div class="liveinfo">'
                        +'<p>'+liveTitleStr+'</p>'
                        +'<div class="icon1"></div>'
                        +'<span class="fl">'+nickStr+'</span>'
                        +'<div class="icon2"></div>'
                        +'<span class="fl">'+liveList[key].viewCount+'</span>'
                        +'<span class="fr last">'+liveList[key].gameName+'</span></div></a></li>';
                    ulObj.append(liStr);
                }}else{
                liStr = '<div style="font-size:14px;;margin:40px 0px 40px 80px;">暂无数据</div>';
                ulObj.append(liStr);
            }
            hoveritem();
            angleImage(ulObj);
        },
        loadVideoList:function(videoList,keyword,type,blocki){
            if(parseInt(type)>0){
                $('#sep').css('display','block');
                $('#all').css('display','none');
                var ulObj = $('#sep ul');
                ulObj.removeClass('anchor_ul');
            }
            else{
                $('#sep').css('display','none');
                $('#all').css('display','block');
                var ulObj = $('#all ul:eq('+blocki+')');
            }

            if(!this._getVar(videoList)){
                ulObj.parent().prev('.block_title').css('display','none');
                ulObj.parent().css('display','none');
            }else{
                ulObj.parent().prev('.block_title').css('display','block');
                ulObj.parent().css('display','block');
            }

            var liStr = '';
            ulObj.html('');console.log(videoList);
            if(videoList.length>0){
                for(var key in videoList){
                    videoList[key].poster = videoList[key].poster?videoList[key].poster:'static/img/src/default/260x150.png';
                    var angleStr = (parseInt(videoList[key].orientation)==0 && parseInt(videoList[key].ispic))?$conf.angleImage:'';
                    liStr = '<li class="h_item"><a href="'+'videoRoom.php?videoid='+videoList[key].videoID+'"><i></i><b></b>'
                        +'<div class="img_block"><img class="'+angleStr+'" src="'+videoList[key].poster+'">'
                        +'</div><div class="liveinfo">'
                        +'<p>'+videoList[key].title.replace(new RegExp("("+keyword+")","gi"),"<key>$1</key>")+'</p>'
                        +'<div class="icon1 v"></div>'
                        +'<span class="fl">'+videoList[key].viewCount+'</span>'
                        +'<div class="icon2 v"></div>'
                        +'<span class="fl">'+videoList[key].commentCount+'</span>'
                        +'<span class="fr last">'+videoList[key].gameName+'</span></div></a></li>';
                    ulObj.append(liStr);
                }}else{
                liStr = '<div style="font-size:14px;;margin:40px 0px 40px 80px;">暂无数据</div>';
                ulObj.append(liStr);
            }
            hoveritem();
            angleImage(ulObj);
        },
        animate:function(){
            var o = this;
            $('.tabcard').click(function(){
                $('.tabcard').removeClass('cur');
                $(this).addClass('cur');
                o._type = parseInt($(this).index());
                o._pageCur = 1;
                o.request();
                if($(this).index()==0)
                    $('.pageIndex').css('display','none');
                else
                    $('.pageIndex').css('display','block');
            });
        },
        pageIndexRefresh:function(){
            $('.pageIndex').replaceWith('<div class="pageIndex"></div>');

        },

        search:function(){
            var o = this;
            $('.searchdiv input.txt').val(decodeURIComponent($_GET['key']));
            $('.btn_div').click(function(){
                if($('.searchdiv input').val())
                    o.jumpToSearch();
            })
            $('.searchdiv input').keypress(function(e){
                if($('.searchdiv input').val()){
                    if(e.keyCode=='13')
                        o.jumpToSearch();
                }
            });
        },
        jumpToSearch:function(){
            var keyStr = $('.searchdiv input').val();//alert($('input.search')[0].defaultValue+'----');
            //if(keyStr && keyStr != $('input.search')[0].defaultValue)
            document.location.href = 'search.php?key='+encodeURIComponent(keyStr);
        }
    }
    var keyObj = new keyc();
    var loadCount = 4;

    //function search()
    $(function(){
        /* $(".navi .txt a.cur").removeClass("cur");
         $(".navi .txt:eq(1) a").addClass("cur");  */
        $('input.search').val(decodeURIComponent($_GET['key']));
        var searchObj = document.getElementsByClassName('search');
        if(searchObj[0].value==searchObj[0].defaultValue)
            searchObj[0].style.color='#999';
        else
            searchObj[0].style.color='#444';

        $(".block_title .fr").click(function(){
            $(".block_title .fr.cur").removeClass("cur");
            $(this).addClass("cur");
            var type = 4-parseInt($(this).index());
            $('.block_live li').remove();
            hd._getLiveList(gid,loadCount,0,0,type);
            $('.more_live').text('加载更多直播');
        })
        $('.more_live').click(function(){//console.log(this)
            var total = parseInt($('.block_title .num').text());
            var len = parseInt($('.block_live ul li').length);
            if(len>=total)
                $(this).text('已经到底了～^～');
            else{
                var type = 4-parseInt($('.block_title .fr.cur').index());
                hd._getLiveList(gid,loadCount,0,Math.ceil(len/loadCount),type);
            }
        });
    })


    $(function(){
        $('#live').click(function(){$('.tabcard:eq(1)').trigger('click')});
        $('#anchor').click(function(){$('.tabcard:eq(2)').trigger('click')});
        $('#video').click(function(){$('.tabcard:eq(3)').trigger('click')});
    })
</script>
</body>
</html>


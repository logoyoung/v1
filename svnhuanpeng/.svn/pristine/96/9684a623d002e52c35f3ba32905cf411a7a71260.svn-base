<?php

    include_once '../../../include/init.php';
    include_once WEBSITE_PERSON."isAnchor.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>我的空间-欢朋直播-精彩手游直播平台！</title>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <?php include WEBSITE_TPL.'commSource.php';?>
    <link rel="stylesheet" type="text/css" href="<?php echo __CSS__;?>person.css?v=1.0.5">
    <style type="text/css">
        body{
            background-color: #eeeeee;
        }
        #zoneCon{
            padding: 0px 20px;
        }
        #zoneCon .select_tab{
            float: none;
            border-bottom: 1px solid #e0e0e0;
			/*margin-top: 27px;*/
            margin-top: 35px;
        }
		#zoneCon .select_tab li{
			width: 104px;
			margin-right: 12px;
		}
        #zoneCon .zoneopt .delBtnGroup{
            margin-top: -50px;
        }
        #zoneCon .zoneopt .delBtnGroup .btn{
            border-radius: 3px;
            border-style: solid;
            border-width: 1px;
            outline: none;
        }
        #zoneCon .zoneopt .delBtnGroup #deleteVideo{
            padding: 10px 20px;
            background-color: #ff7800;
            border-color: #ff7800;
            color: #fff;
        }
        #zoneCon .zoneopt .delBtnGroup #finishdel{
            padding: 10px 15px;
            background-color: #FF7800;
            border-color: #FF7800;
            color:#fff;
        } 
        #zoneCon .zoneopt .delBtnGroup #canceldel{
            padding: 10px 15px;
        }

		#zoneCon .zoneopt .delBtnGroup #cancelCheck{
			padding: 10px 20px;
			background-color: #f44336;
			border-color: #f44336;
		}
		#zoneCon .zoneopt .delBtnGroup #finishCancelCheck{
			padding: 10px 15px;
			background-color: #03a9f4;
			border-color: #03a9f4;
		}
		#zoneCon .zoneopt .delBtnGroup #cancelCancelCheck{
			padding: 10px 15px;
		}
        #zoneCon .seloptGroup .selopt{
            background-color: #fafafa;
            padding: 12px 0px;
        }
        #zoneCon .selopt.gametype{
            border-bottom: 1px dashed #dee2e5;
        }

        #zoneCon .selopt label{
            height: 30px;
            float: left;
            line-height: 30px;
            text-align: center;
            width: 110px;
            font-size: 14px;
            color: #a8a8a8;
        }

        #zoneCon .selopt span{
            float:left;
            height: 30px;
            text-align: center;
            font-size: 14px;
            line-height: 30px;
            margin-right: 20px;
            padding: 0px 10px;
            cursor: pointer;
            position:relative;
			min-width:56px;
        }

        #zoneCon .selopt span:hover ,#zoneCon .selopt span.checked{
            background-color: #ff9e48;
            color: #ffffff;
        }

        #zoneCon .selopt.orderby span{
            padding-right: 5px;
        }

		#zoneCon .orderby span.checked .arrow_bt{
			background-position:-82px -125px;
		}

        #zoneCon .selopt .personal_icon{
            display: block;
            width: 15px;
            height: 15px;
            float: right;
            margin-top: 8px;
            margin-left: 5px;
			margin-right:2px;
        }

        #zoneCon .tabConDiv .pvideoOne{
            padding: 30px 44px 36px 30px;
            border:1px solid #cccccc;
            margin-bottom: 20px;
            position: relative;
        }

        #zoneCon .tabConDiv .pvideoOne:hover{
            box-shadow: 0px 1px 1px #e0e0e0;
            -webkit-box-shadow: 0px 1px 1px #e0e0e0;
            -o-box-shadow: 0px 1px 1px #e0e0e0;
            -moz-box-shadow: 0px 1px 1px #e0e0e0;
        }

        #zoneCon .tabConDiv .pvideoOne .countDownTag{
            width: 120px;
            height: 32px;
            background-color: #eee;
            line-height: 32px;
            text-align: center;
            color: #999;
            position: absolute;
            top: 0;
            right: 0;
            border-radius: 4px;
        }

        #zoneCon .tabConDiv .pvideoOne .countDownTag .days{
            color: #FF7800;
        }

        #zoneCon .tabConDiv .pvideoOne .liveOne{
            width: auto;
            margin: 0px;
			box-shadow: none;
			-webkit-box-shadow: none;
			-o-box-shadow: none;
			-moz-box-shadow: none;
        }

        #zoneCon .tabConDiv .pvideoOne .imagecontainer{
            margin: 0;
            height: 144px;
        }

		#zoneCon .tabConDiv .imagecontainer .previewOptModal{
			display: none;
			position: absolute;
			background-color: rgba(0, 0, 0, 0.6);
			height: 144px !important;
			width: 260px;
			top: 0px;
		}

		#zoneCon .tabConDiv .imagecontainer .previewOptModal .previewOpt{
			display: block;
			border: 2px solid #FF7800;
			width: 98px;
			height: 42px;
			color: #FF7800;
			font-size: 16px;
			line-height: 42px;
			position: absolute;
			left: 50%;
			top: 50%;
			margin-left: -51px;
			margin-top: -23px;
		}
		#zoneCon .tabConDiv .imagecontainer .previewOptModal .previewOpt:hover{
			background-color: #FF7800;
			color: #fff;
		}
		#zoneCon .tabConDiv .imagecontainer:hover .previewOptModal{
			display: block;
		}
        #zoneCon .tabConDiv .pvideoOne .pvideoinfo{
            height: 144px;
            float: left;
            width: 454px;
        }
        #zoneCon .tabConDiv .pvideoOne .pvideoinfo .pgroup{
            height: 36px;
            line-height: 36px;
        }
        #zoneCon .tabConDiv .tab_con .follow_info{
            margin: 0px;
        }
        .pvideoinfo .pgroup .label{
            width: 104px;
            text-align: right;
            display: block;
            font-size: 16px;
            color: #666666;
            float: left;
        }
        .pvideoinfo .pgroup .pinfo{
            margin-left: 124px;
            max-width: 321px;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 14px;
            color: #414141;
        }
        .pvideoinfo .pgroup .pinfo.title{
            font-size: 20px;
            color: #333333;
            font-weight: bold;
        }
        .pvideoOne .btn{
            height: 44px;
            width: 116px;
            color: #ffffff;
            font-size: 16px;
            float: right;
            padding: 0px;
            margin-left: 0px;
            /*margin-top: 102px;*/
            border-radius: 4px;
            /*box-shadow: 1px 1px 3px #c9c9c9;*/
        }
        .pvideoOne .btn.publish{
            background-color: #FF7800;
            border-color: #FF7800;
        }
        .pvideoOne .btn.published{
            background-color: #cccccc;
            border-color: #cccccc;
        }

        #ensureDelete{
            margin-left: 120px;
            background-color: #f44336;
            border-color: #f44336;
        }
		#ensureCancelCheck{
			margin-left: 120px;
			background-color: #f44336;
			border-color: #f44336;
		}
        .tab_con .noVideo{
            margin-left: 130px;
            /*line-height: 90px;*/
            font-size: 14px;
            color: #303031;
        }
        .tab_con .noVideo .logo{
            width: 260px;
            height: 260px;
            float: left;
            margin-right: 20px;
            /*border:1px solid #e0e0e0;*/
            border-radius: 45px;
        }
        .tab_con .noVideo .noticeword{
            margin-top:100px;
            font-size: 20px;
        }
        .tab_con .noVideo .noticeword p{
            margin-bottom: 12px;
            margin-top: 0px;
        }

        .tab_con .noticeEdit{
            padding: 0 20px 0 40px;
        }
        .tab_con .noticeEdit .editbody{
            font-size: 16px;
            color: #999;
            line-height: 25px;
            min-height: 60px;
            border: 1px solid #eee;
            padding: 20px;
        }
        .tab_con .noticeEdit #editNotice{
            color:#fff;
			width: 118px;
			height: 40px;
			background-color: #ff7800;
			border-radius: 4px;
			border-color: #ff7800;
			float: right;
			margin-top: 40px;
			font-size: 14px;
        }   
        .tab_con .noticeOnEdit{
            height: 280px;
            margin: 30px 20px 0 20px;
        }
        .tab_con .noticeOnEdit .editbody{
            border:1px solid #e0e0e0;
        }
        .tab_con .noticeOnEdit .editbody .wordnum{
            text-align: right;
            margin: 10px 30px 20px 0px;
            font-size: 14px;
            color: #cdcdcd;
        }
        #Notice_wd{
            outline: none;
            border: 0;
            resize: none;
            width: 826px;
            padding: 20px;
            height: 126px;
            font-size: 14px;
            color: #666;
        }
        #submitNotice{
            width: 118px;
            height: 42px;
            background-color: #FF7800;
            border-radius: 4px;
            border-color: #FF7800;
            float:right;
            margin-top: 40px;
            font-size: 16px;
            color:#fff;
        }
		#zoneCon .capacityDiv{
			margin-left: 12px;
            font-size: 14px;
            color: #b1b1b1;
		}
		#zoneCon .capacityDiv .anchor_icon{
			width: 20px;
			height: 20px;
			float:left;
			background-position: -358px -259px;
			margin-right: 8px;
		}

		#zoneCon .capacityDiv p{
			line-height: 20px;
			float: left;
			/*margin-right: 10px;*/
			color:#999999;
            margin: 0 10px 0 0;
		}
		#zoneCon .capacityDiv .capacityBarDiv{
			/*width: 148px;*/
			height: 20px;
			float: left;
			/*border: 1px solid #fee0b8;*/
			overflow: hidden;
		}
		#zoneCon .capacityDiv .capacityBarDiv .capacityText{
			line-height: 20px;
			text-align: center;
			display: block;
			position: absolute;
			/*width: 148px;*/
		}
        #zoneCon .capacityDiv .capacityBarDiv .capacityText .currVideoCount,#zoneCon .capacityDiv .capacityBarDiv .capacityText .videoLimitCount{
            color: #ff7800;
        }
		#zoneCon .capacityDiv .capacityBarDiv .capacityBar{
			height:20px;
			background-color: #fee0b8;
			float: left;
			width: 40%;
		}

        #zoneCon .videoList .liveOne{
            transition: initial;
            -webkit-transition: initial;
            -moz-transition: initial;
        }
    </style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null,false);</script>
    <div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
        <div class="content">
            <div id="zoneCon">
                <div class="zoneopt">
                    <ul class="select_tab">
                        <li class="selected">待发布视频</li>
<!--						<li>审核中录像</li>-->
                        <li>已发布视频</li>
                        <li>编辑公告</li>
                        <div class="clear"></div>
                    </ul>
                </div>
                <div class="tabConDiv">
                    <div class="tab_con unpublishtab">
<!--                        <div class="seloptGroup mt-20">-->
<!--                            <div class="gametype selopt">-->
<!--								<div class="labelDiv left">-->
<!--                                	<label>类型</label>-->
<!--								</div>-->
<!--								<div class="gameTypeSelectDiv left" style="width: 798px;">-->
<!--									<span class="checked">全部类型</span>-->
<!--									<span>全民枪战</span>-->
<!--									<span>我的世界</span>-->
<!--									<span>偶像大师</span>-->
<!--									<span>穿越火线</span>-->
<!--									<div class="clear"></div>-->
<!--								</div>-->
<!--								<div class="clear"></div>-->
<!--                            </div>-->
<!--                            <div class="orderby selopt">-->
<!--                                <label>排序</label>-->
<!--                                <span data-order="0" class="checked">时间顺序 <em class="personal_icon arrow_bt"></em></span>-->
<!--                                <span data-order="1">时长顺序 <em class="personal_icon arrow_up"></em></span>-->
<!--                                <div class="clear"></div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--						<div class="capacityDiv mt-20 none">-->
<!--							<span class="personal_icon"></span>-->
<!--							<p>您的空间容量是:</p>-->
<!--							<span class="capacityBarDiv">-->
<!--								<span class="capacityText">2.4G/6G</span>-->
<!--								<strong class="capacityBar" width="0%"></strong>-->
<!--							</span>-->
<!--							<div class="clear"></div>-->
<!--						</div>-->
                        <div class="capacityDiv mt-20">
                            <span class="anchor_icon"></span>
                            <!--                            <p>您的空间容量是:</p>-->
                            <span class="capacityBarDiv">
								<span class="capacityText">
                                    <span>已发布</span>
                                    <span class="currVideoCount">0</span>
                                    <span>视频， 可发布</span>
                                    <span class="videoLimitCount">0</span>
                                    <span>个（主播每升一级提升一个存储位）</span>
                                </span>
							</span>
                            <div class="clear"></div>
                        </div>
                        <div class="pvideoDiv mt-40 videoList"></div>
						<div class="pageIndex"></div>
                    </div>
<!--					<div class="tab_con checkPending none">-->
<!---->
<!--						<div class="follow_info">-->
<!--							<div class="tabCon videoList mt-10 "></div>-->
<!--						</div>-->
<!--					</div>-->
                    <div class="tab_con publishedtab none">
                        <div class="capacityDiv mt-20">
                            <span class="anchor_icon"></span>
<!--                            <p>您的空间容量是:</p>-->
							<span class="capacityBarDiv">
								<span class="capacityText">
                                    <span>已发布</span>
                                    <span class="currVideoCount">0</span>
                                    <span>视频， 可发布</span>
                                    <span class="videoLimitCount">0</span>
                                    <span>个（主播每升一级提升一个存储位）</span>
                                </span>
							</span>
                            <div class="clear"></div>
                        </div>
                        <div class="follow_info unpvideoDiv">
                            <div class="tabCon videoList mt-40">
<!--                                <div class="liveOne" data-videoid='100'>-->
<!--                                    <a >-->
<!--                                        <div class="imagecontainer">-->
<!--                                            <img src="http://dev-img.huanpeng.com/8/a/8ab0734ab6c33ed35ec33cada7025306.jpg" alt=""/>-->
<!--                                            <div class="live_anchor_name">-->
<!--                                                <span>我的滑板鞋最最时尚</span>-->
<!--                                            </div>-->
<!--                                            <div class="playopt"></div>-->
<!--                                        </div>-->
<!--                                        <div class="liveinfo">-->
<!--                                            <div class="videoName">精彩直播 07/24 12:09</div>-->
<!--                                            <div class="clear"></div>-->
<!--                                            <div class="liveDetail">-->
<!--                                                <span class="anchor_icon viewerIcon"></span>-->
<!--                                                <span>100</span>-->
<!--                                                <span class="anchor_icon commentIcon"></span>-->
<!--                                                <span>100</span>-->
<!--                                                <span class="game_name">flappy bird</span>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </a>-->
<!--                                </div>-->
                            </div>
                        </div>
						<div class="pageIndex"></div>
                    </div>
                    <div class="tab_con editnotice none">
                        <div class="noticeEdit mt-40" style="margin-top: 40px;">
                            <div class="edithead"></div>
                            <div class="editbody">
                                #编辑您想跟观众说的话,会出现在直播间哦#
                            </div>
                            <div class="editfoot"></div>
                            <div class="editopt"></div>
                            <button class="btn" id="editNotice">编辑</button>
                            <div class="clear"></div>
                        </div>
                        <div class="noticeOnEdit none" style="margin-top: 40px;">
                            <div class="edithead"></div>
                            <div class="editbody">
                                <textarea id="Notice_wd" maxlength="300" warp="virtual" placeholder="#编辑您想跟观众说的话,会出现在直播间哦#"></textarea>
                                <div class="wordnum">300</div>
                            </div>
                            <div class="editfoot"></div>
                            <div class="editopt"></div>
                            <button class="btn" id="submitNotice">提交待审核</button>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="clear"></div>
    </div>
<?php include_once WEBSITE_MAIN . 'footerSub.php'; ?>
</body>
<script type="text/javascript" src="zone2.js?v=1.0.4"></script>
<script type="text/javascript">
	personalCenter_sidebar('zone')
	$(document).ready(function(){
		MyZone.init();
	});
</script>
</html>
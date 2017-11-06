
<?php
    $path = realpath(__DIR__);
    include_once '../include/init.php';
    include_once 'initCookie.php';
    $videoid = isset($_GET['videoid']) ? (int)$_GET['videoid'] : 0;
    $videoRoom = "<script>var videoID = $videoid;</script>";
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <link rel="stylesheet" type="text/css" href="<?php echo STATIC_CSS_PATH;?>jquery.mCustomScrollbar.css"/>
<!--	<link rel="stylesheet" type="text/css" href="static/css/common.css">-->
<!--	<link rel="stylesheet" href="static/css/home_v3.css"/>-->

    <?php include WEBSITE_TPL.'commSource.php';?>
	<link rel="stylesheet" type="text/css" href="<?php echo STATIC_CSS_PATH;?>liveroom_v2.css?v=1.0.4">
	<link rel="stylesheet" type="text/css" href="<?php echo STATIC_CSS_PATH;?>videoroom.css?v=1.0.4">
<!--	<script type="text/javascript" src="static/js/jquery-1.9.1.min.js"></script>-->
    <script type="text/javascript" src="<?php echo STATIC_JS_PATH;?>jquery.zclip.js"></script>
    <script type="text/javascript" src="<?php echo STATIC_JS_PATH;?>jquery.mCustomScrollbar.concat.min.js"></script>
<!--	<script type="text/javascript" src="static/js/common.js"></script>-->
	<?php echo $videoRoom;?>
    <script type="text/javascript" src="<?php echo STATIC_JS_PATH;?>share.js?v=1.0.4"></script>
	<script type="text/javascript" src="<?php echo STATIC_JS_PATH;?>videoRoom2.js?v=1.0.4"></script>
	<script type="text/javascript" src="<?php echo STATIC_JS_PATH;?>swfobject.js"></script>
	<style>
		.liveRoom_opt .icon{
			/*background-color: #e0e0e0;*/
		}
		#emojiBtn{
			width: 25px;
			margin: 0px;
		}
		<?php echo "video{position:relative;-moz-transform:rotate({$deg}deg);-webkit-transform:rotate({$deg}deg);-o-transform:rotate({$deg}deg);-ms-transform:rotate({$deg}deg);transform:rotate({$deg}deg);}" ;?>
		.anchor_icon.videoCountIcon{
			background-position: -521px -84px;
		}
		.container_video .player_info{
			margin-left: 16px;
		}
		.anchor_icon.videoShare{
			background-position: -673px -212px;
		}
        .upDiv .anchor_icon.likeIcon.liked{
            background-position: -528px -257px;
        }
		.collectoptDiv .anchor_icon.collectVideoIcon.collected{
			background-position: -485px -257px;
		}

	</style>


</head>
<body style="background-image: none">
<?php  include $path.'/head.php'; ?>
<script>
    new head(null,false);
</script>
<div class="container_video">
    <div class="liveRoom_nav">
        <div class="live_nav_player">
            <div class="player_face">
                <img src="" alt=""/>
            </div>
            <div class="player_info">
                <p class="publisher_name"></p>
                <div class="anchor-live-stat onLiving none">
                    <span class="anchor_icon"></span>
                    <span></span>
                </div>
                <div class="clear"></div>
                <div class="player_otherdesc ">

                </div>
            </div>
        </div>
        <div class="live_nav_opshow">
            <div class="nav_attention">
                <div id="followbtn" class="nav_attention_right">
                    <span class="anchor_icon followIcon3"></span>
                    <em style="font-style: normal;">关注</em>
                </div>
                <div class="nav_attention_left">0</div>
                <div class="clear"></div>
            </div>
            <a class="enterliveRoom">进入直播间</a>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
	<div class="content">
		<div class="liveRoom_left">
			<div class="liveRoomContent">
<!--				<div class="videoTitle">-->
<!--					雾霾来了我们一起打僵尸-->
<!--				</div>-->
				<div class="liveRoom_video" style="background-color: #000">
                    <div id="videoPlayer"></div>
				</div>
				<div class="liveRoom_opt">
					<div class="videodetail left">
						<span class="icon anchor_icon viewerCountIcon"></span>
						<span class='text'></span>
						<span style="" class="icon anchor_icon videoCommentIcon"></span>
						<span class='text'></span>
					</div>
					<div class="videoopt right">
						<div class="upDiv left">
							<span class="icon anchor_icon likeIcon"></span>
							<span class='text'></span>
						</div>
						<div class="collectoptDiv left">
							<span id="collectVideo" class="icon anchor_icon collectVideoIcon icon_collect";"></span>
							<span class='text'></span>
						</div>
						<div class="shareoptDiv left">
							<span class="icon anchor_icon videoShare"></span>
							<span class='text' style="margin-right: 0px;width: auto;">分享</span>
							<span class="icon anchor_icon iconArrow "></span>
                            <div id="shareModal" class="shareModal none">
                                <div class="moreShare">
                                    <p class="title">分享直播至</p>
                                    <div class="shareBtn">
                                        <span class="share_icon sina-icon" data-cmd="tsina"></span>
                                        <span class="share_icon qq-icon" data-cmd="tqq"></span>
                                        <span class="share_icon qzone-icon" data-cmd="tqzone"></span>
                                        <span class="share_icon wx-icon" data-cmd="wx"></span>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="modalBody">
                                    <div id="wx-share-qrcode" class=""></div>
                                    <input class="url_text" type="text" disabled="disabled"/>
                                    <input id="copyUrl" class="btn" type="button" value="复制链接"/>
                                </div>

                            </div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<div class="videocomment">
                <p class="videocomment-title">评论</p>
				<div class="editcomment">
<!--					<div class="editheader">0/300</div>-->
					<div class="editbodyDiv">
                        <span class="user-face"><img src="" alt=""/></span>
						<div class="editbody">
<!--							<div class="placeholder">这个录像怎么样？想说什么就马上说吧！</div>-->
							<div class="unlogin">发表评论请先 <a onclick="loginFast.login(0)">登录</a>或<a onclick="loginFast.login(1)">注册</a></div>
						</div>
                        <div id="publishcomment" >发送</div>
                        <div class="clear"></div>
					</div>
					<div class="editfooter">
						<div class="emoji left">
							<span id="emojiBtn" class="anchor_icon emoji left"></span>
							<span class='left'>表情</span>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="allcomment">
<!--					<div class="commentheader">-->
<!--						<div>全部评论<span>（）</span></div>-->
<!--					</div>-->
					<div class="commentbody">
                        <div class="no-data-div">
                            <img src="static/img/noData/vr-nocomment.png" alt=""/>
                            <p>快来给主播留言吧～</p>
                        </div>
					</div>
					<div class="commentfooter">
						<div class="pageCode"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="video_right">
		<div class="right_content">
<!--			<div class="anchorannouncement">-->
<!--				<div class="announcementtitle">-->
<!--					<span class="icon anchor_icon"></span>-->
<!--					<span>主播公告</span>-->
<!--				</div>-->
<!--				<div class="announcementinfo">-->
<!--					<div class="text">-->
<!--					faslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfssfaslfjsalkdfjslkjdfss-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
			<div class="videoListDiv">
				<ul class="selected_tab">
					<li class="selected">主播视频</li>
					<li>相似视频</li>
					<div class="clear"></div>
				</ul>
				<div class="tabcon videolist"></div>
                <div class="tabcon videolist none"></div>
			</div>
			<div class="liveNowListDiv">
				<div class="ln_title">正在直播</div>
				<div class="livelist">
					<div class="videoOne"></div>
				</div>
				<div class="morelive"><a href="LiveHall.php">更多直播</a></div>

			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php include_once WEBSITE_MAIN . 'footer.php';?>
<script>
	$(document).ready(function(){
		(function(){
			initText();
			function initText(){
				var text = $('.announcementinfo .text');
				if(text.height() <= 50)
					return;

				text.addClass('retracts').append('<span class="showopt show">[展开]</span><span class="dotback"></span>');
				var showopt = $('.announcementinfo .showopt');
				showopt.bind('click',function(){
					if(showopt.hasClass('show'))
						showallText();

					else if(showopt.hasClass('hidden'))
						hiddenText();

					function  showallText(){
						text.removeClass('retracts').find('.dotback').remove();
						showopt.removeClass('show').addClass('hidden').text('[收起]');
					}
					function hiddenText(){
						text.addClass('retracts').append('<span class="dotback"></span>');
						showopt.removeClass('hidden').addClass('show').text('[展开]');
					}
				})
			}
		}());

		$(window).resize(function(){
			var width = $('.liveRoom_left').width();
			if(width < 670)
				$('.nav_attention_left').addClass('none');
			else
				$('.nav_attention_left').removeClass('none');
		});

		VRoom.init();

		(function(){
			//表情按钮
			var options = {
				id : 'facebox',
				path : 'static/img/emoji/',
				assign : 'commentval',
				tip : 'em_'
			};
			var selector = "#emojiBtn";
			Emoji.init(selector, options);
		}());

//        $('.videolist').mCustomScrollbar({
//            snapAmount:6,
//            scrollButton:{enable:true},
//            keyborard:{scrollAmount:6},
//            mouseWheel:{deltaFactor:6},
//            scrollInertia:400
//        });
	});
</script>
</body>
</html>
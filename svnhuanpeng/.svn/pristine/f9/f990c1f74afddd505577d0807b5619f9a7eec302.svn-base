<div id="top_control">
	<style>
		#top_control .icon_set_top{
			background:url(../static/img/icon/bg_btn_navi_2.png) no-repeat;
		}

		#top_control .opt_block:hover .jubao
		{
			background: #ff7800;
			border-color:#ff7800;
		}
		#top_control .jubao{
			border-bottom-left-radius: 4px;
			border-top-left-radius: 4px;
			background-position: -200px 0px;
			border: 1px solid #d2d2d2;
		}
		</style>
	<div class="opt_block">
		<div class="opt icon_set to_top">
			<div class="txt">回顶部</div>
		</div>
	</div>
	<div class="opt_block">
		<div class=" opt icon_set app ">
			<div class="txt">客户端</div>
			<div class="app_down">
				<div class="down_title">欢朋移动客户端</div>
				<div class="imgcode">
					<img src="./static/img/src/qrcode/qrcode_home.png">
				</div>
			</div>
		</div>
	</div>
	<div class="opt_block">
		<div class="opt icon_set weixin ">
			<div class="txt">微信</div>
			<div class="share">
				<div class="share_title">官方微信</div>
				<div class="share_code">
					<img src="./static/img/src/qrcode/weixin_qrcode.png">
				</div>

			</div>
		</div>
	</div>
	<div class="opt_block">
		<a href="http://sighttp.qq.com/authd?IDKEY=7eba570a23408fe6a57be33a4a5005a975c3c3be71f5b7e3" target="_blank">
			<div class="opt icon_set beanchor ">
				<div class="txt">客服</div>
			</div>
		</a>
	</div>
	<div class="opt_block">
		<a href="http://jb.ccm.gov.cn/" target="_blank">
			<div class="opt icon_set_top jubao ">
				<div class="txt">举报</div>
			</div>
		</a>
	</div>
</div>
<script>
    $(window).scroll(function(){
        if($(window).scrollTop() >= 50){
            $('.to_top').show();
        }else{
            $('.to_top').hide();
        }
    });
    $(".to_top").click(function () {
        var speed=200;
        $('body,html').animate({ scrollTop: 0 }, speed,function(){
            return;
        });
    });

    $(".weixin_share").hover(function(){
        $(this).children(".weixin_qrcode").show();
        $(this).children(".weixin_qrcode").css("display","block");
    },function(){
        $(this).children(".weixin_qrcode").hide();
        $(this).children(".weixin_qrcode").css("display","none");
    });

    $(".weibo_share").hover(function(){
        $(this).children(".weibo_qrcode").show();
        $(this).children(".weibo_qrcode").css("display","block");
    },function(){
        $(this).children(".weibo_qrcode").hide();
        $(this).children(".weibo_qrcode").css("display","none");
    });
</script>
$(function(){
    var guessYouLike_num = 6;
	var hp_flash = {
		init: function(){
			//flash初始化
			var file = $conf.domain + 'static/flash/home_rtmpplayer.swf?v=1.0.4';
			var uid = getCookie('_uid');
            var Live_UID =  parseInt($('.player-nav li:eq(0)').attr('data-uid'));
            var Liveroom_ID = parseInt($('.player-nav li:eq(0)').attr('data-roomID'));

			var flashvar = {
					'urlb':$conf.domain + 'static/flash/barrage.swf?v=1.0.4',
					'urlw':$conf.domain + 'static/flash/wait.swf?v=1.0.4',
					'urld':$conf.domain + 'static/flash/dot.swf?v=1.0.4',
					'loadingURL':$conf.domain + 'static/flash/loading.swf?v=1.0.4',
					'UIButtonURL':$conf.domain + 'static/flash/UIButton.swf?v=1.0.4',
					'UID':uid?uid:0,
					'hostID':Live_UID?Live_UID:0,
                	'urlPHP' : $conf.api + 'live/getStreamList.php',
					'recommendPHP':$conf.api + 'other/flashRecommend.php',
					'LiveRecommendURL':$conf.domain + 'static/flash/LiveRecommend.swf?v=1.0.4',
                    "percentLoadingURL":$conf.domain + 'static/flash/percentLoading.swf?v=1.0.6'
			};

			var param = {
					quality:'high',
					bgcolor:'#1a1a1a',
					allowScriptAccess:'always',
					windowlessVideo:'1',
					wmode:'Opaque'
			};
			var attribute = {
					name:'rtmpplayer',
					allowScriptAccess:'always',
					windowlessVideo:'1',
					wmode:'Opaque'
			};

			try{
                swfobject.embedSWF(file, 'rtmpplayer', '100%', '100%', '9.0.0', 'expressInstall.swf?v=1.0.4', flashvar, param, attribute);
                runSwfFunction('rtmpplayer', 'setHostID',parseInt(Live_UID));
                // initPlayer('rtmpplayer',Live_UID,$conf.domain+Liveroom_ID);
                $('.play-content').css('visibility','visible');
			}
			catch (e){
				console.log(e);
			}

			if(!swfobject.hasFlashPlayerVersion("9.0.0")) {
				document.getElementById('install-flash').style.display = 'block';
			}


			$('.recommendBox,.game-listBox').show();
		}
	};
	var hp_style_other = {
		init : function(){
            angleImage($conf.angleImage);
            hp_style_other._initSwiper();
			$(".change-btn").click(function(){
						var like_data = {
								type:"post",
								dataType:"json",
								url: $conf.api+"other/guessYouLike.php"
						};
						ajaxRequest(like_data,function(d){
								var like_html = [];
								var list = d.list;
								for(var i = 0; i < guessYouLike_num; i++){
                                    like_html.push(CreateLi(list[i]));
								}

								$('.likeBox-ul').html(like_html);
                            	angleImage($conf.angleImage);

						});

						function CreateLi(d) {
							if(d.orientation == 0){
                                var tpl = '<li><a href="' + $conf.domain + d.roomID + '">\
								<div class="img-block"><i class="icon-play"></i><b class="mask"></b>\
                            <img class="angle_class" src="'+ d.poster + '"/></div><div class="live-info">\
                            <p>' + d.title + '</p><div class="live-text"><i class="icon-p1"></i>\
                            <span class="live-infon">' + d.nick + '</span><i class="icon-p2"></i>\
                            <span class="live-infor">' + d.userCount + '</span>\
                            <span class="livesm-gm fc-orange r">'+ d.gameName + '</span></div></div></a></li>';
                                return tpl;
							}else{
                                var tpl = '<li><a href="' + $conf.domain + d.roomID + '">\
								<div class="img-block"><i class="icon-play"></i><b class="mask"></b>\
                            <img src="'+ d.poster + '"/></div><div class="live-info">\
                            <p>' + d.title + '</p><div class="live-text"><i class="icon-p1"></i>\
                            <span class="live-infon">' + d.nick + '</span><i class="icon-p2"></i>\
                            <span class="live-infor">' + d.userCount + '</span>\
                            <span class="livesm-gm fc-orange r">'+ d.gameName + '</span></div></div></a></li>';
                                return tpl;
							}

                        }

			});

			//最新最热切换下划线
			$('.gamebtnBox a').mouseover(function(){
						$(this).parent().children().removeClass('cur');
						$(this).addClass('cur');
						var spani = parseInt($(this).index());
						var marginLeft = 0;
						if(spani==1)
								marginLeft= 62+'px';
						if(spani==2)
								marginLeft= 137+'px';
						$(this).parent().children("div[class='underline-min']").css('marginLeft',marginLeft);
				});

			//排行榜区域滑动门代码
			$(".rank-tabBox a").click(function(){
					$(".rank-tabBtn1 .rank-tabBtn2").removeClass("cur");
					$(this).addClass("cur");
					if(parseInt($(this).index())==0)
							$('.rank-tabBox .underline').css('marginLeft','0px');
					else
							$('.rank-tabBox .underline').css('marginLeft','145px');
			});

			//排行榜样式控制
			$('.ranklist').find("li i:eq(0)").addClass('icon-rank1');
			$('.ranklist').find("li i:eq(1)").addClass('icon-rank2');
			$('.ranklist').find("li i:eq(2)").addClass('icon-rank3');
			$('.ranklist').find("li i:eq(3)").text('4.');
			$('.ranklist').find("li i:eq(4)").text('5.');

			//flash轮播切换按钮加cur
			$(".recommendBox").children().eq(0).addClass("cur");

			//排行榜滑动门切换
			$(function(){
				$(".rank-d").click(function(){
					$(this).addClass("cur").siblings().removeClass("cur");
					var ut = $(".rank-tabBox .cur").attr("usertype");
					if (ut == "anchor") {
						$("[user-type='anchor']").show().siblings().hide();
					} else{
						$("[user-type='wealth']").show().siblings().hide();
					};
					$("[date-type='day']").show().siblings(".ranktabBox").hide();
				});
				$(".rank-w").click(function(){
					$(this).addClass("cur").siblings().removeClass("cur");
					var ut = $(".rank-tabBox .cur").attr("usertype");
					if (ut == "anchor") {
						$("[user-type='anchor']").show().siblings().hide();
					} else{
						$("[user-type='wealth']").show().siblings().hide();
					};
					$("[date-type='week']").show().siblings(".ranktabBox").hide();
				});
				$(".rank-m").click(function(){
					$(this).addClass("cur").siblings().removeClass("cur");
					var ut = $(".rank-tabBox .cur").attr("usertype");
					if (ut == "anchor") {
						$("[user-type='anchor']").show().siblings().hide();
					} else{
						$("[user-type='wealth']").show().siblings().hide();
					};
					$("[date-type='month']").show().siblings(".ranktabBox").hide();
				});
				$(".rank-tabBtn1").click(function(){
					$(".rank-tabBtn1").addClass("cur");
					$(".rank-tabBtn2").removeClass("cur");
					$("[user-type='anchor']").show().siblings().hide();
				});
				$(".rank-tabBtn2").click(function(){
					$(".rank-tabBtn2").addClass("cur");
					$(".rank-tabBtn1").removeClass("cur");
					$("[user-type='wealth']").show().siblings().hide();
				});
			});

			//游戏板块滑动门切换
			$(".game-new").hover(function(){
					$(this).parent().parent().next(".gamer-live").children("[game-tab='new']").show().siblings(".gameBox-ul").hide();
			});

			$(".game-hot").hover(function(){
					$(this).parent().parent().next(".gamer-live").children("[game-tab='hot']").show().siblings(".gameBox-ul").hide();
			});

			$(".game-maxFollow").hover(function(){
					$(this).parent().parent().next(".gamer-live").children("[game-tab='maxFollow']").show().siblings(".gameBox-ul").hide();
			})	;


			$(".to_top").click(function () {
					var speed=200;
					$('body,html').animate({ scrollTop: 0 }, speed,function(){
							return;
					});
			});

			$(".weixin_share").hover(function(){
					$(this).children(".weixin_qrcode").show();
			},function(){
					$(this).children(".weixin_qrcode").hide();
			});

			$(".weibo_share").hover(function(){
					$(this).children(".weibo_qrcode").show();
			},function(){
					$(this).children(".weibo_qrcode").hide();
			});

			function flashBtn(){
				var palyer_len = $(".recommendBox li").length;
				if (palyer_len > 5) {
						$(".next-btn").addClass("cur");
						$(".next-btn").show();
				}
			}

			if ($(window).width() >= 1180){
				w1180();
				flashBtn();
			}else{
				w980();
				flashBtn();
			}

            $(".next-btn").click(function(){
                var marT = "";
                var player_tabH = parseInt($(".recommendBox").css("height"));
//						var marT = parseInt($(".recommendBox").css("marginTop"));
                var marT = 0;
                $(".recommendBox").css('marginTop',-100);
                if ($(".playBox").hasClass("w980")) {
                    $(".recommendBox").css('marginTop',-82);
                };
                $(".pre-btn").addClass("cur");
                $(".next-btn").hide();
                $(".pre-btn").show();
            });
            $(".pre-btn").click(function(){
                var player_tabH = parseInt($(".recommendBox").css("height"));
                var marT = "";
                var marT = parseInt($(".recommendBox").css("marginTop"));
//					$(".player-nav").css({
//						"position" : "static",
//						"float" : "left"
//					});
//					$(".recommendBox").css({
//						"position" : "static"
//					});
                $(".recommendBox").css('marginTop',marT + 100);
                if ($(".playBox").hasClass("w980")) {
                    $(".recommendBox").css('marginTop',marT + 82);
                };
                $(".next-btn").addClass("cur");
                $(".pre-btn").hide();
                $(".next-btn").show();
            });


            $(".player-nav li").click(function(){
                $(".player-nav li").removeClass('cur');
                $(this).addClass('cur');
                if($(this).attr('data-uid')){
                    var Live_UID =  parseInt($(this).attr('data-uid'));
                    var Liveroom_ID = parseInt($(this).attr('data-roomID'));
                    setCookie('currentLuid',Live_UID);
                    runSwfFunction('rtmpplayer', 'setHostID',parseInt(Live_UID));
                    initPlayer('rtmpplayer',Live_UID,$conf.domain+Liveroom_ID);
                }
            });

            function indexRandom(){var a = parseInt(Math.random()*5);return a;}
            $('.player-nav li').eq(indexRandom()).click();

            $(window).scroll(function(){
                if($(window).scrollTop() >= 50){
                    $('.to_top').show();
                }else{
                    $('.to_top').hide();
                }
            });
            //当窗口大小发生改变
			$(window).resize(function(){
				var _Width_resize = $(window).width();
				if (_Width_resize >= 1180){
						w1180();
				}else{
						w980();
				}

                if ($(".next-btn").is(":hidden") && $(".recommendBox li").length > 5){
                    if ($(".playBox").hasClass("w1180")) {
                        $(".recommendBox").css({"marginTop" : -100});
                    } else{
                        $(".recommendBox").css({"marginTop" : -82});
                    }
                };
			});

			function w1180() {
					$(".likeBox-ul li:eq(4),.likeBox-ul li:eq(5),.game-li>li:eq(4)").show();
					$('.nav_r').removeClass('fl_980');
					$(".playBox,.content").removeClass("w980").addClass("w1180");
				    guessYouLike_num = 6;
			}
			function w980(){
					$(".likeBox-ul>li:eq(4),.likeBox-ul>li:eq(5),.game-li>li:eq(4)").hide();
					$('.nav_r').addClass('fl_980');
					$(".playBox,.content").removeClass("w1180").addClass("w980");
                	guessYouLike_num = 4;
			}
		},
		_initSwiper : function () {
			var len = $('.swiper-container .swiper-wrapper').find('.swiper-slide').length;
			if(len > 1){
                var mySwiper = new Swiper ('.swiper-container', {
                    loop: true,
                    autoplay: 3500,
                    // 如果需要分页器
                    pagination: '.swiper-pagination'
                })
            }else{}
        }
	};
    //lazyLoad.init();
	hp_flash.init();
	hp_style_other.init();


});


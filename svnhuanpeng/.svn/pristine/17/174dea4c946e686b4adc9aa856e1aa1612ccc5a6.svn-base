var page=function(){	
	 this._on=false;
	 this._btnable=true;
	 this._dis=0;
}
page.prototype={
		init:function(){
			this._mouseopt();
			this._resize();
			this._updowntab();//translateobj();
			this._lunbo();
			this._tabCard();
			this._toTop();
			var timer=setInterval(this._setTime,4000);
			$(window).resize(function(){
			page._resize();
			})		
		},
		
		_resize:function(){
			width=$(window).width();
			var obj=$("#size,#play,#content,#liveHall,#gameType,.footer-top-contain");
			
			if(width<1180){
				var distance=page._dis*90;
				$(".playernavi ul").css("transform","translateY("+distance+"px)");
				//page._updowntab()=null;
				//$(".playernavi ul").css("transform","translateY(0px)");
				//$(".dropbtn.pre,.dropbtn.next").css("display","none");
				//$(".playernavi ul").css("margin-top","0")
				obj.removeClass("w1180");
				obj.addClass("w980");
				var block1_li=[];
				block1_li=$("#block1 li");
				for(var i in block1_li){
					if(i>3) $(block1_li[i]).css("display","none");
				}
				
				var gameBlockCount = $('#block5 .block_live').length;
				for(var i=0;i<gameBlockCount;i++){
					$('#block5 .block_live:eq('+i+') ul li:gt(5)').css('display','none');
					//console.log(i)
				}
				
				$("#block3 .block_live ul li:last").css('display','none');
				$(".rankdiv .ranklist span:last,.rankdiv .ranklist .x_line:last").css("display","block");
				
			}else{
				//page._updowntab();
				
				var distance=page._dis*108;
				$(".playernavi ul").css("transform","translateY("+distance+"px)");
				//$(".dropbtn.pre,.dropbtn.next").css("display","block");
				//$(".playernavi ul").css("margin-top","0");
				obj.removeClass("w980");
				obj.addClass("w1180");
				$("#block1 li").css("display","block");
				$("#block3 li:last-child").css("display","block");//console.log(0000); 
				$(".rankdiv .ranklist span:last,.rankdiv .ranklist .x_line:last").css("display","none");
				//console.log(1111111);
				$('#block5 .block_live ul li').css('display','block');
			}
			angleImage($(".playernavi ul"));
			angleImage($('.block_live ul'));
			angleImage($('.block.fav ul'));
		},
		_updowntab:function(){
			//$(".playernavi ul li:gt("+count+")").css({height:"0",display:"none"});
			 (function(){
				//var dis=0;
		     
			$(".dropbtn.pre").click(function(){
				//page._updown(0,dis);
				var width=$(window).width();
				if(width>=1180)
					 var Y = 108;
				else
					 var Y = 90;
				console.log(page._dis)
				if(page._dis<0){
				page._dis++;
				page._updateBtnStatus();
				var distance=page._dis*Y;
				if(!$.support.leadingWhitespace)
					$(".playernavi ul").css('marginTop',distance+"px");
				else
					$(".playernavi ul").css("transform","translateY("+distance+"px)");
				/*var pre = ['','-ms-','-moz-','-webkit-','-o-'];
				for(var k in pre){
					$(".playernavi ul").css(pre[k]+"transform",pre[k]+"translateY("+distance+"px)");
				}*/
				}
				//page._dis=dis;
				
			});
			
			$(".dropbtn.next").click(function(){
				//console.log(dis)
				//page._updown(1,dis);
				var width=$(window).width();
				if(width>=1180)
					 var Y = 108;
				else
					 var Y = 90;
				var len=$(".playernavi ul li").length;
				//var imgcount = $('#news-list img').length;
				if(page._dis>(5-len)){
					page._dis--;
					page._updateBtnStatus();
				var distance=page._dis*Y;
				//$(".playernavi ul").css("transform","translateY("+distance+"px)");
				if(!$.support.leadingWhitespace)
					$(".playernavi ul").css('marginTop',distance+"px");
				else
					$(".playernavi ul").css("transform","translateY("+distance+"px)");
				}
				});
			//page._dis=dis;
				
			})();
			//translateobj();
		},
		_updateBtnStatus:function(){
			var o = this;
			if(o._dis==0) {
				$('.playernavi .dropbtn.next').addClass('cur');
				$('.playernavi .dropbtn.pre').removeClass('cur');
			}
			else if(o._dis==-1){
				$('.playernavi .dropbtn.pre').addClass('cur');
				$('.playernavi .dropbtn.next').removeClass('cur');
			}
			else{
				$('.playernavi .dropbtn.pre').addClass('cur');
				$('.playernavi .dropbtn.next').addClass('cur');
			}
		},
		_updown:function(x,dis){dis++;console.log(dis);
			//$(".playernavi ul li:gt("+count+")").css("height","0");
			//var livestack=[];
			var count=0;
			var livelist=$(".playernavi ul li");
			var sizeType=$("#size").attr("class");
			if(sizeType=="w1180") {
				marginpx=112;
				count=4;
			}
			else {
			marginpx=92;
			count=6;
			}
			//var margintop=0;
			
			if(!x){
				//console.log($(".playernavi ul").css("transform"));
				$(".playernavi ul").css("transform","translateY(120px)");
				//console.log($(".playernavi ul").css("transform").split(","));	
			}
			else{
				//$(".playernavi ul").css("transform","translateY(0px)");
				/*margintop=$(".playernavi ul").css("margin-top");console.log(margintop);
				var maxtop=((livelist.length-count-1)*marginpx);
				var tmp=parseInt(maxtop)+parseInt(margintop);
				console.log();
				if(tmp>=0&&page._btnable){
					page._btnable=false;
				$(".playernavi ul").animate({marginTop:'-='+marginpx+'px'},200,function(){
					page._btnable=true;
					
				});
				}	*/	
			}
		},
		_lunbo:function(){
			var imgobj = $(".bannerdiv .banner img");
			$(imgobj[0]).css("display","block");
			var spanstr='';
			for(var key=0;key<imgobj.length;key++){
				if(key==imgobj.length-1) 
					spanstr+='<span class="cur"></span>';
				else
				spanstr+='<span></span>';			
			}
			$(".bannerdiv .circle").html(spanstr);
			var circleobj = $(".bannerdiv .circle span");console.log(circleobj)
			circleobj.hover(
			function(){
				$(".bannerdiv .circle span").removeClass("cur");
				$(this).addClass("cur");
				imgobj.css("display","none");
				$(imgobj[imgobj.length-1-$(this).index()]).css("display","block");
				//console.log($(this).index())
				//console.log(this._MouseOn)
				page._on=true;
				//console.log(this._MouseOn)
			},
			function(){
				page._on=false;
			}
			)
		},
		_setTime:function(){
			if(!page._on){
				//console.log(this._MouseOn)
				var obj=$(".bannerdiv .circle span.cur");//console.log(obj.next().index())
				obj.removeClass("cur");
				var imgobj = $(".bannerdiv .banner img");
				if(obj.prev().index()<0){
					
					$(".bannerdiv .circle span:last").addClass("cur");
				}
				else
					obj.prev().addClass("cur");
				
				
				imgobj.css("display","none");
				var i=$(".bannerdiv .circle span.cur").index();
				//console.log(imgobj.length-1-i);
				$(imgobj[imgobj.length-1-i]).css("display","block");
				
			}
		},
		_mouseopt:function(){
			/*$(".playernavi ul li").hover(
				function(){$(this).addClass("cur")},
				function(){console.log($(this).attr("class"))
					if($(this).attr("class")=='cur');
					$(this).removeClass("cur")}
			)*/
			/*$(".navi ul li").click(function(){
				var indexvalue = $(this).index();
				if(indexvalue==0||indexvalue==1)
					location.href='./index.php';
				if(indexvalue==2)
					location.href='./LiveHall.php';
			});*/
			/*$(".playernavi ul li ").click(function(){
				
				$(".playernavi ul li.cur").removeClass("cur");
				//$(this).removeClass("cur");
				$(this).addClass("cur")
			})
			*/
			$(".title_r span").click(function(){
				$(".title_r span.cur").removeClass("cur");
				$(this).addClass("cur");
			})
			$(".rankdiv .ranktype div").click(function(){
				$(".rankdiv .ranktype div.cur").removeClass("cur");
				$(this).addClass("cur");
				if(parseInt($(this).index())==0)
					$('.rankdiv .ranktype .underline').css('marginLeft','0px');
				else
					$('.rankdiv .ranktype .underline').css('marginLeft','131px');
			})
			/*新加样式*/
			/*$('.navi input').focus(function(){console.log('focus');
				//$(this).addClass('cur');
				$('.navi input,.navi ul li.last .search_icon .icon').addClass('cur');
			});
			$('.navi input').blur(function(){console.log('blur');
				$('.navi input,.navi ul li.last .search_icon .icon').removeClass('cur');
			});*/
			
			$('.home_type_tab span').mouseover(function(){
				$(this).parent().children().removeClass('cur');
				$(this).addClass('cur');
				var spani = parseInt($(this).index());
				var marginLeft = 0;
				if(spani==1)
					marginLeft= 59+'px';
				if(spani==2)
					marginLeft= 130+'px';
				$(this).parent().children("div[class='underline']").css('marginLeft',marginLeft);	
				//$('.home_type_tab .underline').css('marginLeft',marginLeft);			
			})
		},
		
		_tabCard:function(){
			var tabOpt = $(".tabCard .tab_opt");
			tabOpt.click(function(){
				var indexi=$(this).index();
				$(".tabCard .tab_div").css({display:"none"});
				$(".tabCard .tab_div").eq(indexi).css({display:"block"});
			});
		},
		
		_toTop:function(){
			var o = this;
			$(".to_top").click(function(){
				//var top = document.body.scrollTop;
				//o._top();
				$('body,html').animate({scrollTop:0},700,function(){
					$(".to_top").css({display:"none"});
					
				});
			});
			window.onscroll=function(){//console.log(document.documentElement.scrollTop)
				var windowtop = (typeof document.body.scrollTop == 'number')?document.body.scrollTop:document.documentElement.scrollTop;
				if(!windowtop)//火狐一直为0
					windowtop = document.documentElement.scrollTop;
				//console.log(windowtop)
				if(windowtop>400)
					$(".to_top").css({display:"block"});
				else
					$(".to_top").css({display:"none"});
			};
		}
}
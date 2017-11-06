var calUtil = {
  //当前日历显示的年份
  showYear:2016,
  //当前日历显示的月份
  showMonth:1,
  //当前日历显示的天数
  showDays:1,
  eventName:"load",
  //初始化日历
  init:function(signList){
    calUtil.setMonthAndDay();
    calUtil.draw(signList);
    calUtil.bindEnvent();
  },
  draw:function(signList){
    //绑定日历
    var str = calUtil.drawCal(calUtil.showYear,calUtil.showMonth,signList);
    $("#calendar").html(str);
    //绑定日历表头
    var calendarName=calUtil.showYear+"年"+calUtil.showMonth+"月";
    $(".calendar_month_span").html(calendarName); 
  },
  //绑定事件
  bindEnvent:function(){
    //绑定上个月事件
    $(".calendar_month_prev").click(function(){
      //ajax获取日历json数据
      var signList=[];
      calUtil.eventName="prev";
      calUtil.init(signList);
      if(WWW_common.isPc){
        if(calUtil.showYear-(new Date).getFullYear()==0){
          
      	if(calUtil.showMonth-(new Date).getMonth()<1){
          $('.calendar_month_prev').css('visibility','hidden');
          $('.liprev').css('visibility','visible');
          }else if(calUtil.showMonth-(new Date).getMonth()==1){
          	$('.linext').css('visibility','hidden');
          }
      }else{
          $('.linext').css('visibility','hidden'); 
          $('.calendar_month_prev').css('visibility','hidden');
          $('.liprev').css('visibility','visible');
      }
      }else{
        if(calUtil.showYear-(new Date).getFullYear()==0){  
          	if(calUtil.showMonth-(new Date).getMonth()<1){
              $('.calendar_month_prev').css('visibility','hidden');
          }
      	}else{
      	   $('.calendar_month_next').css('visibility','visible'); 
      	   $('.calendar_month_prev').css('visibility','hidden');
      	}
      }

      
    });
    //绑定下个月事件
    $(".calendar_month_next").click(function(){
    	$(this).remove();
      //ajax获取日历json数据
      var signList=[];
      calUtil.eventName="next";       
      calUtil.init(signList);
     
       if(WWW_common.isPc){
           if(calUtil.showYear-(new Date).getFullYear()==0){
           if(calUtil.showMonth-(new Date).getMonth()>1){
          $('.calendar_month_next').css('visibility','hidden');
          $('.linext').css('visibility','visible');
      }else if(calUtil.showMonth-(new Date).getMonth()==1){
      	$('.liprev').css('visibility','hidden');
      }
      }else{
          $('.liprev').css('visibility','hidden');
          $('.calendar_month_next').css('visibility','hidden');
          $('.linext').css('visibility','visible');
      }
      
      }else{
          if(calUtil.showYear-(new Date).getFullYear()==0){
          	if(calUtil.showMonth-(new Date).getMonth()>1){
              $('.calendar_month_next').css('visibility','hidden');
          }
        }else{
           $('.calendar_month_prve').css('visibility','visible');
           $('.calendar_month_next').css('visibility','hidden');
        }
      }
    });
    
  },
  //获取当前选择的年月
  setMonthAndDay:function(){
    switch(calUtil.eventName)
    {
      case "load":
        var current = new Date();
        calUtil.showYear=current.getFullYear();
        calUtil.showMonth=current.getMonth() + 1;
        break;
      case "prev":
        var nowMonth=$(".calendar_month_span").html().split("年")[1].split("月")[0];
        calUtil.showMonth=parseInt(nowMonth)-1;
        if(calUtil.showMonth==0)
        {
            calUtil.showMonth=12;
            calUtil.showYear-=1;
        }
        break;
      case "next":
        var nowMonth=$(".calendar_month_span").html().split("年")[1].split("月")[0];
        calUtil.showMonth=parseInt(nowMonth)+1;
        if(calUtil.showMonth==13)
        {
            calUtil.showMonth=1;
            calUtil.showYear+=1;
        }
        break;
    }
  },
  getDaysInmonth : function(iMonth, iYear){
   var dPrevDate = new Date(iYear, iMonth, 0);
   return dPrevDate.getDate();
  },
  bulidCal : function(iYear, iMonth) {
   var aMonth = new Array();
   aMonth[0] = new Array(7);
   aMonth[1] = new Array(7);
   aMonth[2] = new Array(7);
   aMonth[3] = new Array(7);
   aMonth[4] = new Array(7);
   aMonth[5] = new Array(7);
   aMonth[6] = new Array(7);
   var dCalDate = new Date(iYear, iMonth - 1, 1);
   var iDayOfFirst = dCalDate.getDay();
   var iDaysInMonth = calUtil.getDaysInmonth(iMonth, iYear);
   var iVarDate = 1;
   var d, w;
   aMonth[0][0] = "日";
   aMonth[0][1] = "一";
   aMonth[0][2] = "二";
   aMonth[0][3] = "三";
   aMonth[0][4] = "四";
   aMonth[0][5] = "五";
   aMonth[0][6] = "六";
   for (d = iDayOfFirst; d < 7; d++) {
    aMonth[1][d] = iVarDate;
    iVarDate++;
   }
   for (w = 2; w < 7; w++) {
    for (d = 0; d < 7; d++) {
     if (iVarDate <= iDaysInMonth) {
      aMonth[w][d] = iVarDate;
      iVarDate++;
     }
    }
   }
   return aMonth;
  },
  ifHasSigned : function(signList,day){
   var signed = false;
   $.each(signList,function(index,item){
    if(item.signDay == day) {

     signed = true;
     return false;
    }
   });
   return signed ;
  },
  drawCal : function(iYear, iMonth ,signList) {
   var myMonth = calUtil.bulidCal(iYear, iMonth);
   var htmls = new Array();
   htmls.push("<div class='sign_main' id='sign_layer'>");
   htmls.push("<ul class='sign_succ_calendar_title nav-sign'>");
   if(WWW_common.isPc){
   htmls.push("<li class='calendar_month_prev'>◀</li>");
   htmls.push("<li class='calendar_month_span'></li>");
   htmls.push("<li class='calendar_month_next'>▶</li>");
   }else{
   	 htmls.push("<li class='calendar_month_prev'>上月</li>");
   htmls.push("<li class='calendar_month_span'></li>");
   htmls.push("<li class='calendar_month_next'>下月</li>");
   }
   htmls.push("</ul>");
   htmls.push("<div class='sign_cal' id='sign_cal'>");
   htmls.push("<table>");
   htmls.push("<tr>");
   htmls.push("<th>" + myMonth[0][0] + "</th>");
   htmls.push("<th>" + myMonth[0][1] + "</th>");
   htmls.push("<th>" + myMonth[0][2] + "</th>");
   htmls.push("<th>" + myMonth[0][3] + "</th>");
   htmls.push("<th>" + myMonth[0][4] + "</th>");
   htmls.push("<th>" + myMonth[0][5] + "</th>");
   htmls.push("<th>" + myMonth[0][6] + "</th>");
   htmls.push("</tr>");
   var d, w;
   var conf=window.Sign||{};
   
   for (w = 1; w < 7; w++) {
    htmls.push("<tr>");
    for (d = 0; d < 7; d++) {
    var anum=myMonth[w][d];
    var date='date'+anum; 
    var confKey=this.showYear+''+(this.showMonth>9?this.showMonth:'0'+this.showMonth),confItem=null;
    if(myMonth[w][d]){
    	confKey += myMonth[w][d]>9?myMonth[w][d]:('0'+myMonth[w][d]);
    	confItem = conf[confKey];
    	switch(parseInt(confItem)){
        case  1://已签到
	   		htmls.push("<td class='"+date+" qiandao'><p>" + (!isNaN(myMonth[w][d]) ? myMonth[w][d] : " ") + "</p></td>");
                        break;
        case  2://今天
	   		htmls.push("<td class='"+date+" able-qiandao'><p>" + (!isNaN(myMonth[w][d]) ? myMonth[w][d] : " ") + "</p></td>");
                        break;
	    	case  3://未签到
	   		htmls.push("<td class='"+date+" disable-qiandao'><p>" + (!isNaN(myMonth[w][d]) ? myMonth[w][d] : " ") + "</p></td>");
                        break;
	    	case  4://礼品
	   		htmls.push("<td class='"+date+" lipin'><p><span>" + (!isNaN(myMonth[w][d]) ? myMonth[w][d] : " ") + "</span></p></td>");
                        break;
        case  5://礼品领取
    		htmls.push("<td class='"+date+" relipin'><p><span>" + (!isNaN(myMonth[w][d]) ? myMonth[w][d] : " ") + "</span></p></td>");
                        break;
        default://无
        htmls.push("<td><p>" + (!isNaN(myMonth[w][d]) ? myMonth[w][d] : " ") + "</p></td>");
    	}
    }else{
      	htmls.push("<td class='date'><p>" + (!isNaN(myMonth[w][d]) ? myMonth[w][d] : " ") + "</p></td>");
    	
    }

    }
    htmls.push("</tr>");
   }
   htmls.push("</table>");
   htmls.push("</div>");
   htmls.push("</div>");
   return htmls.join('');
  }
};
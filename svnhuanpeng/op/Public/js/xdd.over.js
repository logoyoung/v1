WWW_common = window.WWW_common || {};
WWW_common.over_close = function(){
    if(window.history.length>1)
        window.history.go(-1);
    else
        window.location='/news/activity';
}
$(function(){
    if(WWW_common.isPc){
      $('body').append('<div id="fixed_alert" class="fixed_alert"><div class="firmbox"><p class="fail"><i></i>活动已结束!</p><h4 class="h4-msg2"></h4><h4></h4><div class="bottombox"><a class="confirmBtn" onclick="WWW_common.over_close();">关闭</a></div></div></div>');  
    }else{
        $('body').append('<div id="fixed_alert" class="fixed_alert"><div class="pubcont"><h1 class="h1-fixed"><i></i>活动已结束!<h1/><p class="p-fixed"><p/><h2 class="h2-fixed"><a class="confirmBtn" onclick="WWW_common.over_close();">确定</a></h2></div></div>');
    }
});
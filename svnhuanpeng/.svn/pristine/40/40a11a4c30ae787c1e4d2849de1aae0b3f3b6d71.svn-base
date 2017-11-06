var __sidebar = 'page-sidebar-menu';

!function(){
	$(function(){
		var sidebar = function(){
			var $_GET = (function(){
			var url = window.document.location.href.toString();
			var paramStr = url.split('?');
			var _get = {};
			if(typeof(paramStr[1]) == 'string'){
				var paramArr = paramStr[1].split('&');
				for(var i in paramArr){//console.log(paramArr);
					var param = paramArr[i].split('=');
					_get[param[0]] = param[1];
				}
				return _get;
			}else{
				return _get;
			}
		})();
		
		var sidebarparam = (typeof $_GET['sidebar']=='string')? $_GET['sidebar']:'0-0';
		if(!sidebarparam)
			return false;
		var page = sidebarparam.split('-');
        var item = $('.'+__sidebar).children().eq(page[0]);
        item.addClass('active open').find('ul').css('display','block').find('li').eq(parseInt(page[1])).addClass('active');
        item.find('a > .arrow').addClass('open');
        item.find('ul').css('display', 'block');
        item.find('ul > li').eq(parseInt(page[1])).addClass('active');
		//$('.'+__sidebar+' >li').eq(parseInt(page[0])+1).addClass('open');
		//$('.'+__sidebar+' >li>ul').eq(parseInt(page[0])).css('display','block');
		//$('.'+__sidebar+' >li>ul').eq(parseInt(page[0])).find('li').eq(parseInt(page[1])).addClass('cur');
		
		}
		sidebar();
	})
	
}()
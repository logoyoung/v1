/*获取get参数 －－$_GET['param']*/
var $_GET = $_GET||(function(){
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
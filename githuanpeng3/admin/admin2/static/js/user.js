/**
 *用户操作类
 * _uid  0:游客 ,其他正整数为用户uid
 */
var user = function(){
	 //this._loginStatus = false;
	 this._uid = 0;
	 this._enc = '';
}

user.prototype={
		init:function(){
			var u = this;
			u._uid = u._getCookie('_uid');
			u._enc = u._getCookie('_enc');
			if(!u._uid||!u._enc){
				return u;
			}
			
		},
		login:function(){
			
		},
		reg:function(){
			
		},
		_getCookie:function(){
			var a  = document.cookie.match(new RegExp("(?:^|;)\\s*" + arguments[0] + "=([^;]*)"));
		    return (a) ? decodeURIComponent(a[1]) : null;
		},
		_setCookie:function(){
			if(!arguments[0]||!arguments[1])
				return;
			var cookieStr = arguments[0] + "=" + encodeURIComponent(arguments[1]) + ";";
			if(arguments[2]){alert(1)
				var pat = /([dhms])(\d+)/i;
				var mat = pat.exec(arguments[2]);//console.log(mat)
				var t = parseInt(mat[2]);
				var time = 0;
				switch(mat[1].toLowerCase()){
				case 'd':time=t*24*60*60*1000;break;
				case 'h':time=t*60*60*1000;break;
				case 'm':time=t*60*1000;break;
				case 's':time=t*1000;break;
				default:break;
				}//alert(2)
				var exp = new Date();
				exp.setTime(exp.getTime()+time);
				cookieStr +="expires="+exp.toGMTString()+";";
				//alert(3)
			}	
			//alert(cookieStr)
			document.cookie = cookieStr;	
			//console.log(document.cookie);
		},
		
		_getLoveList:function(){
			var count = parseInt(arguments[0]);
			var count = count?count:'';
			
		},
		_getHistoryList:function(){
			
		}
		
}
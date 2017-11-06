//流请求XMLHttpRequest对象
var myXMLHttpRequest = {};
function homeInitPlayer(obj, luid){
	//停掉上次请求
	/*try{
		if(myXMLHttpRequest.length!=0){
			console.log(myXMLHttpRequest);
    			myXMLHttpRequest.abort();
		}
    }catch(e){
    	console.log(e);
    }*/
	if(typeof myXMLHttpRequest.abort =='function'){
		console.log('killed');
		myXMLHttpRequest.abort();
	}
	var liveRoom = (typeof arguments[2] == 'string')?arguments[2]:'';
	
    var player = getSwfObject(obj);

    var playInterval = setInterval(function(){
        if(getSwfObject(obj)){
            player = getSwfObject(obj);
            console.log('my rtmpplayer is ' + player);
            /*try{
            		if(player.PercentLoaded() == 100)
            			player.videostop();
            }catch(e){
            		console.log(e);
            }*/
            console.log(player);
            requestPlayUrl();
            clearInterval(playInterval);
        }
    }, 500);

    function requestPlayUrl(){
    		myXMLHttpRequest = 
    		$.ajax({
            	url:'http://' + document.domain + '/a/getStreamList.php',
            type:'post',
            dataType:'json',
            data:{
                anchorUserID:luid
            },
            success:function(d){
            		if(d.length==0){
            			console.log('stop request');
            			return true;
            		}
                var streamList = d.streamList;
                var orientation = d.orientation;
                var stream = d.stream;
                console.log(streamList[0] + '   ' + stream);
                //var room = 'http://'+document.domain+'liveRoom.php?luid='+luid;
                var interval = setInterval(function(){//防止 chatProxy 未加载完而出现错误
                    try{
                        if(player.PercentLoaded() == 100){
                            if(luid == 134){
                                player.inputURL('myStream', 'rtmp://223.203.212.30:8080/liverecord','room.php?luid=134');  
                            }else{
                                if(liveRoom)
                                    player.inputURL(stream, 'rtmp://'+streamList[0],liveRoom); 
                                else{
                                    player.inputURL(stream, 'rtmp://'+streamList[0]);//alert(width)
                                }
                            }

                            if(orientation == 0){
                                player.angle(1);
                            }else {
                                player.angle(0);
                            }
                            clearInterval(interval);
                            // console.log(player.PercentLoaded());
                        }
                    }catch(e){
                        console.log(e)
                    }
                },500);
            }
        });
    }

    function getSwfObject(obj){
        return swfobject.getObjectById(obj);
        //if (navigator.appName.indexOf("Microsoft") != -1) {
        //    return window[obj];
        //} else {
        //    return document[obj];
        //}
    }
}

document.write('<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=A739765f9a84bee561d30fa0b537ccb9"></script>');
function ShowMap(objname,options){
	if(options){
		this._city = options.city ? options.city : ""; 
		this._addr = options.addr ? options.addr : "";// ַ
		this._title = options.title ? options.title : "";
		this._lawfirm = options.lawfirm ? options.lawfirm : ""; 
		this._tel = options.tel ? options.tel : ""; 
		this._user = options.user ? options.user : ""; 
		this._mapx = options.mapx ? options.mapx : ""; 
		this._pic = options.pic ? options.pic : ""; 
		this._ismove = options.ismove ? options.ismove : "0";
		this._piobj = options.piobj ? options.piobj : "";
		this._zoom = options.zoom ? options.zoom : "18"; 
	}
	var point=new BMap.Point(116.417273,39.923683);

	var zoom=this._zoom;

	var map = new BMap.Map(objname);
	map.enableScrollWheelZoom();
	map.centerAndZoom(point, zoom);

	var cr = new BMap.CopyrightControl({anchor: BMAP_ANCHOR_TOP_LEFT});
	map.addControl(cr); 
	var bs = map.getBounds();
	cr.addCopyright({id: 1, content: "<a></a>", bounds: bs});
	if (this._mapx != ""){
		var mx=this._mapx.substr(0,this._mapx.indexOf(","));
		var my=this._mapx.substr(this._mapx.indexOf(",")+1);
		point=new BMap.Point(mx,my);
		map.centerAndZoom(point, zoom); 
	}
	else if (this._addr != ""){
		var myGeo = new BMap.Geocoder();    
		myGeo.getPoint(this._addr, function(poi){
			map.centerAndZoom(poi, zoom);
			marker.setPosition(poi);
		}, this._city);
	}
	else if (this._city != ""){
		map.setCenter(this._city);
		if (this._ismove=="0"){setTimeout(function(){map.clearOverlays();}, 1000);}
	}
	else{
		var myCity = new BMap.LocalCity();
		myCity.get(function(result){map.setCenter(result.name);});
		if (this._ismove=="0"){setTimeout(function(){map.clearOverlays();}, 1000);}
	}

	//������ע
	var marker = new BMap.Marker(point);
	map.addOverlay(marker);
	
	if (this._ismove=="1"){
		marker.enableDragging();ק//
		var label = new BMap.Label("��ק�����λ��",{offset:new BMap.Size(20,-15)});
		label.setStyle({ backgroundColor:"red", color:"white", fontSize : "12px" });
		marker.setLabel(label);

		var poj=this._piobj;

		marker.addEventListener("dragend", function(e){
			try{document.getElementById(poj).value = e.point.lng + "," + e.point.lat;}catch (ex) {}
		});
		map.addEventListener("click", function(e){
			marker.setPosition(e.point);
			try{document.getElementById(poj).value = e.point.lng + "," + e.point.lat;}catch (ex) {}
		});
	}

	if (this._ismove=="0"){
		
		var opts = {width:250,height:110,title : "<font color=#FE710F size=3>" + this._title + "</font>"}
		var infotxt="<table border='0'><tr><td valign='top'>"; 
		infotxt += "</td><td><p style='font-size:12px;line-height:24px;'>";
		if (this._addr !=""){infotxt += "<b>地址：</b>" + this._addr + "<br/>";};
		if (this._tel !=""){infotxt += "<b>电话：</b>" + this._tel + "<br/>";};
		if (this._user !=""){infotxt += "<b>���Σ�</b>" + this._user + "<br/>";};
		infotxt += "</p></td></tr></table>";

		var label2 = new BMap.Label(this._title,{offset:new BMap.Size(20,-15)});
		label2.setStyle({ backgroundColor:"red", color:"white", fontSize : "12px" });
		marker.setLabel(label2);

		var infoWindow = new BMap.InfoWindow(infotxt,opts);
		marker.addEventListener("mouseover", function(){
			this.openInfoWindow(infoWindow);
			document.getElementById('picid').onload = function (){infoWindow.redraw();}
		});
	}
}

function getBDAddress(callBackFun,spStr){
	if (!spStr){spStr="";}
	var geolocation = new BMap.Geolocation();
	geolocation.getCurrentPosition(function(r){
		if(this.getStatus() == BMAP_STATUS_SUCCESS){
			var point = new BMap.Point(r.point.lng,r.point.lat);
			var gc = new BMap.Geocoder();    
			gc.getLocation(point, function(rs){
				var addComp = rs.addressComponents;
				var addVal = addComp.province + spStr + addComp.city + spStr + addComp.district + spStr + addComp.street + spStr + addComp.streetNumber;
				callBackFun(addVal);
			});
		}
	},{enableHighAccuracy: true})
}
function layer() {
		
}
layer.msg = function(msg) {
	var layerMsg = $('<div id="layer"></div>');
		layerMsg.css({
			padding: '5px 10px',
			background: 'rgba(0,0,0,0.4)',
			position: 'fixed',
			left: '50%',
			bottom: '50%',
			transform: 'translateX(-50%)',
			color: '#fff',
			fontSize: '14px',
			borderRadius: '2px'
		}).text(msg);
		if($('#layer').length === 0) {
			layerMsg.appendTo('body');
		}else {
			$('#layer').text(msg);
		}
		setTimeout(function(){
			$('#layer').remove();
		},2000);
};
layer.loading = function() {
	var loading = $('<div id="loading"><img src="./image/loading.gif" style="width:162px;height:162px;"></div>') ;
	loading.css({
		position: 'fixed',
		top: '0',
		left: '0',
		width: '100%',
		height: '100%',
		display: 'flex',
		justifyContent: 'center',
		alignItems: 'center',
		zIndex: '100'
	});
	loading.appendTo('body');
};
layer.closeLoading = function() {
	$('#loading').remove();
};

$(document).ajaxError(function() {
    layer.msg('服务器连接错误！');
});
// $(document).ajaxComplete(function() {
// 	layer.closeLoading();
// });
// $(document).ajaxSend(function() {
// 	layer.loading();
// })
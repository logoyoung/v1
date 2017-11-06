function layer() {
		
}
layer.msg = function(msg) {
	var layerMsg = $('<div id="layer"></div>');
		layerMsg.css({
			padding: '5px 10px',
			background: 'rgba(0,0,0,0.3)',
			position: 'fixed',
			left: '50%',
			bottom: '20%',
			transform: 'translateX(-50%)',
			color: '#fff',
			fontSize: '12px',
			borderRadius: '2px'
		}).text(msg);
		layerMsg.appendTo('body');
		setTimeout(function(){
			$('#layer').remove();
		},2000);
};

$(document).ajaxError(function() {
    layer.msg('服务器错误...');
});
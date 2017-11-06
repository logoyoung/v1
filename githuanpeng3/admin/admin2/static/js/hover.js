$(function(){
	(function(){
		var superdom = $('.h_sup');//获取父元素
		superdom.hover(
				function(){//console.log($(this));
					$(this).find('.h_pop').css('display','block');
				},		
				function(){
					$(this).find('.h_pop').css('display','none');
				}
		);
	}());
})

var hoveritem = function(){
	var item = $('.h_item');//获取自身样式
	   item.hover(
			   function(){
				   $(this)[0].className += ' cur';
			   },
			   function(){
				   $(this)[0].className = $(this)[0].className.replace(' cur','');
			   }
	   );
}
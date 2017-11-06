$(document).ready(function(){
        $.extend({
            changeTopNav:function(v){
                $('.user-sidebar').each(function(){
                    if($(this).attr('p')==v){
                        $(this).show();
                    }else{
                        $(this).hide();
                    }
                });
                Op_common.setCookie('nt',v,8640000); 
            },
            changeSideNav:function(v){
                var sidebar=$('.user-sidebar:visible'),
                    current=sidebar.find('[data-id='+v+']'),
                    submenu = current.siblings('ul'),
                    li = current.parents('li'),
                    submenus = sidebar.find('li.submenu ul'),
                    submenus_parents = sidebar.find('li.submenu');
				if(li.hasClass('open'))
				{
					if(($(window).width() > 768) || ($(window).width() < 320)) {
						submenu.slideUp();
					} else {
						submenu.fadeOut(250);
					}
					li.removeClass('open');
				} else 
				{
					if(($(window).width() > 768) || ($(window).width() < 320)) {
						submenus.slideUp();			
						submenu.slideDown();
					} else {
						submenus.fadeOut(250);			
						submenu.fadeIn(250);
					}
					submenus_parents.removeClass('open');		
					li.addClass('open');	
				};
                
                Op_common.setCookie('ns',v,8640000); 
            }
        });
        //{{
        //顶菜单变动
        $('#user-nav').on('click','li a',function(){
            $('#user-nav .active').removeClass('active');
            $(this).addClass('active');
            $.changeTopNav($(this).data('id'));
        });
        //左菜单变动
        $('.user-sidebar').on('click','[data-id]',function(){
            $.changeSideNav($(this).data('id'));
        });
        //子菜单变动
        $('.user-sidebar').on('click','ul ul li a',function(){
            Op_common.setCookie('nd',$(this).attr('href'),8640000); 
        });
        //}}end by zt
	
	// === Sidebar navigation === //
	var ul = $('.user-sidebar:visible > ul');
	// === Fixes the position of buttons group in content header and top user navigation === //
	function fix_position()
	{
		var uwidth = $('#user-nav > ul').width();
		$('#user-nav > ul').css({width:uwidth,'margin-left':'-' + uwidth / 2 + 'px'});
        
        var cwidth = $('#content-header .btn-group').width();
        $('#content-header .btn-group').css({width:cwidth,'margin-left':'-' + uwidth / 2 + 'px'});
	}
	// === Resize window related === //
	var rz=function()
	{        
		if($(window).width() > 768)
		{
			$('#user-nav > ul').css({width:'auto',margin:'0'});
                        $('#content-header .btn-group').css({width:'auto'});
		}
		else if($(window).width() > 320)
		{
			ul.css({'display':'block'});	
			$('#content-header .btn-group').css({width:'auto'});		
		}
		else if($(window).width() < 320)
		{       
			ul.css({'display':'none'});
			fix_position();
		}
	};
	$(window).resize(rz);
	rz();
	
});



/************* matrix.tables.js *************/
$(document).ready(function(){
	
	$('.data-table').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"sDom": '<""l>t<"F"fp>'
	});
	
	$('input.input2').uniform();
	
	$('select.select2').select2();
	
});


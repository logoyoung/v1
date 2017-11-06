WWW_common = window.WWW_common || {};

if(WWW_common.isPc){
    $(function(){
        $.getScript('//res.dangpoo.com/js/jquery.md5.js');
        $.get('/index/loginapt',function(obj){
            //登录框
            var template='<link rel="stylesheet" href="//res.dangpoo.com/css/xdd.login.css" />';
                template +='<div id="fixed_login" class="fixed_login" style="display:none">';
                template +='<div class="login">';
                template +='<div id="closebtn" onclick="hidemodl()"></div>';
                template +='<h1>用户登录</h1>';
                template +='<p class="name">';
                template +='<i class="i-text"></i>';
                template +='<input id="login-u" type="text" placeholder="请输入用户名或手机号码" value="'+obj.u+'" autocomplete="off">';
                template +='<span class="span">*请输入您的账号</span>';
                template +='</p>';
                template +='<p class="name">';
                template +='<i class="i-pwd"></i>';
                template +='<input id="login-p" type="password" placeholder="密码" autocomplete="off">';
                template +='<span class="span">*密码错误</span>';
                template +='</p>';
                template +='<p class="p2-login">';
                template +='<input id="login-t" type="hidden" value="'+obj.t+'">';
                template +='<input id="login-url" type="hidden" value="">';
                template +='<input id="login-sub" type="button" value="立即登录">';
                template +='</p>';
                template +='<h2>您还没有欢朋账号<a href="/index/register">立即注册</a><a href="/index/resetpwd" id="resetpwd">忘记密码？</a></h2>';
                template +='</div>';
                template +='</div>';
                $('body').append(template); 
        },'json');
        
        $('body').on('keydown','#login-p',function(e){
            if(e.keyCode == 13) {
                if($.trim($(this).val())=='')return;
                $('#login-p').click();
            }
        }).on('click','#login-sub',function(){
        var  u=$('#login-u'),
             p=$('#login-p');
            if(u.val()==''){
                u.closest('p').addClass('error');
                return;
            }
            if(p.val()==''){
                p.closest('p').addClass('error');
                return;
            }
            var data={u:u.val(),p:$.md5(p.val()),t:$('#login-t').val()};
            $(this).attr('disabled',1).val('正在登录...');
            $.post('/index/loginapi',data,function(obj,opt){
                $('#login-sub').removeAttr('disabled').val('立即登录');
                if(obj.status=='1'){
                    window.location.reload();
                }else{
                    WWW_common.alert(obj.info,null,{icon:"fail"});   
                }
            },'json');
        }).on('click','#fixed_login input',function(){
            $(this).closest('p').removeClass('error');
        });
        WWW_common.login = function(){
            $('#fixed_login').show();
        }
    });
}else{
    WWW_common.login=function(){
        window.location = '/index/login?back=1';
    };
}
function hidemodl(){
	$("#fixed_login").hide()
}

$(document).ready(function(){
    $('#to-recover').click(function(){
            $("#loginform").slideUp();
            $("#recoverform").fadeIn();
    });
    $('#to-login').click(function(){
            $("#recoverform").hide();
            $("#loginform").fadeIn();
    });

    $('#loginform').on('keydown','.control-group input',function(e){
        if(e.which == 13) {
            if($.trim($(this).val())=='')return;
            var next=$(this).closest('.control-group').next('.control-group');
            if(next.length>0){
                next.find('input').focus();
            }else{
                $('#login').click();
            }
        }
    });

    $('#login').click(function(){
        Op_common.showLoad();
        $.ajax({
           type: "POST",
           dataType:"json",
           data: $(this).closest('form').serialize(),
           success: function(obj){
               $('.ui-dialog-close').show();
               if(obj.status==0){
                   dialog.getCurrent().title('登录成功');
                   dialog.getCurrent().content('欢迎登录~');
                   setTimeout(function(){
                       window.location.reload();
                   },888);
               }else{
                   dialog.getCurrent().title('登录失败');
                   dialog.getCurrent().content(obj.msg);
                   setTimeout(function(){
                       dialog.getCurrent().close();
                   },2000);
               }
           },
        });
    });
    Op_common.ajaxForm($('#recoverform'),function(obj){
        Op_common.alert(obj.msg);
    });

    $('.verify img').click(function(){
        var p=/\?(.+)/.exec(this.src);
        if(p){
            console.log(p)
            this.src = this.src.replace(p[1],Math.random());
        }else{
            this.src += '?'+Math.random();
        }
    });
});
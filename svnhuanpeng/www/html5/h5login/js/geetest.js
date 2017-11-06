function geetest2(conf, callBack,hideCallBack){
        function handler(captchaObj){
            captchaObj.appendTo(conf.append);
            if(conf.product == 'popup'){
                captchaObj.onReady(function(){
                    captchaObj.show();
                });
                captchaObj.hide(function(){
                    hideCallBack && typeof hideCallBack == 'function' && (hideCallBack());
                });
            }
            captchaObj.onSuccess(function(){
                captchaObj.hide();
                callBack && typeof callBack =='function' && (callBack(captchaObj.getValidate()));
            });
        }
        $.ajax({
            url: $conf.api + "code/geetest_api.php?rand="+Math.round(Math.random()*100),
            type:'get',
            dataType:'json',
			beforeSend: function() {
                $('#mobileCode').prop('disabled',true);
                $('[id^=geetest_]').remove();
			},
            success:function(data){
                // 使用initGeetest接口
                // 参数1：配置参数，与创建Geetest实例时接受的参数一致
                // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
                initGeetest({
                    gt: data.gt,
                    challenge: data.challenge,
                    product: conf.product, // 产品形式
                    offline: !data.success
                }, handler);
                $('#mobileCode').prop('disabled',false);
            }
        });
};
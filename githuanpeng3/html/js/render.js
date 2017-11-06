/**
 * Created by junxiao on 2017/4/14.
 */
$(function () {
    $(".xl,.r_btnbox,.user_pic").hover(function(){
        $(this).children(".drop_menu").show();
        $(this).children(".drop_menu").css("display","block");
    },function(){
        $(this).children(".drop_menu").hide();
        $(this).children(".drop_menu").css("display","none");
    });
    //头部右侧鼠标经过变色
    $(".icon-color").hover(function(){
        $(this).next().addClass("fc_orange");
    },function(){
        $(this).next().removeClass("fc_orange");
    });

    $(".weixin_share").hover(function(){
        $(this).children(".weixin_qrcode").show();
        $(this).children(".weixin_qrcode").css("display","block");
    },function(){
        $(this).children(".weixin_qrcode").hide();
        $(this).children(".weixin_qrcode").css("display","none");
    });

    $(".weibo_share").hover(function(){
        $(this).children(".weibo_qrcode").show();
        $(this).children(".weibo_qrcode").css("display","block");
    },function(){
        $(this).children(".weibo_qrcode").hide();
        $(this).children(".weibo_qrcode").css("display","none");
    });
});
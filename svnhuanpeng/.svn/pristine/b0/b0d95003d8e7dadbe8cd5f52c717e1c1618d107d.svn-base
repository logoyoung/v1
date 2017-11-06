/**
 * Created by junxiao on 2017/3/28.
 */
$(function () {
    //lazyload
    lazyLoad.init();
    //hover
    $('.h_item').hover(function(){
        $('.h_item').removeClass('cur');
        $(this).addClass('cur');
    });
    //win WIDTH
    window.onresize = function () {
        if($(window).width() <= 1200){
            $('#gameType').removeClass('w1180').addClass('w980');
        }else{
            $('#gameType').removeClass('w980').addClass('w1180');
        }
    }

});
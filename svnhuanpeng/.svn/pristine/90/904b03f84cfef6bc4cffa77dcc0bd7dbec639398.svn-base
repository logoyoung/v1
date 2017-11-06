/**
 * Created by junxiao on 2017/3/28.
 */
$(function () {
    //lazyload
    lazyLoad.init();
    //hover
//  $('.h_item').mouseenter(function(){
//      $('.h_item').removeClass('cur');
//      $(this).addClass('cur');
//  });


    //win WIDTH
//  window.onresize = function () {
//      if($(window).width() <= 1200){
//          $('#gameType').removeClass('w1180').addClass('w980');
//      }else{
//          $('#gameType').removeClass('w980').addClass('w1180');
//      }
//  };


	//当窗口大小发生改变
    $(window).resize(function(){
        _Width_resize = $(window).width();
        if (_Width_resize >= 1180){
            w1180();
        }else{
            w980();
        }
    });

	function w1180(){
        $("#gameType").removeClass("w980").addClass("w1180");
    }
    function w980(){
        $("#gameType").removeClass("w1180").addClass("w980");
    }
    setTimeout(function () {
        if ($(window).width() >= 1180){
            w1180();
        }else{
            w980();
        }
    },1000/60);

    window.onscroll = function () {
      if($(window).scrollTop() >= 50){
          $('.to_top').show();
      }else{
          $('.to_top').hide();
      }
    };

    $(".to_top").click(function () {
        var speed=200;
        $('body,html').animate({ scrollTop: 0 }, speed,function(){
            return;
        });

    });
});
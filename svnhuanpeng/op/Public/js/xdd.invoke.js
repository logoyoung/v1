WWW_common = window.WWW_common || {};
WWW_common.isInstalled = false;

//if(navigator.userAgent.match(/android/i)) {
//     var isInstalled;
//     var ifrSrc = 'cartooncomicsshowtwo://platformapi/startApp? type=0&id=${com.id}&phone_num=${com.phone_num}';
//     var ifr = document.createElement('iframe');
//     ifr.src = ifrSrc;
//     ifr.style.display = 'none';
//     ifr.onload = function() {
//
//     // alert('Is installed.');
//
//     isInstalled = true;
//
//     alert(isInstalled);
//
//     document.getElementById('openApp0').click();};
//
//     ifr.onerror = function() {
//
//         // alert('May be not installed.');
//
//         isInstalled = false;
//
//         alert(isInstalled);
//
//     }
//
//     document.body.appendChild(ifr);
//
//     setTimeout(function() {
//
//         document.body.removeChild(ifr);
//
//     },1000);
//
//}
$(function(){
    if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i))  {
        var ifrSrc = 'weixin://';var ifr = document.createElement('iframe');
        ifr.src = ifrSrc;
        ifr.style.display = 'none';
        ifr.onload = function() {
            WWW_common.isInstalled = true;
            alert('app install')
        };
        ifr.onerror = function() {
            WWW_common.isInstalled = false;
            alert('app not install')
        };
        document.body.appendChild(ifr);
        setTimeout(function() {
             document.body.removeChild(ifr);
        },1000);
        setTimeout(function() {
                 alert(WWW_common.isInstalled)
        },2000);
    }


});
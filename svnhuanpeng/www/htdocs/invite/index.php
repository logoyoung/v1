<?php
    include '../init.php';

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta content="email=no" name="format-detection" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" >
    <title>邀请活动</title>
    <link rel="stylesheet" href="<?php echo STATIC_CSS_PATH; ?>common.css?v=1.0.4">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/index.css?v=1.0.4" id="styleCss">
    <!--[if lte IE 8]><link rel="stylesheet" href="css/IE8.css?v=1.0.4"><![endif]-->
    <script>
        (function () {

            if (navigator.userAgent.toLowerCase().match(/(iphone|ipod|android|ios|ipad|ucweb)/i))  {

                var styleCss = document.querySelector('#styleCss');

                styleCss.href = 'css/mobile.css?v=1.0.4';
                /*rem*/
                (function(){

                    size();
                    window.onresize = function (){

                        size();

                    };

                    function size(){

                        var winW = document.documentElement.clientWidth || document.documentElement.body.clientWidth;

                        document.documentElement.style.fontSize = winW / 20 +'px';

                    }

                })();

            }
        })();
    </script>
<!--    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>-->
    <script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>common.js?v=1.0.4"></script>
    <script src="js/request.js?v=1.0.4" type="text/javascript"></script>
</head>
<body>

<div class="request-container">

    <div class="request-header">
        <div class="header-content"></div>
    </div>
    <div class="request-awaBox">
        <div class="request-award"></div>
    </div>
    <div class="request-shareBox">
        <div class="request-share"></div>
    </div>
    <div class="request-getAward">
        <div class="request-getContent">
            <button class="getAward" id="getBtn"></button>
            <p class="pcP">您有<span id="total">0</span>次邀请奖励可领取, <i id="openList">查看奖励</i> &gt;</p>

            <button class="mobileBtn-center"></button>
            <p class="mobileP">您有<span id="mobileTotal">0</span>次邀请奖励可领取,<a class="mobile-right">查看领取</a>&gt;</p>

        </div>
    </div>
    <div class="request-listBox">

        <div id="listAward" class="request-listAward">

            <div class="listLeft">

                <table id="leftTable">

                </table>

            </div>
            <div class="listRight">
                <div class="right-content">

                    <ul id="listSc">

                        <li></li>

                    </ul>


                </div>

            </div>

        </div>

    </div>
    <div class="request-footer">
        <div class="request-footerTitle"></div>
        <div class="request-footerContent"></div>
    </div>
    <div class="request-IE8">
        您的浏览器版本过低,请<a href="http://chrome.360.cn">升级浏览器</a>
    </div>
    <div class="request-listModal" id="listModal">
        <button id="closeModal"></button>

        <table id="modalCotent">
            <tr>
                <th>昵称</th>
                <th>首充登录App时间</th>
                <th>获得奖励</th>
                <th>状态</th>
            </tr>
        </table>

    </div>
    <div class="modalBg"></div>

</div>
<?php include WEBSITE_TPL.'loginModal.php' ;?>
</body>
</html>
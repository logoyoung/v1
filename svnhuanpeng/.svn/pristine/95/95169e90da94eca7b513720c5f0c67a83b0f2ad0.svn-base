<?php
include '../../../../include/init.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>申请提现</title>
    <meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta content="email=no" name="format-detection" />
    <link rel="stylesheet" href="css/reset.css?t=<?php echo time();?>">
    <link rel="stylesheet" href="css/myWithdraw.css?t=<?php echo time();?>">
    <script src="<?php echo STATIC_JS_PATH; ?>jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="<?php echo STATIC_JS_PATH; ?>common.js?v=1.0.4" type="text/javascript"></script>
    <script type="text/javascript">
    /*rem*/
    (function(){
        size();
        window.onresize = function (){
            size();
        };
        function size(){
            var winW = document.documentElement.clientWidth || document.documentElement.body.clientWidth;
            document.documentElement.style.fontSize = winW / 22.5 +'px';
        }

    })();
    </script>
    <script type="text/javascript" src="js/myWithdraw.js?t=<?php echo time();?>"></script>
</head>
<body>

    <div class="withdraw-box"></div>

    <div class="modal-box">
        <div class="modal-success" style="display: none;">
            <img src="img/icon_right.png">
            <p></p>
        </div>
        <div class="modal-loading" style="display: none;">
            <div class="icon_loading"></div>
        </div>
        <div class="error-modal" style="display: none;">
            <img src="img/icon_fail.png">
            <p id="error-content"></p>
        </div>
    </div>
</body>
</html>
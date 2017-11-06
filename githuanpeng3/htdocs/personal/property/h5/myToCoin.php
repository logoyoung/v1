<?php
include '../../../../include/init.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta name="format-detection" content="telephone=no"/>
    <meta content="email=no" name="format-detection" />
    <title>兑换金币</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/myExchange.css?t=<?php echo time(); ?>">
    <script src="lib/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="<?php echo STATIC_JS_PATH; ?>common.js?v=1.0.4" type="text/javascript"></script>
    <script src="js/myToCoin.js?t=<?php echo time(); ?>" type="text/javascript"></script>
</head>
<body>

<div class="myExchange-container">

    <!--余额区-->
    <div class="myExchange-surplus">
        <p class="surplus-gold">
            金币余额: <span class="goldMoney">0</span>
        </p>
        <p class="surplus-hp">

            金豆余额: <span class="hpMoney">0</span>

        </p>
    </div>

    <!--数额区-->
    <div class="myExchange-select">

        <p class="select-title">请选择兑换数额
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="alertError"></span>
        </p>

        <ul class="select-top">

            <li class="selectThis">
                <p class="hp">15金豆</p>
                <p class="gold">15金币</p>
            </li>
            <li>
                <p class="hp">20金豆</p>
                <p class="gold">20金币</p>
            </li>
            <li>
                <p class="hp">30金豆</p>
                <p class="gold">30金币</p>
            </li>

        </ul>

        <ul class="select-bottom">

            <li>
                <p class="hp">50金豆</p>
                <p class="gold">50金币</p>
            </li>
            <li>
                <p class="hp">100金豆</p>
                <p class="gold">100金币</p>
            </li>
            <li>
                <p class="hp">200金豆</p>
                <p class="gold">200金币</p>
            </li>

        </ul>

        <div class="select-ratio">
            <p>
                兑换比例:
                <span> 1 金豆 = 1 金币</span>
            </p>
        </div>

    </div>

    <!--兑换区-->
    <div class="myExchange-change">

        <button class="exchangeBtn">立即兑换</button>

    </div>

    <!--协议区-->
    <div class="myExchange-protocol clearfix">

        <p>查看</p>

        <a href="http://www.huanpeng.com/protocol/mobileReciveProtocol.html?v=1.0.4" class="protocol-result">《欢朋直播收益兑换协议》</a>

    </div>
</div>

<div class="myExchange-modalBox">

    <div class="modal-loading">

        <div class="icon_loading"></div>
    </div>
    <div class="modal-success">
        <img src="img/icon_right.png">
        <p>兑换成功!</p>
    </div>
    <div class="modal-fail">
        <img src="img/icon_fail.png">
        <p>兑换失败,请稍后再试</p>
    </div>

</div>

</body>
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
</html>
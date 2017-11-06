<?php
include_once '../../../include/init.php';
include_once WEBSITE_PERSON."isAnchor.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>主播资料-欢朋直播-精彩手游直播平台！</title>
    <?php include  WEBSITE_TPL.'commSource.php'; ?>
    <link rel="stylesheet" href="<?php echo STATIC_CSS_PATH; ?>person.css?v=1.0.5">
    <link rel="stylesheet" href="<?php echo STATIC_CSS_PATH; ?>level.css?v=1.0.4">
    <link rel="stylesheet" href="css/reset.css?v=1.0.4">
    <link rel="stylesheet" href="css/zhubo.css?v=1.0.4">
    <script type="text/javascript" src="lib/highcharts.js?v=1.0.4"></script>
    <script type="text/javascript" src="lib/person.js?v=1.0.4"></script>
</head>
<body style="background: #eeeeee">

<?php include WEBSITE_MAIN.'head.php'; ?>
<script>
    new head(null);
</script>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
    <div class="person-container content">

        <div class="person-title">
            <h2 style="font-weight: normal;">主播资料</h2>
        </div>

        <div class="person-content">
            <div class="person-img">
                <img src="">
            </div>
            <div class="person-result">

                <div class="resultFirst">
                    <p class="result-title"></p>
                </div>
                <div class="resultSecond">
                    <span src="" class="result-level anchorLvl-icon"></span>
                    <div class="level-fac" style="position: relative;">
                        <p class="allPro"></p>
                        <div style="position:absolute; left: 48px;top: 0px; color: #fff;font-size: 12px;line-height: 14px;" class="level-desc">还差 <span></span> 经验值升级</div>
                    </div>
                </div>
                <div class="resultThird">
                    <p>房间号 : <span class="roomId" style="color: #ff7800;"></span></p>
                </div>
                <div class="resultFourth">
                    <p class="proIt">粉丝 : <span></span>人</p>
                    <p class="proTime">累计直播时长 : <span></span></p>
                    <p class="proMoney" style="margin-left: 95px;display: none;">签约月薪 : <span style="color: #ff7800;" id="proBase"></span></p>
                </div>

            </div>
        </div>

        <div class="proMost">
            <h2 style="font-weight: normal;">主播人气峰值</h2>
            <div class="monthLink">
                <a class="curr" id="nowMonth">本月</a>
                <a id="lastMonth">10月</a>
                <a id="thirdBeforeMonth">9月</a>
            </div>
            <!--chartShow-->
            <div class="chartShow">
                <div class="chartShow-content" style="min-width: 800px;height: 360px;"></div>
            </div>


            <p>
                本月人气峰值：&nbsp;<span id="top">0</span>&nbsp;人
                &nbsp;
                &nbsp;
                本月直播时长：共&nbsp;<span id="monthLiveTime" style="color: #ff7800;font-size: 18px;">0</span>
            </p>

        </div>

        <div class="commit">
            <p>提示 : 人气峰值为当天人气值的最大值。人气值数据,是结合 "打击外挂"、"协议去重" 等策略得出的最终数值,与以往的PCU数据不同。</p>
        </div>

        <div class="person-footer">
            <div class="footerLeft">
                <a href="<?php echo WEB_ROOT_URL.'help/helpAssistant.php'?>" target="_blank">
                    <img src="img/live.png">
                    <p>直播教程</p>
                </a>
            </div>
            <div class="footerCenter">
                <a href="<?php echo WEB_ROOT_URL.'download.php'?>" target="_blank">
                    <img src="img/setup.png">
                    <p>客户端下载</p>
                </a>
            </div>
            <div class="footerRight">
                <a href="<?php echo WEB_ROOT_URL.'help/helpReg.php'?>" target="_blank">
                    <img src="img/help.png">
                    <p>帮助中心</p>
                </a>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
</body>
<script type="application/javascript">
    $(document).ready(function () {
        personalCenter_sidebar('homepage');
    });
</script>
</html>

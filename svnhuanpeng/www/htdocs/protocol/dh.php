<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/3/1
 * Time: 17:07
 */

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=no"/>
    <meta content="email=no" name="format-detection" />
    <!--<link rel="stylesheet" href="css/reset.css">-->
    <title>欢朋直播收益兑换规则</title>
    <?php include '../tpl/commSource.php';?>
    <script type="application/javascript" src="../static/js/common.js"></script>
    <script type="application/javascript">
        window.pageTitle = document.title;
    </script>
    <style>

        html,body{
            background: #eee;
        }

        .rcp-container{
            width: 980px;
            margin: 100px auto 50px auto;
            color: #666;
        }
        .rcp-container .rcp-body{
            background: #fff;
            padding: 50px;
        }
        .rcp-body .rcp-title{
            border-bottom: 1px solid #f4f4f4;
            font-family: "微软雅黑";
            padding-bottom: 22px;
        }
        .rcp-body .rcp-title h3{
            text-align: center;
            font-size: 22px;
            color: #444;
            line-height: 30px;
            font-weight: normal;
        }
        .rcp-body .rcp-content{
            margin-top: 28px;
            font-family: "微软雅黑";
        }
        .rcp-body .rcp-content p{
            line-height: 1.5em;
            margin: 13px;
            font-size: 13px;
            padding: 0;
            text-indent: 2em;
            font-family: "微软雅黑";

        }
        .rcp-body .rcp-content h4{
            text-indent: 1em;
            font-weight: bold;
            font-size: 16px;
            color: #ff7800;
        }
        .rcp-body .rcp-content h5{
            text-indent: 2em;
            font-size: 14px;
            font-weight: bold;
        }
        .rcp-body .rcp-content .indent-54{
            text-indent: 5.4em
        }
        .rcp-body .rcp-content .indent-144{
            text-indent: 7.4em
        }

    </style>
</head>
<body>
<?php $path = realpath(__DIR__); include $path . '/../head.php'; ?>
<div class="rcp-container">
    <div class="rcp-body">
        <div class="rcp-title">
            <h3>欢朋直播收益兑换规则</h3>
        </div>
        <div class="rcp-content">

            <p style="text-indent: 0;">亲爱的欢朋主播们：</p>
            <p style="text-indent: 0;">感谢大家选择欢朋直播平台，也感谢各位主播的信任与支持。请大家在兑换时认真阅读兑换规则，具体兑换规则如下：</p>

            <h4>1.主播收益：</h4>
            <p>主播收益有“金币”、“金豆”两种，具体如下：</p>

            <h5>1.1 关于金币</h5>
            <p>金币由主播收到的礼物转化而来。用户通过消费欢朋币赠送礼物给您，该部分礼物会按照一定比例兑换成金币作为您的收益。</p>

            <h5>1.2 关于金豆</h5>
            <p>金豆由用户送出的欢朋豆转化而来。用户通过赠送欢朋豆给您，该部欢朋豆会按照一定比例兑换成金豆作为您的收益。</p>

            <h5>1.3 兑换比例</h5>
            <p>您的收益金币、金币也可以兑换为 RMB ，金币也可以兑换为欢朋币。</p>
            <p>1 金币相当于 1 元 RMB，与人民币的兑换比例是 1：1；</p>
            <p>1 金豆相当于 1 元 RMB，与人民币的兑换比例是 1：1。</p>
            <p>1 金币相当于 10 个欢朋币，与欢朋币的兑换比例是 1：10；</p>


            <h4>2.兑换条件</h4>
            <p></p>
            <h5>2.1 兑换资格</h5>
            <p>主播需在本平台独家直播，不得参与其他平台任何直播等相关活动，不满足上述条件者取消其兑换基本资格。</p>

            <h5>2.2 兑换标准</h5>
            <p>主播收益每个月仅可兑换一次 RMB，在每次结算周期内进行兑换提现，金币超过 100 个才可兑换。当月未兑换金币和金豆收益将自动累积至下月。
                金币兑换为欢朋币不受次数和时间限制，随时可以兑换。</p>

            <h5>2.3 违规处理</h5>
            <p>如果您在直播时由于违规操作导致直播间被封禁，我们将冻结该月的直播收益；如多次出现直播事故或违规情况，情节严重的主播将永久封号，并取消其金币、金豆的兑换资格。</p>

            <h5>3.收益兑换期</h5>
            <p>每月 1～5 日为申请收益兑换期，10～20 日为兑换收益到账期。</p>
            <p>如您在申请收益兑换期间提出申请，提交申请且审核通过后将于到账期收到相应款项。
                提现超过一定额度会缴纳一定的税费，每次提现额度不同缴纳的税费也会不同，缴纳税率可能会受政策影响随时进行调整。</p>

        </div>
    </div>
</div>
<?php include $path . '/../footerSub.php'; ?>
<script>var head = new head(null,false);</script>
</body>
</html>

<?php
include_once '/usr/local/huanpeng/include/init.php';
?>
<!DOCTYPE html>
    <html>
<head>
    <meta charset="UTF-8">
    <title>网站维护中...</title>
    <style>
        *{
            margin: 0;
            padding: 0;
        }
        html,body{
            width: 100%;
            height: 100%;
            background: #f0f0f0;
        }
        img{
            width: 100%;
            height: auto;
            overflow: hidden;
        }
        .maintain{
            /*width: 1200px;
            height: 475px;*/
            margin: auto;
        }
        .maintain .description{
            position: absolute;
            width: 100%;
            text-align: center;
            font-size: 42px;
            color: #999;
            top: 140px;
        }
        .maintain .description .timer{
            font-size: 28px;
            margin-top: 40px;
        }
        </style>
</head>
<body>
<div class="maintain">
    <div class="description">
        <div>网站正在维护中～敬请期待</div>
        <div class="timer">预计网站2小时候恢复正常</span></div>
    </div>
    <img src="<?php echo WEB_ROOT_URL; ?>static/img/webmaintain.png">
    </div>
<script>

</script>
</body>
</html>
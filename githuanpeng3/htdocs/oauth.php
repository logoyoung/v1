<?php include '../include/init.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>三方绑定-欢朋直播-精彩手游直播</title>
    <?php include 'tpl/commSource.php';?>
</head>
<body>
<?php include 'head.php'?>
<style>
    img{
        width: inherit;
        height: inherit;
    }
</style>
<div id="news-content" style="min-height:200px; width: 700px;padding:0px 100px;margin: 100px auto 50px auto;">
    <p style="text-align: center;">
        <span style="font-size: 24px;">
            <strong><?php  echo $_GET['err'];   ?></strong>
        </span>
    </p>
</div>

<?php include 'footerSub.php';?>
<script>
    var head = new head(null,false);
</script>
</body>
</html>
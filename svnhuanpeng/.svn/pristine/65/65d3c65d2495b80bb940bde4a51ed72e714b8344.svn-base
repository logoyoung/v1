<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 16/12/1
 * Time: 17:18
 */
include '../include/init.php';
$db = new DBHelperi_huanpeng();
$nid = isset($_GET['id'])?(int)$_GET['id']:'';
if(!$nid) exit;
$path = realpath(__DIR__);
$news = getInformationById($nid,$db);
if(!is_array($news)) exit;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo "{$news['title']}-新闻-欢朋直播"; ?></title>
        <?php include $path . '/tpl/commSource.php';?>
        <script type="text/javascript" src="./static/js/page.js?v=1.0.4"></script>
    </head>
<body>
<?php include $path . '/head.php'?>
<style>
    img{
        width: inherit;
        height: inherit;
    }
    </style>
<div id="news-content" style="min-height:700px; width: 700px;padding:0px 100px;margin: 100px auto 50px auto;background: #fff;"></div>
<?php include $path . '/footerSub.php';?>
<script>
    var content = <?php echo isset($news['content']) ? json_encode($news['content']) : ''; ?>;
    var head = new head(null,false);
    var page = new page();
    page.init();
    var decodeHtml = function (str) {
        var s = "";
        if (str.length == 0) return "";
        //s = str.replace(/&gt;/g, "&");
        s = str.replace(/&amp;/g,"&");
        s = s.replace(/&lt;/g, "<");
        s = s.replace(/&gt;/g, ">");
        //s = s.replace(/&nbsp;/g, " ");
        s = s.replace(/&#39;/g, "\'");
        s = s.replace(/&quot;/g, "\"");
        s = s.replace(/<br>/g, "\n");
        return s;
    }
    console.log(decodeHtml(content));
    document.getElementById('news-content').innerHTML = decodeHtml(content);
</script>
</body>
</html>

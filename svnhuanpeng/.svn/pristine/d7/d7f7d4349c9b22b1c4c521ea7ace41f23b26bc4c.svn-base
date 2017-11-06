<?php
/**
 *
 *尾页备案信息
 *   */


//备注页面信息
$act = array(
    'na'=>array(
        'title' => '信息网络传播视听节目许可证0108268号－欢朋直播',
        'img'   => array(
            '../static/img/src/xkz/na_1.jpg',
            '../static/img/src/xkz/na_2.jpg',
            '../static/img/src/xkz/na_3.jpg',
            '../static/img/src/xkz/na_4.jpg'
        ),
        'head'  => '信息网传播视听节目许可证0108268号'
    ),
    'pm'=>array(
        'title' => '节目制作许可证京字第666号－欢朋直播',
        'img'   => array(
            '../static/img/src/xkz/pm_1.jpg'
        ),
        'head'  => '节目制作许可证京字第666号'
    ),
    'bj'=>array(
        'title' => '京网文【2016】6172-843号－欢朋直播',
        'img'   => array(
            '../static/img/src/xkz/bj_1.jpg'
        ),
        'head'  => '京网文【2016】6172-843号'
    ),
    'sp'=>array(
        'title' => '营业性演出许可证京市演1169号－欢朋直播',
        'img'   => array(
            '../static/img/src/xkz/sp_1.jpg'
        ),
        'head'  => '营业性演出许可证京市演1169号'
    ),
    'dx'=>array(
        'title' => '京ICP证060797号－欢朋直播',
        'img'   => array(
            '../static/img/src/xkz/dx_1.jpg',
            '../static/img/src/xkz/dx_2.jpg',
            '../static/img/src/xkz/dx_3.jpg'
        ),
        'head'  => '京ICP证060797号'
    ),
);

$type = isset($_GET['act'])?$_GET['act']:'';
if( !$type )
{
    exit;
}
$page = $act[$type];

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php  echo $page['title'];  ?></title>
    <meta charset="UTF-8">
    <?php include '../tpl/commSource.php';?>
</head>
<body>
<?php include '../head.php'; ?>
<style>
    body{
        background:#eee;
    }
    .protocol-contain{
        width:980px;
        margin:100px auto 50px auto;
        color:#666;
    }
    .protocol-body{
        background:#fff;
        padding:50px;
        min-height: 500px;
    }
    .protocol-body .protocol-title{
        border-bottom: 1px solid #f4f4f4;
        font-family: "微软雅黑";
        padding-bottom: 22px;
    }
    .protocol-body .protocol-title h3{
        text-align: center;
        font-size: 22px;
        color: #444;
        line-height: 30px;
        font-weight: normal;
    }
    .protocol-body .protocol-content{
        margin-top: 28px;
        font-family: "微软雅黑";
    }
    .protocol-body .protocol-content p{
        line-height: 1.5em;
        font-size: 13px;
        padding: 0px 0px;
        text-indent:2em;
    }
    .protocol-body h2{
        font-size: 20px;
        height: 60px;
        line-height: 60px;
        text-align: center;
        color: #333;
    }
    .protocol-body .protocol-body-content
    .protocol-body .protocol-body-content>img{
        max-height: 1000px;
        max-width: 900px;
        margin: auto;
    }
</style>
<div class="protocol-contain">
    <!--京ICP证060797-->
    <!--<div class="protocol-body ">
        <img src="https://vr0.6rooms.com/imges/live/help/dxfw_v2.jpg">
    </div>-->
    <?php
        echo "<div class=\"protocol-body \">
              <div class=\"protocol-body-content\">
              <h2>{$page['head']}</h2>";

        foreach ($page['img'] as $k => $v)
        {
            echo "<img src=\"{$v}\">";
        }
       /*echo "<img src=\"{$page['img']}\">"*/
       echo "</div>
              </div>";
    ?>
</div>
<?php include '../footerSub.php'; ?>
<script>var head = new head(null,false);</script>
</body>
</html>
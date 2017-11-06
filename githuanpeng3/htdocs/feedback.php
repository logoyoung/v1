<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/1/6
 * Time: 14:24
 */
/**
 * 问题反馈页面
 *   */
session_start();
include ('../include/init.php');
include (INCLUDE_DIR.'upload.class.php');


$db = new DBHelperi_huanpeng();
// $db->realEscapeString($string)
// $db->affectedRows
$uid = $_COOKIE['_uid'];
$enc = $_COOKIE['_enc'];
$login = 0; // 未登录
$upStatus = 0; // 未提交

if ($uid && $enc) {
    if (CheckUserIsLogIn($uid, $enc, $db) === true)
        $login = 1; // 登录
    else
        $login = 2; // 异常
}//var_dump( isset($_POST['sbt']) );var_dump( $contact );

//echo "<script> var login='{$login}';var upStatus='{$upStatus}'  </script>";
$path = realpath(__DIR__);
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset='utf-8'>
    <?php include $path.'/tpl/commSource.php';?>
    <link rel="stylesheet" type="text/css" href="./static/css/home_v3.css?v=1.0.4">


    <style>
        body {
            background-color: #f2f2f2;
            color: #333;
        }
    </style>
</head>
<body>

<?php
include ($path.'/head.php');
$ref = urlencode('../feedback.php');
echo '<script>var head = new head(null,false);</script>';
if($upStatus==1){
    echo '<div class="error_page"><div class="pic"><img src="./static/img/src/bg_nodata.png" alt=""></div>'
        .'<span>举报成功</span><a href="index.php" class="sub">返回首页</a></div>';
    include ('./footerSub.php');
    exit;
}
if(!$login){
    echo '<div class="error_page"><div class="pic"><img src="./static/img/src/bg_nodata.png" alt=""></div>'
        .'<span>请先登录</span><a href="./personal/login.php?ref_url='.$ref.'" class="sub">前往登录</a></div>';
    include ($path.'/footer.php');
    exit;
}

?>
<style>
    #feedback{
        width: 1000px;
        margin: 60px auto;
        padding-top: 30px;
    }
    .fb-head{
        height: 50px;
        margin-bottom: 20px;
        border-bottom: 2px #ddd solid;
    }
    .fb-head h1{
        width: 167px;
        height: 49px;
        font-size: 26px;
        color: #555;
        border-bottom: 2px #ff7800 solid;
        text-align: center;
    }
    .fb-body{
        background: none;
        width: 100%;
    }
    .fb-lable{
        width: 90px;
        display: inline-block;
        font-size: 14px;
        padding: 5px 0px;
        float: left;
    }
    #feedback .textarea, input[type="text"] {
        border: 1px #ccc solid;
        height: 25px;
        line-height: 25px;
        padding: 5px;
        width: 988px;
        margin: 5px 0px;
        outline: 0px;
    }
    #feedback .textarea:hover, input[type="text"]:hover {
        border: 1px solid #ff7800;
    }
    #feedback .textarea{
        width: 988px;
        height: 180px;
        resize: none;
        float: left;
    }
    #feedback .content,#feedback .content{
        margin: 20px 0px;
    }
    #feedback .connect,#feedback .content,#feedback .content .text{
        float: left;
    }
    #feedback .btn-sbt{
        width: 160px;
        height: 44px;
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        -ms-border-radius: 6px;
        -o-border-radius: 6px;
        border-radius: 6px;
        background: #ff7800;
        text-align: center;
        color: #fff;
        line-height: 32px;
        cursor: pointer;
        height: 44px;
        border-radius: 6px;
        margin-right: 209px;
        outline: none;
        /* list-style-type: none; */
        border-style: none;
    }
    </style>
<div class="contain">
    <div id="feedback">
<div class="fb-head">
    <h1>意见反馈</h1>
</div>
        <div class="fb-body">
            <form method="post" action="" onsubmit="return false;">
                <div class="connect">
                    <label class="fb-lable">联系方式：</label>
                    <input id="contact" type="text" class="" value="请输入您的手机号或者邮箱"
                           onfocus="if(this.value==this.defaultValue){this.value='';this.style.color='#444';}"
                           onblur="if(this.value=='') {this.value=this.defaultValue;this.style.color='#999'; }">
                </div>
                <div class="content">
                    <label class="fb-lable">意见：</label>
                    <div class="text"><textarea class="textarea" id="cont" maxlength="200"></textarea></div>
                </div>
                <input class="btn-sbt" type="submit" value="提交">
            </form>
        </div>
</div>
</div>
<?php include $path.'/footerSub.php';?>
<script>
    function checkMobile(mobile){
        var mobile = ( (typeof mobile) == 'undefined' || mobile==null )?'':mobile;
        //if( mobile.length !=  11 )
        var match = /^(13|15|18)\d{9}$|^17(6|7|8)\d{8}$/;
        var emailpat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        if(match.test(mobile)||emailpat.test(mobile)){
            return true;
        }
        else{
            return false;
        }
    }
    function errfn(data) {
        tips(data.desc);
    }

    function sucfn() {
        var htmlsuc = '<div class="error_page"><div class="pic"><img src="./static/img/src/bg_nodata.png" alt=""></div>'
                    + '<span>反馈成功</span><a href="index.php" class="sub">返回首页</a></div>';
        $('.contain').html(htmlsuc);
    }
    function check() {
        var contact = $('#contact').val();
        if(!checkMobile(contact)){
            tips('联系方式不合法')
            return false;
        }
        var comment = $('#cont').val();
        if(comment.length==0){
            tips('请输入反馈内容')
            return false;
        }
        ajaxRequest({
            url:$conf.api+'other/feedBack.php',
            data:{uid:getCookie('_uid'),encpass:getCookie('_enc'),contact:contact,comment:comment}
        },sucfn,errfn)
    }
    $('.btn-sbt').click(function () {
        check();
    })
</script>
</body>
</html>
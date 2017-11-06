<?php
include_once '../../include/init.php';
include_once WEBSITE_PERSON . "isLogin.php";
include_once INCLUDE_DIR . 'Anchor.class.php';
$db = new DBHelperi_huanpeng();
$anchor = new AnchorHelp($_COOKIE['_uid']);

$isAnchor = (int)$anchor->isAnchor();

if($isAnchor && ( !RN_MODEL || $anchor->getRealNameCertifyInfo()['status'] == 101)){
    header("Location:".WEB_PERSONAL_URL."homepage/index.php");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>我做主播-欢朋直播-精彩手游直播平台！</title>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>

    <?php include WEBSITE_TPL . 'commSource.php'; ?>
    <link rel="stylesheet" type="text/css" href="<?php echo __CSS__; ?>person.css?v=1.0.5">
    <link rel="stylesheet" href="../application/beAnchor.css<?php echo "?time=".time(); ?>">
    <style type="text/css">
        body {
            background-color: #eeeeee;
        }
        #beanchor{
            padding: 0px 20px;
        }

        #beanchor .cert-to-anchor .page-body .commit .btn {
            margin:75px auto;
            padding:0px;
        }

        /*#beanchor .page-title{*/
            /*margin-top: 30px;*/
        /*}*/
        /*#beanchor .logo {*/
            /*width: 220px;*/
            /*height: 220px;*/
            /*float: left;*/
            /*border-radius: 220px;*/
            /*margin-left: 345px;*/
            /*margin-top: 45px;*/

            /*!*border:1px solid ;*!*/
        /*}*/

        /*#beanchor .noticeMsg {*/
            /*text-align: center;*/
        /*}*/

        /*#beanchor .noticeMSg h3 {*/
            /*font-size: 22px;*/
            /*color: #666666*/
        /*}*/

        /*#beanchor .noticeMSg h4 {*/
            /*font-size: 14px;*/
            /*color: #666666*/
        /*}*/

        /*#beanchor .noticeMSg .c_red {*/
            /*color: #f44336;*/
        /*}*/

        /*#beanchor .optdiv {*/
            /*text-align: center;*/
        /*}*/

        /*#beanchor .optdiv .btn {*/
            /*padding: 12px 80px;*/
            /*float: left;*/
            /*margin-left: 369px;*/
            /*background-color: #ff7800;*/
            /*border-radius: 4px;*/
            /*color: #fff;*/
        /*}*/

        /*#beanchor .optdiv .download p {*/
            /*margin: 0;*/
            /*font-size: 14px;*/
            /*color: #adadad;*/
        /*}*/

        /*#beanchor .optdiv .download .btn {*/
            /*padding: 14px 25px;*/
            /*margin-left: 365px;*/
        /*}*/

        /*#beanchor .cretifynotice {*/
            /*margin-left: 312px;*/
        /*}*/

        /*#beanchor .cretifynotice .cretify_icon {*/
            /*float: left;*/
            /*width: 75px;*/
            /*height: 75px;*/
            /*margin-right: 50px;*/
        /*}*/


        /*.agreement-rule{*/
            /*border: 1px solid #e0e0e0;*/
            /*!*overflow-y: scroll;*!*/
            /*!*height: 100px;*!*/
            /*padding: 30px 30px 40px 30px;*/
        /*}*/

        /*.agreement-rule .agreement-headline{*/
            /*font-size: 30px;*/
            /*text-align: center;*/
            /*padding-bottom: 22px;*/
        /*}*/

        /*.agreement-rule dt{*/
            /*font-weight: bold;*/
            /*margin-top: 1em;*/
        /*}*/

        /*.agreement-rule dd{*/
            /*text-indent: 2em;*/
            /*padding: 5px 0;*/
        /*}*/

        /*.sign-agreement-div{*/
            /*padding: 0 36px 44px 36px;*/
        /*}*/

        /*.sign-agreement-div .checkboxDiv{*/
            /*margin: 20px 0px 0px 40px;*/
            /*cursor: pointer;*/
        /*}*/
        /*.sign-agreement-div #agree-submit{*/
            /*margin: 40px 0px 60px 320px;*/
            /*width: 145px;*/
            /*height: 45px;*/
            /*background-color: #ff7800;*/
            /*color: #fff;*/
            /*border-color: #ff7800;*/
            /*border-radius: 4px;*/
            /*font-size: 14px;*/
        /*}*/

    </style>

    <script type="application/javascript">
        <?php
        include_once   WEBSITE_MAIN.'application/init.php';
        echo $varToJs.$varErrorToJs;
        ?>
    </script>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
    <div class="content">
        <div id="beanchor">
            <div class="page-content cert-to-anchor" style=";padding: 0;">
                <?php include WEBSITE_MAIN.'application/view/start.php'; ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
<script>
    personalCenter_sidebar('beanchor');
//    function empty2(params) {
//        return params = (params == '' || params == null || typeof(params) == 'undefined') ? 0 : params;
//    }
//    function timeFunc(x) {
//        var timer = parseInt($('#timer').text());
//        timer--;
//        if (timer < 0) {
//            console.log($('#timer').data('timerUrl'))
//            document.location = $('#timer').data('timerUrl');
//            return;
//        }
//        $('#timer').text(timer);
//        setTimeout('timeFunc(100)', 1000);
//    }
//
//
//    function pageLoad(data) {
//        console.log(data);
//        var info = data['info'];
//        if (empty2(info['phonestatus']))
//            $('.phone').addClass('finish');
//        if (empty2(info['identstatus']))
//            $('.mail').addClass('finish');
//        if (empty2(info['bankstatus']))
//            $('.person').addClass('finish');
//
//        if (!empty2(info['phonestatus']))
//            locationRef(1);
//        else if (!empty2(info['identstatus']))
//            locationRef(2);
//        else if (!empty2(info['bankstatus']))
//            locationRef(3);
//        else {
//            $('.success').removeClass('none');
//            $('#notice_title,#jump').addClass('none');
//            $('.logo').attr('src', '../static/img/logo/certifySucc.png')
//        }
//    }
//
//    var root = conf.getConf();
//    var uid = getCookie('_uid');
//    var url = root['domain'] + '/personal/mp/mp_ajax/rpc_ajax.php';
//    $.ajax({
//        type: 'post',
//        url: url,
//        data: {uid: uid},
//        success: pageLoad,
//        dataType: 'json'
//    });

//    var pageUser = {};
//    pageUser.uid = getCookie('_uid');
//    pageUser.enc = getCookie('_enc');
//    $.ajax({
//        url:$conf.person + 'mp/mp_ajax/rpc_ajax.php',
//        type:'post',
//        dataType:'json',
//        data:{
//            uid:pageUser.uid,
//            encpass:pageUser.enc
//        },
//        success:function(data){
//            judgeCertifyStatus(data);
//        }
//    });
//
//    function judgeCertifyStatus(data){
//        var info = data.info;
//
//        if(info['emailstatus'] == $conf.certStatus.mail.pass){
//            $('.mail').addClass('finish');
//        }
//        if(info['phonestatus'] == $conf.certStatus.phone.pass){
//            $('.phone').addClass('finish');
//        }
//        if(info['identstatus'] == $conf.certStatus.ident.pass){
//            $('.person').addClass('finish');
//        }
//
//        if(info['phonestatus'] != $conf.certStatus.phone.pass){
//            locationRef(1);
//        }else if (info['emailstatus'] != $conf.certStatus.mail.pass){
//            locationRef(2);
//        }else if (info['identstatus'] != $conf.certStatus.ident.pass){
//            if(info['identstatus'] == $conf.certStatus.ident.wait){
//                locationRef(3);
//            }else{
//                locationRef(4);
//            }
//        }else{
//            //申请成功
//            var isAnchor = <?php //echo $isAnchor;?>// || 0;
//            if(isAnchor) {
//                $('.success').removeClass('none');
//                $('#notice_title,#jump').addClass('none');
//                $('.logo').attr('src', $conf.domain + 'static/img/logo/certifySucc.png');
//            }else{
//                $('#beanchor').html('<p class="page-title">我做主播</p> <div class="sign-agreement-div"> <div class="step"><img src="../static/img/logo/beanchor-logo.png" alt=""/></div> <div class="agreement-rule"> <p class="agreement-headline">欢朋TV个人直播协议</p> <div class="article"> <dl> <dd>《欢朋TV个人直播协议》，是北京欢朋网络科技有限公司（以下简称“我方”）和你方（你方为自然人、法人或其他组织）所约定的规范双方权利和义务的具有法律效力的电子协议，下称“本协议”。你方勾选“我同意”或点击“我已阅读并遵守该协议”按钮，即表示你方已经仔细阅读、充分理解并完全地毫无保留地接受本协议的所有条款</dd> <dt>第一条 总则</dt> <dd> <b>1、</b> 你方根据我方注册要求及规则，在我方合法经营的斗鱼平台（以下简称“平台”）上申请成为我方的直播服务提供方（或称“直播方”），为我方平台用户提供在线解说（本协议项下“解说”均亦指“直播”）视频内容的直播服务，你方在我方平台提供服务期间均应视为协议期内。我方不事先审核前述被上载的、由你方参与、编辑、制作的视频内容，也不主动对该等视频进行任何编辑、整理、修改、加工。 </dd> <dd> <b>2、</b> 签署本协议前，你方已充分了解我方之各项规则及要求，且有条件及有能力、资格履行本协议约定的直播方职责及义务。本协议对你方构成有效的、带有约束力的、可强制执行的法定义务，你方对本协议下所有条款及定义等内容均已明确知悉，并无疑义。 </dd> <dd> <b>3、</b> 你方承诺并声明在为我方提供服务时符合所在地法律的相关规定，不得以履行本协议名义从事其他违反中国及所在地法律规定的行为。 </dd> <dd> <b>4、</b> 你方与我方不构成任何劳动法律层面的雇佣、劳动、劳务关系，我方无需向你方支付社会保险金和福利。 </dd> <dd> <b>5、</b> 未经我方事先书面同意，你方不得在第三方竞争平台上从事任何与解说相关的行为（包括但不限于：视频直播互动、同步推流、发布解说视频或其余类似行为）。前述第三方竞争平台指：与我方及我方关联公司有竞争关系的第三方直播平台，包括但不限于虎牙直播、战旗TV、熊猫TV、火猫直播、风云直播、播狗、新浪看游戏、QT、17173、PPTV、TGA、AZUBU、TWITCH等及其相关联的直播网站。 </dd> <dd> <b>6、</b> 你方在我方平台提供直播服务期间产生的所有成果（包括但不限于解说视频、音频，及与本协议事项相关的任何文字、视频、音频等，以下统称“直播方成果”）的全部知识产权（包括但不限于著作权、商标权等知识产权以及相关的一切衍生权利）、所有权及相关权益，由我方享有。协议期内及协议期满后，我方可以任何方式使用直播方成果并享有相应的收益，未经我方事先书面同意，你方不得自行或提供、授权給任何第三方以任何方式使用（包括但不限于在视频平台、直播平台、游戏网站等其他任何平台发布）及获得任何收益。 </dd> </dl> </div> </div> <div class="checkboxDiv"> <label class="checkbox-label" for="agree-rule"> <input id="agree-rule" type="checkbox" class="none"/> </label> <a href="javascript:;">我已经阅读并同意《欢朋TV个人直播协议》</a> </div> <button class="btn" id="agree-submit">提交</button> </div>');
//                !function(){
//                    $('.checkboxDiv').bind('click', function(){
//                        var checked = $('#agree-rule').is(':checked');
//                        if(checked){
//                            $(this).find('.checkbox-label').removeClass('checked');
//                        }else{
//                            $(this).find('.checkbox-label').addClass('checked');
//                        }
//                        document.getElementById('agree-rule').checked = !checked;
//                        return false;
//                    });
//
//                    $('#agree-submit').bind('click', function(){
//                        var agree = $('#agree-rule').is(':checked');
//                        if(!agree){
//                            tips('同意协议才可认证主播');
//                            return;
//                        }
//                        $.ajax({
//                            url:$conf.api + 'becomeAnchor.php',
//                            type:'post',
//                            dataType:'json',
//                            data:{
//                                uid:pageUser.uid,
//                                encpass:pageUser.enc,
//                                agree:agree
//                            },
//                            success:function(d){
//                                if(d.isSuccess == 1){
//                                    location.href = location.href;
//                                }
//                            }
//                        });
//                    });
//                }()
//            }
//        }
//    }
//
////    function locationRef(location) {
////        var root = conf.getConf();
////        var url = '';
////
////
////
////        if (locationConf[location]) {
////            $('#notice_title').text(locationConf[location].title);
////            $('.logo').attr('src', locationConf[location].img);
////            $('.toTheCertify').show();
////            $('.toTheCertify .btn').attr('href', locationConf[location].url).text(locationConf[location].btnText);
////        } else {
////            return;
////        }
////        $('#timer').data('timerUrl', url);
////        timeFunc();
////    }
//    function locationRef(status){
//        console.log('status is ' + status);
//        var locationConf = {
//            1: {
//                img: $conf.domain + 'static/img/src/bg_nodata.png',
//                url: $conf.person + 'mp/certify_phone',
//                btnText: '手机认证',
//                title: '申请主播需要手机认证'
//            },
//            2: {
//                img: $conf.domain + 'static/img/src/bg_nodata.png',
//                url: $conf.person + 'mp/certify_email',
//                btnText: '邮箱认证',
//                title: '申请主播需要邮箱认证'
//            },
//            3: {
//                img: $conf.domain + 'static/img/src/bg_nodata.png',
//                url: $conf.domain,
//                btnText: '返回首页',
//                title: '您的信息正在审核中，审核通过即可成为主播'
//            },
//            4: {
//                img: $conf.domain + 'static/img/src/bg_nodata.png',
//                url: $conf.person + 'mp/certify_realname',
//                btnText: '实名认证',
//                title: '申请主播需要实名认证'
//            }
//        };
//        if (locationConf[status]) {
//            $('#notice_title').text(locationConf[status].title);
//            $('.logo').attr('src', locationConf[status].img);
//            $('.toTheCertify').show();
//            $('.toTheCertify .btn').attr('href', locationConf[status].url).text(locationConf[status].btnText);
//        }
//    }



</script>
</body>
</html>

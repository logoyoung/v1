<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 16/11/28
 * Time: 17:28
 */
include '../module/checkLogin.php';
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>直播推荐</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <link href="../common/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="../common/global/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet"
          type="text/css"/>
    <link href="../common/global/plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGIN STYLES -->

    <!-- BEGIN THEME STYLES -->
    <link href="../common/global/css/components.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="../common/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="../common/admin/layout/css/themes/light.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="../common/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->
    <style type="text/css">
        html, body { margin: 0; padding: 0; }
        .news-content { min-width: 980px; width: 100%; min-height: 800px; }
        .news-content input, .news-content select { outline: none; }
        .news-content .news-mask { display: none; margin: 0px; padding: 0px; background: #fff; position: absolute; width: 100%; height: 100%; z-index: 999;left:0;top:0; }
        .news-content .news-mask .news-preview { width: 100%; height: 100%; }
        .news-content .news-left { border: 1px solid #ddd; border: 1px solid #ddd \9; *border: 1px solid #ddd; _border: 1px solid #ddd; padding: 20px; float: left; width: 65%; height: 100%; }
        .news-content .news-left .news-left-head { width: 100%; height: 100px; border-bottom: 1px solid #ddd; border-bottom: 1px solid #ddd \9; *border-bottom: 1px solid #ddd; _border-bottom: 1px solid #ddd; }
        .news-content .news-left .news-left-head .news-content-title { width: 100%; height: 50px; }
        .news-content .news-left .news-left-head .news-content-title > input { border: none; width: 100%; height: 100%; font-size: 24px; line-height: 100%; outline: none; }
        .news-content .news-left .news-left-head .news-content-options { margin: 20px 0px; width: 100%; height: 50px; }
        .news-content .news-left .news-left-head .news-content-options .news-content-type { float: left; }
        .news-content .news-left .news-left-head .news-content-options .news-content-editor { float: right; padding: 0px 40px; }
        .news-content .news-left .news-left-body { width: 100%; }
        .news-content .news-right { font-size: 13px; float: right; width: 28%; margin-top: 26px; }
        .news-content .news-right .news-poster { background: #f5f5f5; width: 192px; height: 126px; position: relative; }
        .news-content .news-right .news-poster .news-poster-submit, .news-content .news-right .news-poster .news-poster-real { width: 100px; height: 40px; position: absolute; left: 46px; top: 45px; text-align: center; line-height: 38px;  -webkit-border-radius: 4px; -moz-border-radius: 4px; -ms-border-radius: 4px; -o-border-radius: 4px; border-radius: 4px; cursor: pointer; background: #ff7800; color:#fff; }
        .news-content .news-right .news-poster .news-poster-real { visibility: hidden; }
        .news-content .news-right .news-poster-txt { line-height: 50px; border-bottom: 1px solid #ddd; border-bottom: 1px solid #ddd \9; *border-bottom: 1px solid #ddd; _border-bottom: 1px solid #ddd; }
        .news-content .news-right .news-group-btn { width: 100%; }
        .news-content .news-right .news-group-btn .news-btn { width: 92px; height: 38px; line-height: 20px; text-align: center; float: left; cursor: pointer; border: 1px solid #ddd; border: 1px solid #ddd \9; *border: 1px solid #ddd; _border: 1px solid #ddd; -webkit-border-radius: 4px; -moz-border-radius: 4px; -ms-border-radius: 4px; -o-border-radius: 4px; border-radius: 4px; }
        .news-content .news-right .news-group-btn .news-btn-right { margin-left: 20px; }
    </style>

    <link href="../common/admin/pages/css/vertifyRealName.css" rel="stylesheet" type="text/css"/>
    <?php include '../module/mainStyle.php'; ?>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content page-style-square content_body page-sidebar-fixed">
<?php include '../module/head.php'; ?>
<div class="clearfix"></div>
<div class="page-container">
    <?php include '../module/sidebar.php'; ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box bg-yellow-gold">
                        <div class="portlet-title">
                            <div class="caption">
                                <h4 class="page-title">新建</h4>
                            </div>
                            <div class="tools">
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="tabbable">
                                <!--<ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#anchor_wait_tab" id="anchor_wait" data-toggle="tab">
                                            推荐列表 </a>
                                    </li>
                                    <li>
                                        <a href="#anchor_pass_tab" id="anchor_pass" data-toggle="tab">
                                            直播列表 </a>
                                    </li>
                                </ul>-->
                                <!--<div class="tab-content no-space">
                                    <div class="tab-pane active" id="anchor_wait_tab">
                                        <div class="portlet-body">
                                            <table class="table table-striped table-bordered table-hover" >
                                                <thead>
                                                <tr text-align:center>
                                                    <th>liveId</th>
                                                    <th>主播昵称</th>
                                                    <th>游戏名称</th>
                                                    <th>直播标题</th>
                                                    <th>封面图</th>
                                                    <th>开播时间</th>
                                                    <th>操作</th>
                                                </tr>
                                                </thead>
                                                <tbody id="recommend_tbodys">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="anchor_pass_tab">

                                        <table class="table table-striped table-bordered table-hover" >
                                            <thead>
                                            <tr text-align:center>
                                                <th>liveId</th>
                                                <th>主播昵称</th>
                                                <th>游戏名称</th>
                                                <th>直播标题</th>
                                                <th>封面图</th>
                                                <th>开播时间</th>
                                                <th>操作</th>
                                            </tr>
                                            </thead>
                                            <tbody id="anchor_pass_tbodys">
                                            </tbody>
                                        </table>
                                        <nav style="text-align: center">
                                            <ul class="pagination" id="pagination_pass"></ul>
                                        </nav>

                                    </div>
                                </div>-->

                                <!--------news-->
                                <div class="news-content" >
                                    <div class="news-mask">
                                        <div id="preview-x" style="width: 30px;height: 30px;position: absolute;right: 0;top: 0;cursor:pointer;">返回</div>
                                        <ifram class="news-preview"></ifram>
                                    </div>
                                    <div class="news-left">
                                        <div class="news-left-head">
                                            <div class="news-content-title">
                                                <input type="text" id="news-title" v-model="newsTitle" placeholder="请在这里输入标题">
                                            </div>
                                            <div class="news-content-options">
                                                <div class="news-content-type">
                                                    <span>类型：</span>
                                                    <select id="news-type" class="form-filter input-sm">
                                                        <option value="5">新闻</option>
                                                        <option value="8">活动</option>
                                                        <option value="13">公告</option>
                                                    </select>
                                                </div>
                                                <div class="news-content-editor" id="newsEditor">小编：<span id="news-editor-name"></span></div>
                                            </div>
                                        </div>
                                        <div class="news-left-body">
                                            <ifram style="width: 100%;height: 100%;">
                                                <script id="editor" type="text/plain" style="width:100%;height:500px;"></script>
                                            </ifram>
                                        </div> 

                                    </div>
                                    <div class="news-right">
                                        <div class="news-poster">
                                            <img style="width: 100%;height:100%;"src="http://dev.huanpeng.com/static/img/src/default/164x92.png">
                                           <form id="news-poster-form" style="display: none" enctype="multipart/form-data">
                                            <input  id="news-poster" class="news-poster-real" type="file" name="file" accept="image/jpeg, image/jpg, image/png, image/gif">
                                            </form>
                                               <div  class="news-poster-submit">上传封面</div>
                                        </div>
                                        <div class="news-poster-txt"><p>封面 ：尺寸：260像素 * 116像素</p></div>
                                        <label for="radio-group">
                                            <p>推荐类型：</p>
                                            <p><input style="margin-left: -10px" name="radio" type="radio" value="0" checked="true" /><label>不推荐</label></p>
                                            <p><input style="margin-left: -10px" name="radio" type="radio" value="1" /><label>首页焦点推荐（需要上传封面)</label></p>
                                            <p><input style="margin-left: -10px" name="radio" type="radio" value="2" /><label>首页列表推荐</label></p>
                                        </label>
                                        <div class="news-group-btn">
                                            <div class="news-btn news-btn-left btn green"  >预览</div>
                                            <div class="news-btn news-btn-right btn blue" >保存</div>
                                        </div>
                                    </div>
                                </div>
                                <!--------news------>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

<?php include '../module/footer.php'; ?>
<?php include '../module/mainScript.php'; ?>
<script type="text/javascript" src="../common/global/scripts/common.js"></script>
<script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
<script type="text/javascript" charset="utf-8" src="../plugin/richtxt/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="../plugin/richtxt/ueditor.all.js"> </script>
<script type="text/javascript" charset="utf-8" src="../plugin/richtxt/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript">
    var ue = UE.getEditor('editor');
    ue.addListener('ready',function (ue) {
        (function(){
            var NEWS = function () {
                var data = {};
                var admin = {};
                //var newsContent = ue.getContent();
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
                var encodeHtml = function (str) {
                    var s = "";
                    if (str.length == 0) return "";
                    //s = str.replace(/&/g, "&gt;");
                    s = str.replace(/</g, "&lt;");
                    s = s.replace(/>/g, "&gt;");
                    s = s.replace(/ /g, "&nbsp;");
                    s = s.replace(/\'/g, "&#39;");
                    s = s.replace(/\"/g, "&quot;");
                    s = s.replace(/\n/g, "<br>");
                    return s;
                }
                var getAdmin = function () {
                    return  {
                        _uid:getCookie('admin_uid'),
                        _enc:getCookie('admin_enc'),
                        _type:getCookie('admin_type'),
                        _name:getCookie('admin_name')
                    }
                }
                var getNewsData = function () {
                    //var newsContent = ue.getContent();
                    return {
                        newsTitle:document.getElementById('news-title').value,
                        newsType:document.getElementById('news-type').value,
                        newsRecommendType:(function () {
                            var radioGroup = document.getElementsByName('radio');
                            for(var i=0;i<radioGroup.length;i++){
                                if(radioGroup[i].checked)
                                    return radioGroup[i].value;
                            }
                        }()),
                        newsContent:''
                    }
                };

                var setNewsData = function (data) {console.log(ue.setContent);
                    document.getElementById('news-title').value = data.title;
                    document.getElementById('news-type').value = data.tid;
                    document.getElementsByName('radio').item(parseInt(data.isRecommend)).checked = true;
                    $('.radio>span').removeClass('checked');
                    $('.radio>span').eq(parseInt(data.isRecommend)).addClass('checked');
                    //setContent(data.content);
                    //UE.getEditor('editor').getContent();
                    console.log(data.content);
                    UE.getEditor('editor').execCommand('insertHtml', decodeHtml(data.content));
                }
                var newsAjax = function () {
                    data = getNewsData();//刷新获取
                    var newsData = {
                        id:$_GET['nid'],
                        uid:admin._uid,
                        encpass:admin._enc,
                        type:admin._type,
                        tid:data.newsType,
                        title:data.newsTitle,
                        content:UE.getEditor('editor').getContent(),
                        isRecommend:data.newsRecommendType,
                        poster:poster,
                        status:0
                    };
                    if(!$_GET['nid'])
                        var newsUrl = $conf.api+'information/info/addInformation.php';
                    else
                        var newsUrl = $conf.api+'information/info/updateInformation.php';

                    $.ajax({
                        url:newsUrl,
                        data:newsData,
                        type:'post',
                        dataType:'json',
                        success:function(d){
                            if(!d.stat)
                                dialog(d.err.desc);
                            else
                                dialog('提交成功');
                        }
                    })
                }
                var poster = '';
                var posterPreview = function(){
                    //$('.news-poster-real').bind('click')
                    $('.news-poster-real').bind('change',function () {
                        /*
                        window.URL = window.URL||window.webkitURL;
                        poster = window.URL.createObjectURL($(this)[0].files[0]);
                        $('.news-poster>img').attr('src',poster);
                        window.URL.revokeObjectURL($(this)[0].files[0]);
                        */
                        $('#news-poster-form').ajaxSubmit({
                            url:$conf.api_upload + 'tool/upLoadPic.php',
                            data:{
                                uid:admin._uid,
                                encpass:admin._enc,
                                type:admin._type,
                                utype:1
                            },
                            type:'post',
                            dataType:'json',
                            success:function (d) {
                                poster = d['resuData'].poster;
                                var src = d['resuData'].domain + d['resuData'].poster;
                                $('.news-poster>img').attr('src',src);
                            }
                        });
                    })
                    $('.news-poster-submit').bind('click',function () {
                        $('.news-poster-real').trigger('click');
                    });
                }
                var modifyNews = function (id,callback) {
                    //var admin =
                    var data = {
                        uid:admin._uid,
                        encpass:admin._enc,
                        type:admin._type,
                        poster:poster,
                        id:id
                    };
                    $.ajax({
                        url:$conf.api+'information/info/getInformation.php',
                        data:data,
                        type:'post',
                        dataType:'json',
                        success:callback
                    })
                }
                var callback = function (d) {
                    console.log(d);
                    setNewsData(d['resuData'].list)
                }
                var dialog = function (msg) {
                    alert(msg);
                }

                //
                var preview = function () {
                    var newsContent = UE.getEditor('editor').getContent();
                    document.getElementsByClassName('news-mask').item(0).style.display = 'block';
                    document.getElementsByClassName('news-preview').item(0).innerHTML = newsContent;
                }
                var eventsRegister = function () {
                    $('.news-btn-left').bind('click',preview);
                    $('.news-btn-right').bind('click',newsAjax);
                    $('#preview-x').bind('click',function () {
                        $('.news-mask').css('display','none');
                    })
                }
                var showAdminName = function (name) {
                    document.getElementById('news-editor-name').innerHTML = name;
                }
                var init = function () {
                    data = getNewsData();
                    admin = getAdmin();
                    eventsRegister();
                    showAdminName(admin._name);
                    if($_GET['nid'])
                        modifyNews($_GET['nid'],callback);
                    else if($_GET['type'])
                        document.getElementById('news-type').value = parseInt($_GET['type']);
                    posterPreview();
                    return true;
                }
                return {
                    init:init(),
                    ajax:newsAjax,
                    preview:preview
                }

            };
            window.ADMIN_NEWS = NEWS();

        }())
    });
</script>
</body>
</html>
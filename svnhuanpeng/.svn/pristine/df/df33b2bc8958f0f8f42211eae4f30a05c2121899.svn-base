<?php
require_once '../../include/init.php';
include_once WEBSITE_PERSON."isAnchor.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>我的房管-欢朋直播-精彩手游直播平台！</title>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <?php include WEBSITE_TPL.'commSource.php';?>
    <link rel="stylesheet" type="text/css" href="<?php echo STATIC_CSS_PATH;?>person.css?v=1.0.5">
    <style type="text/css">
        body{
            background-color: #eeeeee;
        }
        #roomadmin{
            padding: 0 20px 45px 20px;
        }
        #roomadmin .userOne{
            float: left;
            margin: 0 15px 30px 15px;
            display: block;
            border:1px solid #e0e0e0;
			width: 195px;
			height: 110px;
        }
        #roomadmin .userOne .face{
            width: 60px;
            height: 60px;
            display: block;
            float: left;
			margin: 10px 10px 0px 10px;
        }
        #roomadmin .userOne .info{
            float: left;
        }
        #roomadmin .userOne .info p{
            margin: 10px 0px 10px 10px;
            max-width: 104px;
            overflow: hidden;
            text-overflow:ellipsis;
            font-size: 14px;
            color: #adadad;
			white-space: nowrap;
        }
        #roomadmin .userOne .opt{
            float: right;
            cursor: pointer;
            color:#ff7800;
            font-size: 14px;
            margin-top: 15px;
			margin-right: 12px;
            display: none;
        }
        #roomadmin .userOne:hover{
            border-color: #ff7800 ;
        }
        #roomadmin .userOne:hover .opt{
            display: block;
        }
        #roomadmin .userOne .opt:hover{
            color: #ff9e48;
        }

        #roomadmin #addOne{
            color:#FFFFFF;
            float: right;
            width: 80px;
            height: 30px;
            padding: 0;
            background-color: #ff7800;
            border: 1px solid #ff7800;
            margin-top: -42px;
        }

        #addOne span{
            float: left;
            line-height: 28px;
            font-size: 14px;
        }

        #addOne .personal_icon{
            float: left;
            width: 20px;
            height: 20px;
            margin: 4px 2px 4px 12px;
            background-position: -160px -77px;
        }

        #ensureDelete{
            margin-left: 120px;
            background-color: #f44336;
            border-color: #f44336;
        }

        #addsubmit{
            padding: 12px 40px;
            background-color: #f44336;
            border:1px solid #f44336;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
    <div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
        <div class="content">
            <div id="roomadmin">
                <p class="page-title">我的房管</p>
                <button id="addOne" class="btn"><span class="personal_icon"></span><span>添加</span></button>
                <div class="adminlist mt-30">

                </div>
            </div>
        </div>
    </div>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
</body>
<script type="text/javascript">
	personalCenter_sidebar('admin');

	$conf = conf.getConf();
    $(document).ready(function(){

        !function(){
            var dialogConfig = {
                title:'提示',
                content:'',
                cancelValue:'取消',
                cancel:function(){},
                okValue:'确定',
                ok:function(){}
            };

            var add_loading = false;
            $('#addOne').bind('click', function(){
                function addAdmin(){
                    var adminNick = $.trim($('#adminName').val());
                    if(!adminNick){
                        $('.addAdmin-notice .err-word-notice').html('昵称不能为空');
                        return false;
                    }
                    if(add_loading)
                        return false;

                    add_loading = true;
                    var requestUrl = $conf.api + 'room/addHomeAdmin.php';
                    var requestData = {
                        uid:getCookie('_uid'),
                        encpass:getCookie('_enc'),
                        nick:adminNick
                    };
                    ajaxRequest({url:requestUrl,data:requestData},function(d){
                        add_loading = false;
                        $('.adminlist').prepend(userOneHtml(d));
                        $('.adminlist .userOne:eq(0) .opt').bind('click', cancelRoomAdminEvent);
                        diaLogs.close().remove();
                        $('.nodata').remove();
                    },function (d) {
                        add_loading = false;
                        $('.addAdmin-notice .err-word-notice').html(d.desc);
                    },function(){
                        add_loading = false;
                    });
                    return false;
                };
                var content = '<input id="adminName" placeholder="请输入昵称"/><p class="err-word-notice"></p>';
                var diaLogs = dialog($.extend(dialogConfig,{
                    'title':'添加房管   ',
                    skin:'err-notice person-notice addAdmin-notice',
                    content:content,
                    ok:addAdmin
                }));
                diaLogs.showModal();
                $('.ui-dialog-close').text('');
                $(".addAdmin-notice input").keypress(function(event){
                    var keycode = (event.keyCode ? event.keyCode : event.which);
                    if(keycode == '13'){
                        addAdmin();
                    }
                });
                $(".addAdmin-notice input").focus(function(){
                    $('.addAdmin-notice .err-word-notice').html('');
                });
            });
            var requestUrl = $conf.api + 'room/getHomeAdminList.php';
            var requestData ={
                uid:getCookie('_uid'),
                encpass:getCookie('_enc'),
                size:11,
                page:1
            };

            ajaxRequest({url:requestUrl,data:requestData},function(d){
                if(d.list.length){
                    $('.adminlist').html('');
                    for(var i in d.list){
                        $('.adminlist').append(userOneHtml(d.list[i]));
                    }
                    $('.adminlist').append('<div class="clear"></div>');
                    var del = $(".adminlist .userOne .opt");
                    del.bind('click',cancelRoomAdminEvent);
                }else{
                    $('.adminlist').html('<div class="nodata" style="width: 460px;height: 260px;margin: 50px auto;"><img src="../static/img/logo/noddata-admin.png" alt=""></div>')
                }
            },function(d){

            });



            function userOneHtml(d){
                var htmlstr = '';
                htmlstr += '<div class="userOne" data-userID="'+ d.uid+'">';
                htmlstr += '<div class="face">';
                htmlstr += '<img src="'+ d.head+'">'
                htmlstr += '</div>';
                htmlstr += '<div class="info">';
                htmlstr += '<p>'+ d.nick+'</p>';
                htmlstr += '</div> ';
                htmlstr += '<div class="clear"></div>';
                htmlstr += '<div class="opt">';
                htmlstr += '<a>取消管理员</a>';
                htmlstr += '</div>';
                htmlstr += '<div class="clear"></div>';
                htmlstr += '</div>';

                return htmlstr;
            }

            function cancelRoomAdminEvent(){
                var admin = $(this).parent();
                var diaLogs = dialog($.extend(dialogConfig, {
                    skin:'err-notice person-notice cancelAdmin-notice',
                    content:'<p>你确定要取消该管理员么?</p>',
                    title:'取消房管',
                    ok:delAdmin
                }));
                diaLogs.showModal();
                $('.ui-dialog-close').text('');
                function delAdmin(){
                    var adminUserID = parseInt(admin.attr('data-userID'));
                    if(!adminUserID){
                        alert('无效的参数');
                        return true;
                    }
                    var requestUrl = $conf.api +'room/cancelHomeAdmin.php';
                    var requestData = {
                        uid:getCookie('_uid'),
                        encpass:getCookie('_enc'),
                        adminID:adminUserID
                    };
                    ajaxRequest({url:requestUrl,data:requestData},function(d){
                        admin.remove();
                        diaLogs.close().remove();
                    });
                    return false;
                }
            }
        }();


//        return;
//        (function(){
//
//
//			function 	cancelRoomAdminEvent(){
//				var admin = $(this).parent();
//				function noticeBoxHtml(){
//					var htmlstr = '';
//					htmlstr += '<div id="noticeBox" style="position:fixed;left:50%;top:320px;z-index: 1000;">'
//					htmlstr += '<div class="theBox" style="padding: 26px 16px">';
//					htmlstr += '<div class="box_head">';
//					htmlstr += '<div class="closeBox">';
//					htmlstr += '<span class="personal_icon close"></span>';
//					htmlstr += '<div class="clear"></div>';
//					htmlstr += '</div>';
//					htmlstr += '</div>';
//					htmlstr += '<div class="box_body">';
//					htmlstr += '<div class="imgLogo"></div>';
//					htmlstr += '<p>真的要取消这位管理员么？</p>';
//					htmlstr += '</div>';
//					htmlstr += '<div class="box_foot">';
//					htmlstr += '<button id="ensureDelete" class="btn">确认删除</button>';
//					htmlstr += '<button class="btn close">关闭</button>';
//					htmlstr += '</div>';
//					htmlstr += '</div>';
//					htmlstr += '</div>';
//
//					return htmlstr;
//				}
//				function deladmin(){
//					var adminUserID = parseInt(admin.attr('data-userID'));
//					if(!adminUserID) {
//						console.log('adminUserID is not found');
//						return;
//					}
//					$.ajax({
//						url:$conf.api + 'cancelHomeAdmin.php',
//						type:'post',
//						dataType:'json',
//						data:{
//							uid:getCookie('_uid'),
//							encpass:getCookie('_enc'),
//							adminID:adminUserID
//						},
//						success:function(d){
//							if(d.isSuccess == 1){
//								admin.remove();
//								NoticeBox.remove();
//							}
//						}
//					});
//				}
//
//				var htmlstr = noticeBoxHtml();
//				NoticeBox.create(htmlstr);
//				$("#noticeBox .close").bind('click', NoticeBox.remove);
//				$('#ensureDelete').bind('click', deladmin);
//			}
//            var add = $("#addOne");
//            add.bind('click',function(){
//                function noticeBoxHtml(){
//                    function head(){
//                        var htmlstr = '';
//                        htmlstr += '<div class="box_head">';
//                        htmlstr += '<p class="title left">添加管理员</p>';
//                        htmlstr += '<div class="closeBox">';
//                        htmlstr += '<span class="personal_icon close"></span>';
//                        htmlstr += '<div class="clear"></div>';
//                        htmlstr += '</div>';
//                        htmlstr += '</div>';
//
//                        return htmlstr;
//                    }
//                    function body(){
//                        var htmlstr = '';
//                        htmlstr +='<div class="box_body" style="margin-bottom: 0px;">';
//                        htmlstr +='<div class="control-group">';
//                        htmlstr +='<div class="control-label">用户昵称</div>';
//                        htmlstr +='<div class="controls">';
//                        htmlstr +='<input id="adminName" type="text" class="w-230 text" placeholder="请输入用户昵称">';
//                        //htmlstr +='<div class="errinfo">该昵称不存在</div>';
//                        htmlstr +='</div>';
//                        htmlstr +='</div>';
//                        htmlstr +='<div class="controls">';
//                        htmlstr +='<button class="btn" id="addsubmit">添加</button>';
//                        htmlstr +='</div>';
//                        htmlstr +='</div>';
//
//                        return htmlstr;
//                    }
//
//                    var htmlstr = '';
//                    htmlstr += ' <div id="noticeBox" style="position:fixed;left:50%;top:220px;z-index: 1000;">';
//                    htmlstr += '<div class="theBox" style="padding: 30px 20px; height: auto;">';
//                    htmlstr = htmlstr + head() + body() + '</div>' + '</div>';
//
//                    return htmlstr;
//                }
//				function successFn(d){
//					if(d.isSuccess == 1){
//						$('.adminlist').prepend(userOneHtml(d.adminer));
//						$('.adminlist .userOne:eq(0) .opt').bind('click', cancelRoomAdminEvent);
//						NoticeBox.remove();
//					}else{
//						$('#adminName').parent().find('.errInfo').remove();
//						$('#adminName').parent().append('<div class="errinfo">该用户不存在</div>');
//					}
//				}
//
//                var htmlstr = noticeBoxHtml();
//                NoticeBox.create(htmlstr);
//
//                $("#noticeBox .close").bind('click', NoticeBox.remove);
//                $('#noticeBox #adminName').focus('click',function(){
//                    // $(this).val('');
//                    $("#noticeBox .box_body .errinfo").remove();
//                });
//				$('#addsubmit').bind('click',function(){
//					console.log('add submit click');
//					var adminNick = $.trim($('#adminName').val());
//					if(!adminNick){
//						$('#adminName').parent().find('.errInfo').remove();
//						$('#adminName').parent().append('<div class="errinfo">昵称不能为空</div>');
//						return;
//					}
//					var option = {
//						url:$conf.api + 'addHomeAdmin.php',
//						type:'post',
//						dataType:'json',
//						data:{
//							uid:getCookie('_uid'),
//							encpass:getCookie('_enc'),
//							adminNick:adminNick
//						},
//						success:successFn
//					};
//					$.ajax(option);
//				});
//            });
//
//			$.ajax({
//				url:$conf.api + 'getHomeAdminList.php',
//				type:'post',
//				dataType:'json',
//				data:{
//					uid:getCookie('_uid'),
//					encpass:getCookie('_enc'),
//					size:11,
//					page:1
//				},
//				success:function(d){
//					if(d.roomAdminList){
//						for(var i in d.roomAdminList)
//							$('.adminlist').append(userOneHtml(d.roomAdminList[i]));
//                        $('.adminlist').append('<div class="clear"></div>');
//						var del = $(".adminlist .userOne .opt");
//						del.bind('click',cancelRoomAdminEvent);
//					}
//				}
//			});
//        }());

    });
</script>
</html>

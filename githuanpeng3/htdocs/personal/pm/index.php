<?php
include_once '../../../include/init.php';
include_once WEBSITE_PERSON . "isLogin.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>我的消息-欢朋直播-精彩手游直播平台！</title>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <?php include WEBSITE_TPL . 'commSource.php'; ?>
    <link rel="stylesheet" type="text/css" href="<?php echo __CSS__; ?>person.css?v=1.0.5">
    <script type="text/javascript" src="../../static/js/json2.js?v=1.0.4"></script>
    <style type="text/css">
    body {
        background-color: #eeeeee;
    }

    #msgCon {
        padding: 0 20px 40px 20px;
    }

    #msgCon .head {
        border-bottom: 1px solid #e0e0e0;
    }

    #msgCon .head .deleteGroup {
        margin-bottom: 12px;
    }

    #msgCon .head .btn {
        border-radius: 4px;
        padding: 10px 20px;
        color: #999999;
    }

    #msgCon .head .deleteGroup .left {
        margin-top: 19px;
        line-height: 20px;
        font-size: 14px;
        line-height: 16px
    }

    #msgCon .head .deleteGroup .checkAll {
        margin: 2px 6px 0px 12px;
        width: 16px;
        height: 16px;
        float: left;
    }

    #msgCon #ensureDel {
        background-color: #FF7800;
        border-color: #FF7800;
        color: #fff;
    }

    #msgCon #cancelDel {
        background-color: #e0e0e0;
        border-color: #e0e0e0;
    }

    #msgCon #delMsgBtn {
        color: #FFFFFF;
        background-color: #ff7800;
        border-color: #ff7800;
        font-size: 12px;
        padding: 10px 20px;
        margin-bottom: 12px;
        float: right;
        border-radius: 4px;
    }

    #msgCon .msgOne {
        margin: 30px 0px;
    }

    #msgCon .msgOne .face {
        width: 60px;
        height: 60px;
        border-radius: 60px;
        float: left;
    }

    #msgCon .msgOne .msginfo {
        background-color: #fafafa;
        padding: 18px 20px;
        width: 794px;
        margin-left: 12px;
        min-height: 24px;
        float: right;
        line-height: 16px;
        color: #333333;
    }

    #msgCon .msgOne .msginfo:before {
        content: '';
        position: absolute;
        border-right: 6px solid #fafafa;
        border-top: 6px solid transparent;
        border-bottom: 6px solid transparent;
        margin-left: -25px;
    }

    #msgCon .msgOne.onedit .msginfo {
        width: 744px;
    }

    #msgCon .msgOne .check {
        width: 16px;
        height: 16px;
        margin: 24px 22px 0px 12px;
        float: left;
    }

    #msgCon .msgOne .msginfo .sendname {
        font-size: 14px;
        color: #ff7800;
    }

    #msgCon .msgOne .msginfo .sendtime {
        margin-top: 2px;
        float: right;
        color: #b6b6b6;
    }

    #msgCon .msgOne .msginfo .info {
        position: relative;
    }

    #msgCon .msgOne .msginfo .info.retracts {
        height: 34px;
        overflow: hidden;
    }

    #msgCon .msgOne .msginfo .info.retracts:after {
        /* content: '...'; */
        font-weight: bold;
        position: absolute;
        right: 45px;
        bottom: 1px;
        width: 18px;
        background-color: #fafafa;
    }

    #msgCon .msgOne .msginfo .info .showopt {
        position: absolute;
        display: block;
        background-color: #fafafa;
        right: 14px;
        cursor: pointer;
        color: #969696;
    }

    #msgCon .msgOne .msginfo .info .showopt.show {
        bottom: 2px;
    }

    #msgCon .msgOne .msginfo .info .showopt.hidden {
        position: static;
        text-align: right;
        margin-right: 14px;
    }

    #msgCon .msgOne .msginfo .info .personal_icon {
        width: 14px;
        height: 12px;
        position: absolute;
        right: 0px;
        bottom: 5px;
        background-color: #fafafa;
    }

    #msgCon .msgOne .msginfo .info .arrow_up {
        bottom: 1px;
    }

    .pageIndex {
        height: 30px;
        float: right;
        margin: 30px 30px 8px 30px;
    }

    .pageIndex .pageNum {
        width: 20px;
        height: 20px;
        background: #fff;
        text-align: center;
        line-height: 20px;
        display: inline-block;
        border: 1px solid #e7e7e7;
        margin: 0px 3px;
    }

    .pageIndex span.current {
        width: 20px;
        height: 20px;
        text-align: center;
        line-height: 20px;
        display: inline-block;
        color: #fff;
        background: #ff7800;
    }

    .pageIndex span.point {
        width: 20px;
        height: 20px;
        text-align: center;
        line-height: 20px;
        display: inline-block;
    }

    .pageIndex span {
        margin: 0px 3px;
    }

    .pageIndex span.disabled {
        color: #A0A0A0;
    }
    </style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
    <div class="content">
        <div id="msgCon">
            <div class="head mt-30">
                <!-- <div class="deleteGroup">
                    <div class="left">
                        <input type="checkbox" class="checkAll"> <span>全选</span>
                    </div>
                    <div class="right">
                        <button id="ensureDel" class="btn">删除</button>
                        <button id="cancelDel" class="btn">取消</button>
                    </div>
                    <div class="clear"></div>
                </div> -->
                <button id="delMsgBtn" class='btn none'>编辑</button>
                <div class="clear"></div>
            </div>
            <div class="msgBody">
                <!-- <div class="msgOne onedit">
                    <input class="check" type="checkbox" />
                    <div class="face"></div>
                    <div class="msginfo">
                        <div class="titleDiv">
                            <span class="sendname">欢朋小助手</span> <span class="sendtime">2015.10.16</span>
                        </div>
                        <div class="info mt-15 retracts">
                            就啊的说法劳动法酸辣
                            <span class="showopt show">[展开]</span> <span
                                class="personal_icon arrow_bt"></span>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div> -->
            </div>
            <div class="pageIndex"></div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php'; ?>
</body>
<script type="text/javascript">


var msg2 = msg2 || function () {
        this._uid = getCookie('_uid');
        this._enc = getCookie('_enc');
        this._size = 5;//每页显示5个
        this._page = 1;//当前显示的页数
        this._msgCount = 0;//当前消息的数量
        this._delMsg = [];//带删除消息队列

        var apiUrl = conf.getConf();
        this._url = apiUrl['api'] + 'user/info/userMessageLists.php';
        this._delUrl = apiUrl['api'] + 'user/info/deleteMessage.php';
        this._start();
    };
msg2.prototype = {
    _start: function () {
        this._getMsg(this);
        
    },
    _getMsg: function () {
        var o = arguments[0];
        var requestUrl = o._url;
        var requestData = {
            uid:o._uid,
            encpass:o._enc,
            page:o._page,
            size:o._size
        };

        ajaxRequest({url:requestUrl,data:requestData},function (data) {
            o._HTMLLoad(data,o);
        });
//        $.ajax({
//            url: o._url,
//            data: {uid: o._uid, encpass: o._enc, page: o._page, size: o._size},
//            type: 'post',
//            dataType: 'json',
//            async: false,//这里采用同步方式避免与删除冲突
//            success: function (data) {
//                o._HTMLLoad(data, o)
//            }
//        });
    },
    _HTMLLoad: function (data) {
        if (!data || typeof(data) != 'object')
            return;
        var o = arguments[1];
        o._msgCount = data['total'];
        var msgList = data['list'];
        var msgOneStr = '';
        var msgBody = $('.msgBody');
        msgBody.html('');

        if(o._msgCount == 0){
            $('#msgCon .head').css('height','52px');
            $('#delMsgBtn').addClass('none');
            msgBody.html('<div class="nodata" style="width: 460px;height:260px;margin: 100px auto;"><img src="'+$conf.domain+'static/img/logo/nodata-usermsg.png" alt=""></div>');
            return;
        }

        for (var key in msgList) {
            msgOneStr = ' <div class="msgOne" msgId="' + msgList[key]['msgID'] + '">'
            + '<div class="face"><img src="' + msgList[key].head
            + '"></div><div class="msginfo"><div class="titleDiv">'
            + '<span class="sendname">' + msgList[key].title
            + '</span> <span class="sendtime">' + js_date_format('yyyy-MM-dd HH:mm:ss',msgList[key].ctime)
            + '</span></div><div class="info mt-15 retracts">' + msgList[key].comment
            //+ '<span class="showopt show">[展开]</span>'
            //+ '<span class="personal_icon arrow_bt"></span>'
            + '</div></div><div class="clear"></div></div>';
            msgBody.append(msgOneStr);
        }
        // o._deleteMsg(o);
        o._createPageIndex(o);
    },
    _createPageIndex: function (o) {
        o._nodeFresh();
        var d = o._getd(o);
        $('.pageIndex').createPage(d);
    },
    _getd: function (o) {
        return {
            pageCount: Math.ceil(o._msgCount / o._size),
            current: o._page,
            pageMax: 6,
            backFn: function (e) {
                o._page = e;
                o._getMsg(o)
            }
        };
    },
    _nodeFresh: function () {
        $('.pageIndex').replaceWith('<div class="pageIndex"></div>');
    },
    _deleteMsg: function () {
        $('#ensureDel').click(function () {//alert(1)
            $('input[class="check"][checked]').each(function () {
                console.log($(this).parent().attr('msgId'))
                if ($(this).attrt('checked') == true)
                    console.log($(this).parent().attr('msgId'))
            });
        });
    },
    _delReq: function () {
        var o = arguments[0];
        var requestUrl = o._delUrl;
        var requestData = {
            uid:o._uid,
            encpass:o._enc,
            delMsgList:o._delMsg.join(',')
        };
        ajaxRequest({url:requestUrl,data:requestData},function(d){
            var lastPage = Math.ceil(o._msgCount / o._size);
            if (lastPage == o._page) {//删除最后一页选项
                var lastMsgCount = o._msgCount % o._size;//最后一页消息数量
                lastMsgCount = lastMsgCount ? lastMsgCount : o._size;//余数为0则为整页数
                console.log(lastMsgCount + '--' + o._delMsg.length);
                if (o._delMsg.length == lastMsgCount) {
                    o._page--;
                    console.log(o._page);
                    location.href = location.href;
                }
            }
            o._delMsg = [];//清空删除队列
            o._getMsg(o);

        },function(d){
            if(d.code == '-4301'){
                tips('请至少选择一条消息!');
            }
        });
    },
};
var msg2 = new msg2();


$(document).ready(function () {
    personalCenter_sidebar('msg');
    (function () {
        function editStatus() {
            (function head() {
                function delgroupHtml() {
                    var htmlstr = '';
                    htmlstr += '<div class="deleteGroup">';
                    htmlstr += '<div class="left">';
                    htmlstr += '<input type="checkbox" class="checkAll">';
                    htmlstr += '<span>全选</span>';
                    htmlstr += '</div>';
                    htmlstr += '<div class="right">';
                    htmlstr += '<button id="ensureDel" class="btn">删除</button>';
                    htmlstr += '<button id="cancelDel" class="btn">取消</button>';
                    htmlstr += '</div>';
                    htmlstr += '<div class="clear"></div>';
                    htmlstr += '</div>';

                    return htmlstr;
                }

                $("#delMsgBtn").remove();
                var head = $("#msgCon .head");
                head.html(delgroupHtml());

            }());
            (function body() {
                var msg = $("#msgCon .msgOne");
                msg.addClass('onedit').find('.face').before('<input class="check" type="checkbox" />');
            }());

            var cancel = $("#cancelDel");
            var check = $("#msgCon .head").find('.checkAll');
            var ensure = $("#ensureDel");

            cancel.bind('click', normalStatus);
            check.bind('click', function () {
                var isCheck = $(this).is(":checked");
                var checkBox = $('#msgCon .msgBody .msgOne .check');

                checkBox.prop('checked', isCheck);
            });
            ensure.bind('click', function () {
                var checkBox = $('#msgCon .msgBody .msgOne .check');
                var msg = [];
                checkBox.each(function () {
                    var isCheck = $(this).is(':checked');
                    if (isCheck) {
                        msg2._delMsg.push($(this).parent().attr('msgId'));
                        console.log(msg2._delMsg);
                        msg.push($(this).parent());//$(this).parent().attr('data-msgid');
                        //下面是删除成功的操作，应该放到ajax 里面
                        $(this).parent().remove();
                    }
                });
                msg2._delReq(msg2);

                
                //刷新 or 重新请求消息列表重新绘制
                normalStatus();
            });
        };
        function normalStatus() {
            (function head() {
                $('#msgCon .head .deleteGroup').remove();
                var head = $("#msgCon .head");
                head.html('<button id="delMsgBtn" class="btn">编辑</button><div class="clear"></div>');
            }());
            (function body() {
                $('#msgCon .msgOne').removeClass('onedit');
                $('#msgCon .msgOne .check').remove();
            }());
            var delBtn = $('#delMsgBtn');
            delBtn.bind('click', editStatus);
        };
        normalStatus();
    }());
});


</script>
</html>
<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/16
 * Time: 上午10:40
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
    <title>直播管理</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <?php include '../module/mainStyle.php'; ?>
    <link href="../common/admin/pages/css/portfolio.css" rel="stylesheet" type="text/css"/>
    <link href="../common/admin/pages/css/userHead.css" rel="stylesheet" type="text/css"/>

</head>
<body class="page-header-fixed page-sidebar-closed-hide-logo page-sidebar-fixed">
<?php include '../module/head.php'; ?>
<div class="clearfix"></div>
<div class="page-container live-vertify-page">
    <?php include '../module/sidebar.php'; ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="portlet box green vertify-portlet">
                <div class="portlet-title">
                    <div class="caption">头像审核</div>
                    <div class="inputs">
                        <div class="portlet-input input-inline input-small">
                            <div class="input-icon right">
                                <i class="icon-magnifier"></i>
                                <input type="text" class="form-control" placeholder="search...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" portlet-body col-md-12" >
                    <div class="tabbable" id="headCheckBody">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#head-wait" id="" data-toggle="tab" aria-expanded="true" @click="changeView(0)">待审核</a></li>
                            <li><a href="#head-pass" id="" data-toggle="tab" aria-expanded="false" data-page="pass" @click="changeView(1)">已通过 </a></li>
                            <li><a href="#head-unpass" id=""data-toggle="tab" aria-expanded="false" data-page="unpass" @click="changeView(2)">未通过</a></li>
                        </ul>
                        <div class="tab-content no-space" :is="currentView">
                            <check></check>
                            <pass></pass>
                            <unpass></unpass>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<textarea id="jsTempplate-passTable" style="display: none;">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th class="checkbox-th"></th>
            <th>头像</th>
            <th>昵称</th>
            <th>uid</th>
            <th>提交时间</th>
            <th>状态</th>
        </tr>
        </thead>
        <tbody>
        <%for(var i in dataList){ var data=dataList[i]%>
        <tr data-uid="<%data[i].uid%>">
            <td>
                <div class="checker">
                    <span>
                        <input type="checkbox" class="checkboxes" value="1"/>
                    </span>
                </div>
            </td>
            <td><img height="50" width="50" src="<%=data.head%>"></td>
            <td><%=data.nick%></td>
            <td><%=data.uid%></td>
            <td><%=data.ctime%></td>
            <td><%=data.status%></td>

        </tr>
        <%}%>
        </tbody>
    </table>
    <nav>
        <div class="btn btn-xs green">全选</div>
        <div class="btn btn-xs green">反选</div>
        <div class="btn green pull-right">重新审核</div>
    </nav>
</textarea>
<?php include '../module/footer.php'; ?>
<?php include '../module/mainScript.php'; ?>
<script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
<script type="text/javascript" src="../common/global/plugins/swfobject.js"></script>
<script type="text/javascript" src="../common/global/scripts/common.js"></script>
<script type="text/javascript" src="../common/admin/pages/scripts/userHead.js"></script>
<script>
	var heartBeatType = 'userPic';
	heartBeat(heartBeatType);
	setInterval("heartBeat(heartBeatType)", 60000); //一分钟发送一次心跳，持续绑定审核项目
	function heartBeat(heartBeatType)
	{
		$.ajax({
			url: $conf.api + 'heartBeat.php',
			type: 'post',
			dataType: 'json',
			data: {
				uid : getCookie('admin_uid'),
				heartBeatType : heartBeatType,
			},
			success: function (data) {
				if (data.stat == 1) {
					$("#test").append(data.time);
				} else {
					cleanVideoInfo();
					videoNumber();
				}
			}
		});
	}
</script>
<script>
    var dataList = [];
    $(document).ready(function () {
		return;
        var userHeadCheckBox = Vue.extend({
            template: "#UserHeadCheckBox",
            props: {
                types: {
                    type: Number,
                    default: 1
                },
                title: {
                    type: String,
                    default: '通过'
                },
                checkList:{
                    type:[Array, Object],
                    twoWay:true,
                    default:function () {
                        return {};
                    }
                }
            },
            methods: {
                checkHead: function (index) {
                    if (this.checkList[index]) {
                        var stat = this.checkList[index].stat;
                        var imgUrl = this.checkList[index].imgUrl;
                        var uid = this.checkList[index].uid;
                        this.checkList.$set(index, {uid:uid,stat:!stat,imgUrl:imgUrl});
                    }
                }
            }
        });

        Vue.component('headCheckBox',{
            template:"#headCheckBox",
            data:function () {
                return {
                    checkList:dataList
                }
            },
            methods: {
                exchange: function () {
                    console.log(this.checkList);
                    var list = this.checkList;
                    for (var index in list) {
                        var stat = this.checkList[index].stat;
                        var imgUrl = this.checkList[index].imgUrl;
                        var uid = this.checkList[index].uid;
                        this.checkList.$set(index, {uid:uid,stat: !stat, imgUrl: imgUrl});
                    }
                },
                submit: function () {
                    var succList = [];
                    var failedList = [];
                    for (var i in this.checkList) {
                        if (this.checkList[i].stat) {
                            succList.push(this.checkList[i].uid);
                        } else {
                            failedList.push(this.checkList[i].uid);
                        }
                    }
                    console.log(succList);
                    console.log(failedList);
                    var self = this;
                    var requestData = {
                        uid:getCookie('admin_uid'),
                        encpass:getCookie('admin_enc'),
                        succuid:succList.join(),
                        failuid:failedList.join()
                    }
                    //set pass and request new list
					ajaxRequest({url:$conf.user.api + 'setPicPass.php',data:requestData}, function () {
                        self.checkList.$remove();
                        getHeadInfo(function(d){
                            for(var i in d){
                                self.checkList.push(d[i]);
                            }
                        });
                    });
                }
            },
            components:{
                'userheadcheckbox':userHeadCheckBox
            }
        });

        function getHeadInfo(doCallBack) {
            var url = $conf.user.api + 'getWaitPassList.php';
            var data = {uid:getCookie('admin_uid'), encpass:getCookie('admin_enc')}
            ajaxRequest({url:url,data:data}, function (d) {
                var list = d.data;
                var arr = [];
                for(var i in list){
                    var data = list[i];
                    arr.push({
                        uid:data.uid,
                        stat:1,
                        imgUrl:data.pic
                    })
                }
                typeof doCallBack =='function' && doCallBack(arr);
            })
        }

        //first init page
        getHeadInfo(function (d) {
            for(var i in d){
                dataList.push(d[i]);
            }

            new Vue({
                el:"#userCheckBox"
            });
        })

//        $.ajax({
//            url:'http://dev.huanpeng.com/admin2/api/user/getWaitPassList.php',
//            type:'post',
//            dataType:'json',
//            data:{
//                uid:getCookie('admin_uid'),
//                encpass:getCookie('admin_enc')
//            },
//            success:function (d) {
//                if(d.stat == 1){
//                    var list = d.resuData.data;
//                    for(var i in list){
//                        var data = list[i];
//                        dataList.push({
//                            uid:data.uid,
//                            stat:1,
//                            imgUrl:data.pic
//                        });
//                    }
//                }
//                new Vue({
//                    el:"#userCheckBox"
//                })
//            }
//        });
    });


</script>
<script>


//function userHeadCheck(){
//    this.optionWidth = 0;
//    this.optionHeight = 0;
//    this.checkOption = 'checked';
//    this.outStyle = 'top';
//    this.moduleSelect = 'passed-module';
////    this.waitList = [];
////    this.successList = [];
//    this.checkList=false;
//    this.cssConf = {};
//
//    this.passModalList = ['un-passed-module', 'passed-module'];
//    this.numSelectList = ['col-md-6','col-md-3','col-md-2'];
//
//    this.optionDom = $('.user-head-option-panel');
//    this.baseDom = $('.user-head-one');
//    this.baseDoms = {
//        base:this.baseDom,
//        pending:$('.user-head-one.pending'),
//        optitonGroup:this.baseDom.find('.pass-option-group'),
//        box:this.baseDom.find('.pass-option-box'),
//        faceBox:this.baseDom.find('.user-head')
//    };
//    this.optionDoms = {
//        failedModule:this.optionDom.find('.failed-select button'),
//        succModule:this.optionDom.find('.success-select button'),
//        numModule:this.optionDom.find('#show-num .option-item-one button'),
//        submitBtn:this.optionDom.find('.form-actions .submit')
//    };
//
//    this.checkCode = {
//        'passed-module':1,
//        'un-passed-module':-1,
//        'unset':0
//    };
//
//    this.initOptionPanel();
//    this.getUserHeadList();
//
////    this._bindHandler('initOptionPanel', 'getUserHeadList');
//}
//
//userHeadCheck.prototype = {
//    init:function(){
//        this.baseDom = $('.user-head-one');
//        this.baseDoms = {
//            base:this.baseDom,
//            pending:$('.user-head-one.pending'),
//            optitonGroup:this.baseDom.find('.pass-option-group'),
//            box:this.baseDom.find('.pass-option-box'),
//            faceBox:this.baseDom.find('.user-head')
//        };
//        this.setOptionGroupSize();
//        this.initSelectModule();
//        var self = this;
//        this.baseDoms.base.bind({
//            resize:function(){
//                self.setOptionGroupSize();
//            },
//            mouseleave:function(e){
//                var style = 'bottom'
//                var positionX = e.clientX - $(this).offset().left;
//                var positionY = e.clientY + $(document).scrollTop() - $(this).offset().top;
//                var width = $(this).width();
//                var height = $(this).height();
//                if(positionX <= 0){//left
//                    style="left"
//                }else if(positionX >=width){//right
//                    style='right';
//                }else if(positionY <=0){//top
//                    style="top";
//                }else if(positionY >= height){//bottom
//                    style="bottom"
//                }
//                self.outStyle = style;
//
//                var isPending = $(this).hasClass('pending');
//                var css = isPending ? self.cssConf[style]:{left:0,top:0};
//                $(this).find('.pass-option-box').animate(css,200,function(){
//                    isPending && $(this).css(self.cssConf['bottom']).hide();
//                    self.outStyle = 'top';
//                });
//            },
//            mouseenter:function(e){
//                var enterStyle = {
//                    "top":"bottom",
//                    "bottom":"top",
//                    "left":"right",
//                    "right":"left"
//                };
//
//                var style = enterStyle[self.outStyle];
//                var css = self.cssConf[style];
//                if($(this).hasClass('pending')) {
//                    $(this).find('.pass-option-box').css(css).show().animate({left: 0, top: 0}, 200);
//                }
//            }
//        });
//
//        this.baseDoms.box.bind('click', function(){
//            var parentsDom = $(this).parents('.user-head-one');
//            var isPending = parentsDom.hasClass('pending');
//            if(isPending){
//                parentsDom.removeClass('pending').addClass(self.checkOption);
//                self._setCheckList(parentsDom.data('uid'), self.moduleSelect);
//            }else{
//                parentsDom.removeClass(self.checkOption).addClass('pending');
//                self.initSelectModule();
//                self._setCheckList(parentsDom.data('uid'), 'unset');
//            }
//            //check list handle
//        });
//    },
//    setOptionGroupSize:function(){
//        this.optionWidth = this.baseDoms.faceBox.width();
//        this.optionHeight = this.baseDoms.faceBox.height();
//        var css = {
//            width:this.optionWidth,
//            height:this.optionHeight
//        }
//        this.baseDoms.optitonGroup.css(css);
//        this.baseDoms.box.css(css);
//
//        this.cssConf = {
//            right:{
//                left:this.optionWidth+"px",
//                top:0
//            },
//            left:{
//                left:-this.optionWidth+'px',
//                top:0
//            },
//            top:{
//                left:0,
//                top:-this.optionHeight+"px"
//            },
//            bottom:{
//                left:0,
//                top:this.optionHeight+'px'
//            }
//        }
//    },
//    initChangeRow:function(num){
//        var row = 12/num;
//        var col = 'col-md-' + row;
//        this.baseDoms.base.removeClass(this.numSelectList.join(' ')).addClass(col);
//        this.setOptionGroupSize();
//    },
//    initSelectModule:function(module){
//        if(module){
//            $('.user-head-one.pending').removeClass(this.passModalList.join(' ')).addClass(module);
//        }else{
//            $('.user-head-one.pending').removeClass(this.passModalList.join(' ')).addClass(this.moduleSelect);
//        }
//    },
//    initOptionPanel:function(){
//        var self = this;
//        var dom = this.optionDoms;
//        dom.failedModule.bind('click', function(){
//            dom.failedModule.removeClass('active');
//            dom.succModule.removeClass('active');
//            $(this).addClass('active');
//            self.moduleSelect = self.passModalList[0];
//            self.initSelectModule();
//        });
//        dom.succModule.bind('click', function(){
//            dom.failedModule.removeClass('active');
//            dom.succModule.removeClass('active');
//            $(this).addClass('active');
//            self.moduleSelect = self.passModalList[1];
//            self.initSelectModule();
//        });
//        dom.numModule.bind('click', function(){
//            dom.numModule.removeClass('active');
//            $(this).addClass('active');
//            self.initChangeRow($(this).text());
//        });
//        dom.submitBtn.bind('click', function(){
//            if(!self.checkList){
//                return;
//            }
//            var module = !self.passModalList.indexOf(self.moduleSelect);
//
//            module = self.passModalList[Number(module)];
//            self.initSelectModule(module);
//            $('.user-head-one.pending').removeClass('pending').addClass(self.checkOption);
//            self.baseDoms.box.css({left:0,top:0,display:'block'});
//            self._setUnsetCheckList(self.checkCode[module]);
//            var suids = [];
//            var fuids = [];
//            for(var i in self.checkList){
//                if(self.checkList[i] == 1){
//                    suids.push(i);
//                }else if(self.checkList[i] == -1){
//                    fuids.push(i);
//                }
//            }
//            self.requestCheckPic(suids, fuids, function(d){
//                 if(d.stat == 1){
//                     $('.user-head-table').html('');
//                     self.getUserHeadList();
//                 }
//            });
//        });
//    },
//    getUserHeadList:function(){
//        var self = this;
//        $.ajax({
//            url:'http://dev.huanpeng.com/admin2/api/user/getWaitPassList.php',
//            type:'post',
//            dataType:'json',
//            data:{
//                uid:getCookie('admin_uid'),
//                encpass:getCookie('admin_enc')
//            },
//            success:function(d){
//                self._clearList();
//                if(d.stat==1){
//                    var waitList =[];
//                    var html = '';
//                    var list = d.resuData.data;
//                    for(var i in list){
//                        var data = list[i];
//                        html += '<div class="user-head-one col-md-3 pending" data-uid="'+data.uid+'"> <img class="user-head" src="'+data.pic+'" alt=""/> <div class="pass-option-group"> <div class="pass-option-box" style="display:none"><div class="option-label"></div></div> </div> </div>';
//                        waitList.push(data.uid);
//                    }
//                    $('.user-head-table').html(html);
//                    self._initcheckList(waitList);
//                    self.init();
//                }
//            }
//        });
//    },
//    requestCheckPic:function(suids, fuids, callBack){
//        var self = this;
//        $.ajax({
//            url:'http://dev.huanpeng.com/admin2/api/user/setPicPass.php',
//            type:'post',
//            dataType:'json',
//            data:{
//                uid:getCookie('admin_uid'),
//                encpass:getCookie('admin_enc'),
//                succluid:suids.join(),
//                failluid:fuids.join()
//            },
//            success:function(d){
//                typeof callBack=='function' && callBack(d);
//            }
//        });
//    },
//    _initcheckList:function(d){
//        for(var i in d){
//            this.checkList[d[i]] = 0;
//        }
//    },
//    _setCheckList:function(uid, module){
//        [-1,0,1].indexOf(this.checkList[uid]) > -1 && (this.checkList[uid] = this.checkCode[module]);
//
//    },
//    _setUnsetCheckList:function(module){
//        module = Number(module);
//        for(var i in this.checkList){
//            if(!this.checkList[i]){
//                this.checkList[i] = module;
//            }
//        }
//    },
//
//    _clearList:function(){
//        this.checkList = {};
//    },
//    _bindHandler:function(){
//        var args = [].slice.call(arguments, 0);
//        for(var i in args){
//            this[args[i]] = this[args[i]].bind(this);
//        }
//    }
//};
//
//
//
//function userHeadList(){
//    this.reCheckUrl = '';
//    this.uHeadHtmlFn = huanpeng.template('sTempplate-passTable');
//}
//userHeadList.prototype = {
//
//    init:function(type){
//        this.userList = false;
//        this.type=type;
//        this.url = '';
//        this.dom = this.type == 'pass' ? $("#user-pass") : $('#user-unpass');
//
//        this.requestList();
//    },
//    requestList:function(){
//        var url = this.url;
//        var self = this;
//        $.ajax({
//            url:url,
//            type:'post',
//            dataType:'json',
//            data:{
//                uid:getCookie('admin_uid'),
//                encpass:getCookie('admin_enc')
//            },
//            success:function(d){
//                self.uHeadHtmlFn({dataList:dataList});
//                self.initEvent();
//            }
//        });
//    },
//    initEvent:function(){
//        var self = this;
//        this.dom.find('tbody tr').bind('click', function(){
//            var uid = $(this).data('uid');
//            var span = $(this).find('.checker span')
//            if(span.hasClass('checked')){
//                self.userList[uid]=0;
//                span.removeClass('checked');
//            }else{
//                self.userList[uid]=1;
//                span.addClass('checked');
//            }
//        });
//
//        this.dom.find('.all-select').bind('click', function(){
//            self.dom.find('tbody tr td .checker span').addClass('checked');
//            for(var i in self.userList){
//                self.userList[i] = 1;
//            }
//        });
//
//        this.dom.find('.re-select').bind('click', function(){
//            self.dom.find('tbody tr td .checker span').each(function(){
//                if($(this).hasClass('checked')){
//                    var uid = $(this).parents('tr').data('uid');
//                    self.userList[uid]=0;
//                    $(this).removeClass('checked');
//                }else{
//                    var uid = $(this).parents('tr').data('uid');
//                    self.userList[uid]=1;
//                    $(this).addClass('checked');
//                }
//            });
//        });
//
//        this.dom.find('.re-check').bind('click', function(){
//            self.requestReCheck();
//        });
//
//        //翻页事件
//    },
//    requestReCheck:function(){
//        var data = [];
//        for(var i in this.userList){
//            if(this.userList[i])
//                data.push(i);
//        }
//        $.ajax({
//            url:this.reCheckUrl,
//            type:'post',
//            dataType:'json',
//            data:data.join(','),
//            success:function(d){
//                //self.init(self.type);
//            }
//        })
//    }
//};
//
//
//var headObj;
//var listObj
//$(document).ready(function(){
//    headObj = new userHeadCheck();
//    listObj =  new userHeadList();
//    $('.nav-tabs li a').bind('click',function(){
//        if($(this).data('page')){
//            listObj.init($(this).data('page'));
//        }else{
//            headObj.getUserHeadList();
//        }
//    });
//});
</script>
</body>
</html>
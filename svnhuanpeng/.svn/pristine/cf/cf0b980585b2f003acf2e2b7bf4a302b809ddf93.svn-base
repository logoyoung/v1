<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 16/11/28
 * Time: 17:28
 */
include '../module/checkLogin.php';

$db = new DBHelperi_admin();
$gameInfo = get_GameList($db);//var_dump($gameInfo);
$gameDefault = getAdminRecommendGame($db);//var_dump($gameDefault);
$optStr = '';
//var_dump($gameInfo['list']);
foreach ($gameInfo['list'] as $k=>$v) {
    //var_dump($v);
    $optStr .= '<option data-src="'.$v['poster'].'" value="' . $v['gameID'] . '">' . $v['gameName'] . '</option>';
    //$optStr .= '1s';
}

//var_dump($optStr);
//echo '<script> var options = \''.$optStr.'\';</script>'
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
        .col-md-12 .portlet-body .row select{
            width: 90px;
        }
        .thumbnail .caption{
            text-align: center;
        }
    </style>

    <link href="../common/admin/pages/css/vertifyRealName.css" rel="stylesheet" type="text/css"/>
    <?php include '../module/mainStyle.php'; ?>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content page-style-square content_body page-sidebar-fixed">
<?php
    echo '<script> var options = \''.$optStr.'\';</script>';
    echo '<script> var gameDefault = '.json_encode($gameDefault).'; </script>';
?>
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
                                <h4 class="page-title">游戏分类推荐</h4>
                            </div>
                            <div class="tools">
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="tabbable">


                                <!--------news-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet light portlet-fit bordered">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class=" icon-layers "></i>
                                                    <span class="caption-subject  bold uppercase">首页游戏分类推荐</span>
                                                </div>
                                                <div class="tools">
                                                    <button type="button" class="btn btn-edit bg-gray-gold" id="gtedit">编辑</button>&nbsp;&nbsp;&nbsp;
                                                    <button type="button" class="btn btn-save bg-gray-gold" id="gtsave">保存</button>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="mt-element-card mt-element-overlay">

                                                    <div class="row gt-body">
                                                        <div class="col-sm-6 col-md-2 game-type">
                                                            <div class="thumbnail">
                                                                <img src="http://dev-img.huanpeng.com/2/9/29b25438eacbbdb580d5fcde9f85bf72.png" alt="...">
                                                                <div class="caption">
                                                                    <select >
                                                                        <?php echo $optStr; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-md-2 game-type">
                                                            <div class="thumbnail">
                                                                <img src="http://dev-img.huanpeng.com/2/9/29b25438eacbbdb580d5fcde9f85bf72.png" alt="...">
                                                                <div class="caption">
                                                                    <select >
                                                                        <?php echo $optStr; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-md-2 game-type">
                                                            <div class="thumbnail">
                                                                <img src="http://dev-img.huanpeng.com/2/9/29b25438eacbbdb580d5fcde9f85bf72.png" alt="...">
                                                                <div class="caption">
                                                                    <select>
                                                                        <?php echo $optStr; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-md-2 game-type">
                                                            <div class="thumbnail">
                                                                <img src="http://dev-img.huanpeng.com/2/9/29b25438eacbbdb580d5fcde9f85bf72.png" alt="...">
                                                                <div class="caption">
                                                                    <select>
                                                                        <?php echo $optStr; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-md-2 game-type">
                                                            <div class="thumbnail">
                                                                <img src="http://dev-img.huanpeng.com/2/9/29b25438eacbbdb580d5fcde9f85bf72.png" alt="...">
                                                                <div class="caption">
                                                                    <select>
                                                                        <?php echo $optStr; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--------news------>
                                <!--floor-->
                                                    <div class="row gf-body">
                                                        <div class="col-md-12">
                                                            <div class="portlet light portlet-fit bordered">
                                                                <div class="portlet-title">
                                                                    <div class="caption">
                                                                        <i class=" icon-layers "></i>
                                                                        <span class="caption-subject  bold uppercase">首页游戏推荐楼层</span>
                                                                    </div>
                                                                    <div class="tools">
                                                                        <button type="button" class="btn btn-edit bg-gray-gold" id="gfedit">编辑</button>&nbsp;&nbsp;&nbsp;
                                                                        <button type="button" class="btn btn-save bg-gray-gold" id="gfsave">保存</button>
                                                                    </div>
                                                                </div>
                                                                <div class="portlet-body">
                                                                    <div class="mt-element-card mt-element-overlay">

                                                                        <div class="row">
                                                                            <div class="col-xs-6 col-md-4 ">
                                                                                <label>楼层数量</label>
                                                                                <select id="floor-game-switch">
                                                                                    <option>1</option>
                                                                                    <option>2</option>
                                                                                    <option>3</option>
                                                                                    <option>4</option>
                                                                                    <option>5</option>
                                                                                    <option>6</option>
                                                                                </select>
                                                                            </div>
                                                                            <div id="floor-block" class="col-xs-6 col-md-6 ">
                                                                                <div class="game-floor">
                                                                                <div class="col-xs-6 col-md-3"><label>楼层1</label></div>
                                                                                <div class="col-xs-6 col-md-3 ">
                                                                                    <select >
                                                                                        <?php echo $optStr; ?>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-xs-6 col-md-3"><label>行数</label></div>
                                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                                            <option>1</option>
                                                                                            <option>2</option>
                                                                                            <option>3</option>
                                                                                        </select></label></div>
                                                                                </div>
                                                                                <div class="game-floor">
                                                                                    <div class="col-xs-6 col-md-3"><label>楼层2</label></div>
                                                                                    <div class="col-xs-6 col-md-3 ">
                                                                                        <select >
                                                                                            <?php echo $optStr; ?>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="col-xs-6 col-md-3"><label>行数</label></div>
                                                                                    <div class="col-xs-6 col-md-3"><label><select>
                                                                                                <option>1</option>
                                                                                                <option>2</option>
                                                                                                <option>3</option>
                                                                                            </select></label></div>
                                                                                </div>
                                                                                <div class="game-floor">
                                                                                    <div class="col-xs-6 col-md-3"><label>楼层3</label></div>
                                                                                    <div class="col-xs-6 col-md-3 ">
                                                                                        <select >
                                                                                            <?php echo $optStr; ?>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="col-xs-6 col-md-3"><label>行数</label></div>
                                                                                    <div class="col-xs-6 col-md-3"><label><select>
                                                                                                <option>1</option>
                                                                                                <option>2</option>
                                                                                                <option>3</option>
                                                                                            </select></label></div>
                                                                                </div>
                                                                                <div class="game-floor">
                                                                                    <div class="col-xs-6 col-md-3"><label>楼层4</label></div>
                                                                                    <div class="col-xs-6 col-md-3 ">
                                                                                        <select >
                                                                                            <?php echo $optStr; ?>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="col-xs-6 col-md-3"><label>行数</label></div>
                                                                                    <div class="col-xs-6 col-md-3"><label><select>
                                                                                                <option>1</option>
                                                                                                <option>2</option>
                                                                                                <option>3</option>
                                                                                            </select></label></div>
                                                                                </div>
                                                                                <div class="game-floor">
                                                                                    <div class="col-xs-6 col-md-3"><label>楼层5</label></div>
                                                                                    <div class="col-xs-6 col-md-3 ">
                                                                                        <select >
                                                                                            <?php echo $optStr; ?>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="col-xs-6 col-md-3"><label>行数</label></div>
                                                                                    <div class="col-xs-6 col-md-3"><label><select>
                                                                                                <option>1</option>
                                                                                                <option>2</option>
                                                                                                <option>3</option>
                                                                                            </select></label></div>
                                                                                </div>
                                                                                <div class="game-floor">
                                                                                    <div class="col-xs-6 col-md-3"><label>楼层6</label></div>
                                                                                    <div class="col-xs-6 col-md-3 ">
                                                                                        <select >
                                                                                            <?php echo $optStr; ?>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="col-xs-6 col-md-3"><label>行数</label></div>
                                                                                    <div class="col-xs-6 col-md-3"><label><select>
                                                                                                <option>1</option>
                                                                                                <option>2</option>
                                                                                                <option>3</option>
                                                                                            </select></label></div>
                                                                                </div>


                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                <!--/floor-->
                                <!--typerec-->
                                <div class="row gn-body">
                                    <div class="col-md-12">
                                        <div class="portlet light portlet-fit bordered">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class=" icon-layers "></i>
                                                    <span class="caption-subject  bold uppercase">导航栏游戏分类推荐</span>
                                                </div>
                                                <div class="tools">
                                                    <button type="button" class="btn btn-edit bg-gray-gold" id="gnedit">编辑</button>&nbsp;&nbsp;&nbsp;
                                                    <button type="button" class="btn btn-save bg-gray-gold" id="gnsave">保存</button>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="mt-element-card mt-element-overlay">

                                                    <div class="row">
                                                        <div class="col-xs-6 col-md-4 ">
                                                            <label>推荐数量</label>
                                                            <select id="nav-game-switch">
                                                                <option value="1">3</option>
                                                                <option value="2">6</option>
                                                                <option value="3">9</option>
                                                                <option value="4">12</option>
                                                                <option value="5">15</option>
                                                                <option value="6">18</option>
                                                            </select>
                                                        </div>
                                                        <div id="nav-block" class="col-xs-6 col-md-6 ">
                                                            <div class="game-nav">
                                                                <div class="col-xs-6 col-md-3"><label>游戏推荐</label></div>
                                                                <div class="col-xs-6 col-md-3 ">
                                                                    <select >
                                                                        <?php echo $optStr; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                            <?php echo $optStr; ?>
                                                                        </select></label></div>
                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                            <?php echo $optStr; ?>
                                                                        </select></label></div>
                                                            </div>
                                                            <div class="game-nav">
                                                                <div class="col-xs-6 col-md-3"></div>
                                                                <div class="col-xs-6 col-md-3 ">
                                                                    <select >
                                                                        <?php echo $optStr; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                            <?php echo $optStr; ?>
                                                                        </select></label></div>
                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                            <?php echo $optStr; ?>
                                                                        </select></label></div>
                                                            </div>
                                                            <div class="game-nav">
                                                                <div class="col-xs-6 col-md-3"></div>
                                                                <div class="col-xs-6 col-md-3 ">
                                                                    <select >
                                                                        <?php echo $optStr; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                            <?php echo $optStr; ?>
                                                                        </select></label></div>
                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                            <?php echo $optStr; ?>
                                                                        </select></label></div>
                                                            </div>
                                                            <div class="game-nav">
                                                                <div class="col-xs-6 col-md-3"></div>
                                                                <div class="col-xs-6 col-md-3 ">
                                                                    <select >
                                                                        <?php echo $optStr; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                            <?php echo $optStr; ?>
                                                                        </select></label></div>
                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                            <?php echo $optStr; ?>
                                                                        </select></label></div>
                                                            </div>
                                                            <div class="game-nav">
                                                                <div class="col-xs-6 col-md-3"></div>
                                                                <div class="col-xs-6 col-md-3 ">
                                                                    <select >
                                                                        <?php echo $optStr; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                            <?php echo $optStr; ?>
                                                                        </select></label></div>
                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                            <?php echo $optStr; ?>
                                                                        </select></label></div>
                                                            </div>
                                                            <div class="game-nav">
                                                                <div class="col-xs-6 col-md-3"></div>
                                                                <div class="col-xs-6 col-md-3 ">
                                                                    <select >
                                                                        <?php echo $optStr; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                            <?php echo $optStr; ?>
                                                                        </select></label></div>
                                                                <div class="col-xs-6 col-md-3"><label><select>
                                                                            <?php echo $optStr; ?>
                                                                        </select></label></div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--/typerec-->
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
<script type="text/javascript">
//game model
    (function () {
        var GM = {
            //游戏推荐模型

            GAME:[],
            //分类游戏推荐
            TGAME:{
                size:5,
                data:{},
            },
            //楼层游戏推荐
            FGAME:{
                fsize:9,//楼层最大数
                csize:4,//当前楼层
                fline:2,//每个楼层行数
                data:{}
            },
            //导航栏游戏推荐
            NGAME:{
                nsize:12,
                csize:9,
                data:{}
            }
        }
        //选择器
        var myswitch = {
            tgs:'',
            fgs:'#floor-game-switch',
            ngs:'#nav-game-switch',
            gtype:'.game-type',
            gfloor:'.game-floor',
            gnav:'.game-nav'
        };
        //添加选项条目

        //创建模版
        var GV = {
            view_createTGame:function(size){

            },
            view_createFGame:function(size,selector){
                var obj = $(selector).parent().parent().find('#floor-block');
                var csize = obj.find('select').length/2;
                var str = '';
                for(var i=0;i<size;i++)
                    str += '<div class="game-floor"><div class="col-xs-6 col-md-3"><label>楼层'+(csize+i+1)+'</label></div>'
                        +'<div class="col-xs-6 col-md-3 "><label>'
                        +'<select >'+ options+' </select></label>'
                        +'</div> <div class="col-xs-6 col-md-3"><label>行数</label></div>'
                        +'<div class="col-xs-6 col-md-3"><label>'
                        +'<select> <option>1</option><option>2</option><option>3</option> </select></label></div></div>';
                obj.append(str);
            },
            view_createNGame:function(size,selector){
                var obj = $(selector).parent().parent().find('#nav-block');
                var str = '';
                for(var i=0;i<size;i++)
                    str += '<div class="game-nav"><div class="col-xs-6 col-md-3"></div>'
                        +'<div class="col-xs-6 col-md-3 "><label>'
                        +'<select > '+options+' </select></label>'
                        +'</div> <div class="col-xs-6 col-md-3"><label><select > '+options+' </select><label></div>'
                        +'<div class="col-xs-6 col-md-3"><label>'
                        +'<select> '+options+' </select></label></div></div>';
                obj.append(str);
            },
            view_removeTGame:function(size){},
            view_removeFGame:function(size){},
            view_removeNGame:function(size){}
        };
        //控制对象
        var GC = function () {
            var control_add = function (size,selector,opt) {console.log('add:'+size);
                if(selector==myswitch['fgs'])
                    GV.view_createFGame(size,selector,opt);
                else if(selector==myswitch['ngs'])
                    GV.view_createNGame(size,selector,opt);
            }
            //删除选项条目
            var control_remove = function (size,selector,opt) {//console.log(size);
                var $obj = $(selector).parent().parent();
                var len = parseInt($obj.find(opt).length)-size-1;
                $obj.find(opt+':gt('+len+')').remove();
            }
            //注册下拉框事件
            var control_selectReg = function (selector,opt,$event,addcallback,removecallback) {
                $(selector).bind($event,function () {
                    var fsize = $(selector).val();
                    var csize = $(selector).parent().parent().find(opt).length;
                    var e = null;
                    if(fsize>csize)
                        (e = addcallback).call(e,fsize-csize,selector,opt);
                    else if(fsize<csize)
                        (e = removecallback).call(e,csize-fsize,selector,opt);
                })
            }
            //编辑按钮注册事件
            var control_editorBtn = function () {
                var kv = {
                    gtedit:'.gt-body',
                    gfedit:'.gf-body',
                    gnedit:'.gn-body'
                };
                $('.btn-edit').bind('click',function () {
                    var id = $(this).attr('id');
                    if($(this).text()=='编辑') {
                        $(this).css('background', '#ddd').text('可编辑');
                        $(kv[id]).find('select').removeAttr('disabled','');
                    }
                    else {
                        $(this).css('background', '#c1c1c1').text('编辑');
                        $(kv[id]).find('select').attr('disabled','');
                    }
                })
            }
            var config = {
                //默认不可选
                disable:false,
                //楼层数量
                floorcount:4,
                //推荐行数
                navcount:4,
                api:$conf.api+'game/recommentGame.php',
            }
            //设置默认推荐
            var control_default = function () {
                //$(myswitch['fgs']).val(4);
                //$(myswitch['ngs']).val(4);
                //5各推荐游戏配置
                var gtselect = $('.gt-body').find('select');
                //var imgArr = $('.gt-body').find('img');
                for(var i=0;i<gameDefault['rlist'].length;i++) {
                    gtselect[i].value = gameDefault['rlist'][i].gameID;
                    $(gtselect[i]).parent().parent().find('img')[0].src = gameDefault['rlist'][i].poster;
                }
                $('.gt-body').find('select').bind('change',function(){
                    var imgsrc = $(this).find('option:selected')[0].getAttribute('data-src');console.log(imgsrc);
                    $(this).parent().parent().find('img')[0].src = imgsrc;
                })
                //楼层配置
                var gfselect = $('#floor-block').find('select');
                $(myswitch['fgs']).val(gameDefault['flist'].length);
                $(myswitch['fgs']).trigger('change');
                for(var i=0;i<gameDefault['flist'].length;i++){
                   var k = 2*i;
                    //console.log(i);
                    //console.log(gfselect[2*i]);
                    gfselect[2*i].value = gameDefault['flist'][i].gameID;
                    gfselect[2*i+1].value = gameDefault['flist'][i].number;
                }
                //导航栏游戏推荐
                var gnselect = $('#nav-block').find('select');
                $(myswitch['ngs']).val(gameDefault['nlist'].length/3);
                $(myswitch['ngs']).trigger('change');
                for(var i=0;i<gameDefault['nlist'].length;i++){
                    gnselect[i].value = gameDefault['nlist'][i].gameID
                }


            }
            var control_getTGame = function () {
                //todo
                var tg = [];
                var gtbody = $('.gt-body select');
                for(var i=0; i<gtbody.length;i++)
                    tg.push(gtbody[i].value)
                return tg.join(',');
            }
            var control_getFGame = function () {
                //todo
                var fg = [];
                var fl = [];
                var gfbody = $('#floor-block select');
                for(var i=0;i<gfbody.length;i++){
                    if(i%2==0)
                        fg.push(gfbody[i].value);
                    else
                        fl.push(gfbody[i].value);
                }
                return {
                    games:function () {
                        return fg.join(',');
                    },
                    lines:function () {
                        return fl.join(',');
                    }
                }
            }
            var control_getNGame = function () {
                //todo
                var ng = [];
                var gnbody = $('#nav-block select');
                for(var i=0; i<gnbody.length;i++)
                    ng.push(gnbody[i].value)
                return ng.join(',');
            }
            var control_ajax = function (api,data,callback) {
                data['uid'] = getCookie('admin_uid');
                data['encpass'] = getCookie('admin_enc');
                data['type'] = getCookie('admin_type');console.log(data);
                $.ajax({
                    url:api,
                    data:data,
                    type:'post',
                    dataType:'json',
                    success:callback
                });
            }
            var control_dialog = function (d) {
                if(d.stat==1)
                    alert('提交成功');
                else
                    alert('提交失败');
            }
            var control_save = function () {
                $('#gtsave').bind('click',function () {
                    var games = control_getTGame();
                    var data = {
                        recomType:2,
                        gameID:games,
                    };
                    control_ajax(config['api'],data,control_dialog);
                })
                $('#gfsave').bind('click',function () {
                    var gdata = control_getFGame();
                    var data = {
                        recomType:3,
                        gameID:gdata['games'],
                        number:gdata['lines']
                    }
                    control_ajax(config['api'],data,control_dialog);
                })
                $('#gnsave').bind('click',function () {
                    var games = control_getNGame();
                    var data = {
                        recomType:1,
                        gameID:games,
                    };
                    control_ajax(config['api'],data,control_dialog);
                })
            }
            var control_init = function () {//console.log(1)
                //默认不可选
                $('select').attr('disabled','');
                control_selectReg(myswitch['fgs'],myswitch['gfloor'],'change',control_add,control_remove);
                control_selectReg(myswitch['ngs'],myswitch['gnav'],'change',control_add,control_remove);
                control_editorBtn();
                control_default();
                control_save();
            }
            //control_init();
            //return
            return {
                init:control_init
            }
        }
        var CONTROL = GC();
        CONTROL.init();

    }())

</script>
</body>
</html>
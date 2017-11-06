<?php
/**
 * 账户余额
 * Author zwq
 * Date 2017年5月3日10:35:05
 */
include '../module/checkLogin.php';
include '../../core/common/Export.class.php';

require('../lib/BrokerageCompany.class.php');
$bcompany = new BrokerageCompany();
$company = $bcompany->getList();
$month = getPastMonth();
$datas[]=array('abean'=>1000,'acoin'=>2000,'ubean'=>3000,'ucoin'=>4000);
if($_REQUEST['export']==1){
    $excel[] = array('主播金币','主播金豆','用户欢朋币','用户欢朋豆');
    foreach ($datas as $data) {
         $excel[] = array($data['abean'],$data['acoin'],$data['ubean'],$data['ucoin']);
    }
    Export::outputCsv($excel,date('Y-m-d').'普通用户列表');

}
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8"/>
        <title>账户余额</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta content="" name="description"/>
        <meta content="" name="author"/>
        <style type="text/css">
        input, optgroup, select, textarea {
            color: #333;
        }
            .verticalAlign{ vertical-align:middle; display:inline-block; height:100%; margin-left:-1px;}
            .xcConfirm .xc_layer{position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: #666666; opacity: 0.5; z-index: 2147000000;}
            .xcConfirm .popBox{position: fixed; left: 50%; top: 50%; background-color: #ffffff; z-index: 2147000001; width: 570px; height: 300px; margin-left: -285px; margin-top: -150px; border-radius: 5px; font-weight: bold; color: #535e66;}
            .xcConfirm .popBox .ttBox{height: 30px; line-height: 30px; padding: 30px 30px; border-bottom: solid 1px #eef0f1;}
            .xcConfirm .popBox .ttBox .tt{font-size: 18px; display: block; float: left; height: 30px; position: relative;}
            .xcConfirm .popBox .ttBox .clsBtn{display: block; cursor: pointer; width: 12px; height: 12px; position: absolute; top: 22px; right: 30px; background: url(../common/global/img/icons.png) -48px -96px no-repeat;}
            .xcConfirm .popBox .txtBox{margin: 40px 100px; height: 100px; overflow: hidden;}
            .xcConfirm .popBox .txtBox .bigIcon{float: left; margin-right: 20px; width: 48px; height: 48px; background-image: url(../common/global/img/icons.png); background-repeat: no-repeat; background-position: 48px 0;}
            .xcConfirm .popBox .txtBox p{ height: 84px; margin-top: 16px; line-height: 26px; overflow-x: hidden; overflow-y: auto;}
            .xcConfirm .popBox .txtBox p input{width: 364px; height: 30px; border: solid 1px #eef0f1; font-size: 18px; margin-top: 6px;}
            .xcConfirm .popBox .btnArea{border-top: solid 1px #eef0f1;}
            .xcConfirm .popBox .btnGroup{float: right;}
            .xcConfirm .popBox .btnGroup .sgBtn{margin-top: 14px; margin-right: 10px;}
            .xcConfirm .popBox .sgBtn{display: block; cursor: pointer; float: left; width: 95px; height: 35px; line-height: 35px; text-align: center; color: #FFFFFF; border-radius: 5px;}
            .xcConfirm .popBox .sgBtn.ok{background-color: #FF7800; color: #FFFFFF;}
            .xcConfirm .popBox .sgBtn.cancel{background-color: #a0a0a0; color: #FFFFFF;}
            .table th, .table td {
                text-align: center;
                vertical-align: middle!important;
            }
        </style>
        <?php include '../module/mainStyle.php'; ?>
    </head>
    <body class="page-header-fixed page-quick-sidebar-over-content page-style-square content_body page-sidebar-fixed">
        <?php include '../module/head.php'; ?>
        <div class="clearfix"></div>
        <div class="page-container">
            <?php include '../module/sidebar.php'; ?>
            <div class="page-content-wrapper">
                <div class="page-content">
                    <!--<h3 class="page-title">游戏类型列表</h3>-->
                    <div class="row">
                    <form id='myform' method='post' class="col-md-12">
                        <div class="col-md-12">
                            <div class="portlet box bg-yellow-gold">
                                <input type='hidden' name='sidebar' id='sidebar' value=<?= $_REQUEST['sidebar']?> >
                                <div class="portlet-title">
                                    <div class="caption">
                                        <h4 class="page-title">账户余额</h4>
                                    </div>
                                    <div class="tools">
                                    <select name='month' id='month' style="color: #333" class="table-group-action-input input-inline input-small input-sm">
                                        <?php foreach($month as $k=>$v) { ?>
                                        <option value="<?=$v; ?>" <?php if($_REQUEST['month']==$v){?> selected <?php }?> ><?=$v; ?></option>
                                        <?php } ?>
                                    </select>
                                    <button type='submit' class="btn bg-yellow-gold" >搜索</button>
                                    <button type='submit' class="btn bg-yellow-gold" name="export" value="1" >导出</button>
                                    </div>
                                </div>
                        </form>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover line-height">
                                        <thead>
                                            <tr>
                                                <th width="25%">主播金币</th>
                                                <th width="25%">主播金豆</th>
                                                <th width="25%">用户欢朋币</th>
                                                <th width="25%">用户欢朋豆</th>
                                            </tr>
                                            <tr>
                                                <th style="cursor:pointer" class='golist' gourl='statisticsList.php?sidebar=0-2'><span style="color:#ff7800"><?=$datas[0]['abean'] ?></span></th>
                                                <th style="cursor:pointer" class='golist' gourl='golist2'><span style="color:#ff7800"><?=$datas[0]['acoin'] ?></span></th>
                                                <th style="cursor:pointer" class='golist' gourl='golist3'><span style="color:#ff7800"><?=$datas[0]['ubean'] ?></span></th>
                                                <th style="cursor:pointer" class='golist' gourl='golist4'><span style="color:#ff7800"><?=$datas[0]['ucoin'] ?></span></th>
                                            </tr>
                                        </thead>
                                        <tbody id="typebodys" class="line-height">
                                        </tbody>
                                    </table>
                                    <nav style="text-align: center" id="msg_text">
                                        <ul class="pagination" id="game_list"></ul>
                                    </nav>
                                </div>
                            </div>
                            <!-- END PORTLET-->
                        </div>
                    </div>
                </div>
                <?php include '../module/footer.php'; ?>
                <?php include '../module/mainScript.php'; ?>
                <script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
                <script type="text/javascript" src="../common/global/plugins/xcConfirm.js"></script>
                <script type="text/javascript" src="../common/global/scripts/common.js" ></script>
                </body>
                </html>
                
                
<script type="text/javascript">            
	$(function(){
		$('.golist').bind('click',function(){
			var url = $(this).attr('gourl');
			$('#myform').attr('action',url);
			$('#sidebar').val('0-2');
			$('#myform').submit();
		});
	});
</script>
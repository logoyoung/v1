<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/11
 * Time: 下午1:56
 */
exit;
include_once '../../../../include/init.php';
include WEBSITE_PERSON."isLogin.php";


$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$userCertifyStatus =  get_userCertifyStatus($_COOKIE['_uid'], $db);
?>

<!DOCTYPE html>
<html>
<head>
	<title>个人中心-欢朋直播-精彩手游直播平台！</title>
	<meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <?php include WEBSITE_TPL.'commSource.php';?>
    <link rel="stylesheet" type="text/css" href="<?php echo STATIC_CSS_PATH;?>person.css?v=1.0.5">
    <script type="text/javascript" src="<?php echo STATIC_JS_PATH;?>jquery.form.js"></script>
	<style>
		body{
			background-color: #eeeeee;
		}
		.content{
			min-height:820px;
		}

	</style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
<?php
$userCertifyStatus =  get_userCertifyStatus($_COOKIE['_uid'], $db);
?>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
	<div class="content">
		<div id="personal">
			<div class="basic">
				<?php include '../pdetail.html.php' ?>
				<div class="pblockdiv">
					<div class="pblock">
						<?php include '../titleLink.html.php' ?>

						<div class="list cur">
							<div class="form-horizontal mt-20">
								<div class="control-group">
									<div class="control-label">所属银行:</div>
									<div class="controls">
										<div class="select-bank text" data-bank="icbc" style="width: 136px; height: 18px;">
											<span class="bank_icon icbc left"></span>
											<p class="left bankName">工商银行</p>
											<span class="personal_icon arrow_bt right"></span>
											<div class="clear"></div>
										</div>
										<div class="select-bank-result" style="display: none">
											<ul>
												<li class="banckOne" data-bank="icbc">
													<span class="bank_icon icbc left"></span>
													<p class="left bankName">工商银行</p>
													<div class="clear"></div>
												</li>
											</ul>
										</div>
										<select id="bankSelect" class='w-160 text' style="display: none">
											<option value="icbc">工商银行</option>
											<option value="ccb">建设银行</option>
											<option value="abc">农业银行</option>
											<option value="bcm">交通银行</option>
											<option value="boc">中国银行</option>
											<option value="cmbc">民生银行</option>
											<option value="cmb">招商银行</option>
											<option value="psbc">中国邮政储蓄</option>
										</select>

									</div>
								</div>
								<div id="cardid" class="control-group">
									<div class="control-label">银行卡号:</div>
									<div class="controls">
										<input class="w-230" type="text" placeholder="请输入银行卡号" class="m-wrap small">

									</div>
								</div>
								<div id="cardid_cp" class="control-group">
									<div class="control-label">确认银行卡号:</div>
									<div class="controls">
										<input class="w-230" type="text" placeholder="请再次确认银行卡号" class="m-wrap small">
									</div>
								</div>
								<div class="control-group">
									<div class="control-label">银行卡正面照:</div>
									<div class="controls h-175">
										
										<div class="left photo">
                                                <span class="pic">
													<img src="../../../static/img/bankCard_front.png" alt=""/>
                                                </span>
											<label>银行卡背面照</label>
											<div class="clear"></div>
											<form action="" id="upload_front" name="upload_front" enctype="multipart/form-data" method="post">
												<div class="upload">上传
													<input name="file" type="file" id='front' accept="image/*" >
												</div>
											</form>
										</div>
										<div class="left photo">
                                                <span class="pic">
													<img src="../../../static/img/bankCard_back.png" alt=""/>
                                                </span>
											<label>银行卡正面照</label>
											<div class="clear"></div>
											<form action="" id="upload_back" name="upload_back" enctype="multipart/form-data" method="post">
												<div class="upload">上传
													<input name="file" type="file" id='back' accept="image/*" >
												</div>
											</form>
										</div>
									</div>
								</div>
								<div class="clear"></div>
								<div class="control-group">
									<div class="controls">
										<div class="stepTitle">第一步：检查你的银行卡是否符合以下要求</div>
										<div class="stepInfo">1.提供的事本人身份证办理的银行卡。</div>
										<div class="stepInfo">2.银行卡要开通网上银行或手机银行，未开通的青岛当地营业厅办理。</div>
									</div>
								</div>
								<div class="control-group">
									<div class="controls">
										<div class="stepTitle">第二步：馅转账再提交认证</div>
										<div class="stepInfo">请使用你填写的银行卡想欢朋官方指定的账户转账0.25元</div>
										<div class="stepInfo notice">＊请使用网上银行或手机银行转账，其他方式都无效。转账金额不予退回</div>
									</div>
								</div>
								<div class="control-group">
									<div class="controls transfer">
										<div class="transferTitle">欢朋官方指定账号</div>
										<div class="transferInfo">收款人：xxx</div>
										<div class="transferInfo">首款卡号：xxxx xxxx xxxx xxxx xxx</div>
										<div class="transferInfo">收款银行：工商银行首体南路支行</div>
									</div>
								</div>
								<div class="control-group">
									<div class="controls btn-controls">
										<div class="ensuretransfer">我确认转账，提交申请</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
</body>
<script src="<?php echo STATIC_JS_PATH; ?>personal.js?v=1.0.4"></script>
<script>

$('.pblock .title span.bankCert').addClass('cur');
	$(document).ready(function(){
        $conf = conf.getConf();
        inputPlaceholder();
		function err(text){
			var htmlstr = '<span class="errInfo"> <span class="err_text">'+text+'</span> </span>';// <span class="err_icon">x</span>

			return htmlstr;
		}

        $(document).bind('click', function(){
            $('.select-bank-result').css('display', 'none');
        });
		(function(){
			var selBank = $('.select-bank');
			var bankList = [
				'icbc',
				'ccb',
				'abc',
				'bcm',
				'boc',
				'cmbc',
				'cmb',
				'psbc'
			];
			var bankName = {
				'icbc':'工商银行',
				'ccb':'建设银行',
				'abc':'农业银行',
				'bcm':'交通银行',
				'boc':'中国银行',
				'cmbc':'民生银行',
				'cmb':'招商银行',
				'psbc':'中国邮政储蓄'
			};
			function in_array(item,arr){
				for(var i in arr){
					if(item == arr[i])
						return true;
				}
				return false;
			}
			function bankOneHtml(bank, flag){
				var htmlstr = '';
				htmlstr += '<li class="banckOne" data-bank="'+bank+'">';
				htmlstr += '<span class="bank_icon ' + bank + ' left"></span>';
				htmlstr += '<p class="left bankName">'+bankName[bank]+'</p>';
				htmlstr += flag ?  '<span class="personal_icon arrow_up right"></span>' : '';
				htmlstr += '<div class="clear"></div>';
				htmlstr += '</li>';
				return htmlstr;
			}
			function selectOneHtml(bank){
				var htmlstr = '';
				htmlstr += '<span class="bank_icon ' + bank + ' left"></span>';
				htmlstr += '<p class="left bankName">' + bankName[bank] + '</p>';
				htmlstr += '<span class="personal_icon arrow_bt right"></span>';
				htmlstr += '<div class="clear"></div>';

				return htmlstr;
			}
			selBank.bind('click',function(){
				console.log('click');
				var bank = $("#bankSelect").val();
				//if(!in_array(bank, bankList)) return false;
				var result_list = $('.select-bank-result');
				result_list.find('li').remove();
				result_list.css('display','block');
				var ul =  result_list.find('ul');
				ul.append(bankOneHtml(bank, true));

				for(var i in bankList)
					if(bankList[i] != bank)
						ul.append(bankOneHtml(bankList[i], false));

				(function(){
					var bLi = ul.find('li');
					bLi.bind('click',function(){
						var b = $(this).attr('data-bank');
						selBank.html(selectOneHtml(b));
						selBank.attr('data-bank',b);
						result_list.css('display', 'none');
						$('#bankSelect').val(b);
					});
				}());
                return false;
			});

		}());
		(function(a){
			a('.control-group .controls input[type=text]').blur(function(){
				var theVal = a.trim(a(this).val());
				if(!theVal)
					$(this).parent().append(err('内容不能为空'));
			});
			a('.control-group .controls input[type=text]').focus(function(){
				var err = a(this).parent().find('.errInfo');
				if(!err.get()[0])
					return;
				err.remove();
			});
			a('#cardid_cp .controls input[type=text]').blur(function(){
				var cardid = a.trim(a('#cardid input[type=text]').val());
				var cardid_cp = a.trim(a(this).val());

				if(cardid != cardid_cp)
					a(this).parent().append(err('两次输入的银行卡号不一致'));
			});

			a('#front').change( function(){
				upload_pic('front');
			});
			a("#back").change(function(){
				upload_pic('back');
			});

			a('.ensuretransfer').bind('click', function(){
				var bank = $("#bankSelect").val();
				var bcard =  a.trim(a('#cardid input[type=text]').val());
				var bcard_cp = a.trim(a('#cardid_cp input[type=text]').val());
				var front = a("#front").val();
				var back = a('#back').val();

				if(!bank) {
					a('#bankSelect').parent().append(err('类型错误'));
					return;
				}
				if(!bcard) {
					a('#cardid').append(err('内容不能为空'));
					return;
				}
				if(!bcard_cp){
					a('#cardid_cp').append(err('内容不能为空'));
					return;
				}
				if(bcard != bcard_cp){
					a('#cardid_cp').append(err('两次输入的银行卡号不一致'));
					return;
				}

				if(!front || !back){
					a("#upload_front").parent().parent().append(err('请上传证件照片'));
					return;
				}

				var option = {
					url:'../mp_ajax/certBank_ajax.php',
					type:'post',
					dataType:'json',
					data:{
						uid:getCookie('_uid'),
						encpass:getCookie('_enc'),
						bank:bank,
						cardid:bcard
					},
					success:function(d){
						if(d.isSuccess == 1){
							alert('ok');
							location.href = $conf.person + '/mp';
						}
					}
				}
				a.ajax(option);
			});

			function upload_pic(type){
				var form = null;

				if(type == 'front'){
					form = a('#upload_front');
				}

				if(type == 'back'){
					form = a('#upload_back');
				}

				if(!form)
					return;

                var fileUploadRes = checkUploadImage($('#'+type).get()[0], $conf.uploadImgSize);
                if(fileUploadRes < 0){
                    console.log(fileUploadRes);
                    return;
                }

				var option = {
					url:'../mp_ajax/upload_bankpic_ajax.php',
//					contentType:'multipart/form-data',
					type:'post',
					dataType:'json',
					data:{
						uid:getCookie('_uid'),
						encpass:getCookie('_enc'),
						type:type
					},
					uploadProgress:function(event, position, totla, percentComplete){
						console.log(percentComplete);
					},
					success:function(d){
						if(d.isSuccess == 1){
							form.parent().find('.pic').html('<img src="'+ d.img+'">');
						}
					}
				}

				form.ajaxSubmit(option);
			}
		}(jQuery));
	});

//	$(document).ready(function()    {
//		var options = {    //beforeSubmit:  showRequest,
//			contentType:"multipart/form-data",
//			dataType:"json",
//			type:"post",
//			beforeSend: function(){
//				var uname = $("#upload_csv_user").val();
//				if(uname==""){                    alert("请选择CSV文件并上传！");
//					$("#progress").hide();
//					return false;
//					exit();
//				}else{
//					$("#progress").show();                    //clear everything
//					$("#bar").width('0%');
//					$("#message").html("");
//					$("#percent").html("0%");
//				}
//			},
//			uploadProgress: function(event, position, total, percentComplete){
//				$("#bar").width(percentComplete+'%');
//				$("#percent").html(percentComplete+'%');            },
//			success: function(data)            {
//				if(data['err']==2){                    //alert('上传文件不能为空，请重新上传文件!');
//				}else{
//					alert('共导入数据'+data['total_persons']+'条,成功创建用户'+data['created_persons']+'条,认证成功用户'+data['authed_persons']+'条');
//					$("#bar").width('100%');
//					$("#percent").html('100%');
//				}
//			},
//			complete: function(response)
//			{                $("#message").html("<font color='green'>"+response.responseText+"</font>");
//			},            error: function()            {                $("#message").html("<font color='red'> ERROR: unable to upload files</font>");            }        };
//			$("#myForm").ajaxForm(options);    });



</script>
</html>
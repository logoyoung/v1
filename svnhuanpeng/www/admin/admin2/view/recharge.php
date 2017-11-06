<?php
/**
 * 充值
 * yandong@6rooms.com
 * date 2016-6-27 12:08
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
	<title>充值</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport"/>
	<meta content="" name="description"/>
	<meta content="" name="author"/>
	<?php include '../module/mainStyle.php'; ?>
</head>
<body class="page-header-fixed page-sidebar-closed-hide-logo page-sidebar-fixed">
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
								<h4 class="page-title">充值｜奖励下发</h4>
							</div>
							<div class="tools">
							</div>
						</div>
						<div class="portlet-body form">
							<!-- BEGIN FORM-->
							<form action="#" id='newgame' class="form-horizontal form-bordered">
								<div class="form-body">
									<div class="form-group">
										<label class="control-label col-md-3">账户UID<span class="required">
                                                        * </span></label>
										<div class="col-md-4">
											<input class="form-control" id="rechargeUid" type="text"/>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">类型 <span class="required">
                                                        * </span>
										</label>
										<div class="col-md-4">
											<select class="form-control" name="select" id="rechargeType">
												<option value='0'>请选择</option>
												<option value='101'>内币发放</option>
												<option value='110'>活动发放</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">所属活动 <span class="required">
                                                        * </span>
										</label>
										<div class="col-md-4">
											<select class="form-control" name="select" id="rechargeActive">
												<option value='0'>请选择</option>
												<option value='1001'>运营内币发放</option>
												<option value='2001'>封测活动</option>
												<option value='2002'>王者荣耀Solo活动</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">欢朋币<span class="required">
                                                        * </span></label>
										<div class="col-md-4">
											<input class="form-control" id="rechargeHpcoin" type="text"/>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">欢朋豆<span class="required">
                                                        * </span></label>
										<div class="col-md-4">
											<input class="form-control" id="rechargeHpbean" type="text"/>
										</div>
									</div>
									<div class="form-group last">
										<label class="control-label col-md-3">描述<span class="required">
                                                        * </span></label>
										<div class="col-md-9">
											<textarea id="maxlength_textarea" class="form-control" maxlength="225"
													  rows="2" placeholder="最少8个字"></textarea>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<div class="row">
										<div class="col-md-offset-3 col-md-9">
											<button type="button" class="btn bg-yellow-gold" style="color:#FFF; "
													id='addRecharge'>提交
											</button>
										</div>
									</div>
								</div>
							</form>
							<!-- END FORM-->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include '../module/footer.php'; ?>
<?php include '../module/mainScript.php'; ?>
<script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
<script type="text/javascript" src="../common/global/scripts/common.js"></script>
<script>


	function cancelInit() {
		$("#rechargeUid").val('');
		$("#rechargeType").val('0');
		$("#rechargeActive").val('0');
		$("#rechargeHpcoin").val('');
		$("#rechargeHpbean").val('');
		$("#maxlength_textarea").val('');
	}

	$("#addRecharge").on('click', function () {
		var ruid = $("#rechargeUid").val();
		var rtype = $("#rechargeType").val();
		var activeid = $("#rechargeActive").val();
		var hpcoin = $("#rechargeHpcoin").val();
		var hpbean = $("#rechargeHpbean").val();
		var desc = $("#maxlength_textarea").val();
		if (ruid == 0) {
			alert('账户不能为空!');
			return;
		}
		if (isNaN(ruid)) {
			alert('账户格式错误!');
			return;
		}
		if (rtype == 0) {
			alert('请选择类型!');
			return;
		}
		if (activeid == 0) {
			alert('请选择活动!');
			return;
		}
		if (hpbean == '' && hpcoin=='' ) {
			alert('充值数额不能为空!');
			return;
		}
		if (isNaN(hpcoin)) {
			alert('请输入正确的欢朋币数额!');
			return;
		}
		if (isNaN(hpbean)) {
			alert('请输入正确的欢朋豆数额!');
			return;
		}
		if (desc == '') {
			alert('描述不能为空');
			return;
		}
		if(desc.length < 8){
			alert('描述最少8个字');
			return;
		}
		$.ajax({
			url: '../api/recharge/recharge.php',
			type: 'post',
			dataType: 'json',
			data: {
				uid: getCookie('admin_uid'),
				encpass: getCookie('admin_enc'),
				type: getCookie('admin_type'),
				ruid: ruid,
				rtype: rtype,
				activeid: activeid,
				hpcoin: hpcoin,
				hpbean: hpbean,
				desc: desc
			},
			success: function (d) {
				if (d.stat) {
					alert('添加成功');
					cancelInit();
				} else {
					alert('添加失败,请稍候重试!');
				}
			}
		});
	});
	function gameTypeList() {
		$.ajax({
			url: '../api/game/getGameTypeList.php',
			dataType: 'json',
			data: {
				uid: getCookie("admin_uid"),
				encpass: getCookie('admin_enc'),
				type: getCookie('admin_type')
			},
			type: 'POST',
			cache: false,
			success: function (data) {
				if (data) {
//                            $.each(data.data, function (i, item) {
//                                $("#gametype").append(
//                                        '<option value= ' + item.gametid + '>' + item.name + '</option>'
//                                        );
//                            });

				}
			},
			error: function () {

			}

		});
	}
</script>
</body>
</html>
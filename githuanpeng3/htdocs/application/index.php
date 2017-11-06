<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/16
 * Time: 上午10:08
 */

include_once 'init.php';
$path = realpath(__DIR__);
if(!$isMobile){
	include WEB_PERSONAL_URL.'isLogin.php';
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php if($isMobile){ ?>
		<meta name="viewport" content="target-densitydpi=device-dpi,width=640,user-scalable=no" />
	<?php }else{ ?>
		<meta name="viewport"content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes"/>
	<?php }?>
	<title><?php echo $pageTitle; ?></title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="../static/css/common.css?v=1.0.4">
	<link rel="stylesheet" href="../static/css/head.css?v=1.0.4">
	<link rel="stylesheet" href="../static/css/footer.css?v=1.0.4">
	<link rel="stylesheet" href="beAnchor.css<?php echo "?time=".time(); ?>">
	<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>swfobject.js"></script>
	<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>jquery.qrcode.min.js"></script>
	<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>jquery.artDialog.js"></script>
	<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>common.js?t=<?php echo time(); ?>"></script>
	<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>head.js?v=1.0.4"></script>
	<script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>hover.js?v=1.0.4"></script>

	<script type="application/javascript">
		<?php echo $varToJs.$varErrorToJs;?>
	</script>
</head>
<body>

<?php if(!$isMobile) include  WEBSITE_MAIN.'head.php';?>

<div class="page-content cert-to-anchor <?php echo $pageClass. " "; if($isMobile)echo " h5-style"; ?>">

	<?php include $include_view_path;?>
</div>

<?php if(!$isMobile) include $path.'/../footerSub.php';?>
<script type="application/javascript" src="../static/js/jquery-1.9.1.min.js"></script>
<script type="application/javascript" src="../static/js/jquery.form.js"></script>
<script type="application/javascript" src="../static/js/common.js?v=1.0.4"></script>
<?php if(!$isMobile){?>
	<script type="application/javascript" src="../static/js/head.js?v=1.0.4"></script>
	<script>$(document).ready(function () {new head(null, false);})</script>
<?php }?>
<script type="application/javascript" src="main.js?t=<?php echo time(); ?>"></script>
<script>

	$(document).ready(function () {
		app.init();
		if(isIphoneClient()){
			try {
//				alert($('html').html());
				// window.appInsertJSSucceed();
//				alert(isIphoneClient());
//				window.appSetTitle(pageTitle);
//				alert("setTitle over");
			}catch(e) {
//				alert(JSON.stringify(e));
			}
		}
	});
</script>
</body>
</html>

<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	require('../config/config.php');
	require('../api/GameType.class.php');

	$gameType = new GameType();
	$data = $gameType->getList();
	extract($data);
	$domain = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . '/';
	$sidebar = 2;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<!-- Title and other stuffs -->
	<title>欢朋直播后台管理-<?php echo $company['name']; ?></title> 
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<?php include('include/static.php'); ?>
</head>

<body>
<?php include('include/header.php'); ?>
<!-- Main content starts -->
<div class="content">
	<?php include('include/sidebar.php'); ?>
  	<!-- Main bar -->
  	<div class="mainbar">
      <!-- Page heading -->
      <div class="page-head">
        <h2 class="pull-left">
			<i class="icon-table"></i>游戏类型列表
			<a href="<?php echo $domain; ?>/system/manage/view/gameTypeEdit.php"class="btn btn-success">添加游戏类型</a>
		</h2>
        <div class="clearfix"></div>
      </div>
      <!-- Page heading ends -->

	    <!-- Matter -->
	    <div class="matter">
        <div class="container">
          <!-- Table -->
            <div class="row">
              <div class="col-md-12">
                <div class="widget">
                  <div class="widget-content">
                    <table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>类型ID</th>
								<th>名称</th>
								<th>ICON</th>
								<th>操作</th>
							</tr>
						</thead>
						
                      <tbody>
						<?php if($list) { ?>
						<?php foreach($list as $k=>$v) { ?>
                        <tr>
                          <td><?php echo $v['gametid']; ?></td>
                          <td><?php echo $v['name']; ?></td>
						  <td><img style="width:50px; height:50px" src="<?php echo $v['icon']; ?>" /></td>
                          <td>
							<a href="<?php echo $domain; ?>/system/manage/view/gameTypeList.php?m=del&gametid=<?php echo $v['gametid']; ?>" class="btn btn-xs btn-warning" title="修改"><i class="icon-pencil"></i></a>
							<a href="<?php echo $domain; ?>/system/manage/view/gameTypeEdit.php?gametid=<?php echo $v['gametid']; ?>" class="btn btn-xs btn-danger" title="删除"><i class="icon-remove"></i></a>
                          </td>
                        </tr> 
						<?php } ?>						
						<?php } ?>	
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
        </div>
		</div>
		<!-- Matter ends -->
    </div>
   <!-- Mainbar ends -->	    	
   <div class="clearfix"></div>
</div>
<!-- Content ends -->
<?php include('include/footer.php'); ?>
</body>
</html>
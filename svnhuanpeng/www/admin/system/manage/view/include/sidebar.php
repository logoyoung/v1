<!-- Sidebar -->
<div class="sidebar">
	<div class="sidebar-dropdown"><a href="#">导航栏</a></div>
	<!--- Sidebar navigation -->
	<!-- If the main navigation has sub navigation, then add the class "has_sub" to "li" of main navigation. -->
	<ul id="nav">   
		<li><a <?php if($sidebar == 1) {?>class="subdrop"<?php }?> href="<?php echo $domain . 'system/manage/view/gameList.php'; ?>"><i class="icon-calendar"></i>游戏管理</a></li>
		<li><a <?php if($sidebar == 2) {?>class="subdrop"<?php }?> href="<?php echo $domain . 'system/manage/view/gameTypeList.php'; ?>"><i class="icon-calendar"></i>游戏类型管理</a></li>
	</ul>
</div>
<!-- Sidebar ends -->
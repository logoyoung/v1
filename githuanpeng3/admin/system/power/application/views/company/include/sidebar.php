<!-- Sidebar -->
<div class="sidebar">
	<div class="sidebar-dropdown"><a href="#">导航栏</a></div>
	<!--- Sidebar navigation -->
	<!-- If the main navigation has sub navigation, then add the class "has_sub" to "li" of main navigation. -->
	<ul id="nav">   
		<li><a <?php if($nav == 1) { ?>class="subdrop" <?php } ?> href="<?php echo $this->config->config['adminuser_url']; ?>?d=company&c=anchor"><i class="icon-calendar"></i>主播管理</a></li>
	</ul>
</div>
<!-- Sidebar ends -->
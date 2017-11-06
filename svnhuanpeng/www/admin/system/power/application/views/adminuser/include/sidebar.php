<!-- Sidebar -->
<div class="sidebar">
	<div class="sidebar-dropdown"><a href="#">导航栏</a></div>

	<!--- Sidebar navigation -->
	<!-- If the main navigation has sub navigation, then add the class "has_sub" to "li" of main navigation. -->
	<ul id="nav">   
		<?php if($nav != 4) { ?>
		<li><a <?php if($nav == 1) { ?>class="subdrop" <?php } ?> href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=user"><i class="icon-calendar"></i>用户管理</a></li>
		<li><a <?php if($nav == 2) { ?>class="subdrop" <?php } ?> href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=role"><i class="icon-calendar"></i>组管理</a></li>
		<li><a <?php if($nav == 3) { ?>class="subdrop" <?php } ?> href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=control"><i class="icon-calendar"></i>权限管理</a></li>
		<?php } ?>
		<li><a <?php if($nav == 4) { ?>class="subdrop" <?php } ?> href="<?php echo $this->config->config['adminuser_url']; ?>?d=adminuser&c=modify"><i class="icon-calendar"></i>更改密码</a></li>
	</ul>
</div>
<!-- Sidebar ends -->
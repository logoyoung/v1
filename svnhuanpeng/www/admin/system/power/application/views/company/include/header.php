<div class="navbar navbar-fixed-top bs-docs-nav" role="banner">
    <div class="conjtainer">
		<!-- Menu button for smallar screens -->
		<div class="navbar-header">
			<button class="navbar-toggle btn-navbar" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
				<span>Menu</span>
			</button>
			<!-- Site name for smallar screens -->
			<a href="index.html" class="navbar-brand hidden-lg">欢朋后台管理</a>
		</div>
		<!-- Navigation starts -->
		<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">         
			<!-- Links -->
			<ul class="nav navbar-nav pull-right">
				<li class="dropdown pull-right">            
					<a data-toggle="dropdown" class="dropdown-toggle" href="#">
						欢迎你，<i class="icon-user"></i><?php echo $_COOKIE['admin_name']; ?><b class="caret"></b>
					</a>
					<!-- Dropdown menu -->
					<ul class="dropdown-menu">
						<li><a target="_blank" href="<?php echo $this->config->config['adminuser_url'] . '?c=login&m=logout'; ?>"><i class="icon-cogs"></i>退出登录</a></li>
						<li><a href="<?php echo $this->config->config['adminuser_url'] . '?d=adminuser&c=modify'; ?>"><i class="icon-off"></i>修改密码</a></li>
					</ul>
				</li>
			</ul>
		</nav>
    </div>
</div>
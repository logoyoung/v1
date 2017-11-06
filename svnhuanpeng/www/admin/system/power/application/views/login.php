<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <!-- Title and other stuffs -->
  <title>欢朋直播后台管理</title>
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="">
  <!-- Stylesheets -->
  <link href="<?php echo $this->config->config['adminuser_css_url']; ?>style/bootstrap.css" rel="stylesheet">
  <link href="<?php echo $this->config->config['adminuser_css_url']; ?>style/font-awesome.css" rel="stylesheet" >
  <link href="<?php echo $this->config->config['adminuser_css_url']; ?>style/style.css" rel="stylesheet">
  
  <!-- HTML5 Support for IE -->
  <!--[if lt IE 9]>
  <script src="<?php echo $this->config->config['adminuser_js_url']; ?>js/html5shim.js"></script>
  <![endif]-->
  <!-- Favicon -->
  <link rel="shortcut icon" href="<?php echo $this->config->config['adminuser_css_url']; ?>img/favicon/favicon.png">
</head>

<body>

<!-- Form area -->
<div class="admin-form">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <!-- Widget starts -->
            <div class="widget worange">
              <!-- Widget head -->
              <div class="widget-head">
                <i class="icon-lock"></i>&nbsp;&nbsp;欢朋直播后台管理
              </div>

              <div class="widget-content">
                <div class="padd">
                  <!-- Login form -->
                  <form class="form-horizontal" action="<?php echo $this->config->config['adminuser_url']; ?>?c=login&m=doLogin" method="post">
                    <!-- Email -->
                    <div class="form-group">
                      <label class="control-label col-lg-3" for="inputEmail">邮箱</label>
                      <div class="col-lg-9">
                        <input type="text" class="form-control" id="inputEmail" name="email" placeholder="邮箱">
                      </div>
                    </div>
                    <!-- Password -->
                    <div class="form-group">
                      <label class="control-label col-lg-3" for="inputPassword">密码</label>
                      <div class="col-lg-9">
                        <input type="password" class="form-control" id="inputPassword" name="password" placeholder="密码">
                      </div>
                    </div>
					<input type="hidden" name="referer" value="<?php echo $referer; ?>">
                        <div class="col-lg-9 col-lg-offset-2">
							<button type="submit" class="btn btn-danger">登录</button>
							<label style="color:red"><?php echo isset($error) ? $error : ''; ?></label>
						</div>
                    <br />
                  </form>
				  
				</div>
                </div>
            </div>  
      </div>
    </div>
  </div> 
</div>
</body>
</html>
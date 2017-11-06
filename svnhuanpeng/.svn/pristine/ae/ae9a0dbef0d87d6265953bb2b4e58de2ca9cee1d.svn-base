<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/29
 * Time: 上午10:59
 */
?>
<textarea id="jsTemplate-loginModal" style="display: none">
    <div class="loginCon">
        <div class="loginHeader">
            <ul class="login_select_tab">
                <li class="selected">登录</li>
                <li>注册</li>
            </ul>
            <span class="loginModal-close"></span>
        </div>
        <%if(loginModal){%>
            <div class="loginCon-left" style="padding: 30px 0;">
                <div id="login_form" class="login_tab_con" >
                    <div class="login-form-item">
                        <span class="login-input-icon login-icon-phone"> <i></i> </span>
                        <input type="text" class="text input-item-text to-nick" id="username" placeholder="请输入手机号" autocomplete="off">
                        <div class="input-item-error-text" id="login-error-text-username"></div>
                    </div>
                    <div class="login-form-item">
                        <span class="login-input-icon login-icon-password"> <i></i> </span>
                        <input type="password" class="text input-item-text to-pass" name="modal-login" id="password" placeholder="输入密码" autocomplete="off">
                        <!--<input type="password" class="text input-item-text to-pass" name="modal-login" id="password" placeholder="输入密码" style="position: absolute;z-index: -1;left: 37px;top:0px;"autocomplete="off">-->
                        <div class="input-item-error-text" id="login-error-text-password"></div>
                    </div>
                    <div class="login-form-item button-container login-button-container">
                        <a id="loginsubmit" href="javascript:;" class="input-item-btn">登录</a>
                    </div>
                    <div class="login-form-item control-password">
                        <a href="javascript:;"class="toRegister" onclick="loginFast.login(1)">我要注册</a>
                        <a href="<?php echo WEB_ROOT_URL.'resetpwd/index.php'; ?>">忘记密码?</a>
                    </div>
					<div class="loginCon-foot"></div>
                </div>
            </div>
        <%}else{%>
            <div class="loginCon-left">
                <div id="reg_form" class="login_tab_con">
                    <div class="login-form-item">
                        <span class="login-input-icon login-icon-phone"><i></i></span>
                        <input type="text" class="text input-item-text" id="username" placeholder="请输入手机号">
                        <input type="password" style="display: none">
                        <div class="input-item-error-text" id="login-error-text-username"></div>
                    </div>
					<!--                    <div class="login-form-item identCode-item">-->
					<!--                        <span class="login-input-icon login-icon-text"> <i></i> </span>-->
					<!--                        <input type="text" class="input-item-text" id="identCode" placeholder="验证码">-->
					<!--                        <div class="input-item-error-text" id="login-error-text-identCode"></div>-->
					<!--                        <span class="login-identCode">-->
					<!--                            <img src="http://dev.huanpeng.com/main/a/code/registerCode.php?nowtime='+new Date().getTime()+'">-->
					<!--                        </span>-->
					<!--                    </div>-->
                    <div class="login-form-item mobileCode-item">
                        <span class="login-input-icon login-icon-msg"><i></i></span>
                        <input type="text" class="text input-item-text" id="mobileCode" placeholder="验证码">
                        <div class="input-item-error-text" id="login-error-text-mobileCode"></div>
                        <a href="javascript:;" id="reg-getMobileCode">获取验证码</a>
                    </div>
                    <div class="login-form-item">
                        <span class="login-input-icon login-icon-nick"><i></i></span>
                        <input type="text" class="text input-item-text" id="usernick" placeholder="昵称">
                        <div class="input-item-error-text" id="login-error-text-usernick"></div>
                    </div>
                    <div class="login-form-item">
                        <span class="login-input-icon login-icon-password"><i></i></span>
                        <input type="password" class="text input-item-text" id="password" name="modal-reg" placeholder="密码" autocomplete="off">
                        <!--<input type="password" class="text input-item-text" id="password" name="modal-reg" placeholder="密码" autocomplete="off" style="position: absolute;z-index: -1;left: 29px;top:0px;">-->
                        <div class="input-item-error-text" id="login-error-text-password"></div>
                    </div>
                    <div class="login-form-item button-container login-button-container">
                        <a id="regsubmit" href="javascript:;" class="input-item-btn">注册</a>
                    </div>
                    <div class="login-form-item control-agreement">
                        <span  href="javascript:;"class="">注册即代表同意
                            <a target="_blank" class="agreement-rule" href="<?php echo WEB_ROOT_URL; ?>protocol/protocolUser.php">《欢朋直播用户协议及版权说明》</a>
                        </span>
                    </div>
                    <div class="loginCon-foot"></div>
                </div>
            </div>
            <div id="reg-captcha"></div>
        <%}%>
		<div class="login-threeParty">
			<p style="margin-bottom: 30px;">第三方账号直接登录</p>
			<p>
				<a id="weibo-login-hm" href="<?php echo WEB_ROOT_URL; ?>personal/oauth/index.php?channel=weibo&order=login">
					<img src="<?php echo WEB_ROOT_URL; ?>static/img/threeParty/weibo-60.png" alt="">
				</a>
			</p>
			<p>
				<a id="qq-login-hm" href="<?php echo WEB_ROOT_URL; ?>personal/oauth/index.php?channel=qq&order=login">
					<img src="<?php echo WEB_ROOT_URL; ?>static/img/threeParty/qq-60.png" alt="">
				</a>
			</p>
			<p>
				<a id="wechat-login-hm" href="<?php echo WEB_ROOT_URL; ?>personal/oauth/index.php?channel=wechat&order=login">
					<img src="<?php echo WEB_ROOT_URL; ?>static/img/threeParty/wechat-60.png" alt="">
				</a>
			</p>
		</div>
		<div class="clear"></div>
    </div>
</textarea>
<textarea name="" id="jsTemplate-bindMobileModal" cols="" rows="" style="display: none;">
	<div class="loginCon bind-phone">
		<div class="loginHeader">
			<p class="title" style="margin: 0;float: left;height: 50px;line-height: 50px;font-size: 24px;color: #333;">绑定手机</p>
			<span class="loginModal-close"></span>
		</div>
		<div class="loginCon-left">
			<div id="bind_form" class="login_tab_con">
				<div class="login-form-item">
					<div class="label">手机号</div>
					<input type="text" class="text input-item-text" id="username" name="username" placeholder="请输入手机号">
                    <input type="password" style="display: none;">
                    <div class="input-item-error-text" id="login-error-text-username"></div>
				</div>
				<div class="login-form-item mobileCode-item">
					<div class="label">验证码</div>
					<input type="text" class="text input-item-text" id="mobileCode" placeholder="验证码">
					<div class="input-item-error-text" id="login-error-text-mobileCode"></div>
					<a href="javascript:;" id="bind-getMobileCode">获取验证码</a>
				</div>
				<div class="login-form-item">
					<div class="label">密码</div>
					<input type="password" class="text input-item-text" id="password" placeholder="密码" autocomplete="off">
				</div>
				<div class="login-form-item">
					<div class="label">确认密码</div>
					<input type="password" class="text input-item-text" id="password2" placeholder="密码" autocomplete="off">
					<div class="input-item-error-text" id="login-error-text-password2"></div>
				</div>
				<div class="login-form-item button-container login-button-container">
					<a id="bindSubmit" href="javascript:;" class="input-item-btn">注册</a>
				</div>
				<div class="loginCon-foot"></div>
			</div>
		</div>
		<div id="binding-captcha"></div>
	</div>
</textarea>

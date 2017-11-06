<div class="title">
    <a href="<?php echo WEB_PERSONAL_URL;?>"><span class="personal_info">个人资料</span></a>
    <a href="<?php echo WEB_PERSONAL_URL.'mp/modify_passwd'?>"><span class="changePassword">修改密码</span></a>
    <?php
    if($userCertifyStatus['emailstatus'] != EMAIL_PASS)
        echo '<a href="'.WEB_PERSONAL_URL.'mp/certify_email"><span class="emailCert">邮箱认证</span></a>';

//    if($userCertifyStatus['phonestatus'] == 0)
//        echo '<a href="'.WEB_PERSONAL_URL.'mp/certify_phone"><span class="phoneCert">手机认证</span></a>';

//    if($userCertifyStatus['bankstatus'] == 0)
//        echo '<a href="'.WEB_PERSONAL_URL.'mp/certify_bankcard"><span class="bankCert">银行卡认证</span></a>';

//    if($userCertifyStatus['identstatus'] == 0)
//        echo '<a href="'.WEB_PERSONAL_URL.'mp/certify_realname"><span class="realnameCert">实名认证</span></a>';
    ?>

</div>
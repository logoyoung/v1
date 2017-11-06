<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 16/12/16
 * Time: 11:07
 */
?>

        <div class="content-left">
            <div class="slider-list">
                <div class="slider-one">
                    <div class="one-title">账号相关</div>
                    <a href="helpReg.php"><div class="one-tab reg">注册相关</div></a>
                    <a href="helpAccount.php"><div class="one-tab login">账号相关</div></a>
                    <a href="helpPwdProtect.php"><div class="one-tab pwd-protect">密保问题</div></a>
                    <a href="helpRecharge.php"><div class="one-tab recharge">充值问题</div></a>
                </div>
            </div>
            <div class="slider-list">
                <div class="slider-one">
                    <div class="one-title">主播相关</div>
                    <a href="helpAnchorCertification.php"><div class="one-tab anchor-cert">主播认证</div></a>
                    <a href="helpAnchorIncome.php"><div class="one-tab anchor-income">主播收益</div></a>
                    <a href="helpIncomePresent.php"><div class="one-tab income-present">收益提现</div></a>
                </div>
            </div>
            <div class="slider-list">
                <div class="slider-one">
                    <div class="one-title">直播教程</div>
                    <a href="helpAssistant.php"><div class="one-tab help-pc">欢朋直播助手</div></a>
                    <a href="helpiPhone.php"><div class="one-tab help-iPhone">iPhone版</div></a>
                    <a href="helpAndroid.php"><div class="one-tab help-Android">Android版</div></a>
                </div>
            </div>
        </div>
               <!--
               帮助内容
               -->

<script>
    $(function () {
        opt = opt||'reg';
        $('.'+opt).addClass('show');
    })
</script>
</html>
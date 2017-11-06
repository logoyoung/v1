<?php
include '../../../../include/init.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>绑定银行卡</title>
    <meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta name="format-detection" content="telephone=no" />
    <meta content="email=no" name="format-detection" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <link rel="stylesheet" href="css/mui.picker.css?t=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/mui.css?t=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/myBindCard.css?t=<?php echo time(); ?>">
    <script src="<?php echo STATIC_JS_PATH; ?>jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="<?php echo STATIC_JS_PATH; ?>common.js?v=1.0.4" type="text/javascript"></script>
    <script src="js/mui.js?t=<?php echo time(); ?>" type="text/javascript"></script>
    <script src="js/mui.picker.js?t=<?php echo time(); ?>" type="text/javascript"></script>
    <script src="js/data.city.js?v=1.0.4" type="text/javascript"></script>
    <script src="js/myBindCard.js?t=<?php echo time();?>" type="text/javascript"></script>
</head>

<body>

<div class="bindCard-container">
    <div class="bindCard-tipBody">
        <div class="bindCard-Box clearfix">
            <div class="bindCard-stepBlock">
                <div class="bindCard-stepBody">
                    <figure class="step-one">
                        <div class="st1 num1"></div>
                        <figcaption>银行卡信息</figcaption>
                    </figure>
                    <figure class="step-progress">
                        <div class="progressBG">
                            <div class="progressStep"></div>
                        </div>
                    </figure>
                    <figure class="step-two noAll">
                        <div class="st2 num2"></div>
                        <figcaption>信息确认</figcaption>
                    </figure>
                </div>
            </div>
        </div>
        <div id="template-step1">
            <div class="bindCard-person">
                <table id="tableFixed">
                    <tr>
                        <td>
                            <p class="desc">姓名</p>
                        </td>
                        <td class="line">
                            <input id="bindName" type="text" placeholder="请输入姓名" class="inpStyle">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="desc">开户行</p>
                        </td>
                        <td class="line" style="position: relative;">
                            <select id="bankSelect" class="inpStyle otherS">
                                <option value="请选择银行">请选择银行</option>
                                <option data-bank="2" value="招商银行">招商银行</option>
                                <option data-bank="5" value="中国工商银行">中国工商银行</option>
                                <option data-bank="10" value="中国农业银行">中国农业银行</option>
                                <option data-bank="15" value="中国建设银行">中国建设银行</option>
                                <option data-bank="20" value="交通银行">交通银行</option>
                                <option data-bank="25" value="上海浦东发展银行">上海浦东发展银行</option>
                                <option data-bank="30" value="民生银行">民生银行</option>
                                <option data-bank="35" value="光大银行">光大银行</option>
                                <option data-bank="40" value="兴业银行">兴业银行</option>
                                <option data-bank="45" value="广东发展银行">广东发展银行</option>
                                <option data-bank="50" value="平安银行">平安银行</option>
                                <option data-bank="55" value="北京银行">北京银行</option>
                                <option data-bank="60" value="中国银行">中国银行</option>
                                <option data-bank="65" value="中信银行">中信银行</option>
                                <option data-bank="70" value="华夏银行">华夏银行</option>
                                <option data-bank="75" value="中国邮政储蓄银行">中国邮政储蓄银行</option>
                                <option data-bank="80" value="城市商业银行">城市商业银行</option>
                                <option data-bank="85" value="农村商业银行">农村商业银行</option>
                                <option data-bank="90" value="农村合作银行">农村合作银行</option>
                                <option data-bank="95" value="农村信用合作社">农村信用合作社</option>
                                <option data-bank="100" value="恒丰银行">恒丰银行</option>
                                <option data-bank="105" value="渤海银行">渤海银行</option>
                                <option data-bank="110" value="南京银行">南京银行</option>
                                <option data-bank="115" value="江苏银行">江苏银行</option>
                                <option data-bank="120" value="宁波银行">宁波银行</option>
                                <option data-bank="125" value="上海银行">上海银行</option>
                                <option data-bank="130" value="杭州银行">杭州银行</option>
                                <option data-bank="135" value="中国农业发展银行">中国农业发展银行</option>
                                <option data-bank="140" value="花旗银行">花旗银行</option>
                                <option data-bank="145" value="渣打银行">渣打银行</option>
                            </select>
                            <div class="arrow"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="desc">开户地</p>
                        </td>
                        <td class="line">
                            <input class="inpStyle" id="selectCity" type="text" name="input_area" placeholder="请选择银行开户地" readonly>
                            <div class="arrow"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="desc">卡号</p>
                        </td>
                        <td class="line">
                            <input id="firstCN" class="inpStyle" type="text" maxlength="25" placeholder="请填写银行卡号">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="desc">卡号确认</p>
                        </td>
                        <td class="line">
                            <input id="checkNum" class="inpStyle" type="text" maxlength="25" placeholder="请再次填写银行卡卡号">
                        </td>
                    </tr>
                </table>
            </div>
            <div class="bindCard-nextBtn">
                <button id="nextBtn">下一步</button>
            </div>
            <div class="bindCard-tip">
                <p class="tip-title">注意:</p>
                <p class="tip-desc-row1">请绑定您实名注册身份开通的银行卡,否则您可能收不到打款;</p>
                <p class="tip-desc-row2">请确认银行卡信息填写无误。</p>
            </div>
        </div>
        <div id="template-step2">
            <div class="bindCard-show">
                <div class="hp-logo"></div>

                <div class="bank-desc">
                    <h3 id="bankTitle"></h3>
                    <p id="bankLBS" class="mlText"></p>
                </div>

                <div class="number-desc">
                    <p class="mlText">卡号</p>
                    <h3 id="cardNumber"></h3>
                </div>
                <div class="person-desc">
                    <p class="mlText" id="bankName"></p>
                    <!--<h4 id="bankPhone"></h4>-->
                </div>

            </div>
            <div class="bindCard-btn">
                <button id="subTohp">确认</button>
            </div>
        </div>
    </div>
</div>

<div class="modalBox">
    <div class="loading">
        <div class="icon_loading"></div>
    </div>
    <div class="success">
        <img src="img/icon_right.png">
        <p></p>
    </div>
    <div class="fail">
        <img src="img/icon_fail.png">
        <p></p>
    </div>
</div>
</body>
</html>


<div class="pdetail">
    <div class="personalPic">
        <img/>
        <label class="modify-face" for="user-face-file">修改头像</label>
        <div class="check-pic-block">
            <div class="check-icon"></div>
            <div class="text"></div>
        </div>
        <form id="modify-face-form" name="modify-face-form" method="post" class="none">
            <input name="file" id="user-face-file" type="file" accept="image/jpeg, image/jpg, image/png, image/gif"/>
        </form>
    </div>
    <div class="personalInfo">
        <span class="nick">&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<span class="levellable" style="padding-bottom: 0px;"> 等级：
			<div class="userLvl-icon level" style="position: absolute"></div>
			<span class="personal_icon levelUpIcon"></span>
		</span>
		<span class="bar">
			<div class="levelbar"></div>
			<p style="float: left;margin: 6px 0px 0px 0px; font-size: 12px;position: absolute;"></p>
		</span>
        <i style="padding: 0;
                font-size: 12px;
                text-align: center;
                top: 20px;
                font-style: normal;
                position: relative;
                color: #7f7f7f;">用户ID: <span id="userID" style="display: inline-block;color: #ff7800;font-size: 14px;
"></span></i>
    </div>
    <div class="payment_block">
        <div class="paytype">
            <div class="pay">
                欢朋币
				<div class="icon anchor_icon hpcoin"></div>
                <icon1></icon1>
            </div>
            <div class="num"></div>
            <a class="paybtn">充值</a>
			<div class="clear"></div>
        </div>
        <div class="paytype">
            <div class="pay">
                欢朋豆
				<div class="icon anchor_icon hpbean"></div>
                <icon1></icon1>
            </div>
            <div class="num"></div>
			<div class="clear"></div>
        </div>
		<div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.paybtn').attr('href', $conf.person + 'recharge.php');
        $('#user-face-file').change(function(){

            $conf = conf.getConf();
            var form = $('#modify-face-form');

            if(!form) return;

            var fileUploadRes = checkUploadImage($("#user-face-file").get()[0], $conf.uploadImgSize);
            if(fileUploadRes < 0){
                console.log(fileUploadRes);
                return;
            }

            form.ajaxSubmit({
                url:$conf.api + '/upload/uploadUserPic.php ',
                type:'post',
                dataType:'json',
                data:{
                    uid:getCookie('_uid'),
                    encpass:getCookie('_enc')
                },
                uploadProgress:function(event, position, totla, percentComplete){
                    console.log(percentComplete);
                },
                success:function(d){
                    if(d.status==1){
//                        $('.pdetail .personalPic img').attr('src', d.userPic);
                        setUserHead(d.content.picCheckStat, d.content.head);
                    }else{
                        if(d.code){
                            tips(d.desc);
                        }
                    }
                }
            });
        });
    });

</script>
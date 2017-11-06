
function setUserHead(stat, url){
    var picDom = $("#personal .basic .pdetail .personalPic");
    var checkUrl = arguments[2] ? arguments[2] : url;
    console.log(checkUrl);
    stat = parseInt(stat);
    switch (stat){
        case 0:
            picDom.find('.check-pic-block').remove();
            picDom.find('img').attr('src', checkUrl).after('<div class="check-pic-block"><div class="check-icon"></div><div class="text">新头像审核中</div></div>')
            break;
        case 1:
            picDom.find('img').attr('src', url);
            picDom.find('.check-pic-block').remove();
            break;
        case 2:
            picDom.find('.check-pic-block').remove();
            picDom.find('img').attr('src', checkUrl).after('<div class="check-pic-block"><div class="check-icon"></div><div class="text">新头像未通过审核</div></div>')
            break;
    }
}

(function(){
    personalCenter_sidebar('personal');
    var $conf = conf.getConf();
    var personalInfo = $("#personal .basic .pdetail");
    if(!personalInfo.get()[0])
        return;

    var requestUrl = $conf.api + 'user/info/accessPersonalInfo.php';
    var requestData = {
        uid:getCookie('_uid'),
        encpass:getCookie('_enc')
    };
    ajaxRequest({url:requestUrl,data:requestData},function(d){
        setUserHead(d.picCheckStat, d.head, d.picCheckUrl);
        // setUserHead(d.picCheckStat, d.pic, d.picCheckUrl);
        personalInfo.find('.personalInfo .nick').text(d.nick);
        personalInfo.find('.levellable .level').addClass('level'+ d.level);

        var bar_width = personalInfo.find('.personalInfo .bar').width();
        var between = parseInt(d.levelIntegral - d.integral);
        var levelbar_width = (d.integral / d.levelIntegral) * bar_width;
        personalInfo.find('.personalInfo .bar .levelbar').css('width',levelbar_width + 'px');
        personalInfo.find('.personalInfo .bar p').html('距离升级还有 <i style="font-style: normal;color: #FF7800;">' + between + "</i> 经验值");
        personalInfo.find('.payment_block .paytype:eq(0) .num').text(digitsFormat(d.hpcoin));
        personalInfo.find('.payment_block .paytype:eq(1) .num').text(digitsFormat(d.hpbean));

        setCookie('_unick', d.nick);

        address(d.addr,d.pid,d.cid);
    });

    function address(addr, pid, cid){
        console.log(addr);
        var selfAddr = addr;
        var selfPid = Number(pid);
        var selfCid = Number(cid);
        normalStatus(selfAddr);

        function normalStatus(addr){
            console.log(addr);
            if(addr){
                var html = '<span class="identifyDetail mt-12 mr-20 left">' + addr + '</span><span class="option mt-12 left">修改</span><div class="clear"></div>';
            }else{
                var html = '<span class="identifyDetail mt-12 mr-20 left">填写您的地址</span><span class="option mt-12 left">添加</span><div class="clear"></div>';
            }

            $("#anchorAddr .controls").html(html).find('.option').click(function(){
                editStatus();
            });

        }

        function editStatus(){
            var html = '<div class="select-container">\
                                <div class="selectCity">\
                                    <input type="text" placeholder="请选择省/市" id="proCity" readonly="readonly">\
                                    <span class="selectTitle">\
                                        <div class="title block-title">\
                                            <div class="title-prov curr">省</div>\
                                            <div class="title-city">市</div>\
                                        </div>\
                                        <div class="contentBox">\
                                            <div class="content">\
                                                <div class="contentLeft" id="contentLeft"></div>\
                                                <div class="contentRight" id="contentRight"></div>\
                                            </div>\
                                        </div>\
                                    </span>\
                                </div>\
                                <div class="resLoc">\
                                    <input type="text" placeholder="请输入您的具体地址" id="conLoc">\
                                </div>\
                                <div class="handle">\
                                    <button class="saveBtn">保存</button>\
                                    <button class="cancelBtn">取消</button>\
                                </div>\
                            </div>';
            $("#anchorAddr .controls").html(html);

            var requestUrl = $conf.api + 'other/addressMap.php';
            var requestData = {
                type:1,
                uid:getCookie('_uid'),
                encpass:getCookie("_enc")
            };
            ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                var resList = responseData.list;
                var proList = [];
                for(var i in resList){
                    proList.push(createPro(resList[i]));
                }
                $("#contentLeft").html(proList);
                bindEvent(resList);


                $('#proCity').focus(function () {
                    $('.selectTitle').css('display','block');
                });

                $('.resLoc,#conLoc').focus(function () {
                    $('.selectTitle').css('display','none');
                });

                $(".select-container .handle .saveBtn").click(function () {
                    var detail = $('.resLoc input').val();
                    var requestUrl = $conf.api + 'user/revise/alterUserAddr.php';
                    var requestData = {uid:getCookie('_uid'),encpass:getCookie('_enc'),pid:selfPid,cid:selfCid,detail:detail};
                    ajaxRequest({url:requestUrl,data:requestData},function(){
                        normalStatus(getAddress(selfPid, selfCid, detail));
                    });
                });
                $(".select-container .handle .cancelBtn").click(function () {
                    normalStatus(selfAddr);
                });

                function getAddress(pid,cid,detail){
                    var pro = resList[pid-1].name;
                    var citylist = resList[pid-1].list;
                    var city = '';
                    for(var i in citylist){
                        if(citylist[i].id == cid){
                            city = citylist[i].name;
                        }
                    }

                    return pro + city + detail;
                }
            });

        }

        function createPro(obj) {

            if(obj.pid && obj.name && obj.list){

                var onePro = '<a data-pid="'+obj.pid+'">'+obj.name+'</a>';
                return onePro;
            }

        }
        function bindEvent(resList) {
            $('#contentLeft>a').click(function (e) {
                e.preventDefault();

                var indexT = $(this).attr('data-pid');
                selfPid = indexT;
                if(resList[indexT-1].list){
                    var cityJson = resList[indexT-1].list;
                }
                $('#proCity').val($(this).html());

                var cityList = [];
                for(var j in cityJson){
                    cityList.push(CreateCity(cityJson[j]));
                }

                $('#contentRight').html(cityList);

                //animation
                $('.title-prov').removeClass('curr');
                $('.title-city').addClass('curr');
                $('.content').css('left','-460px');

                $('#contentRight>a').click(function (e) {
                    e.preventDefault();
                    selfCid = $(this).attr('data-id');
                    var oldLoc = $('#proCity').val();
                    $('#proCity').val(oldLoc+'/'+$(this).html());
                    $('.content').css('left','0');
                    $('.title-prov').addClass('curr');
                    $('.title-city').removeClass('curr');

                });

            });

        }

        function CreateCity(obj) {
            if(obj.id && obj.name){
                var oneCity = '<a data-id="'+obj.id+'">'+obj.name+'</a>';
                return oneCity;
            }
        }
    }
}());

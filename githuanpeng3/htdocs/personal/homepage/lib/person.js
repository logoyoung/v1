$(function () {

    initAnchorInfo();

    var nowDate = new Date();
    var nowMonth = nowDate.getMonth() + 1;

    if(nowMonth > 2){
        var lastMonth = nowMonth - 1;
        var thirdBeforeMonth = lastMonth - 1;
    }else if(nowMonth == 2){
        var lastMonth = 1;
        var thirdBeforeMonth = 12;
    }else if(nowMonth == 1){
        var lastMonth = 12;
        var thirdBeforeMonth = 11;
    }


    $('#nowMonth').attr('clickParam',nowMonth);
    $('#lastMonth').html(lastMonth+'月');
    $('#lastMonth').attr('clickParam',lastMonth);
    $('#thirdBeforeMonth').html(thirdBeforeMonth+'月');
    $('#thirdBeforeMonth').attr('clickParam',thirdBeforeMonth);

    highChartDoThis(nowMonth);

    $('.monthLink>a').click(function () {
        $('.monthLink>a').removeClass('curr');
        $(this).addClass('curr');

        //请求参数 月份
        var clickMonth = $(this).attr('clickParam');
        highChartDoThis(clickMonth);

    });
});

function initAnchorInfo(){
    var domBase = $('.person-content');
    var domObject = {
        head:domBase.find('.person-img>img'),
        nick:domBase.find('.resultFirst .result-title'),
        level:domBase.find('.resultSecond'),
        roomID:domBase.find('.roomId'),
        followCount:domBase.find('.proIt span'),
        liveLength:domBase.find('.proTime span'),
        moneyBase:domBase.find('.proMoney'),
        proBase:domBase.find('.proMoney #proBase')
    };

    var requestUrl = $conf.api + 'anchor/anchorInfo.php';
    var requestData = {
        uid:getCookie('_uid'),
        encpass:getCookie('_enc')
    };

    ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
        var barWidth = responseData.integral / responseData.levelIntegral * 100;
        var between = Number(responseData.levelIntegral) - Number(responseData.integral);

        domObject.head.attr('src', responseData.pic);
        domObject.nick.text(responseData.nick);
        domObject.roomID.text(responseData.roomID);
        domObject.followCount.text(responseData.fansCount);
        domObject.liveLength.text(responseData.timeLength);
        domObject.level.find('.result-level').addClass('lv'+responseData.level);//anchorLvl-icon lv1 left lv22
        domObject.level.find('.allPro').css('width',barWidth+'%');
        domObject.level.find('.level-desc span').text(responseData.gapIntegral);

        /*if(responseData.type == 1){
            domObject.moneyBase.css('display','inline-block');
            domObject.proBase.text(responseData.salary+'元');
        }*/

    },function (responseData) {

    });
}


function highChartDoThis(nowMonth) {

    Highcharts.setOptions({
        timezoneOffset : 8,
        colors: ['#ff7800'],
        noData:'您所查找的数据找不到了'
    });

    var requestUrl = $conf.api + 'other/popularity.php';
    var requestData = {
        uid:getCookie('_uid'),
        encpass:getCookie('_enc'),
        month:nowMonth
    };

    ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
        var data  = [];
        var list = responseData.list;
        for(var i in list){
            data.push(parseInt(list[i]));
        }

        var trueData = data;
        var xCategories = [];
        for(var j=1; j < list.length + 1; j++){
            xCategories.push(nowMonth + '月'+j+'日');
        }
        var maxTop = parseInt(Math.max.apply(null, trueData));
        $('#top').html(maxTop);
        $('#monthLiveTime').text(responseData.monthLiveTime);
        $('.chartShow-content').highcharts({
            chart: {
                type: 'line',
                zoomType: 'x'
            },
            credits: {
                enabled: false
            },
            subtitle: {
                text: '单位(人)',
                align: 'left',
                x: 30,
                y: 30,
                style:{
                    fontSize : 12,
                    color: '#666'
                }
            },
            xAxis: {
                type: 'datetime',
                minTickInterval: 5,
                lineColor:'#eee',
                categories: xCategories
            },
            yAxis: {
                gridLineColor: '#eee'
            },
            plotOptions: {
                area: {
                    marker: {
                        radius: 2
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                },
                line: {
                    dataLabels: {
                        enabled: false,
                        color: '#ff7800'
                    },
                    enableMouseTracking: true
                }
            },
            series: [ {
                name: '我的人气',
                data: trueData
            }]
        });

        $('.highcharts-legend,.highcharts-yaxis,.highcharts-title,.highcharts-credits').css('display','none');
        $('.highcharts-subtitle').css({
            'font-family':'微软雅黑',
            'letter-spacing' : '2px'
        });
    })

}


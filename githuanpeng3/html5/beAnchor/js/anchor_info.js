/**
 * Created by junxiao on 2017/6/21.
 */

var chartsData = [];

function dataforMonth(mon, sign) {
	
    var requestUrl  =  $conf.api + 'anchor/anchorLiveTime.php';

       var uid         =   sessionStorage.getItem('_uid');
       var encpass      =   sessionStorage.getItem('_enc');

    // test uid encapss
//  var uid = '1860';
//  var encpass = '9db06bcff9248837f86d1a6bcf41c9e7';

    if(!uid || !encpass){return false;}
    var requestData =  {
        uid         :       uid,
        encpass     :       encpass,
        month       :       mon
    };

    chartsData = [];

    if(sign === 'init'){
        $('body').css('background','#fff');
        ajaxRequest({url:requestUrl, data : requestData}, function (responseData) {

            setApplayStatus(responseData,uid,encpass);

            $('.content-header>.btn').click(function () {
                $('.content-header>.btn').removeClass('sel');
                $(this).addClass('sel');
                var mon = $(this).data('value');
                dataforMonth(mon, null);
            });

            $('.anchor-img').attr('src',responseData.head);
            $('.anchor-name').text(responseData.name);
            $('.anchor-roomID').text('房号 : '+responseData.roomID);
            $('.anchorLvl_icon').addClass('lv'+responseData.level);
            $('.anchor-intergal').text(responseData.integral);

            //本月直播时长
            var hour_month = string_fixed(responseData.livedTimeMonth).hour;
            var minu_month = string_fixed(responseData.livedTimeMonth).minu;
            $('.data-desc .data').html(boldDom(hour_month)+'小时'+boldDom(minu_month)+'分钟');

            //highcharts
            var liveHour_month = Number(responseData.livedHourMonth);
            var onLiveHour_month = Number(responseData.noLivedHourMonth);

            if(liveHour_month >= 100){
                chartsData = [
                    {
                        y: liveHour_month
                    }
                ];
                chartsForHP('#dataForm',chartsData,'full')
            }else{
                chartsData = [
                    {
                        y: liveHour_month
                    },
                    {
                        color:'#eee',
                        y: onLiveHour_month
                    }
                ];
                chartsForHP('#dataForm',chartsData)
            }

            //今日直播时长
            var hour_today = string_fixed(responseData.livedTimeToday).hour;
            var minu_today = string_fixed(responseData.livedTimeToday).minu;
            var live_vail  = responseData.livedVaildDay;
            var live_max   = responseData.livedMaxVisit;
            //今日直播DOM
            $('#live_today').html(boldDom(hour_today)+'小时'+boldDom(minu_today)+'分钟');
            //本月有效天数
            $('#live_vaild').html(boldDom(live_vail)+'天');
            //本月人气峰值
            $('#live_max').html(boldDom(live_max));
            
            document.title = '主播资料';
            if(window.appSetTitle){
                window.appSetTitle('主播资料');
            }else if(window.phonePlus.appSetTitle){
                window.phonePlus.appSetTitle('主播资料');
            }

        })
    }else{

        ajaxRequest({url:requestUrl, data : requestData}, function (responseData) {

            setApplayStatus(responseData,uid,encpass);

            //本月直播时长
            var hour_month = string_fixed(responseData.livedTimeMonth).hour;
            var minu_month = string_fixed(responseData.livedTimeMonth).minu;
            $('.data-desc .data').html(boldDom(hour_month)+'小时'+boldDom(minu_month)+'分钟');

            //highcharts
            var liveHour_month = Number(responseData.livedHourMonth);
            var onLiveHour_month = Number(responseData.noLivedHourMonth);

            if(liveHour_month >= 100){
                chartsData = [
                    {
                        y: liveHour_month
                    }
                ];
                chartsForHP('#dataForm',chartsData,'full');
            }else{
                chartsData = [
                    {
                        y: liveHour_month
                    },
                    {
                        color:'#eee',
                        y: onLiveHour_month
                    }
                ];
                chartsForHP('#dataForm',chartsData);
            }
            //今日直播时长
            var live_vail  = responseData.livedVaildDay;
            var live_max   = responseData.livedMaxVisit;

            //本月有效天数
            $('#live_vaild').html(boldDom(live_vail)+'天');
            //本月人气峰值
            $('#live_max').html(boldDom(live_max));

        })
    }

}
function string_fixed(a){
    var str = a;
    var obj = {};
    var hourInx = str.indexOf('小时');
    obj.hour = str.substr(0, hourInx);
    var rep = new RegExp(obj.hour + '小时');
    var newStr = str.replace(rep,'');
    var minuInx = newStr.indexOf('分钟');
    obj.minu = newStr.substr(0, minuInx);
    return obj;
}
function boldDom(a){
    return '<span class="bold">'+a+'</span>';
}
function chartsForHP(selector,chartsData,sign){

    if(sign === 'full'){
        // radialGradient 渐变Init
        Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
            return {
                radialGradient: { cx: -0.1, cy: 0.5, r: 1.8},
                stops: [
                    [0, '#9850be'],
                    [1, Highcharts.Color('#00FFFF').brighten(0.3).get('rgb')]
                ]
            };
        });
    }else{
        // radialGradient 渐变Init
        Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
            return {
                radialGradient: { cx: 0.1, cy: 0.6, r: 0.7},
                stops: [
                    [0, 'darkorange'],
                    [1, Highcharts.Color('#ff7800').brighten(0.3).get('rgb')] // darken  ffb101
                ]
            };
        });
    }


    $(selector).highcharts({
        credits: {
            enabled:false
        },
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            spacing : [0, 0 , 0, 0]
        },
        title: {
            floating:true,
            text: ''
        },
        plotOptions: {
            pie: {
                size: 200,
                borderWidth:0,
                center:['50%','50%'],
                dataLabels: {
                    enabled: false //label 开关
                }
            }
        },
        series: [{
            type: 'pie',
            innerSize: '92.5%',
            data: chartsData
            // data: [
            //     {
            //         y: 86.0
            //     },
            //     {
            //         color:'#eee',
            //         y: 14.0
            //     }
            // ]
        }],
        tooltip: {
            enabled:false
        }
    });
}

/**
 * by fanguang 2017/07/17
 * @method 处理经纪公司签约状态
 * @params { Object,String,String } data:主播时长接口返回数据
 *     
 * @return null
 */
 function setApplayStatus(data,uid,encpass) {
    console.log(data);

    var statusObj = {
        cname: data.cname,
        // -1：去签约  0：审核中可以取消  1：审核中不可以取消  2：未通过  3：通过
        applyStatus: data.applyStatus,  
        aid: data.aid,
        reason: data.reason,
        uid: uid,
        encpass: encpass,
        cid: data.cid,
        videoId: '',
        videoTitle: '未选择'
    }
    $('#applyStatus').find('.status').text(statusObj.cname);
    for(var key in statusObj) {
        sessionStorage.setItem(key,statusObj[key]);
    }
 } 

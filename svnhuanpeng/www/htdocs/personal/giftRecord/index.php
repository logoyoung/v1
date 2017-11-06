<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/26
 * Time: 上午11:41
 */
require_once('../../../include/init.php');
include_once WEBSITE_PERSON . "isLogin.php";

$year = date('Y');
$month = date('m');

if ((int)$_GET['year'] >= 2015 and (int)$_GET['year'] <= date('Y')) {
    $year == (int)$_GET['year'];
}
if ((int)$_GET['month'] and (int)$_GET['month'] > 0 and (int)$_GET['month'] <= 12) {
    if (!($year == date('Y') and $_GET['month'] > date('m'))) {
        $month = $_GET['month'];
    }
}

for ($i = 2015; $i <= date('Y'); $i++) {
    $yearlist[] = $i;
}
for ($i = 1; $i <= date('m'); $i++) {
    $monthlist[] = $i < 10 ? "0$i" : $i;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>送礼记录-欢朋直播-精彩手游直播平台！</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <?php include WEBSITE_TPL . 'commSource.php'; ?>
    <link rel="stylesheet" type="text/css" href="<?php echo STATIC_CSS_PATH; ?>person.css?v=1.0.5">
<!--    <link rel="stylesheet" type="text/css" href="../property/property.css"/>-->
    <style>
        body {
            background-color: #eeeeee;
        }

        #gift-record {
            padding: 0px 20px;
        }

        #gift-record .gift-record-content {
            padding: 0px 50px;
        }

        .table-option {
            border-bottom: 1px solid #e0e0e0;
            height: 57px;
        }

        .table-option .table-option-right {
            float: right;
        }

        .today-cost {
            margin-top: 36px;
        }

        .today-cost span {
            float: left;
            font-size: 14px;
            line-height: 24px;
        }

        .today-cost .personal_icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .today-cost .label {
            font-size: 16px;
            margin-right: 20px;
        }

        .today-cost .num {
            color: #ff7800;
            margin-left: 20px;
        }

        .coin-table {
            margin-top: 30px;
            margin-bottom:30px;
        }

        .bean-table {
            margin-top: 30px;
            margin-bottom:30px;
        }

        .gift-record-content .table thead th {
            height: 40px;
            text-align: left;
            padding-left: 30px;
            border: 1px solid #e0e0e0;
            font-size: 16px;
            font-weight: normal;
        }

        .gift-record-content .table tbody tr td {
            height: 60px;
            text-align: left;
            padding-left: 30px;
            border: 1px solid #e0e0e0;
        }

        .gift-record-content .table tbody td img {
            vertical-align: middle;
            margin-right: 10px;
            width: 38px;
            height: 38px;
        }


    </style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>
    new head(null);
</script>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
    <div class="content">
        <div id="gift-record">
            <p class="page-title">送礼记录</p>

            <div class="gift-record-content">
                <div class="row-fluid today-cost">
                    <span class="label" for="">今日赠送</span>
                    <span class="personal_icon hpcoin"></span>
                    <span>欢朋币</span>
                    <span class="coin-num num mr-50">0</span>
                    <span class="personal_icon hpbean"></span>
                    <span>欢朋豆</span>
                    <span class="bean-num num">0</span>

                    <div class="clear"></div>
                </div>
                <div class="table-option mt-45">
                    <ul class="table-option-left select_tab left">
                        <li class="selected">欢朋币</li>
                        <li>欢朋豆</li>
                    </ul>
                    <div class="table-option-right select-time">
                        <label for="">查询时间</label>
                        <select name="" id="selectyear">
                            <?php
                            foreach ($yearlist as $k => $v) {
                                echo "<option value=" . $v . ">" . $v . "</option>";
                            }
                            ?>
                        </select>
                        <span class="year-lab">年</span>
                        <select name="" id="selectmonth">
                            <?php
                            foreach ($monthlist as $k => $v) {
                                echo "<option value=" . $v . ">" . $v . "</option>";
                            }
                            ?>
                        </select>
                        <span class="month-lab">月</span>
                        <button id="query-submit">查询</button>
                    </div>
                    <div class="clear"></div>
                </div>
                <table class="coin-table table">
                    <thead>
                    <tr>
                        <th>送礼时间</th>
                        <th>收礼人</th>
                        <th>礼物和数量</th>
                        <th>房间号</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                <table class="bean-table table none">
                    <thead>
                    <tr>
                        <th>送礼时间</th>
                        <th>收礼人</th>
                        <th>礼物和数量</th>
                        <th>房间号</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
</body>
<script type="text/javascript">
    personalCenter_sidebar('giftHistory');
    !function () {
        $("#selectyear").val("<?php echo $year; ?>");
        $("#selectmonth").val("<?php echo $month; ?>");

        $('#selectyear').change(function () {
            var date = new Date();
            var year = $(this).val();
            var month = 12;
            if (year == date.getFullYear()) {
                month = date.getMonth() + 1;
            }

            $('#selectmonth option').remove();

            for (var i = 1; i <= month; i++) {
                var theMonth = i < 10 ? '0' + i : i;
                $('#selectmonth').append('<option value=' + theMonth + '>' + theMonth + '</option>');
            }

            $('#selectmonth').val(theMonth);
        });
    }();

    $(document).ready(function () {
        !function () {

            var requestUrl = $conf.api +'gift/api_getTodayCost.php';
            var requestData = {uid:getCookie('_uid'), encpass:getCookie('_enc')};
            ajaxRequest({url:requestUrl,data:requestData},function(responseData){
                $('.coin-num').text(parseInt(responseData.hpcoin) || 0);
                $('.bean-num').text(parseInt(responseData.hpbean) || 0);
            });
//            $.ajax({
//                url: 'api_getTodayCost.php',
//                type: 'post',
//                dataType: 'json',
//                data: {
//                    uid: getCookie('_uid'),
//                    encpass: getCookie('_enc')
//                },
//                success: function (d) {
//                    $('.coin-num').text(parseInt(d.hpCoin) || 0);
//                    $('.bean-num').text(parseInt(d.hpBean) || 0);
//                }
//            });
        }();

        var img = {
            31: '../../static/img/gift/gift-1.png',
            32: '../../static/img/gift/gift-2.png',
            33: '../../static/img/gift/gift-3.png',
            34: '../../static/img/gift/gift-4.png',
            35: '../../static/img/gift/gift-5.png'
        }

        var size = 10;
        var type = 0;
        var sendRecordUrl = $conf.api + 'gift/api_sendRecord.php';

        !function () {

            function initRecordList(d) {
                var htmlStr = '';
                for (var i in d) {
                    htmlStr += '<tr><td>' + d[i].ctime + '</td><td>' + d[i].nick + '(' + d[i].uid + ')</td> <td><img src="' + img[d[i].giftID] + '" alt=""/>' + d[i].giftName + 'x' + d[i].giftNum + '</td> <td>' + d[i].roomID + '</td> </tr>';
                }
                $('.gift-record-content .table').eq(type).find('tbody').html(htmlStr);
            }

            function pageCallBackFunction(page) {
                var year = $('#selectyeasr').val();
                var month = $('#selectmonth').val();
                var getType = type == 0 ? 'coin' : 'bean';
                var requestUrl = sendRecordUrl;
                var requestData = {
                    uid:getCookie('_uid'),
                    encpass:getCookie('_enc'),
                    year: year,
                    month: month,
                    page: page,
                    size: size,
                    type: getType

                };
                ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                   initRecordList(responseData.list);
                },function(responseData){

                });

//
//                $.ajax({
//                    url: sendRecordUrl,
//                    type: 'post',
//                    dataType: 'json',
//                    data: {
//                        uid: getCookie('_uid'),
//                        encpass: getCookie('_enc'),
//                        year: year,
//                        month: month,
//                        page: page,
//                        size: size,
//                        type: getType
//                    },
//                    success: function (d) {
//                        initRecordList(d.list);
//                    }
//                });
            }

            function initPageCode(allCount) {
                if (allCount > size) {
                    var pageCount = parseInt(allCount / size);
                    if (allCount % size != 0) {
                        pageCount += 1;
                    }
                    $('.pageIndex').remove();
                    $('#gift-record').append('<div class="pageIndex"></div>');
                    $('.pageIndex').createPage({
                        pageCount: pageCount,
                        backFn: function (page) {
                            pageCallBackFunction(page);
                        }
                    });
                } else {
                    $('.pageIndex').remove();
                }
            }

            function initRecord(d) {
                initRecordList(d.list);
                initPageCode(d.total);
            }

            function requestRecord() {
                var year = $("#selectyear").val();
                var month = $('#selectmonth').val();
                var getType = type == 0 ? 'coin' : 'bean';
                var requestUrl = sendRecordUrl;
                var requestData = {
                    uid:getCookie('_uid'),
                    encpass:getCookie('_enc'),
                    year: year,
                    month: month,
                    page: 1,
                    size: size,
                    type:getType
                };
                ajaxRequest({url:requestUrl,data:requestData},function(responseData){
                    initRecord(responseData);
                });
//                $.ajax({
//                    url: sendRecordUrl,
//                    type: 'post',
//                    dataType: 'json',
//                    data: {
//                        uid: getCookie('_uid'),
//                        encpass: getCookie('_enc'),
//                        year: year,
//                        month: month,
//                        page: 1,
//                        size: size,
//                        type: getType
//                    },
//                    success: function (d) {
//                        initRecord(d);
//                    }
//                });
            }

            requestRecord();

            $('.select_tab li').click(function () {
                $('.select_tab li').removeClass('selected');
                $('.gift-record-content .table').addClass('none');

                var i = $(this).index();
                type = i;
                $(this).addClass('selected');
                $('.gift-record-content .table').eq(i).removeClass('none');
                requestRecord();
            });

            $('#query-submit').bind('click', function () {
                requestRecord();
            });
        }();
    });
</script>
</html>

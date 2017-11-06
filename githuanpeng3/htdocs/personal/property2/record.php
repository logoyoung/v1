<html>
<head>
    <meta charset='utf-8'>
    <title>个人中心-欢朋直播-精彩手游直播平台！</title>
    <link rel="stylesheet" type="text/css" href="../../static/css/common.css?v=1.0.4">
    <link rel="stylesheet" type="text/css" href="../../static/css/home.css?v=1.0.4">
    <link rel="stylesheet" type="text/css" href="../../static/css/person.css?v=1.0.5">
    <script type="text/javascript" src="../../static/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="../../static/js/common.js?v=1.0.4"></script>
	<script type="text/javascript" src="../../static/js/page.js?v=1.0.4"></script>
	<script type="text/javascript" src="../../static/js/home_data_load.js?v=1.0.4"></script>
    <style type="text/css">
        body{
            background-color: #eeeeee;
        }
        #exchangeCon{
            padding: 0px 20px;
        }
        #exchangeCon .navigation{
            padding: 0px 10px;
            font-size: 18px;
            color: #333333;
        }
        #exchangeCon .table thead tr {
            height: 40px;
            border-style: solid none;
            border-width: 1px;
            border-color: #cccccc;
            font-weight: bold;
        }
        #exchangeCon .table tbody{
            font-size: 14px;
        }
        #exchangeCon .table tbody .date{
            width: 180px;
        }
        #exchangeCon .table tbody .hpb{
            width: 210px;
        }
        #exchangeCon .table tbody .rmb{
            width: 170px;
        }
        #exchangeCon .table tbody .status{
            width: 200px;
        }
    </style>
</head>
<body>
<?php  include '../../header.html'; ?>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
    <div class="content">
        <div id="exchangeCon">
            <div class="row-fluid navigation mt-45">
                <a href="index.php">我的财产</a>
                <span>></span>
                <a href="">兑换纪录</a>
            </div>
            <div class="row-fluid billrecord mt-20">
                <table class="table">
                    <thead>
                        <tr>
                            <th>日期</th>
                            <th></th>
                            <th>兑换欢朋币</th>
                            <th>人民币</th>
                            <th>状态</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="date">2015</td>
                            <td class=""></td>
                            <td class="hpb">100000</td>
                            <td class="rmb">500</td>
                            <td class="status">兑换成功</td>
                        </tr>
                        <tr>
                            <td class="date">2015</td>
                            <td class=""></td>
                            <td class="hpb">200000000</td>
                            <td class="rmb">1000</td>
                            <td class="status">兑换成功</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
	var page=new page();
	page.init();
	personalCenter_sidebar('property');

	(function(){
		$.ajax({
			url:'getExchangeRecord_ajax.php',
			type:'post',
			dataType:'json',
			data:{
				uid:getCookie('_uid'),
				encpass:getCookie('_enc')
			},
			success:function(d){
				if(d.exchangeList){
					var list = d.exchangeList;
					console.log(list);
					var htmlstr = '';
					for(var i in list){
						htmlstr += '<tr><td class="date">'+list[i].date+'</td><td class=""></td><td class="hpb">'+list[i].hpcoin+'</td><td class="rmb">'+list[i].rmb+'</td><td class="status">兑换成功</td> </tr>';
					}
					$(".table tbody").html(htmlstr);
				}
			}
		})
	}());
</script>
</html>
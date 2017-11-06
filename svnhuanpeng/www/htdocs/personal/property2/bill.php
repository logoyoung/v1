<html>
<head>
    <title>个人中心-欢朋直播-精彩手游直播平台！</title>
    <meta charset='utf-8'>
    <link rel="stylesheet" type="text/css" href="../../static/css/common.css">
    <link rel="stylesheet" type="text/css" href="../../static/css/home.css">
    <link rel="stylesheet" type="text/css" href="../../static/css/person.css?v=1.0.5">
    <script type="text/javascript" src="../../static/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="../../static/js/common.js"></script>
	<script type="text/javascript" src="../../static/js/page.js"></script>
	<script type="text/javascript" src="../../static/js/home_data_load.js"></script>
    <style type="text/css">
        body{
            background-color: #eeeeee;
        }
        #billCon{
            padding: 0px 20px;
        }
        #billCon .navigation{
            padding: 0px 10px;
            font-size: 18px;
            color: #333333;
        }
        #billCon .table thead tr {
            height: 40px;
            border-style: solid none;
            border-width: 1px;
            border-color: #cccccc;
            font-weight: bold;
        }
        #billCon .table tbody{
            font-size: 14px;
        }
        #billCon .table tbody .date{
            width: 180px;
        }
        #billCon .table tbody .desc{
            width: 118px;
        }
        #billCon .table tbody .income{
            color:#039be5;
        }
        #billCon .table tbody .exchange{
            color:#e53935;
        }
        #billCon .table tbody .billtype{
            width: 243px;
        }

    </style>
</head>
<body>
<?php  include '../../header.html'; ?>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
    <div class="content">
        <div id="billCon">
            <div class="row-fluid navigation mt-45">
                <a href="index.php">我的财产</a>
                <span>></span>
                <a href="">账单</a>
            </div>
            <div class="row-fluid billrecord mt-20">
                <table class="table">
                    <thead>
                        <tr>
                            <th>日期</th>
                            <th></th>
                            <th>描述</th>
                            <th>收入或提现</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="date">2015</td>
                            <td class=""></td>
                            <td class="desc income">收入</td>
                            <td class="billtype income">500</td>
                        </tr>
                        <tr>
                            <td class="date">2015</td>
                            <td class=""></td>
                            <td class="desc exchange">提现</td>
                            <td class="billtype exchange">1000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	var page=new page();
	page.init();

	personalCenter_sidebar('property');
	(function(){
		$.ajax({
			url:'getBillRecord_ajax.php',
			type:'post',
			dataType:'json',
			data:{
				uid:getCookie('_uid'),
				encpass:getCookie('_enc')
			},
			success:function(d){
				if(d.billRecordList){
					var list = d.billRecordList;
					console.log(list);
					var htmlstr = '';
					for(var i in list){
						htmlstr += '<tr> <td class="date">'+list[i].date+'</td> <td class=""></td>';
						if(list[i].type == 3){
							htmlstr +=	'<td class="desc income">收入</td><td class="billtype income">'+ list[i].cash+'</td></tr>';
						}else{
							htmlstr += '<td class="desc exchange">提现</td><td class="billtype exchange">'+ list[i].cash+'</td></tr>';
						}
					}
					$(".table tbody").html(htmlstr);
				}
			}
		})
	}());
</script>
</body>
</html>
<?php

include __DIR__."/../../../include/init.php";

use lib\Finance;


$db  = new DBHelperi_huanpeng();
$finance = new Finance();


$table = "hpf_guarantee_201708";

$sql = "select * from $table where (status=1 or status=100) and real_pay != pay";

$res = $db->query($sql);

$reissueDetailInfo = [];

$finishOrderid = [
	150388671355055638,
	150366753901482204
];

while($row = $res->fetch_assoc())
{
	$tuid = $row['tuid'];
	$pay = $row['pay'];
	
	$rate = $finance->getRate($tuid, 10);
	$rate = bcmul( $rate, Finance::RATE_HB_GB, 3 );
	
	$income = bcmul( $pay, $rate );

	$id = $row['id'];

	if( $income != $row[ 'income' ] )
	{

		if( in_array($id, $finishOrderid))
		{
			continue;
		}

		echo "==============$id============\n";
		echo "pay:$pay | rate:$rate | income:{$row['income']} | fixIncome:$income\n";

		
		if($row['status'] == 100)
		{
			$reissue = $income - $row['income'];
			//todo reissue the income
			echo "reissue \n";
			echo "before:{$row['income']} after $income reissue income $reissue \n\n";

			$desc = [
				'ctime' => $row['ctime'],
				'cause' => "约玩订单补发",
				'guarantee_order_id' => $id,
				'before' => $row['income'],
				'after' => $income,
				'reissue' => $reissue
			];

			$desc = json_encode( $desc );

			// $result = $finance->innerRecharge($tuid, 0 , $reissue/1000, 0, 0, 0, $desc, $id );
			// if( Finance::checkBizResult( $result ) )
			// {
			// 	$reissueDetailInfo[$id] = $tuid;

			// 	echo "successful.\n\n";
			// }
			// else
			// {
			// 	echo "failed.\n\n";
			// }
		}
		else
		{
			echo "fix \n";
			echo "fix guarantee income {$row['income']} to $income \n";
			// $result = hp_fix_updateGuaranteeIncome($id, $income, $table, $db);

			// if( $result )
			// {
			// 	echo "successful.\n\n";

			// }
			// else
			// {
			// 	echo "failed.\n\n";
			// }
		}
	}
}

echo json_encode($reissueDetailInfo);

function hp_fix_updateGuaranteeIncome( $id, $income, $table, $db )
{
	$sql = "update $table set income=$income where id=$id";
	$res = $db->query($sql);
	
	return $res;
	// var_dump($sql."===>query result".$res.", affect_rowis ====>").$db->affectedRows;
}
<?php

include __DIR__."/../../../include/init.php";

use lib\Finance;

$db = new DBHelperi_huanpeng(true);

$table = "hpf_guarantee_201708";

$sql = "select * from $table where income=0";

$finance = new Finance();

$res = $db->query($sql);

while ($row = $res->fetch_assoc()) 
{
	$tuid = $row['tuid'];
	$pay = $row['pay'];
	var_dump($pay);
	$rate = $finance->getRate($tuid, 10);
	$rate = bcmul( $rate, Finance::RATE_HB_GB, 3 );
	var_dump($rate);
	$income = bcmul( $pay, $rate );

	$id = $row['id'];

	hp_fix_updateGuaranteeIncome($id, $income, $table, $db);
}

function hp_fix_updateGuaranteeIncome( $id, $income, $table, $db )
{
	$sql = "update $table set income=$income where id=$id";
	$res = $db->query($sql);

	var_dump($sql."===>query result".$res.", affect_rowis ====>").$db->affectedRows;
}
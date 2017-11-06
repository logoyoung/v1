<?php
  
include '../include/lib/GiftTable.php';
 
$data = new GiftTable();
var_dump($data);exit;
echo $data->getTableSuffix();
//$data->checkTable(1);
?>
<?php

namespace Common\Model;

class WithdrawModel extends WithdrawBaseModel
{
	public function __construct($tablename,$date){
		$month = date("Ym",strtotime($date));
		$this->trueTableName  = $tablename."_".$month;
		parent::__construct();
	}
	/*protected $trueTableName = 'exchange_detail';
	public function selectTable($date){
		$month = date("Ym",strtotime($date));
		$this->trueTableName = $this->trueTableName . '_' . $month;
		return $this;
	}*/
	//提现状态 2:审核 3:审核通过 4:审核未通过
	public  function getCheckstatus($index = false)
	{
		$hash = [2=>'待审核',3=>'审核通过',4=>'审核未通过'];
		return $index === false?$hash:$hash[$index];
	}
	public static function getStatus($index = false)
	{
		$hash = ['check'=>'2','pass'=>'3','unpass'=>'4'];
		return $index === false?$hash:$hash[$index];
	}
}

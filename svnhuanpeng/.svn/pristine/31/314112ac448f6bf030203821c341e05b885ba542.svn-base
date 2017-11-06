<?php

namespace Common\Model;

class LivestatisModel extends WithdrawBaseModel
{
	protected $trueTableName = 'admin_statisticslive';

	public function getStatus( $index = false ){
		$hash = ['day'=>1,'game'=>2,'twohour'=>3];
		return $index === false?$hash:$hash[$index];
	}
}

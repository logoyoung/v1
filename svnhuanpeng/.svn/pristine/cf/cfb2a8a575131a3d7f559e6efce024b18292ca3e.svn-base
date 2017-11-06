<?php

namespace Common\Model;

class RecommendLiveWaitModel extends PublicBaseModel
{
	protected $trueTableName = 'admin_recommend_live';
	public function getClient($index = false)
	{
		$hash = ['untop'=>'0','top'=>'1'];
		return $index === false?$hash:$hash[$index];
	}
}

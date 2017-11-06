<?php

namespace Common\Model;

class AnchorModel extends PublicBaseModel
{
	public function getOrder($index = false)
    {
        $hash = ['1'=>'直播时长 降序',
				'2'=>'直播时长 升序',
				'3'=>'金币收入 降序',
				'4'=>'金币收入 升序',
				'5'=>'有效天数 降序',
				'6'=>'有效天数 升序'];
        return $index === false?$hash:$hash[$index];
    }
    
	public function getOrderSql($index = false )
    {
        $hash = ['1'=>'length desc',
				'2'=>'length asc',
				'3'=>'coin desc',
				'4'=>'coin asc',
				'5'=>'valid desc',
				'6'=>'valid asc'];
        return $index === false?$hash:$hash[$index];
    }
    
    public function getAnchorStatus($index = false )
    {
        $hash = ['1'=>'正常',
				'2'=>'已禁播'];
        return $index === false?$hash:$hash[$index];
    }
}

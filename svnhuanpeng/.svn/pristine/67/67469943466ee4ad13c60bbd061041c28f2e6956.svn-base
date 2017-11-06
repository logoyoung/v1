<?php

namespace Common\Model;

class CompanyModel extends PublicBaseModel
{
    public function getStatus($index = false)
    {
        $hash = ['0'=>'正常','1'=>'合同终止'];
        return $index === false?$hash:$hash[$index];
    }
    public function getType($index = false)
    {
        $hash = ['0'=>'官方','1'=>'经纪公司','2'=>'工会'];
        return $index === false?$hash:$hash[$index];
    }
    public function getRate($index = false)
    {
        $hash = ['base'=>BASE_RATE,'official'=>OFFICIAL_RATE,'other'=>OTHER_RATE];//普通主播默认比率  平台签约主播主播默认比率  经纪公司，工会默认比率
        return $index === false?$hash:$hash[$index];
    }
    public function getOrder($index = false)
    {
        $hash = ['1'=>'主播人数 降序','2'=>'主播人数 升序','3'=>'金币收入 降序','4'=>'金币收入 升序'];
        return $index === false?$hash:$hash[$index];
    }
	public function getOrderSql($index = false )
    {
        $hash = ['1'=>'companypeople desc,coin desc',
				'2'=>'companypeople asc,coin desc',
				'3'=>'coin desc,companypeople desc',
				'4'=>'coin asc,companypeople desc'];
        return $index === false?$hash:$hash[$index];
    }
}

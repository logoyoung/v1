<?php

namespace Common\Model;

class AnchorStatisModel extends BaseModel
{
    protected $record_write = true;
    
    public function getOrder($index = false)
    {
    	$hash = ['1'=>'粉丝人数  降序',
    			'2'=>'粉丝人数 升序',
    			'3'=>'新增粉丝人数 降序',
    			'4'=>'新增粉丝人数 升序'];
    	return $index === false?$hash:$hash[$index];
    }
    
    public function getOrderSql($index = false )
    {
    	$hash = ['1'=>'fans desc',
    			'2'=>'fans asc',
    			'3'=>'newfans desc',
    			'4'=>'newfans asc'];
    	return $index === false?$hash:$hash[$index];
    }
}

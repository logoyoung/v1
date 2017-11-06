<?php

namespace Common\Model;

class PromocodeModel extends BaseModel
{
    public function getStatus($index = false)
    {
        $hash = ['0'=>'无效','1'=>'有效'];
        return $index === false?$hash:$hash[$index];
    }
    public function getVips($index = false)
    {
        $hash = ['0'=>'非重点合作','1'=>'重点合作'];
        return $index === false?$hash:$hash[$index];
    }

}

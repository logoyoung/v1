<?php

namespace Common\Model;

class ChannelVersionModel extends BaseModel
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
    public function getClient($index = false)
    {
        $hash = ['1'=>'android','2'=>'客户端','3'=>'ios','4'=>'官网','5'=>'H5'];
        return $index === false?$hash:$hash[$index];
    }

}

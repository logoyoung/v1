<?php

namespace Common\Model;

class InternalDistributionRecordModel extends PublicBaseModel
{
    public function getType($index = false)
    {
        $hash = ['101'=>'内币发放','110'=>'活动发放'];
        return $index === false?$hash:$hash[$index];
    }
    public function getActive($index = false)
    {
        $hash = ['1001'=>'运营内币发放','2001'=>'封测活动','2002'=>'王者荣耀Solo活动'];
        return $index === false?$hash:$hash[$index];
    }
}

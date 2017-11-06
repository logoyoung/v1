<?php

namespace Common\Model;

class HPFMonthModel extends HPFBaseModel
{
    public function __construct($tablename,$date){
        $month = date("Ym",strtotime($date));
        $this->trueTableName  = "hpf_".$tablename."_".$month;
        parent::__construct();
    }    
}

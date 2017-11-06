<?php

namespace Common\Model;

class PublicBaseModel extends \Think\Model
{
    protected $record_write = false;

    protected function _after_insert($data,$options){
        if($this->record_write){
            $this->record_log(func_get_args());
        }
    }
    protected function _after_update($data,$options){
        if($this->record_write){
            $this->record_log(func_get_args());
        }
    }
    protected function _after_delete($data,$options){
        if($this->record_write){
            $this->record_log(func_get_args());
        }
    }
    
    protected function record_log($args){
        if(!IS_CLI){
            \Think\Log::write($this->getTableName().'##'.MODULE_NAME.'##U:'.get_uid().'##'.json_encode($args),\Think\Log::INFO,'',C('HP_LOG_PATH').'/recordwrite/'.date('Ymd').'.log');
        }
    }
}

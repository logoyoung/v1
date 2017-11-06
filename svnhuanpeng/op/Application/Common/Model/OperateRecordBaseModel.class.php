<?php

namespace Common\Model;

class OperateRecordBaseModel extends \Think\Model
{
	protected function record_log($args){
		if(!IS_CLI){
			\Think\Log::write($this->getTableName().'##'.MODULE_NAME.'##U:'.get_uid().'##'.json_encode($args),\Think\Log::INFO,'',C('HP_LOG_PATH').'/recordwrite/'.date('Ymd').'.log');
		}
	}
}

<?php
// +----------------------------------------------------------------------
// | Op Log
// +----------------------------------------------------------------------
namespace HP\Log;

class Log {
    static public function system($msg,$level=null)
    {
        $func = __FUNCTION__;
        if(IS_CLI){
            $msg = '['.__ACTION__.']'.$msg;
            $func = 'cli'.$func;
        }
        return \Think\Log::write($msg, $level?$level:\Think\Log::INFO,'',C('HP_LOG_PATH').$func.'/'.date('Ymd').'.log');
    }
    static public function project($msg,$level=null)
    {
        return \Think\Log::write($msg, $level?$level:\Think\Log::INFO,'',C('HP_LOG_PATH').__FUNCTION__.'/'.date('Ymd').'.log');
    }

    static public function api($msg,$level=null)
    {
        return \Think\Log::write($msg, $level?$level:\Think\Log::INFO,'',C('HP_LOG_PATH').__FUNCTION__.'/'.date('Ymd').'.log');
    }
    static public function statis($msg,$level=null,$pathname='')
    {
		if($pathname){
			return \Think\Log::write($msg, $level?$level:\Think\Log::INFO,'',C('HP_LOG_PATH').__FUNCTION__.'/'.$pathname.date('Ymd').'.log');
		}else{
			return \Think\Log::write($msg, $level?$level:\Think\Log::INFO,'',C('HP_LOG_PATH').__FUNCTION__.'/'.date('Ymd').'.log');
		}
    }
	static public function write($msg,$level=null)
	{
		return \Think\Log::write($msg, $level?$level:\Think\Log::INFO,'',C('HP_LOG_PATH').__FUNCTION__.'/'.date('Ymd').'.log');
	}
	static public function secure($msg,$level=null)
    {
        return \Think\Log::write($msg, $level?$level:\Think\Log::INFO,'',C('XDD_LOG_PATH').__FUNCTION__.'/'.date('Ymd').'.log');
    }
}
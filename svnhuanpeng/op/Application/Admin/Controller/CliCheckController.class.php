<?php

namespace Admin\Controller;
use HP\Log\Log;
use HP\Op\Check;
use HP\Op\Admin;
class CheckTestController extends BaseController
{
	protected $pageSize = 10;
	protected function _access(){
		return self::ACCESS_NOLOGIN;
	}
	public function livelengthcheck($start,$end,$do=''){
		$liveDao = D('live');
		$liveLenDao = D('liveLength');
		//$where = [];
		$date = '';
		for ($date=$start;strtotime($date)<strtotime($end);$date=date('Y-m-d',strtotime('1 day',strtotime($date)))){
			echo "$date\n";
		}
	}
}

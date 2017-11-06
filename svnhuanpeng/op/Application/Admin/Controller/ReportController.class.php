<?php

namespace Admin\Controller;

use Common\Model\SalaryModel;
use HP\Op\Statis;
use HP\Op\Anchor;
use HP\Op\Company;


class ReportController extends BaseController
{

	protected $pageSize = 20;

	public function _access()
	{
		return [
			'index' => [ 'index' ]
		];
	}


	public function index()
	{
		I( "get.timestart" ) ? $stime = I( "get.timestart" ) : $stime = date( "Y-m-d", strtotime( get_date()) );
		I( "get.timeend" ) ? $etime = I( "get.timeend" ) : $etime = date( "Y-m-d", strtotime( get_date() ) );
		$timestart=$stime;
		$timeend=$etime;
		$stime = $stime . ' 00:00:00';
		$etime = $etime . ' 23:59:59';
		$where=1;
		$Dao=D('report');
		$total = $Dao->where( $where )->count();
		$Page = new \HP\Util\Page( $total, $this->pageSize );
		if( $total )
		{
			$list = $Dao->where( $where )->order( 'ctime desc ' )->limit( $Page->firstRow . ',' . $Page->listRows )->select();
		}
		else
		{
			$list = array();
		}

//		$Page = new \HP\Util\Page( count( $total ), $this->pageSize );
	    $this->data=$list;
		$this->page = $Page->show();
		$this->display();
	}



}

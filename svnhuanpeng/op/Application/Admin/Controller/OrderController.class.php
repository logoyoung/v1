<?php

namespace Admin\Controller;

use Common\Model\SalaryModel;
use HP\Op\Statis;
use HP\Op\Anchor;
use HP\Op\Game;


class OrderController extends BaseController
{

	protected $pageSize = 10;
	protected $orderStatus = array(
//		'0' => '未知状态',
		'1' => '进行中',
		'2' => '已取消',
		'3' => '申诉中',
		'4' => '已完成'

	);
	protected $wstatus = array(
		'1' => "10,50,70,80,90,110,150,170,140",
		'2' => "20,30,40,60,100,130,160,1010",
		'3' => "120",
		'4' => "1000"
	);

	protected $ishow = array(
		'-1' => '异常订单关闭',
		'0' => '创建',
		'10' => '创建订单',
		'30' => '用户取消订单',
		'40' => '拒绝订单',
		'50' => '接受订单',
		'60' => '主播取消订单',
		'80' => '用户完成订单',
		'90' => '用户申请退单',
		'100' => '主播同意退单',
		'110' => '主播不同意退单',
		'120' => '用户申诉',
		'130' => '客服同意申诉',
		'140' => '客服不同意申诉',
		'20' => '超时',
		'70' => '系统确认',
		'150' => '超时',
		'160' => '超时',
		'170' => '超时',
		'1000' => '支付完成',
		'1010' => '退款完成',
		'1020' => '退款完成'
	);

	protected $detail = array(
		'-1' => '异常订单关闭',
		'0' => '创建',
		'10' => '创建订单',
		'30' => '用户取消掉订单(交易取消)',
		'40' => '拒绝订单(交易取消)',
		'50' => '接受订单',
		'60' => '主播取消掉订单(交易取消)',
		'80' => '用户完成订单',
		'90' => '用户申请退单',
		'100' => '主播同意退单(交易退单)',
		'110' => '主播不同意退单',
		'120' => '用户申诉到客服',
		'130' => '客服同意申诉(交易退单)',
		'140' => '客服不同意申诉(交易完成)',
		'20' => '创建订单后1小时不接单取消掉订单(交易取消)',
		'70' => '接单超时陪玩时间24小时后自动确认订单',
		'150' => '订单确认超过24小时(交易完成)',
		'160' => '订单退单超过24小时主播未处理(交易退单)',
		'170' => '主播拒绝退单后用户24小时没有申诉客服(交易完成)',
		'1000' => '结束订单,支付完成',
		'1010' => '取消订单,退款完成',
		'1020' => '回退订单,退款完成'
	);

	protected $unit = 1000;

	public function _access()
	{
		return [
			'index' => [ 'index' ],
			'orderlog' => [ 'index' ]

		];
	}

	/**
	 * 列表页面
	 */
	public function index()
	{
		if( $uid = I( 'get.uid' ) )
		{
			$where['cert_uid'] = $uid;
		}
		if( $userid = I( 'get.user_id' ) )
		{
			$where['uid'] = $userid;
		}
		if( $orderid = I( 'get.orderid' ) )
		{
			$where['order_id'] = [ 'like', "%$orderid%" ];
		}
		$status = I( "get.status" ) ? I( "get.status" ) : '';
		$stime = I( "get.timestart" ) ? I( "get.timestart" ) : date( 'Y-m-d', strtotime( '-7 day' ) );
		$etime = I( "get.timeend" ) ? I( "get.timeend" ) : date( 'Y-m-d', time() );

		if( $status )
		{
			$where['status'][] = ['in',$this->wstatus[$status]] ;
			$where['status'][] = ['NEQ','0'] ;
		}
		else
		{
			$where['status'][] = ['not in','0,-1'] ;
		}
		if( $stime )
		{
			$stime .= " 00:00:00";
			$where['ctime'][] = [ 'egt', $stime ];
		}
		if( $etime )
		{
			$etime .= " 23:59:59";
			$where['ctime'][] = [ 'elt', $etime ];
		}
		$Dao = D( 'due_order' );
		if( $export = I( 'get.export' ) )
		{//导出数据
			$list = $Dao
				->where( $where )
				->limit( '0,1000' )
				->order( 'ctime desc ' )
				->select();
			//导出日志信息
		    $orderlogdao = D( 'due_order_log' );
    	    $logs = $orderlogdao->where($where)->field("order_id,status,reason,ctime,uid")->group('order_id,status')->select();
    	    foreach ($logs as $log){
    	        $log['status'] = $this->detail[$log['status']] ? $this->detail[$log['status']] : '';
    	        $logstr = $log['uid'].":".$log['status'].":".$log['reason'].":".$log['ctime']."|" ;
    	        $loginfos[$log['order_id']] .= "$logstr";
    	    }
			
		}
		else
		{
			$total = $Dao->field( "amount,discount,real_amount" )->where( $where )->select();
			$sum = array( 'order' => count( $total ), 'pay' => array_sum( array_column( $total, 'amount' ) ) / $this->unit, 'discount' => array_sum( array_column( $total, 'discount' ) ) / $this->unit, 'real' => array_sum( array_column( $total, 'real_amount' ) ) / $this->unit );
			$Page = new \HP\Util\Page( count( $total ), $this->pageSize );
			$list = $Dao->where( $where )->order( 'ctime desc ' )->limit( $Page->firstRow . ',' . $Page->listRows )->select();
		}
		if( $list )
		{
			$luids = array_column( $list, 'cert_uid' );
			$uids = array_column( $list, 'uid' );
			$ids = array_merge( $luids, $uids );
			$userinfo = Anchor::anchorInfo( $ids );
			foreach ( $list as $k => $v )
			{
				$list[$k]['nick'] = $userinfo[$v['cert_uid']]['nick'];
				$list[$k]['usernick'] = $userinfo[$v['uid']]['nick'];
				$list[$k]['discount'] = $v['discount'] / $this->unit;
				$list[$k]['amount'] = $v['amount'] / $this->unit;
				$list[$k]['real_amount'] = $v['real_amount'] / $this->unit;
				$list[$k]['status'] = $this->ishow[$v['status']];
				if( $v['stime'] == '1971-01-01 01:01:01' )
				{
					$list[$k]['stime'] = '--';
				}
			}

			if( $export = I( 'get.export' ) )
			{//导出数据
				$excel[] = array( '订单号', '用户ID', '用户昵称', '主播ID', '主播昵称', '资质ID', '总价', '优惠金额', '实际付款', '订单状态', '有无评论','创建时间', '完成时间','流水日志' );
				foreach ( $list as $data )
				{
				    $loginfo = '"' . str_replace( array( ',', '&nbsp;', '<br>', '<br/>', '<br />' ), array( '，', ' ', PHP_EOL, PHP_EOL, PHP_EOL ), $loginfos[$data['order_id']] ) . '"';
					$excel[] = array( "\t" . $data['order_id'], $data['uid'], $data['usernick'], $data['cert_uid'], $data['nick'], $data['cert_id'], $data['amount'], $data['discount'], $data['real_amount'], $data['status'], $data['comment'],$data['ctime'], $data['stime'],$loginfo );
				}
				\HP\Util\Export::outputCsv( $excel, date( 'Y-m-d' ) . '订单列表' );
			}
		}
		$this->status = $this->orderStatus;
		$this->data = $list ? $list : array();
		$this->timestart = substr( $stime, 0, 10 );
		$this->timeend = substr( $etime, 0, 10 );
		$this->page = $Page->show();
		$this->sum = $sum;
		$this->display();
	}

	function orderlog()
	{
		$id = I( 'get.id' ) ? I( 'get.id' ) : '';
		if( $id )
		{
			$dao = D( 'due_order_log' );
			$data = $dao->where( "order_id=$id" )->order( "ctime asc" )->select();
			if( $data )
			{
				$usernick = Anchor::anchorInfo( array_column( $data, 'uid' ) );
				foreach ( $data as $k => $v )
				{
					if( $v['uid'] == '-1000' )
					{
						$data[$k]['nick'] = '系统操作';
					}
					else
					{
						$data[$k]['nick'] = $usernick[$v['uid']]['nick'] ? $usernick[$v['uid']]['nick'] : '';
					}
					$data[$k]['detail'] = $this->detail[$v['status']] ? $this->detail[$v['status']] : '';
				}
				if( $export = I( 'get.export' ) )
				{//导出数据
					$excel[] = array( '记录ID', '订单号', '订单状态', '状态描述', '原因', '操作者ID', '操作者昵称', '记录创建时间' );
					foreach ( $data as $v )
					{
						if( $v['detail'] )
						{
							$v['detail'] = '"' . str_replace( array( ',', '&nbsp;', '<br>', '<br/>', '<br />' ), array( '，', ' ', PHP_EOL, PHP_EOL, PHP_EOL ), $v['detail'] ) . '"';
						}
						if( $v['reason'] )
						{
							$v['reason'] = '"' . str_replace( array( ',', '&nbsp;', '<br>', '<br/>', '<br />' ), array( '，', ' ', PHP_EOL, PHP_EOL, PHP_EOL ), $v['reason'] ) . '"';
						}
						$excel[] = array( $v['id'], "\t" . $v['order_id'], $v['status'], "\t" . $v['detail'], "\t" . $v['reason'], $v['uid'], $v['nick'], "\t" . $v['ctime'] );
					}
					\HP\Util\Export::outputCsv( $excel, '订单详情' );
				}

			}
			else
			{
				$data = array();
			}
			$this->data = $data;
		}
		$this->display();

	}
}


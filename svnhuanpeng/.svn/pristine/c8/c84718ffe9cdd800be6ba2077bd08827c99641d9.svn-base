<?php
// +----------------------------------------------------------------------
// | Anchor Info
// +----------------------------------------------------------------------
// | Author: zwq
// +----------------------------------------------------------------------
namespace HP\Op;
use HP\Log\Log;

class Anchor extends \HP\Cache\Proxy
{


	/**批量获取用户昵称头像
	 *
	 * @param $uids  用户id列表
	 * @param $db
	 */
	static public function anchorInfo( $uids )
	{
		$db = D( 'userstatic' );
		if( is_array( $uids ) )
		{
			$uids = implode( ',', $uids );
			$where['uid'] = [ 'in', $uids ];
		}
		$res = $db->field( "uid,nick,pic" )->where( $where )->getField( 'uid,nick,pic,username' );
		return $res ? $res : array();
	}

	/**批量获取主播房间id
	 *
	 * @param $uids  主播id列表
	 *
	 * @return array|bool
	 */
	function anchorRoomID( $uids )
	{
		$db = D( 'roomid' );
		if( is_array( $uids ) )
		{
			$uids = implode( ',', $uids );
			$where['uid'] = [ 'in', $uids ];
		}
		$res = $db->field( "uid,roomid" )->where( $where )->getField( 'uid,roomid' );
		return $res ? $res : array();
	}

	/**批量获取主播在线时长及收入
	 *
	 * @param $uids
	 * @param $month 月份  2017-02
	 *
	 * @return array|bool
	 */
	static function anchorLiveLength( $uids = null, $monthstart = null, $monthend = null )
	{

		$db = D( 'liveLength' );
		if( is_array( $uids ) )
		{
			$uids = implode( ',', $uids );
			$where['uid'] = [ 'in', $uids ];
		}
		if( $monthstart )
		{
			$where['date'][] = [ 'egt', $monthstart ];
		}
		if( $monthend )
		{
			$where['date'][] = [ 'elt', $monthend ];
		}
		$res = $db->field( "uid,sum(length) as length,sum(bean) as bean,sum(coin) as coin" )
			->where( $where )
			->group( 'uid' )
			->getField( 'uid,sum(length) as length,sum(bean) as bean,sum(coin) as coin' );
		return $res ? $res : array();
	}

	/**根据主播id获取主播在线时长及收入
	 *
	 * @param $uid
	 * @param $month 月份  2017-02
	 *
	 * @return array|bool
	 */
	static function getLiveLengthByUid( $uid = null, $monthstart = null, $monthend = null )
	{
		$Dao = D( 'AnchorStatis' );
		$where['uid'] = $uid;
		if( $monthstart )
		{
			$where['date'][] = [ 'egt', $monthstart ];
		}
		if( $monthend )
		{
			$where['date'][] = [ 'elt', $monthend ];
		}
		$res = $Dao->field( "*,DATE_FORMAT(`date`, '%m-%d') as day" )->where( $where )->select();
		return $res ? $res : array();
	}

	/**根据主播id获取主播人气
	 *
	 * @param $uid
	 * @param $month
	 *
	 * @return array|bool
	 */
	static function getPopularByUid( $uid = null, $monthstart = null, $monthend = null )
	{
		$Dao = D( 'AnchorMostPopular' );
		$where['uid'] = $uid;
		if( $monthstart )
		{
			$where['ctime'][] = [ 'egt', $monthstart . ' 00:00:00' ];
		}
		if( $monthend )
		{
			$where['ctime'][] = [ 'elt', $monthend . ' 23:59:59' ];
		}
		$res = $Dao->field( "uid,max(popular) as popular,DATE_FORMAT(`ctime`, '%m-%d') as day" )
			->where( $where )
			->group( "DATE_FORMAT(`ctime`, '%Y%m%d')" )
			->select();

		return $res ? $res : array();
	}

	/**批量获取主播人气
	 *
	 * @param $uids
	 * @param $month
	 *
	 * @return array|bool
	 */
	static function anchorPopular( $uids = null, $monthstart = null, $monthend = null )
	{
		$db = D( 'AnchorMostPopular' );
		if( is_array( $uids ) )
		{
			$uids = implode( ',', $uids );
			$where['uid'] = [ 'in', $uids ];
		}
		if( $monthstart )
		{
			$where['ctime'][] = [ 'egt', $monthstart . ' 00:00:00' ];
		}
		if( $monthend )
		{
			$where['ctime'][] = [ 'elt', $monthend . ' 23:59:59' ];
		}
		$res = $db->field( "uid,max(popular) as popular" )
			->where( $where )
			->group( 'uid' )
			->getField( 'uid,max(popular) as popular' );
		return $res ? $res : array();
	}

	/**批量获取主播首播日期
	 *
	 * @param $uids  用户id列表
	 * @param $db
	 */
	static function anchorFirstDay( $uids )
	{
		$db = D( 'Live' );
		if( is_array( $uids ) )
		{
			$uids = implode( ',', $uids );
			$where['uid'] = [ 'in', $uids ];
		}
		$res = $db->field( "uid, min(ctime) as ctime" )
			->where( $where )
			->group( 'uid' )
			->getField( 'uid,min(ctime) as ctime' );
		return $res;
	}

	/**批量获取主播有效播出天数
	 *
	 * @param $uids  用户id列表
	 * @param $db
	 */
	static function anchorValidDay( $uids, $monthstart = null, $monthend = null )
	{
		$db = D( 'LiveLength' );
		if( is_array( $uids ) )
		{
			$uids = implode( ',', $uids );
			$where['uid'] = [ 'in', $uids ];
		}
		$where['length'] = [ 'egt', 3600 ];
		if( $monthstart )
		{
			$where['date'][] = [ 'egt', $monthstart . ' 00:00:00' ];
		}
		if( $monthend )
		{
			$where['date'][] = [ 'elt', $monthend . ' 23:59:59' ];
		}

		$res = $db->field( "uid, count(*) as total" )
			->where( $where )
			->group( 'uid' )
			->getField( 'uid, count(*) as total' );
		return $res ? $res : array();
	}

	/**批量获取主播身份信息
	 *
	 * @param $uids  主播id列表
	 *
	 * @return array
	 */
	static function anchorRealInfo( $uids )
	{
		$db = D( 'userrealname' );
		if( is_array( $uids ) )
		{
			$uids = implode( ',', $uids );
			$where['uid'] = [ 'in', $uids ];
		}
		$res = $db->field( "uid,name,papersid" )
			->where( $where )
			->getField( 'uid,name,papersid' );
		return $res ? $res : array();
	}

	/**
	 *
	 * @param $uids  主播id列表
	 *
	 * @return array
	 * 1.更新 company_anchor
	 * 2.更新 anchor 表 cid rate
	 * 3.增加 anchor_change_record 记录
	 * 4.增加 rate_change_record 记录
	 */

	static function setAnchorContract( $fcid = null, $data )
	{
		$anchorDao = D( 'anchor' );
		$anchordata['cid'] = $data['cid']?$data['cid']:0;
		$anchordata['rate'] = $data['rate']?$data['rate']:0;
		$anchordata['utime']= date('Y-m-d H:i:s');
		$before = $anchorDao->find( $data['uid'] );
		$changeAnchor=$anchorDao->where( [ 'uid' => $data['uid']] )->save( $anchordata );
		if($changeAnchor === false){
			return false;
		}
		$adminid = Admin::getUid();
		if( $fcid )
		{//修改。
			$adesc = '签约变更引起的角色变化';
			$rdesc = '签约变更的比率变化';
            
            //更改申请记录
            D('anchorapplycompany')->where(['uid'=>$data['uid'],'cid'=>$fcid,'status'=>4])->save(['status'=>6,'canceltime'=>get_date()]);
		}
		else
		{
			$adesc = '签约引起的角色变化';
			$rdesc = '签约引起的比率变化';
		}
		\HP\Log\Op::write( \HP\Log\Op::CHANGE_COMPANY, $data );
		//通知财务
		$role_change_id = addRoleChangeRecord( array( 'uid' => $data['uid'], 'before_cid' => $before['cid'], 'after_cid' => $data['cid'], 'adminid' => $adminid, 'desc' => $adesc ) );
		$list[$data['uid']] = addRateChangeRecord( array( 'uid' => $data['uid'], 'before_rate' => $before['rate'], 'after_rate' => $data['rate'], 'adminid' => $adminid, 'type' => '1', 'role_change_id' => $role_change_id, 'desc' => $rdesc ) );
		$res = publicRequist::outside_setRate( $list, $data['rate'], '完成签约' );//通知财务系统
		if( $res == 1 )
		{
			Log::statis(json_encode(array('list'=>$list,'res'=>json_encode($res))),'','changeRateSuccess');
			updateNoticStatus( $list[$data['uid']] );//是否通知到财务系统
		}
		else
		{
			Log::statis(json_encode(array('list'=>$list,'res'=>json_encode($res))),'','changeRateUnsuccess');
			unsuccessLogForFinanceBack( '完成签约比率改变 财务系统返回失败', array( 'financeBack' => $res, 'adminid' => $adminid, 'roleChangeid' => $role_change_id, 'rate' => $data['rate'], 'before' => $before, 'list' => $list ) );
		}
		return true;
	}


	static function due_rate_charge( $uid )
	{
		//陪玩比率
		$check=self::check_due_record_is_exist($uid);
		if(!$check){
		$cid = self::getCidByUid( $uid );
		if( $cid && ($cid != 15) )
		{
			$ctype = self::checkCompanyType( $cid );
			if( $ctype && ( $ctype[0]['type'] == 1 ) )
			{
				self::setDueContract( $uid, 70, $cid, Admin::getUid(), '陪玩' );
			}
		}
		}

	}

	function getCurrentTime ()  {
		list ($msec, $sec) = explode(" ", microtime());
		return (float)$msec + (float)$sec;
	}


	/**根据uid 获取cid
	 * @param $uid  主播uid
	 *
	 * @return bool
	 */
	function getCidByUid( $uid )
	{
		if( empty( $uid ) )
		{
			return false;
		}
		$dao = D( 'anchor' );
		$info = $dao->where( "uid=$uid" )->select();
		if( false !== $info )
		{
			return $info[0]['cid'];
		}
		else
		{
			return false;
		}
	}

	function check_due_record_is_exist($uid){
		$Dao = D( 'due_rate_change_record' );
		$info=$Dao->where("uid=$uid")->select();
		if($info){
			return true;
		}else{
			return false;
		}
	}

	function setDueContract( $uid, $rate, $cid, $adminid, $desc )
	{
		$rid = self::add_due_record( $uid, $rate, $cid, $adminid, $desc );
		if( $rid )
		{
			$res = json_decode( publicRequist::outside_dueRate( array( $uid => $rid ), $rate, $desc ), true );//通知财务系统
		}
		else
		{
			//TODO  记日志
			$res = array( 'status' => 0, 'content' => 'error to add_due_record ' );
		}
		$Dao = D( 'due_rate_change_record' );
		if( $res['status'] == 1 )
		{
			$Dao->where( "id=$rid" )->save( array( 'status' => 1 ) );
		}
		else
		{
			$Dao->where( "id=$rid" )->save( array( 'desc' => $res ) );
		}
	}

	/**  检测公司类型
	 *
	 * @param $cid  公司id
	 *
	 * @return bool|mixed
	 */
	function checkCompanyType( $cid )
	{
		$Dao = D( 'company' );
		$res = $Dao->where( "id=$cid" )->select();
		if( $res )
		{
			return $res;
		}
		else
		{
			return false;
		}
	}

	function add_due_record( $uid, $rate, $cid, $adminid, $desc )
	{
		$dao = D( 'due_rate_change_record' );
		$data = array(
			'uid' => $uid,
			'cid' => $cid,
			'rate' => $rate,
			'adminid' => $adminid,
			'desc' => $desc
		);
		$res = $dao->add( $data );
		if( $res )
		{
			return $res;
		}
		else
		{
			return false;
		}
	}


	static function pass_Role_change_Record( $adminid, $uid, $beforInfo, $db )
	{
		$data = array(
			'uid' => $uid,
			'before_cid' => (int)$beforInfo['cid'],
			'after_cid' => 0,
			'adminid' => $adminid,
			'desc' => '通过实名认证或取消签约'
		);
		$res = addRoleChangeRecord( $data, $db );
		if( false !== $res )
		{
			return $res;
		}
		else
		{
			return false;
		}
	}

	static function pass_Rate_Record( $adminid, $uid, $role_change_id, $beforInfo )
	{
		$data = array(
			'uid' => $uid,
			'before_rate' => (int)$beforInfo['rate'],
			'after_rate' => BASE_RATE,
			'adminid' => $adminid,
			'type' => 1,
			'role_change_id' => (int)$role_change_id,
			'desc' => '通过实名认证引起的比率变化,经纪公司id:' . $beforInfo['cid']
		);
		$res = addRateChangeRecord( $data );
		if( $res )
		{
			return $res;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 更新通知状态
	 *
	 * @param int    $rate_change_id
	 * @param object $db
	 *
	 * @return bool
	 */
	function updateNoticStatus( $rate_change_id )
	{
		if( empty( $rate_change_id ) )
		{
			return false;
		}
		$Dao = D( 'rate_change_record' );
		$res = $Dao->where( "id in ($rate_change_id)" )->save( array( 'status' => 1 ) );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 财务系统成功返回,后续操作失败日志
	 *
	 * @param $type
	 * @param $desc
	 * @param $db
	 */
	function unsuccessLogForFinanceBack( $title, $desc )
	{
		$data = array(
			'title' => $title,
			'desc' => json_encode( $desc ),
		);
		$Dao = D( 'unsuccess_log_for_financeBack' );
		$Dao->add( $data );

	}

	static function rateChange( $uids )
	{
		$adminid = Admin::getUid();
		for ( $i = 0, $k = count( $uids ); $i < $k; $i++ )
		{
			$beforeInfo = getBeforeRateByUid( $uids[$i] );
			$roleId = self::pass_Role_change_Record( $adminid, $uids[$i], $beforeInfo );//添加一条记录到角色变更表
			if( $roleId )
			{
				$list[$uids[$i]] = self::pass_Rate_Record( $adminid, $uids[$i], $roleId, $beforeInfo );//添加一条记录到汇率变更表
			}
//			//调用setRate
			$r = publicRequist::outside_setRate( $list, BASE_RATE, '完成实名认证' );//通知财务系统
			if( $r == 1 )
			{
				updateNoticStatus( $list[$uids[$i]] );//是否通知到财务系统
			}
			else
			{
				unsuccessLogForFinanceBack( '实名认证后，比率变化 财务系统返回失败', array( 'financeBack' => $r, 'uid' => $uids[$i], 'adminid' => $adminid, 'rateRecordId' => $list, 'roleid' => $roleId, 'beforeinfo' => $beforeInfo ) );
			}
		}
	}

	static function getBlackList( $uids )
	{
		$db = D( 'AnchorBlacklist' );
		if( is_array( $uids ) )
		{
			$uids = implode( ',', $uids );
			$where['luid'] = [ 'in', $uids ];
		}
		$res = $db->field( 'luid,ctime' )->where( $where )->getField( 'luid,ctime' );
		return $res ? $res : array();
	}
	static function getHistoryBlackList($uids,$stime,$etime){
		if( !is_array( $uids ) || !count($uids) )
		{
			//$where['luid'] = [ 'in', $uids ];
			return array();
		}
		$uids = implode(',', $uids);
		if((empty($stime)) || (empty($etime))){
			//$where['ctime'] = ['between',["$stime 00:00:00","$etime 23:59:59"]];
			return array();
		}
		$stime = "$stime 00:00:00";
		$etime = "$etime 23:59:59";
		//$where['type'] = ['in',[3,100]];
		$dao = D('AnchorBlackRecord');
		//$result = $dao->field('luid,type,ctime')->where($where)->order('id desc')->group('luid')->select();
		$result = $dao->query("select * from (SELECT `luid`,`type`,`ctime` FROM `anchorblackrecord`"
  							  ." WHERE `luid` IN ($uids)  AND ctime<= '{$etime}'"
			  				  ." AND `type` IN (3,100) order by ctime desc ) a GROUP BY luid ");
		$res = [];
		foreach ($result as $v){
			if($v['type'] == '3')
				$res[$v['luid']] = $v['ctime'];
		}
		return $res?$res:array();
	}
	
	static function getContractTime($uids) 
	{
		$db = M( 'anchor_change_record' );
	    $where['after_cid'] = ['gt',0];
		if( is_array( $uids ) )
		{
			$uids = implode( ',', $uids );
			$where['uid'] = [ 'in', $uids ];
		}
		$res = $db->where( $where )->group('uid')->getField( 'uid,min(ctime)' );
		return $res ? $res : array();
	}

	static function RepairRate( $uids )
	{
		$adminid = 100;
		for ( $i = 0, $k = count( $uids ); $i < $k; $i++ )
		{
			$beforeInfo = getBeforeRateByUid( $uids[$i] );
			$roleId = self::pass_Role_change_Record( $adminid, $uids[$i], $beforeInfo );//添加一条记录到角色变更表
			if( $roleId )
			{
				$list[$uids[$i]] = self::pass_Rate_Record( $adminid, $uids[$i], $roleId, $beforeInfo );//添加一条记录到汇率变更表
			}
//			//调用setRate
			$r = publicRequist::outside_setRate( $list, BASE_RATE, '完成实名认证' );//通知财务系统
			if( $r )
			{
				updateNoticStatus( $list[$uids[$i]] );//是否通知到财务系统
			}
			else
			{
				unsuccessLogForFinanceBack( '实名认证后，比率变化 财务系统返回失败', array( 'financeBack' => $r, 'uid' => $uids[$i], 'adminid' => $adminid, 'rateRecordId' => $list, 'roleid' => $roleId, 'beforeinfo' => $beforeInfo ) );
			}
		}
	}


}
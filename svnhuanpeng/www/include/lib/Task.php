<?php
namespace lib;

use lib\User;
use \DBHelperi_huanpeng;

/**
 * 任务类
 * User: dong
 * Date: 17/3/30
 * Time: 上午9:50
 */
class Task
{
	public $uid = null;
	private $_db = null;
	const TASK_TYPE = 0; //任务类型
	const TASK_UPLOAD_PIC = 6; //上传头像
	const TASK_CHECK_EMAIL = 12; //验证邮箱
	const TASK_FOLLOW_ANCHOR = 18; //关注5个主播
	const TASK_SEND_HPBEAN = 24; //送欢豆给主播
	const TASK_PAY = 30; //首次充值达十元
	const TASK_APP_FIRST = 36; //首次登录手机App



	public function __construct( $uid, $db = '' )
	{
		if( $uid )
		{
			$this->uid = (int)$uid;
		}
		if( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new DBHelperi_huanpeng();
		}
		return true;
	}

	/**
	 * 获取用户任务列表
	 *
	 * @return array  array('5'=>array('id'=>'','bean'=>'','type'=>'','coin'=>'','title'=>''))
	 * key为任务id
	 */
	private static function _getTaskList( $db )
	{
		$list = array();
		$res = $db->field( 'id, bean, `type`, title' )->where( "type=" . self::TASK_TYPE . " and  status=" . TASK_STAT_ONLINE )->select( 'taskinfo' );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$list[$v['id']] = $v;
			}
		}
		return $list;
	}

	/**
	 * 获取任务详情
	 *
	 * @param  int $taskid 任务id
	 *
	 * @return array()
	 */
	private function _getTaskInfo( $taskid )
	{
		if( empty( $taskid ) )
		{
			return false;
		}
		$res = $this->_db->where( "id = $taskid and status=" . TASK_STAT_ONLINE )->select( 'taskinfo' );
		if( $res )
		{
			return $res;
		}
		else
		{
			return array();
		}
	}

	/**
	 * 获取用户已经完成的任务ID列表
	 *
	 * @return array  array('6'=>'1')  key任务id;value任务状态
	 */
	private function _getFinishedTaskIdList()
	{
		$list = array();
		$res = $this->_db->field( 'taskid,status' )->where( "uid=" . $this->uid )->select( 'task' );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$list[$v['taskid']] = $v['status'];
			}
		}
		return $list;
	}

	/**
	 * 我的任务列表
	 *
	 * @return array
	 */
	public function getUserTaskList()
	{
		$finishList = $this->_getFinishedTaskIdList();
		$taskList = $this->_getTaskList( $this->_db );

		$myTaskList = array();

		if( $finishList )
		{
			foreach ( $taskList as $key => $row )
			{
				if( isset( $finishList[$key] ) )
				{
					$row['status'] = $finishList[$key];
				}
				else
				{
					$row['status'] = 0;
				}

				array_push( $myTaskList, $row );
			}
		}
		else
		{
			foreach ( $taskList as $key => $row )
			{
				$row['status'] = 0;
				array_push( $myTaskList, $row );
			}
		}

		return $myTaskList;

	}


	/**
	 * 任务是否完成
	 *
	 * @param int $taskid 任务
	 *
	 * @return bool 完成返回记录id   未完成0
	 */
	public  function _isTaskFinish( $taskid )
	{
		if( empty( $taskid ) )
		{
			return false;
		}
		$res = $this->_db->field( 'id' )->where( "uid= $this->uid and taskid = $taskid and status !=" . TASK_UNFINISH )->select( 'task' );
		if( $res )
		{
			return $res[0]['id'];
		}
		else
		{
			return 0;
		}
	}

	/**
	 * 添加完成任务记录
	 *
	 * @param int    $uid    用户id
	 * @param int    $taskid 任务id
	 * @param int    $type   任务类型
	 * @param int    $bean   奖励
	 * @param object $db
	 *
	 * @return bool
	 */
	private static function _addToTask( $uid, $taskid, $type, $bean, $db )
	{
		$data = array(
			'uid' => $uid,
			'taskid' => $taskid,
			'status' => TASK_FINISHED,
			'type' => $type,
			'getbean' => $bean
		);
		$res = $db->insert( 'task', $data );
		if( $res !== false )
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * 完成任务
	 *
	 * @param int  $uid    用户id
	 * @param int  $taskid 任务id
	 * @param type $db
	 *
	 * @return boolean
	 */
	public static function synchroTask( $uid, $taskid, $db )
	{
		if( empty( $uid ) || empty( $taskid ) )
		{
			return false;
		}

		if(!$db)
		{
			$db = new DBHelperi_huanpeng();
		}

		$isExist = User::checkUersIsExistByUid( $uid, $db );
		if( !$isExist )
		{
			return false;
		}
		$taskInfoList = self::_getTaskList( $db );
		if( isset( $taskInfoList[$taskid] ) )
		{
			$res = self::_addToTask( $uid, $taskid, $taskInfoList[$taskid]['type'], $taskInfoList[$taskid]['bean'], $db );
			return $res;
		}
		else
		{
			return false;
		}
	}

	/**
	 *根据任务id获取任务详情
	 *
	 * @param int $taskid 任务id
	 *
	 * @return bool
	 */
	private function _getFinishTaskInfoByTaskId( $taskid )
	{
		$res = $this->_db->where( "uid=$this->uid  and  taskid=$taskid and status=" . TASK_FINISHED )->limit( 1 )->select( 'task' );
		if( false !== $res )
		{
			return $res[0];
		}
		else
		{
			return false;
		}
	}

	/**
	 * 领完豆以后更新任务状态
	 *
	 * @param int $recordid 记录id
	 *
	 * @return bool
	 */
	private function _UpdateStatusAfterAddBean( $recordid )
	{
		if( empty( $recordid ) )
		{
			return false;
		}
		$res = $this->_db->where( 'id=' . $recordid )->update( 'task', array( 'status' => TASK_BEAN_RECEIVED ) );
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
	 * 完成任务领取欢豆
	 *
	 * @param int $taskid 任务id
	 *
	 * @return bool  领取成功true  领取失败false
	 */
	public function getBeanByTask( $taskid )
	{
		if( empty( $taskid ) )
		{
			return false;
		}
		$res = $this->_getFinishTaskInfoByTaskId( $taskid );
		if( false !== $res )
		{
			if( $res )
			{
				$obj = new Finance();
				$financeBack = $obj->addUserBean( $this->uid, $res['getbean'], Finance::GET_BEAN_CHANNEL_TASK, '完成任务，领取奖励', $res['id'] );
				if( is_array( $financeBack ) )
				{
					$upResule = $this->_UpdateStatusAfterAddBean( $res['id'] );
					if( !$upResule )
					{
						unsuccess_log_for_financeBack( '财务系统成功返回，更新任务状态失败', $res, $this->_db ); //财务系统成功返回，但更新任务状态失败
					}
					$userObj = new User( $this->uid );
					$upHpbResult = $userObj->updateUserHpBean( $financeBack['hd'] );//更新欢豆
					if( !$upHpbResult )
					{
						unsuccess_log_for_financeBack( '财务系统成功返回，更新用户欢豆余额失败', $res, $this->_db ); //财务系统成功返回，更新用户欢豆余额失败
					}
					return $res['getbean'];
				}
				else
				{
					unsuccess_log_for_financeBack( 30, $res, $this->_db );//任务领豆,财务系统返回失败
					return true; //
				}
			}
			else
			{
				return true; //没有数据
			}
		}
		else
		{
			return false;
		}
	}


}
<?php
// +----------------------------------------------------------------------
// | Anchor Info
// +----------------------------------------------------------------------
// | Author: zwq
// +----------------------------------------------------------------------
namespace HP\Op;

use HP\Log\Log;
use HP\Util\Curl;

class Live extends \HP\Cache\Proxy
{

	/**批量获取用户直播信息
	 *
	 * @param $uids  用户id列表
	 * @param $db
	 */
	static $msgTypeGroup = array( 'notice' => 1, 'stop' => 2, 'kill' => 3, 'cancel' => 100 );
	static $typesstr = array( '1' => '警告', '2' => '关流', '3' => '禁播', '100' => '取消禁播' );

	static $manageLiveApi = '/api/public/manage/liveManage.php';
	static $setMsgType = 0; //发送给指定用户
	static $isSiteMsg = 1; //发送了站内信的同时推送一条消息
	static $msgTitle = '违规通知'; //标题

	static public function liveInfo( $uids )
	{
		$db = D( 'live' );
		if( is_array( $uids ) )
		{
			$uids = implode( ',', $uids );
			$where['status'] = LIVE;
			$where['uid'] = [ 'in', $uids ];
		}
		$res = $db->where( $where )->getField( 'uid,liveid,status,livetype,stop_reason' );
		return $res ? $res : array();
	}

	static public function getUnpassreson()
	{
		$db = D( 'Livereviewreason' );
		$resons = $db->getField( "id,reason" );
		return $resons;
	}

	static public function checkLive( $liveid, $opt )
	{

		if( !$liveid )
		{
			$liveinfo = self::liveInfo( array( $opt['luid'] ) );
			$liveid = isset( $liveinfo[0]['liveid'] ) ? $liveinfo[0]['liveid'] : 0;
		}
		//$dao = D('Livereviewresult');
		$dao = D( 'AnchorBlackRecord' );
		$reasontype = $opt['reasontype'];
		$reason = self::getUnpassreson()[$opt['reasontype']];
		$luid = $opt['luid'];
		$type = self::$msgTypeGroup[$opt['act']];
		$data = array(
			"liveid" => $liveid,
			"type" => $type,
			"luid" => $luid,
			"uid" => Admin::getUid(),
			"reason" => $reasontype,
			"content" => $opt['content'],
			"remark"=>$opt['remark'],
			"pic" => $opt['pic']?$opt['pic']:"",
		);
		$resid = $dao->add( $data, [], true );
		switch ( $opt['act'] )
		{
			case 'notice':
				if( $reasontype == 44 )
				{
					$reason = '警告！你的直播涉嫌违规：' . $opt['content'];
				}
				else
				{
					$reason = '警告！你的直播涉嫌"'.$reason . '"：' . $opt['content'];
				}
				break;
			case 'stop':
				if( $reasontype == 44 )
				{
					$reason = "亲爱的欢朋主播！依据《主播协议》​，你本场直播已被强行终止。账号涉嫌违规：".$opt['content'] ."如有疑问，请联系客服！" ;
				}
				else
				{
					$reason = "亲爱的欢朋主播！依据《主播协议》​，你本场直播已被强行终止。账号涉嫌“".$reason."”：".$opt['content'] ."如有疑问，请联系客服！" ;
				}
				break;
			case 'kill':
				if( $reasontype == 44 )
				{
					$reason = "亲爱的欢朋主播！依据《主播协议​》，你的账户已被封禁直播权限。账号涉嫌违规：".$opt['content'] ."如有疑问，请联系客服" ;
				}
				else
				{
					$reason = "亲爱的欢朋主播！依据《主播协议​》，你的账户已被封禁直播权限。账号涉嫌“".$reason."”：".$opt['content'] ."如有疑问，请联系客服" ;
				}
				$dao = D( 'AnchorBlacklist' );
				reset( $data );
				$data['luid'] = $luid;
				$data['recordid'] = $resid;
				$dao->add( $data, [], true );
				break;
		}
		//站内信消息
		$megCallBack = publicRequist::set_message( self::$setMsgType, self::$msgTitle, $reason, $luid );
		Log::statis( json_encode( array( 'adminid' => $data['uid'],'act'=>$opt['act'], 'type' => $data['type'], 'title' => self::$msgTitle, 'msg' => $opt['content'], 'uid' => $luid, 'res' => json_decode( $megCallBack ) ) ),'','checkLive_msg_callback' );
		$megCallBack = json_decode( $megCallBack, true );
		if( $megCallBack['status'] == 1 )
		{
			//发推送消息
			$pushCallback = publicRequist::push_message( self::$setMsgType, self::$msgTitle, $reason, $luid, self::$isSiteMsg );
			Log::statis( json_encode( array( 'adminid' => $data['uid'], 'act'=>$opt['act'],'luid' => $luid, 'content' => $opt['content'], 'callback' => json_decode($pushCallback) ) ), '', 'checkLive_push_callback' );
		}
		return self::_sendMessage( $luid, $liveid, $type, $reason );//发直播间消息
	}


	private function _sendMessage( $luid, $liveid, $order, $reason )
	{
		$data = [
			'luid' => $luid,
			'liveID' => $liveid,
			'order' => $order,
			'reason' => $reason,
			'tm' => time(),
			'sign' => ''
		];
		$url = ADMIN_LIVE_API . self::$manageLiveApi . '?' . http_build_query( $data );
		$ret = Curl::get( $url );
		Log::statis( ADMIN_LIVE_API . self::$manageLiveApi );
		Log::statis( $ret );
		$ret = json_decode( $ret, true );
		if( !$ret['status'] )
		{
			return false;
		}
		return true;
	}

}
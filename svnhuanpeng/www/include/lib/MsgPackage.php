<?php
namespace lib;
	/**
	 * Created by PhpStorm.
	 * User: SEELE
	 * Date: 2017/3/29
	 * Time: 9:59
	 */

/**
 * 消息封包类操作
 *
 * Class MsgPackage
 */
class MsgPackage
{
	/**
	 * @var 整站的房间ID
	 */
	const SOCKET_SITE_LUID = 0;

	/**
	 * @var 所有在线人数的房间ID
	 */
	const SOCKET_ROOM_LUID = 1;

	const SOCKET_ROOM_RANK_CHANGE_DAY  = 1;
	const SOCKET_ROOM_RANK_CHANGE_WEEK = 2;
	const SOCKET_ROOM_RANK_CHANGE_ALL  = 3;


	const SMS_TYPE_REGISTER      = 1;
	const SMS_TYPE_APPLY         = 2;
	const SMS_TYPE_GETBACKPASSWD = 3;
	const SMS_TYPE_APPLY_FAILED  = 4;
	const SMS_TYPE_APPLY_SUCCESS = 5;
	const SMS_TYPE_BINDMOBILE    = 6;
	//todo SMS_TYPE_PUSH 后台推送消息

	const MAIL_TYPE_BINDMAIL = 'registemail_102';

	const SITEMSG_TYPE_TO_USER = 0;
	const SITEMSG_TYPE_TO_ALL  = 2;
	const SITEMSG_GROUP_ALL    = 2;
	const SITEMSG_GROUP_USER   = 1;

	/**
	 * 获取直播开始socket消息package
	 *
	 * @param int $luid   主播ID
	 * @param int $liveid 直播ID
	 *
	 * @return mixed
	 */
	public static function getLiveStartMsgSocketPackage( int $luid, int $liveid )
	{

		$content = array(
			't'   => 601,
			'lid' => $liveid
		);

		return static::_buildSocketData( SocketSend::SEND_TO_ROOM, $content, $luid );
	}

	/**
	 * 获取直播结束socket消息package
	 *
	 * @param int $luid   主播ID
	 * @param int $liveid 直播ID
	 *
	 * @return mixed
	 */
	public static function getLiveEndMsgSocketPackage( int $luid, int $liveid, int $reasonid = 0, string $reason = '' )
	{
		$content = array(
			't'      => 602,
			'lid'    => $liveid,
			'rid'    => $reasonid,
			'reason' => $reason
		);

		return static::_buildSocketData( SocketSend::SEND_TO_ROOM, $content, $luid );
	}

	/**
	 * 获取用户进入房间通知的socket消息package
	 *
	 * @param int    $luid      主播ID
	 * @param int    $uid       用户ID
	 * @param string $nick      用户昵称
	 * @param int    $group     用户所在分组
	 * @param int    $level     用户等级
	 * @param string $pic       用户头像
	 * @param int    $viewCount 观众人数<虚拟人数>
	 * @param int    $showHead  是否展示头像
	 * @param int    $showWel   是否发送欢迎信息
	 * @param int    $isGust    是否为游客
	 *
	 * @return mixed
	 */
	public static function getUserEnterMsgSocketPackage( int $luid, int $uid, string $nick, int $group, int $level, string $pic, int $viewCount, int $showHead, int $showWel, int $isGust )
	{
		$content = array(
			't'         => 501,
			'tm'        => time(),
			'nn'        => $nick,
			'uid'       => $uid,
			'level'     => $level,
			'pic'       => $pic,
			'group'     => $group,
			'viewCount' => $viewCount,
			'showHead'  => $showHead,
			'showWel'   => $showWel,
			'isGust'    => $isGust
		);

		return static::_buildSocketData( SocketSend::SEND_TO_ROOM, $content, $luid );
	}

	/**
	 * 用户成功进入房间回调信息
	 *
	 * @param int $luid 主播ID
	 * @param int $uid  用户ID
	 * @param int $mid  消息ID
	 * @param int $e    错误代码
	 *
	 * @return mixed
	 */
	public static function getUserSuccEnterMsgCallBackSocketpackage( int $luid, int $uid, int $mid, int $e )
	{

		$content = array(
			't'   => 1104,
			'mid' => $mid,
			'e'   => $e
		);

		$content = static::_getCallBackMsgContent( $content );

		return static::_buildSocketData( SocketSend::SEND_TO_USER, $content, $luid, $uid );
	}

	/**
	 * 获取用户退出房间的socket消息package
	 *
	 * @param int $luid      主播ID
	 * @param int $uid       用户ID
	 * @param int $viewCount 观看人数<虚拟人数>
	 * @param int $showHead  是否展示头像
	 * @param int $showWel   是否欢迎
	 * @param int $isGust    是否为用户
	 *
	 * @return mixed
	 */
	public static function getUSerExitMsgSocketPackage( int $luid, int $uid, int $viewCount, int $showHead, int $showWel, int $isGust )
	{
		$content = array(
			't'         => 506,
			'tm'        => time(),
			'uid'       => $uid,
			'viewCount' => $viewCount,
			'showHead'  => $showHead,
			'showWel'   => $showWel,
			'isGust'    => $isGust
		);

		return static::_buildSocketData( SocketSend::SEND_TO_ROOM, $content, $luid );
	}

	/**
	 * 获取用户发言socket消息package
	 *
	 * @param int    $luid    主播ID
	 * @param int    $uid     用户ID
	 * @param string $nick    主播昵称
	 * @param string $msg     发送消息
	 * @param int    $group   用户所在分组
	 * @param int    $level   用户等级
	 * @param int    $isPhone 是否为手机用户
	 * @param int    $msgid   消息ID
	 *
	 * @return mixed
	 */
	public static function getUserMsgSocketPackage( int $luid, int $uid, string $nick, string $msg, int $group, int $level, int $isPhone, int $msgid )
	{
		$content = array(
			't'     => 502,
			'tm'    => time(),
			'cuid'  => $uid,
			'cunn'  => $nick,
			'msg'   => $msg,
			'msgid' => $msgid,
			'level' => $level,
			'group' => $group,
			'phone' => $isPhone
		);

		return static::_buildSocketData( SocketSend::SEND_TO_ROOM, $content, $luid );
	}

	/**
	 * 用户发言回调socket消息package
	 *
	 * @param int $luid 主播ID
	 * @param int $uid  用户ID
	 * @param int $mid  消息ID
	 * @param int $e    错误代码
	 *
	 * @return mixed
	 */
	public static function getUserMsgCallBackSocketPackage( int $luid, int $uid, int $mid, int $e )
	{
		$content = array(
			't'   => 1100,
			'mid' => $mid,
			'e'   => $e
		);
		$content = static::_getCallBackMsgContent( $content );

		return static::_buildSocketData( SocketSend::SEND_TO_USER, $content, $luid, $uid );
	}

	/**
	 * 获取用户赠送欢朋豆的socket消息package
	 *
	 * @param int    $luid        主播ID
	 * @param int    $uid         用户ID
	 * @param string $senderNick  送礼人昵称
	 * @param int    $gid         礼物ID
	 * @param string $giftName    礼物名称
	 * @param int    $sendNum     送礼数量
	 * @param int    $senderLevel 送礼人等级
	 * @param int    $isPhone     是否为手机发送
	 * @param int    $senderGroup 送礼人所在房间组ID
	 * @param int    $anchorBean
	 *
	 * @return mixed
	 */
	public static function getSendBeanMsgSocketPackage( int $luid, int $uid, string $senderNick, int $gid, string $giftName, int $sendNum, int $senderLevel, int $isPhone, int $senderGroup, float $anchorBean )
	{
		$content = array(
			't'     => 504,
			'tm'    => time(),
			'ouid'  => $uid,
			'ounn'  => $senderNick,
			'gid'   => $gid,
			'gnum'  => $sendNum,
			'gname' => $giftName,
			'level' => $senderLevel,
			'phone' => $isPhone,
			'group' => $senderGroup,
			'type'  => 'bean',
			'gd'    => Convert::property( $anchorBean )
		);

		return static::_buildSocketData( SocketSend::SEND_TO_ROOM, $content, $luid );
	}

	public static function getSendBeanMsgSocketPackageTest( int $luid, int $uid, string $senderNick, int $gid, string $giftName, int $sendNum, int $senderLevel, int $isPhone, int $senderGroup )
	{
		$content = array(
			't'     => 504,
			'tm'    => time(),
			'ouid'  => $uid,
			'ounn'  => $senderNick,
			'gid'   => $gid,
			'gnum'  => $sendNum,
			'gname' => $giftName,
			'level' => $senderLevel,
			'phone' => $isPhone,
			'group' => $senderGroup,
			'type'  => 'bean'
		);

		return static::_buildSocketData( SocketSend::SEND_TO_USER, $content, $luid );
	}

	/**
	 * 用户赠送欢朋豆的回调通知
	 *
	 * @param int $luid 主播ID
	 * @param int $uid  用户ID
	 * @param int $e    错误代码
	 * @param int $mid  消息ID
	 * @param int $coin 用户欢朋币余额
	 * @param int $bean 用户欢朋豆余额
	 * @param int $cost 送礼消耗欢朋豆数量
	 *
	 * @return mixed
	 */
	public static function getSendBeanMsgCallBackSocketPackage( int $luid, int $uid, int $e, int $mid, int $coin, int $bean, int $cost )
	{
		$content = array(
			't'    => 1102,
			'mid'  => $mid,
			'e'    => $e,
			'cost' => (int)$cost,
			'coin' => $coin,
			'bean' => $bean,
//			'costNUm' => $num,
//			'constamount' => $amount
		);
		$content = static::_getCallBackMsgContent( $content );

		return static::_buildSocketData( SocketSend::SEND_TO_USER, $content, $luid, $uid );
	}

	/**
	 * 获取用户送礼socket 消息的package
	 *
	 * @param int    $luid        主播ID
	 * @param int    $uid         用户ID
	 * @param string $senderNick  送礼人昵称
	 * @param int    $gid         礼物ID
	 * @param string $giftName    礼物名称
	 * @param int    $sendNum     赠送数量
	 * @param int    $senderLevel 送礼人等级
	 * @param int    $isPhone     是否为手机用户
	 * @param int    $senderGroup 送礼人所在分组
	 * @param int    $sendTimer   赠送次数
	 *
	 * @return mixed
	 */
	public static function getSendGiftMsgSocketPackage( int $luid, int $uid, string $senderNick, int $gid, string $giftName, int $sendNum, int $senderLevel, int $isPhone, int $senderGroup, int $sendTimer )
	{

		$content = array(
			't'     => 504,
			'tm'    => time(),
			'ouid'  => $uid,
			'ounn'  => $senderNick,
			'gid'   => $gid,
			'gnum'  => $sendNum,
			'gname' => $giftName,
			'level' => $senderLevel,
			'phone' => $isPhone,
			'group' => $senderGroup,
			'timer' => $sendTimer,
			'type'  => 'coin'

		);

		return static::_buildSocketData( SocketSend::SEND_TO_ROOM, $content, $luid, $uid );
	}

	public static function getSendGiftMsgSocketPackageTest( int $luid, int $uid, string $senderNick, int $gid, string $giftName, int $sendNum, int $senderLevel, int $isPhone, int $senderGroup, int $sendTimer )
	{

		$content = array(
			't'     => 504,
			'tm'    => time(),
			'ouid'  => $uid,
			'ounn'  => $senderNick,
			'gid'   => $gid,
			'gnum'  => $sendNum,
			'gname' => $giftName,
			'level' => $senderLevel,
			'phone' => $isPhone,
			'group' => $senderGroup,
			'timer' => $sendTimer,
			'type'  => 'coin'

		);

		return static::_buildSocketData( SocketSend::SEND_TO_USER, $content, $luid, $uid );
	}

	/**
	 * 送礼回调通知
	 *
	 * @param int $luid 主播ID
	 * @param int $uid  用户ID
	 * @param int $e    错误代码
	 * @param int $mid  消息ID
	 * @param int $coin 用户欢朋币余额
	 * @param int $bean 用户欢朋豆余额
	 * @param int $cost 送礼消耗欢朋币数量
	 *
	 * @return mixed
	 */
	public static function getSendGiftMsgCallBackSocketPackage( int $luid, int $uid, int $e, int $mid, int $coin, int $bean, int $cost )
	{

		$content = array(
			't'    => 1103,
			'mid'  => $mid,
			'e'    => $e,
			'cost' => (int)$cost,
			'coin' => $coin,
			'bean' => $bean,
		);
		$content = static::_getCallBackMsgContent( $content );

		return static::_buildSocketData( SocketSend::SEND_TO_USER, $content, $luid, $uid );
	}

	/**
	 * 获取用户赠送飞碟的socket消息package
	 *
	 * @param int    $luid       主播ID
	 * @param int    $uid        用户ID
	 * @param int    $gid        礼物ID
	 * @param int    $giftName   礼物名称
	 * @param string $senderNick 送礼人昵称
	 * @param string $anchorNick 收礼人昵称
	 * @param int    $treasureID 宝箱ID
	 * @param int    $timeout    宝箱开启时间
	 *
	 * @return mixed
	 */
	public static function getSendFlyingGiftMsgSocketPackage( int $luid, int $uid, int $gid, string $giftName, string $senderNick, string $anchorNick, int $treasureID, int $timeout = TREASURE_TIME_OUT )
	{
		$content = array(
			't'          => 535,
			'tm'         => time(),
			'uid'        => $uid,
			'nick'       => $senderNick,
			'luid'       => $luid,
			'lunick'     => $anchorNick,
			'gname'      => $giftName,
			'gid'        => $gid,
			'treasureID' => $treasureID,
			'timeOut'    => $timeout
		);

		return static::_buildSocketData( SocketSend::SEND_TO_ALL, $content, self::SOCKET_SITE_LUID );
	}

	public static function getSendFlyingGiftMsgSocketPackageTest( int $luid, int $uid, int $gid, string $giftName, string $senderNick, string $anchorNick, int $treasureID, int $timeout = TREASURE_TIME_OUT )
	{
		$content = array(
			't'          => 535,
			'tm'         => time(),
			'uid'        => $uid,
			'nick'       => $senderNick,
			'luid'       => $luid,
			'lunick'     => $anchorNick,
			'gname'      => $giftName,
			'gid'        => $gid,
			'treasureID' => $treasureID,
			'timeOut'    => $timeout
		);

		return static::_buildSocketData( SocketSend::SEND_TO_USER, $content, self::SOCKET_SITE_LUID );
	}

	/**
	 * 获取房间禁言socket消息package
	 *
	 * @param int    $luid         主播ID
	 * @param int    $targetUid    目标用户ID
	 * @param string $targetNick   目标用户nic
	 * @param int    $adminUid     管理员用户ID
	 * @param string $adminNick    管理员昵称
	 * @param int    $adminGroup   管理员所属分组
	 * @param int    $outTimeStamp 过期时间
	 *
	 * @return mixed
	 */
	public static function getRoomSilenceUserMsgSocketPackage( int $luid, int $targetUid, string $targetNick, int $adminUid, string $adminNick, int $adminGroup, int $outTimeStamp, $timeStr )
	{
		$content = array(
			't'            => 505,
			'admin'        => $adminUid,
			'adminNick'    => $adminNick,
			'targetUid'    => $targetUid,
			'targetNick'   => $targetNick,
			'group'        => $adminGroup,
			'outTimestamp' => $outTimeStamp,
			'timeStr'      => $timeStr
		);

		return static::_buildSocketData( SocketSend::SEND_TO_ROOM, $content, $luid );
	}

	/**
	 * 获取房间开启宝箱的socket消息package
	 *
	 * @param int    $luid  主播ID
	 * @param int    $uid   用户ID
	 * @param string $unick 用户昵称
	 * @param int    $num   获取欢朋豆数量
	 *
	 * @return mixed
	 */
	public static function getOpenTreasureBoxMsgSocketPackage( int $luid, int $uid, string $unick, int $num )
	{
		$content = array(
			't'     => '511',
			'tm'    => time(),
			'num'   => $num,
			'uid'   => $uid,
			'unick' => $unick
		);

		return static::_buildSocketData( SocketSend::SEND_TO_ROOM, $content, $luid );
	}


	/**
	 * 获取后台发布警告消息socket消息package
	 *
	 * @param int    $luid   主播ID
	 * @param string $reason 操作原因描述
	 *
	 * @return mixed
	 */
	public static function getBackManageWaringMsgSocketPackage( int $luid, string $reason )
	{

		$content = array(
			't'      => 540,
			'tm'     => time(),
			'reason' => $reason
		);

		return static::_buildSocketData( SocketSend::SEND_TO_USER, $content, $luid, $luid );
	}

	/**
	 * 获取后台发布停止直播socket消息package
	 *
	 * @param int    $luid   主播ID
	 * @param string $reason 操作原因描述
	 *
	 * @return mixed
	 */
	public static function getBackManageStopLiveMsgSocketPackage( int $luid, string $reason )
	{
		$content = array(
			't'      => 541,
			'tm'     => time(),
			'reason' => $reason
		);

		return static::_buildSocketData( SocketSend::SEND_TO_USER, $content, $luid, $luid );
	}

	/**
	 * 获取后台发布封号消息socket消息package
	 *
	 * @param int    $luid   主播ID
	 * @param string $reason 操作原因描述
	 *
	 * @return mixed
	 */
	public static function getBackManageClosureMsgSocketPackage( int $luid, string $reason )
	{
		$content = array(
			't'      => 542,
			'tm'     => time(),
			'reason' => $reason
		);

		return static::_buildSocketData( SocketSend::SEND_TO_USER, $content, $luid, $luid );
	}

	/**
	 * 获取房间排行榜变化通知的socket 消息 package
	 *
	 * @param int $luid 主播ID
	 * @param int $type 更新类型，1：日榜，2：周榜，3：总榜
	 *
	 * @return mixed
	 */
	public static function getRoomRankChangeMsgSocketPackage( int $luid, int $type )
	{
		$content = array(
			't'    => 701,
			'tm'   => time(),
			'type' => $type
		);

		return static::_buildSocketData( SocketSend::SEND_TO_ROOM, $content, $luid );
	}

	/**
	 * 开播提醒通知
	 *        注意，在应用层上，开播提醒是对应单个用于进行发送，这里返回的socketMsgPackage 是针对单个用户的包
	 *
	 * @param int    $luid       主播ID
	 * @param int    $uid        用户ID
	 * @param string $anchorNick 主播昵称
	 * @param string $anchorPic  主播头像
	 *
	 * @return mixed
	 */
	public static function getLiveStartNoticeMsgSocketPackage( int $luid, int $uid, string $anchorNick, string $anchorPic )
	{
		$content = array(
			't'    => 2001,
			'tm'   => time(),
			'msg'  => "主播：$anchorNick 开始直播啦，快点前去围观吧~",
			'luid' => $luid,
			'nick' => $anchorNick,
			'pic'  => $anchorPic
		);

		return static::_buildSocketData( SocketSend::SEND_TO_USER, $content, self::SOCKET_ROOM_LUID, $uid );
	}

	/**
	 * 发送约玩新订单数量
	 *
	 * @param int $luid
	 * @param int $uid
	 * @param int $num 当前订单数量
	 *
	 * @return mixed
	 */
	public static function getNewDueOrderNumMsgSocketPackage( int $luid, int $uid, int $num, $isPush = 0 )
	{
		$content = [
			't'   => 512,
			'tm'  => time(),
			'num' => $num,
			'uid' => $uid
		];

		$pushRoomID = $isPush ? self::SOCKET_ROOM_LUID : $luid;

		return static::_buildSocketData( SocketSend::SEND_TO_USER, $content, $pushRoomID, $luid );
	}

	/**
	 * 关注主播房间消息
	 *
	 * @param $luid
	 * @param $uid
	 * @param $unick
	 * @param $msg
	 *
	 * @return mixed
	 */
	public static function getFollowUserMsgSocketPackage( $luid, $uid, $unick, $level, $group )
	{
		$msg = $unick . "关注了主播";

		$content = [
			't'     => 507,
			'tm'    => time(),
			'uid'   => $uid,
			'nick'  => $unick,
			'level' => $level,
			'group' => $group
		];

		return static::_buildSocketData( SocketSend::SEND_TO_ROOM, $content, $luid, $uid );
	}

	/**
	 * 分享房间消息
	 *
	 * @param $luid
	 * @param $uid
	 * @param $unick
	 * @param $msg
	 *
	 * @return mixed
	 */
	public static function getShareRoomMsgSocketPackage( $luid, $uid, $unick, $level, $group )
	{
		$msg = $unick . "分享了直播间";

		$content = [
			't'     => 508,
			'tm'    => time(),
			'uid'   => $uid,
			'nick'  => $unick,
			'level' => $level,
			'group' => $group
		];

		return static::_buildSocketData( SocketSend::SEND_TO_ROOM, $content, $luid, $uid );
	}

	public static function getSystemPushMsgSocketPackage($uid,$msg,$title,$action='nothing',$custom=[])
	{ 
		$content = [
			't' => 2101,
			'msg'=>$msg,
			'title'=>$title,
			'action'=>$action,
			'custom' =>$custom,
			'pic' => WEB_ROOT_URL."static/img/due/systemIcon.png"
		];
		
		return static::_buildSocketData(SocketSend::SEND_TO_USER, $content, self::SOCKET_ROOM_LUID, $uid);
	}

	private static function _getCallBackMsgContent( $content )
	{

		$msg = array_merge( $content, static::_getSocketErrorDesc( $content['e'] ) );

		return $msg;
	}

	private static function _getSocketErrorDesc( $code )
	{
		$error = array(
			-3503 => array(
				'errtype' => 1,
				'errwd'   => '获取用户信息失败，或者当前用户不存在'
			),
			-3501 => array(
				'errtype' => 1,
				'errwd'   => '聊天消息保存失败'
			),
			-3512 => array(
				'errtype' => 1,
				'errwd'   => '聊天消息不能为空'
			),
			-3502 => array(
				'errtype' => 1,
				'errwd'   => '用户发言发送失败，socket发送失败'
			),
			-3009 => array(
				'errtype' => 2,
				'errwd'   => '当前用户被禁言',
			),
			-3015 => array(
				'errtype' => 2,
				'errwd'   => '发言字数超过限制'
			),

			//送礼相关
			-3511 => array(
				'errtype' => 1,
				'errwd'   => '传入参数错误'
			),
			-3504 => array(
				'errtype' => 1,
				'errwd'   => '获取用户信息失败，或者当前用户不存在'
			),
			-3507 => array(
				'errtype' => 1,
				'errwd'   => '获取主播信息失败，或者当前主播不存在',
			),
			-3510 => array(
				'errtype' => 1,
				'errwd'   => '当前服务器繁忙'
			),
			-3508 => array(
				'errtype' => 9,
				'errwd'   => '送礼成功，全局通知消息发送失败'
			),
			-3509 => array(
				'errtype' => 9,
				'errwd'   => '送礼成功，房间通知消息发送失败'
			),
			-3514 => array(
				'errtype' => 2,
				'errwd'   => '您的欢朋币余额不足,是否立即充值'
			),
			-3515 => array(
				'errtype' => 2,
				'errwd'   => '您的欢朋豆余额不足'
			),
			-3530 => [
				'errtype' => 2,
				'errwd'   => '您的发言消息包含敏感词汇'
			]
		);

		if ( isset($error[$code]) && $error[$code] )
		{
			return $error[$code];
		}
		else
		{
			return array();
		}
	}

	/**
	 * socket消息编码
	 *
	 * @param string $msg
	 *
	 * @return mixed|string
	 */
	public static function socketMsgEncode( string $msg )
	{
		$msgGz = gzdeflate( $msg, 6 );
		$msgGz = static::_myBase64Encode( $msgGz );

		return $msgGz;
	}

	/**
	 * socket消息解码
	 *
	 * @param string $msgGz
	 *
	 * @return mixed|string
	 */
	public static function socketMsgDecode( string $msgGz )
	{
		$msg = static::_myBase64Decode( $msgGz );
		$msg = gzinflate( $msg );

		return $msg;
	}

	/**
	 * @param string $str
	 *
	 * @return mixed|string
	 */
	/**
	 * 私有Base64编码
	 *
	 * 将+ / = 字符 转换为 ( ) @ 字符
	 *
	 * @param string $str
	 *
	 * @return mixed|string
	 */
	private static function _myBase64Encode( string $str )
	{
		$base64Str = base64_encode( $str );
		$base64Str = str_replace( array( '+', '/', '=' ), array( '(', ')', '@' ), $base64Str );

		return $base64Str;
	}

	/**
	 * 私有base64解码
	 *
	 * 将 ( ) @ 字符转换为 + / =字符
	 *
	 * @param string $str
	 *
	 * @return mixed|string
	 */
	private static function _myBase64Decode( string $str )
	{
		$base64Str = str_replace( array( '(', ')', '@' ), array( '+', '/', '=' ), $str );
		$base64Str = base64_decode( $base64Str );

		return $base64Str;
	}

	/**
	 * 构建socket包数据
	 *
	 * @param int   $type    消息类型 标记全局发送，房间发送，还是对个人发送
	 * @param array $content 消息实体
	 * @param int   $luid    主播ID
	 * @param int   $uid     用户ID
	 *
	 * @return mixed
	 */
	private static function _buildSocketData( int $type, array $content, $luid = 0, $uid = 0 )
	{
		$data['type']    = $type;
		$data['content'] = static::socketMsgEncode( json_encode( toString( $content ) ) );
		$data['uid']     = $uid;
		$data['luid']    = $luid;
		return $data;
	}

	/**
	 * 获取socket消息ID
	 *
	 * @return int
	 */
	private static function _getSocketMsgID()
	{
		return time() + rand( 1000, 9999 );
	}


	/**
	 * 获取注册短息验证码消息
	 *
	 * @param int    $code 短信验证码
	 * @param string $url  短信附带的URL
	 *
	 * @return string
	 */
	public static function getRegisterUserCodeMsgSmsPackage( $mobile, int $code, string $url )
	{
		$msg = '【欢朋直播】验证码是' . $code . '，用于注册手机验证，15分钟内有效。切勿泄露他人。欢朋直播App下载：' . $url . "download.php";

		$data = array(
			'sms'    => $msg,
			'type'   => self::SMS_TYPE_REGISTER,
			'mobile' => $mobile,
			'code'   => $code ? $code : static::getSmsCode()
		);

		return $data;
	}

	/**
	 * 获取认证主播短息验证码信息
	 *
	 * @param $code
	 *
	 * @return string
	 */
	public static function getApplyAnchorCodeMsgSmsPackge( $mobile, int $code = 0 )
	{
		$msg = '\'【欢朋直播】验证码是\' . $code . \'，用于申请主播手机验证，15分钟内有效。切勿泄露他人。';

		$data = array(
			'sms'    => $msg,
			'type'   => self::SMS_TYPE_APPLY,
			'mobile' => $mobile,
			'code'   => $code ? $code : static::getSmsCode()
		);

		return $data;
	}

	/**
	 * 获取找回密码短息验证码信息
	 *
	 * @param int $code 短信验证码
	 *
	 * @return string
	 */
	public static function getFindPasswdCodeMsgSmsPackage( $mobile, int $code = 0 )
	{
		$msg = '【欢朋直播】验证码是' . $code . '，用于找回密码手机验证，15分钟内有效。切勿泄露他人。';

		$data = array(
			'sms'    => $msg,
			'type'   => self::SMS_TYPE_GETBACKPASSWD,
			'mobile' => $mobile,
			'code'   => $code ? $code : static::getSmsCode()
		);

		return $data;
	}

	/**
	 * 获取主播认证失败原因信息
	 *
	 * @param string $reason 失败原因
	 *
	 * @return string
	 */
	public static function getFailedApplyAnchorReasonMsgSmsPackage( $mobile, string $reason, int $code = 0 )
	{
		$msg = '【欢朋直播】很遗憾，你的主播认证申请未通过审核。原因：' . $reason . '。你可以通过手机客户端或者网站重新提交认证';

		$data = array(
			'sms'    => $msg,
			'type'   => self::SMS_TYPE_APPLY_FAILED,
			'mobile' => $mobile,
			'code'   => $code ? $code : static::getSmsCode()

		);

		return $data;
	}

	/**
	 * 获认证主播成功通知信息
	 *
	 * @param string $url 下载客户端URL
	 *
	 * @return string
	 */
	public static function getSuccApplyAnchorMsgSmsPackage( $mobile, string $url, int $code = 0 )
	{
		$msg = "【欢朋直播】恭喜你！你的主播认证申请通过审核。请前往欢朋直播开启你的直播生涯吧！欢朋直播App下载：$url download.php";

		$data = array(
			'sms'    => $msg,
			'type'   => self::SMS_TYPE_APPLY_SUCCESS,
			'mobile' => $mobile,
			'code'   => $code ? $code : static::getSmsCode()
		);

		return $data;
	}

	/**
	 * 获取绑定手机验证码消息
	 *
	 * @param int $code 短信验证码
	 *
	 * @return string
	 */
	public static function getBindMobileCodeMsgSmsPackage( $mobile, int $code = 0 )
	{
		$msg = "【欢朋直播】验证码是$code，用于绑定手机验证，15分钟内有效。切勿泄露他人";

		$data = array(
			'sms'    => $msg,
			'type'   => self::SMS_TYPE_BINDMOBILE,
			'mobile' => $mobile,
			'code'   => $code ? $code : static::getSmsCode()
		);

		return $data;
	}

	/**
	 * @param int $length
	 *
	 * @return int
	 */
	public static function getSmsCode( $length = 6 )
	{
		$min = '1' . str_repeat( '0', $length - 1 );
		$max = str_repeat( '1', $length ) * 9;

		return rand( $min, $max );
	}

	/**
	 * 获取认证邮箱消息package
	 *
	 * @param int    $uid              用户ID
	 * @param string $nick             用户昵称
	 * @param string $emailCallBackUrl 邮箱认证地址
	 * @param string $emailAddress     邮箱地址
	 * @param string $key              邮箱发送的认证APPkey
	 * @param int    $outTime          过期时间
	 *
	 * @return string
	 */
	public static function getCertifyEmailMsgEmailPackage( int $uid, string $nick, string $emailCallBackUrl, string $emailAddress, string $key = CERT_EMAIL_KEY, int $outTime = EMAIL_CERT_OUTTIME )
	{
		//build email certify appkey
		$certTime = time() + $outTime;
		$appkey   = md5( $uid . $key . $emailAddress . $certTime ) . "-" . $certTime;
		$data     = array(
			'appkey' => $appkey,
			'email'  => $emailAddress,
			'uid'    => $uid,
		);

		$url = $emailCallBackUrl . "?" . http_build_query( $data );//WEB_PERSONAL_URL . $emailCallBackUrl . http_build_query( $data );

		$content = array(
			'username' => $nick,
			'url'      => $url,
			'expire'   => '24小时',
		);
		ksort( $content );

		$data = [
			'content' => json_encode( $content ),
			'type'    => self::MAIL_TYPE_BINDMAIL,
			'email'   => $emailAddress
		];

		return $data;
	}


	/**
	 *
	 * @param int    $uid
	 * @param string $title
	 * @param string $msg
	 * @param int    $type
	 * @param bool   $groupAuto
	 * @param int    $group
	 *
	 * @return array
	 */
	public static function getSiteMsgPackage( $uid, string $title, string $msg, $type = MsgPackage::SITEMSG_TYPE_TO_ALL, $adminuid=0,$groupAuto = true, $group = 0 )
	{
		$data = [
			'sendid' => 0,
			'uid'    => $uid,
			'title'  => $title,
			'msg'    => $msg,
			'adminuid'=>$adminuid
		];

		if ( $type == MsgPackage::SITEMSG_TYPE_TO_ALL )
		{
			$data['type']  = $type;
			$data['group'] = $groupAuto ? MsgPackage::SITEMSG_GROUP_ALL : $group;
		}
		elseif ( $type == MsgPackage::SITEMSG_TYPE_TO_USER )
		{
			$data['type']  = $type;
			$data['group'] = $groupAuto ? MsgPackage::SITEMSG_GROUP_USER : $group;
		}
		else
		{
			return $data;
		}

		return $data;
	}

	/**
	 * 发送苹果系统推送消息
	 *
	 * 	注意，type类型已经定义在函数内部，外部程序不需要传入。
	 *
	 * @param string $deviceToken 设备ID
	 * @param string $title
	 * @param string $msg
	 * @param string $mid
	 * @param string $action
	 * @param array  $subData 子数据
	 *
	 * @return array
	 */
	public static function getSystemPushApplePushMsgPackage(string $deviceToken, string $title,string $msg, string $mid, $action='nothing',array $subData=[])
	{
		$customs['type'] = 2101;
		$custom['data'] = [
			'pic' => WEB_ROOT_URL.'static/img/due/systemIcon.png',
			'action' => $action,
			'subData' => $subData,
		    'title'   => $title
		];

		$customs['action'] = $action;
		$customs['pic'] = WEB_ROOT_URL."static/img/due/systemIcon.png";
		$customs['custom'] = $custom;
		$data = [
			'tk' => $deviceToken,
			'content' => $msg,
			'mid'=>$mid,
			'title' => $title,
			'custom'=>json_encode($customs),
			'image'=>'Default.png',
			'sound' => 'default'
		];

		return $data;
	}

	public static function getLiveStartApplePushMsgPackage( string $deviceToken, string $anchorNick, string $mid, array $custome )
	{
		$data = [
			'tk'      => $deviceToken,
			'content' => "主播：$anchorNick 开始直播啦，快点前去围观吧~",
			'mid'     => $mid,
			'title'   => "开播提醒",
			'custom'  => json_encode( $custome ),
			'image'     => 'Default.png',
			'sound'   => 'default'
		];

		return $data;
	}

	
	public static function getDueOrderUserPlaceOrderApplePushMsgPackage(string $deviceToken,string $userNick, string $mid, array$custom)
	{
		$data = [
			'tk' => $deviceToken,
			'content' => $userNick."要你陪玩，请尽快接单哦",
			'mid' =>$mid,
			'title' =>"查看",
			'custom'=>json_encode($custom),
			'image' => "Default.png",
			'sound'=>'default'
		];

		return $data;
	}

	public static function getDueOrderAnchorRejectOrderApplePushMsgPackage(string $deviceToken, string $anchorNick, string $mid, array$custom)
	{
		$data = [
			'tk' => $deviceToken,
			'content' => $anchorNick."主播忙其他的事情了，下次再约",
			'mid' =>$mid,
			'title' =>"查看",
			'custom'=>json_encode($custom),
			'image' => "Default.png",
			'sound'=>'default'
		];

		return $data;
	}

	public static function getDueOrderAnchorAcceptOrderApplePushMsgPackage(string$deviceToken, string $anchorNick,string$mid,array$custom)
	{
		$data = [
			'tk' => $deviceToken,
			'content' => $anchorNick."即将带你起飞，请保证在线哦",
			'mid' =>$mid,
			'title' =>"查看",
			'custom'=>json_encode($custom),
			'image' => "Default.png",
			'sound'=>'default'
		];

		return $data;
	}

	public static function getDueOrderUserRefundOrderApplePushMsgPackage(string$deviceToken, string $userNick,string $mid, array$custom)
	{

		$data = [
			'tk' => $deviceToken,
			'content' => $userNick."想要退单，请处理",
			'mid' =>$mid,
			'title' =>"查看",
			'custom'=>json_encode($custom),
			'image' => "Default.png",
			'sound'=>'default'
		];

		return $data;
	}

	public static function getDueOrderApplePushMsgPackage(string$deviceToken,string $msg, string $mid, array $custom)
	{
		$title = $custom['title'];
		unset($custom['title']);
		asort($custom);

		$data = [
			'tk' => $deviceToken,
			'content' =>$msg,
			'mid' =>$mid,
			'title' =>$title,
			'custom'=>json_encode($custom),
			'image' => "Default.png",
			'sound'=>'default'
		];

		return $data;
	}

	public static function getDueOrderNewOrderApplePushMsgPackage(string $deviceToken, string $mid, array $custom)
	{
		$data = [
			'tk' => $deviceToken,
			'mid' =>$mid,
			'custom'=>json_encode($custom),
			'image' => "Default.png",
			'sound'=>'default'
		];

		return $data;
	}

}


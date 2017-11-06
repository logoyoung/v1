<?php 
/**
 * 欢朋发布环境配置文件
 *   
 **/
//基本目录
define('WEBSITE_ROOT', '/usr/local/huanpeng/');//项目目录
define('INCLUDE_DIR', WEBSITE_ROOT . 'include/');//库目录
define('LOG_DIR', '/data/logs/');//日志目录
define('IMAGE_DIR', '/data/huanpeng-img/');//图片目录
define('VIDEO_TMP', '/data/tmp/');//录像临时存放目录

// 域名
define('DOMAINNAME_PRO', 'www.huanpeng.com');//正式域名
define('DOMAINNAME_PRO_IMG', 'img.huanpeng.com');//正式图片域名
define('DOMAINNAME_DEV', 'dev.huanpeng.com');//开发域名
define('DOMAINNAME_DEV_IMG', 'dev-img.huanpeng.com');//开发图片域名
define('WEBSITE_TPL', WEBSITE_ROOT . 'tpl/');//视图模版目录
define('WEBSITE_MAIN', WEBSITE_ROOT . 'htdocs/');//web访问目录
define('WEBSITE_PERSON', WEBSITE_MAIN . "personal/");//web个人中心目录

// 日志文件
define('LOGFN_GENERAL', LOG_DIR . 'huanpeng_general.log');//访问日志
define('LOGFN_DBH_DBUG', LOG_DIR . 'huanpeng_debug_dbh.log');//开发数据库错误日志
define('LOGFN_VIDEO_SAVE_SUC', LOG_DIR . 'huanpeng_video_save_success.log');//录像保存成功日志
define('LOGFN_VIDEO_SAVE_ERR', LOG_DIR . 'huanpeng_video_save_error.log');//录像生成失败日志
define('LOGFN_REQUEST_ERR', LOG_DIR . 'huanpeng_request.error.log');//web访问错误日志
define('LOGFN_VIDEO_MERGE_ERR', LOG_DIR . 'video_merge.error.log');//录像合并错误日志
define('LOGFN_SEND_MSG_ERR', LOG_DIR."huanpeng_sendMsg.log");//欢朋消息日志
define('LOGFN_WX_PAY', LOG_DIR."huanpeng_wx_pay.log");//微信支付日志

//直播状态 0:直播创建 1:未获得直播流名称  2：非正常结束 3：超时
//100：正在直播   101：直播结束  102:录像保存中  103:生成录像
define('LIVE_CREATE', 0);
define('LIVE_CREATE_NOSTREAM', 1);
define('LIVE_ABNOMAL_END', 2);
define('LIVE_TIMEOUT', 3);
define('LIVE_CLIENT_CREATE', 50);
define('LIVE', 100);
define('LIVE_STOP', 101);
define('LIVE_SAVING', 102);
define('LIVE_VIDEO', 103);

//保存录像调用方式 0:主动保存  1:超时保存
define('VIDEO_SAVETYPE_CALL', 0);
define('VIDEO_SAVETYPE_TIMEOUT', 1);

//录像状态 0:待发布 1:审核中 2:已发布 3:审核未通过 100:录像已被删除
define('VIDEO_WAIT', 0);
define('VIDEO_UNPUBLISH', 1);
define('VIDEO', 2);
define('VIDEO_UNPASS', 3);
define('VIDEO_DEL', 100);

//未发布录像过期时间[单位:天]
define('VIDEO_TIMEOUT', 15);

// 直播室
define('LIVEROOM_ANONYMOUS', 3000000000);

//首页直播推荐数量
define("LIVE_RECOMMENT_NUMBER", 6);

//邮箱认证状态 0:未填写, 1:填写未认证, 2:认证通过,
define('EMAIL_NOT', 0);
define('EMAIL_UNPASS', 1);
define('EMAIL_PASS', 2);

//邮箱认证过期时间
define('EMAIL_CERT_OUTTIME', 86400);

//实名认证状态 1:待审核, 100:审核未通过, 101:审核通过
define("RN_NOT", 0);
define('RN_WAIT', 1);
define('RN_UNPASS', 100);
define('RN_PASS', 101);

//银行卡认证状态 1:审核中, 100:审核未通过, 101:审核通过
define('BANK_CERT_NOT', 0);
define('BANK_CERT_WAIT', 1);
define('BANK_CERT_UNPASS', 100);
define("BANK_CERT_PASS", 101);


//账单纪录状态 0:送礼以及主播的收入, 1:充值, 2:提现, 3:主播兑换
define('BILL_GIFT', 0);
define('BILL_RECHARGE', 1);
define('BILL_CASH', 2);
define('BILL_EXCHANGE', 3);
define('BILL_CASH_COIN', 2);
define('BILL_CASH_BEAN', 3);

//任务状态
define("TASK_STAT_ONLINE", 1);
define("TASK_STAT_STOP", 0);
//任务纪录状态	0:未完成 , 1:已经完成, 2:已经领取
define("TASK_UNFINISH", 0);
define("TASK_FINISHED", 1);
define("TASK_BEAN_RECEIVED", 2);

//推送房间ID
define("PUSH_ROOM_ID", 1);
define("LIVE_START_NOTICE_RECEIVE", 1);
define("LIVE_START_NOTICE_NOT", 0);

//审核模式
define("CHECK_HEAD", 1);  //头像审核
define("CHECK_NICK", 2);//昵称审核
define("CHECK_TITLE", 3);//直播标题审核
define("CHECK_COMMENT", 4);//评论审核
define("CHECK_BARRAGE", 5);//／弹幕审核
define("CHECK_NOTICE", 6);//／主播公告审核

//头像审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
define("USER_PIC_WAIT", 0);
define("USER_PIC_PASS", 1);
define("USER_PIC_UNPASS",2);
define("USER_PIC_AUTO_PASS", 3);
define("USER_PIC_AUTO_UNPASS",4);

//昵称审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
define('USER_NICK_WAIT', 0);
define('USER_NICK_PASS', 1);
define('USER_NICK_UNPASS',2);
define('USER_NICK_AUTO_PASS', 3);
define('USER_NICK_AUTO_UNPASS',4);

//直播标题审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
define('LIVE_TITLE_WAIT', 0);
define('LIVE_TITLE_PASS', 1);
define('LIVE_TITLE_UNPASS',2);
define('LIVE_TITLE_AUTO_PASS', 3);
define('LIVE_TITLE_AUTO_UNPASS',4);

//评论审核状态 0:待审核, 1:人工审核通过, 2:人工审核未通过 ,3:机器审核通过,4:机器审核未通过
define('COMMENT_WAIT', 0);
define('COMMENT_PASS', 1);
define('COMMENT_UNPASS',2);
define('COMMENT_AUTO_PASS', 3);
define('COMMENT_AUTO_UNPASS',4);
//游戏类
define('OTHER_GAME', 401);

//room 禁言时间
define("ROOM_SILENCE_TIMEOUT", 3600);

//apple push
define('APPLE_PUSH_PRO',50);

define('RECHARGE_ORDER_FINISH',1);

date_default_timezone_set('Asia/Shanghai');









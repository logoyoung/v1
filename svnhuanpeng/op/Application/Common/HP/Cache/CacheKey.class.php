<?php
// +----------------------------------------------------------------------
// 登记用到的memcacheKey
// 以免后期重复
// +----------------------------------------------------------------------
namespace HP\Cache;

class CacheKey
{
    const FILE_G2F = 'file_g2f_v2_'; #文件FID GUid对应
    const FILE_FID = 'file_fid_v2_'; #文件信息
    
    const USER_BASE = 'user_base_v1_'; #用户基础信息缓存
    const USER_CAPITAL = 'user_capital_v2_'; #用户帐户信息缓存
    const USER_INFO = 'user_info_v1_'; #用户统计信息缓存
    const USER_SIGN = 'user_sign_v2_'; #用户签到信息缓存
    const USER_ISESSION = 'user_session_v1_'; #用户自定义session数据
    
    const PROJECT_BASE = 'project_base_v2_'; #项目基础信息缓存
    const PROJECT_INVEST = 'project_invest_v2_'; #项目投资信息缓存
    const PROJECT_LIST = 'project_list_v1';#项目列表
    
    const MESSAGE_COUNT = 'message_count_v1_'; #用户消息统计
    
    const INDEX_NEW = 'index_new_v2';#首页新闻信息
    const INDEX_STATISTICS = 'index_statistics';#首页统计信息
    const INDEX_PROJECT = 'index_project_v3';#首页项目信息
//    const NEWS_DETAIL = 'new_detail';#新闻详情页面
    
    const HTML_DIV = 'html_div_';#div区块输出constant
    const SECURE_IPSAFETY = 'secure_ipsafety_v2_';#黑名单
    const SECURE_IPSAFETY_INTRA = 'secure_ipsafety_intra';#内网
    const SECURE_ACCOUNT = 'secure_account_v1_';#锁定用户
    const CONTENT_KEFU = 'content_kefu';#客服配置
    
   //审核锁定 zwq 2017年5月16日 =============================
    const DIFF_CHECK_USERPIC = 'diff_check_userpic';#头像审核打开列表锁定当前页uid
    const DIFF_CHECK_REALNAME = 'diff_check_realname';#实名认证审核打开列表锁定当前页uid
    //op用户权限缓存
    const ADMIN_USERINFO = 'admin_userinfo_';
    const ADMIN_USER = 'admin_user_';
   
}

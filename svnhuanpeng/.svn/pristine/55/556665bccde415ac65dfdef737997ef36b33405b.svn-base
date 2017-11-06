<?php
namespace service\rule;
use service\common\AbstractService;
use system\HttpHelper;
use service\rule\param\TextParam;
use Exception;

/**
 *   反垃圾文本识别
 *
 *  接入反垃圾服务
 * PS : 1.此服务调用第三方服务，线上超时时间为1s,目前校验服务为最低级，即使反垃圾服务挂了也不会影响直播服务。
 *      2.关于dev pre 响应慢的问题，因dev 、pre服务器为铁通网络，跨服务商调用ping 近100ms,还存在掉包情况，暂时没办法，忍忍吧。
 *      3.出问题怎么查？
 *        a.所有被反垃圾的都会在shumei_error shumei_access有记录
 *        b.所有请求第方服务的日志都会在http_access.log 或 http_error.log 都会有记录
 *
 *      4.测式设备总是被反垃圾服务怎么办？
 *          反垃圾服务器通过机器学习DeviceId，ip等，可通过管理后台添加白名单解决。
 *
 * @author xuyong xuyong@6.cn
 *
 */
class TextService extends AbstractService
{

    //用于权限认证，由数美提供
    const ACCESS_KEY      = 'ZpG1mFofk3TGp6BtUYDZ';
    //机构码
    const ORGANIZATION    = 'WWNuEnXJEyVkuAVdsZ9d';

    //北京
    const IDC_BJ          = 'bj';
    //深圳
    const IDC_SZ          = 'sz';
    //上海
    const IDC_SH          = 'sh';
    //默认北京机房
    private $_idc         = self::IDC_BJ;
    //api 机房对应的api (默认北京机房)
    public static $apiUrls   = [
        //北京
        self::IDC_BJ => 'http://api.fengkongcloud.com/v2/saas/anti_fraud/text',
        //深圳
        self::IDC_SZ => 'http://api-sz.fengkongcloud.com/v2/saas/anti_fraud/text',
        //上海
        self::IDC_SH => 'http://api-sh.fengkongcloud.com/v2/saas/anti_fraud/text',
    ];


    //文本类型 直播垃圾文本识别
    const TYPE_ZHIBO = 'ZHIBO';
    //文本类型 电商文本识别
    const TYPE_ECOM  = 'ECOM';
    //文本类型 游戏平台文本识别
    const TYPE_GAME  = 'GAME';

    //channel 文本渠道 参数 直播间评论和弹幕
    const CHANNEL_COMMENT       = 'COMMENT';
    //channel 文本渠道 参数 全局群聊
    const CHANNEL_GROUP_CHAT    = 'GROUP_CHAT';
    //channel 文本渠道 参数 战队群聊
    const CHANNEL_TEAM_CHAT     = 'TEAM_CHAT';
    //channel 文本渠道 参数 动态评论及个人发帖
    const CHANNEL_DYNAMIC_COMMENT = 'DYNAMIC_COMMENT';
    //channel 文本渠道 参数 昵称变更
    const CHANNEL_NICKNAME      = 'NICKNAME';
    //channel 文本渠道 参数 描述变更
    const CHANNEL_PROFILE       = 'PROFILE';
    //channel 文本渠道 参数 SIGNATURE：签名变更
    const CHANNEL_SIGNATURE     = 'SIGNATURE';
    //channel 文本渠道 参数 私信聊天
    const CHANNEL_MESSAGE       = 'MESSAGE';
    //channel 文本渠道 参数 THEME：主题变更
    const CHANNEL_THEME         = 'THEME';
    //channel 文本渠道 参数 商品描述
    const CHANNEL_PRODUCT       = 'PRODUCT';

    //风险级别 PASS为系统认定没有问题的，建议直接放行
    const RISK_LEVEL_PASS    = 'PASS';
    //风险级别 REJECT为系统认定有问题的，建议直接拦截
    const RISK_LEVEL_REJECT  = 'REJECT';
    //风险级别 REVIEW为系统不确定的，建议人工审核
    const RISK_LEVEL_REVIEW  = 'REVIEW';

    //数美 返回code码 成功
    const SHUMEI_CODE_SUCCESS      = 1100;
    //数美 返回code码 参数不合法
    const SHUMEI_CODE_PARAM_ERROR  = 1902;
    //数美 返回code码 服务失败
    const SHUMEI_CODE_SERVER_ERROR = 1903;
    //数美 返回code码 余额不足
    const SHUMEI_CODE_MONEY_ERROR  = 9100;
    //数美 返回code码 无权限操作
    const SHUMEI_CODE_AUTH_ERROR   = 9101;
    //数美 error msg
    public static $shumeiErrorMsg = [
        self::SHUMEI_CODE_PARAM_ERROR  => '参数不合法',
        self::SHUMEI_CODE_SERVER_ERROR => '服务失败,数美服务异常',
        self::SHUMEI_CODE_MONEY_ERROR  => '余额不足',
        self::SHUMEI_CODE_AUTH_ERROR   => '无权限操作',
    ];

    //无效的idc
    const ERROR_CODE_IDC   = -16100;
    //缺少必要参数
    const ERROR_CODE_PARAM = -16102;

    public static $errorMsg = [
        self::ERROR_CODE_IDC   => '无效的idc',
        self::ERROR_CODE_PARAM => '缺少必要参数',
    ];

    public static $whiteChannel   = [
        self::CHANNEL_THEME,
        self::CHANNEL_NICKNAME,
    ];

    public static $whiteFilterMsg = [
        '短时间大量发帖',
        '账户行为内容可疑',
        '全局可疑内容',
    ];

    private $_param   = [];
    private $_qid     = 0;
    private $_timeout = 1;
    private $_timeoutDev = 2;
    private $_logErrorName  = 'shumei_error';
    private $_logDebugName  = 'shumei_access';
    private $_result  = [];
    private $_logParams = [];
    private $_callLevel = 0;

    /**
     * 设置接口调用机房（默认不需要设置）
     * @param string $idc bj,sh,sz (建义用定义的常量)
     */
    public function setApiIdc($idc)
    {
        if(!isset(self::$urls[$idc]))
        {
            $code = self::ERROR_CODE_IDC;
            throw new Exception(self::$errorMsg[$code], code);
        }

        $this->_idc = $idc;
        return $this;
    }

    public function setCallLevel($callLevel = true)
    {
        $this->_callLevel = $callLevel;
        return $this;
    }

     /**
     * 调用机房 (默认北京机房)
     * @return string
     */
    public function getApiIdc()
    {
        return $this->_idc ? $this->_idc : self::IDC_BJ;
    }

    public function getCallLevel()
    {
        return $this->_callLevel;
    }

    /**
     * 获取调用 api
     * @return string rul
     */
    public function getApiUrl()
    {
        return self::$apiUrls[$this->getApiIdc()];
    }

    /**
     *  设置 校验的文本
     *
     * @param  $text            校验的文本
     * @param string $uid       用户uid 传空为系统会自动生成
     * @param string $channel  文本渠道  建义根据自己的场景选用，
     *                         具体支持的调用本类的CHANNEL_开头的常量
     *                         直播间评论和弹幕
     *
     * @param string $type   默认是文本类型直播 建义使用 TYPE_开头的常量
     *                       支持 ZHIBO：直播垃圾文本识别，ECOM：电商文本识别，GAME：游戏平台文本识别
     */
    public function addText($text, $uid = '', $channel = 'GROUP_CHAT', $type = 'ZHIBO')
    {
        $param = new TextParam();
        $param->setAccessKey(self::ACCESS_KEY);
        $param->setTokenId( $uid ? $uid : uniqid());
        $param->setText($text);
        $param->setChannel($channel);
        $this->_param[$this->_qid++] = $param;

        return $param;
    }

    /**
     * 校验文本
     * @return boolean | 单个返回boolean 多个并发 返回 array
     */
    public function checkStatus()
    {
        $result = $this->getResult();
        $total  = count($result);
        if($total == 1)
        {
            $result = $result[0];

            if(!$this->_checkResult($result,0))
            {
                return false;
            }

            return $this->_checkResultLevel($result,0);
        }

        $checkResult = [];
        foreach ($result as $key => $val)
        {
            $checkResult[$key] = ($this->_checkResult($val,$key) && $this->_checkResultLevel($val,$key)) ? true : false;
        }

        return $checkResult;
    }

    /**
     * 获取验结果
     * @return array
     */
    public function getResult()
    {

        if(!$this->_param)
        {
            $code = self::ERROR_CODE_PARAM;
            throw new Exception(self::$errorMsg[$code], code);
        }

        if($this->_result)
        {
            return $this->_result;
        }

        $httpHelper =  new HttpHelper();
        foreach ($this->_param as $qid => $v )
        {
            $httpHelper->addPost($this->getApiUrl(), $v->getParam(), $this->_getTimeout());
            $this->_logParams[$qid] = $v->getParam();
        }

        $result = $httpHelper->getResult();
        $httpHelper = null;
        $data   = [];
        foreach ($result as $k => $r)
        {

            //查查看http error log
            if($r['status'] != HttpHelper::HTTP_OK)
            {
                $this->_result[$k] = false;
                continue;
            }

            $r = json_decode($r['content'],true);
            if($r['code'] == self::SHUMEI_CODE_SUCCESS)
            {
                $r['detail'] = json_decode($r['detail'],true);
            }

            $this->_result[$k] = $r;
        }

        return $this->_result;
    }

    private function _getTimeout()
    {
        if($GLOBALS['env'] == 'DEV' || $GLOBALS['env'] == 'PRE') {
            return $this->_timeoutDev;
        }

        return $this->_timeout;
    }

    public function close()
    {
        $this->_result    = [];
        $this->_param     = [];
        $this->_logParams = [];
        $this->_errorLevel = false;
        $this->_callLevel = 0;
    }

    private function _checkResult($result,$qkey)
    {

        $logMsg = $this->_getLogParam($result,$qkey);
        //dev pre 环境 记录全量日志
        if($GLOBALS['env'] == 'DEV' || $GLOBALS['env'] == 'PRE') {
            write_log("info|{$logMsg}|caller:".$this->getCaller(),$this->_logDebugName);
        }

        if($result == false )
        {
            write_log("error|调用数美http服务异常，请检查网络;{$logMsg}".$this->getCaller(),$this->_logErrorName);
            return $this->getCallLevel() ? false : true;
        }

        if(!isset($result['code']))
        {
            write_log("error|数美接口响应数据结构有调整，请查看最新文档调整程序;{$logMsg}".$this->getCaller(),$this->_logErrorName);
            return $this->getCallLevel() ? false : true;;
        }

        $code  = $result['code'];
        $codes = array_keys(self::$shumeiErrorMsg);
        if($code != self::SHUMEI_CODE_SUCCESS )
        {
            $errorMsg = isset(self::$shumeiErrorMsg[$code]) ? self::$shumeiErrorMsg[$code] : '数美返回未知的错误码';
              write_log("error|error_code:{$code};error_msg:{$errorMsg};{$logMsg}".$this->getCaller(),$this->_logErrorName);
            return $this->getCallLevel() ? false : true;;
        }

        return $result;
    }

    private function _checkResultLevel($result,$qkey)
    {
        if(!$result)
        {
            return $this->getCallLevel() ? false : true;
        }

        $logMsg = $this->_getLogParam($result,$qkey);
        $result['riskLevel'] = (string) $result['riskLevel'];

        switch ($result['riskLevel']) {

            //风险级别 PASS为系统认定没有问题的，建议直接放行
            case self::RISK_LEVEL_PASS:
                return true;

            //风险级别 REJECT为系统认定有问题的，建议直接拦截
            case self::RISK_LEVEL_REJECT:

                //是否白名单校验项 (通过短时间内大量发贴的用户)
                if(isset($this->_param[$qkey]) && in_array($this->_param[$qkey]->getChannel(), self::$whiteChannel))
                {
                    $description = isset($result['detail']['description']) ? trim($result['detail']['description']) : '';
                    if($description && in_array($description, self::$whiteFilterMsg))
                    {

                        write_log("notice|放行通过,短时间内大量发贴;channel:{$this->_param[$qkey]->getChannel()};".self::RISK_LEVEL_REJECT.":".$logMsg.$this->getCaller(),$this->_logDebugName);
                        return true;
                    }
                }

                write_log("notice|".self::RISK_LEVEL_REJECT.":".$logMsg.$this->getCaller(),$this->_logDebugName);
                return false;

            //风险级别 REVIEW为系统不确定的，建议人工审核
            case self::RISK_LEVEL_REVIEW:
                write_log("notice|".self::RISK_LEVEL_REVIEW.":系统不确定的，建议人工审核".$logMsg.$this->getCaller(),$this->_logDebugName);
                return $this->getCallLevel() ? false : true;

            default:
                write_log("notice|".$result['riskLevel'].":未知的风险级别，请查看最新文档调整程序".$logMsg.$this->getCaller(),$this->_logDebugName);
                return true;
        }

    }

    private function _getLogParam($result,$k)
    {
        return 'param:'.json_encode($this->_logParams[$k],JSON_UNESCAPED_UNICODE).";result:".json_encode($result,JSON_UNESCAPED_UNICODE);
    }
}
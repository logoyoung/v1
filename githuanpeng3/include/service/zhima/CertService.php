<?php
namespace service\zhima;
use Exception;
use service\common\AbstractService;
use lib\user\ZhimaCert;
use service\user\UserCertCreateService;
use service\event\EventManager;
use service\zhima\sdk\zmop2017919\ZmopClient;
use service\zhima\sdk\zmop2017919\request\ZhimaCustomerCertificationInitializeRequest;
use service\zhima\sdk\zmop2017919\request\ZhimaCustomerCertificationQueryRequest;
use service\zhima\sdk\zmop2017919\request\ZhimaCustomerCertificationCertifyRequest;

class CertService extends AbstractService
{
    //芝麻信用网关地址
    private $_gatewayUrl       = 'https://zmopenapi.zmxy.com.cn/openapi.do'; //'https://openapi.alipay.com/gateway.do';//

    //商户私钥文件
    private $_privateKeyFile   = INCLUDE_DIR.'zmopSDK/app_private_key.pem';
    //芝麻公钥文件
    private $_zmPublicKeyFile  = INCLUDE_DIR.'zmopSDK/zm_public_key.pem';
    //数据编码格式
    private $_charset          = 'UTF-8';
    //芝麻分配给商户的 appId
    private $_appId            = '1001878';

    //芝麻认证产品码,示例值为真实的产品码
    private $_productCode      = 'w1010100000000002978';
    //merchantID 商户号
    private $_openId           = '268821000000044492923';

    //身份证
    const TYPE_IDCARDE  = 10;
    const STATUS_INIT   = 1;
    const STATUS_SUCC   = 2;
    const STATUS_ERROR  = 3;
    const BIZ_CODE_FACE = 1;

    const FACE_SDK = 'FACE_SDK';

    private $_uid;
    private $_type;
    private $_certName;
    private $_certno;
    private $_transactionId;
    private $_bizCode;
    private $_bizNo;
    private $_status;
    private $_errorMsg;
    private $_biz_etime;
    private $_utime;
    private $_zhimaDb;
    private $_channel;
    private $_zhimaApiTimeout;
    private $_log = 'zhima_cert_access';
    //http调用芝麻api默认超时时间
    private $_httpTimeout = 7;

    public function setUid($uid)
    {
        $this->_uid = $uid;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setType($type)
    {
        $this->_type = $type;
    }

    public function getType()
    {
        return $this->_type ? $this->_type : self::TYPE_IDCARDE;
    }

    public function setCertName($certName)
    {
        $this->_certName = $certName;
        return $this;
    }

    public function getCertName()
    {
        return $this->_certName;
    }

    public function setCertno($certno)
    {
        $this->_certno = $certno;
        return $this;
    }

    public function getCertno()
    {
        return $this->_certno;
    }

    public function setTransactionId($transactionId)
    {
        $this->_transactionId = $transactionId;
    }

    public function getTransactionId()
    {
        return $this->_transactionId;
    }

    public function setBizCode($bizCode)
    {
        $this->_bizCode = $bizCode;
    }

    public function getBizCode()
    {
        return $this->_bizCode ? $this->_bizCode : self::FACE_SDK;
    }

    public function setBizNo($bizNo)
    {
        $this->_bizNo = $bizNo;
        return $this;
    }

    public function getBizNo()
    {
        return $this->_bizNo;
    }

    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function setErrorMsg($errorMsg)
    {
        $this->_errorMsg = $errorMsg;
        return $this;
    }

    public function getErrorMsg()
    {
        return $this->_errorMsg;
    }

    public function setBizEtime($bizEtime)
    {
        $this->_biz_etime = $bizEtime;
        return $this;
    }

    public function getBizEtime()
    {
        return $this->_biz_etime;
    }

    public function setUtime($utime)
    {
        $this->_utime = $utime;
        return $this;
    }

    public function getUtime()
    {
        return $this->_utime ? $this->_utime : date('Y-m-d H:i:s');
    }

    public function setChannel($channel)
    {
        $this->_channel = $channel;
        return $this;
    }

    public function getChannel()
    {
        return $this->_channel ? $this->_channel : '';
    }

    public function setZhimaHttpTimeout($timeout)
    {
        $this->_zhimaApiTimeout = (int) $timeout;
        return $this;
    }

    public function getZhimaHttpTimeout()
    {
        return $this->_zhimaApiTimeout > 0 ? $this->_zhimaApiTimeout : $this->_httpTimeout;
    }

    /**
     * 初始化认证
     * @return array
     */
    public function getZhimaInitBizno()
    {
        $result = [
            'uid'            => $this->getUid(),
            'merchant_id'    => $this->_openId,
            'transaction_id' => '',
            'biz_no'         => '',
        ];

        $identityParam = [
            'identity_type' => 'CERT_INFO',
            //身份证
            'cert_type'     => 'IDENTITY_CARD',
            //证件姓名
            'cert_name'     => $this->getCertName(),
            //证件号
            'cert_no'       => $this->getCertno(),
        ];

        $identityParam = hp_json_encode($identityParam);
        $log = "uid:{$this->getUid()};".$identityParam;
        $this->log("notice|收到初始化请求;{$log};line:".__LINE__);

        //判断有过初始化请求
        $certData = $this->getZhimaCertByUidCertNo();
        if($certData && ($certData['status'] == self::STATUS_INIT) && (strtotime($certData['biz_etime']) > time()) )
        {
            $result['transaction_id'] = $certData['transaction_id'];
            $result['biz_no']         = $certData['biz_no'];
            $log  .=";result:".hp_json_encode($result);
            $this->log("success|初始化芝麻整个流程成功;复用上次初始化信息; param:{$log};line:".__LINE__);
            return $result;
        }

        try {

            $result['transaction_id'] = $this->createTid();
            $client  = new ZmopClient($this->_gatewayUrl,$this->_appId,$this->_charset,$this->_privateKeyFile,$this->_zmPublicKeyFile,$this->getZhimaHttpTimeout());
            $request = new ZhimaCustomerCertificationInitializeRequest();
            $request->setChannel('appsdk');
            $request->setPlatform('zmop');
            $request->setTransactionId($result['transaction_id']);
            //w1010100000000002978
            $request->setProductCode($this->_productCode);
            //FACE
            $request->setBizCode($this->getBizCode());
            $request->setIdentityParam($identityParam);
            $request->setMerchantConfig('{"need_user_authorization":"false"}');
            $request->setExtBizParam('{}');// 必要参数
            $response = $client->execute($request);

            if(!$response)
            {
                $this->log("error|初始化认证芝麻接口无响应，可能是网络异常;param:{$log};line:".__LINE__);
                return false;
            }

            if(!$response->success)
            {
                $this->log("error|初始化认证ali接口异常;param:{$log};response:".hp_json_encode($response)."|line:".__LINE__);
                return false;
            }

            $result['biz_no'] = $response->biz_no;
            $bizNo = $response->biz_no;
            $log  .=";result:".hp_json_encode($result);
            $this->log("nitoce|获取芝麻api数据成功;msg:param:{$log}");

            $zhimaCertDb = $this->getZhimaDb();
            $utime = $this->getUtime();
            //记录写入数据库
            $addDb = $zhimaCertDb->add(
                $this->getUid(),
                self::TYPE_IDCARDE,
                $this->getCertName(),
                $this->getCertno(),
                $result['transaction_id'],
                $this->getBizCode(),
                $bizNo,
                self::STATUS_INIT,
                self::getDefaultBizEtime(),
                $utime
            );

            if(!$addDb)
            {
                $this->log("error|初始化认证记录写入数据库异常;param:{$log};line:".__LINE__);
                return false;
            }

            $this->log("success|初始化芝麻认证成功;param:{$log};line:".__LINE__);

            return $result;

        } catch (Exception $e) {
            $this->log("error|初始化芝麻认证异常;msg:{$e->getMessage()}, param:{$log}; trace:".$e->getTraceAsString());
            return false;
        }

    }

    /**
     *  认证成功
     * @return [type] [description]
     */
    public function zhimaCertSuccss()
    {
        try {

            $log      = "uid:{$this->getUid()};transactionId:{$this->getTransactionId()}";
            $this->log("nitoce|收到验证成功加写数据库请求，{$log};line:".__LINE__.';class:'.__CLASS__);

            $certData = $this->getCertDataByTidUid();
            if(!$certData)
            {
                $this->log("warning|收到验证成功写数据库请求，库里不存在此记录{$log};line:".__LINE__.';class:'.__CLASS__);
                return false;
            }

            $log      .= "cert_name:{$certData['cert_name']};cert_no:{$certData['cert_no']}";
            $userCert = new UserCertCreateService;
            $userCert->setUid($this->getUid());
            $userCert->setCertName($certData['cert_name']);
            $userCert->setCertno($certData['cert_no']);
            $userCert->setTransactionId($this->getTransactionId());
            $userCert->setUtime($this->getUtime());
            $userCert->setZhimaStatus(self::STATUS_SUCC);

            if(!$userCert->zhimaCertSuccss())
            {
                throw new Exception('更新数据库异常;line:'.__LINE__);
            }

            $this->log("success|验证成功，更新数据库相关操作成功{$log}");
            $event = new EventManager();
            $event->trigger($event::ACTION_ZHIMA_CERT_SUCC,['uid' => $this->getUid()]);
            $event = null;

            return true;

        } catch (Exception $e) {
            $this->log("error|验证成功处理失败，msg:{$e->getMessage()}; {$log};class:".__CLASS__);
            return false;
        }
    }

    /**
     * 认证失败
     * @return [type] [description]
     */
    public function zhimaCertError()
    {
        $zhimaCertDb    = $this->getZhimaDb();
        $log            = "uid:{$this->getUid()};transactionId:{$this->getTransactionId()}";
        if(!$this->getCertDataByTidUid())
        {
            $this->log("nitoce|验证失败时，更新数据库时，库里不存在此记录，{$log};line:".__LINE__.';class:'.__CLASS__);
            return false;
        }

        $result = $zhimaCertDb->updateStatusByTidUid($this->getTransactionId(),$this->getUid(),self::STATUS_ERROR,$this->getErrorMsg());
        if($result === false)
        {
            $this->log("error|验证失败时，更新数据库状态异常，{$log}");
            return false;
        }

        $this->log("success|验证失败时，更新数据库状态成功，{$log}");
        return true;
    }

    /**
     * 获取biz_no有限时间
     * @return [type] [description]
     */
    public static function getDefaultBizEtime()
    {
        return date('Y-m-d H:i:s',(time() + 82800));
    }

    public function getAliZhimaCertStatus()
    {
        $client  = new ZmopClient($this->_gatewayUrl,$this->_appId,$this->_charset,$this->_privateKeyFile,$this->_zmPublicKeyFile);
        $request = new ZhimaCustomerCertificationQueryRequest();
        $request->setChannel('apppc');
        $request->setPlatform('zmop');
        $request->setBizNo($this->getBizNo());// 必要参数

        try {

            $response = $client->execute($request);

            if($response->success != 1)
            {
                throw new Exception('芝麻接口无响应;line:'.__LINE__);
            }

            $this->log("notice|query request:".hp_json_encode($response));

            if(strcasecmp($response->passed, 'false') === 0)
            {
                $this->log("notice|没通过验证; biz_no:{$this->getBizNo()};query request:".hp_json_encode($response));
                return false;
            }

            return $response;

        } catch (Exception $e) {
            $this->log("error|获取芝麻校验状态异常; biz_no:{$this->getBizNo()}; msg:{$e->getMessage()}");
            return false;
        }
    }

    public function initH5CertUrl()
    {
        $client  = new ZmopClient($this->_gatewayUrl,$this->_appId,$this->_charset,$this->_privateKeyFile,$this->_zmPublicKeyFile);
        $request = new ZhimaCustomerCertificationCertifyRequest();
        $request->setChannel("apppc");
        $request->setPlatform("zmop");
        $request->setBizNo($this->getBizNo());// 必要参数
        $request->setReturnUrl("http://www.huanepng.com");// 必要参数

        try {

            $url = $client->generatePageRedirectInvokeUrl($request);
            $this->log("success|生成h5校验url成功; biz_no:{$this->getBizNo()}; h5 url:{$url}");
            return $url;

        } catch (Exception $e) {
            $this->log("error|生成h5校验url失败; biz_no:{$this->getBizNo()}; msg:{$e->getMessage()}");
            return false;
        }

    }

    public function getZhimaDb()
    {
        if(!$this->_zhimaDb)
        {
            $this->_zhimaDb = new ZhimaCert;
        }

        return $this->_zhimaDb;
    }

    public function getCertDataByTidUid()
    {
        $zhimaCertDb = $this->getZhimaDb();
        $zhimaCertDb->setMaster(true);
        return $zhimaCertDb->getZhimaCertByYidUid($this->getTransactionId(), $this->getUid());
    }

    public function getZhimaCertByUidCertNo()
    {
        $zhimaCertDb = $this->getZhimaDb();
        $zhimaCertDb->setMaster(true);
        return $zhimaCertDb->getZhimaCertByUidCertNoStatus($this->getUid(),$this->getCertno(),self::STATUS_INIT);
    }

    public function log($msg)
    {
        write_log($msg.';class:'.__CLASS__,$this->_log);
    }

    private function createTid()
    {
        return 'HP'.substr($this->getUid(),-3).date('YmdHis').mt_rand(100, 999);
    }

}
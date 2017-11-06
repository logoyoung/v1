<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/7/27
 * Time: 10:38
 */
namespace service\weixin;
use system\HttpHelper;
use system\RedisHelper;
/**
 * 微信企业号服务
 * Class WeiXinEnterpriseSevice
 * @package test\apiTest
 * 基础频率
每企业调用单个cgi/api不可超过1000次/分，30000次/小时
每企业调用接口的并发数不可超过300
企业每ip调用接口不可超过20000次/分，600000次/小时
第三方应用提供商每ip调用接口不可超过40000次/分，1200000次/小时
发消息频率
每企业不可超过帐号上限数*30人次/天，发消息频率不计入基础频率
创建帐号频率
每企业创建帐号数不可超过帐号上限数*3/月
创建应用频率
每企业最大应用数限制为30个，创建应用次数不可超过30*3/月
创建群聊频率
每个企业号成员（群的创建者）创建群聊个数不可超过500/天
 *以上所有频率，按天拦截则被屏蔽一天（自然天），按月拦截则屏蔽一个月（30天，非自然月），按分钟拦截则被屏蔽60秒，按小时拦截则被屏蔽60分钟。
 */
class WeiXinEnterpriseService
{
    //企业号id
    private $corpid = 'wx1072dfe6cadb7f76';
   //接口报警 应用ID
    const API_ALERT_AGNET_ID = 1000002;
    const ZENTAO_ALERT_AGNET_ID = 1000003;
    //100002接口报警秘钥
    private $_corpsecretArr =[
        self::API_ALERT_AGNET_ID    =>'XZBakxDCHIfa1tNEeqvT6a7XLyV2PgmGcaAc5FShMrE',
        self::ZENTAO_ALERT_AGNET_ID =>'c6FdjRt73Yjc6d6-xTF7V1UpDCG2Fnp2IW7AmLCpOXw',
        ];
    private $accessToken;
    private $_fromFile = 0;
    private $_fromApi = 0;
    private $_timeOut = 2;
    private $corpsecret;
    public $_agentId = self::API_ALERT_AGNET_ID;
    public  $redis;
    const ACCESS_TOKEN_KEY = 'ACCESS_TOKEN';
    //accessToken过期时间
    const ACCESS_TOKEN_KEY_EXPIRE = 7200;
    const WEIXIN_ACCESS_TOKEN_FILE = 'weixin_accesstoken';

    public function setCorpid($corpid)
    {
        $this->corpid = $corpid;
        return $this;
    }
    public function setCorpsecret($corpsecret ='')
    {
        if(empty($corpsecret))
        {
            $this->corpsecret =  $this->_corpsecretArr[$this->_agentId];
        }else
        {
            $this->corpsecret = $corpsecret;
        }

        return $this;
    }
    public function setAgentId($agentId)
    {
        $this->_agentId = $agentId;
        return $this;
    }
    public function setFromFile($fromFile = true)
    {
        $this->_fromFile = $fromFile;
        return $this;
    }
    public function setFromApi($fromApi = true)
    {
        $this->_fromApi = $fromApi;
        return $this;
    }
    public function getFromFile()
    {
        return $this->_fromFile;
    }
    public function HttpHelper()
    {
        return new HttpHelper();
    }
    /**
     * 获取redis资源
     * @return \redis
     */
    public function getRedis()
    {
        if (is_null($this->redis))
        {
            $this->redis = RedisHelper::getInstance("huanpeng");
        }
        return $this->redis;
    }

    /**
     * API获取AccessToken
     * @return bool
     */

    private function _getAccessToken()
    {
        //获取AccessToken
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';
        $params = [
            'corpid'=>$this->corpid,
            'corpsecret'=>$this->corpsecret,
        ];
        $res = $this->curlApi($url,$params);
        if(isset($res['access_token']))
        {
            if(!empty($res['access_token']))
            {

                $this->accessToken = $res['access_token'];
                write_log('accessToken:'.$this->accessToken.'|url'.$url);
                return $this->accessToken;
            }
        }

        $msg = json_encode($res);
        write_log('微信API错误:'.$msg.'|'.$url,'huanpeng_apiTest');
        return false;

    }

    /**
     * curl 微信api 返回json_decode值
     * @param $url
     * @param $params
     * @return bool|mixed
     */
    public function curlApi($url,$params,$post = 0)
    {
        //根据不同环境规定对微信api不同的超时设置
        $env = get_hp_env();
        $this->_timeOut = ($env == 'dev'|| $env =='pre') ?  10 : 2;
        $Curl = $this->HttpHelper();
        if($post)
        {
            $Curl->addPost($url,$params,$this->_timeOut);
        }else
        {
            $Curl->addGet($url,$params,$this->_timeOut);
        }
        $res = $Curl->getResult();
        foreach ($res as $value)
        {
            if ($value['status'] == 0)
            {
                $msg = json_encode($value);
                write_log($msg, 'huanpeng_apiTest');
                return false;
            }else
            {
                if (isset($value['content'])) {
                    $res = $value['content'];
                    return $res;
                }
            }
        }
        return false;
    }

    /**
     * 获取AccessToken
     * @param bool $expire
     * @return bool|string|array
     */
    public function getAccessToken($expire = false)
    {
        //强制刷新所有应用token
        if($this->_fromApi)
        {
            $accessToken = [];
            foreach ($this->_corpsecretArr as $key => $value)
            {
                $this->corpsecret = $value;
                //文件access重置
                $this->accessToken = $this->_getAccessToken();
                if($this->accessToken  != false)
                {
                    //redis重置
                    $status = $this->getRedis()->set(self::ACCESS_TOKEN_KEY.$this->_agentId,$this->accessToken);
                    if($status)
                    {
                        $this->getRedis()->expire(self::ACCESS_TOKEN_KEY.$this->_agentId,self::ACCESS_TOKEN_KEY_EXPIRE);
                    }else
                    {
                        $msg = '设置accessToken Redis缓存失败';
                        write_log($msg,'huanpeng_apiTest');
                    }
                    $accessToken[$key] =  $this->accessToken;
                }
            }
            return $accessToken;
        }
        //设置对应应用秘钥
        $this->setCorpsecret();
        //判断是否走文件
        if($this->_fromFile)
        {
            $this->accessToken = $this->getAccessTokenFromFile();
            return $this->accessToken;

        }else
        {
            //走redis
            if(!$expire)
            {
                $this->accessToken = $this->getRedis()->get(self::ACCESS_TOKEN_KEY.$this->_agentId);
                if($this->accessToken)
                {
                    return $this->accessToken;
                }
            }
            //api获取AccessToken
            $this->accessToken  = $this->_getAccessToken();
            if($this->accessToken  != false)
            {
                $status = $this->getRedis()->set(self::ACCESS_TOKEN_KEY.$this->_agentId,$this->accessToken);
                if($status)
                {
                    $this->getRedis()->expire(self::ACCESS_TOKEN_KEY.$this->_agentId,self::ACCESS_TOKEN_KEY_EXPIRE);
                }else
                {
                    $msg = '设置accessToken Redis缓存失败';
                    write_log($msg,'huanpeng_apiTest');
                }
                return $this->accessToken ;
            }
            return false;
        }

    }

    /**
     * 获取部门列表
     * 微信api返回{"errcode": 0,"errmsg": "ok",、"department": [{"id": 1,"name": "欢朋test","parentid": 0,"order": 2147483447},]}
     * @return bool
     */
    public function getDepartmentList()
    {
        $this->accessToken = $this->getAccessToken();
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/department/list';
        $params = ['access_token'=>$this->accessToken];
        $res = $this->curlApi($url,$params);
        if(isset($res['department']))
        {
            if(!empty($res['department']))
            {
                $departmentList = $res['department'];
                return $departmentList;
            }
        }
        $msg = json_encode($res);
        write_log('微信API错误:'.$msg.'|'.$url,'huanpeng_apiTest');
        return false;

    }

    /**
     * 获取部门成员
     * 参数	必须	说明
     * access_token	是	调用接口凭证
    department_id	是	获取的部门id
    fetch_child	否	1/0：是否递归获取子部门下面的成员
    status	否	0获取全部成员，1获取已关注成员列表，2获取禁用成员列表，4获取未关注成员列表。status可叠加,未填写则默认为4
     * @param $departmentId
     */
    public function getUserByDepartmentId($departmentId)
    {
        $this->accessToken = $this->getAccessToken();
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/list';
        $params = ['access_token'=>$this->accessToken,'department_id'=>$departmentId,'status'=>0];
        $res = $this->curlApi($url,$params);
        if(isset($res['userlist']))
        {
            if(!empty($res['userlist']))
            {
                $list = $res['userlist'];
                return $list;
            }
        }
        $msg = json_encode($res);
        write_log('微信API错误:'.$msg.'|'.$url,'huanpeng_apiTest');
        return false;
    }

    /**
     * 获取应用列表
     *微信api返回
     *  {"errcode":0,"errmsg":"ok","agentlist":[{"agentid":1000002,"name":"接口报警","square_logo_url":"http://p.qpic.cn/qqmail_pic/4280577195/6d6bf1658ecf4211ac14abdb559290af9f8bf6b9f7a698a1/0"}]}
     * @return bool
     */
    public function getAgentList()
    {
        $this->accessToken = $this->getAccessToken();
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/agent/list';
        $params = ['access_token'=>$this->accessToken];
        $res = $this->curlApi($url,$params);
        if(isset($res['agentlist']))
        {
            if(!empty($res['agentlist']))
            {
                $list = $res['agentlist'];
                return $list;
            }
        }
        $msg = json_encode($res);
        write_log('微信API错误:'.$msg.'|url:'.$url,'param:'.json_encode($params).'huanpeng_apiTest');
        return false;
    }
    /**
     *发送文本消息按部门
    参数	必须	说明
    touser	否	成员ID列表（消息接收者，多个接收者用‘|’分隔，最多支持1000个）。特殊情况：指定为@all，则向关注该企业应用的全部成员发送
    toparty	否	部门ID列表，多个接收者用‘|’分隔，最多支持100个。当touser为@all时忽略本参数
    totag	否	标签ID列表，多个接收者用‘|’分隔，最多支持100个。当touser为@all时忽略本参数
    msgtype	是	消息类型，此时固定为：text （支持消息型应用跟主页型应用）
    agentid	是	企业应用的id，整型。可在应用的设置页面查看
    content	是	消息内容，最长不超过2048个字节，注意：主页型应用推送的文本消息在微信端最多只显示20个字（包含中英文）
    safe	否	表示是否是保密消息，0表示否，1表示是，默认0
     */
    public function sendTextByDepartmentId($content,$departmentIds=5,$userIds ='',$tagIds = '',$agentid = self::API_ALERT_AGNET_ID)
    {
        $this->accessToken = $this->getAccessToken();
        //post 方式
        if(is_array($departmentIds))
        {
            $departmentId = implode('|',$departmentIds);
        }else
        {
            $departmentId = trim($departmentIds);
        }
        if(is_array($userIds))
        {
            $userId = implode('|',$userIds);
        }else
        {
            $userId = trim($userIds);
        }
        if(is_array($tagIds))
        {
            $tagId = implode('|',$tagIds);
        }else
        {
            $tagId = trim($tagIds);
        }
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token='.$this->accessToken;
        //拼装参数
        $params = [
            'touser'=>$userId,//参数可选
            'toparty'=>$departmentId,
            'totag'=>$tagId,    //参数可选
            'msgtype'=>'text',
            'agentid'=>$agentid,
            'text'=>[
                'content'=>$content,
            ],
            ];
        //微信post参数json格式
        $param = json_encode($params);
        $res = $this->curlApi($url,$param,1);

        if($res['errcode'] == 0)
        {
            if(isset($res['invalidparty']))
            {
                if($res['invalidparty']!='')
                {
                    $msg = json_encode($res);
                    write_log('微信API错误（部门id错误）:'.$msg.'|'.$url,'huanpeng_apiTest');
                    return false;
                }
            }
        }else
        {
            $msg = json_encode($res);
            $params = json_encode($params);
            write_log('微信API错误:'.$msg.'|'.$url.'|params:'.$params ,'huanpeng_apiTest');
            return false;
        }
        $msg = json_encode($res);
        write_log('微信警报发送成功:'.$msg.'|'.$url,'huanpeng_apiTest');
        return true;
    }

    /**
     * accessToken 写文件
     */
     public function writeAccessTokenToFile()
     {
         $logName = self::WEIXIN_ACCESS_TOKEN_FILE;
         $logFile = LOG_DIR . $logName . '.log.';
         if (!file_exists($logFile))
         {
             touch($logFile);
             @chmod($logFile, 0777);
             clearstatcache();
         }
         $this->accessToken = $this->_getAccessToken();
         $res['accessToken'] = $this->accessToken ?$this->accessToken:0;
         $res['expire'] = time();
         $content = json_encode($res);
         $status = file_put_contents($logFile, $content);
         if(!$status)
         {
             write_log('error:'.$logFile.'写入出错:|content:'.$content,'huanpeng_apiTest');
         }
         return $this->accessToken;
     }

    /**
     * 读accessToken 文件 只允许胥勇使用
     */
     public function getAccessTokenFromFile()
     {
         $logName = self::WEIXIN_ACCESS_TOKEN_FILE;
         $logFile = LOG_DIR . $logName . '.log.';
         if (file_exists($logFile))
         {
             $content = file_get_contents($logFile);
             $res = json_decode($content,true);
             if($res['accessToken'])
             {
                 $expire = $res['expire'] + self::ACCESS_TOKEN_KEY_EXPIRE;
                 $now = time();
                 if($now < $expire)
                 {
                     return $res['accessToken'];
                 }
             }
         }
         return $this->writeAccessTokenToFile();

     }

    /**
     * 获取单个成员信息
     * @param $userId
     * @return bool
     */
     public function getSingerUserInfo($userId)
     {
         $userId = trim($userId);
         $this->accessToken = $this->getAccessToken();
         $url ='https://qyapi.weixin.qq.com/cgi-bin/user/get';
         $params = ['access_token'=>$this->accessToken,'userid'=>$userId];
         $res = $this->curlApi($url,$params);
         if($res['errcode'] == 0)
         {
             return $res;
         }
         $msg = json_encode($res);
         write_log('微信API错误:'.$msg.'|'.$url,'huanpeng_apiTest');
         return false;
     }
}
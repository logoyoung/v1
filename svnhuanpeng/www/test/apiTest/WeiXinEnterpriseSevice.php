<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/7/27
 * Time: 10:38
 */
namespace test\apiTest;
use system\HttpHelper;
use system\RedisHelper;
/**
 * 微信企业号服务
 * Class WeiXinEnterpriseSevice
 * @package test\apiTest
 */
class WeiXinEnterpriseSevice
{
    private $corpid = 'wx1072dfe6cadb7f76';
    private $corpsecret = 'XZBakxDCHIfa1tNEeqvT6a7XLyV2PgmGcaAc5FShMrE';
    private $accessToken;
    public  $redis;
    const ACCESS_TOKEN_KEY = 'WEIXINENTERPRISE_ACCESS_TOKEN';
    //accessToken过期时间
    const ACCESS_TOKEN_KEY_EXPIRE = 7200;
    public function setCorpid($corpid)
    {
        $this->corpid = $corpid;
        return $this;
    }
    public function setCorpsecret($corpsecret)
    {
        $this->corpsecret = $corpsecret;
        return $this;
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
        $Curl = $this->HttpHelper();
        if($post)
        {
            $Curl->addPost($url,$params,5);
        }else
        {
            $Curl->addGet($url,$params,5);
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
     * @return bool|string
     */
    public function getAccessToken($expire = false)
    {
        if(!$expire)
        {
            $this->accessToken = $this->getRedis()->get(self::ACCESS_TOKEN_KEY);
            if($this->accessToken)
            {
                return $this->accessToken;
            }
        }
        //api获取AccessToken
        $this->accessToken  = $this->_getAccessToken();
            if($this->accessToken  != false)
            {
                $status = $this->getRedis()->set(self::ACCESS_TOKEN_KEY,$this->accessToken);
                if($status)
                {
                    $this->getRedis()->expire(self::ACCESS_TOKEN_KEY,self::ACCESS_TOKEN_KEY_EXPIRE);
                }else
                {
                    $msg = '设置accessToken Redis缓存失败';
                    write_log($msg,'huanpeng_apiTest');
                }
                return $this->accessToken ;
            }
            return false;

    }

    /**
     * 获取部门列表
     * 微信api返回{"errcode": 0,"errmsg": "ok",、"department": [{"id": 1,"name": "欢朋test","parentid": 0,"order": 2147483447},]}
     * @return bool
     */
    public function getDepartmentList()
    {
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
        write_log('微信API错误:'.$msg.'|'.$url,'huanpeng_apiTest');
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
    public function sendTextByDepartmentId($content,$departmentIds=5,$userId ='',$tagId = '',$agentid = 1000002)
    {
        //post 方式
        if(is_array($departmentIds))
        {
            $departmentId = implode('|',$departmentIds);
        }else
        {
            $departmentId = trim($departmentIds);
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
            write_log('微信API错误:'.$msg.'|'.$url.'|params:'.$params,'huanpeng_apiTest');
            return false;
        }
        $msg = json_encode($res);
        write_log('微信警报发送成功:'.$msg.'|'.$url,'huanpeng_apiTest');
        return true;
    }

}
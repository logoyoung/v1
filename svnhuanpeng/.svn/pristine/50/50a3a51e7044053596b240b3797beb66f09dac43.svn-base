<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/7/24
 * Time: 17:03
 */
require __DIR__.'/../../include/init.php';
use system\HttpHelper;
use system\Timer;
use service\common\ApiCommon;
use service\weixin\WeiXinEnterpriseService;

/**
 * 接口测试类
 * Class apiTest
 */

class apiTest
{
    //微信公共号发消息开关 1 为开 0为关
    const SENDMSG_SWITCH = 1;
    //默认接口请求超时时间s
    const TIME_OUT = 5;
    //默认超过此时间记录接口日志
    const API_TIME_OUT = 2;
    //默认请求方式
    const DEFAULT_REQUEST = 'post';
    //仅判断状态默认值  statusOnly = 1
    const DEFAULT_STATUS  = 1;
    //默认callback json返回状态
    const DEFAULT_SUCCESS_STATUS = 1;
    //配置故障级别
    const LEVEL0 = 00;//无故障 仅记录检测时间 检测结果
    const LEVEL1 = 10;//1级故障 接口超时
    const LEVEL2 = 20;//2级故障  接口返回值不匹配
    const LEVEL3 = 30;//3级故障 接口返回状态有问题
    const LEVEL4 = 40;//4级故障 接口httpCode 出错
    public static $errorMsg =[
        self::LEVEL1 =>'错误类型:接口响应超时',
        self::LEVEL2 =>'错误类型:返回值不匹配',
        self::LEVEL3 =>'错误类型:返回状态错误',
        self::LEVEL4 =>'错误类型:httpCode错误',
        ];

    //配置数组
    private $config = [];
    //接口类型
    private $postType = [];
    private $getType = [];
    //信息
    public $msg = [];
    public function HttpHelper()
    {
        return new HttpHelper();
    }
    public function Timer()
    {
        return new Timer();
    }
    public function getWeiXinSevice()
    {
        return new WeiXinEnterpriseService();
    }
    //设置配置
    public function setApiConfig($config)
    {
        $this->config = $config;
        return $this;
    }
    //获取接口配置
    public function getApiConfig()
    {
        //一次获取接口初始配置
        $this->config = $this->getDefaultConfig();
        return $this;
    }
   //请求分类
    public function requestClassify()
    {
        $res = [];
        $config = $this->config;
        //按KEY排序
        foreach ($config as  $value)
        {
            if(!isset($value['request']))
            {
                $value['request'] = 'post';
            }
            $res[$value['request']][]    = $value;
        }
        $this->postType = isset($res['post'])?$res['post']:[];
        $this->getType  = isset($res['get'])?$res['get']:[];
        return true;
    }
    //验证配置参数 如果没有会报错
    public function checkApiConfig()
    {
        foreach ($this->config as $value)
        {
            //验证对应参数不能为空
            $requirRule = ['key'=>'1','apiUrl' =>'1','params'=>'1','request'=>'1'];
            ApiCommon::checkParams($value,$requirRule);
        }
    }
    //并发请求接口POST
    public function httpMultiPost()
    {
        $multiCurl  = $this->HttpHelper();
        foreach ($this->postType as $value)
        {
            if(!isset($value['timeout']))
            {
                $value =['timeout'=> self::TIME_OUT];
            }
            $multiCurl->addPost($value['apiUrl'],$value['params'],$value['timeout']);
        }

        $result       = $multiCurl->getResult();
        foreach ($this->postType as $key=>$value)
        {
            $result[$key]['key'] = $value['key'];
        }

        return $result;
    }
    //并发请求get
    public function httpMultiGet()
    {
        $multiCurl  = $this->HttpHelper();
        foreach ($this->getType as $value)
        {
            if(!isset($value['timeout']))
            {
                $value =['timeout'=> self::TIME_OUT];
            }
            $multiCurl->addGet($value['apiUrl'],$value['params'],$value['timeout']);
        }

        $result       = $multiCurl->getResult();
        foreach ($this->getType as $key=>$value)
        {
            $result[$key]['key'] = $value['key'];
        }
        return $result;
    }
    //检查接口
    public function mainCheckApi()
    {
        $timer = $this->Timer();
        $timer->start();
        $msg = "================================接口检测开始=================================".PHP_EOL;
        echo $msg;
        write_log($msg,"huanpeng_apiTest");
        //接口分类
        $this->requestClassify();
        $postRes = [];
        $getRes = [];
        $alert = '';
        $error = 0;
        $total = 0;
        //合并请求后结果
        if(count($this->postType)> 0 )
        {
            $postRes = $this->httpMultiPost();
        }
        if(count($this->getType) > 0)
        {
            $getRes  = $this->httpMultiGet();
        }
        //给所有post接口增加post字段
        foreach ($postRes as &$temp)
        {
            $temp['request'] = 'post';
        }
        //给所有get接口增加get字段
        foreach ($getRes as &$temp)
        {
            $temp['request'] = 'get';
        }
         $res = array_merge($postRes,$getRes);
        foreach ($res as $value)
        {
            //检查接口状态码
            $AlertLevel = $this->checkHttpCode($value['httpCode']);
           if($AlertLevel == self::LEVEL0)
           {
               //检查返回值（含 状态码 返回值 接口超时）
               $AlertLevel = $this->checkCallBack($value);
               if($AlertLevel != self::LEVEL0)
               {
                   //返回值出现问题
                   $alert = $this->getAlert($value,$AlertLevel);
                    echo $alert;
                   $error++;
               }
           }else
           {
               //状态码出现问题
               $alert =  $this->getAlert($value,$AlertLevel);
               echo $alert;
               $error++;
           }
           $total++;
          // echo $alert;
        }
        $timer->end();
        //总耗时
        $msg =  '==================================== 出错接口数量:'.$error."个================================".PHP_EOL;
        $msg .=  '====================================接口检查执行完毕 总共检查接口数量:'.$total.'个 总共耗时:'.$timer->getTime(3)."================================".PHP_EOL;
        write_log($msg,"huanpeng_apiTest");
        echo $msg;

    }

    /**
     * 获取警报内容
     * @param $value
     * @param int $level
     */
    public function getAlert($value,$level = self::LEVEL1)
    {
        $msg = '';
         switch ($level)
         {
             //0级无警报
             case self::LEVEL0:
                 break;
             case self::LEVEL1:
                 $msg = $this->packMsg($value,$level);
                 $this->writeLog($msg);
                 //echo $msg;
                break;
             case self::LEVEL2:
                 $msg = $this->packMsg($value,$level);
                 $this->writeLog($msg);
                 //echo $msg;
                 //发消息
                 $this->sendMsg($msg,$value);
                 break;
             case self::LEVEL3:
                 $msg = $this->packMsg($value,$level);
                 $this->writeLog($msg);
                 $this->sendMsg($msg,$value);
                 //echo $msg;
                 //发消息
                 break;
             case self::LEVEL4:
                 $msg = $this->packMsg($value,$level);
                 $this->writeLog($msg);
                 $this->sendMsg($msg,$value);
                 //echo $msg;
                 //发消息
                 break;
             default:
                 break;
         }
         return $msg;
    }
    //组装消息
    public function packMsg($value,$level)
    {
        //获取配置
        //获取get方法接口url
        if($value['request'] == 'get')
        {
            $url = parse_url($value['url']);
            $value['url'] = $url['path'];
        }
        $config = $this->config[$value['key']];
        $name = isset($config['name']) ? '接口名:'.$config['name'].'|':'';
        $param = '接口参数:'.json_encode($config['params']).'|';
        $request = '接口请求:'.$config['request'].'|';
        $tips =self::$errorMsg[$level];
        if($value['status'] == 1)
        {
            if(is_array($value['content']))
            {
                $value['content'] = json_encode($value['content']);
            }
            $msg = $name.$request.'接口地址:'.$value['url'].'|'.$tips.'| httpCode:'.$value['httpCode'].'| 返回:'.$value['content'].'|'.$param.' 执行时间:'.$value['run_time']."s";
        }else
        {
            $msg = $name.$request.'接口地址:'.$value['url'].'|'.$tips.'| httpCode:'.$value['httpCode'].'| 错误信息:'.$value['errorMsg'].'| '.$param.'执行时间:'.$value['run_time']."s";
        }
        return $msg;
    }
    /**
     * 记日志z
     * @param $msg
     * @param string $logName
     * @return bool
     */
    public function writeLog($msg,$logName = 'huanpeng_apiTest')
    {
        write_log($msg,$logName);
        return true;

    }
    //微信发消息

    /**
     *         {
    "touser": "UserID1|UserID2|UserID3",
    "toparty": " PartyID1 | PartyID2 ",
    "totag": " TagID1 | TagID2 ",
    "msgtype": "text",
    "agentid": 1,
    "text": {
    "content": "Holiday Request For Pony(http://xxxxx)"
    },
    "safe":0
    }
     */
    public function sendMsg($msg,$value,$msgtype = 'text')
    {
        //echo '微信发送：'.$msg;
        if(self::SENDMSG_SWITCH)
        {
            //$value中有url 可取配置文件 做各种按需调整
            $wxSerivce = $this->getWeiXinSevice();
                //按类型发消息
                switch ($msgtype)
                {
                    case 'text':
                        //按部门 发消息
                        $res = $wxSerivce->sendTextByDepartmentId($msg);
                        return $res;

                        break;
                    default:
                        break;
                }
            echo '糟糕！！微信发送消息失败！！请看日志！！！';
            return false;
        }else
        {
            return true;
        }
    }
    //检查接口状态码
    public function checkHttpCode($httpCode)
    {
        $httpStatusCode = floor($httpCode/100);
        switch ($httpStatusCode)
        {
            case 2:
                return self::LEVEL0;
                break;
            case 3:
                return self::LEVEL4;
                break;
            case 4:
                return self::LEVEL4;
                break;
            case 5:
                return self::LEVEL4;
                break;
            default:
                return self::LEVEL4;
                break;
        }
    }

    /**
     * 检查返回值状态是否正确
     * @param $callback
     * @return bool
     */
    public function checkStatus($callback)
    {
        $res = json_decode($callback);
        if($res['status'] == self::DEFAULT_STATUS )
        {
            return true;
        }
        return false;

    }

    /**
     * 检查返回值是否正确
     * @param $apiUrl @接口地址
     * @param $value @返回
     * @param bool $huangpeng
     * @return bool
     */
    public function checkCallBack($value,$huangpeng = true)
    {
            if($huangpeng)
            {
                $callBack = $value['content'];
                $defaultCallBack = $this->config[$value['key']];
                //是否只是检查状态
                if(isset($defaultCallBack['statusOnly']))
                {
                    if($defaultCallBack['statusOnly'] == self::DEFAULT_STATUS)
                    {
                        if(is_array($callBack))
                        {
                            $res = $callBack;
                        }else
                        {
                            $res = json_decode($callBack,true);
                        }

                        if($res['status'] != self::DEFAULT_SUCCESS_STATUS)
                        {
                            return self::LEVEL2;
                        }
                    }
                }else
                    //匹配返回值
                {
                     //短信余额验证
                     if(isset($defaultCallBack['checkParam']))
                     {
                         //判断参数
                        switch ($defaultCallBack['checkParam'])
                        {
                            case 'balance':
                                $checkParamArr = json_decode($callBack,true);
                                if(isset($checkParamArr['resuData']))
                                {
                                    foreach ($checkParamArr['resuData'] as $key=>$v)
                                    {
                                        if(isset($v['data']))
                                            {
                                                if($v['data'][0]['balance'] < $defaultCallBack[$defaultCallBack['checkParam']] )
                                                {
                                                    return self::LEVEL2;
                                                }
                                            }
                                    }
                                }
                                break;
                            default:
                                if($callBack != trim($defaultCallBack['callback']))
                                {
                                    return self::LEVEL3;
                                }
                                break;
                        }
                     }
                }
                //检查是否超时
                if(isset($defaultCallBack['runTime']))
                {
                    if($value['run_time'] > $defaultCallBack['runTime'])
                    {
                        return self::LEVEL1;
                    }
                }else
                {
                    if($value['run_time'] > self::API_TIME_OUT)
                    {
                        return self::LEVEL1;
                    }
                }
            }
          //此处可以处理其他非欢朋接口返回值情况
            return self::LEVEL0;
    }

    /**
     * 一次获取默认的配置按接口地址
     */
   public function getDefaultConfig()
   {
       $res = [];
       //按KEY排序
       foreach ($this->config as  $value)
       {
           $res[$value['key']] = $value;
       }
       return $res;
   }
    //执行
    public function action()
    {
        //验证配置参数
        $this->checkApiConfig();
        //重新整理配置参数
        $this->getApiConfig();
        //验证接口
        $this->mainCheckApi();

    }
}
include('config/config.php');
$apiTest = new apiTest();
$apiTest->setApiConfig($api_config)->action();

<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/7/25
 * Time: 16:10
 */
//key 接口序号 不能重复 name=>接口名，request=> get\post , apiUrl=>接口地址,params =>[参数=>值]，timeOut=>'curl超时时间 不设置默认5s'，
//callback=>'返回json值',statusOnly=>'是否仅仅检查返回status',runTime=>'接口允许响应时间 超过则记日志 不设置默认大于2s记日志'
//新加匹配参数checkParam 自定义匹配值 默认匹配全值checkParam = 1
$api_config = [
   ['key'=>1,'name'=>'支付宝下单接口','request'=>'post','apiUrl'=>'http://www.huanpeng.com/api/alipay/unifiedorder.php', 'params'=>['uid' => 1860,'encpass' => '9db06bcff9248837f86d1a6bcf41c9e7','quantity'=>'100','productID'=>5,'channel'=>'alipay','client'=>'android','refUrl'=>'','promotionID'=>'1'], 'timeout'=>'5','callback'=>'1','statusOnly'=>1],

    ['key'=>2,'name'=>'微信下单接口','request'=>'post','apiUrl'=>'http://www.huanpeng.com/api/alipay/unifiedorder.php', 'params'=>['uid' => 1860,'encpass' => '9db06bcff9248837f86d1a6bcf41c9e7','quantity'=>'100','productID'=>5,'channel'=>'alipay','client'=>'android','refUrl'=>'','promotionID'=>'1'], 'timeout'=>'5','callback'=>'1','statusOnly'=>1],
//监控balance字段 小于3000 报警
    ['key'=>3,'name'=>'短信监控','request'=>'get','apiUrl'=>'http://liveuser/api/getBalanceInfo.php', 'params'=>['appid'=>102], 'timeout'=>'5','callback'=>'1','checkParam'=>'balance','balance'=>'3000'],
];
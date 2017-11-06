<?php
require __DIR__.'/../../include/init.php';
use system\HttpHelper;
use system\Timer;

/**
 *   日志
 *
 */
class test {

    /**
     *  post 单个请求 (get用法相同)
     * @return [type] [description]
     */
    public function httpPost() {
        $url    = 'dev.huanpeng.com/api/video/getVideoList.php';
        $param  = ['luid' => 1870,'size' => 8];
        $curl   = new HttpHelper();

        //开起调式模式 (不管成功与否都会记志日志) dev pro环境默认开启的
        $curl->setDebug(true);

        //自定义日志名（注意成功失败都会记录到这个文件里,
        // 默认日志
        // /data/logs/http_access.log.20170531
        // /data/logs/http_error.log.20170531
        //$curl->setLogName('http_order');

        //载入post请求相关参数  （具体参数可见addPost方法）
        //$url 请求api
        //$param 请求参数
        //5 超时时间
        //默认自动解析响应数据的json ,xml
        $curl->addPost($url,$param,5);

        //http get
        //$curl->addGet($url,$param,5);
        //发起请求并获取响应结果集
        $result = $curl->getResult();

        //注意单个请求响应数据在结果集的第0位数组，（不管成功与失败都会有结果集）
        $result = $result[0];
        $curl   = null;
        //status=1 http 请求成功, status=0 http 请求失败
        if($result['status']) {
            //接口响应数据在content里 $result['run_time']
            $data = $result['content'];
            print_r($data);
        } else {
          //http 失败 会返回相对应的错误码跟错误消息开发者可以合理处理
            /* $result =  (
                [status] => 0
                [httpCode] => 0
                [errorCode] => 6
                [errorMsg] => Couldn't resolve host name: Couldn't resolve host 'dev.huanpeng1.com'
                [url] => dev.huanpeng1.com/api/video/getVideoList.php 接口地址
                [run_time] => 3.2198  //请求耗时
            )*/
        }
    }

    /**
     *  批量 post 请求 (get用法相同)
     * @return [type] [description]
     */
    public function httpMultiPost() {

        $urls = [
            0 => ['url' => 'dev.huanpeng.com/api/video/getVideoList.php', 'param' => ['luid' => 1870,'size' => 8], 'timeout' => 4],
            1 => ['url' => 'dev.huanpeng.com/api/room/LiveRoomRanking.php', 'param' => ['luid' => 1870,'timeType' => 3], 'timeout' => 4],
            2 => ['url' => 'dev.huanpeng.com/api/room/getSocketServer.php','param' => ['luid' => 1870], 'timeout' => 3],
        ];
        //保证请求顺序
        ksort($urls);
        $timer = new Timer;
        $timer->start();
        $multiCurl  = new HttpHelper();

        foreach ($urls as $l) {
            $multiCurl->addPost($l['url'],$l['param'],$l['timeout']);
        }

        //批量发起请求并获取响应结果集 （其实跟单个请求返一样，只是单个请求key 0 就是它的结果集，而并发每个结果集对应的它的发起顺序）
        //结果集 严格按照发起请求的先后顺序，所以请求的key 就是结查集的key

        $result       = $multiCurl->getResult();
        $multiCurl    = null;

        $timer->end();
        //总耗时
        echo 'total_run_time:'.$timer->getTime(3);
        echo "\n";

        $videoList    = $result[0];
        $liveRank     = $result[1];
        $socketServer = $result[2];
        print_r($videoList);
        print_r($liveRank);
        print_r($socketServer);
    }

}

$t = new test();
//$t->httpPost();
$t->httpMultiPost();
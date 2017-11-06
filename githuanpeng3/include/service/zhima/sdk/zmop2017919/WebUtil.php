<?php
namespace service\zhima\sdk\zmop2017919;
/**
 * Created by PhpStorm.
 * User: dengpeng.zdp
 * Date: 2015/9/28
 * Time: 19:25
 */

class WebUtil{

    /**
     * 将传入的参数组织成key1=value1&key2=value2形式的字符串
     * @param $params
     * @return string
     */
    public static function buildQueryWithoutEncode($params) {
       return WebUtil::buildQuery($params, false);
    }

    public static function buildQueryWithEncode($params){
        return WebUtil::buildQuery($params, true);
    }

    public static function buildQuery($params, $needEncode){
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === WebUtil::checkEmpty($v)) {
                if($needEncode){
                    $v = urlencode($v);
                }

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     *  校验$value是否非空
     *  if not set ,return true;
     *  if is null , return true;
     * @param $value
     * @return bool
     */
    public static function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }

    /**
     * 向服务器发起post请求
     * @param $url 服务器地址
     * @param null $postFields 请求参数
     * @param $charset 字符集
     * @return mixed 服务器返回的值
     * @throws Exception post异常
     */
    public static function curl($url, $postFields = null, $timeout = 7, $connectionout = 7) {

        $httpHelper = new \system\HttpHelper();
        $httpHelper->setDebug(true);
        $httpHelper->setLogName('zhima_cert_access');
        $httpHelper->addPost($url,$postFields,$timeout,$connectionout);
        $response   = $httpHelper->getResult();
        $response   = $response[0];
        if(!$response['status'] || $response['httpCode'] != 200)
        {
            throw new Exception($response['errorMsg'], $response['httpCode']);
        }

        return $response['content'];
    }

	/**
     * post文件上传路径处理
     * php5.5+使用CURLFile，否则拼@
     *
     * @param $filePath 上传文件本地路径
     * @return mixed
     */
    public static function getFilePath($filePath) {
        if (class_exists ( 'CURLFile' )) {
            return new CURLFile ( $filePath );
        } else {
            return "@" . $filePath;
        }
    }
}
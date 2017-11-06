<?php

namespace  Wcs\PersistentFops;
use Wcs;
use Wcs\Config;

class Fops {

    /**
     * @var $bucket
     * 空间名
     */
    public $bucket;
    public $auth;
    

    public function __construct($auth, $bucket)
    {
        $this->bucket = $bucket;
        $this->auth = $auth;
    }

    public function _genernate_header($url, $body=null)
    {
       $token = $this->auth->get_token($url, $body);
       $headers = array("Authorization:$token");
       return $headers;
    }
    /**
     * 持久化操作函数
     *
     * @return mixed
     */
    public function exec($fops, $key, $notifyURL=null, $force=0, $separate=0) {
        $url = Config::WCS_MGR_URL . '/fops';
        $encodebucket = \Wcs\url_safe_base64_encode(($this->bucket));
        $body = 'bucket='.$encodebucket;
        //
        $body .= '&key='.$key;
        $body .= '&fops=' .\Wcs\url_safe_base64_encode($fops);
        if(!empty($notifyURL)) {
            $body .= '&notifyURL=' .\Wcs\url_safe_base64_encode($notifyURL);
        }
        $body .= '&force=' . $force;
        $body .= '&separate=' . $separate;
        //print_r($body."\n");
        $headers = $this->_genernate_header($url, $body);

        $resp = $this->_post($url, $headers, $body);
        return $resp;

    }

    /**
     * @param $persistentId
     * @return mixed
     */
    public static function status($persistentId) {
        $url = Config::WCS_MGR_URL . '/status/get/prefop?persistentId=' . $persistentId;
        $resp = \Wcs\http_get($url, null);

        return $resp->respBody;
    }


    /**
     * @param $url
     * @param $token
     * @param $content
     * @return mixed
     */

    public function _post($url, $headers, $content) {
        $resp = \Wcs\http_post($url, $headers, $content);
        return $resp->respBody;
    }


}

<?php


namespace Wcs\Upload;

use Wcs;
use Wcs\Http\PutPolicy;
use Wcs\Config;

$UPLOAD = '';

class Uploader
{

    private $userParam;
    private $userVars;
    private $mimeType;


    function __construct($token, $userParam = null, $userVars = null, $mimeType = null)
    {
        $this->token = $token;
        $this->userParam = $userParam;
        $this->userVars = $userVars;
        $this->mimeType = $mimeType;
    }



    /**
     * 普通上传
     * @param $bucketName
     * @param $fileName
     * @param $localFile
     * @param $returnBody
     * @return string
     */
    function upload_return($localFile) {
        global $UPLOAD;
        $UPLOAD = basename($localFile);
        $resp = $this->_upload($localFile);

        return $this->build_result($resp);

    }

    function _upload($localFile) {

        if(!file_exists($localFile)) {
            die("ERROR: {$localFile}文件不存在！");
        }
        $url = Config::WCS_PUT_URL . '/file/upload';

        $token = $this->token;

        $mimeType = null;
        $fields = array(
            'token' => $token,
            'file' => $this->create_file($localFile, $this->mimeType),
            //'key' => $fileName
        );

        //自定义可选参数
        if($this->userParam !== null) {
            $fields['userParam'] = $this->userParam;
        }
        if($this->userVars !== null) {
            $fields['userVars'] = $this->userVars;
        }
        if($this->mimeType !== null) {
            $fields['mimeType'] = $this->mimeType;
        }

        $opt = array(
            CURLOPT_PROGRESSFUNCTION => array( 'Wcs\Upload\Uploader', 'upload_progress'),
            CURLOPT_NOPROGRESS => false
        );

        $resp = \Wcs\http_post($url, null, $fields, $opt);

        return $resp;
    }


    private function create_file($filename, $mime)
    {
        // PHP 5.5 introduced a CurlFile object that deprecates the old @filename syntax
        // See: https://wiki.php.net/rfc/curl-file-upload
        if (function_exists('curl_file_create')) {
            return curl_file_create($filename, $mime);
        }

        // Use the old style if using an older version of PHP
        $value = "@{$filename}";
        if (!empty($mime)) {
            $value .= ';type=' . $mime;
        }

        return $value;
    }

    private function build_result($resp) {
        if ($resp->code == 28) {
            $ret = Array(
                'code' => 28,
                'message' => '请求超时！'
            );
            return json_encode($ret, JSON_UNESCAPED_UNICODE);
        } else {
            return $resp->respBody;
        }
    }

    function upload_progress($resource, $download_size = 0, $downloaded = 0, $upload_size = 0, $uploaded = 0) {

        /**
         * $resource parameter was added in version 5.5.0 breaking backwards compatibility;
         * if we are using PHP version lower than 5.5.0, we need to shift the arguments
         * @see http://php.net/manual/en/function.curl-setopt.php#refsect1-function.curl-setopt-changelog
         */
        if (version_compare(PHP_VERSION, '5.5.0') < 0) {
            $uploaded = $upload_size;
            $upload_size = $downloaded;
            $downloaded = $download_size;
            $download_size = $resource;
        }

        global $UPLOAD;

        $progress = 0;
        if($upload_size > 0 ) {
            $progress = floor($uploaded / $upload_size * 100);
        }

        $content = json_encode(array('progress' => $progress));
        $path = "";
        $destination =  $path . "." . $UPLOAD . ".prs";
        $file = fopen($destination, "w");
        fwrite($file, $content, JSON_UNESCAPED_UNICODE);
        //显示当前上传进度
        //print_r("progress: ". $progress ."%\n");
        fclose($file);

    }
}

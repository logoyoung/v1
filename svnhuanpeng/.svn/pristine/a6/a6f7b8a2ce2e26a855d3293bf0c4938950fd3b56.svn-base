<?php
namespace system;
use system\Timer;

/**
 * example
 *   in /usr/local/huanpeng/test/xuyong/curl.php
 */
class HttpHelper
{

    const HTTP_TIMEOUT            = 20;
    const HTTP_CONNECTION_TIMEOUT = 20;
    const HTTP_OK    = 1;
    const HTTP_ERROR = 0;
    private $multiCurl;
    private $result;
    private $timer;
    private $debug;
    private $logName;
    private $getHeader;
    private $warningTime  = 5;
    private $requestParam = [];

    /**
     *  get
     * @param string $url
     * @param array  $param
     * @param int   $timeout         [description]
     * @param init $connectTimeout [description]
     */
    public function addGet($url, $param = [], $timeout = self::HTTP_TIMEOUT, $connectTimeout = self::HTTP_CONNECTION_TIMEOUT)
    {

        $multiCurl = $this->getMultiCurl();
        $curl      = $multiCurl->addGet($url,$param);
        $this->requestParam[] = $param;
        $this->init($curl,$timeout, $connectTimeout);
        return $curl;
    }

    /**
     *  post
     * @param string $url            [description]
     * @param array | xml $param          [description]
     * @param int $timeout        [description]
     * @param int $connectTimeout [description]
     */
    public function addPost($url, $param = [], $timeout = self::HTTP_TIMEOUT, $connectTimeout = self::HTTP_CONNECTION_TIMEOUT)
    {
        $multiCurl = $this->getMultiCurl();
        $curl      = $multiCurl->addPost($url,$param);
        $this->requestParam[] = $param;
        $this->init($curl,$timeout, $connectTimeout);
        return $curl;
    }

    /**
     * download
     * @param [type] $url            [description]
     * @param [type] $filename       [description]
     * @param [type] $timeout        [description]
     * @param [type] $connectTimeout [description]
     */
    public function addDownload($url,$filename, $timeout = self::HTTP_TIMEOUT, $connectTimeout = self::HTTP_CONNECTION_TIMEOUT)
    {
        $multiCurl = $this->getMultiCurl();
        $curl      = $multiCurl->addDownload($url,$filename);
        $this->requestParam[] = $filename;
        $this->init($curl,$timeout, $connectTimeout);
        return $curl;
    }

    /**
     * head
     * @param [type] $url            [description]
     * @param array  $param          [description]
     * @param [type] $timeout        [description]
     * @param [type] $connectTimeout [description]
     */
    public function addHead($url, $param = [], $timeout = self::HTTP_TIMEOUT, $connectTimeout = self::HTTP_CONNECTION_TIMEOUT)
    {
        $multiCurl = $this->getMultiCurl();
        $curl      = $multiCurl->addHead($url,$param);
        $this->requestParam[] = $param;
        $this->init($curl,$timeout, $connectTimeout);
        return $curl;
    }

    /**
     * put
     * @param [type] $url            [description]
     * @param array  $param          [description]
     * @param [type] $timeout        [description]
     * @param [type] $connectTimeout [description]
     */
    public function addPut($url, $param = [], $timeout = self::HTTP_TIMEOUT, $connectTimeout = self::HTTP_CONNECTION_TIMEOUT)
    {
        $multiCurl = $this->getMultiCurl();
        $curl      = $multiCurl->addPut($url,$param);
        $this->requestParam[] = $param;
        $this->init($curl,$timeout, $connectTimeout);
        return $curl;
    }

    /**
     * delete
     * @param [type] $url            [description]
     * @param array  $queryParam     [description]
     * @param array  $data           [description]
     * @param [type] $timeout        [description]
     * @param [type] $connectTimeout [description]
     */
    public function addDelete($url, $queryParam = [], $data = [], $timeout = self::HTTP_TIMEOUT, $connectTimeout = self::HTTP_CONNECTION_TIMEOUT)
    {
        $multiCurl = $this->getMultiCurl();
        $curl      = $multiCurl->addDelete($url, $queryParam, $data);
        $this->init($curl,$timeout, $connectTimeout);
        return $curl;
    }

    /**
     * options
     * @param [type] $url            [description]
     * @param array  $param          [description]
     * @param [type] $timeout        [description]
     * @param [type] $connectTimeout [description]
     */
    public function addOptions($url, $param = [], $timeout = self::HTTP_TIMEOUT, $connectTimeout = self::HTTP_CONNECTION_TIMEOUT)
    {
        $multiCurl = $this->getMultiCurl();
        $curl      = $multiCurl->addOptions($url,$param);
        $this->init($curl,$timeout, $connectTimeout);
        return $curl;
    }

    public function addPatch($url, $param = [], $timeout = self::HTTP_TIMEOUT, $connectTimeout = self::HTTP_CONNECTION_TIMEOUT)
    {
        $multiCurl = $this->getMultiCurl();
        $curl      = $multiCurl->addPatch($url,$param);
        $this->requestParam[] = $param;
        $this->init($curl,$timeout, $connectTimeout);
        return $curl;
    }

    public function addSearch($url, $param = [], $timeout = self::HTTP_TIMEOUT, $connectTimeout = self::HTTP_CONNECTION_TIMEOUT)
    {
        $multiCurl = $this->getMultiCurl();
        $curl      = $multiCurl->addSearch($url,$param);
        $this->requestParam[] = $param;
        $this->init($curl,$timeout, $connectTimeout);
        return $curl;
    }

    public function getResult()
    {
        $this->getMultiCurl()->start();
        $result = $this->result;
        $this->close();
        ksort($result);
        return $result;
    }

    private function init($curl, $timeout, $connectTimeout)
    {
        $timer          = new Timer();
        $timer->start();
        $this->timer[]  = $timer;
        $timeout        = (int) $timeout;
        $connectTimeout = (int) $connectTimeout;
        $curl->setTimeout($timeout <= 0 ? self::HTTP_TIMEOUT : $timeout);
        $curl->setConnectTimeout($connectTimeout <= 0 ? self::HTTP_CONNECTION_TIMEOUT : $connectTimeout);
        //parse json
        $curl->setDefaultJsonDecoder(true);
        //parse xml
        $curl->setDefaultXmlDecoder();
        return $curl;
    }

    /**
     *
     * @param boolean $getHeader [description]
     */
    public function setGetHeader( $getHeader = true)
    {
        $this->getHeader = $getHeader;
        return $this;
    }

    /**
     * [setDebug description]
     * @param [type] $debug [description]
     */
    public function setDebug($debug)
    {
        $this->debug = (bool) $debug;
        return $this;
    }

    /**
     * [setLogName description]
     * @param [type] $name [description]
     */
    public function setLogName($name)
    {
        $this->logName = trim($name);
        return $this;
    }

    /**
     * [setWainingTime description]
     * @param [type] $t [description]
     */
    public function setWainingTime($t)
    {
        $t = (int) $t;
        if($t > 0){
            $this->warningTime = $t;
        }
        return $this;
    }

    public function close()
    {
        $this->multiCurl = false;
        $this->getHeader = false;
        $this->result    = null;
        $this->logName   = null;
        $this->timer     = null;
        $this->debug     = false;
        $this->requestParam = null;
    }


    private function getMultiCurl()
    {
        if($this->multiCurl) {
            return $this->multiCurl;
        }
        $this->result    = [];
        $this->multiCurl = new MultiCurl();

        if(($GLOBALS['env'] == 'DEV' || $GLOBALS['env'] == 'PRE') && $this->debug == false) {
            $this->debug = true;
        }

        $this->multiCurl->success(function ($instance) {
            $data             = [];
            $data['status']   = self::HTTP_OK;
            $data['httpCode'] = $instance->httpStatusCode;
            $data['content']  = $instance->response;
            if($this->getHeader)
            {
                $data['header'] = $instance->responseHeaders;
            }
            $data['url']      = $instance->url;
            $data['run_time'] = $this->getRunTime($instance);
            $this->result[$instance->id] = $data;
            $c   = !is_string($data['content']) ? json_encode($data['content']) : $data['content'];
            $p   = $this->getRequestParam($instance);
            $msg = "httpCode:{$data['httpCode']};content:{$c};param:{$p};run_time:{$data['run_time']};url:{$data['url']}";
            $p    = null;
            $c    = null;
            if($this->debug) {
                write_log("info|{$msg}",$this->logName?:'http_access');
            }

            if($data['run_time'] >= $this->warningTime) {
                write_log("warning|{$msg}",$this->logName?:'http_access');
            }
            $msg  = null;
            $data = null;
        });

        $this->multiCurl->error(function ($instance) {
            $data              = [];
            $data['status']    = self::HTTP_ERROR;
            $data['httpCode']  = $instance->httpStatusCode;
            $data['errorCode'] = $instance->errorCode;
            $data['errorMsg']  = $instance->errorMessage;
            if($this->getHeader)
            {
                $data['header'] = $instance->responseHeaders;
            }
            $data['url']       = $instance->url;
            $data['run_time']  = $this->getRunTime($instance);
            $this->result[$instance->id] = $data;
            $p   = $this->getRequestParam($instance);
            $msg = "error|httpCode:{$data['httpCode']};error_code:{$data['errorCode']};error_msg:{$data['errorMsg']};param:{$p};run_time:{$data['run_time']};url:{$data['url']}";
            $p    = null;
            $data = null;
            write_log($msg,$this->logName?:'http_error');
            $msg = null;
        });

        $this->multiCurl->complete(function ($instance) {
             $instance = null;
        });
        return $this->multiCurl;
    }

    private function getRunTime($instance)
    {

        if(!isset($this->timer[$instance->id])) {
            return 0;
        }

        $timer = $this->timer[$instance->id];
        $timer->end();
        $run_time = $timer->getTime(3);
        $timer = null;
        unset($this->timer[$instance->id]);
        return $run_time;
    }

    private function getRequestParam($instance)
    {
        if(!isset($this->requestParam[$instance->id])) {
            return '';
        }
        $param = $this->requestParam[$instance->id];
        $param = !is_string($param) ? json_encode($param) : $param;
        unset($this->requestParam[$instance->id]);
        return $param;
    }

     /**
     * Destruct
     *
     * @access public
     */
    public function __destruct()
    {
        $this->close();
    }

}

class Curl
{
    const VERSION = '1.0.0';
    const DEFAULT_TIMEOUT = 30;

    public $curl;
    public $id = null;

    public $error     = false;
    public $errorCode = 0;
    public $errorMessage = null;

    public $curlError = false;
    public $curlErrorCode = 0;
    public $curlErrorMessage = null;

    public $httpError = false;
    public $httpStatusCode = 0;
    public $httpErrorMessage = null;

    public $baseUrl = null;
    public $url     = null;
    public $requestHeaders  = null;
    public $responseHeaders = null;
    public $rawResponseHeaders = '';
    public $responseCookies = [];
    public $response    = null;
    public $rawResponse = null;

    public $beforeSendFunction = null;
    public $downloadCompleteFunction = null;
    public $successFunction  = null;
    public $errorFunction    = null;
    public $completeFunction = null;
    public $fileHandle       = null;

    private $cookies = [];
    private $headers = [];
    private $options = [];

    private $jsonDecoder = 'Decoder::decodeJson';
    private $jsonDecoderArgs = [];
    private $jsonPattern = '/^(?:application|text)\/(?:[a-z]+(?:[\.-][0-9a-z]+){0,}[\+\.]|x-)?json(?:-[a-z]+)?/i';
    private $xmlDecoder  = 'Decoder::decodeXml';
    private $xmlPattern  = '~^(?:text/|application/(?:atom\+|rss\+)?)xml~i';
    private $defaultDecoder = null;

    public static $RFC2616 = [
        '!', '#', '$', '%', '&', "'", '*', '+', '-', '.', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B',
        'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q',
        'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '|', '~',
    ];
    public static $RFC6265 = [
        // RFC6265: "US-ASCII characters excluding CTLs, whitespace DQUOTE, comma, semicolon, and backslash".
        // %x21
        '!',
        // %x23-2B
        '#', '$', '%', '&', "'", '(', ')', '*', '+',
        // %x2D-3A
        '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':',
        // %x3C-5B
        '<', '=', '>', '?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q',
        'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[',
        // %x5D-7E
        ']', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~',
    ];

    private static $deferredProperties = [
        'effectiveUrl',
        'rfc2616',
        'rfc6265',
        'totalTime',
    ];

    /**
     * Construct
     *
     * @access public
     * @param  $base_url
     * @throws \ErrorException
     */
    public function __construct($base_url = null)
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }

        $this->curl = curl_init();
        $this->id = uniqid('', true);
        $this->setDefaultUserAgent();
        $this->setDefaultTimeout();
        $this->setOpt(CURLINFO_HEADER_OUT, true);

        $header_callback_data = new \stdClass();
        $header_callback_data->rawResponseHeaders = '';
        $header_callback_data->responseCookies    = [];
        $this->headerCallbackData = $header_callback_data;
        $this->setOpt(CURLOPT_HEADERFUNCTION, $this->createHeaderCallback($header_callback_data));

        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->headers = new CaseInsensitiveArray();
        $this->setUrl($base_url);
    }

    /**
     * Before Send
     *
     * @access public
     * @param  $callback
     */
    public function beforeSend($callback)
    {
        $this->beforeSendFunction = $callback;
    }

    /**
     * Build Post Data
     *
     * @access public
     * @param  $data
     *
     * @return array|string
     */
    public function buildPostData($data)
    {
        $binary_data = false;
        if (is_array($data)) {
            if (isset($this->headers['Content-Type']) &&
                preg_match($this->jsonPattern, $this->headers['Content-Type'])) {
                $json_str = json_encode($data);

                if (!($json_str === false)) {
                    $data = $json_str;
                }
            } else {

                if (ArrayUtil::is_array_multidim($data)) {
                    $data = ArrayUtil::array_flatten_multidim($data);
                }

                foreach ($data as $key => $value) {
                    if (is_string($value) && strpos($value, '@') === 0 && is_file(substr($value, 1))) {
                        $binary_data = true;
                        if (class_exists('CURLFile')) {
                            $data[$key] = new \CURLFile(substr($value, 1));
                        }
                    } elseif ($value instanceof \CURLFile) {
                        $binary_data = true;
                    }
                }
            }
        }

        if (!$binary_data && (is_array($data) || is_object($data))) {
            $data = http_build_query($data, '', '&');
        }

        return $data;
    }

    /**
     * Call
     *
     * @access public
     */
    public function call()
    {
        $args = func_get_args();
        $function = array_shift($args);
        if (is_callable($function)) {
            array_unshift($args, $this);
            call_user_func_array($function, $args);
        }
    }

    /**
     * Close
     *
     * @access public
     */
    public function close()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
        $this->options     = null;
        $this->jsonDecoder = null;
        $this->jsonDecoderArgs = null;
        $this->xmlDecoder      = null;
        $this->defaultDecoder  = null;
    }

    /**
     * Complete
     *
     * @access public
     * @param  $callback
     */
    public function complete($callback)
    {
        $this->completeFunction = $callback;
    }

    /**
     * Progress
     *
     * @access public
     * @param  $callback
     */
    public function progress($callback)
    {
        $this->setOpt(CURLOPT_PROGRESSFUNCTION, $callback);
        $this->setOpt(CURLOPT_NOPROGRESS, false);
    }

    /**
     * Delete
     *
     * @access public
     * @param  $url
     * @param  $query_parameters
     * @param  $data
     *
     * @return string
     */
    public function delete($url, $query_parameters = [], $data = [])
    {
        if (is_array($url)) {
            $data = $query_parameters;
            $query_parameters = $url;
            $url = $this->baseUrl;
        }

        $this->setUrl($url, $query_parameters);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        return $this->exec();
    }

    /**
     * Download Complete
     *
     * @access private
     * @param  $fh
     */
    private function downloadComplete($fh)
    {
        if (!$this->error && $this->downloadCompleteFunction) {
            rewind($fh);
            $this->call($this->downloadCompleteFunction, $fh);
            $this->downloadCompleteFunction = null;
        }

        if (is_resource($fh)) {
            fclose($fh);
        }

        if (!defined('STDOUT')) {
            define('STDOUT', fopen('php://stdout', 'w'));
        }

        $this->setOpt(CURLOPT_FILE, STDOUT);
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Download
     *
     * @access public
     * @param  $url
     * @param  $mixed_filename
     *
     * @return boolean
     */
    public function download($url, $mixed_filename)
    {
        if (is_callable($mixed_filename)) {
            $this->downloadCompleteFunction = $mixed_filename;
            $this->fileHandle = tmpfile();
        } else {
            $filename = $mixed_filename;

            $download_filename = $filename . '.pccdownload';

            $mode = 'wb';
            if (file_exists($download_filename) && $filesize = filesize($download_filename)) {
                $mode = 'ab';
                $first_byte_position = $filesize;
                $range = $first_byte_position . '-';
                $this->setOpt(CURLOPT_RANGE, $range);
            }
            $this->fileHandle = fopen($download_filename, $mode);

            $this->downloadCompleteFunction = function ($fh) use ($download_filename, $filename) {
                rename($download_filename, $filename);
            };
        }

        $this->setOpt(CURLOPT_FILE, $this->fileHandle);
        $this->get($url);

        return ! $this->error;
    }

    /**
     * Error
     *
     * @access public
     * @param  $callback
     */
    public function error($callback)
    {
        $this->errorFunction = $callback;
    }

    /**
     * Exec
     *
     * @access public
     * @param  $ch
     *
     * @return mixed
     */
    public function exec($ch = null)
    {
        if ($ch === null) {
            $this->responseCookies = [];
            $this->call($this->beforeSendFunction);
            $this->rawResponse   = curl_exec($this->curl);
            $this->curlErrorCode = curl_errno($this->curl);
            $this->curlErrorMessage = curl_error($this->curl);
        } else {
            $this->rawResponse = curl_multi_getcontent($ch);
            $this->curlErrorMessage = curl_error($ch);
        }
        $this->curlError = !($this->curlErrorCode === 0);

        $this->rawResponseHeaders = $this->headerCallbackData->rawResponseHeaders;
        $this->responseCookies    = $this->headerCallbackData->responseCookies;
        $this->headerCallbackData->rawResponseHeaders = null;
        $this->headerCallbackData->responseCookies = null;

        if ($this->curlError && function_exists('curl_strerror')) {
            $this->curlErrorMessage =
                curl_strerror($this->curlErrorCode) . (
                    empty($this->curlErrorMessage) ? '' : ': ' . $this->curlErrorMessage
                );
        }

        $this->httpStatusCode = $this->getInfo(CURLINFO_HTTP_CODE);
        $this->httpError = in_array(floor($this->httpStatusCode / 100), [4, 5]);
        $this->error     = $this->curlError || $this->httpError;
        $this->errorCode = $this->error ? ($this->curlError ? $this->curlErrorCode : $this->httpStatusCode) : 0;

        if ($this->getOpt(CURLINFO_HEADER_OUT) === true) {
            $this->requestHeaders = $this->parseRequestHeaders($this->getInfo(CURLINFO_HEADER_OUT));
        }
        $this->responseHeaders = $this->parseResponseHeaders($this->rawResponseHeaders);
        $this->response        = $this->parseResponse($this->responseHeaders, $this->rawResponse);

        $this->httpErrorMessage = '';
        if ($this->error) {
            if (isset($this->responseHeaders['Status-Line'])) {
                $this->httpErrorMessage = $this->responseHeaders['Status-Line'];
            }
        }
        $this->errorMessage = $this->curlError ? $this->curlErrorMessage : $this->httpErrorMessage;

        if ($this->error) {
            $this->call($this->errorFunction);
        } else {
            $this->call($this->successFunction);
        }

        $this->call($this->completeFunction);

        if (!($this->fileHandle === null)) {
            $this->downloadComplete($this->fileHandle);
        }

        return $this->response;
    }

    /**
     * Get
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return mixed Returns the value provided by exec.
     */
    public function get($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setUrl($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $this->setOpt(CURLOPT_HTTPGET, true);
        return $this->exec();
    }

    /**
     * Get Info
     *
     * @access public
     * @param  $opt
     *
     * @return mixed
     */
    public function getInfo($opt = null)
    {
        $args = [];
        $args[] = $this->curl;

        if (func_num_args()) {
            $args[] = $opt;
        }

        return call_user_func_array('curl_getinfo', $args);
    }

    /**
     * Get Opt
     *
     * @access public
     * @param  $option
     *
     * @return mixed
     */
    public function getOpt($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }

    /**
     * Head
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function head($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setUrl($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $this->setOpt(CURLOPT_NOBODY, true);
        return $this->exec();
    }

    /**
     * Create Header Callback
     *
     * @access private
     * @param  $header_callback_data
     *
     * @return callable
     */
    private function createHeaderCallback($header_callback_data)
    {
        return function ($ch, $header) use ($header_callback_data) {
            if (preg_match('/^Set-Cookie:\s*([^=]+)=([^;]+)/mi', $header, $cookie) === 1) {
                $header_callback_data->responseCookies[$cookie[1]] = trim($cookie[2], " \n\r\t\0\x0B");
            }
            $header_callback_data->rawResponseHeaders .= $header;
            return strlen($header);
        };
    }

    /**
     * Options
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function options($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setUrl($url, $data);
        $this->removeHeader('Content-Length');
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        return $this->exec();
    }

    /**
     * Patch
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function patch($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }

        if (is_array($data) && empty($data)) {
            $this->removeHeader('Content-Length');
        }

        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        return $this->exec();
    }

    /**
     * Post
     *
     * @access public
     * @param  $url
     * @param  $data
     * @param  $follow_303_with_post
     * @return string
     *
     * [1] https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3.2
     * [2] https://github.com/php/php-src/pull/531
     * [3] http://php.net/ChangeLog-5.php#5.5.11
     */
    public function post($url, $data = [], $follow_303_with_post = false)
    {
        if (is_array($url)) {
            $follow_303_with_post = (bool)$data;
            $data = $url;
            $url = $this->baseUrl;
        }

        $this->setUrl($url);

        if ($follow_303_with_post) {
            $this->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        } else {
            if (isset($this->options[CURLOPT_CUSTOMREQUEST])) {
                if ((version_compare(PHP_VERSION, '5.5.11') < 0) || defined('HHVM_VERSION')) {
                    trigger_error(
                        'Due to technical limitations of PHP <= 5.5.11 and HHVM, it is not possible to '
                        . 'Curl object or upgrade your PHP engine.',
                        E_USER_ERROR
                    );
                } else {
                    $this->setOpt(CURLOPT_CUSTOMREQUEST, null);
                }
            }
        }

        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        return $this->exec();
    }

    /**
     * Put
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function put($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $put_data = $this->buildPostData($data);
        if (empty($this->options[CURLOPT_INFILE]) && empty($this->options[CURLOPT_INFILESIZE])) {
            if (is_string($put_data)) {
                $this->setHeader('Content-Length', strlen($put_data));
            }
        }
        if (!empty($put_data)) {
            $this->setOpt(CURLOPT_POSTFIELDS, $put_data);
        }
        return $this->exec();
    }

    /**
     * Search
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function search($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'SEARCH');
        $put_data = $this->buildPostData($data);
        if (empty($this->options[CURLOPT_INFILE]) && empty($this->options[CURLOPT_INFILESIZE])) {
            if (is_string($put_data)) {
                $this->setHeader('Content-Length', strlen($put_data));
            }
        }
        if (!empty($put_data)) {
            $this->setOpt(CURLOPT_POSTFIELDS, $put_data);
        }
        return $this->exec();
    }

    /**
     * Set Basic Authentication
     *
     * @access public
     * @param  $username
     * @param  $password
     */
    public function setBasicAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
        return $this;
    }

    /**
     * Set Digest Authentication
     *
     * @access public
     * @param  $username
     * @param  $password
     */
    public function setDigestAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
        return $this;
    }

    /**
     * Set Cookie
     *
     * @access public
     * @param  $key
     * @param  $value
     */
    public function setCookie($key, $value)
    {
        $name_chars = [];
        foreach (str_split($key) as $name_char) {
            if (isset($this->rfc2616[$name_char])) {
                $name_chars[] = $name_char;
            } else {
                $name_chars[] = rawurlencode($name_char);
            }
        }

        $value_chars = [];
        foreach (str_split($value) as $value_char) {
            if (isset($this->rfc6265[$value_char])) {
                $value_chars[] = $value_char;
            } else {
                $value_chars[] = rawurlencode($value_char);
            }
        }

        $this->cookies[implode('', $name_chars)] = implode('', $value_chars);

        $this->setOpt(CURLOPT_COOKIE, implode('; ', array_map(function ($k, $v) {
            return $k . '=' . $v;
        }, array_keys($this->cookies), array_values($this->cookies))));
        return $this;
    }

    /**
     * Set Cookies
     *
     * @access public
     * @param  $cookies
     */
    public function setCookies($cookies)
    {
        foreach ($cookies as $key => $value) {
            $name_chars = [];
            foreach (str_split($key) as $name_char) {
                if (isset($this->rfc2616[$name_char])) {
                    $name_chars[] = $name_char;
                } else {
                    $name_chars[] = rawurlencode($name_char);
                }
            }

            $value_chars = [];
            foreach (str_split($value) as $value_char) {
                if (isset($this->rfc6265[$value_char])) {
                    $value_chars[] = $value_char;
                } else {
                    $value_chars[] = rawurlencode($value_char);
                }
            }

            $this->cookies[implode('', $name_chars)] = implode('', $value_chars);
        }

        $this->setOpt(CURLOPT_COOKIE, implode('; ', array_map(function ($k, $v) {
            return $k . '=' . $v;
        }, array_keys($this->cookies), array_values($this->cookies))));
        return $this;
    }

    /**
     * Get Cookie
     *
     * @access public
     * @param  $key
     *
     * @return mixed
     */
    public function getCookie($key)
    {
        return $this->getResponseCookie($key);
    }

    /**
     * Get Response Cookie
     *
     * @access public
     * @param  $key
     *
     * @return mixed
     */
    public function getResponseCookie($key)
    {
        return isset($this->responseCookies[$key]) ? $this->responseCookies[$key] : null;
    }

    /**
     * Set Max Filesize
     *
     * @access public
     * @param  $bytes
     */
    public function setMaxFilesize($bytes)
    {

        $gte_v550 = version_compare(PHP_VERSION, '5.5.0') >= 0;
        if ($gte_v550) {
            $callback = function ($resource, $download_size, $downloaded, $upload_size, $uploaded) use ($bytes) {
                return $downloaded > $bytes ? 1 : 0;
            };
        } else {
            $callback = function ($download_size, $downloaded, $upload_size, $uploaded) use ($bytes) {
                return $downloaded > $bytes ? 1 : 0;
            };
        }

        $this->progress($callback);
        return $this;
    }

    /**
     * Set Port
     *
     * @access public
     * @param  $port
     */
    public function setPort($port)
    {
        $this->setOpt(CURLOPT_PORT, intval($port));
        return $this;
    }

    /**
     * Set Connect Timeout
     *
     * @access public
     * @param  $seconds
     */
    public function setConnectTimeout($seconds)
    {
        $this->setOpt(CURLOPT_CONNECTTIMEOUT, $seconds);
        return $this;
    }

    /**
     * Set Cookie String
     *
     * @access public
     * @param  $string
     *
     * @return bool
     */
    public function setCookieString($string)
    {
        $this->setOpt(CURLOPT_COOKIE, $string);
        return $this;
    }

    /**
     * Set Cookie File
     *
     * @access public
     * @param  $cookie_file
     *
     * @return boolean
     */
    public function setCookieFile($cookie_file)
    {
        $this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
        return $this;
    }

    /**
     * Set Cookie Jar
     *
     * @access public
     * @param  $cookie_jar
     *
     * @return boolean
     */
    public function setCookieJar($cookie_jar)
    {
        $this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
        return $this;
    }

    /**
     * Set Default JSON Decoder
     *
     * @access public
     * @param  $assoc
     * @param  $depth
     * @param  $options
     */
    public function setDefaultJsonDecoder()
    {
        $this->jsonDecoder = __NAMESPACE__.'\Decoder::decodeJson';
        $this->jsonDecoderArgs = func_get_args();
        return $this;
    }

    /**
     * Set Default XML Decoder
     *
     * @access public
     */
    public function setDefaultXmlDecoder()
    {
        $this->xmlDecoder = __NAMESPACE__.'\Decoder::decodeXml';
        return $this;
    }

    /**
     * Set Default Decoder
     *
     * @access public
     * @param  $mixed boolean|callable|string
     */
    public function setDefaultDecoder($mixed = 'json')
    {
        if ($mixed === false) {
            $this->defaultDecoder = false;
        } elseif (is_callable($mixed)) {
            $this->defaultDecoder = $mixed;
        } else {
            if ($mixed === 'json') {
                $this->defaultDecoder = $this->jsonDecoder;
            } elseif ($mixed === 'xml') {
                $this->defaultDecoder = $this->xmlDecoder;
            }
        }
        return $this;
    }

    /**
     * Set Default Timeout
     *
     * @access public
     */
    public function setDefaultTimeout()
    {
        $this->setTimeout(self::DEFAULT_TIMEOUT);
        return $this;
    }

    /**
     * Set Default User Agent
     *
     * @access public
     */
    public function setDefaultUserAgent()
    {
        $user_agent = 'haunpeng.com/' . self::VERSION;
        $user_agent .= ' PHP/' . PHP_VERSION;
        $curl_version = curl_version();
        $user_agent .= ' curl/' . $curl_version['version'];
        $this->setUserAgent($user_agent);
        return $this;
    }

    /**
     * Set Header
     *
     * Add extra header to include in the request.
     *
     * @access public
     * @param  $key
     * @param  $value
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        $headers             = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
        return $this;
    }

    /**
     * Set Headers
     *
     * Add extra headers to include in the request.
     *
     * @access public
     * @param  $headers
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }

        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
        return $this;
    }

    /**
     * Set JSON Decoder
     *
     * @access public
     * @param  $mixed boolean|callable
     */
    public function setJsonDecoder($mixed)
    {
        if ($mixed === false) {
            $this->jsonDecoder     = false;
            $this->jsonDecoderArgs = [];
        } elseif (is_callable($mixed)) {
            $this->jsonDecoder     = $mixed;
            $this->jsonDecoderArgs = [];
        }

        return $this;
    }

    /**
     * Set XML Decoder
     *
     * @access public
     * @param  $mixed boolean|callable
     */
    public function setXmlDecoder($mixed)
    {
        if ($mixed === false) {
            $this->xmlDecoder = false;
        } elseif (is_callable($mixed)) {
            $this->xmlDecoder = $mixed;
        }

        return $this;
    }

    /**
     * Set Opt
     *
     * @access public
     * @param  $option
     * @param  $value
     *
     * @return boolean
     */
    public function setOpt($option, $value)
    {
        $required_options = [
            CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
        ];

        if (in_array($option, array_keys($required_options), true) && !($value === true)) {
            trigger_error($required_options[$option] . ' is a required option', E_USER_WARNING);
        }

        $success = curl_setopt($this->curl, $option, $value);
        if ($success) {
            $this->options[$option] = $value;
        }
        return $success;
    }

    /**
     * Set Opts
     *
     * @access public
     * @param  $options
     *
     * @return boolean
     */
    public function setOpts($options)
    {
        foreach ($options as $option => $value) {
            if (!$this->setOpt($option, $value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Set Referer
     *
     * @access public
     * @param  $referer
     */
    public function setReferer($referer)
    {
        $this->setReferrer($referer);
        return $this;
    }

    /**
     * Set Referrer
     *
     * @access public
     * @param  $referrer
     */
    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);
        return $this;
    }

    /**
     * Set Timeout
     *
     * @access public
     * @param  $seconds
     */
    public function setTimeout($seconds)
    {
        $this->setOpt(CURLOPT_TIMEOUT, $seconds);
        return $this;
    }

    /**
     * Set Url
     *
     * @access public
     * @param  $url
     * @param  $mixed_data
     */
    public function setUrl($url, $mixed_data = '')
    {
        $this->baseUrl = $url;
        $this->url = $this->buildURL($url, $mixed_data);
        $this->setOpt(CURLOPT_URL, $this->url);
        return $this;
    }

    /**
     * Set User Agent
     *
     * @access public
     * @param  $user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
        return $this;
    }

    /**
     * Success
     *
     * @access public
     * @param  $callback
     */
    public function success($callback)
    {
        $this->successFunction = $callback;
    }

    /**
     * Unset Header
     *
     * Remove extra header previously set using Curl::setHeader().
     *
     * @access public
     * @param  $key
     */
    public function unsetHeader($key)
    {
        unset($this->headers[$key]);
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Remove Header
     *
     * @access public
     * @param  $key
     */
    public function removeHeader($key)
    {
        $this->setHeader($key, '');
    }

    /**
     * Verbose
     *
     * @access public
     * @param  bool $on
     * @param  resource $output
     */
    public function verbose($on = true, $output = STDERR)
    {
        if ($on) {
            $this->setOpt(CURLINFO_HEADER_OUT, false);
        }
        $this->setOpt(CURLOPT_VERBOSE, $on);
        $this->setOpt(CURLOPT_STDERR, $output);
    }

    /**
     * Destruct
     *
     * @access public
     */
    public function __destruct()
    {
        $this->close();
    }

    public function __get($name)
    {
        $return = null;
        if (in_array($name, self::$deferredProperties) && is_callable(array($this, $getter = '__get_' . $name))) {
            $return = $this->$name = $this->$getter();
        }
        return $return;
    }

    /**
     * Get Effective Url
     *
     * @access private
     */
    private function __get_effectiveUrl()
    {
        return $this->getInfo(CURLINFO_EFFECTIVE_URL);
    }

    /**
     * Get RFC 2616
     *
     * @access private
     */
    private function __get_rfc2616()
    {
        return array_fill_keys(self::$RFC2616, true);
    }

    /**
     * Get RFC 6265
     *
     * @access private
     */
    private function __get_rfc6265()
    {
        return array_fill_keys(self::$RFC6265, true);
    }

    /**
     * Get Total Time
     *
     * @access private
     */
    private function __get_totalTime()
    {
        return $this->getInfo(CURLINFO_TOTAL_TIME);
    }

    /**
     * Build Url
     *
     * @access private
     * @param  $url
     * @param  $mixed_data
     *
     * @return string
     */
    private function buildURL($url, $mixed_data = '')
    {
        $query_string = '';
        if (!empty($mixed_data)) {
            if (is_string($mixed_data)) {
                $query_string .= '?' . $mixed_data;
            } elseif (is_array($mixed_data)) {
                $query_string .= '?' . http_build_query($mixed_data, '', '&');
            }
        }
        return $url . $query_string;
    }

    /**
     * Parse Headers
     *
     * @access private
     * @param  $raw_headers
     *
     * @return array
     */
    private function parseHeaders($raw_headers)
    {
        $raw_headers = preg_split('/\r\n/', $raw_headers, null, PREG_SPLIT_NO_EMPTY);
        $http_headers = new CaseInsensitiveArray();

        $raw_headers_count = count($raw_headers);
        for ($i = 1; $i < $raw_headers_count; $i++) {
            list($key, $value) = explode(':', $raw_headers[$i], 2);
            $key = trim($key);
            $value = trim($value);
            if (isset($http_headers[$key])) {
                $http_headers[$key] .= ',' . $value;
            } else {
                $http_headers[$key] = $value;
            }
        }

        return array(isset($raw_headers['0']) ? $raw_headers['0'] : '', $http_headers);
    }

    /**
     * Parse Request Headers
     *
     * @access private
     * @param  $raw_headers
     *
     * @return array
     */
    private function parseRequestHeaders($raw_headers)
    {
        $request_headers = new CaseInsensitiveArray();
        list($first_line, $headers) = $this->parseHeaders($raw_headers);
        $request_headers['Request-Line'] = $first_line;
        foreach ($headers as $key => $value) {
            $request_headers[$key] = $value;
        }
        return $request_headers;
    }

    /**
     * Parse Response
     *
     * @access private
     * @param  $response_headers
     * @param  $raw_response
     *
     * @return mixed
     */
    private function parseResponse($response_headers, $raw_response)
    {
        $response = $raw_response;
        if (isset($response_headers['Content-Type'])) {
            if (preg_match($this->jsonPattern, $response_headers['Content-Type'])) {
                if ($this->jsonDecoder) {
                    $args = $this->jsonDecoderArgs;
                    array_unshift($args, $response);
                    $response = call_user_func_array($this->jsonDecoder, $args);
                }
            } elseif (preg_match($this->xmlPattern, $response_headers['Content-Type'])) {
                if ($this->xmlDecoder) {
                    $response = call_user_func($this->xmlDecoder, $response);
                }
            } else {
                if ($this->defaultDecoder) {
                    $response = call_user_func($this->defaultDecoder, $response);
                }
            }
        }

        return $response;
    }

    /**
     * Parse Response Headers
     *
     * @access private
     * @param  $raw_response_headers
     *
     * @return array
     */
    private function parseResponseHeaders($raw_response_headers)
    {
        $response_header_array = explode("\r\n\r\n", $raw_response_headers);
        $response_header  = '';
        for ($i = count($response_header_array) - 1; $i >= 0; $i--) {
            if (stripos($response_header_array[$i], 'HTTP/') === 0) {
                $response_header = $response_header_array[$i];
                break;
            }
        }

        $response_headers = new CaseInsensitiveArray();
        list($first_line, $headers) = $this->parseHeaders($response_header);
        $response_headers['Status-Line'] = $first_line;
        foreach ($headers as $key => $value) {
            $response_headers[$key] = $value;
        }
        return $response_headers;
    }
}

class MultiCurl
{
    public $baseUrl      = null;
    public $multiCurl;

    private $curls       = [];
    private $activeCurls = [];
    private $isStarted   = false;
    private $concurrency = 25;
    private $nextCurlId  = 0;

    private $beforeSendFunction = null;
    private $successFunction    = null;
    private $errorFunction      = null;
    private $completeFunction   = null;

    private $cookies = [];
    private $headers = [];
    private $options = [];

    private $jsonDecoder = null;
    private $xmlDecoder  = null;

    /**
     * Construct
     *
     * @access public
     * @param  $base_url
     */
    public function __construct($base_url = null)
    {
        $this->multiCurl = curl_multi_init();
        $this->headers   = new CaseInsensitiveArray();
        $this->setUrl($base_url);
    }

    /**
     * Add Delete
     *
     * @access public
     * @param  $url
     * @param  $query_parameters
     * @param  $data
     *
     * @return object
     */
    public function addDelete($url, $query_parameters = array(), $data = array())
    {
        if (is_array($url)) {
            $data = $query_parameters;
            $query_parameters = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url, $query_parameters);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $curl->setOpt(CURLOPT_POSTFIELDS, $curl->buildPostData($data));
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Add Download
     *
     * @access public
     * @param  $url
     * @param  $mixed_filename
     *
     * @return object
     */
    public function addDownload($url, $mixed_filename)
    {
        $curl = new Curl();
        $curl->setUrl($url);

        if (is_callable($mixed_filename)) {
            $callback = $mixed_filename;
            $curl->downloadCompleteFunction = $callback;
            $curl->fileHandle = tmpfile();
        } else {
            $filename = $mixed_filename;
            $curl->downloadCompleteFunction = function ($instance, $fh) use ($filename) {
                file_put_contents($filename, stream_get_contents($fh));
            };
            $curl->fileHandle = fopen('php://temp', 'wb');
        }

        $curl->setOpt(CURLOPT_FILE, $curl->fileHandle);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Add Get
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addGet($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url, $data);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Add Head
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addHead($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url, $data);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $curl->setOpt(CURLOPT_NOBODY, true);
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Add Options
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addOptions($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url, $data);
        $curl->removeHeader('Content-Length');
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Add Patch
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addPatch($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url);
        $curl->removeHeader('Content-Length');
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $curl->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Add Post
     *
     * @access public
     * @param  $url
     * @param  $data
     * @param  $follow_303_with_post
     *
     * @return object
     */
    public function addPost($url, $data = array(), $follow_303_with_post = false)
    {
        if (is_array($url)) {
            $follow_303_with_post = (bool)$data;
            $data = $url;
            $url = $this->baseUrl;
        }

        $curl = new Curl();

        if (is_array($data) && empty($data)) {
            $curl->removeHeader('Content-Length');
        }

        $curl->setUrl($url);

        if (!$follow_303_with_post) {
            $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        }
        $curl->setOpt(CURLOPT_POST, true);
        $curl->setOpt(CURLOPT_POSTFIELDS, $curl->buildPostData($data));
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Add Put
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addPut($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $put_data = $curl->buildPostData($data);
        if (is_string($put_data)) {
            $curl->setHeader('Content-Length', strlen($put_data));
        }
        $curl->setOpt(CURLOPT_POSTFIELDS, $put_data);
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Add Search
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addSearch($url, $data = [])
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'SEARCH');
        $put_data = $curl->buildPostData($data);
        if (is_string($put_data)) {
            $curl->setHeader('Content-Length', strlen($put_data));
        }
        $curl->setOpt(CURLOPT_POSTFIELDS, $put_data);
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Add Curl
     *
     * Add a Curl instance to the handle queue.
     *
     * @access public
     * @param  $curl
     *
     * @return object
     */
    public function addCurl(Curl $curl)
    {
        $this->queueHandle($curl);
        return $curl;
    }

    /**
     * Before Send
     *
     * @access public
     * @param  $callback
     */
    public function beforeSend($callback)
    {
        $this->beforeSendFunction = $callback;
    }

    /**
     * Close
     *
     * @access public
     */
    public function close()
    {
        foreach ($this->curls as $curl) {
            $curl->close();
        }

        if (is_resource($this->multiCurl)) {
            curl_multi_close($this->multiCurl);
        }
    }

    /**
     * Complete
     *
     * @access public
     * @param  $callback
     */
    public function complete($callback)
    {
        $this->completeFunction = $callback;
    }

    /**
     * Error
     *
     * @access public
     * @param  $callback
     */
    public function error($callback)
    {
        $this->errorFunction = $callback;
    }

    /**
     * Set Url
     *
     * @access public
     * @param  $url
     */
    public function setUrl($url)
    {
        $this->baseUrl = $url;
        return $this;
    }

    /**
     * Start
     *
     * @access public
     */
    public function start()
    {
        if ($this->isStarted) {
            return;
        }

        $this->isStarted = true;

        $concurrency = $this->concurrency;
        if ($concurrency > count($this->curls)) {
            $concurrency = count($this->curls);
        }

        for ($i = 0; $i < $concurrency; $i++) {
            $this->initHandle(array_shift($this->curls));
        }

        do {

            if (curl_multi_select($this->multiCurl) === -1) {
                usleep(1);
            }

            curl_multi_exec($this->multiCurl, $active);

            while (!($info_array = curl_multi_info_read($this->multiCurl)) === false) {
                if ($info_array['msg'] === CURLMSG_DONE) {
                    foreach ($this->activeCurls as $key => $ch) {
                        if ($ch->curl === $info_array['handle']) {

                            $ch->curlErrorCode = $info_array['result'];
                            $ch->exec($ch->curl);

                            unset($this->activeCurls[$key]);

                            if (count($this->curls) >= 1) {
                                $this->initHandle(array_shift($this->curls));
                            }
                            curl_multi_remove_handle($this->multiCurl, $ch->curl);

                            $ch->close();

                            break;
                        }
                    }
                }
            }

            if (!$active) {
                $active = count($this->activeCurls);
            }
        } while ($active > 0);

        $this->isStarted = false;
    }

    /**
     * Success
     *
     * @access public
     * @param  $callback
     */
    public function success($callback)
    {
        $this->successFunction = $callback;
    }

    /**
     * Destruct
     *
     * @access public
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Queue Handle
     *
     * @access private
     * @param  $curl
     */
    private function queueHandle($curl)
    {
        $curl->id = $this->nextCurlId++;
        $this->curls[$curl->id] = $curl;
    }

    /**
     * Init Handle
     *
     * @access private
     * @param  $curl
     * @throws \ErrorException
     */
    private function initHandle($curl)
    {
        if ($curl->beforeSendFunction === null) {
            $curl->beforeSend($this->beforeSendFunction);
        }
        if ($curl->successFunction === null) {
            $curl->success($this->successFunction);
        }
        if ($curl->errorFunction === null) {
            $curl->error($this->errorFunction);
        }
        if ($curl->completeFunction === null) {
            $curl->complete($this->completeFunction);
        }

        $curlm_error_code = curl_multi_add_handle($this->multiCurl, $curl->curl);
        if (!($curlm_error_code === CURLM_OK)) {
            throw new \ErrorException('cURL multi add handle error: ' . curl_multi_strerror($curlm_error_code));
        }

        $this->activeCurls[$curl->id] = $curl;
        $curl->call($curl->beforeSendFunction);
    }
}

class Decoder
{
    /**
     * Decode JSON
     *
     * @access public
     * @param  $json
     * @param  $assoc
     * @param  $depth
     * @param  $options
     */
    public static function decodeJson() {
        $args = func_get_args();

        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $args = array_slice($args, 0, 3);
        }

        $response = call_user_func_array('json_decode', $args);
        if ($response === null) {
            $response = $args['0'];
        }
        return $response;
    }

    /**
     * Decode XML
     *
     * @access public
     * @param  $response
     */
    public static function decodeXml($response) {
        $xml_obj = @simplexml_load_string($response);
        if (!($xml_obj === false)) {
            $response = $xml_obj;
        }
        return $response;
    }
}


class CaseInsensitiveArray implements \ArrayAccess, \Countable, \Iterator
{

    /**
     * @var mixed[] Data storage with lower-case keys
     * @see offsetSet()
     * @see offsetExists()
     * @see offsetUnset()
     * @see offsetGet()
     * @see count()
     * @see current()
     * @see next()
     * @see key()
     */
    private $data = array();

    /**
     * @var string[] Case-Sensitive keys.
     * @see offsetSet()
     * @see offsetUnset()
     * @see key()
     */
    private $keys = array();

    /**
     * Construct
     *
     * @param mixed[] $initial (optional) Existing Array to convert.
     *
     * @return CaseInsensitiveArray
     *
     * @access public
     */
    public function __construct(array $initial = null)
    {
        if ($initial !== null) {
            foreach ($initial as $key => $value) {
                $this->offsetSet($key, $value);
            }
        }
    }

    /**
     * Offset Set
     *
     * Set data at a specified Offset.  Converts the offset to lower-case, and
     * stores the Case-Sensitive Offset and the Data at the lower-case indexes
     * in $this->keys and @this->data.
     *
     * @see https://secure.php.net/manual/en/arrayaccess.offseteset.php
     *
     * @param string $offset The offset to store the data at (case-insensitive).
     * @param mixed $value The data to store at the specified offset.
     *
     * @return void
     *
     * @access public
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $offsetlower = strtolower($offset);
            $this->data[$offsetlower] = $value;
            $this->keys[$offsetlower] = $offset;
        }
    }

    /**
     * Offset Exists
     *
     * Checks if the Offset exists in data storage.  The index is looked up with
     * the lower-case version of the provided offset.
     *
     * @see https://secure.php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param string $offset Offset to check
     *
     * @return bool If the offset exists.
     *
     * @access public
     */
    public function offsetExists($offset)
    {
        return (bool) array_key_exists(strtolower($offset), $this->data);
    }

    /**
     * Offset Unset
     *
     * Unsets the specified offset. Converts the provided offset to lowercase,
     * and unsets the Case-Sensitive Key, as well as the stored data.
     *
     * @see https://secure.php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param string $offset The offset to unset.
     *
     * @return void
     *
     * @access public
     */
    public function offsetUnset($offset)
    {
        $offsetlower = strtolower($offset);
        unset($this->data[$offsetlower]);
        unset($this->keys[$offsetlower]);
    }

    /**
     * Offset Get
     *
     * Return the stored data at the provided offset. The offset is converted to
     * lowercase and the lookup is done on the Data store directly.
     *
     * @see https://secure.php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param string $offset Offset to lookup.
     *
     * @return mixed The data stored at the offset.
     *
     * @access public
     */
    public function offsetGet($offset)
    {
        $offsetlower = strtolower($offset);
        return isset($this->data[$offsetlower]) ? $this->data[$offsetlower] : null;
    }

    /**
     * Count
     *
     * @see https://secure.php.net/manual/en/countable.count.php
     *
     * @param void
     *
     * @return int The number of elements stored in the Array.
     *
     * @access public
     */
    public function count()
    {
        return (int) count($this->data);
    }

    /**
     * Current
     *
     * @see https://secure.php.net/manual/en/iterator.current.php
     *
     * @param void
     *
     * @return mixed Data at the current position.
     *
     * @access public
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Next
     *
     * @see https://secure.php.net/manual/en/iterator.next.php
     *
     * @param void
     *
     * @return void
     *
     * @access public
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * Key
     *
     * @see https://secure.php.net/manual/en/iterator.key.php
     *
     * @param void
     *
     * @return mixed Case-Sensitive key at current position.
     *
     * @access public
     */
    public function key()
    {
        $key = key($this->data);
        return isset($this->keys[$key]) ? $this->keys[$key] : $key;
    }

    /**
     * Valid
     *
     * @see https://secure.php.net/manual/en/iterator.valid.php
     *
     * @return bool If the current position is valid.
     *
     * @access public
     */
    public function valid()
    {
        return (bool) !(key($this->data) === null);
    }

    /**
     * Rewind
     *
     * @see https://secure.php.net/manual/en/iterator.rewind.php
     *
     * @param void
     *
     * @return void
     *
     * @access public
     */
    public function rewind()
    {
        reset($this->data);
    }
}

class ArrayUtil
{
    /**
     * Is Array Assoc
     *
     * @access public
     * @param  $array
     *
     * @return boolean
     */
    public static function is_array_assoc($array)
    {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

    /**
     * Is Array Multidim
     *
     * @access public
     * @param  $array
     *
     * @return boolean
     */
    public static function is_array_multidim($array)
    {
        if (!is_array($array)) {
            return false;
        }

        return (bool)count(array_filter($array, 'is_array'));
    }

    /**
     * Array Flatten Multidim
     *
     * @access public
     * @param  $array
     * @param  $prefix
     *
     * @return array
     */
    public static function array_flatten_multidim($array, $prefix = false)
    {
        $return = [];
        if (is_array($array) || is_object($array)) {
            if (empty($array)) {
                $return[$prefix] = '';
            } else {
                foreach ($array as $key => $value) {
                    if (is_scalar($value)) {
                        if ($prefix) {
                            $return[$prefix . '[' . $key . ']'] = $value;
                        } else {
                            $return[$key] = $value;
                        }
                    } else {
                        if ($value instanceof \CURLFile) {
                            $return[$key] = $value;
                        } else {
                            $return = array_merge(
                                $return,
                                self::array_flatten_multidim(
                                    $value,
                                    $prefix ? $prefix . '[' . $key . ']' : $key
                                )
                            );
                        }
                    }
                }
            }
        } elseif ($array === null) {
            $return[$prefix] = $array;
        }
        return $return;
    }
}
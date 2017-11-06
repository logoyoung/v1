<?php
namespace service\live;
use Exception;
use service\common\AbstractService;
use lib\live\LiveHelper;
/**
 * 获取直播流
 */
class StreamDataService
{

    private static $_log = 'stream_data_service_access';

    /**
     * 新版流信息获取
     * @param  int $anchorUid 主播uid
     * @return array
     */
    public static function getMultiStreamByAnchorUid($anchorUid)
    {
        try
        {

            $stream = LiveHelper::getplayurl($anchorUid);
            if(!$stream || !is_array($stream))
            {
                return [];
            }

            if(isset($stream['master']['poster']) && $stream['master']['poster'])
            {
                $stream['master']['poster'] = self::getPosterUrl($stream['master']['poster']);
            }

            if(isset($stream['slave']['poster']) && $stream['slave']['poster'])
            {
                $stream['slave']['poster'] = self::getPosterUrl($stream['slave']['poster']);
            }

            return $stream;

        } catch (Exception $e) {

            write_log("error|获取直播流，数据库异常,anchorUid:{$anchorUid}; error_msg:{$e->getMessage()}; trace:{$e->getTraceAsString()}; line:" . __LINE__, self::$_log);
            return [];

        }
    }

    /**
     * 兼容旧版流数据（旧版只有主屏流）
     * @param  array  $multiStream 新版流信息
     * @return array
     */
    public static function getOldMasterStreamByMultiStream(array $multiStream)
    {
        $result = [
            'orientation' => '',
            'liveID'      => '',
            'streamList'  => [],
            'stream'      => '',
            'isLiving'    => 0,
        ];

        $masterStream = isset($multiStream['master'])  ? $multiStream['master'] : [];

        // if(!isset($masterStream['status']) || $masterStream['status'] != LIVE)
        // {
        //     return $result;
        // }

        if(!isset($masterStream['playtype']) || $masterStream['playtype'] != 1)
        {
            $result['liveID'] = isset($masterStream['liveid']) ? $masterStream['liveid'] : 0;
            return $result;
        }

        $result['orientation'] = (int) $masterStream['orientation'];
        $result['liveID']      = $masterStream['liveid'];
        $result['streamList']  = (array) $masterStream['server'];
        $result['stream']      = "{$masterStream['stream']}?{$masterStream['token']}";
        $result['isLiving']    = 1;

        return $result;
    }

    /**
     * 获取h5直播流信息
     * @param  array  $multiStream 新版流信息
     * @return array
     */
    public static function getH5StreamByMultiStream(array $multiStream)
    {
        $result = [
            'streamList'  => [],
            'orientation' => '',
            'stream'      => '',
            'liveID'      => '',
            'isLiving'    => 0,
        ];

        $masterStream = isset($multiStream['master'])  ? $multiStream['master'] : [];
        // if(!isset($masterStream['status']) || $masterStream['status'] != LIVE)
        // {
        //     return $result;
        // }

        if(!isset($masterStream['playtype']) || $masterStream['playtype'] != 1)
        {
            $result['liveID'] = isset($masterStream['liveid']) ? $masterStream['liveid'] : 0;
            return $result;
        }

        $result['orientation'] = (int) $masterStream['orientation'];
        $result['liveID']      = $masterStream['liveid'];
        $result['streamList']  = (array) $masterStream['server'];
        $result['stream']      = self::hlsStream($masterStream['stream']);
        $result['isLiving']    = 1;

        return $result;
    }

    public static function getPosterUrl($poster)
    {
        $conf   = self::getConf();
        $poster = ltrim($poster, '/');
        return isset($conf['domain-lposter']) ? rtrim($conf['domain-lposter'], '/') . '/' . $poster : $poster;
    }

    public static function getConf()
    {
        return $GLOBALS['env-def'][$GLOBALS['env']];
    }

    public static function hlsStream($stream)
    {
        if(!$stream)
        {
            return '';
        }

        $st     = 'liverecord/'.$stream;
        $iparam = createHlsSecret($st);

        return  $stream . '?' . $iparam;
    }
}
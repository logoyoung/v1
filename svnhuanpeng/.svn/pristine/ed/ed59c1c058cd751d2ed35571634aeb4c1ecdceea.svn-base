<?php

    //define('DEBUG', 1);
    require(__DIR__.'/../include/init.php');
    define('GET_TASK_URL', WEB_ROOT_URL.'a/getVideoTask.php');
    define('GET_TASK_INFO_URL', WEB_ROOT_URL.'a/getStreamListByLive.php');
    define('CALLBACK_URL' ,WEB_ROOT_URL.'a/videoSavedCallBack.php');
    define('REPORT_URL', WEB_ROOT_URL.'a/videoMergeReport.php');

    define('FILE_SAVE_TEMP_DIR', '/data/tmp');
    define('CURL_CMD', '/usr/bin/curl -Ss');
    define('FFMPEG_CMD', '/usr/local/bin/ffmpeg');
    define('FFPROBE_CMD', '/usr/local/bin/ffprobe');

    define('LOG_FILE', '/data/logs/saverecord.log');

    define('SLEEP_INTERVAL', 1);
    
    //define('START_TASK', 26000);
    //date_default_timezone_set( 'Asia/Shanghai' );

    /*********************** FUNCTIONS **********************/

    /*function get_datetime($tm=null)
    {
        if (!$tm) $tm = time();
        return date( "Y-m-d H:i:s", $tm );
    }*/

    function _my_log($msg)
    {
        $pid = '['.getmypid().']';
        $tm = get_datetime();
        $msg = $pid.'['.$tm.'] '.$msg;
        return file_put_contents(LOG_FILE, $msg."\n", FILE_APPEND);
    }

    function url_get_contents($url)
    {
        // TODO: should be rewrite by libcurl functions
        return file_get_contents($url);
    }

    function parse_ip($str)
    {
        $pat = '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/';
        $n = preg_match($pat, $str, $m);
        if ($n==0)
            return false;
        else
            return $m[0];
    }

    function check_file($filename, &$error)
    {
        // TODO: more strict file test
        // file size smaller then 100 bytes
        if (filesize($filename)<=100)
        {
            $error = file_get_contents($filename);
            return false;
        }
        return true;
    }
    function reporter($msg = array()){
        //TODO:report the error
        $msg = http_build_query($msg);
        return url_get_contents(REPORT_URL.'?'.$msg);
    }

    /********************** MAIN ***********************/

    while (true)
    {

        // get liveid for saving
        $liveidr = url_get_contents(GET_TASK_URL);
        if (DEBUG) _my_log('got id: '.$liveidr);
        $liveid = (int)$liveidr;
        if ($liveid<0)
        {
            _my_log('get liveid failed with result:'.$liveidr);
            continue;
        }
        if ($liveid==0)
        {
            sleep(SLEEP_INTERVAL);
            continue;
        }

        // get live info
        $url = GET_TASK_INFO_URL."?liveid=$liveid";
        $content = url_get_contents($url);
        if (DEBUG) _my_log('get task info url:'.$url.' result:'.$content);
        $list = json_decode($content, true);
        if (!is_array($list) or count($list)==0)
        {   
            $errStr = 'get task info failed with result:'.$content;
            _my_log($errStr);
            reporter(array(
                'errcode'=>1,
                'errmsg' =>$errStr,
                'taskid' =>$liveid
            ));
            continue;
        }

        // download files
        $file_list = array();
        foreach ($list as $ss)
        {
            $server = parse_ip($ss['server']);
            if (!$server)
            {   
                $errStr = 'cannot paser ip info from:'.$ss['server'];
                _my_log($errStr);
                reporter(array(
                'errcode'=>2,
                'errmsg' =>$errStr,
                'taskid' =>$liveid
                ));
                continue;
            }
            $stream = $ss['stream'];

            $download_url = "http://$server:3559/$stream.flv";
            $tmp_file = FILE_SAVE_TEMP_DIR."/$stream.flv";
            $cmd = CURL_CMD." \"$download_url\" -o $tmp_file";
            if (DEBUG) _my_log('downloading file: '.$cmd);
            `$cmd`;
            $r = check_file($tmp_file, $error);
            if (!$r)
            {   
                $errStr = 'download file failed with url:'.$download_url.' error:'.$error;
                _my_log($errStr);
                reporter(array(
                'errcode'=>3,
                'errmsg' =>$errStr,
                'taskid' =>$liveid
                ));
                continue 2;
            }

            $file_list[] = $tmp_file;
        }

        // merge files
        $file_output = FILE_SAVE_TEMP_DIR."/$liveid.mp4";
        /* if (!count($file_list)==1)
        {   
            if (DEBUG) _my_log('got only one file, renaming from:'.$file_list[0].' to:'.$file_output);
            //rename($file_list[0], $file_output); 
            //flv conv to mp4 by ffmpeg 
            $cmd = FFMPEG_CMD." -i $file_list[0] -c:v libx264 -crf 19 -strict experimental $file_output";
            if (DEBUG) _my_log('conv file: '.$cmd);
            `$cmd`;
            //delete temp file
            if (DEBUG) _my_log('deleting file: '.$file_list[0]);
            unlink($file_list[0]); 
        }
        else
        { */
            // generate merge list file
        $listfile = FILE_SAVE_TEMP_DIR."/$liveid.lst";
        $content = '';
        foreach ($file_list as $file)
        {
            $fn = str_replace(FILE_SAVE_TEMP_DIR.'/', '', $file);
            $content .= "file '$fn'\n";
        }
        file_put_contents($listfile, $content);
        
        // merge
        $cmd = FFMPEG_CMD." -f concat -i $listfile -c copy -movflags faststart $file_output";
        if (DEBUG) _my_log('merging file: '.$cmd);
        `$cmd`;

        // delete temp files
        foreach ($file_list as $file)
        {
            if (DEBUG) _my_log('deleting file: '.$file);
            unlink($file);
        }
        if (DEBUG) _my_log('deleting file: '.$listfile);
        unlink($listfile); 
        //}

        // get file info
        $file_poster = FILE_SAVE_TEMP_DIR."/$liveid.jpg";
        $cmd = FFPROBE_CMD." -i $file_output 2>&1";
        if (DEBUG) _my_log('getting file info: '.$cmd);
        $r = `$cmd`;
        $pat = '/Duration: (\d+):(\d+):(\d+)\.\d+,/';
        $n = preg_match($pat, $r, $m);
        if (!$n)
        {
            $errStr = 'get output file info failed with result:'.$r;
            _my_log($errStr);
            reporter(array(
                'errcode'=>4,
                'errmsg' =>$errStr,
                'taskid' =>$liveid
                ));
            continue;
        }
        $duration = $m[1]*3600+$m[2]*60+$m[3];
        if (DEBUG) _my_log('got duration: '.$duration);

        // take poster
        $pos = floor($duration/2);     // caculate the image position
        $pos_s = $pos - 50;
        if ($pos_s < 0) $pos_s = 0;
        $pos_i = $pos - $pos_s;
        $cmd = FFMPEG_CMD." -ss $pos_s -i $file_output -f mjpeg -vframes 1 -ss $pos_i $file_poster";
        if (DEBUG) _my_log('taking poster: '.$cmd);
        $r = `$cmd`;

        // call back
        $callBackUrl = CALLBACK_URL."?liveid=$liveid&vfile=$file_output&poster=$file_poster&length=$duration";
        $callBackRet = url_get_contents($callBackUrl);
        $result      = $callBackRet?'suceess':'failed';
        if (DEBUG) _my_log("the task $liveid callback $result");
        if(!$callBackRet)
            reporter(array(
                'errcode'=>5,
                'errmsg' =>'callback failed',
                'taskid' =>$liveid
            ));
        //todo
        //exit;
    }

?>
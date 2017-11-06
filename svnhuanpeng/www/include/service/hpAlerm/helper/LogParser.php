<?php
namespace service\hpAlerm\helper;

class LogParser
{

    //日志名
    private $_logFile;
    private $_tl    = 0;
    private $_regex;

    public function setLogFile($file)
    {
        $this->_logFile = $file;
        $this->_regex    = null;
        return $this;
    }

    public function setTl($tl)
    {
        $this->_tl = (int) $tl;
        return $this;
    }

    public function getTl()
    {
        return $this->_tl;
    }

    public function setRegex($regex)
    {
        $this->_regex = $regex;
        return $this;
    }

    public function getMatch()
    {
        if(!file_exists($this->_logFile))
        {
            return false;
        }

        if(!is_readable($this->_logFile))
        {
            return -1;
        }

        $fp = fopen($this->_logFile, 'r');
        if(!$fp)
        {
            unset($fp);
            return -2;
        }

        $match = [
            'num' => 0,
            'msg' => '',
            'tl'  => $this->_tl,
        ];

        if($this->_tl > 0)
        {
            fseek($fp,$this->_tl);
        }
        $n = 0;
        while(( ($line = fgets($fp,4096))  !== false ) && !feof($fp))
        {
            $match['tl']    = ftell($fp);
            $line           = trim($line);

            if($this->_tl != 0)
            {
                if($n++ >= 5000)
                {
                    break;
                }
            }

            if($this->_regex)
            {
                if(!preg_match($this->_regex, $line))
                {
                    continue;
                }
            }

            $match['num']++;
            $match['msg']   = $line;
        }

        fclose($fp);
        $fp = null;

        return $match;
    }

}
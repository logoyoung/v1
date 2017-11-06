<?php

// +----------------------------------------------------------------------
// Curl
// +----------------------------------------------------------------------

namespace HP\Util;

class Curl
{
    static public function post($uri,$data)
    {
        is_array($data) and $data=http_build_query($data);
        $ch = curl_init ();
        curl_setopt ($ch, CURLOPT_URL,$uri);
        curl_setopt ($ch, CURLOPT_POST,1);
        curl_setopt ($ch, CURLOPT_HEADER,0);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS,$data);
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }
    static public function get($uri)
    {
        $ch = curl_init ();
        curl_setopt ($ch, CURLOPT_URL,$uri);
        curl_setopt ($ch, CURLOPT_HEADER,0);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER,1);
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }
}

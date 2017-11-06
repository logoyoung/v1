<?php
namespace service\rule\helper;

class HuanPengWordFilter
{
    public static function getFilterRegx()
    {
        $filer = [
            '欢朋'    => '#欢[^\w]*朋#',
            '官方'    => '#官[^\w]*方#',
            '斗鱼'    => '#斗[^\w]*鱼#',
            '虎牙'    => '#虎[^\w]*牙#',
            '触手'    => '#触[^\w]*手#',
            '熊猫'    => '#熊[^\w]*猫#',
            '战旗'    => '#战[^\w]*旗#',
            '龙珠'    => '#龙[^\w]*珠#',
            '映客'    => '#映[^\w]*客#',
            '花椒'    => '#花[^\w]*椒#',
        ];

        return $filer;
    }

    public static function checkFilterWord($word)
    {
        $regx = self::getFilterRegx();
        foreach ($regx as $k => $p)
        {
            if(preg_match($p, $word))
            {
                return $k;
            }
        }

        return true;
    }
}
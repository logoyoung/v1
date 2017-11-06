<?php

namespace HP\Util;
class Repair{
    
    //2017年8月11日 zwq add。
    //对比 anchor;hpf_rate 表 rate
    static public function getRate(){
        $where['type'] = 1;
        $hpf_rates = D( 'hpf_rate' )->where( $where )->getField( 'uid,rate' );
        $rates = D( 'anchor' )->getField( 'uid,rate' );
        $rates[0] = 60;
        
        foreach ( $rates as $uid => $rate )
        {
            $rate = $rate / 100;
            if( $rate == 0.6 )
            {
                continue;
            }
            if( $rate <> $hpf_rates[$uid] )
            {
                $data['uid'] = $uid;
                $data['anchor_rate'] = $rate;
                $data['hpf_rate'] = $hpf_rates[$uid];
                $datas['diff1'][] = $data;
            }
        }
        
        foreach ( $hpf_rates as $uid => $rate )
        {
            $anchor_rate = $rates[$uid] / 100;
            if( $anchor_rate == 0.6 )
            {
                continue;
            }
            if( $rate <> $anchor_rate )
            {

                $data['uid'] = $uid;
                $data['anchor_rate'] = $anchor_rate;
                $data['hpf_rate'] = $hpf_rates[$uid];
                $datas['diff2'][] = $data;
            }
        }
        return $datas;
    }
}
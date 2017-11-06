<?php
$uid = [113851,113647,2175,2250,2925,3820,4430,4450];
$uids = array_rand($uid,1);
        $u = $uids;
        echo $u;
die;
require __DIR__.'/../../include/init.php';


function t1()
{
    $db   = new DBHelperi_huanpeng();
    $uids = '69375';
    $res  = $db->field( 'uid,level' )->where( "uid in($uids)" )->order( 'level desc' )->select( 'anchor' );
    print_r($res);
}


function t2()
{
    $uid = [113851,113647,2175,2250,2925,3820,4430,4450];
    for($i = 1; $i <= 20;$i++) {
        $db   = new DBHelperi_huanpeng();
        $uids = array_rand($uid,1);
        $u = $uids[0];
        $res  = $db->field( 'uid,level' )->where( "uid in($u)" )->order( 'level desc' )->select( 'anchor' );
        print_r($res);
    }
}

//strace -o t.log -t -F -f php mysqli.php
t2();
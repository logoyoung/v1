<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/7/13
 * Time: 下午5:45
 */

include "../../includeAdmin/init.php";

$db = new DBHelperi_admin();

//改变结果
function succ3($content=array()){
    if (empty($content)) {
        $succ = array(
            'status' => "1",
            'content' => (object) $content
        );
    } else {
        $succ = toString(array(
            'status' => "1",
            'content' => $content
        ));
    }
    exit(json_encode($succ));
}

function error3($code) {
    $err = array(
        'status' => 0,
        'content' => array(
            'code' => $code,
            'desc' => errDesc($code),
            'type'=>2
        )
    );

    exit(json_encode($err));
}

$title = trim(urldecode($_POST['title']));
$reason = trim(urldecode($_POST['reason']));
if($title || $reason){
    $title = $db->realEscapeString($title);
    $reason = $db->realEscapeString($reason);

    $sql = "insert into admin_app_break_report(title, reason) VALUE ('$title', '$reason')";

    if($db->doSql($sql))
        succ3();

}else{
    error3(-1007);
}

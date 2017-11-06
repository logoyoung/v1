<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/5/11
 * Time: 下午6:04
 */

include_once INCLUDE_DIR."Anchor.class.php";

$isAnchor = true;
if(isset($_COOKIE['_uid']) && isset($_COOKIE['_enc']) && (int)$_COOKIE['_uid'] && trim($_COOKIE['_enc'])){
    $anchorHelp = new ANchorHelp($_COOKIE['_uid']);

    if($anchorHelp->checkStateError($_COOKIE['_enc'])){
        $isAnchor = false;
    }elseif( !( $anchorHelp->isAnchor() && ( !RN_MODEL || $anchorHelp->getRealNameCertifyInfo()['status'] == 101 ) ) ){
        $isAnchor = false;
    }
}else{
    $isAnchor = false;
}

if(!$isAnchor){
    include WEBSITE_TPL."error-404.php";
    exit;
}


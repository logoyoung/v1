<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 17/3/6
 * Time: 下午9:10
 */
require dirname(dirname(dirname(dirname(__FILE__))))."/core/common/init.php";
define('DB_MODEL',true);
if(!DB_MODEL){
    $GLOBALS['admin_db_env'] = "PRO";
}else{
    $GLOBALS['admin_db_env'] = "DEV";
}
<?php
require __DIR__.'/../../include/init.php';
$loader->addPrefix('dota',__DIR__.'/../../');
ini_set('display_errors', 0);

switch(get_current_env()) {

    case 'DEV':
    case 'PRE':
        error_reporting(E_ALL);
        break;

    case 'PRO':
    default :
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;
}
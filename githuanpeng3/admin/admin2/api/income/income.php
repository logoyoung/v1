<?php

/**
 * 获取经纪公司列表
 * yandong@6rooms.com
 * date 2017-01-22 16:43
 *
 */
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
require '../../lib/Anchor.class.php';
require '../../lib/BrokerageCompany.class.php';

$bcompany = new BrokerageCompany();
$list = $bcompany->getList();
$company = array();
foreach($list as $k=>$v) {
    $company[$v['id']] = $v['name'];
}

$anchorobj = new Anchor();
$res = $anchorobj->searchList();

if ($res['list']) {
    foreach($res['list'] as $k=>$v) {
        $res['list'][$k]['company'] = isset($company[$v['cid']]) ? $company[$v['cid']] : 0;
    }
    succ($res);
} else {
    error(-1014);
}
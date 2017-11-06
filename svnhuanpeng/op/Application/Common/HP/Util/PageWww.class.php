<?php
// +----------------------------------------------------------------------
// 兼容bootcss分页
// +----------------------------------------------------------------------
namespace HP\Util;

class PageWww extends Page{
    public $rollPage   = 11;// 分页栏每页显示的页数
    public $lastSuffix = false; // 最后一页是否显示总页数

    protected $p       = 'page'; //分页参数名
    protected $url     = ''; //当前链接URL
    protected $nowPage = 1;

    // 分页显示定制
    protected $config  = array(
        'header' => '<li class="rows">第%NOW_PAGE%页,共%TOTAL_PAGE%页</li>',
        'prev'   => '上一页',
        'next'   => '下一页',
        'first'  => '首页',
        'last'   => '末页',
        'theme'  => '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    );
}

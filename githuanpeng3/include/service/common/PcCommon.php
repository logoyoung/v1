<?php

namespace service\common;

use service\common\PcHeader;

/**
 * 基础类库
 * @author longgang@6.cn
 * @date 2017-04-14 11:10:23
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */
class PcCommon
{

    public $smarty;

    //推荐游戏ID

    public function __construct()
    {
        return $this->_init();
    }

    private function _init()
    {
        include INCLUDE_DIR . "smartySDK/Smarty.class.php";
        $this->smarty = new \Smarty();

        if ($GLOBALS['env'] == "PRO")
        {
            $this->smarty->debugging = FALSE;
            $this->smarty->caching = FALSE; //TRUE;
            $this->smarty->cache_lifetime = 0; //120;
        } elseif ($GLOBALS['env'] == 'PRE')
        {
            $this->smarty->debugging = FALSE;
            $this->smarty->caching = FALSE;
            $this->smarty->cache_lifetime = 0;
        } else
        {
            $this->smarty->debugging = FALSE;
            $this->smarty->caching = FALSE;
            $this->smarty->cache_lifetime = 0;
        }

        $headerSerice = new PcHeader();
        $header = $headerSerice->getHeaderData();
        $this->smarty->assign('header', $header);
    }

}

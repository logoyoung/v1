<?php
include '../include/init.php';

use service\common\PcCommon;
use service\game\GameService;

/**
 * 游戏分类
 * @author longgang@6.cn
 * @date 2017-04-13 17:19:25
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */

class Game extends PcCommon
{

    private function _getData()
    {
        $service = $this->getService();
        return $service->getAllGameList();
    }

    public function display()
    {
        
        $data = $this->_getData();
        if(!$data) {
            // do log
        }
        
        $result = ['status' => 0,'content' => ['list' => $data]];

        $this->smarty->assign('isLogin','1');
        $this->smarty->assign('content', xss_clean($result));
        $this->smarty->assign('headSign','game');
        
        $this->smarty->display('game.tpl');

    }

    public function getService()
    {
        return new GameService();
    }

}

$game = new Game();
$game ->display();
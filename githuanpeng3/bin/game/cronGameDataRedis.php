<?php

ini_set('memory_limit', '1024M');
require_once __DIR__ . '/../../include/init.php';

use lib\game\Game;
use system\Timer;
use service\game\helper\GameRedis;

/**
 *  将游戏信息存入redis
 * @date 2017-07-17 13:22:26
 * @author longgang <longgang@6.cn>
 * @copyright (c) 2017, longgang
 * @version 1.0.0
 */
class cronGameDataRedis
{
    private $_logName = 'cron_game_data_redis';

    public function run()
    {
        $this->log('start|游戏信息种入缓存开始');
        $timer = new Timer();
        $timer->start();
        $gameDao = new Game();
        $totalNum = $gameDao->getGameTotalNum();
        if ($totalNum == 0)
        {
            $this->log('error|从数据库获取游戏总数异常');
            die(-1);
        }

        $this->log("notice|共计:{$totalNum} 个游戏");

        $gameRedis = new GameRedis();

        $gameData = $gameDao->getGameList();

        if (!$gameData)
        {
            $this->log("error|从game表里获取gameData异常,停止脚本");
            return false;
        }

        $gameIds = array_unique(array_column($gameData, 'gameid'));

        if (!$gameIds)
        {
            $this->log("error|从game表里获取gameID字段异常,停止脚本");
            return false;
        }

        $gameRedis->setAllGameIdsData($gameIds);

        $this->log("notice|allgameid存入缓存");

        $gameList = [];
        foreach ($gameData as $game)
        {
            $gameList[$game['gameid']] = hp_json_encode($game);
        }
        
        $gameRedis->clearAllGameListData();
        $gameRedis->setAllGameListData($gameList);

        $this->log("notice|allgamelist存入缓存");
        
        $timer->end();
        $t = $timer->getTime();
        $this->log("end |success| 脚本执行完成; 耗时:{$t}s; 游戏总数:{$totalNum} 条;");

        return true;
    }

    public function log($msg)
    {
        return write_log($msg, $this->_logName);
    }

}

$obj = new cronGameDataRedis();

$obj->run();

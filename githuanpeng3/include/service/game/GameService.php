<?php

namespace service\game;

use service\common\AbstractService;
use lib\Game;
use service\game\helper\GameRedis;

/**
 * 首页服务类
 * @author longgang@6.cn
 * @date 2017-04-13 17:19:25
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */
class GameService extends AbstractService
{

    //获取所有游戏失败
    const ERROR_ALL_GAME = -720001;
    //获取推荐游戏失败
    const ERROR_RECOMMEND_GAME = -720002;
    //默认排序字段
    const DEFAULT_ORDER = 'ord';
    const VIDEO_TYPE_HOT = 0; //最热录像
    const VIDEO_TYPE_NEW = 1; //最新录像
    const VIDEO_TYPE_FOLLOW = 2; //最多关注录像
    //游戏直播列表缓存前缀
    const GAME_LIVE_LIST = 'hp_game_live_list';
    //首页推荐游戏列表前缀
    const RECOMMEND_GAME_LIST = 'hp_recommend_game_list_index';
    //所有游戏列表前缀
    const ALL_GAME_LIST = 'all_game_list';

    public static $errorMsg = [
        self::ERROR_ALL_GAME => '获取所有游戏失败',
        self::ERROR_RECOMMEND_GAME => '获取推荐游戏失败',
    ];
    //游戏ID
    private $_gameId;
    //显示数量
    private $_size;
    //需求字段
    public $column;
    //排序字段
    public $order;
    private $_fromDb;

    public function setGameId($gameId)
    {
        $this->_gameId = $gameId;
        return $this;
    }

    //游戏字段
    public function setColumn($column)
    {
        $this->column = $column;
        return $this;
    }

    public function setSize($size)
    {
        $this->_size = $size;
        return $this;
    }

    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    public function getOrder()
    {
        return $this->order ? $this->order : 'liveCount';
    }

    public function getGameId()
    {
        return $this->_gameId;
    }

    public function setFromDb($fromDb = true)
    {
        $this->_fromDb = $fromDb;
        return $this;
    }

    public function getFromDb()
    {
        return $this->_fromDb;
    }

    /**
     * 获取所有游戏列表
     * @return array
     */
    public function getAllGameList()
    {
        $gameRedis = new GameRedis();

        $res = $gameRedis->getAllGameListData();
        
        $gameLiveCount = $gameRedis->getGameLiveCount();

        $data = [];
        if ($res && ($res = array_filter($res)))
        {
            foreach ($res as $k => $v)
            {
                $v = json_decode($v, true);
                $data[$k] = $v;
                $data[$k]['liveCount'] = isset($gameLiveCount[$v['gameid']]) ? $gameLiveCount[$v['gameid']] : 0;
            }
        }

        if (!$data)
        {

            $gameObj = new Game();

            $data = $gameObj->getGameList();
        }

        if (!$data)
        {
            $code = self::ERROR_ALL_GAME;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return [];
        }

        $data = twoKeyOrder($data, $this->getOrder(), SORT_DESC, self::DEFAULT_ORDER);

        if ($this->column)
        {
            if (is_array($this->column))
            {
                $temps = [];
                foreach ($data as $v)
                {
                    $temp = [];
                    foreach ($this->column as $key => $column)
                    {
                        $temp[$key] = $v[$column];
                    }
                    $temps[] = $temp;
                }
                $data = $temps;
            } else
            {
                $data = array_column($data, $this->column);
            }
        }

        if ($this->_size)
        {
            $data = array_slice($data, 0, $this->_size);
        }

        return $data;
    }

    //获取首页游戏分类推荐游戏列表
    public function getRecommendGameList()
    {
        $gameRedis = new GameRedis();
        $gameIds = $gameRedis->getRecommendGameList();
        $datas = [];

        $gameLiveCount = $gameRedis->getGameLiveCount();

        if ($gameIds)
        {
            $gameIds = explode(',', $gameIds);
            foreach ($gameIds as $gameId)
            {
                $data = [];
                $data['gameid'] = $gameId;

                $res = $gameRedis->getGameListDataByGameId($gameId);

                if ($res && ($res = array_filter($res)))
                {
                    $gameInfo = json_decode($res[$gameId], true);
                    $data['name'] = isset($gameInfo['name']) ? $gameInfo['name'] : '';
                    $data['poster'] = isset($gameInfo['poster']) ? $gameInfo['poster'] : '';
                }

                $data['liveCount'] = isset($gameLiveCount[$gameId]) ? $gameLiveCount[$gameId] : 0;
                $datas['list'][] = $data;
            }
            $datas['total'] = $gameRedis->getGameCount();
        }

        if (!$datas)
        {
            $gameObj = new Game();

            $datas = $gameObj->getRecommendGame();
        }

        if (!$datas)
        {
            $code = self::ERROR_RECOMMEND_GAME;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return [];
        }

        return $datas;
    }

    public function getGameInfoById($gameidsArray, $db = null)
    {
        $gameObj = new Game();

        if (!$db)
        {
            $db = Game::getDB();
        }

        return $gameObj->getGameInfo($gameidsArray, $db);
    }

    //根据gameID获取gamename
    public function getGameTypeNameByGameId()
    {
        $gameObj = new Game();
        return $gameObj->getGameTypeName($this->getGameId(), Game::getDB());
    }

    //获取所有gameid
    public function getGameIds()
    {
        $gameObj = new Game();
        return $gameObj->getGameIds(Game::getDB());
    }

    /**
     * 根据传入的参数获取对应的录像分页列表信息
     *
     * @param int    $gameId
     * @param int    $page
     * @param int    $size
     * @param int    $type
     * @param array  $conf
     *
     * @return array
     */
    private function makeVideoListsByTypeOrGameId($gameId, $page, $size, $type, $conf)
    {
        $finallyLiveLists = [];
        if ($type == self::VIDEO_TYPE_HOT)
        {
            $order = 'viewcount'; //播放数
        }
        if ($type == self::VIDEO_TYPE_NEW)
        {
            $order = 'ctime'; //时间
        }
        if ($type == self::VIDEO_TYPE_FOLLOW)
        {
            $order = 'videoFollow'; //视频收藏
        }

        $res = $this->getVideoByGameId($gameId);
        if ($res)
        {
            if ($type == self::VIDEO_TYPE_FOLLOW)
            {//按获取视频收藏
                $ids = array_column($res, 'videoid');
                $vfollow = getVideoCountByVideoId(implode(',', $ids), $db); //TODO
                for ($i = 0, $k = count($res); $i < $k; $i++)
                {
                    if (array_key_exists($res[$i]['videoid'], $vfollow))
                    {
                        $res[$i]['videoFollow'] = $vfollow[$res[$i]['videoid']];
                    } else
                    {
                        $res[$i]['videoFollow'] = 0;
                    }
                }
            }
            $afterSort = dyadicArray($res, $order);
        } else
        {
            $afterSort = [];
        }

//      $page = returnPage(count($afterSort), $size, $page);
        $offset = ( $page - 1 ) * $size;
        $afterCut = array_slice($afterSort, $offset, $size); //以后加缓存
        if ($afterCut)
        {
            $vids = array_column($afterCut, 'videoid');
            $comment = getVideoCommentCountByVideoId($vids, $db);
        }
        if ($afterCut)
        {
            $huanVlist = explode(',', HUANPENG_VIDEO);
            foreach ($afterCut as $v)
            {
                $finallyLive['videoID'] = $v['videoid'];
                if ($v['poster'])
                {
                    if (in_array($v['videoid'], $huanVlist))
                    {
                        $finallyLive['poster'] = DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $v['poster'];
                    } else
                    {
                        $finallyLive['poster'] = sposter($v['poster']);
                    }
                    $finallyLive['ispic'] = '1';
                } else
                {
                    $finallyLive['poster'] = CROSS;
                    $finallyLive['ispic'] = '0';
                }
                $finallyLive['title'] = $v['title'];
                $finallyLive['orientation'] = $v['orientation'];
                $finallyLive['gameName'] = $v['gamename'];
                $finallyLive['videoTimeLength'] = $v['length'];
                $finallyLive['commentCount'] = array_key_exists($v['videoid'], $comment) ? $comment[$v['videoid']] : '0';
                if ($type == self::VIDEO_TYPE_FOLLOW)
                {
                    $finallyLive['viewCount'] = $v['videoFollow'];
                } else
                {
                    $finallyLive['viewCount'] = $v['viewcount'];
                }
//            $finallyLive['giftCount'] = 0;
                array_push($finallyLiveLists, $finallyLive);
            }
        } else
        {
            $finallyLiveLists = [];
        }
        return $finallyLists = array('list' => $finallyLiveLists, 'count' => count($afterSort));
    }

    public function getVideoListsByGameId($type, $page, $size, $gameId = 0)
    {
        if (!in_array($type, array(self::VIDEO_TYPE_HOT, self::VIDEO_TYPE_NEW, self::VIDEO_TYPE_FOLLOW)))
        {
            return false;
        }
        $result = $this->makeVideoListsByTypeOrGameId($gameId, $page, $size, $type, $this->_db, $this->getConf(), $this->redisObj);
        if ($gameId)
        {
            if ($gameId == OTHER_GAME)
            {
                $ref = '其他视频';
            } else
            {
                $gameObj = new Game();
                $game = $gameObj->getGameInfoByGameId($gameId);
                $ref = $game['name'];
            }
        } else
        {
            $ref = '全部视频';
        }
        if ($result)
        {
            return array('list' => $result['list'], 'ref' => $ref, 'total' => $result['count']);
        } else
        {
            return array('list' => $result['list'], 'ref' => $ref, 'total' => $result['count']);
        }
    }

    public static function getGameIdByGameName($gameName)
    {
        $gameObj = new Game();
        $res = $gameObj::getGameIdAndTypeByName($gameName, Game::getDB());
        if (isset($res['gameid']))
        {
            return $res['gameid'];
        } else
        {
            return FALSE;
        }
    }

    private function getConf()
    {
        return $GLOBALS['env-def'][$GLOBALS['env']];
    }

}

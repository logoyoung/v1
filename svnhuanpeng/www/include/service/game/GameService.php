<?php

namespace service\game;

use service\common\AbstractService;
use lib\Game;
use service\live\LiveService;
use service\user\FollowService;
use service\user\UserDataService;
use service\room\RoomManagerService;
use service\room\LiveRoomService;
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
    const LIVE_TYPE_HOT = 1; //最热直播
    const LIVE_TYPE_NEW = 2; //最新直播
    const LIVE_TYPE_FOLLOW = 3; //最多关注直播
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
        if($res)
        {
            foreach ($res as $k => $v)
            {
                $v = json_decode($v,true);
                $data[$k] = $v;
                $data[$k]['liveCount'] = isset($gameLiveCount[$v['gameid']]) ? $gameLiveCount[$v['gameid']] : 0;
            }
        }
        
        if(!$data)
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
        
        if($gameIds)
        {
            $gameIds = explode(',', $gameIds);
            foreach ($gameIds as $gameId)
            {
                $data = [];
                $data['gameid'] = $gameId;
                
                $res = $gameRedis->getGameListDataByGameId($gameId);
                if($res)
                {
                    $gameInfo = json_decode($res[$gameId],true);                   
                    $data['name']   = isset($gameInfo['name']) ? $gameInfo['name'] : '';
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
        
        if(!$db)
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


    private function DataLists($gameId, $conf, $redisObj)
    {
        $gamelist = $luides = array();

        $liveService = new LiveService();
        $liveService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $liveService->setGameId($gameId);
        $liveLists = $liveService->getLiveListsByGameId();
        
        if ($liveLists)
        {
            $luides = array_unique(array_column($liveLists, 'uid'));

            $followService = new FollowService();
            $followService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $fansCount = $followService->getUserCount($luides);

            $userDataService = new UserDataService();
            $userDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $userDataService->setUid($luides);
            $autherInfo = $userDataService->batchGetUserInfo();

            $roomManagerService = new RoomManagerService();
            $roomManagerService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $roomManagerService->setUid($luides);
            $room = $roomManagerService->getRoomIdsByUids();

            $liveRoomService = new LiveRoomService();
            $liveRoomService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $liveRoomService->setLuid($luides);
            $viewCount = $liveRoomService->batchGetLiveUserCountFictitious();

            foreach ($liveLists as $k => $v)
            {
                $list['uid'] = $v['uid'];
                $list['head'] = array_key_exists($v['uid'], $autherInfo) ? $autherInfo[$v['uid']]['pic'] : '';
                $list['roomID'] = array_key_exists($v['uid'], $room) ? $room[$v['uid']] : 0;
                $list['gameName'] = $v['gamename'];
                $list['nick'] = array_key_exists($v['uid'], $autherInfo) ? $autherInfo[$v['uid']]['nick'] : '';
                $list['title'] = $v['title'];
                $list['stime'] = strtotime($v['ctime']);
                $list['orientation'] = $v['orientation'];
                if ($v['poster'])
                {
                    $list['poster'] = $conf['domain-lposter'] . "/" . $v['poster'];
                    $list['ispic'] = '1';
                } else
                {
                    $list['poster'] = CROSS;
                    $list['ispic'] = '0';
                }
                $list['viewCount'] = isset($viewCount[$v['uid']]) ? $viewCount[$v['uid']] : 0;
                $list['fansCount'] = array_key_exists($v['uid'], $fansCount) ? $fansCount[$v['uid']] : 0;
                array_push($gamelist, $list);
            }
        }
        return $gamelist;
    }

    /**
     *  组合最热、最新、最多关注直播数据
     *
     * @param int    $gameId   游戏id
     * @param int    $size     数量
     * @param int    $page     页数
     * @param int    $type     类型
     * @param array  $conf     配置数组
     * @param object $redisObj redis对象
     *
     * @return array|bool
     */
    private function makeLiveListsByTypeOrGameId($gameId, $size, $page, $type, $conf, $redisObj)
    {
        if (!in_array($type, array(self::LIVE_TYPE_NEW, self::LIVE_TYPE_HOT, self::LIVE_TYPE_FOLLOW, self::LIVE_TYPE_FOLLOW)))
        {
            return false;
        }
        if ($type == self::LIVE_TYPE_HOT)
        {
            $sort = 'viewCount';
            $tow_sort = 'stime';
        }
        if ($type == self::LIVE_TYPE_NEW)
        {
            $sort = 'stime';
            $tow_sort = 'viewCount';
        }
        if ($type == self::LIVE_TYPE_FOLLOW)
        {
            $sort = 'fansCount';
            $tow_sort = 'stime';
        }
//		$cacheKey = "HuanPeng_HomePageGameOrderListBy".$gameId.$size.$page.$sort; //定义一个缓存的键名
        $getCatch = '';
        if ($getCatch)
        {
            $afterSort = json_decode($getCatch, true);
        } else
        {
            $res = $this->DataLists($gameId, $conf, $redisObj);
            if ($res)
            {
                $afterSort = multiArraySort($res, $sort, $tow_sort);
//                	$redisObj->set( $cacheKey, jsone( $afterSort ), 100 ); //加入缓存,第三个参数为缓存时间70s
            } else
            {
                $afterSort = [];
            }
        }
        if ($gameId)
        {
            if ((int) $gameId == 0)
            {
                $afterSortLength = count($afterSort);
            } else
            {
                $temp = Game:: getLiveCountByGid(array($gameId), Game::getDB());
                $afterSortLength = isset($temp[$gameId]) ? $temp[$gameId] : 0;
            }
        } else
        {
            $afterSortLength = count($afterSort);
        }
//        $page = returnPage($afterSortLength, $size, $page);
        $offect = ( $page - 1 ) * $size;
        $finallyLiveLists = array_slice($afterSort, $offect, $size);
        return array('list' => $finallyLiveLists, 'count' => $afterSortLength);
    }

    /**
     * 获取直播大厅&首页楼层列表
     *
     * @param  int $type   0最热 1最新 2最多关注
     * @param  int $page   页数
     * @param  int $size   数量
     * @param  int $gameId 游戏id
     *
     * @return array|bool
     */
    public function getLiveListByGameId($type, $page, $size, $gameId = 0)
    {
        if (!in_array($type, array(self::LIVE_TYPE_NEW, self::LIVE_TYPE_HOT, self::LIVE_TYPE_FOLLOW)))
        {
            return false;
        }
        $result = $this->makeLiveListsByTypeOrGameId($gameId, $size, $page, $type, $this->getConf(), Game::getRedis());
        if ($gameId)
        {
            if ($gameId == OTHER_GAME)
            {
                $ref = '其他游戏';
            } else
            {
                if (empty($result['count']))
                {
                    $gameObj = new Game();
                    $game = $gameObj->getGameInfoByGameId($gameId);
                    $ref = $game['name'];
                } else
                {
                    $ref = $result['list'][0]['gameName'];
                }
            }
        } else
        {
            $ref = '全部直播';
        }
        if ($result['list'])
        {
            return array('list' => $result['list'], 'ref' => $ref, 'total' => $result['count']);
        } else
        {
            return array('list' => [], 'ref' => $ref, 'total' => "0");
        }
    }

    /**
     * 根据传入的参数获取对应的录像分页列表信息
     *
     * @param int    $gameId
     * @param int    $size
     * @param int    $page
     * @param int    $type
     * @param array  $conf
     *
     * @return array
     */
    private function makeVideoListsByTypeOrGameId($gameId, $page, $size, $type, $conf, $redisObj)
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
//		$cacheKey = "HuanPeng_getVideoPageListBy$gameId$page$size".$order; //定义一个缓存的键名
//		$getCatch = $redisObj->get( $cacheKey );
        $getCatch = '';
        if ($getCatch)
        {
            $afterSort = json_decode($getCatch, true);
        } else
        {
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
//		$redisObj->set( $cacheKey, json_encode( $afterSort ), 60 ); //加入缓存,第三个参数为缓存时间60s
            } else
            {
                $afterSort = [];
            }
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
        if(isset($res['gameid']))
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

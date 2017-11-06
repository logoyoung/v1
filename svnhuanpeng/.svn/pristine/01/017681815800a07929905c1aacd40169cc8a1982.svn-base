<?php
namespace service\game;

/**
 * 首页服务类
 * @author longgang@6.cn
 * @date 2017-04-13 17:19:25
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */

use lib\Game;

class GameZoneService
{
    
    //游戏ID
    private $_gameId;
    //显示数量
    private $_size;
    
    //需求字段
    public $column;
    //排序字段
    public $order;
    
    //底层数据服务
    public $gameDataService;
    
    public function __construct()
    {
        $this->gameDataService = new Game();
    }

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
        $this->order= $order;
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

    /**
     * 获取所有游戏列表
     * @return array
     */
    public function getAllGameList()
    {
        $data = $this->gameDataService->getGameList();
        
        if(!$data)
        {
            $code   = self::ERROR_ALL_GAME;
            $msg    = self::$errorMsg[$code];
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }
        
        $data = dyadicArray($data, $this->getOrder());

        if($this->column)
        {
            if(is_array($this->column))
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
                $data = array_column($data,$this->column);
            }
        }
        
        if($this->_size)
        {
            $data = array_slice($data, 0, $this->_size);
        }
        
        return $data;
    }

    //获取首页游戏分类推荐游戏列表
    public function getRecommendGameList()
    {
        $data = $this->gameDataService->getRecommendGame();
        
        if(!$data)
        {
            $code   = self::ERROR_RECOMMEND_GAME;
            $msg    = self::$errorMsg[$code];
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }
        
        if(isset($data['list']))
        {
            $data['list'] = dyadicArray($data['list'], $this->getOrder());
        }
        return $data;
    }


}
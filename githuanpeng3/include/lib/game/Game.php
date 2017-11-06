<?php

namespace lib\game;

use Exception;
use system\DbHelper;

class Game
{

    const DB_CONF = 'huanpeng';
    //上架游戏
    const STATUS_01 = 0;
    //下架游戏
    const STATUS_02 = 1;
    
    public static $fields = [
        'gameid', //int(10) unsigned NOT NULL AUTO_INCREMENT,
        'gametid', //int(10) unsigned NOT NULL DEFAULT '0',
        'name', //varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
        'icon', //varchar(100) NOT NULL DEFAULT '',
        'poster', //varchar(100) NOT NULL DEFAULT '',
        'ord', //int(10) unsigned NOT NULL,
        'ctime', //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        'status', //tinyint(3) unsigned NOT NULL DEFAULT '0',
        'direction', //tinyint(3) unsigned NOT NULL DEFAULT '1',
        'gamepic', //blob COMMENT '游戏图片',
        'bgpic', //varchar(100) NOT NULL DEFAULT '' COMMENT '背景图片',
        'description', //blob NOT NULL COMMENT '游戏描述',
        'scheme', //varchar(100) NOT NULL DEFAULT '',
    ];
    public static $unUpdateFields = ['gameid', 'ctime'];
    private $_master = false;

    public function getGameData(array $fields = [])
    {
        if ($fields)
        {
            $fields[] = 'gameid';
            $fields = array_unique($fields);
        } else
        {
            $fields = self::$fields;
        }

        $db = $this->getDb();
        $fields = $db->buildFieldsParam($fields);
        $sql = "SELECT {$fields} FROM '{$this->getTable()}' WHERE `status`=" . self::STATUS_01;

        try
        {

            return $db->query($sql);            
        } catch (Exception $e)
        {
            return false;
        }
    }
    
    public function getGameInfoByGameId($gameId,array $fields = [])
    {
        if(!$gameId)
        {
            return false;
        }
        $gameId = (array) $gameId;
        $num = count($gameId);
        if($fields)
        {
            $fields[] = 'gameid';
            $fields   = array_unique($fields);
        } else {
            $fields = self::$fields;
        }

        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam($fields);
        $inStr   = $db->buildInPrepare($gameId);
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `gameid` IN($inStr) LIMIT {$num}";

        try {

            $result = $db->query($sql,$gameId);
            if(!$result)
            {
                return $result;
            }

            $data = [];
            foreach ($result as $v)
            {
                $data[$v['gameid']] = $v;
            }

            return $data;

        } catch (Exception $e) {
            return false;
        }
    }

    public function getGameInfoByName($name,array $fields = [])
    {
        if(!$name)
        {
            return false;
        }
        $name = (array) $name;
        $num = count($name);
        if($fields)
        {
            $fields[] = 'gameid';
            $fields   = array_unique($fields);
        } else {
            $fields = self::$fields;
        }

        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam($fields);
        $inStr   = $db->buildInPrepare($name);
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `name` IN($inStr) LIMIT {$num}";

        try {

            $result = $db->query($sql,$name);
            if(!$result)
            {
                return $result;
            }

            $data = [];
            foreach ($result as $v)
            {
                $data[$v['name']] = $v;
            }

            return $data;

        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * 供校验脚本使用
     * @return init
     */
    public function getGameTotalNum()
    {
        $sql = "SELECT COUNT(*) AS total_num FROM `{$this->getTable()}` WHERE `status`=" . self::STATUS_01;

        try
        {
            $db = $this->getDb();
            $result = $db->query($sql);
            if (!$result)
            {
                return 0;
            }

            return (int) $result[0]['total_num'];
        } catch (Exception $e)
        {
            return 0;
        }
    }

    /**
     * 供校验脚本使用
     * @param  [type] $page   [description]
     * @param  [type] $size   [description]
     * @param  array  $fields [description]
     * @return [type]         [description]
     */
    public function getGameList($page = 1, $size = 1000, array $fields = [])
    {
        if ($fields)
        {
            $fields[] = 'gameid';
            $fields = array_unique($fields);
        } else
        {
            $fields = self::$fields;
        }
        $db = $this->getDb();
        $fields = $db->buildFieldsParam($fields);
        $page = (int) $page;
        $size = $size > 1000 ? 1000 : $size;
        $offset = ($page - 1) * $size;
        $bdParam = ['offset' => $offset, 'size' => $size];
        $sql = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `status`=" . self::STATUS_01 . " ORDER BY `gameid` ASC LIMIT :offset,:size";

        try
        {
            $res = $db->query($sql, $bdParam);
            $list = [];
            $conf = $this->getConf();
            
            if($res)
            {
                foreach ($res as $k=>$v)
                {
                    $res[$k]['icon'] = $v['icon'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $v['icon'] : '';;
                    $res[$k]['poster'] = $v['poster'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $v['poster'] : '';
                    $res[$k]['bgpic'] = $v['bgpic'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $v['bgpic'] : '';
                    
                }
            }
            return $res;
            
        } catch (Exception $e)
        {
            return false;
        }
    }

    public function getTable()
    {
        return 'game';
    }

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function getConf()
    {
        return $GLOBALS['env-def'][$GLOBALS['env']];
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/5
 * Time: 15:26
 */

namespace lib\due;
use system\DbHelper;

/**
 * 约玩设置表类
 * Class DueSkill
 * @package lib\due
 */
class DueSkill
{
    //db 配置文件的key
    public static $dbConfName = 'huanpeng';
    private $uid = null;
    private $_db = null;
    public  $param = [];
    //定义新增技能默认展示开关 -1 不展示 1展示
    const ADD_SKILL_SWITCH = '-1';

    /**
     * 初始化类
     * @param $uid
     * @param string $db
     */
    public function __construct( $uid, $db = '' )
    {
        if( $uid )
        {
            $this->uid = (int)$uid;
        }
        if( $db )
        {
            $this->_db = $db;
        }
        else
        {
            $this->_db = DbHelper::getInstance(self::$dbConfName);
        }
        return true;
    }
    /**
     * 定义表名
     * @return string
     */
    public function tableName()
    {
        return 'due_skill';
    }
    /**
     * 查询主播所有技能
     * @return array|bool
     */
    public function getAllSkill()
    {
        $table = $this->tableName();
        //查询主播约玩资质
        $sql   = "SELECT `id` as skillId,  `uid`,  `cert_id`,  `game_id`,  `tag_ids`,  `price`,  `unit`,  `stime`,  `etime`,  `service_day` ,`switch` ,`ctime` ,`utime`,`avg_score`,`total_score`,`comment_num` FROM `{$table}` WHERE `uid` = :uid";
        //参数绑定
        $bdParam = [
            'uid' =>  $this->uid,
        ];

        try {

            return  $this->_db->query($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 查询主播技能按skillId
     * @param $data @技能 $data['skillId']
     * @param $order 默认不进行排序；可以直接传递排序规则
     * @return bool|\PDOStatement
     */
    public function getSkillBySkillId($data,$order='')
    { 
        $skillId = $data['skillId'];
        $table = $this->tableName();
        if(is_array($skillId))
        {
            //绑定占位符
            $in    = $this->_db->buildInPrepare($skillId); 
            //查询主播约玩资质
            $sql   = "SELECT `id` as skillId,  `uid`,  `cert_id`,  `game_id`,  `tag_ids`,  `price`,  `unit` ,  `stime`,  `etime`,  `service_day` ,`switch` ,`ctime` ,`utime`,`avg_score`,`total_score`,`comment_num`  FROM `{$table}` WHERE `id` in ({$in}) $order";
            $bdParam = $skillId;
        }else
        {

            //查询主播约玩资质
            $sql   = "SELECT `id` as skillId,  `uid`,  `cert_id`,  `game_id`,  `tag_ids`,  `price`,  `unit`,  `stime`,  `etime`,  `service_day` ,`switch` ,`ctime` ,`utime`,`avg_score`,`total_score`,`comment_num`  FROM `{$table}` WHERE `id` = :skillId  ORDER BY id limit 1";
            //参数绑定
            $bdParam = [
                'skillId' => $skillId,
            ];
        }

        try { 
            return  $this->_db->query($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 查询技能按游戏id
     * @param $data
     * @return bool|\PDOStatement
     */
    public function getSkillBygameId($data)
    {
        $gameId = $data['gameId'];
        $table = $this->tableName();
        //查询主播约玩资质
         $sql   = "SELECT `id` as skillId,  `uid`,  `cert_id`,  `game_id`,  `tag_ids`,  `price`,  `unit`,  `stime`,  `etime`,  `service_day` ,`switch` ,`ctime` ,`utime`,`avg_score`,`total_score`,`comment_num`  FROM `{$table}` WHERE `game_id` = :gameId and uid =:uid  limit 1";
        //参数绑定
        $bdParam = [
                     'uid'=>$this->uid,
                'gameId' => $gameId,
            ];

        try {
            return  $this->_db->query($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * 增加主播技能
     * @return bool
     */
    public function addSkill($data)
    {
        $this->param = $data;
        $table = $this->tableName();
        $sql   = "INSERT INTO `{$table}`( `uid`,  `cert_id`,  `game_id`,  `tag_ids`,  `price`,  `unit`,  `stime`,  `etime`,  `service_day` ,`switch`) VALUES(:uid,:cert_id,:game_id,:tag_ids,:price,:unit,:stime,:etime,:service_day,:switch)";
        //参数绑定
        $bdParam = [
            'uid'           => $this->uid,
            'cert_id'       => $this->param['certId'],
            'game_id'       => $this->param['gameId'],
            'tag_ids'       => isset($this->param['tag_ids'])?$this->param['tag_ids']:'',
            'price'         => $this->param['price'],
            'unit'          => $this->param['unit'],
            'stime'         => isset($this->param['stime'])?$this->param['stime']:'',
            'etime'         => isset($this->param['etime'])?$this->param['etime']:'',
            'service_day'   => isset($this->param['service_day'])?$this->param['service_day']:'',
            'switch'        => isset($this->param['switch'])?$this->param['switch']:self::ADD_SKILL_SWITCH,
        ];
        try {

            $result = $this->_db->execute($sql,$bdParam);
            return $result;

        } catch (Exception $e) {

            return false;
        }
    }
    /**
     * 修改主播技能
     * @param $data
     * @return bool
     */
    public function updateSkillBySkillId($data)
    {
        $this->param = $data;
        $table = $this->tableName();
        $sql   = "UPDATE `{$table}` SET  `price` = :price,  `unit` = :unit,  `stime` = :stime,  `etime` = :etime,  `service_day` = :service_day ,`switch`=:switch, `utime` = :utime WHERE `id` = :id and `uid` = :uid LIMIT 1";
        //参数绑定
        $bdParam = [
            'id'            => isset($this->param['skillId']) ? $this->param['skillId'] : '',
            'uid'           => $this->uid,
           // 'tag_ids'       => isset($this->param['tagIds']) ? $this->param['tagIds'] : '',
            'price'         => isset($this->param['price']) ? $this->param['price'] : '',
            'unit'          => isset($this->param['unit']) ? $this->param['unit'] : '',
            'stime'         => isset($this->param['stime']) ? $this->param['stime'] : '',
            'etime'         => isset($this->param['etime']) ? $this->param['etime'] : '',
            'service_day'   => isset($this->param['serviceDay']) ? $this->param['serviceDay'] : '',
            'switch'        => isset( $this->param['switch'])?$this->param['switch']:'-1',
            'utime'         => date('Y-m-d H:i:s',time()),
        ];
        try {

            return $this->_db->execute($sql,$bdParam);

        } catch (Exception $e) {

            return false;
        }
    }
    public function updateSwitchBySkillId($data)
    {
        $this->param = $data;
        $table = $this->tableName();
        $sql   = "UPDATE `{$table}` SET  `switch` = :switch WHERE `id` = :skillId and `uid` = :uid LIMIT 1";
        //参数绑定
        $bdParam = [
            'skillId'   => $this->param['skillId'],
            'uid'       => $this->uid,
            'switch'    =>  $this->param['switch'],
        ];
        try {

            return $this->_db->execute($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * 更新开关项按游戏id
     * @param $data
     * @return bool
     */
    public function updateSwitchByGameId($data)
    {
        $this->param = $data;
        $table = $this->tableName();
        $sql   = "UPDATE `{$table}` SET  `switch` = :switch WHERE `game_id` = :gameId and `uid` = :uid LIMIT 1";
        //参数绑定
        $bdParam = [
            'gameId'   => $this->param['gameId'],
            'uid'       => $this->uid,
            'switch'    =>  $this->param['switch'],
        ];
        try {

            return $this->_db->execute($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }

    }

}
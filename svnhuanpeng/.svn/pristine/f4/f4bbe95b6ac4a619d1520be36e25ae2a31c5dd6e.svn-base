<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017/6/8
 * Time: 11:26
 * Desc: 陪玩 底层支持
 */
namespace lib\due;

use system\DbHelper;

class DueRecommend
{
    // 数据表前缀
    public static $dbConfName = 'huanpeng';
    // 静态存储数据库链接
    private static $dblink;

    private $size;

    private $pageTotal;

    private $orderby;

    const SIZE_NUM = 4;


    const TABLE_NAME_02 = 'due_skill';

    public function __construct($db = '')
    {
        if (empty(self::$dblink))
            self::$dblink = DbHelper::getInstance(self::$dbConfName);
    }

    /**
     * 设置单页展示记录数
     * -------------
     * 
     * @param int $size            
     */
    public function setSize($size = '')
    {
        $this->size = $size ? $size : self::SIZE_NUM;
    }

    /**
     * 设置查询排序规则
     * ------------
     * 
     * @param string $orderby            
     */
    public function setOrderBy($orderby = '')
    {
        if ($orderby != '') {
            switch ($orderby) {
                case 1:
                    $this->orderby = 'order by utime desc';
                    break;
                default:
                    $this->orderby = '';
            }
        }
    }

    /**
     * 推荐列表
     * ------
     * @return multitype:number
     */
    public function getRecommend()
    {
        $table_02 = self::TABLE_NAME_02;
        $sql = "select id as skillId,uid from `{$table_02}` where status=1 and switch=1";
        $result = self::$dblink->query($sql);
        return $result;
    }
}

?>
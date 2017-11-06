<?php
namespace service\rank;
use lib\Rank;

/**
 *  排行服务类
 *
 *  @author xuyong
 *  @date    2017-4-15 17:40
 *  @version 1.01
 *
 *   使用方法
 *     $service = new \service\rank\RankService();
 *     $service->setUserType($userType);
 *     $service->setTimeType($timeType);
 *     $service->setOrderType($orderType);
 *     $service->setSize($size);
 *     $list = $service->getList();
 *
 */

class RankService
{

    //默认pc端输出数据条数
    const DEFAULT_PC_NUM = 10;
    //默认移动端出数据条数
    const DEFAULT_MB_NUM = 10;

    //所有用户类型
    public static $userTypeAll  = [
        Rank::USER_TYPE_A,
        Rank::USER_TYPE_U,
    ];

    //所有时间排序
    public static $timeTypeAll  = [
        Rank::TIME_TYPE_D,
        Rank::TIME_TYPE_W,
        Rank::TIME_TYPE_M,
    ];

    //所有排序方式
    public static $orderTypeAll = [
        Rank::ORDER_TYPE_1,
        Rank::ORDER_TYPE_2,
        Rank::ORDER_TYPE_3,
    ];


    public static $timeTypeAllKey = [
        Rank::TIME_TYPE_D => 'dayList',
        Rank::TIME_TYPE_W => 'weekList',
        Rank::TIME_TYPE_M => 'monthList',
    ];

    //暂时未用到
    private $_groupType = [];
    private $_userType;
    private $_timeType;
    private $_orderType;
    private $_size;
    //是否获取所有排行数据
    private $_getAll = false;
    //底层数据服务
    private $_rankDao;

    public function setGroupType($groupType)
    {
        $this->_groupType = $groupType;
        return $this;
    }

    public function setUserType($userType)
    {
        $this->_userType = $userType;
        return $this;
    }

    public function getUserType()
    {
        return $this->_userType;
    }

    public function setTimeType($timeType)
    {
        $this->_timeType = $timeType;
        return $this;
    }

    public function getTimeType()
    {
        return $this->_timeType;
    }

    public function setOrderType($orderType)
    {
        $this->_orderType = $orderType;
        return $this;
    }

    public function getOrderType()
    {
        return $this->_orderType;
    }

    public function setSize($size)
    {
        $this->_size = $size;
        return $this;
    }

    public function getSize()
    {
        return $this->_size ? $this->_size : self::DEFAULT_PC_NUM;
    }

    public function setGetAll($s=false)
    {
        $this->_getAll = $s;
        return $this;
    }

    public static function getDefaultUserType()
    {
        return Rank::USER_TYPE_A;
    }

    public static function getDefaultTimeType()
    {
        return Rank::TIME_TYPE_D;
    }

    public static function getDefaultOrderType()
    {
        return Rank::ORDER_TYPE_1;
    }

    /**
     * 从底层服务获取排行数据
     * @return array
     */
    public function getRankFromDataService()
    {

        $data = [];
        $data['userType'] = $this->getUserType();
        $data['status']   = 1;

        //按等级不支持日周月排
        if($this->_getAll && $this->getOrderType() != Rank::ORDER_TYPE_3)
        {
            $timeType = self::$timeTypeAll;
        } else
        {
            $timeType = [$this->getTimeType()];
        }

        foreach ($timeType as $v)
        {
            $data[$v] = $this->getRankDao()->getRanking($this->getUserType(),$v,$this->getOrderType(),$this->getSize());
        }

        return $data;
    }

    /**
     * 获所有排行数据
     * @return array
     */
    public function getAll()
    {
        $data    = [];
        //获取全部的排行数据
        $this->setGetAll(true);
        //主播
        $this->setUserType(Rank::USER_TYPE_A);
        //按收入
        $this->setOrderType(Rank::ORDER_TYPE_1);
        //获取主播收入排行数据
        $data['anchorEarn']  = $this->formatPcRankList($this->getRankFromDataService());

        //主播
        $this->setUserType(Rank::USER_TYPE_A);
        //按人气
        $this->setOrderType(Rank::ORDER_TYPE_2);
        //获取主播收人气排行数据
        $data['anchorPop']   = $this->formatPcRankList($this->getRankFromDataService());

        //主播
        $this->setUserType(Rank::USER_TYPE_A);
        $this->setTimeType(Rank::TIME_TYPE_D);
        //按等级
        $this->setOrderType(Rank::ORDER_TYPE_3);
        //获取主播等级排行榜数据
        $data['anchorLevel'] = $this->formatPcRankList($this->getRankFromDataService());

        //观众贡献榜
        $this->setUserType(Rank::USER_TYPE_U);
        $this->setOrderType(Rank::ORDER_TYPE_1);
        //获取观众贡献榜数据
        $data['userDevote']  = $this->formatPcRankList($this->getRankFromDataService());
        $this->setGetAll(false);

        return $data;
    }

    /**
     * 按条件获取单个分类排行
     * @return array
     */
    public function getList()
    {
        $data = $this->getRankFromDataService();
        return isset($data[$this->getTimeType()]) ? $data[$this->getTimeType()] : [];
    }

    /**
     * pc 格式化排行数据
     * @param  array  $list [description]
     * @return [type]       [description]
     */
    public  function formatPcRankList( array $list)
    {
        $data    = [];
        //按等级不支持日周月排
        $timeKey = $this->getOrderType() == Rank::ORDER_TYPE_3 ? [ Rank::TIME_TYPE_D => 'list'] : self::$timeTypeAllKey;

        foreach ($list as $key => $v)
        {
            if(isset($timeKey[$key]))
            {
                $data[$timeKey[$key]] = $v;
            } else
            {
                $data[$key] = $v;
            }
        }

        return $data;
    }

    public function getRankDao()
    {
        if(!$this->_rankDao)
        {
            $this->_rankDao = new Rank();
        }

        return $this->_rankDao;
    }
}
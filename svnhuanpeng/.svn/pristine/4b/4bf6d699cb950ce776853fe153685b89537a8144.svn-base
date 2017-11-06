<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 17/2/25
 * Time: 上午10:33
 * 主播收益统计获取收益相关的数据层
 */
namespace income;

/**获取经纪公司旗下主播公司收益
 * @param $month  string 月份  如 '2017-02'
 * @param $incomeType   string  收入类型  0金币  1金豆
 * @param $order  string  排序   0降序  1升序
 * @param $page   string  页码
 * @param $size   string  每页条数
 * @return array  返回经纪公司ID 及对应的的金豆 、金币数 形如:array(array('id'=>'90','coin'=>'400','bean'=>'340','crmb'=>'40','brmb'=>'50'),);
 * 返回值说明 bean：金豆；coin：金币；crmb：金币兑换成人民币后的数额；prmb：金豆兑换成人民币的数额
 */
function  getCompanyIncome( $month, $incomeType, $order, $page, $size)
{

    $list= array (
        'list'=>array(
                    array('id'=>1,'coin'=>700,'bean'=>220,'crmb'=>15,'brmb'=>15),
                    array('id'=>6,'coin'=>500,'bean'=>250,'crmb'=>25,'brmb'=>25),
                    array('id'=>11,'coin'=>400,'bean'=>20,'crmb'=>35,'brmb'=>35),
                    array('id'=>1,'coin'=>300,'bean'=>300,'crmb'=>55,'brmb'=55)
                     ),
        'total'=>10  //经纪公司总数
    );
return $list;
}


/**获取主播的金豆金币
 * @param $type  string   0未签约主播  1 已签约主播
 * @param $month  string  月份  如 '2017-02'
 * @param $incomeType string   收入类型  0金币  1金豆
 * @param $order  string  排序   0降序  1升序
 * @param $page string   页码
 * @param $size string  每页条数
 * @return array  返回 主播id 及对应的金币、金豆数 形如:array(array('uid'=>'90','coin'=>'400','bean'=>'340'),);
 */
function  getAnchorIncome($type,$month, $incomeType, $order, $page, $size)
{
    $list= array (
        'list'=>array(
            array('uid'=>11,'coin'=>700,'bean'=>220),
            array('uid'=>66,'coin'=>600,'bean'=>250),
            array('uid'=>23,'coin'=>500,'bean'=>20),
            array('uid'=>12,'coin'=>400,'bean'=>300,)
        ),
        'total'=>10  //未签约主播或已签约主播总数
    );
    return $list;
}

/**获取经纪公司旗下主播的金豆金币
 * @param $cid  string   经纪公司id
 * @param $month  string  月份  如 '2017-02'
 * @param $incomeType string   收入类型  0金币  1金豆
 * @param $order  string  排序   0降序  1升序
 * @param $page string   页码
 * @param $size string  每页条数
 * @return array  返回 主播id 及对应的金币、金豆数 形如:array(array('uid'=>'90','coin'=>'400','bean'=>'340'),);
 */
function  getAnchorIncome($cid,$month, $incomeType, $order, $page, $size)
{
    $list= array (
        'list'=>array(
            array('uid'=>11,'coin'=>700,'bean'=>220),
            array('uid'=>66,'coin'=>600,'bean'=>250),
            array('uid'=>23,'coin'=>500,'bean'=>20),
            array('uid'=>12,'coin'=>400,'bean'=>300,)
        ),
        'total'=>10  //总条数
    );
    return $list;
}
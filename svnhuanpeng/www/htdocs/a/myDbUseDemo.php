<?php
exit;
include '../init.php';

 header("Content-type: text/html; charset=utf-8"); 
/* 鉴于现有代码中每次对数据库的CRUD操作都会写一条完整的Sql语句
 * 这样即消耗,也容易出错,同时也增加了代码的冗余度,鉴于此就单独封装了一个CRUD操作的类,完成sql语句的拼接
 * 现将使用方法列举出来,供参考
 * yandong@6rooms.com
 * Date 2015-12-21 11:50 am
 * copyright@6.cn
 * version 0.0
 */
//先实例化一个操作数据库CRUD的对象,建议统一使用$mydb,这样就保证了命名的一致性,方便以后阅读代码
 $mydb=new DBHelperi_huanpeng();
 //下面是CURD操作事例,以userstatic表为例
 /**
  * 添加一条数据
  * insert 第一个参数要操作的表名,第二个参数需要写入的键值
  * 返回 最后一次插入的行号
  */

$data = array(
    'username'=>'huang'.time(),
    'password'=>123456
    );
echo "---------------插入--------------------";
$aresult= $mydb->insert('userstatic',$data);
var_dump($aresult)."<br/>";
######################################
/**
 * 查询 下面是用字符串的方式,这里还可以用数组的形式 如 field(array('username,password')),order(array('uid'=>'desc','tm'=>'asc'))
 * field 需要返回的字段,如果需要全部的字段不需要调用field方法就可以了
 * order 排序 
 * where 查询条件,如果有多个可以用and 的形式写上
 * limit 条数
 * select括号里面是要操作的表明
 * 返回一个二维数组
 */
echo "<br/>";
echo "---------------查询--------------------";
echo "<br/>";
$sresult= $mydb->field('username,password')
    ->order('uid desc')
    ->where('uid='.$aresult.'')
    ->limit(10)
    ->select('userstatic',1);
    var_dump($sresult);
#######################################
echo "<br/>";
echo "---------------修改--------------------";
//**
// * 修改
// * where 条件
// * update 第一个参数是要操作的表名,第二个参数是新的数据
// * 返回bool 成功true 失败false
// */
$datas=array(
    'username'=>'詹姆斯',
    'nick'=>'詹姆斯'
);
$uresult=$mydb->where('uid='.$aresult.'')->update('userstatic',$datas,1);
echo "<br/>";
var_dump($uresult);

//#######################################
echo "<br/>";
echo "------------修改后结果----------------"."<br/>";
$afsuresult=$mydb->where('uid='.$aresult.'')->select('userstatic',1);
echo "<br/>";
var_dump($afsuresult);
//#######################################
echo "<br/>";
echo "---------------删除--------------------";
///**
// * 删除一条数据
// * where 条件
// * delete 括号里面的参数是要操作的表
// * 返回 bool 成功true 失败false 
// */
$dresult=$mydb->where('uid='.$aresult.'')->delete('userstatic',1);
echo "<br/>";
var_dump($dresult);
<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017-6-20
 * Time: 下午3:25:21
 * Desc: due工具类 | 返回一些 配置信息 | 变动小 信息资源消耗少的信息
 */
namespace api\due;

include '../../../include/init.php';

class dueTools
{

    const TAG_TYPE_01 = 1; // 用户-申请退单（已完成）

    const TAG_TYPE_02 = 2; // 用户-取消订单（待接单）

    const TAG_TYPE_03 = 3; // 主播-拒单（待接单）、取消订单（进行中）

    const TAG_TYPE_04 = 4; // 主播拒绝退单  原因tag

    private static $orderMakeTags = [
        self::TAG_TYPE_01 => [
            '联系不到主播',
            '服务态度差',
            '主播水平差',
            '主播临时离开',
            '主播未能满足要求',
            '尝试性操作'
        ],
        self::TAG_TYPE_02 => [
            '联系不到主播',
            '服务态度差',
            '主播水平差',
            '主播临时离开',
            '主播未能满足要求',
            '尝试性操作'
        ],
        self::TAG_TYPE_03 => [
            '宝宝暂时没空哦',
            '联系不到你哦',
            '无法满足你的需求',
            '宝宝临时有事',
            '协商取消',
            '尝试性操作'
        ],
        self::TAG_TYPE_04 => [
            '带打很棒不给退',
            '尽力了就别退了'
        ]
    ];

    /**
     * 返回退单原因标签信息
     * ---------------
     * return array
     */
    public static function orderMakeTags($type)
    {
        return self::$orderMakeTags[$type];
    }

    public function display()
    {
        // type 用来区别以后请求不同的业务所 进行扩展
        $type = ! isset($_POST['type']) ? 1 : intval($_POST['type']);
        $data = self::orderMakeTags($type);
        $data = ! empty($data) ? $data : [];
        render_json([
            'list' => $data
        ]);
    }
}
$toolObj = new dueTools();
$toolObj->display();

?>
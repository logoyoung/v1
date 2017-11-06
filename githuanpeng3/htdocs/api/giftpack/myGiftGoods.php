<?php

/**
 * 获取用户的背包礼物列表
 */
include '../../../include/init.php';

use service\common\ApiCommon;
use service\pack\BackpackService;
use service\user\UserCenterCountService;
use service\pack\PackEvnentService;

class myGiftGoods extends ApiCommon {

    public $paramResult = [];
    public $param = [
        'page' => [
            'name'    => 'page',
            'default' => '0',
        ],
        'size' => [
            'name'    => 'size',
            'default' => '8000'
        ]
    ];

    public function initCheck() {
        $this->checkIsLogin(true);
        ## 个人中心计数
        UserCenterCountService::setValue($this->uid, UserCenterCountService::HASH_TABLE_FIELD_BACKPACK_NUM, 0);
    }

    public $backpackService = null;

    public function getBackpackService(): BackpackService {
        if (is_null($this->backpackService)) {
            $this->backpackService = new BackpackService();
        }
        return $this->backpackService;
    }

    /**
     * 获取数据
     * @param type $uid
     * @return type
     */
    public function getGiftList($uid) {

        $list = $this->getBackpackService()->getGoodsList($uid, $this->paramResult['page'], $this->paramResult['size']);
        $list = $this->sortByType($list);
        $result = [];
        foreach ($list as $value) {
            $info = $this->getBackpackService()->getGoodsInfo([$value['goodsid']]);
            if (empty($info)) {
                continue;
            }
            $tmp = $info[$value['goodsid']];
            $tmp['goodsId'] = $value['id'];
            $tmp['goodsNum'] = $value['goodsNum'];
            $tmp['validTime'] = $value['validTime'];
            $result[] = $tmp;
        }
        $this->_rebuildData($result);
        return $result;
    }

    private function _rebuildData(&$result) {
        $conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $field = ['bg', 'bg_3x', 'poster', 'poster_3x', 'web_preview', 'web_bg'];
        foreach ($result as $index => $data) {
            foreach ($field as $item) {
                if (isset($data[$item]) && $data[$item]) {
                    $result[$index][$item] = "http://" . $conf['domain-img'] . $data[$item];
                }
            }
        }
    }

    /**
     * 数据格式化
     * @param type $list
     * @param type $type
     * @return type
     */
    public function sortByType($list, $type = 1) {
        $formatList = [];

        if ($type == 1) {
            $sortList = $this->getBackpackService()->toolSortOut($list);
            $formatList = $this->getBackpackService()->toolFormatData($sortList);
        }

        return $formatList;
    }

    /**
     * 获取我可以使用的礼物
     */
    public function action() {

        $this->paramResult = self::getParam($this->param, TRUE);
        try {
            $res = $this->getGiftList($this->uid);
            $this->resultFormatData['data']['list'] = $res;
        } catch (Exception $exc) {
            $this->resultFormatData['code'] = is_numeric($exc->getMessage()) ? $exc->getMessage() : -1;
            $funError = errorDesc($exc->getMessage());
            $this->resultFormatData['desc'] = $funError == '未知错误' ? $exc->getMessage() : $funError;
        }
        $this->rander();
    }

}

//
$n = new myGiftGoods();
$n->action();

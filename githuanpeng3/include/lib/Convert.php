<?php

namespace lib;

class Convert {

    /**
     * 不保留小数位
     */
    const PRECISION_00 = 0;

    /**
     * 保留1位小数位
     */
    const PRECISION_01 = 1;

    /**
     * 保留2位小数位
     */
    const PRECISION_02 = 2;

    /**
     * 保留3位小数位
     */
    const PRECISION_03 = 3;

    /**
     * 保留4位小数位
     */
    const PRECISION_04 = 4;

    /**
     * 是否严格格式化
     */
    const IS_FORMAT = FALSE;

    /**
     * 金额数据换算
     * @param type $data   数据
     * @param int $precision  精度:小数位位数
     * @param bool $isFormat 是否格式化  default false
     * @return \lib\type
     */
    public static function property($data, $precision = Convert::PRECISION_02, $isFormat = Convert::IS_FORMAT) {
        if (is_array($data)) {
            $return = [];
            foreach ($data as $key => $value) {
                $return[$key] = self::property($value, $precision, $isFormat);
            }
        } else {
            if (is_numeric($data)) {
                $return = round($data, $precision);
                if ($isFormat) {
                    $return = sprintf("%01.{$precision}f", $return);
                }
            } else {
                $return = $data;
            }
        }
        return $return;
    }

}

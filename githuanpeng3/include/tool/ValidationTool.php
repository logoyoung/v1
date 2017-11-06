<?php

namespace tool;

/**
 * 验证规则方法
 */
class ValidationTool {

    /**
     * 表情检测
     * @param type $str
     * @return type
     */
    public static function checkEmoji($str) {
        return checkEmoji($str) === 0 ? true : false;
    }

    /**
     * 表情昵称长度
     * @param type $str
     * @return type
     */
    public static function checkNickLength($str) {
        if (mb_strlen($str, 'utf-8') < 3 || mb_strlen($str, 'utf-8') > 12) {
            return false;
        } else {
            if (mb_strlen($str, 'latin1') < 3 || mb_strlen($str, 'latin1') > 30) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * 检测手机是否合法
     * 
     * @param type $phone
     */
    public static function checkPhoneValid($phone) {
        return check_phone_valid($phone);
    }
    
    /**
     * 参数过滤
     * @param type $param
     * @return string
     */
    public static function filterWords($param) {
        return filterWords($param);
    }
    
    /**
     * 检测密码长度
     * @param type $param
     * @return type
     */
    public static function checkPasswordLeng($param) {
        return checkPasswordLeng($param);
    }
    


}

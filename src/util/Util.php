<?php
namespace fuyuezhen\wechat\util;

/** 
* 工具类
* @author fuyuezhen <976066889@qq.com>
* @created 2020-12-11
*/ 
class Util
{
    
    /**
     * 获取随机字符
     *
     * @param integer $length  长度
     * @param string $chars    随机字符
     * @return void
     */
    public static function getRandomString($length = 16, $chars ='') {
        empty($chars) && $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}
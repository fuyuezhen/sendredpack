<?php
namespace fuyuezhen\sendredpack\util;

/** 
* 工具类
* @author fuyuezhen <976066889@qq.com>
* @created 2020-12-11
*/ 
class Util
{
    
    /**
     * [xmltoarray xml格式转换为数组]
     * @param  [type] $xml [xml]
     * @return [type]      [xml 转化为array]
     */
    public static function xmlToArray($xml) { 
        //禁止引用外部xml实体 
        libxml_disable_entity_loader(true); 
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA); 
        $val = json_decode(json_encode($xmlstring),true); 
        return $val;
    }

    /**
    * [arraytoxml 将数组转换成xml格式（简单方法）:]
    * @param  [type] $data [数组]
    * @return [type]       [array 转 xml]
    */
    public static function arrayToXml($data){
        $str='<xml>';
        foreach($data as $k=>$v) {
            $str.='<'.$k.'>'.$v.'</'.$k.'>';
        }
        $str.='</xml>';
        return $str;
    }

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
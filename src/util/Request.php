<?php
namespace fuyuezhen\sendredpack\util;

/** 
* 请求类
* @author fuyuezhen <976066889@qq.com>
* @created 2020-12-09
*/ 
class Request
{
    /**
     * 获取接口数据
     * @param string $url 请求地址
     * @param array  $vars 提交数据
     * @param array  $cert 证书
     * @param string $second 超时时间
     * @param string $type 请求类型
     * @return void
     */
    public static function curl($url, $vars, $cert, $second = 30, $aHeader = array()){
       
        $ch = curl_init();
        //超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //cert 与 key 分别属于两个.pem文件
        //请确保您的libcurl版本是否支持双向认证，版本高于7.20.1，相当于发curl验证【当前文件所在目录/cert/wxpay/】下的两个pem证书文件。
        curl_setopt($ch, CURLOPT_SSLCERT, $cert['apiclient_cert']);
        curl_setopt($ch, CURLOPT_SSLKEY, $cert['apiclient_key']);
        //curl_setopt($ch,CURLOPT_CAINFO,dirname(__FILE__).DIRECTORY_SEPARATOR.
        //    'cert'.DIRECTORY_SEPARATOR.'rootca.pem');    //这个不需要，因为大部分的操作系统都已经内置了rootca.pem证书了，就是常见的CA证书。
        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }
}
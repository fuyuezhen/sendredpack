<?php
namespace fuyuezhen\wechat\util;

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
     * @param array $data 提交数据
     * @param string $type 请求类型
     * @author fuyuezhen <976066889@qq.com>
     * @created 2020-12-09
     * @return void
     */
    public static function curl($url, $data = [], $type = 'get'){
        if(is_array($data)){
            $data  = json_encode($data);    
        }
        $headerArray = array("Content-type:application/json;charset='utf-8'", "Accept:application/json");
        // 初始化CURL句柄
        $curl = curl_init(); 
        // 设置请求的URL
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        if($type == 'post' || !empty($data)){
            if($type == 'post' || $type == 'get'){
                curl_setopt($curl, CURLOPT_POST, 1);
            }else{
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type); //设置请求方式
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // 设置提交的字符串
        }
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headerArray);
        // 设为TRUE把curl_exec()结果转化为字串，而不是直接输出 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        $output = json_decode($output,true);

        // if(isset($output['data'])){
        //     $output = $output['data'];
        // }
        return $output;
    }
}
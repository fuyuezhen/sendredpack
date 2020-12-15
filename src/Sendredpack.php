<?php
namespace fuyuezhen\sendredpack;

/** 
* 微信现金红包
* @author fuyuezhen <976066889@qq.com>
* @created 2020-12-11
*/ 
class Redpack
{
    /**
     * 请求参数
     * @var array
     */
    private $parameters = [
        'nonce_str'     => '', // 是 | 随机字符串 ，随机字符串，不长于32位
        'sign'          => '', // 是 | 签名
        'client_ip'     => '', // 是 | Ip地址 ，调用接口的机器Ip地址
        'total_num'     => 1,  // 是 | 红包发放总人数 ，红包发放总人数

        'mch_id'        => '', // 是 | 商户号 ，微信支付分配的商户号
        'wxappid'       => '', // 是 | 公众账号appid

        'mch_billno'    => '', // 是 | 商户订单号 ，每个订单号必须唯一
        'send_name'     => '', // 是 | 商户名称 ，红包发送者名称
        're_openid'     => '', // 是 | 用户openid ，接受红包的用户openid
        'total_amount'  => '', // 是 | 付款金额 ，付款金额，单位分
        'wishing'       => '', // 是 | 红包祝福语 ，如：感谢您参加猜灯谜活动，祝您元宵节快乐！
        'act_name'      => '', // 是 | 活动名称 ，如：猜灯谜抢红包活动
        'remark'        => '', // 是 | 备注 ，如：猜越多得越多，快来抢！
        'scene_id'      => '', // 否 | 场景id
        // 发放红包使用场景，红包金额大于200或者小于1元时必传
        // PRODUCT_1:商品促销
        // PRODUCT_2:抽奖
        // PRODUCT_3:虚拟物品兑奖 
        // PRODUCT_4:企业内部福利
        // PRODUCT_5:渠道分润
        // PRODUCT_6:保险回馈
        // PRODUCT_7:彩票派奖
        // PRODUCT_8:税务刮奖
        // 'risk_info' => '', // 是 | 活动信息
    ];
    
    /**
     * 设置请求参数
     * @return void
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * 发放现金红包
     * @return void
     */
    public function send()
    {
        // throw new SDKRuntimeException("缺少发红包接口必填参数remark！"."<br>");
        // 校验请求参数
        $this->checkParameter();
        echo 11;
        exit;
        // getRandomString


        $obj2 = array();
        //appid
        $obj2['wxappid'] = config('wx_gzh.appId');
        //商户id
        $obj2['mch_id'] = config('wx_sh.mchId');
        //组合成28位，根据官方开发文档，可以自行设置
        $obj2['mch_billno'] = config('wx_sh.mchId') . date('YmdHis') . rand(1000, 9999);
        // 调用接口的机器IP地址
        $obj2['client_ip'] = $_SERVER['REMOTE_ADDR'];
        //接收红包openid
        $obj2['re_openid'] = session('openid');

        /* 付款金额设置start，按照概率设置随机发放。
        * 1-200元之间，单位分。这里设置95%概率为1-2元，5%的概率为2-10元
        */
        $n = rand(1, 100);
        if ($n <= 95) {
            $obj2['total_amount'] = rand(100, 200);
        } else {
            $obj2['total_amount'] = rand(200, 1000);
        }
        //$obj2['total_amount'] = 100;
        /* 付款金额设置end */

        // 红包个数
        $obj2['total_num'] = 1;
        // 商户名称
        $obj2['send_name'] = "小门太";
        // 红包祝福语
        $obj2['wishing'] = "恭喜发财，大吉大利";
        // 活动名称
        $obj2['act_name'] = "小门太认证领红包";
        // 备注
        $obj2['remark'] = "小门太红包";

        /* 文档中未说明以下变量，李富林博客中有。注释起来也没问题。不需要。
        $obj2['min_value'] = $money;
        $obj2['max_value'] = $money;
        $obj2['nick_name'] = '小门太红包';
        */

        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";
        $isPay = pay_lucky_money($url, $obj2);
        $res = xml_to_array($isPay);
        // 发放成功，把红包数据插入数据库
        if ($res['return_msg'] == '发放成功') {
            // 发放成功，进行逻辑处理
        
        } else {
            // 发放失败，返回失败原因
            return $res['return_msg'];
        }
    }

    /**
     * 微信发放现金红包核心函数，调用本函数就直接发放红包了。
     * @param $url 现金红包的请求地址
     * @param $obj
     * @return mixed
     */
    function pay_lucky_money($url, $obj)
    {
        //创建随机字符串(32位)
        $obj['nonce_str'] = str_rand();
        //创建签名
        $sign = get_sign($obj, false);
        //halt($sign);
        $obj['sign'] = $sign;    //将签名传入数组
        $postXml = array_to_xml($obj);    //将参数转为xml格式
        //halt($postXml);
        $responseXml = curl_post_ssl($url, $postXml);    //提交请求
        //halt($responseXml);
        return $responseXml;
    }

    /**
     * @param $arr 生成前面的参数
     * @param $urlencode
     * @return string 返回加密后的签名
     */
    private function getSign($arr, $urlencode)
    {
        $buff = "";
        //对传进来的数组参数里面的内容按照字母顺序排序，a在前面，z在最后（字典序）
        ksort($arr);
        foreach ($arr as $k => $v) {
            if (null != $v && "null" != $v && "sign" != $k) {    //签名不要转码
                if ($urlencode) {
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        // 去掉末尾符号“&”，其实不用这个if，因为长度肯定大于0
        if (strlen($buff) > 0) {
            $stringA = substr($buff, 0, strlen($buff) - 1);
        }
        //签名拼接api
        $stringSignTemp = $stringA . "&key=" . config('wx_sh.key');
        //签名加密并大写
        $sign = strtoupper(md5($stringSignTemp));
        return $sign;
    }

    //post请求网站，需要证书
    function curl_post_ssl($url, $vars, $second = 30, $aHeader = array())
    {
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
        curl_setopt($ch, CURLOPT_SSLCERT, dirname(__FILE__) . DIRECTORY_SEPARATOR .
            'cert' . DIRECTORY_SEPARATOR . 'wxpay' . DIRECTORY_SEPARATOR . 'apiclient_cert.pem');
        curl_setopt($ch, CURLOPT_SSLKEY, dirname(__FILE__) . DIRECTORY_SEPARATOR .
            'cert' . DIRECTORY_SEPARATOR . 'wxpay' . DIRECTORY_SEPARATOR . 'apiclient_key.pem');
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
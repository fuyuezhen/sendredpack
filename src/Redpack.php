<?php
namespace fuyuezhen\sendredpack;

use fuyuezhen\sendredpack\util\Util;
use fuyuezhen\sendredpack\util\Request;
use fuyuezhen\sendredpack\config\UrlConfig;

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
        // 'sign'          => '', // 是 | 签名
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

    // 商户支付密钥
    private $mch_key;
    // 商户证书
    private $apiclient_key;
    private $apiclient_cert;

    /**
     * 初始化
     * @param string $appid    公众账号appid
     * @param string $mch_id   商户号
     * @param string $mch_key  商户支付密钥
     */
    public function __construct($appid, $mch_id, $mch_key)
    {
        $this->parameters['wxappid'] = $appid;
        $this->parameters['mch_id'] = $mch_id;
        $this->mch_key = $mch_key;
    }
    
    /**
     * 设置请求参数
     * @return void
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
        return $this;
    }
    /**
     * 设置请求参数
     * @return void
     */
    public function setApiclientKey($url)
    {
        $this->apiclient_key = $url;
        return $this;
    }
    /**
     * 设置请求参数
     * @return void
     */
    public function setApiclientCert($url)
    {
        $this->apiclient_cert = $url;
        return $this;
    }

    /**
     * 发放现金红包
     * @return void
     */
    public function send()
    {
        /* 付款金额设置start，按照概率设置随机发放。
        * 1-200元之间，单位分。这里设置95%概率为1-2元，5%的概率为2-10元
        */
        // $n = rand(1, 100);
        // if ($n <= 95) {
        //     $obj2['total_amount'] = rand(100, 200);
        // } else {
        //     $obj2['total_amount'] = rand(200, 1000);
        // }
        /* 付款金额设置end */

        // 校验请求参数
        $this->checkParameter();

        //创建签名
        $sign = $this->getSign($this->parameters, false);
        $this->parameters['sign'] = $sign;    //将签名传入数组
        $postXml = Util::arrayToXml($this->parameters);    //将参数转为xml格式
        
        // 提交请求
        $responseXml = Request::curl(UrlConfig::SENDREDPACK_URL, $postXml, [
            'apiclient_cert' => $this->apiclient_cert,
            'apiclient_key'  => $this->apiclient_key,
        ]);    

        // 将xml格式转为数组
        $result = Util::xmlToArray($responseXml);
        // 返回参数：
        // $result = [
        //     'return_code' => 'SUCCESS',         // 是： 返回状态码 ，SUCCESS/FAIL、此字段是通信标识，非红包发放结果标识，红包发放是否成功需要查看result_code来判断
        //     'return_msg'  => '如：签名失败',     // 否： 返回信息 ，如非空，为错误原因签名失败、参数格式校验错误

        //     // 以下字段在return_code为SUCCESS的时候有返回
        //     'result_code'   => 'SUCCESS',       // 是： 业务结果 ，SUCCESS/FAIL。
        //     'err_code'      => 'SYSTEMERROR',   // 否： 错误码信息。
        //     'err_code_des'  => '系统错误',       // 否： 结果信息描述

        //     // 以下字段在return_code和result_code都为SUCCESS的时候有返回
        //     'mch_billno'    => '',       // 是： 商户订单号
        //     'mch_id'        => '',       // 是： 商户号
        //     'wxappid'       => '',       // 是： 公众账号appid
        //     're_openid'     => '',       // 是： 用户openid
        //     'total_amount'  => '',       // 是： 付款金额
        //     'send_listid'   => '',       // 是： 微信单号
        // ];
        if (isset($result['return_code']) && isset($result['result_code']) && $result['return_code']=='SUCCESS' && $result['result_code']=='SUCCESS') {
            $result['return_status'] = 1;
        } else {
            $result['return_status'] = 0;
        }
        return $result;
    }

    /**
     * 校验参数
     * @return void
     */
    private function checkParameter()
    {
        try {
            if (empty($this->parameters['nonce_str'])) {
                $this->parameters['nonce_str'] = Util::getRandomString();
            }
            if (empty($this->parameters['client_ip'])) {
                $this->parameters['client_ip'] = $_SERVER['REMOTE_ADDR'];
            }
            if (empty($this->parameters['total_num'])) {
                $this->parameters['total_num'] = 1;
            }

            if (empty($this->parameters['mch_id'])) {
                throw new \Exception("缺少发红包接口必填参数mch_id！");
            }
            if (empty($this->parameters['wxappid'])) {
                throw new \Exception("缺少发红包接口必填参数wxappid！");
            }
            if (empty($this->parameters['mch_billno'])) {
                throw new \Exception("缺少发红包接口必填参数mch_billno！");
            }
            if (empty($this->parameters['send_name'])) {
                throw new \Exception("缺少发红包接口必填参数send_name！");
            }
            if (empty($this->parameters['re_openid'])) {
                throw new \Exception("缺少发红包接口必填参数re_openid！");
            }
            if (empty($this->parameters['total_amount'])) {
                throw new \Exception("缺少发红包接口必填参数total_amount！");
            }
            if (empty($this->parameters['wishing'])) {
                throw new \Exception("缺少发红包接口必填参数wishing！");
            }
            if (empty($this->parameters['act_name'])) {
                throw new \Exception("缺少发红包接口必填参数act_name！");
            }
            if (empty($this->parameters['remark'])) {
                throw new \Exception("缺少发红包接口必填参数remark！");
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
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
        $stringSignTemp = $stringA . "&key=" . $this->mch_key;
        //签名加密并大写
        $sign = strtoupper(md5($stringSignTemp));
        return $sign;
    }
}
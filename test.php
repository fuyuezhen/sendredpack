<?php

use \fuyuezhen\sendredpack\Redpack;

// 实例
$this->appid             = '';
$this->pay_mchid         = '';
$this->pay_key           = '';
$this->apiclient_key     = '';
$this->apiclient_cert    = '';

$redpack = new Redpack($this->appid, $this->pay_mchid, $this->pay_key);
// 商户证书
$redpack->setApiclientKey($this->apiclient_key);
$redpack->setApiclientCert($this->apiclient_cert);
$redpack->setParameter('mch_billno', date('Ymd') . \mt_rand(0,100) );  // 商户订单号
$redpack->setParameter('send_name', '中央大道');   // 商户名称
$redpack->setParameter('re_openid', $this->wechatInfo['openid']);   // 用户openid
$redpack->setParameter('total_amount', 0.1*100);   // 付款金额
$redpack->setParameter('wishing', '恭喜发财');   // 红包祝福语
$redpack->setParameter('act_name', '恭喜发财');   // 活动名称
$redpack->setParameter('remark', '恭喜发财');   // 备注
$redpack->setParameter('scene_id', 'PRODUCT_2');   // 场景id
$redpack->send();
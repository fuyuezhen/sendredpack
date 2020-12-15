CREATE TABLE `tp_sendredpack_result` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID编号',
  `redpack_type` varchar(255) DEFAULT NULL COMMENT '红包类型',
  `return_code` varchar(255) DEFAULT NULL COMMENT '返回状态码',
  `return_msg` varchar(255) DEFAULT NULL COMMENT '返回信息',
  `result_code` varchar(255) DEFAULT NULL COMMENT '业务结果',
  `err_code` varchar(255) DEFAULT NULL COMMENT '错误码信息。',
  `err_code_des` varchar(255) DEFAULT NULL COMMENT '结果信息描述',
  `mch_billno` varchar(255) DEFAULT NULL COMMENT '商户订单号',
  `mch_id` varchar(255) DEFAULT NULL COMMENT '商户号',
  `wxappid` varchar(255) DEFAULT NULL COMMENT '公众账号appid',
  `re_openid` varchar(255) DEFAULT NULL COMMENT '用户openid',
  `total_amount` varchar(255) DEFAULT NULL COMMENT '付款金额',
  `send_listid` varchar(255) DEFAULT NULL COMMENT '微信单号',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `delete_time` datetime DEFAULT NULL COMMENT '软删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='红包返回结果';
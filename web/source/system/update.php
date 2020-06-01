AlTER TABLE  `ims_ewei_shop_member_level` ADD `commission1` decimal(10,2) DEFAULT '0.00' COMMENT '其他返利1';
AlTER TABLE  `ims_ewei_shop_member_level` ADD `commission2` decimal(10,2) DEFAULT '0.00' COMMENT '其他返利2';
AlTER TABLE  `ims_ewei_shop_member_level` ADD `commission3` decimal(10,2) DEFAULT '0.00' COMMENT '其他返利3';
AlTER TABLE  `ims_ewei_shop_member` ADD `agentpower` varchar(255) NULL;
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `abonus_rate` varchar(255) NOT NULL DEFAULT '' COMMENT '区域代理返利';
alter table `ims_ewei_shop_goods` modify `upgradebag` varchar(255) not null  DEFAULT "0" COMMENT '升级礼包商品对应等级字段值类型修改';
AlTER TABLE  `ims_ewei_shop_goods` ADD `isupgradebag` tinyint(4) DEFAULT 0 COMMENT '商品是否为升级礼包类型商品';
AlTER TABLE  `ims_ewei_shop_goods` ADD `upgradebag` int(10) DEFAULT 0 COMMENT '升级礼包商品对应等级';
AlTER TABLE  `ims_ewei_shop_goods` ADD `commissionagent` tinyint(4) DEFAULT '0' COMMENT '独立佣金管理奖返利控制';
AlTER TABLE  `ims_ewei_shop_goods` ADD `commissionself` tinyint(4) DEFAULT '0' COMMENT '独立佣金自购返利控制';
AlTER TABLE  `ims_ewei_shop_order_goods` ADD `commissionself` decimal(10,2) DEFAULT '0.00' COMMENT '自购返利比例';
AlTER TABLE  `ims_ewei_shop_member_level` ADD `commission` decimal(10,2) DEFAULT '0.00' COMMENT '返利比例';
ALTER TABLE `ims_ewei_shop_member` ADD `is_allowance` int(1) DEFAULT 0;
ALTER TABLE `ims_ewei_shop_member` ADD `agent100` int(11) DEFAULT 0 ,ADD `agent99` int(11) DEFAULT 0 ,ADD `agent98` int(11) DEFAULT 0 ,ADD `agent97` int(11) DEFAULT 0 ,ADD `agent96` int(11) DEFAULT 0 ;
ALTER TABLE `ims_ewei_shop_order` ADD `agent100` int(11) DEFAULT 0 ,ADD `agent99` int(11) DEFAULT 0 ,ADD `agent98` int(11) DEFAULT 0 ,ADD `agent97` int(11) DEFAULT 0,ADD `agent96` int(11) DEFAULT 0 ;
ALTER TABLE `ims_ewei_shop_order_goods` ADD `commissionAgent` text COMMENT '代理返利';
ALTER TABLE `ims_ewei_shop_order_goods` ADD `commissionCredit` text COMMENT '返利积分';
ALTER TABLE `ims_ewei_shop_order` ADD `serviceid` int(11) DEFAULT 0 COMMENT '服务人id';
ALTER TABLE `ims_ewei_shop_order` ADD INDEX idx_agent100 (`agent100`), ADD INDEX idx_agent99 (`agent99`), ADD INDEX idx_agent98 (`agent98` ), ADD INDEX idx_agent97 (`agent97` ), ADD INDEX idx_agent96 (`agent96` );
ALTER TABLE `ims_ewei_shop_order_goods` ADD `status100` int(11) DEFAULT 0 ,ADD `status99` int(11) DEFAULT 0 ,ADD `status98` int(11) DEFAULT 0 ,ADD `status97` int(11) DEFAULT 0 ,ADD `status96` int(11) DEFAULT 0 ;
ALTER TABLE `ims_ewei_shop_order_goods` ADD INDEX idx_status100 (`status100`), ADD INDEX idx_status99 (`status99`), ADD INDEX idx_status98 (`status98` ), ADD INDEX idx_status97 (`status97` ), ADD INDEX idx_status96 (`status96` );
ALTER TABLE `ims_ewei_shop_member` ADD `has_subscribe` int(1) DEFAULT 0;
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `saleupdate`  tinyint(3) NULL DEFAULT 0 AFTER `beforehours`;
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `newgoods`  tinyint(3) NOT NULL DEFAULT 0 AFTER `saleupdate`;
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `video`  varchar(521) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `newgoods`;
CREATE INDEX `idx_productsn` ON `ims_ewei_shop_goods`(`productsn`) USING BTREE ;
ALTER TABLE `ims_ewei_shop_order` ADD COLUMN `officcode`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `isshare`;
ALTER TABLE `ims_ewei_shop_order` MODIFY COLUMN `wxapp_prepay_id`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `officcode`;
ALTER TABLE `ims_ewei_shop_order` ADD COLUMN `cashtime`  int(11) NULL DEFAULT 0 AFTER `wxapp_prepay_id`;
ALTER TABLE `ims_ewei_shop_universalform_type` ADD COLUMN `adcontent`  text NOT NULL AFTER `adpic`;
ALTER TABLE `ims_zmcn_fw_history` ADD COLUMN `mobile`  varchar(20) NOT NULL DEFAULT '' COMMENT '手机' AFTER `ip`;
ALTER TABLE `ims_ewei_shop_member_log` ADD COLUMN `note`  varchar(255) NOT NULL COMMENT '验证' AFTER `senddata`;
ALTER TABLE `ims_ewei_shop_task2_set` ADD COLUMN `upgrade_cate`  int(11) NOT NULL COMMENT '升级类别id' AFTER `upgrade_link`;
ALTER TABLE `ims_ewei_shop_task2_set` ADD COLUMN `upgrade_intro`  text NOT NULL COMMENT '图文' AFTER `upgrade_cate`;
ALTER TABLE `ims_ewei_shop_coupon` ADD COLUMN `couponcode`  varchar(100) NULL;
ALTER TABLE `ims_ewei_shop_task2_set` ADD COLUMN `qrcode_desc`  varchar(255) NULL;
ALTER TABLE `ims_ewei_shop_groups_goods` MODIFY COLUMN `category`  varchar(255) NOT NULL DEFAULT 0 AFTER `title`;
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `shop_id`  int(11) NULL AFTER `has_subscribe`;
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `informationreward`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '完善资料奖励' AFTER `team_orders`;
ALTER TABLE `ims_ewei_shop_order` ADD COLUMN `day`  int(3) NOT NULL DEFAULT 0 COMMENT '是否日结，0不是 1 是' ,ADD COLUMN `month`  int(3) NOT NULL DEFAULT 0 COMMENT '是否月结，0不是 1 是';
ALTER TABLE `ims_ewei_shop_commission_record` ADD COLUMN `month`  int(3) NOT NULL DEFAULT 0 COMMENT '是否月结，0不是 1 是';


ALTER TABLE `ims_ewei_shop_task2_list` ADD COLUMN `share_title`  varchar(255) NOT NULL COMMENT '分享标题' AFTER `qrcode`, ADD COLUMN `share_icon`  varchar(255) NOT NULL COMMENT '分享图标' AFTER `share_title`, ADD COLUMN `description`  varchar(255) NOT NULL COMMENT '分享描述' AFTER `share_icon`;
ALTER TABLE `ims_ewei_shop_rebate_log` ADD COLUMN `total`  int(11) NOT NULL DEFAULT 0 COMMENT '商品数量' AFTER `status`;
ALTER TABLE `ims_wys_tongcheng_msg` ADD COLUMN `type`  varchar(255) NOT NULL DEFAULT '' COMMENT '发布类型' AFTER `shang_cnt`;
ALTER TABLE `ims_wys_tongcheng_msg` ADD COLUMN `businessprice`  float(50,0) NOT NULL DEFAULT 0 COMMENT '商业发布金额' AFTER `type`, ADD COLUMN `normalprice`  float(50,0) NOT NULL DEFAULT 0 AFTER `businessprice`;
ALTER TABLE `ims_wys_tongcheng_banner` ADD COLUMN `sid`  int(10) NULL DEFAULT 0 AFTER `package`;
ALTER TABLE `ims_ewei_shop_rebate` MODIFY COLUMN `goodsid`  varchar(255) NOT NULL DEFAULT '' AFTER `pay_money`;
ALTER TABLE `ims_ewei_shop_member_level` ADD COLUMN `rechargemoney`  decimal(10,2) NOT NULL COMMENT '充值金额' AFTER `minconsume`;
ALTER TABLE `ims_ewei_shop_wxapp_sendticket` ADD COLUMN `getmax`  int(11) NOT NULL DEFAULT 0 COMMENT '最大可获取数，0为不限制' AFTER `title`;
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `fixed_pay`  tinyint(3) unsigned DEFAULT NULL COMMENT '收货方式1指定订金2折扣订金';
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `fixed_money`  decimal(10,2) DEFAULT NULL COMMENT '固定订金或折扣比例的金额';
ALTER TABLE `ims_ewei_shop_goods_option` ADD COLUMN `preprice`  decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '预售价格' AFTER `liveprice`;

ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `protocolshow`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '协议显示' AFTER `video`,  ADD COLUMN `protocolcontent`  text NOT NULL COMMENT '协议内容' AFTER `protocolshow`;
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `isprepay`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '是否预付',ADD COLUMN `prepayprice`  decimal(10,0) NOT NULL DEFAULT 0.00 COMMENT '预付价格' AFTER `isprepay`;
ALTER TABLE `ims_ewei_shop_merch_saler` ADD COLUMN `wxappopenid`  varchar(255) NULL COMMENT '小程序openid' AFTER `merchid`;
ALTER TABLE `ims_ewei_shop_store` ADD COLUMN `distributor`  varchar(255) NOT NULL DEFAULT '' COMMENT '配送员' AFTER `cates`;
AlTER TABLE  `ims_ewei_shop_store` ADD `orderNum` int(10) DEFAULT 0 COMMENT '订单数量' AFTER `displayorder`;
AlTER TABLE  `ims_ewei_shop_store` ADD `orderAmount` float(10,2) DEFAULT 0 COMMENT '订单总额' AFTER `orderNum`;
AlTER TABLE  `ims_ewei_shop_store` ADD `orderConversion` float(10,2) DEFAULT 0 COMMENT '订单转化率' AFTER `orderAmount`;
ALTER TABLE `ims_ewei_shop_rebate` ADD COLUMN `data`  text NOT NULL COMMENT '返利政策' AFTER `status`;

ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `isnotlogin`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '开启登录时才会生效。0为登录，1为不登录' AFTER `agentpower`;
ALTER TABLE `ims_ewei_shop_order` ADD COLUMN `isprepay`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否预付类型';
ALTER TABLE `ims_ewei_shop_order` ADD COLUMN `isprepaysuccess`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '预付订单状态';
ALTER TABLE `ims_ewei_shop_order` ADD COLUMN `balance`  decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '尾款金额';
AlTER TABLE  `ims_ewei_shop_merch_group` ADD `memberdiscount` tinyint(4) DEFAULT '0' COMMENT '商家入驻设置会员折扣价权限控制';
ALTER TABLE `ims_ewei_shop_coupon_taskdata` ADD COLUMN `fromtype`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '商户类型' AFTER `sendamount`, ADD COLUMN `frommerch`  int(11) NOT NULL DEFAULT 0 COMMENT '商户id' AFTER `fromtype`;
AlTER TABLE  `ims_ewei_shop_groupaward_billp` ADD `samegrade` decimal(10,2) DEFAULT 0 COMMENT '团队业绩同级分红';
AlTER TABLE  `ims_ewei_shop_commission_record` ADD `detail` varchar(255) DEFAULT '' COMMENT '团队业绩分红详细手机端分红展示';
AlTER TABLE  `ims_mc_credits_record` ADD `openid` varchar(50) DEFAULT '' COMMENT '关联小程序openid';
AlTER TABLE  `ims_ewei_shop_bottledoctorarticle` ADD `readnumber` int(10) DEFAULT 0 COMMENT '社群文章阅读量';
AlTER TABLE  `ims_ewei_shop_coupon_data` ADD `goodscates` varchar(255) NULL;
AlTER TABLE  `ims_ewei_shop_coupon_taskdata` ADD `goodscates` varchar(255) NULL;
AlTER TABLE  `ims_ewei_shop_order` ADD `isdeclaration` tinyint(3) DEFAULT '0' COMMENT '报单订单';
ALTER TABLE  `ims_ewei_shop_goods` ADD COLUMN `notselfverify`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '1为不支持自提，0为支持';
ALTER TABLE `ims_tdt_shop` ADD COLUMN `mobileauth`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '手机授权';
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `commission_agent` varchar(1000) NOT NULL DEFAULT '' COMMENT '商品代理返利';
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `has_commission_agent` tinyint(3) NOT NULL DEFAULT 0 COMMENT '独立代理返利';
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `memberlimit` int(10) NOT NULL DEFAULT 0 COMMENT '下级名额';
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `memberlimits` varchar(255) COMMENT '下级名额';
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `usermonthbuy` tinyint(3) DEFAULT 0 COMMENT '限制按月购买';
ALTER TABLE `ims_ewei_shop_goods_group` ADD COLUMN `buylimit` mediumtext COMMENT '商品组会员等级购买权限';
ALTER TABLE `ims_ewei_shop_goods_group` ADD COLUMN `isbuylimit` tinyint(3) DEFAULT 0 COMMENT '商品组会员等级购买权限开关';
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `down_time` varchar(255) DEFAULT 0  COMMENT '降级时间';
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `up_time` varchar(255) DEFAULT 0  COMMENT '升级时间';
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `be_downtime` varchar(255) DEFAULT 0  COMMENT '被降级时间';
ALTER TABLE `ims_ewei_shop_order` ADD COLUMN `server_status` tinyint(2) DEFAULT 0  COMMENT '服务状态 0为未服务,1为已服务,-1为上级拒绝服务';
ALTER TABLE `ims_ewei_shop_order` ADD COLUMN `server_detail` varchar(255) DEFAULT ''  COMMENT '服务详情';
ALTER TABLE `ims_ewei_shop_order` ADD COLUMN `isfree` tinyint(3) DEFAULT 0  COMMENT '是否免费 0 不免费，2免费';
ALTER TABLE `ims_ewei_shop_machine` ADD COLUMN `freetimes` int(5) DEFAULT 0  COMMENT '免费次数';
CREATE TABLE `ims_ewei_shop_machine_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `name` varchar(50) DEFAULT '',
  `params` varchar(1000) DEFAULT '',
  `createtime` int(10) DEFAULT '0' COMMENT '创建时间',
  `updtetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '自动更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `ims_ewei_shop_space_limit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT '记录类型',
  `times` int(11) DEFAULT NULL COMMENT '名额次数',
  `remark` text COMMENT '备注',
  `otheropenid` varchar(255) DEFAULT NULL COMMENT '被使用者openid',
  `createtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;


CREATE TABLE `ims_ewei_shop_invoice` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT COMMENT '发票记录表',
  `uniacid` int(11) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT '0' COMMENT '会员ID',
  `orderID` varchar(255) DEFAULT NULL COMMENT '订单ID，可为空',
  `expresscom` varchar(30) NOT NULL COMMENT '物流公司',
  `expresssn` varchar(50) NOT NULL COMMENT '物流单号',
  `express` varchar(255) DEFAULT NULL COMMENT '物流公司代码',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '开票金额',
  `raised` varchar(255) DEFAULT NULL COMMENT '发票抬头',
  `number` varchar(255) DEFAULT NULL COMMENT '纳税人识别号',
  `type` varchar(255) DEFAULT NULL COMMENT '发票类型',
  `content` varchar(255) DEFAULT NULL COMMENT '发票内容',
  `status` tinyint(1) DEFAULT NULL COMMENT '开具状态',
  `createTime` int(10) DEFAULT '0' COMMENT '创建时间',
  `updateTime` int(10) DEFAULT '0' COMMENT '最后一次更新时间',
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

ALTER TABLE `ims_ewei_shop_invoice`ADD COLUMN `payment`  tinyint(1) NULL AFTER `updateTime`;
ALTER TABLE `ims_ewei_shop_invoice`ADD COLUMN `raisedType`  tinyint(1) NULL AFTER `updateTime`;

ALTER TABLE `ims_ewei_shop_order`ADD COLUMN `invoice`  tinyint(3) NULL AFTER `fids`,ADD COLUMN `invoiceprice`  decimal(10,2) NULL AFTER `invoice`,ADD COLUMN `type`  varchar(255) NULL AFTER `invoiceprice`;

ALTER TABLE `ims_ewei_shop_order`ADD COLUMN `isCash`  tinyint(1) NULL AFTER `type`,ADD COLUMN `earnest`  decimal(10,2) NULL AFTER `isCash`;
ALTER TABLE `ims_ewei_shop_member_level` ADD COLUMN `content`  text NOT NULL COMMENT '会员须知' AFTER `background`;
ALTER TABLE `ims_ewei_shop_member` ADD `need_up` int(1) DEFAULT 0 COMMENT '会员是否申请升级';

CREATE TABLE `ims_ewei_shop_bottledoctor_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT NULL,
  `docname` varchar(50) DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `docheadimgurl` varchar(255) DEFAULT NULL,
  `communicationtype` tinyint(1) DEFAULT NULL COMMENT '交流类型',
  `enabled` tinyint(3) DEFAULT NULL COMMENT '是否显示',
  `categoryid` int(11) DEFAULT NULL COMMENT '咨询类型id',
  `communicationid` int(11) DEFAULT NULL,
  `docimages` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=204 DEFAULT CHARSET=utf8 PACK_KEYS=0;

ALTER TABLE `ims_ewei_shop_bottledoctor_answer`
ADD COLUMN `voiceTime`  int(11) NULL AFTER `voice`;

ALTER TABLE `ims_ewei_shop_bottledoctor_answer`
ADD COLUMN `mid`  varchar(255) NULL AFTER `voiceTime`;

ALTER TABLE `ims_ewei_shop_bottledoctor_answer`
ADD COLUMN `voice`  varchar(255) NULL AFTER `docimages`;

CREATE TABLE `ims_ewei_shop_bottledoctor_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `displayorder` tinyint(3) unsigned DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '1',
  `advimg` varchar(255) DEFAULT '',
  `advurl` varchar(500) DEFAULT '',
  `isrecommand` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_enabled` (`enabled`),
  KEY `idx_isrecommand` (`isrecommand`),
  KEY `idx_displayorder` (`displayorder`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_bottledoctor_communion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `emoticon` varchar(255) DEFAULT NULL,
  `images` text,
  `voice` varchar(255) DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `descripTion` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `communicationtype` tinyint(1) DEFAULT NULL COMMENT '交流类型',
  `enabled` tinyint(1) DEFAULT NULL COMMENT '是否显示',
  `categoryid` int(11) DEFAULT NULL COMMENT '咨询类型id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=177 DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_bottledoctorarticle_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL DEFAULT '',
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `isshow` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_category_name` (`category_name`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='话题分类';


CREATE TABLE `ims_ewei_shop_bottledoctorarticle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_title` varchar(255) NOT NULL DEFAULT '',
  `article_content` longtext,
  `article_category` int(11) NOT NULL DEFAULT '0',
  `article_date_v` varchar(20) NOT NULL DEFAULT '',
  `article_date` varchar(20) NOT NULL DEFAULT '',
  `article_mp` varchar(50) NOT NULL DEFAULT '',
  `article_author` varchar(20) NOT NULL DEFAULT '',
  `article_readnum_v` int(11) NOT NULL DEFAULT '0',
  `article_readnum` int(11) NOT NULL DEFAULT '0',
  `article_keyword` varchar(255) NOT NULL DEFAULT '',
  `article_state` int(1) NOT NULL DEFAULT '0',
  `uniacid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_article_title` (`article_title`),
  KEY `idx_article_content` (`article_content`(10)),
  KEY `idx_article_keyword` (`article_keyword`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='营销文章';

ALTER TABLE `ims_ewei_shop_bottledoctorarticle`
ADD COLUMN `article_rule_money`  decimal(10,0) NULL AFTER `uniacid`,
ADD COLUMN `article_rule_credit`  int(11) NULL AFTER `article_rule_money`,
ADD COLUMN `resp_img`  text NULL AFTER `article_rule_credit`,
ADD COLUMN `resp_desc`  text NULL AFTER `resp_img`,
ADD COLUMN `displayorder`  int(11) NULL AFTER `resp_desc`,
ADD COLUMN `article_visit`  tinyint(3) NULL AFTER `displayorder`;

CREATE TABLE `ims_ewei_shop_bottledoctor_decode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT NULL,
  `createTime` int(11) DEFAULT NULL,
  `decode` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=223 DEFAULT CHARSET=utf8 PACK_KEYS=0;

CREATE TABLE `ims_ewei_shop_bottledoctor_decodeset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

ALTER TABLE `ims_ewei_shop_bottledoctor_decodeset`
ADD COLUMN `timeSet`  int(11) NULL AFTER `enabled`;


ALTER TABLE `ims_ewei_shop_member`ADD COLUMN `communicationgrade`  tinyint(1) NOT NULL AFTER `independent`;
ALTER TABLE `ims_ewei_shop_member`ADD COLUMN `categoryid`  varchar(255) NULL DEFAULT '' COMMENT '社区交流模块id' AFTER `isnotlogin`;



ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `leveldeadline`  int(10) NOT NULL COMMENT '会员等级期限' AFTER `team_orders`;
ALTER TABLE `ims_ewei_shop_member_level` ADD COLUMN `background`  varchar(255) NOT NULL COMMENT '会员卡背景颜色' AFTER `rechargemoney`;

ALTER TABLE `ims_ewei_shop_goods`ADD COLUMN `fixed_pay`  int(10) NULL AFTER `PageUrl`,ADD COLUMN `fixed_money`  decimal(10,0) NULL AFTER `fiex_pay`;
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `store_id`  int(10) NOT NULL DEFAULT 0 COMMENT '门店id' AFTER `leveldeadline`;
ALTER TABLE `ims_ewei_shop_saler`  ADD COLUMN `wxappopenid`  varchar(255) NULL DEFAULT '' COMMENT '关联的小程序账号' AFTER `roleid`;

ALTER TABLE `ims_ewei_shop_order_goods`ADD COLUMN `dingjin`  decimal(10,2) NULL AFTER `content91`;
ALTER TABLE `ims_ewei_shop_member` ADD `credit3` decimal(10,2) DEFAULT '0.00' AFTER `credit2`,ADD `credit4` decimal(10,2) DEFAULT '0.00' AFTER `credit3`,ADD `credit5` decimal(10,2) DEFAULT '0.00' AFTER `credit4`,ADD `credit6` decimal(10,2) DEFAULT '0.00' AFTER `credit5`,ADD `credit20` decimal(10,2) DEFAULT '0.00' AFTER `credit6`;
ALTER TABLE `ims_ewei_shop_order` ADD `machineid` varchar(50) NOT NULL DEFAULT '' COMMENT '机器编号';
ALTER TABLE `ims_ewei_shop_rebate_join` ADD COLUMN `goodsid`  int(11) NULL COMMENT '商品id' AFTER `popenid`;

ALTER TABLE `ims_ewei_shop_order_goods` ADD
  `applytime100` int(11) DEFAULT '0',ADD
  `checktime100` int(10) DEFAULT '0',ADD
  `paytime100` int(11) DEFAULT '0',ADD
  `invalidtime100` int(11) DEFAULT '0',ADD
  `deletetime100` int(11) DEFAULT '0',ADD
  `content100` text,ADD
  `applytime99` int(11) DEFAULT '0',ADD
  `checktime99` int(10) DEFAULT '0',ADD
  `paytime99` int(11) DEFAULT '0',ADD
  `invalidtime99` int(11) DEFAULT '0',ADD
  `deletetime99` int(11) DEFAULT '0',ADD
  `content99` text,ADD
  `applytime98` int(11) DEFAULT '0',ADD
  `checktime98` int(10) DEFAULT '0',ADD
  `paytime98` int(11) DEFAULT '0',ADD
  `invalidtime98` int(11) DEFAULT '0',ADD
  `deletetime98` int(11) DEFAULT '0',ADD
  `content98` text,ADD
  `applytime97` int(11) DEFAULT '0',ADD
  `checktime97` int(10) DEFAULT '0',ADD
  `paytime97` int(11) DEFAULT '0',ADD
  `invalidtime97` int(11) DEFAULT '0',ADD
  `deletetime97` int(11) DEFAULT '0',ADD
  `content97` text,ADD
  `applytime96` int(11) DEFAULT '0',ADD
  `checktime96` int(10) DEFAULT '0',ADD
  `paytime96` int(11) DEFAULT '0',ADD
  `invalidtime96` int(11) DEFAULT '0',ADD
  `deletetime96` int(11) DEFAULT '0',ADD
  `content96` text;
  AlTER TABLE  `ims_ewei_shop_member_level` ADD `daylimit` decimal(10,2) DEFAULT '0.00' COMMENT '每日限额',ADD `minconsume` decimal(10,2) DEFAULT '0.00' COMMENT '月最低消费';
  ALTER TABLE `ims_ewei_shop_commission_level` ADD `commission11` decimal(10,2) DEFAULT '0.00',ADD `commission22` decimal(10,2) DEFAULT '0.00',ADD `commission33` decimal(10,2) DEFAULT '0.00';
  ALTER TABLE `ims_ewei_shop_order_goods` ADD `gstatus100` int(11) DEFAULT 0 ,ADD `gstatus99` int(11) DEFAULT 0 ,ADD `gstatus98` int(11) DEFAULT 0 ,ADD `gstatus97` int(11) DEFAULT 0 ,ADD `gstatus96` int(11) DEFAULT 0 ;

   ALTER TABLE `ims_ewei_shop_order_goods` ADD `gstatus95` int(11) DEFAULT 0 ,ADD `gstatus94` int(11) DEFAULT 0 ,ADD `gstatus93` int(11) DEFAULT 0 ,ADD `gstatus92` int(11) DEFAULT 0 ,ADD `gstatus91` int(11) DEFAULT 0 ;
   ALTER TABLE `ims_ewei_shop_fullback_log` ADD `type` tinyint(3) DEFAULT 0 COMMENT '全返类型 0 余额 1 积分';
   ALTER TABLE `ims_ewei_shop_fullback_goods` ADD `credit` tinyint(3) DEFAULT 0 COMMENT '全返帐户 0 余额 1 积分';

  ALTER TABLE `ims_ewei_shop_order_goods` ADD
  `gapplytime100` int(11) DEFAULT '0',ADD
  `gchecktime100` int(10) DEFAULT '0',ADD
  `gpaytime100` int(11) DEFAULT '0',ADD
  `ginvalidtime100` int(11) DEFAULT '0',ADD
  `gdeletetime100` int(11) DEFAULT '0',ADD
  `gcontent100` text,ADD
  `gapplytime99` int(11) DEFAULT '0',ADD
  `gchecktime99` int(10) DEFAULT '0',ADD
  `gpaytime99` int(11) DEFAULT '0',ADD
  `ginvalidtime99` int(11) DEFAULT '0',ADD
  `gdeletetime99` int(11) DEFAULT '0',ADD
  `gcontent99` text,ADD
  `gapplytime98` int(11) DEFAULT '0',ADD
  `gchecktime98` int(10) DEFAULT '0',ADD
  `gpaytime98` int(11) DEFAULT '0',ADD
  `ginvalidtime98` int(11) DEFAULT '0',ADD
  `gdeletetime98` int(11) DEFAULT '0',ADD
  `gcontent98` text,ADD
  `gapplytime97` int(11) DEFAULT '0',ADD
  `gchecktime97` int(10) DEFAULT '0',ADD
  `gpaytime97` int(11) DEFAULT '0',ADD
  `ginvalidtime97` int(11) DEFAULT '0',ADD
  `gdeletetime97` int(11) DEFAULT '0',ADD
  `gcontent97` text,ADD
  `gapplytime96` int(11) DEFAULT '0',ADD
  `gchecktime96` int(10) DEFAULT '0',ADD
  `gpaytime96` int(11) DEFAULT '0',ADD
  `ginvalidtime96` int(11) DEFAULT '0',ADD
  `gdeletetime96` int(11) DEFAULT '0',ADD
  `gcontent96` text;

  ALTER TABLE `ims_ewei_shop_order_goods` ADD
  `gapplytime95` int(11) DEFAULT '0',ADD
  `gchecktime95` int(10) DEFAULT '0',ADD
  `gpaytime95` int(11) DEFAULT '0',ADD
  `ginvalidtime95` int(11) DEFAULT '0',ADD
  `gdeletetime95` int(11) DEFAULT '0',ADD
  `gcontent95` text,ADD
  `gapplytime94` int(11) DEFAULT '0',ADD
  `gchecktime94` int(10) DEFAULT '0',ADD
  `gpaytime94` int(11) DEFAULT '0',ADD
  `ginvalidtime94` int(11) DEFAULT '0',ADD
  `gdeletetime94` int(11) DEFAULT '0',ADD
  `gcontent94` text,ADD
  `gapplytime93` int(11) DEFAULT '0',ADD
  `gchecktime93` int(10) DEFAULT '0',ADD
  `gpaytime93` int(11) DEFAULT '0',ADD
  `ginvalidtime93` int(11) DEFAULT '0',ADD
  `gdeletetime93` int(11) DEFAULT '0',ADD
  `gcontent93` text,ADD
  `gapplytime92` int(11) DEFAULT '0',ADD
  `gchecktime92` int(10) DEFAULT '0',ADD
  `gpaytime92` int(11) DEFAULT '0',ADD
  `ginvalidtime92` int(11) DEFAULT '0',ADD
  `gdeletetime92` int(11) DEFAULT '0',ADD
  `gcontent92` text,ADD
  `gapplytime91` int(11) DEFAULT '0',ADD
  `gchecktime91` int(10) DEFAULT '0',ADD
  `gpaytime91` int(11) DEFAULT '0',ADD
  `ginvalidtime91` int(11) DEFAULT '0',ADD
  `gdeletetime91` int(11) DEFAULT '0',ADD
  `gcontent91` text;

  CREATE TABLE `ims_mihua_sq_price` (
  `price_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `min_price` decimal(10,0) DEFAULT '0',
  `max_price` decimal(10,0) DEFAULT '0',
  `price_name` varchar(32) DEFAULT NULL,
  `is_rent` int(1) DEFAULT '0',
  `orderby` tinyint(3) DEFAULT '100',
  PRIMARY KEY (`price_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='房屋价格区间表';
CREATE TABLE `ims_mihua_sq_type` (
  `type_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `type_name` varchar(32) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='房屋类型表';
ALTER TABLE `ims_mihua_sq_info` ADD `price_id` int(11) NOT NULL DEFAULT '0',ADD `area_id` int(11) NOT NULL DEFAULT '0',ADD `type_id` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `ims_mihua_sq_info` ADD `price` int(11) NOT NULL DEFAULT '0' COMMENT '总价',ADD `square_price` int(11) NOT NULL DEFAULT '0' COMMENT '平方价',ADD `square_area` int(11) NOT NULL DEFAULT '0' COMMENT '面积';
ALTER TABLE `ims_mihua_sq_info` ADD `unit` varchar(11) NOT NULL DEFAULT '元' COMMENT '单位';
ALTER TABLE `ims_mihua_sq_channel` ADD `is_rent` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 买房模块 1租房模块';
ALTER TABLE `ims_mihua_sq_info` ADD `house_name` varchar(32) NOT NULL DEFAULT '' COMMENT '楼盘';
ALTER TABLE `ims_zhjd_seller` ADD `is_top` int(1) NOT NULL DEFAULT '0' COMMENT '是否加入精选 加入1 不加入0',ADD `is_onsale` int(1) NOT NULL DEFAULT '0' COMMENT '限时特价 0不是 1 是';
ALTER TABLE `ims_zhjd_platform` ADD `banner` text COMMENT '轮播图';
ALTER TABLE `ims_zhjd_order` ADD `yj_cost` decimal(10,2) NOT NULL COMMENT '押金';
ALTER TABLE `ims_zhjd_user` ADD `limit_time` int(5) NOT NULL DEFAULT 0 COMMENT '充值限制次数';
ALTER TABLE `ims_zhjd_order` ADD `refund_amount` decimal(10,2) NOT NULL DEFAULT '0.0' COMMENT '退款金额',ADD `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '退款备注';
ALTER TABLE `ims_zhjd_seller` ADD `areas` varchar(255) NOT NULL DEFAULT '' COMMENT '房屋面积',ADD `bed_type` varchar(255) NOT NULL DEFAULT '' COMMENT '床型',ADD `bed_add` int(1) NOT NULL DEFAULT '0' COMMENT '0 不可加床 1 可加床',ADD `people_num` varchar(5) NOT NULL DEFAULT '' COMMENT '可住人数',ADD `check_in` varchar(50) NOT NULL DEFAULT '' COMMENT '入住时间',ADD `check_out` varchar(50) NOT NULL DEFAULT '' COMMENT '退房时间'; 
ALTER TABLE `ims_zhjd_ptsms` ADD `secret` varchar(50) NOT NULL DEFAULT '' ,ADD `qianming` varchar(50) NOT NULL DEFAULT '';
ALTER TABLE `ims_zhjd_order` ADD `is_comment` int(1) NOT NULL DEFAULT '0' COMMENT '是否评论';
ALTER TABLE `ims_zhjd_order` ADD `is_online` int(1) NOT NULL DEFAULT '0' COMMENT '是否属于在线付';
ALTER TABLE `ims_zhjd_platform` ADD `recharge` char(255) DEFAULT '' COMMENT '充值活动';
ALTER TABLE ims_zhjd_member_level ADD `type` INT(1) NOT NULL DEFAULT 0 COMMENT '会员类型 0 按订单自动升级 1 一次性充值升级';
ALTER TABLE ims_zhjd_member_level ADD `recharge` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额';
ALTER TABLE ims_zhjd_member_level ADD `rebate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '会员下单返利金额';
ALTER TABLE `ims_zhjd_seller` MODIFY COLUMN `areas`  varchar(255) NOT NULL DEFAULT '' ;
ALTER TABLE ims_zhjd_seller ADD `setting` text NOT NULL DEFAULT '' COMMENT '房屋配置';
ALTER TABLE ims_zhjd_seller ADD `bed_num` varchar(50)  NOT NULL DEFAULT '' COMMENT '房间数量',ADD `room_area` varchar(50) NOT NULL DEFAULT '' COMMENT '房间面积';
ALTER TABLE `ims_zhjd_ptsms` MODIFY COLUMN `tpl_id` varchar(50) NOT NULL DEFAULT '';

ALTER TABLE `ims_zhjd_platform` ADD `logo1` varchar(255) NOT NULL DEFAULT '',ADD `logo2` varchar(255) NOT NULL DEFAULT '',ADD `logo3` varchar(255) NOT NULL DEFAULT ''  COMMENT '首页项目logo';

ALTER TABLE `ims_ewei_shop_task2_set` ADD COLUMN `upgrade_link`  varchar(255) NOT NULL DEFAULT '' COMMENT '升级链接' AFTER `bg_img`;
ALTER TABLE `ims_zhjd_platform` ADD `banner_link` varchar(255) COMMENT '轮播链接';
ALTER TABLE `ims_account` ADD `istest` tinyint(3) NOT NULL DEFAULT '1';
ALTER TABLE `ims_zhjd_order` ADD `idcard` varchar(18) NOT NULL DEFAULT '' COMMENT '身份证号';
ALTER TABLE `ims_zhjd_order` ADD `reduce` decimal(10,2) NOT NULL COMMENT '减免金额',ADD `note` varchar(50) NOT NULL COMMENT '减免原因';
ALTER TABLE `ims_zhjd_coupons` MODIFY COLUMN `seller_id` varchar(1000) NOT NULL DEFAULT '';
ALTER TABLE `ims_zhjd_order` ADD `pay_way` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式';
ALTER TABLE `ims_ewei_shop_member_address` ADD COLUMN `lng`  varchar(255) NOT NULL DEFAULT '' AFTER `streetdatavalue`, ADD COLUMN `lat`  varchar(255) NOT NULL DEFAULT '' AFTER `lng`;
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `merchid`  int(11) NOT NULL DEFAULT 0 ;
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `areaAgent`  int(1) NOT NULL DEFAULT 0 ;
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `agentprovince`  varchar(50) NOT NULL DEFAULT '',ADD COLUMN `agentcity`  varchar(50) NOT NULL DEFAULT '',ADD COLUMN `agentarea`  varchar(50) NOT NULL DEFAULT '' ;
ALTER TABLE `ims_mc_members` MODIFY COLUMN `credit1` decimal(10,2) NOT NULL,MODIFY COLUMN `credit2` decimal(10,2) NOT NULL,MODIFY COLUMN `credit3` decimal(10,2) NOT NULL,MODIFY COLUMN `credit4` decimal(10,2) NOT NULL,MODIFY COLUMN `credit5` decimal(10,2) NOT NULL;
ALTER TABLE `ims_ewei_shop_commission_level` ADD COLUMN `level` int(10) NOT NULL DEFAULT '0' COMMENT '分销级别';
ALTER TABLE `ims_mc_mapping_fans` ADD COLUMN `lasttime` int(10) NOT NULL DEFAULT '0' COMMENT '最后访问时间';



ALTER TABLE `ims_ewei_shop_member` ADD `agent95` int(11) DEFAULT 0 ,ADD `agent94` int(11) DEFAULT 0 ,ADD `agent93` int(11) DEFAULT 0 ,ADD `agent92` int(11) DEFAULT 0 ,ADD `agent91` int(11) DEFAULT 0 ;
ALTER TABLE `ims_ewei_shop_order` ADD `agent95` int(11) DEFAULT 0 ,ADD `agent94` int(11) DEFAULT 0 ,ADD `agent93` int(11) DEFAULT 0 ,ADD `agent92` int(11) DEFAULT 0,ADD `agent91` int(11) DEFAULT 0 ;
ALTER TABLE `ims_ewei_shop_order_goods` ADD `status95` int(11) DEFAULT 0 ,ADD `status94` int(11) DEFAULT 0 ,ADD `status93` int(11) DEFAULT 0 ,ADD `status92` int(11) DEFAULT 0 ,ADD `status91` int(11) DEFAULT 0 ;
ALTER TABLE `ims_ewei_shop_member` ADD `fids` varchar(15000) NOT NULL DEFAULT '' COMMENT '上级id',ADD INDEX `fids` (`fids`);
ALTER TABLE `ims_ewei_shop_member` ADD `team_orders` int(10) NOT NULL DEFAULT 0 COMMENT '下级订单总数';
ALTER TABLE `ims_ewei_shop_order` ADD `fids` varchar(15000) NOT NULL DEFAULT '' COMMENT '上级id',ADD INDEX `fids` (`fids`);
ALTER TABLE `ims_ewei_shop_member` ADD `initialid` int(11) NOT NULL DEFAULT 0 COMMENT '源会员id';
ALTER TABLE `ims_ewei_shop_order` ADD `initialid` int(11) NOT NULL DEFAULT 0 COMMENT '源会员id';
ALTER TABLE `ims_ewei_shop_member` ADD `independent` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否独立';
ALTER TABLE `ims_hulu_like_member` 
ADD `birthplace` varchar(100) COMMENT '出生地',
ADD `id_card` varchar(18),
ADD `id_card_img` varchar(255),
ADD `constellation` varchar(20) COMMENT '星座',
ADD `nation` varchar(100) COMMENT '名族',
ADD `profession` varchar(100) COMMENT '职业',
ADD `marriage` varchar(100) COMMENT '婚姻情况',
ADD `marriage_time` varchar(100) COMMENT '想什么时候结婚',
ADD `income` varchar(20) COMMENT '月收入',
ADD `child` varchar(20) COMMENT '子女情况',
ADD `qq` varchar(100);
ALTER TABLE `ims_hulu_like_member` ADD `domicile` varchar(100) COMMENT '居住地';

ALTER TABLE `ims_ewei_shop_order_goods` ADD
  `applytime95` int(11) DEFAULT '0',ADD
  `checktime95` int(10) DEFAULT '0',ADD
  `paytime95` int(11) DEFAULT '0',ADD
  `invalidtime95` int(11) DEFAULT '0',ADD
  `deletetime95` int(11) DEFAULT '0',ADD
  `content95` text,ADD
  `applytime94` int(11) DEFAULT '0',ADD
  `checktime94` int(10) DEFAULT '0',ADD
  `paytime94` int(11) DEFAULT '0',ADD
  `invalidtime94` int(11) DEFAULT '0',ADD
  `deletetime94` int(11) DEFAULT '0',ADD
  `content94` text,ADD
  `applytime93` int(11) DEFAULT '0',ADD
  `checktime93` int(10) DEFAULT '0',ADD
  `paytime93` int(11) DEFAULT '0',ADD
  `invalidtime93` int(11) DEFAULT '0',ADD
  `deletetime93` int(11) DEFAULT '0',ADD
  `content93` text,ADD
  `applytime92` int(11) DEFAULT '0',ADD
  `checktime92` int(10) DEFAULT '0',ADD
  `paytime92` int(11) DEFAULT '0',ADD
  `invalidtime92` int(11) DEFAULT '0',ADD
  `deletetime92` int(11) DEFAULT '0',ADD
  `content92` text,ADD
  `applytime91` int(11) DEFAULT '0',ADD
  `checktime91` int(10) DEFAULT '0',ADD
  `paytime91` int(11) DEFAULT '0',ADD
  `invalidtime91` int(11) DEFAULT '0',ADD
  `deletetime91` int(11) DEFAULT '0',ADD
  `content91` text;


CREATE TABLE `ims_mihua_sq_info_booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `info_id` int(11) DEFAULT NULL,
  `uniacid` int(11) DEFAULT NULL,
  `openid` varchar(100) NOT NULL,
  `house_name` varchar(100) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `book_date` varchar(20) DEFAULT NULL,
  `book_time` varchar(20) DEFAULT NULL,
  `place` varchar(150) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1 未处理 2已处理',
  `addtime` int(11) unsigned DEFAULT '0',
  `updatetime` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='看房预约表';
CREATE TABLE `ims_mihua_sq_house_require` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `openid` varchar(100) NOT NULL,
  `mobile` varchar(100) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `house_type` varchar(255) DEFAULT NULL,
  `build_type` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1 未处理 2已处理',
  `addtime` int(11) unsigned DEFAULT '0',
   `updatetime` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='房屋需求表';
CREATE TABLE `ims_mihua_sq_data_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `checktext` varchar(255) NOT NULL DEFAULT '' COMMENT '选项',
  `thumb1` varchar(255) DEFAULT NULL COMMENT '首页背景图',
  `thumb2` varchar(255) DEFAULT NULL COMMENT '次页背景图',
  `contract` text NOT NULL COMMENT '协议',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='房产信息设置表';
CREATE TABLE `ims_zhjd_balance_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `give` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '赠送金额'
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '来源类型1 消费 2 充值',
  `remark` varchar(55) NOT NULL COMMENT '备注',
  `create_time` datetime NOT NULL COMMENT '时间',
  `order_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='余额明细表';
CREATE TABLE `ims_zhjd_collect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '酒店搜藏表';
CREATE TABLE `ims_zhjd_travel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL COMMENT '用户id',
  `title` varchar(100) NOT NULL COMMENT '标题',
  `city` varchar(50) NOT NULL COMMENT '城市',
  `content` text NOT NULL COMMENT '详细内容',
  `img` text NOT NULL COMMENT '轮播图',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `status` int(1) NOT NULL DEFAULT 0 COMMENT '状态',
  `location` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '发布地址',
  `travel_time` date NOT NULL COMMENT '出发时间',
  `travel_fee` varchar(10) NOT NULL COMMENT '人均费用',
  `days` int(10) NOT NULL DEFAULT 0 COMMENT '天数',
  `people` int(10) NOT NULL DEFAULT 0 COMMENT '人数',
  `comments` int(10) NOT NULL DEFAULT 0 COMMENT '评论数',
  `views` int(10) NOT NULL DEFAULT 0 COMMENT '浏览量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT '旅行攻略表';
update ims_modules_bindings set title='批次管理' where module='zmcn_fw' and do='zmfwpee';
AlTER TABLE  `ims_zmcn_fw_batch` ADD `createdata` tinyint(1) DEFAULT '0' COMMENT '建立数据' AFTER `validity`;
CREATE TABLE `ims_zmcn_fw_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) DEFAULT '0',
  `batchid` int(11) DEFAULT '0' COMMENT '批次编号',
  `codetype` int(11) DEFAULT '0' COMMENT '数据类型',
  `largecode` varchar(255) DEFAULT NULL COMMENT '最大箱',
  `bigcode` varchar(255) DEFAULT NULL COMMENT '大箱',
  `middlecode` varchar(255) DEFAULT NULL COMMENT '中箱',
  `smallcode` varchar(255) DEFAULT NULL COMMENT '小箱',
  `code` varchar(255) DEFAULT '',
  `checkcode` varchar(255) DEFAULT NULL COMMENT '验证码',
  `opencode` varchar(255) DEFAULT NULL COMMENT '物流码，对外公开',
  `outcode` varchar(255) DEFAULT NULL COMMENT '外部导入的数据，目前只支持盒码',
  `addtime` int(10) DEFAULT '0',
  `deliveryID` int(11) DEFAULT '0' COMMENT '出货记录ID',
  `firsttime` int(11) DEFAULT '0' COMMENT '第一次查询时间',
  `firstip` varchar(30) DEFAULT NULL COMMENT '第一次查询IP',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `batchid` (`batchid`),
  KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
CREATE TABLE `ims_zmcn_fw_deliveyRrecord` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `code` varchar(255) DEFAULT NULL COMMENT '码',
  `direction` varchar(255) DEFAULT NULL COMMENT '方向send为出货，return为退货',
  `adminID` int(11) DEFAULT '0',
  `sendID` int(11) DEFAULT '0' COMMENT '出货者',
  `sendInfo` text,
  `receiveID` int(11) DEFAULT '0' COMMENT '收到货用户ID',
  `receiveInfo` text,
  `complete` tinyint(1) DEFAULT '0' COMMENT '0未完成,1已完成',
  `createTime` int(11) DEFAULT '0',
  `updateTime` int(11) DEFAULT '0',
  `ip` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='出货记录表';
ALTER TABLE `ims_zmcn_fw_data` ADD COLUMN `deliveryID`  int(11) default 0 COMMENT '出货记录ID' AFTER `addtime`;
CREATE TABLE `ims_ewei_shop_task2_favorite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `taskid` int(10) DEFAULT '0',
  `openid` varchar(50) DEFAULT '',
  `deleted` tinyint(1) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `merchid` int(11) DEFAULT '0',
  `type` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_goodsid` (`taskid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_deleted` (`deleted`),
  KEY `idx_createtime` (`createtime`)
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=utf8;

CREATE TABLE `ims_zmcn_fw_admin` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `type` varchar(255) DEFAULT '' COMMENT '类型，admin为管理员,agent为经销商',
  `openid` varchar(255) DEFAULT NULL COMMENT 'openid',
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `province` varchar(255) DEFAULT '' COMMENT '省',
  `city` varchar(255) DEFAULT '' COMMENT '市',
  `district` varchar(255) DEFAULT '' COMMENT '区',
  `setting` text CHARACTER SET latin1 COMMENT '设置，包括权限等',
  `createTime` int(11) DEFAULT '0' COMMENT '创建时间',
  `updateTime` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT '一物一码管理员表';
CREATE TABLE `ims_zmcn_fw_agent` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `type` varchar(255) DEFAULT '' COMMENT '类型，admin为管理员,agent为经销商',
  `openid` varchar(255) DEFAULT NULL COMMENT 'openid',
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `province` varchar(255) DEFAULT '' COMMENT '省',
  `city` varchar(255) DEFAULT '' COMMENT '市',
  `district` varchar(255) DEFAULT '' COMMENT '区',
  `setting` text CHARACTER SET latin1 COMMENT '设置，包括权限等',
  `createTime` int(11) DEFAULT '0' COMMENT '创建时间',
  `updateTime` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT '一物一码经销商表';

CREATE TABLE `ims_zhjd_system_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `seller_id` int(11) COMMENT '酒店id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `secondtitle` varchar(255) NOT NULL DEFAULT '' COMMENT '副标题',
  `content` text COMMENT '详细内容',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT '系统通知';

CREATE TABLE `ims_ewei_shop_task2_set` (
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `entrance` tinyint(1) NOT NULL DEFAULT '0',
  `keyword` varchar(10) NOT NULL DEFAULT '',
  `cover_title` varchar(20) NOT NULL DEFAULT '',
  `cover_img` varchar(255) NOT NULL DEFAULT '',
  `cover_desc` varchar(255) NOT NULL DEFAULT '',
  `msg_pick` text NOT NULL,
  `msg_progress` text NOT NULL,
  `msg_finish` text NOT NULL,
  `msg_follow` text NOT NULL,
  `isnew` tinyint(1) NOT NULL DEFAULT '0',
  `bg_img` varchar(255) NOT NULL DEFAULT '../addons/ewei_shopv2/plugin/task/static/images/sky.png',
  PRIMARY KEY (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `ims_ewei_shop_task2_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `taskid` int(11) DEFAULT '0',
  `openid` varchar(50) DEFAULT '',
  `nickname` varchar(50) DEFAULT '',
  `headimgurl` varchar(255) DEFAULT '',
  `level` tinyint(3) DEFAULT '0',
  `content` varchar(255) DEFAULT '',
  `images` text,
  `createtime` int(11) DEFAULT '0',
  `deleted` tinyint(3) DEFAULT '0',
  `append_content` varchar(255) DEFAULT '',
  `append_images` text,
  `reply_content` varchar(255) DEFAULT '',
  `reply_images` text,
  `append_reply_content` varchar(255) DEFAULT '',
  `append_reply_images` text,
  `istop` tinyint(3) DEFAULT '0',
  `checked` tinyint(3) NOT NULL DEFAULT '0',
  `replychecked` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_createtime` (`createtime`),
  KEY `idx_orderid` (`taskid`)
) ENGINE=MyISAM AUTO_INCREMENT=307 DEFAULT CHARSET=utf8;
CREATE TABLE `ims_ewei_shop_rebate` (
  `rebate_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `addtime` int(11) NOT NULL,
  `rebate_data` text NOT NULL,
  `pay_money` decimal(10,0) NOT NULL,
  `goodsid` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`rebate_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE `ims_ewei_shop_rebate_join` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `join_num` int(11) NOT NULL COMMENT '参与人数',
  `addtime` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '完成状态',
  `popenid` varchar(255) NOT NULL COMMENT '上级openid',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=268 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE `ims_ewei_shop_rebate_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `join_id` int(11) DEFAULT '0',
  `join_user` varchar(255) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  `orderid` int(11) NOT NULL,
  `reward_data` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=291 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE `ims_kmd_caipiao_type` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0' COMMENT '公众号ID',
  `type` varchar(255) DEFAULT NULL,
  `issueId` int(11) DEFAULT NULL,
  `lotterytime` int(11) DEFAULT NULL,
  `number` mediumtext,
  `createTime` int(10) DEFAULT '0' COMMENT '创建时间',
  `updateTime` int(10) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE `ims_kmd_caipiao_record` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `type` varchar(11) DEFAULT NULL,
  `issueId` varchar(255) DEFAULT NULL,
  `number` mediumtext,
  `iswin` int(11) DEFAULT '0',
  `createTime` int(11) DEFAULT '0',
  `updateTime` int(11) DEFAULT '0',
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_wxapp_sendticket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `cpid` varchar(200) NOT NULL,
  `expiration` int(11) NOT NULL DEFAULT '0',
  `starttime` int(11) DEFAULT NULL,
  `endtime` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '新人礼包',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
CREATE TABLE `ims_ewei_shop_perm_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `uniacid` int(11) DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT '',
  `date` date DEFAULT NULL COMMENT '日期',
  `first` time DEFAULT NULL,
  `second` time DEFAULT NULL,
  `hour` float(11,2) DEFAULT '0.00',
  `overtime` float(11,2) DEFAULT '0.00',
  `status` int(1) DEFAULT '0' COMMENT '0为待审核,1为已审',
  `createtime` int(11) DEFAULT '0',
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='出勤记录表';
alter table ims_tyzm_diamondvote_reply add joinrule mediumtext;
alter table ims_tyzm_diamondvote_reply add joinurl varchar(200);
alter table ims_tyzm_diamondvote_voteuser add updateimages mediumtext;

CREATE TABLE `ims_ewei_shop_danmu_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `headimgurl` varchar(255) NOT NULL DEFAULT '',
  `nickname` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_groupaward_bill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `billno` varchar(100) DEFAULT '',
  `paytype` int(11) DEFAULT '0',
  `year` int(11) DEFAULT '0',
  `month` int(11) DEFAULT '0',
  `week` int(11) DEFAULT '0',
  `ordercount` int(11) DEFAULT '0',
  `ordermoney` decimal(10,2) DEFAULT '0.00',
  `paytime` int(11) DEFAULT '0',
  `totalAgent` int(11) DEFAULT '0',
  `totalProfit` decimal(10,2) DEFAULT '0.00',
  `count100` decimal(10,2) DEFAULT '0.00' COMMENT '代理数量',
  `count99` decimal(10,2) DEFAULT '0.00',
  `count98` decimal(10,2) DEFAULT '0.00',
  `count97` decimal(10,2) DEFAULT '0.00',
  `count96` decimal(10,2) DEFAULT '0.00',
  `count95` decimal(10,2) DEFAULT '0.00',
  `count94` decimal(10,2) DEFAULT '0.00',
  `count93` decimal(10,2) DEFAULT '0.00',
  `count92` decimal(10,2) DEFAULT '0.00',
  `count91` decimal(10,2) DEFAULT '0.00',
  `performance100` decimal(10,2) DEFAULT '0.00' COMMENT '总业绩',
  `performance99` decimal(10,2) DEFAULT '0.00',
  `performance98` decimal(10,2) DEFAULT '0.00',
  `performance97` decimal(10,2) DEFAULT '0.00',
  `performance96` decimal(10,2) DEFAULT '0.00',
  `performance95` decimal(10,2) DEFAULT '0.00',
  `performance94` decimal(10,2) DEFAULT '0.00',
  `performance93` decimal(10,2) DEFAULT '0.00',
  `performance92` decimal(10,2) DEFAULT '0.00',
  `performance91` decimal(10,2) DEFAULT '0.00',
  `createtime` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '0',
  `starttime` int(11) DEFAULT '0',
  `endtime` int(11) DEFAULT '0',
  `confirmtime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`) USING BTREE,
  KEY `idx_paytype` (`paytype`) USING BTREE,
  KEY `idx_createtime` (`createtime`) USING BTREE,
  KEY `idx_paytime` (`paytime`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE,
  KEY `idx_month` (`month`) USING BTREE,
  KEY `idx_week` (`week`) USING BTREE,
  KEY `idx_year` (`year`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `ims_ewei_shop_groupaward_billp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `billid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `payno` varchar(255) DEFAULT '',
  `paytype` tinyint(3) DEFAULT '0',
  `totalSale` decimal(10,2) DEFAULT '0.00',
  `totalProfit` decimal(10,2) DEFAULT '0.00',
  `realProfit` decimal(10,2) DEFAULT '0.00',
  `status` tinyint(3) DEFAULT '0',
  `reason` varchar(255) DEFAULT '',
  `paytime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_billid` (`billid`) USING BTREE,
  KEY `idx_uniacid` (`uniacid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE `ims_ewei_shop_groupaward_billo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `billid` int(11) DEFAULT '0',
  `orderid` int(11) DEFAULT '0',
  `ordermoney` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `idx_billid` (`billid`) USING BTREE,
  KEY `idx_uniacid` (`uniacid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;
CREATE TABLE `ims_ewei_shop_groupaward_fullback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `billid` int(11) DEFAULT '0',
  `billpid` int(11) DEFAULT '0',
  `openid` varchar(55) DEFAULT '',
  `money` decimal(10,2) DEFAULT '0.00',
  `year` int(11) DEFAULT '0',
  `month` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_billid` (`billid`) USING BTREE,
  KEY `idx_uniacid` (`uniacid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;


CREATE TABLE `ims_mihua_sq_delegate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `delegate` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `city_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `ispass` int(11) DEFAULT '0',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

CREATE TABLE `ims_mihua_sq_employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `job` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `area_id` int(11) DEFAULT NULL,
  `ispass` int(11) DEFAULT '0',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
CREATE TABLE `ims_ewei_shop_machine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) NOT NULL DEFAULT '',
  `uniacid` int(11) NOT NULL,
  `machineid` varchar(50) DEFAULT '',
  `tel` varchar(15) DEFAULT '',
  `note` varchar(100) DEFAULT '',
  `status` tinyint(3) DEFAULT '0' COMMENT '0关闭，1开启',
  `createtime` int(10) NOT NULL DEFAULT '0',
  `runingtime` int(10) DEFAULT NULL COMMENT '开机时间',
  PRIMARY KEY (`id`),
  KEY `idx_openid` (`openid`),
  KEY `idx_uniacid` (`uniacid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE `ims_ewei_shop_machine_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `level` tinyint(3) DEFAULT '0',
  `openid` varchar(50) DEFAULT '',
  `diyid` varchar(50) DEFAULT '',
  `createtime` int(11) DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0,未审核 1、已审核 ',
  `checktime1` int(10) NOT NULL DEFAULT '0',
  `checktime2` int(10) NOT NULL DEFAULT '0',
  `applylevel` tinyint(3) NOT NULL DEFAULT '0',
  `applystatus` tinyint(3) NOT NULL DEFAULT '0' COMMENT '默认0，1 待审核 2，店家已审',
  `deposit` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `store_openid` varchar(50) NOT NULL DEFAULT '',
  `minimum` decimal(10,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `idx_openid` (`openid`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `ims_ewei_shop_machine_commission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `orderid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '' COMMENT '支付人openid',
  `times` int(11) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `createtime` int(11) DEFAULT '0',
  `storecommission` decimal(10,2) DEFAULT '0.00' COMMENT '店家奖励',
  `invitercommission` decimal(10,2) DEFAULT '0.00' COMMENT '推店方奖励',
  `poolcommission` decimal(10,2) DEFAULT '0.00' COMMENT '平台奖金池',
  `statecommission` decimal(10,2) DEFAULT '0.00' COMMENT '平台奖金',
  `charityfunds` decimal(10,2) DEFAULT '0.00' COMMENT '爱心基金',
  `sharecommission` decimal(10,2) DEFAULT '0.00' COMMENT '个人共享奖金',
  `commission1` decimal(10,2) DEFAULT '0.00' COMMENT '一级奖励',
  `commission2` decimal(10,2) DEFAULT '0.00' COMMENT '二级奖励',
  `commission3` decimal(10,2) DEFAULT '0.00' COMMENT '三级奖励',
  `machineid` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_orderid` (`orderid`),
  KEY `idx_createtime` (`createtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `ims_ewei_shop_machine_commission_apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `openid` varchar(50) NOT NULL DEFAULT '',
  `money` decimal(10,2) DEFAULT '0.00',
  `realmoney` decimal(10,2) NOT NULL DEFAULT '0.00',
  `applyno` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `deductionmoney` decimal(10,2) NOT NULL DEFAULT '0.00',
  `charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` int(3) NOT NULL DEFAULT '0',
  `createtime` int(10) DEFAULT '0',
  `checktime` int(10) DEFAULT '0',
  `paybegin` decimal(10,2) DEFAULT '0.00',
  `payend` decimal(10,2) DEFAULT '0.00',
  `logids` varchar(200) NOT NULL DEFAULT '0',
  `remark` varchar(100) DEFAULT '',
  `paytime` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `ims_ewei_shop_machine_commission_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `openid` varchar(50) DEFAULT '',
  `num` decimal(10,2) DEFAULT '0.00',
  `credittype` varchar(50) DEFAULT '',
  `remark` varchar(100) DEFAULT '',
  `createtime` int(10) DEFAULT '0',
  `checktime` int(10) DEFAULT '0',
  `status` int(3) NOT NULL DEFAULT '0',
  `cid` int(11) NOT NULL DEFAULT '0',
  `machineid` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `ims_ewei_shop_machine_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `levelname` varchar(50) DEFAULT '',
  `levelprefix` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_machine_month_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(50) NOT NULL DEFAULT '',
  `times` int(11) DEFAULT '0' COMMENT '开机次数',
  `createtime` int(11) DEFAULT '0',
  `monthtime` int(10) NOT NULL DEFAULT '0',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '月度最低消费基数',
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `machineid` varchar(50) NOT NULL DEFAULT '' COMMENT '机器编号',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`) USING BTREE,
  KEY `idx_createtime` (`createtime`) USING BTREE,
  KEY `idx_monthtime` (`monthtime`) USING BTREE,
  KEY `idx_openid` (`openid`) USING BTREE,
  KEY `idx_machineid` (`machineid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `ims_ewei_shop_machine_agent` ADD COLUMN `applytime` int(10) NOT NULL DEFAULT '0' COMMENT '申请时间';
ALTER TABLE `ims_ewei_shop_machine` ADD COLUMN `after_sale` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0 正常状态 1 售后维修状态';
ALTER TABLE `ims_ewei_shop_machine` ADD COLUMN `qrstatus` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0 未下载。1已下载', ADD COLUMN `sendstatus` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0未发货 1，已发货';
ALTER TABLE `ims_ewei_shop_machine_agent` ADD COLUMN `storeid` int(11) NOT NULL DEFAULT '0' COMMENT '店铺id',ADD COLUMN `isowner` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0 不可自主管理 ，1 可以自主管理';
ALTER TABLE `ims_ewei_shop_machine` ADD COLUMN `bindid` int(11) NOT NULL DEFAULT '0' COMMENT '绑定者id';
ALTER TABLE `ims_ewei_shop_machine` ADD COLUMN `sendtime` int(10) NOT NULL DEFAULT '0' COMMENT '发货时间';
ALTER TABLE `ims_ewei_shop_machine_level` ADD COLUMN `bind` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否自动绑定机器';
ALTER TABLE `ims_ewei_shop_order` ADD COLUMN `mstatus` tinyint(3) not null default '0' comment '是否月度结算';
ALTER TABLE `ims_ewei_shop_machine` ADD COLUMN `storagestatus` tinyint(3) NOT NULL DEFAULT '0' COMMENT '入库状态';
ALTER TABLE `ims_ewei_shop_machine_agent` ADD COLUMN `addressid` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `ims_ewei_shop_machine_level` ADD COLUMN `num` int(11) NOT NULL DEFAULT '0' COMMENT '自动绑定数量';
ALTER TABLE `ims_ewei_shop_machine_level` ADD COLUMN `levelid` int(11) NOT NULL DEFAULT '0' COMMENT '会员等级';
ALTER TABLE `ims_ewei_shop_machine_agent` ADD COLUMN `machine1` varchar(50) DEFAULT '',ADD COLUMN `machine2` varchar(50) DEFAULT '',ADD COLUMN `machine3` varchar(50) DEFAULT '',ADD COLUMN `storemachine` varchar(50) DEFAULT '';
ALTER TABLE `ims_ewei_shop_groups_set` ADD COLUMN `agent` tinyint NOT NULL COMMENT '判断是否支持分销',ADD COLUMN `reward` mediumtext NOT NULL COMMENT '拼团成功后的各种返利';
ALTER TABLE `ims_ewei_shop_groups_goods` ADD COLUMN `agent` tinyint NOT NULL COMMENT '判断是否支持分销',ADD COLUMN `reward` mediumtext NOT NULL COMMENT '拼团成功后的各种返利';
ALTER TABLE `ims_ewei_shop_groups_order` ADD COLUMN `isrebate` tinyint NOT NULL DEFAULT '0' COMMENT '判断是否已经返利';
ALTER TABLE `ims_ewei_shop_groups_order` ADD COLUMN `optionid` int NOT NULL DEFAULT '0';
AlTER TABLE  `ims_ewei_shop_commission_apply` ADD `recordid` text DEFAULT '' COMMENT '关联ewei_shop_commission_record的id';
AlTER TABLE  `ims_ewei_shop_commission_apply` ADD `recordamount` decimal(10,2) DEFAULT 0.00 COMMENT '此次提现其它佣金金额合计';
ALTER TABLE `ims_ewei_shop_machine_commission_log` ADD COLUMN `ownermachine` varchar(50) DEFAULT '';
ALTER TABLE `ims_ewei_shop_machine` ADD COLUMN `device_id` varchar(50) NOT NULL DEFAULT '' COMMENT '控制器自编码';
ALTER TABLE `ims_ewei_shop_machine` ADD COLUMN `endtime` int(10) NOT NULL DEFAULT 0 COMMENT '结束时间';
ALTER TABLE `ims_ewei_shop_machine_commission_apply` ADD COLUMN `bankname` varchar(50) NOT NULL DEFAULT '',ADD COLUMN `bankcard` varchar(50) NOT NULL DEFAULT '', ADD COLUMN `realname` varchar(50) NOT NULL DEFAULT '';
ALTER TABLE `ims_ewei_shop_machine_level` ADD COLUMN  `createtime` int(10) DEFAULT '0' COMMENT '创建时间', ADD `updtetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '自动更新时间';
ALTER TABLE `ims_ewei_shop_coupon_data`  ADD COLUMN `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券金额',ADD COLUMN `coupontype` tinyint(3) NOT NULL DEFAULT '0' COMMENT '优惠券类型 默认0 1表示支持持续抵扣';
ALTER TABLE `ims_ewei_shop_coupon_data`  ADD COLUMN `frommerch` int(11) NOT NULL DEFAULT '0' COMMENT '来源商家';
ALTER TABLE `ims_ewei_shop_coupon_data`  ADD COLUMN `fromtype` tinyint(3) NOT NULL DEFAULT '0' COMMENT '商家类型 2 B端 1 F端';
ALTER TABLE `ims_ewei_shop_coupon_taskdata`  ADD COLUMN `sendamount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '发送数量';
ALTER TABLE `ims_ewei_shop_coupon` ADD COLUMN `backrate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '返还比例';
ALTER TABLE `ims_ewei_shop_coupon_goodsendtask` ADD COLUMN `allgoods` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否是全场商品 1 是 0 不是';
ALTER TABLE `ims_ewei_shop_merch_user`  ADD COLUMN `merchtype` tinyint(3) NOT NULL DEFAULT '0' COMMENT '商家类型 0 B端 1 F端';
ALTER TABLE `ims_ewei_shop_member_log`  ADD COLUMN `machineid` varchar(50) NOT NULL DEFAULT '' COMMENT '机器码';
ALTER TABLE `ims_ewei_shop_coupon_data`  ADD COLUMN `machineid` varchar(50) NOT NULL DEFAULT '' COMMENT '机器码';
ALTER TABLE `ims_ewei_shop_order` MODIFY `couponid` varchar(255) NOT NULL DEFAULT '' COMMENT '优惠券id';
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN coupondeduct decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券抵扣' after `deduct`;
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN grossindex decimal(10,2) NOT NULL DEFAULT '0.00'
 COMMENT '毛利指数';
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN first_month_record decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '首月业绩';
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN first_agent_time int(10) NOT NULL DEFAULT '0' COMMENT '首次升级对应时间';
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `commissions` varchar(255) NOT NULL DEFAULT 0.00 COMMENT '供应商独立代理返利';
ALTER  TABLE `ims_ewei_shop_machine` ADD COLUMN `level` int(10) NOT NULL DEFAULT 0 COMMENT '设备套餐组';
ALTER TABLE `ims_ewei_shop_order` ADD COLUMN `runningtime` int(10) NOT NULL DEFAULT 0 COMMENT '设备运行时间 单位min';
ALTER TABLE `ims_ewei_shop_commission_record` ADD COLUMN `orderid` int(10) NOT NULL DEFAULT 0 COMMENT '关联订单ID';
ALTER TABLE `ims_ewei_shop_machine` ADD COLUMN `storeid` int(10) NOT NULL DEFAULT 0 COMMENT '店家id';
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `useamount` decimal(10,2) DEFAULT '0.00' COMMENT '排队奖已领额度';
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN   `queue` int(10) DEFAULT 0 COMMENT '排队奖排队数';
CREATE TABLE `ims_ewei_shop_machine_reward_pool` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `orderid` int(11) DEFAULT '0',
  `amount` DECIMAL(10,2) default '0.00',
  `createtime` int(10) DEFAULT '0',
  `updtetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(3) DEFAULT '0',
  `remark` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT '奖金池表';

CREATE TABLE `ims_ewei_shop_machine_balance_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(50) NOT NULL DEFAULT '',
  `createtime` int(11) DEFAULT '0',
  `monthtime` int(10) NOT NULL DEFAULT '0',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '数量',
  `remark` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '备注',
  `machineid` varchar(50) NOT NULL DEFAULT '' COMMENT '机器编号',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`) USING BTREE,
  KEY `idx_createtime` (`createtime`) USING BTREE,
  KEY `idx_monthtime` (`monthtime`) USING BTREE,
  KEY `idx_openid` (`openid`) USING BTREE,
  KEY `idx_machineid` (`machineid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE `ims_ewei_shop_kmd_payments` (
  `itemID` int(11) NOT NULL AUTO_INCREMENT,
  `paymentID` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `tradeTime` datetime DEFAULT '0000-00-00 00:00:00',
  `orderSn` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `statusValue` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `tradetype` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `createTime` int(10) NOT NULL DEFAULT '0',
  `updateTime` int(10) NOT NULL DEFAULT '0',
  `uniacid` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  PRIMARY KEY (`itemID`)
) ENGINE=MyISAM AUTO_INCREMENT=4689 DEFAULT CHARSET=utf8;
CREATE TABLE `ims_ewei_shop_machine_delivery_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `openid` varchar(50) DEFAULT '',
  `createtime` int(11) DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0,未发货 1、已发货 3、已收货',
  `sendtime` int(10) NOT NULL DEFAULT '0' COMMENT '发货时间',
  `finishtime` int(10) NOT NULL DEFAULT '0' COMMENT '完成时间',
  `express` varchar(50) NOT NULL DEFAULT '',
  `expresscom` varchar(50) NOT NULL DEFAULT '',
  `expresssn` varchar(50) NOT NULL DEFAULT '' COMMENT '物流单号',
  `machineids` varchar(100) NOT NULL DEFAULT '',
  `machineid` varchar(100) NOT NULL DEFAULT '',
  `addressid` int(11) NOT NULL DEFAULT '0',
  `remark` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_addressid` (`addressid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE `ims_ewei_shop_kmd_demo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `createtime` int(10) DEFAULT '0' COMMENT '创建时间',
  `updtetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '自动更新时间',
  `status` tinyint(3) DEFAULT '0',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `frominfo` varchar(255) DEFAULT ''  COMMENT '来源信息',
  PRIMARY KEY (`id`),
   KEY `idx_openid` (`openid`) USING BTREE
  KEY `idx_uniacid` (`uniacid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'demo表';
CREATE TABLE if not exists `ims_ewei_shop_commission_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT 0,
  `openid` varchar(255) NOT NULL DEFAULT '',
  `uid` int(10) NOT NULL DEFAULT 0 COMMENT '预留字段',
  `status`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '对应提现记录表状态',
  `createtime`  varchar(255) NOT NULL DEFAULT '' COMMENT '创建时间',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注返利信息',
  `amount` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '数量:返利金额',
  `frominfo`  varchar(255)  NOT NULL DEFAULT '' COMMENT '来源信息',
  `content`  varchar(255)  COMMENT '提现备注信息',
  `credittype`  varchar(50) NOT NULL DEFAULT '' COMMENT '返利金额类型',
  `updatetime`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '自动更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_openid` (`openid`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='其他佣金存放表记录表';
CREATE TABLE if not exists `ims_ewei_shop_fund_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `status`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态',
  `createtime`  int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注返利信息',
  `amount` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '数量:返利金额',
  `machineid`  varchar(255) NOT NULL DEFAULT '' COMMENT '机器码',
  `orderid`  int(11) NOT NULL DEFAULT 0 COMMENT '订单号',
  `updatetime`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '自动更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='公司分成';
CREATE TABLE `ims_ewei_shop_member_apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `openid` varchar(255) NOT NULL DEFAULT '',
  `memberdata` text NOT NULL COMMENT '表单数据',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态',
  `level` int(11) NOT NULL DEFAULT '0' COMMENT '升级的会员id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_merch_member_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `level` int(11) DEFAULT '0',
  `merchid` int(11) DEFAULT '0',
  `levelname` varchar(50) DEFAULT '',
  `ordermoney` decimal(10,2) DEFAULT '0.00',
  `ordercount` int(10) DEFAULT '0',
  `discount` decimal(10,2) DEFAULT '0.00',
  `enabled` tinyint(3) DEFAULT '0',
  `enabledadd` tinyint(1) DEFAULT '0',
  `commission` decimal(10,2) DEFAULT '0.00' COMMENT '返利比例',
  `daylimit` decimal(10,2) DEFAULT '0.00' COMMENT '每日限额',
  `minconsume` decimal(10,2) DEFAULT '0.00' COMMENT '月最低消费',
  `rechargemoney` decimal(10,2) NOT NULL COMMENT '充值金额',
  `background` varchar(255) NOT NULL COMMENT '会员卡背景颜色',
  `content` text NOT NULL COMMENT '会员须知',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_level` (`level`),
  KEY `idx_levelname` (`levelname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_merch_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `level` int(11) DEFAULT '0',
  `merchid` int(11) DEFAULT NULL,
  `agentid` int(11) DEFAULT '0',
  `openid` varchar(50) DEFAULT '',
  `status` tinyint(1) DEFAULT '0',
  `isagent` tinyint(1) DEFAULT '0',
  `agent100` int(11) DEFAULT '0',
  `agent99` int(11) DEFAULT '0',
  `agent98` int(11) DEFAULT '0',
  `agent97` int(11) DEFAULT '0',
  `agent96` int(11) DEFAULT '0',
  `agent95` int(11) DEFAULT '0',
  `agent94` int(11) DEFAULT '0',
  `agent93` int(11) DEFAULT '0',
  `agent92` int(11) DEFAULT '0',
  `agent91` int(11) DEFAULT '0',
  `fids` varchar(15000) NOT NULL DEFAULT '' COMMENT '上级id',
  `leveldeadline` int(10) NOT NULL COMMENT '会员等级期限',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_shareid` (`agentid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_status` (`status`),
  KEY `idx_isagent` (`isagent`),
  KEY `idx_level` (`level`),
  KEY `fids` (`fids`(333))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_diyform_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(50) DEFAULT '',
  `diyfieldsdata` text,
  `status` tinyint(1) DEFAULT '0',
  `createTime` int(11) DEFAULT '0',
  `checktime` int(11) DEFAULT '0',
  `nickname` text,
  `diyformfields` text,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `bg` varchar(255) DEFAULT NULL,
  `data` mediumtext,
  `diyinfo` mediumtext,
  `waittext` text,
  `isdefault` int(11) DEFAULT NULL,
  `keyword2` varchar(255) DEFAULT NULL,
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_commission_queuepaylog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) DEFAULT '0' NOT NULL,
  `openid` varchar(50) DEFAULT '' NOT NULL,
  `createtime` varchar(50) DEFAULT '' NOT NULL,
  `amount` decimal(10,2) DEFAULT '0.00' NOT NULL COMMENT '总额度',
  `paytype` tinyint(4) DEFAULT 0 NOT NULL COMMENT '提现方式',
  `remark` varchar(255) DEFAULT '' NOT NULL COMMENT '备注',
  `status` tinyint(4) DEFAULT 0 NOT NULL COMMENT '状态',
  `orderid` varchar(100) DEFAULT '' NOT NULL COMMENT '订单id',
   PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_package_category_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `pid` int(11) NOT NULL,
  `goodsid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `option` varchar(255) NOT NULL,
  `goodssn` varchar(255) NOT NULL,
  `productsn` varchar(255) NOT NULL,
  `hasoption` tinyint(3) NOT NULL DEFAULT '0',
  `marketprice` decimal(10,2) DEFAULT '0.00',
  `packageprice` decimal(10,2) DEFAULT '0.00',
  `commission1` decimal(10,2) DEFAULT '0.00',
  `commission2` decimal(10,2) DEFAULT '0.00',
  `commission3` decimal(10,2) DEFAULT '0.00',
  `packid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

ALTER TABLE `ims_ewei_shop_package`
ADD COLUMN `ispackage`  tinyint(1) NULL DEFAULT NULL AFTER `displayorder`,
ADD COLUMN `categoryid`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `ispackage`,
ADD COLUMN `ratio`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `categoryid`,
ADD COLUMN `static`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `ratio`,
ADD COLUMN `packageway`  tinyint(1) NULL DEFAULT NULL AFTER `static`;


ALTER TABLE `ims_ewei_shop_package_goods`
ADD COLUMN `categoryid`  int(11) NULL DEFAULT NULL AFTER `commission3`,
ADD COLUMN `categorystatus`  tinyint(1) NULL DEFAULT NULL AFTER `categoryid`;

CREATE TABLE `ims_ewei_shop_commission_weekceiling` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) DEFAULT '0' NOT NULL,
  `openid` varchar(50) DEFAULT '' NOT NULL,
  `weekceilingtime` varchar(50) DEFAULT '' NOT NULL COMMENT '周封顶限制时间',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '一周内已领金额',
   PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_openid` (`openid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_package_goodsgroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `goodsid` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `optionid` int(11) DEFAULT '0',
  `openid` varchar(255) NOT NULL DEFAULT '',
  `selected` tinyint(1) DEFAULT NULL,
  `createtime` int(11) DEFAULT NULL,
  `typeid` int(11) DEFAULT '0',
  `cid` int(11) NOT NULL DEFAULT '0',
  `diyformfields` text,
  `fields` text NOT NULL,
  `type` tinyint(1) DEFAULT '0',
  `diyformid` int(11) DEFAULT '0',
  `diyformdata` text,
  `carrier_realname` varchar(255) DEFAULT '',
  `carrier_mobile` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_deleted` (`deleted`),
  KEY `idx_cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `ims_ewei_shop_bottledoctor_decodeset`
ADD COLUMN `communicationSet`  tinyint(1) NULL AFTER `timeSet`;

ALTER TABLE `ims_ewei_shop_diyform_information`
ADD COLUMN `remarks`  varchar(255) NULL AFTER `diyformfields`;
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `share_holder` varchar(255) NOT NULL DEFAULT '' COMMENT '项目股东';

ALTER TABLE `ims_ewei_shop_member`
ADD COLUMN `datavalue`  varchar(50) NULL AFTER `need_up`;
ALTER TABLE `ims_ewei_shop_goods_group` MODIFY COLUMN `goodsids`  text NOT NULL ;
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `renttype` tinyint(3) NOT NULL DEFAULT 0 COMMENT '租售类型 0 普通购买 1 永久租用 2限期租用';
ALTER TABLE `ims_ewei_shop_goods_option` ADD COLUMN `period` int(11) NOT NULL DEFAULT 0 COMMENT '租期 天';
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_machine_rent_record`(
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `uniacid` int(11) NOT NULL DEFAULT 0,
 `openid` varchar(50) NOT NULL DEFAULT '',
 `macid` varchar(50) NOT NULL DEFAULT '',
 `createtime` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
 `begintime` int(10) NOT NULL DEFAULT 0 COMMENT '开始时间',
 `endtime` int(10) NOT NULL DEFAULT 0 COMMENT '结束时间',
 `periodtime` int(10) NOT NULL DEFAULT 0 COMMENT '租用时间 单位天',
 `status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '状态 2租用状态 1永久有效 -1设备到期',
 `orderid` int(11) NOT NULL DEFAULT 0 COMMENT '关联订单号',
 `merchid` int(11) NOT NULL DEFAULT 0 COMMENT '商家id',
 `goodsid` int(11) NOT NULL DEFAULT 0 COMMENT '商品id',
 `message` text COMMENT '售后信息',
 `statusa` tinyint(3) DEFAULT '0' COMMENT '售后状态',
 PRIMARY KEY (`id`),
 KEY `index_openid` (`openid`),
 KEY `index_uniacid` (`uniacid`),
 KEY `index_macid` (`macid`),
 KEY `index_status` (`status`)
 ) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT '设备租用记录表';
 CREATE TABLE `ims_ewei_shop_machine_error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `macid` varchar(50) NOT NULL DEFAULT '',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `merchid` int(11) NOT NULL DEFAULT '0' COMMENT '商家ID',
  `error_code` varchar(10) NOT NULL DEFAULT '' COMMENT '故障码',
  `message`  varchar(255) NOT NULL DEFAULT '' COMMENT '故障信息',
  `status` tinyint(3) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `index_uniacid` (`uniacid`),
  KEY `index_macid` (`macid`),
  KEY `index_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备故障记录';
 CREATE TABLE `ims_ewei_shop_backpic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `pics` text NOT NULL COMMENT '图片地址',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='轮播广告';

 CREATE TABLE IF NOT EXISTS `ims_ewei_shop_store_withdrawal`(
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `uniacid` int(11) NOT NULL DEFAULT 0,
 `openid` varchar(50) NOT NULL DEFAULT '',
 `createtime` varchar(50) NOT NULL DEFAULT 0 COMMENT '创建时间',
 `amount` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '金额',
 `paytype` tinyint(3) NOT NULL DEFAULT 0 COMMENT '提现方式',
 `status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '状态 0可提现 1待审核 2审核通过 3已打款 -1审核不通过',
 `orderid` int(11) NOT NULL DEFAULT 0 COMMENT '关联订单号',
 `storeid` int(10) NOT NULL DEFAULT 0 COMMENT '门店id',
 `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注信息',
 `realname` varchar(255) NOT NULL DEFAULT '' COMMENT '真实姓名',
 `alipay` varchar(255) NOT NULL DEFAULT '' COMMENT '支付宝账号',
 `bankname` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡类型',
 `bankcard` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡账号',
 `sendmoney` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '已发送金额',
 PRIMARY KEY (`id`),
 KEY `index_openid` (`openid`),
 KEY `index_uniacid` (`uniacid`),
 KEY `index_status` (`status`)
 ) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT '门店提现记录表';

 CREATE TABLE `ims_hulu_like_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT 0,
  `level` int(11) DEFAULT '0',
  `upmoney` int(11) DEFAULT '0',
  `mlevelname` varchar(255) DEFAULT '',
  `alevelname` varchar(255) DEFAULT '',
  `look` int(11) DEFAULT '0' COMMENT '查看其他会员次数',
  `lookwho` varchar(255) DEFAULT '' COMMENT '查看哪种会员',
  `send` int(11) DEFAULT '0' COMMENT '发站内信次数',
  `free_party` int(11) DEFAULT '0' COMMENT '免费参与活动次数',
  `search` int(1) DEFAULT '0' COMMENT '检索会员类型',
  `commission` varchar(255) DEFAULT '' COMMENT '佣金比例',
  `status` tinyint(3) DEFAULT '1' ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `ims_hulu_like_user` 
ADD `starlevel` tinyint(3) COMMENT '星级' DEFAULT '1',
ADD `levelid` tinyint(3) COMMENT '等级id' DEFAULT '0';
ALTER TABLE `ims_hulu_like_user` ADD `fids` varchar(255) DEFAULT '';

 CREATE TABLE `ims_hulu_like_user_operations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT 0,
  `openid` varchar(255) DEFAULT '',
  `lookarr` longtext,
  `sendarr` int(11) DEFAULT 0,
  `free_party` int(11) DEFAULT '0' COMMENT '免费参与活动次数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ims_hulu_like_user_commission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT 0,
  `ordersn` varchar(255) DEFAULT '',
  `commission` varchar(255) DEFAULT '',
  `credittime` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `ims_hulu_like_request`
ADD `he_asset` varchar(255) DEFAULT '' COMMENT '他的资产要求',
ADD `other` longtext COMMENT '其他,选',
ADD `smoke` varchar(255) DEFAULT '' COMMENT '吸烟',
ADD `drink` varchar(255) DEFAULT '' COMMENT '喝酒',
ADD `car` varchar(255) DEFAULT '' COMMENT '有车',
ADD `house` varchar(255) DEFAULT '' COMMENT '有房',
ADD `loan` varchar(255) DEFAULT '' COMMENT '贷款',
ADD `hobby` varchar(255) DEFAULT '' COMMENT '爱好',
ADD `my_asset` varchar(255) DEFAULT '' COMMENT '我的资产',
ADD `thought` tinyint(1) DEFAULT '0' COMMENT '婚姻爱情的看法',
ADD `bear` varchar(255) COMMENT '生孩子的看法',
ADD `housework` tinyint(1) DEFAULT '0' COMMENT '谁做家务',
ADD `relation` varchar(255) DEFAULT '' COMMENT '两人关系',
ADD `tour` varchar(255) DEFAULT '' COMMENT '旅游意向',
ADD `economic` varchar(255) DEFAULT '' COMMENT '家庭经济',
ADD `merit` varchar(255) DEFAULT '' COMMENT '优点',
ADD `defect` varchar(255) DEFAULT '' COMMENT '缺点',
ADD `my_looks` int(11) COMMENT '我的颜值,选',
ADD `he_looks` int(11) COMMENT '他的颜值,选',
ADD `my_int` longtext DEFAULT '' COMMENT '自我介绍',
ADD `require` longtext DEFAULT '' COMMENT '对象要求',
ADD `star` tinyint(1) DEFAULT '0' COMMENT '星',
ADD `my_say` longtext DEFAULT '' COMMENT '对未来的他说,选';

ALTER TABLE `ims_hulu_like_member` ADD `star` tinyint(1) DEFAULT '0' COMMENT '星';
ALTER TABLE `ims_hulu_like_level` ADD `star` tinyint(1) COMMENT '星';

CREATE TABLE `ims_hulu_like_user_ident` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT 0,
  `openid` varchar(255) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `idcard` varchar(30) DEFAULT '',
  `front` varchar(255) DEFAULT '',
  `back` varchar(255) DEFAULT '',
  `createtime` varchar(255) DEFAULT '',
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `ims_hulu_like_member` ADD `is_ok` tinyint(1) DEFAULT '0' COMMENT '是否填写完毕';

  CREATE TABLE IF NOT EXISTS `ims_ewei_shop_member_collect`(
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `uniacid` int(11) NOT NULL DEFAULT 0,
 `openid` varchar(50) NOT NULL DEFAULT '',
 `createtime` varchar(50) NOT NULL DEFAULT 0 COMMENT '提交申请时间',
 `amount` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '金额',
 `paytype` tinyint(3) NOT NULL DEFAULT 0 COMMENT '充值类型',
 `status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '状态 0已提交充值申请 1审核通过 -1审核不通过',
 `paytime` varchar(50) NOT NULL DEFAULT 0 COMMENT '审核时间',
 `content` varchar(255) NOT NULL DEFAULT '' COMMENT '审核描述',
 `images` varchar(1000) NOT NULL DEFAULT '' COMMENT '充值图片凭证',
 PRIMARY KEY (`id`),
 KEY `index_openid` (`openid`),
 KEY `index_uniacid` (`uniacid`),
 KEY `index_status` (`status`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '转账充值表';
 
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_groups_option`(
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `uniacid` int(11) NOT NULL DEFAULT 0,
 `gid` int(11) NOT NULL DEFAULT 0,
 `goodsid` int(11) NOT NULL DEFAULT 0,
 `name` varchar(255) NOT NULL DEFAULT '',
 `marketprice` varchar(255) NOT NULL DEFAULT '',
 `single_price` varchar(255) NOT NULL DEFAULT '',
 `price` varchar(255) NOT NULL DEFAULT '',
 `stock` varchar(255) NOT NULL DEFAULT '',
 `specs` varchar(255) NOT NULL DEFAULT '',
 PRIMARY KEY (`id`)
 ) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT '多规格拼团表';

 ALTER TABLE `ims_hulu_like_tixian` 
 ADD `jifen` varchar(20) DEFAULT 0 COMMENT '积分',
 ADD `type` tinyint(3) DEFAULT 0 COMMENT '0:余额,1:积分';

ALTER TABLE `ims_hulu_like_money` ADD `jifen` varchar(20) DEFAULT 0 COMMENT '积分';
ALTER TABLE `ims_hulu_like_money` ADD `type` tinyint(3) DEFAULT 0 COMMENT '0:余额,1:积分';

ALTER TABLE `ims_hulu_like_active`
  ADD `active_star` tinyint(3) DEFAULT 0 COMMENT '星级限制',
  ADD `active_level` tinyint(3) DEFAULT 0 COMMENT '等级限制';

ALTER TABLE `ims_hulu_like_make`
  ADD `aliyun_appKey` varchar(50) DEFAULT '',
  ADD `aliyun_appSecret` varchar(50) DEFAULT '',
  ADD `aliyun_templateCode` varchar(50) DEFAULT '',
  ADD `aliyun_signName` varchar(50) DEFAULT '';
ALTER TABLE `ims_hulu_like_active` ADD `active_pictrue` varchar(255) DEFAULT '' COMMENT '活动海报';

ALTER TABLE `ims_hulu_like_user_ident`
 ADD `front1` varchar(255) DEFAULT '',
 ADD `back1` varchar(255) DEFAULT '';

ALTER TABLE `ims_hulu_like_make` ADD `guanzhu` varchar(255) DEFAULT '';
ALTER TABLE `ims_hulu_like_make` ADD `zhuyi` longtext DEFAULT '';
ALTER TABLE `ims_hulu_like_user` ADD `dingtime` varchar(255) DEFAULT '';

CREATE TABLE `ims_hulu_like_ding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT 0,
  `dingtime` varchar(255) DEFAULT '',
  `money` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `ims_hulu_like_order` 
  ADD `order_vip_level` varchar(5) DEFAULT '',
  ADD `order_free` varchar(5) DEFAULT '',
  ADD `order_dingtime` varchar(255) DEFAULT '';
ALTER TABLE `ims_hulu_like_active` ADD `old_pictrue` longtext DEFAULT '';
ALTER TABLE `ims_hulu_like_make` ADD `tui_bg` varchar(255) DEFAULT '';

ALTER TABLE `ims_ewei_shop_order_refund`
ADD COLUMN `buyerexpresssn`  varchar(100) NULL DEFAULT '' AFTER `merchid`;
CREATE TABLE `ims_ewei_shop_member_upgrade` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL DEFAULT 0,
  `agentid` int(10) NOT NULL DEFAULT 0,
  `agentopenid` varchar(100) NOT NULL DEFAULT '',
  `status` int(10) NOT NULL DEFAULT 0,
  `level` int(10) NOT NULL DEFAULT 0,
  `upgradetime` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT '感恩奖记录表';

ALTER TABLE `ims_ewei_shop_diyform_information`ADD COLUMN `mobile`  varchar(11) NULL DEFAULT '' AFTER `remarks`;


ALTER TABLE `ims_hulu_like_more`
 ADD `more_tixian_cardname` varchar(255) DEFAULT '',
 ADD `more_tixian_card` varchar(255) DEFAULT '';

 ALTER TABLE `ims_ewei_shop_category` ADD COLUMN `firstrebate`  tinyint(1) NULL AFTER `level`;

 ALTER TABLE `ims_ewei_shop_commission_record`
ADD COLUMN `recommend`  int(10) NOT NULL DEFAULT 0 COMMENT '炫氏美业vip推荐:1为推荐VIP，2推荐总代,0不返利' AFTER `month`;

ALTER TABLE `ims_ewei_shop_commission_record`
ADD COLUMN `agenttier`  int(10) NOT NULL DEFAULT 0 COMMENT '上级的级别:1一级，2二级，3三级' AFTER `recommend`;

CREATE TABLE `ims_ewei_shop_member_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(5) NOT NULL DEFAULT 0,
  `openid` varchar(255) DEFAULT '',
  `goodsid` int(10) DEFAULT 0,
  `optionid` int(10) DEFAULT 0,
  `status` tinyint(4) DEFAULT 0,
  `stock`  int(10) DEFAULT 0,
  `pid`   int(10)  DEFAULT 0,   
  PRIMARY KEY (`id`),
  KEY `index_openid` (`openid`),
  KEY `index_uniacid` (`uniacid`),
  KEY `index_status` (`stock`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_member_inventory_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(5) NOT NULL DEFAULT 0,
  `jopenid` varchar(255) DEFAULT '',
  `yopenid` varchar(255) DEFAULT '',
  `orderid` int(10) DEFAULT 0,
  `status` tinyint(4) DEFAULT 0,
  `total`  int(10) DEFAULT 0,
  `remark`  varchar(255) DEFAULT '',
  `createtime` varchar(80) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `index_jopenid` (`jopenid`),
  KEY `index_yuniacid` (`yopenid`),
KEY `index_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `ims_ewei_shop_order` ADD `upload_image` varchar(255) DEFAULT '' COMMENT '转账凭证';
ALTER TABLE `ims_ewei_shop_order` ADD `is_jinhuo` tinyint(1) DEFAULT 0 COMMENT '判断是否为进货订单';
ALTER TABLE `ims_ewei_shop_order` ADD `agent_superior` varchar(255) DEFAULT '' COMMENT '上级代理';
ALTER TABLE `ims_ewei_shop_commission_record` ADD `where_from` varchar(50) DEFAULT '' COMMENT '判断佣金来源';

CREATE TABLE `ims_ewei_shop_queued_rebate` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
`ogid` int(11) NOT NULL DEFAULT '0' COMMENT '保存了order_goods表中的id',
`uniacid` int(11) NOT NULL COMMENT '公众号id',
`openid` varchar(100) NOT NULL COMMENT '排队的代理商openid',
`status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态,0未返利,1已返利.2不参与返利',
`createtime` int(11) NOT NULL COMMENT '创建时间,根据此字段可以排序出进入时间',
`price` decimal(11,0) NOT NULL DEFAULT '0' COMMENT '之前购买礼包的价格,作为以后排队返金额的依据',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `ims_ewei_shop_groups_goods`
ADD COLUMN `op_more`  varchar(100) NULL COMMENT '开启商品多规格拼团';



ALTER TABLE `ims_hulu_like_user` 
  ADD COLUMN `isding` tinyint(1) DEFAULT 0,
  ADD COLUMN `isrecommend` tinyint(1) DEFAULT 0;

ALTER TABLE `ims_hulu_like_active` ADD COLUMN `avatar` varchar(255) DEFAULT '';

CREATE TABLE `oauth_access_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_ACCESS_TOKEN` (`access_token`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `oauth_authorization_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `authorization_code` varchar(40) DEFAULT '',
  `client_id` varchar(80) DEFAULT '',
  `user_id` varchar(80) DEFAULT '0',
  `redirect_uri` varchar(2000) DEFAULT '',
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` text,
  `id_token` varchar(1000) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_CODE` (`authorization_code`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `oauth_clients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(80) DEFAULT '',
  `client_secret` varchar(80) DEFAULT '',
  `client_name` varchar(120) DEFAULT '',
  `redirect_uri` varchar(2000) DEFAULT '',
  `grant_types` varchar(80) DEFAULT '',
  `scope` varchar(4000) DEFAULT '',
  `user_id` varchar(80) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_APP_SECRET` (`client_id`,`client_secret`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `oauth_public_keys` (
  `client_id` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) DEFAULT NULL,
  `private_key` varchar(2000) DEFAULT NULL,
  `encryption_algorithm` varchar(100) DEFAULT 'RS256'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `oauth_refresh_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL DEFAULT '',
  `user_id` varchar(80) DEFAULT '',
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_REFRESH_TOKEN` (`refresh_token`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `oauth_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(80) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `oauth_users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(80) DEFAULT '',
  `password` varchar(80) DEFAULT '',
  `first_name` varchar(80) DEFAULT '',
  `last_name` varchar(80) DEFAULT '',
  `email` varchar(80) DEFAULT '',
  `email_verified` tinyint(1) DEFAULT '0',
  `scope` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ims_ewei_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT 0,
  `route_name` varchar(60) NULL COMMENT '路线名称',
  `route_length` varchar(10) NULL COMMENT '路程长度',
  `first_run_time` int(11) NULL COMMENT '首班车',
  `last_run_time` int(11) NULL COMMENT '末班车',
  `shop_time` int(11) NULL COMMENT '停站时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '1使用2停用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_route_station` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT 0,
  `route_id` int(11) NOT NULL DEFAULT 0 COMMENT '路程id',
  `station_name` varchar(40) COMMENT '站点名称',
  `reach_time` varchar(20) COMMENT '到站时间/发车时间',
  `route_length` varchar(20) COMMENT '路程长度',
  `first_run_time` varchar(20) COMMENT '首班车',
  `last_run_time` varchar(20) COMMENT '末班车',
  `lng` varchar(30) DEFAULT '',
  `lat` varchar(30) DEFAULT '',
  `address` varchar(60) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_route_bus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT 0,
  `route_id` int(11) NOT NULL DEFAULT 0,
  `station_id` int(11) NOT NULL DEFAULT 0,
  `driver_id` varchar(255) NOT NULL DEFAULT 0 COMMENT '随车工作人员(member表id)',
  `plate_num` varchar(20) COMMENT '车牌号',
  `seat` tinyint(3) DEFAULT 0 COMMENT '座位数',
  `passenger` tinyint(3) DEFAULT 0 COMMENT '乘客数',
  `tel` varchar(11) COMMENT '联系电话',
  `status` tinyint(1) DEFAULT 0 COMMENT '0未发车1已发车',
  `stop` tinyint(1) DEFAULT 0 COMMENT '0靠站1离站',
  `run_time` int(11) DEFAULT 0 COMMENT '当前发车时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ims_ewei_shop_shoptips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `displayorder` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `rtitle` varchar(255) NOT NULL,
  `thumb` varchar(500) NOT NULL,
  `content` varchar(10000) NOT NULL,
  `showtype` varchar(255) NOT NULL,
  `createtime` varchar(255) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `istop` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

ALTER TABLE `ims_ewei_shop_member_address` ADD COLUMN `userid` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证号';
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `userid` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证号';
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `taxation` varchar(255) NOT NULL DEFAULT '' COMMENT '税费';

ALTER TABLE `ims_cjdc_system` ADD COLUMN `is_gddz`  int(11) NULL DEFAULT 0 COMMENT '是否开启固定地址:1开启0关闭' AFTER `pay_money`;

CREATE TABLE `ims_cjdc_gddz` (
`id`  int(11) NOT NULL ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '固定地址名称' ,
`uniacid`  int(11) NULL COMMENT '商户id' ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

ALTER TABLE `ims_cjdc_gddz` MODIFY COLUMN `id`  int(11) NOT NULL AUTO_INCREMENT FIRST ;

CREATE TABLE `ims_cjdc_hygrade` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`uniacid`  int(11) NULL ,
`hygradename`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '等级名称' ,
`discount`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '优惠比例' ,
`money`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '消费金额' ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

ALTER TABLE `ims_cjdc_user` ADD COLUMN `hygrade`  int(11) NULL DEFAULT 0 COMMENT '会员等级' AFTER `hy_day`;

ALTER TABLE `ims_cjdc_order` ADD COLUMN `hygrade`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '会员级别优惠' AFTER `original_money`,ADD COLUMN `hygrade_money`  decimal(10,2) NULL COMMENT '会员优惠金额' AFTER `hygrade`;

ALTER TABLE `ims_cjdc_user` ADD COLUMN `count_money`  decimal(10,2) NULL DEFAULT 0 COMMENT '消费金额总计' AFTER `hygrade`;
ALTER TABLE `ims_cjdc_system` ADD COLUMN `hycontent`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '会员说明' AFTER `is_gddz`;

ALTER TABLE `ims_cjdc_coupons` ADD COLUMN `share`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分享说明' AFTER `day`;

ALTER TABLE `ims_cjdc_usercoupons` ADD COLUMN `is_user`  int(11) NULL COMMENT '是否分享领取' AFTER `type`;
ALTER TABLE `ims_cjdc_coupons` ADD COLUMN `fx_number`  int(11) NULL COMMENT '分享次数' AFTER `share`;
ALTER TABLE `ims_cjdc_usercoupons` ADD COLUMN `fx_number`  int(11) NOT NULL COMMENT '记录分享次数' AFTER `is_user`;

CREATE TABLE `ims_ewei_shop_goods_remind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goodsid` int(11) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `uniacid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `preparegoodstime`  int(11) not null COMMENT '预备发货时间,此时间是给商家来备货的,用户结算页面会显示预计发货时间';

ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `limitbuy` text NOT NULL COMMENT '各级会员商品单次的最大最小限购';
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `level1_rate`  decimal(10,2) DEFAULT NULL COMMENT '省代返利比例';
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `level2_rate`  decimal(10,2) DEFAULT NULL COMMENT '市代返利比例';
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `level3_rate`  decimal(10,2) DEFAULT NULL COMMENT '区代返利比例';

ALTER TABLE `ims_ewei_shop_poster` ADD COLUMN `isagent` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否为代理海报';
ALTER TABLE `ims_ewei_shop_commission_order_log` ADD COLUMN `sendtime` int(10) NOT NULL DEFAULT 0 COMMENT '奖励发放时间';

ALTER TABLE `ims_account` ADD COLUMN `testtime` int(11) default 0 COMMENT '测试账号,提示测试时间';
ALTER TABLE `ims_ewei_shop_order` ADD COLUMN `payqrcode` varchar(1000) default '' COMMENT 'sandpay 支付信息';
INSERT INTO `ims_ewei_shop_plugin` (`id`, `displayorder`, `identity`, `category`, `name`, `version`, `author`, `status`, `thumb`, `desc`, `iscom`, `deprecated`, `isv2`) VALUES ('55', '11', 'sandpay', 'tool', '杉德支付', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/virtual.jpg', NULL, '1', '0', '0');

UPDATE `ims_ewei_shop_express` SET express = 'huitongkuaidi' WHERE id=92;
ALTER TABLE `ims_ewei_shop_goods` ADD COLUMN `inviter_credit` varchar(20) default '' COMMENT '赠送给上级的积分' AFTER `credit`;
CREATE TABLE `ims_ewei_shop_commission_order_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT 0,
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT '用户openid',
  `money` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  `goodsid` int(11) NOT NULL DEFAULT 0 COMMENT '商品id',
  `orderid` int(11) NOT NULL DEFAULT 0 COMMENT '订单id',
  `level` varchar(20) NOT NULL DEFAULT '' COMMENT '佣金级别 level1 一级佣金，level2 二级佣金 level3 三级佣金，agent 代理奖励 peer 同级奖励',
  `from_type` varchar(50) NOT NULL DEFAULT '' COMMENT '来源类型',
  `issend` tinyint(3) NOT NULL DEFAULT 0 COMMENT '是否发放 0 待发放 1已发放',
  `remark` varchar(50) NOT NULL DEFAULT '' COMMENT '备注',
  `createtime` int(10) NOT NULL DEFAULT 0 COMMENT '时间',
  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '自动更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '佣金记录表';
ALTER TABLE `ims_ewei_shop_member` ADD COLUMN `unionid` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '平台unionid';
ALTER TABLE `ims_ewei_shop_live` ADD COLUMN `mid` int(11) NOT NULL DEFAULT 0 COMMENT '主播id';

CREATE TABLE `ims_ewei_shop_live_host` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `uniacid` int(11) NOT NULL DEFAULT 0,
    `openid` varchar(50) NOT NULL DEFAULT '' COMMENT '用户openid',
    `hostname` varchar(60) NOT NULL DEFAULT '' COMMENT '主播昵称',
    `realname` varchar(60) NOT NULL DEFAULT '' COMMENT '主播姓名',
    `phone` char(11) NOT NULL DEFAULT '' COMMENT '注册手机号',
    `idcard1` varchar(200) NOT NULL DEFAULT '' COMMENT '身份证正面',
    `idcard2` varchar(200) NOT NULL DEFAULT '' COMMENT '身份证反面',
    `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:申请中,1:通过,-1:不通过',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `ims_ewei_shop_live_host` ADD COLUMN `applytime` int(11) DEFAULT 0 COMMENT '申请时间';
ALTER TABLE `ims_ewei_shop_live_host` ADD COLUMN `reason` varchar(100) DEFAULT 0 COMMENT '驳回原因';
ALTER TABLE `ims_ewei_shop_live_host` ADD COLUMN `salecate` int(11) DEFAULT 0 COMMENT '直播分类id';
ALTER TABLE `ims_ewei_shop_live_host` ADD COLUMN `identity` varchar(60) DEFAULT 0 COMMENT '直播平台';
ALTER TABLE `ims_ewei_shop_live_host` ADD COLUMN `liveurl` varchar(300) DEFAULT '' COMMENT '直播路径';
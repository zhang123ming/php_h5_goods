/*
Navicat MySQL Data Transfer

Source Server         : mysql
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : wqi

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-02-27 15:52:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ims_ewei_shop_member_ticket
-- ----------------------------
DROP TABLE IF EXISTS `ims_ewei_shop_member_ticket`;
CREATE TABLE `ims_ewei_shop_member_ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `number` varchar(20) NOT NULL DEFAULT '' COMMENT '流水号',
  `uniacid` int(11) NOT NULL COMMENT '公众号ID',
  `openid` varchar(50) NOT NULL,
  `content` varchar(20) NOT NULL DEFAULT '' COMMENT '小票后6位',
  `img1` varchar(255) NOT NULL DEFAULT '' COMMENT '图片一',
  `img2` varchar(255) NOT NULL COMMENT '图片二',
  `img3` varchar(255) NOT NULL DEFAULT '' COMMENT '图片三',
  `is_recharge` tinyint(1) NOT NULL DEFAULT '0' COMMENT '充值状态:0=未充值,1=已充值',
  `is_back` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否驳回:0=否,1=是',
  `is_check` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态:0=未审核,1=已审核',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='用户小票';

-- ----------------------------
-- Table structure for ims_ewei_shop_member_ticket_log
-- ----------------------------
DROP TABLE IF EXISTS `ims_ewei_shop_member_ticket_log`;
CREATE TABLE `ims_ewei_shop_member_ticket_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tid` int(11) NOT NULL DEFAULT '0' COMMENT '小票ID',
  `number` varchar(20) NOT NULL COMMENT '小票流水号',
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号ID',
  `operator` varchar(255) NOT NULL DEFAULT '' COMMENT '操作员',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='小票操作记录';

-- ----------------------------
-- Table structure for ims_ewei_shop_member_ticket_record
-- ----------------------------
DROP TABLE IF EXISTS `ims_ewei_shop_member_ticket_record`;
CREATE TABLE `ims_ewei_shop_member_ticket_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tid` int(11) NOT NULL COMMENT '小票ID',
  `number` varchar(20) NOT NULL COMMENT '小票流水号',
  `uniacid` int(11) NOT NULL COMMENT '公众号ID',
  `operator` varchar(255) NOT NULL DEFAULT '' COMMENT '操作员',
  `credit1` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(111) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='小票充值记录';

INSERT INTO `ims_ewei_shop_plugin` 
(`id`, `displayorder`, `identity`, `category`, `name`, `version`, `author`, `status`, `thumb`, `desc`, `iscom`, `deprecated`, `isv2`)
 VALUES ('54', '54', 'ticket', 'biz', '用户小票管理', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/cashier.jpg', '', '0', '0', '1');

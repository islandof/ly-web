/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : laychat

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-12-04 15:57:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL COMMENT '用户名',
  `password` varchar(60) NOT NULL,
  `avatar` varchar(100) NOT NULL DEFAULT '/assets/images/admin.jpg' COMMENT '头像',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app
-- ----------------------------
DROP TABLE IF EXISTS `app`;
CREATE TABLE `app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_key` varchar(60) NOT NULL,
  `app_secret` varchar(60) NOT NULL,
  `state` enum('forbidden_group_chat','forbidden_private_chat','using','forbidden_all_chat') NOT NULL DEFAULT 'using' COMMENT '状态：''禁止群聊'',''禁止私聊'',‘正常使用’，‘禁止私聊和群聊’',
  `mode` enum('private','public') NOT NULL DEFAULT 'public' COMMENT '模式：‘公用’，‘私有安全模式’',
  `links` int(10) NOT NULL DEFAULT '100' COMMENT '最大链接数',
  `orderid` int(10) NOT NULL COMMENT '拥有者id',
  `create_time` int(10) NOT NULL COMMENT '申请创建时间',
  `buy_time` int(10) NOT NULL COMMENT '购买时间',
  `end_time` int(10) DEFAULT NULL COMMENT '到期时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gaglist
-- ----------------------------
DROP TABLE IF EXISTS `gaglist`;
CREATE TABLE `gaglist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `gid` int(11) NOT NULL COMMENT '被禁的群id',
  `userid` varchar(255) NOT NULL COMMENT '被禁言人的id',
  PRIMARY KEY (`id`),
  KEY `app_id` (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gagwords
-- ----------------------------
DROP TABLE IF EXISTS `gagwords`;
CREATE TABLE `gagwords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `data` varchar(30) NOT NULL COMMENT '敏感词',
  PRIMARY KEY (`id`),
  KEY `app_id` (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for message
-- ----------------------------
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `mid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `app_key` varchar(32) NOT NULL COMMENT 'app_key',
  `from` varchar(255) NOT NULL COMMENT '发起者uid/group_id',
  `to` varchar(255) NOT NULL COMMENT '接收者id，根据type不同，可能是用户uid，可能是群组id',
  `data` text NOT NULL COMMENT '具体的消息数据',
  `type` enum('friend','group') DEFAULT NULL,
  `timestamp` int(11) unsigned NOT NULL COMMENT '消息时间戳',
  PRIMARY KEY (`mid`),
  KEY `app_key` (`app_key`),
  KEY `timestamp` (`timestamp`),
  KEY `from` (`from`),
  KEY `to` (`to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id(没有实际意义)',
  `app_key` varchar(32) NOT NULL COMMENT 'app_key',
  `uid` varchar(255) NOT NULL COMMENT 'uid',
  `logout_timestamp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退出系统时间戳，用于查询离线消息',
  `status` enum('online','offline') NOT NULL DEFAULT 'offline' COMMENT '在线状态，在线或者离线',
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_key_user` (`app_key`,`uid`),
  KEY `TIMESTAMP` (`logout_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for white_list
-- ----------------------------
DROP TABLE IF EXISTS `white_list`;
CREATE TABLE `white_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(255) NOT NULL COMMENT ' 被添加人员id',
  `app_id` int(11) NOT NULL COMMENT ' ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

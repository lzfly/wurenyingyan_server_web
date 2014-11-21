/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50096
Source Host           : localhost:3306
Source Database       : yingyan_core

Target Server Type    : MYSQL
Target Server Version : 50096
File Encoding         : 65001

Date: 2014-11-21 16:07:14
*/

SET FOREIGN_KEY_CHECKS=0;


--
-- Database: `yingyan_core`
--

CREATE DATABASE IF NOT EXISTS `yingyan_core` DEFAULT CHARACTER SET utf8;

USE `yingyan_core`;

-- --------------------------------------------------------



-- ----------------------------
-- Table structure for `camera`
-- ----------------------------
DROP TABLE IF EXISTS `camera`;
CREATE TABLE `camera` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `SMARTCENTER_SN` varchar(128) default NULL,
  `SN` varchar(128) default NULL,
  `NAME` varchar(128) default NULL,
  `IP` varchar(64) default NULL,
  `PORT` int(11) default NULL,
  `MODEL` varchar(128) default NULL,
  `UPTIME` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `SN` USING HASH (`SN`),
  KEY `SMARTCENTER_SN` (`SMARTCENTER_SN`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of camera
-- ----------------------------
INSERT INTO `camera` VALUES ('1', '351792055028994', 'first', '第一摄像头', '192.168.1.35', '10080', 'shijie', '2014-11-18 11:28:46');
INSERT INTO `camera` VALUES ('2', '351792055028994', 'second', '第二摄像头', '192.168.1.36', '10081', 'shijie', '2014-11-13 17:06:34');

-- ----------------------------
-- Table structure for `device`
-- ----------------------------
DROP TABLE IF EXISTS `device`;
CREATE TABLE `device` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `SN` varchar(128) default NULL,
  `TYPE_CODE` varchar(128) default NULL,
  `SMARTCENTER_SN` varchar(128) default NULL,
  `NAME` varchar(128) default NULL,
  `IS_OPEN` tinyint(4) default '1',
  `IS_ONLINE` tinyint(4) default '1',
  `UPTIME` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `SN` USING HASH (`SN`),
  KEY `SMARTCENTER_SN` (`SMARTCENTER_SN`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of device
-- ----------------------------
INSERT INTO `device` VALUES ('26', '2A273A04004B1200', '0x0107', '351792055028994', '￤ﾺﾔ￤ﾺﾺ￧ﾧﾑ￦ﾊﾀ', '1', '0', '2014-11-14 10:29:45');
INSERT INTO `device` VALUES ('27', '922E3A04004B1200', '0x0107', '351792055028994', '922E3A04004B1200', '1', '0', '2014-11-13 11:40:38');
INSERT INTO `device` VALUES ('28', '04273A04004B1200', '0x0107', '351792055028994', '04273A04004B1200', '1', '0', '2014-11-13 11:40:38');
INSERT INTO `device` VALUES ('29', '7DAD5002004B1200', '0x0107', '351792055028994', '7DAD5002004B1200', '1', '1', '2014-11-21 15:35:21');
INSERT INTO `device` VALUES ('30', '712A3A04004B1200', '0x0308', '351792055028994', '712A3A04004B1200', '1', '1', '2014-11-21 15:35:20');
INSERT INTO `device` VALUES ('31', 'D32D3904004B1200', '0x0107', '351792055028994', 'D32D3904004B1200', '1', '0', '2014-11-14 10:22:12');
INSERT INTO `device` VALUES ('33', 'D32D3904004B1211', '0x0107', '351792055028994', '虚拟设备', '1', '1', '2014-11-18 11:32:58');

-- ----------------------------
-- Table structure for `device_bind_camera`
-- ----------------------------
DROP TABLE IF EXISTS `device_bind_camera`;
CREATE TABLE `device_bind_camera` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `DEVICE_SN` varchar(128) default NULL,
  `CAMERA_SN` varchar(128) default NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `DEVICE_SN` USING HASH (`DEVICE_SN`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of device_bind_camera
-- ----------------------------
INSERT INTO `device_bind_camera` VALUES ('28', 'D32D3904004B1211', 'second');
INSERT INTO `device_bind_camera` VALUES ('29', '7DAD5002004B1200', 'first');
INSERT INTO `device_bind_camera` VALUES ('30', '712A3A04004B1200', 'first');

-- ----------------------------
-- Table structure for `device_type`
-- ----------------------------
DROP TABLE IF EXISTS `device_type`;
CREATE TABLE `device_type` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `CODE` varchar(128) NOT NULL,
  `NAME` varchar(128) NOT NULL COMMENT '设备类型名',
  `ICON` varchar(128) default NULL,
  `TYPE` varchar(32) default NULL,
  `UPTIME` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `CODE` USING HASH (`CODE`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of device_type
-- ----------------------------
INSERT INTO `device_type` VALUES ('1', '0x0000', '普通开关', null, null, '2014-11-12 09:59:22');
INSERT INTO `device_type` VALUES ('2', '0x0002', '可控继电器(开关)', null, null, '2014-11-12 09:59:52');
INSERT INTO `device_type` VALUES ('3', '0x0009', '开关插座', null, null, '2014-11-12 10:00:09');
INSERT INTO `device_type` VALUES ('4', '0x0060', '门磁感应器', 'yingyan_icon_door_sensor', 'alarm', '2014-11-14 12:11:01');
INSERT INTO `device_type` VALUES ('5', '0x0101', '调光灯', null, null, '2014-11-12 10:00:50');
INSERT INTO `device_type` VALUES ('6', '0x0102', '彩灯', null, null, '2014-11-12 10:00:59');
INSERT INTO `device_type` VALUES ('7', '0x0105', '可调颜色灯', null, null, '2014-11-12 10:01:23');
INSERT INTO `device_type` VALUES ('8', '0x0106', '光照传感器', null, null, '2014-11-12 10:01:35');
INSERT INTO `device_type` VALUES ('9', '0x0107', '人体红外传感器', 'yingyan_icon_invade_sensor', 'alarm', '2014-11-14 12:11:04');
INSERT INTO `device_type` VALUES ('10', '0x0110', '色温灯', null, null, '2014-11-12 10:02:09');
INSERT INTO `device_type` VALUES ('11', '0x0220', '色温灯', null, null, '2014-11-12 10:02:15');
INSERT INTO `device_type` VALUES ('12', '0x0161', '红外遥控器', null, null, '2014-11-12 10:02:38');
INSERT INTO `device_type` VALUES ('13', '0x0202', '自动可控窗帘', null, null, '2014-11-12 10:03:06');
INSERT INTO `device_type` VALUES ('14', '0x0210', '飞利浦彩灯', null, null, '2014-11-12 10:03:21');
INSERT INTO `device_type` VALUES ('15', '0x0301', '温湿度监测器', null, null, '2014-11-12 10:05:45');
INSERT INTO `device_type` VALUES ('16', '0x0302', '温湿度监测器', null, null, '2014-11-12 10:05:50');
INSERT INTO `device_type` VALUES ('17', '0x0308', '燃气报警器', 'yingyan_icon_gas_sensor', 'alarm', '2014-11-14 12:11:11');
INSERT INTO `device_type` VALUES ('18', '0x0309', 'PM2.5监测器', null, null, '2014-11-12 10:04:45');
INSERT INTO `device_type` VALUES ('19', '0x0310', '烟雾报警器', null, 'alarm', '2014-11-14 12:11:15');
INSERT INTO `device_type` VALUES ('20', '0x0340', '点阵显示器', null, null, '2014-11-12 10:05:18');
INSERT INTO `device_type` VALUES ('21', '0x0403', '声光报警器', null, 'alarm', '2014-11-14 12:11:18');
INSERT INTO `device_type` VALUES ('22', '0xFFFF', '远程IP摄像头', 'yingyan_icon_camera_sensor', null, '2014-11-13 17:24:16');

-- ----------------------------
-- Table structure for `notice`
-- ----------------------------
DROP TABLE IF EXISTS `notice`;
CREATE TABLE `notice` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `CODE` varchar(64) default NULL,
  `SMARTCENTER_SN` varchar(128) default NULL,
  `DEVICE_SN` varchar(128) default NULL,
  `MESSAGE` varchar(1024) default NULL,
  `TYPE` varchar(32) default NULL,
  `PICTURE_FILE` varchar(256) default NULL,
  `UPTIME` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `CODE` USING HASH (`CODE`),
  KEY `SMARTCENTER_SN` (`SMARTCENTER_SN`),
  KEY `UPTIME` (`UPTIME`),
  KEY `TYPE` (`TYPE`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of notice
-- ----------------------------
INSERT INTO `notice` VALUES ('30', '7E955EE7D38584924E9A59965D0106BC', '351792055028994', '7DAD5002004B1200', '{\"alarm\":\"true\"}', 'alarm', 'first_20141121145442.zip', '2014-11-22 06:56:09');
INSERT INTO `notice` VALUES ('31', 'F8D085E183370B01C18FEE7C6A615289', '351792055028994', '7DAD5002004B1200', '{\"alarm\":\"true\"}', 'alarm', 'first_20141121145956.zip', '2014-11-22 07:01:23');
INSERT INTO `notice` VALUES ('32', 'B537600DB4DCC45411CB9DE802834195', '351792055028994', '7DAD5002004B1200', '{\"alarm\":\"true\"}', 'alarm', 'first_20141121150445.zip', '2014-11-22 07:06:12');
INSERT INTO `notice` VALUES ('33', '4E81FA60BB6721A84675FCA56D15C238', '351792055028994', '7DAD5002004B1200', '{\"alarm\":\"true\"}', 'alarm', 'first_20141121151823.zip', '2014-11-22 07:19:50');
INSERT INTO `notice` VALUES ('34', '9B5CA64C3A2769052FF859EB983BBF14', '351792055028994', '7DAD5002004B1200', '{\"alarm\":\"true\"}', 'alarm', 'first_20141121152654.zip', '2014-11-22 07:28:21');
INSERT INTO `notice` VALUES ('35', 'E63A53EF9156E5552AFE1BF51A867CEB', '351792055028994', '712A3A04004B1200', '{\"alarm\":\"true\"}', 'alarm', 'first_20141121153456.zip', '2014-11-22 07:36:23');

-- ----------------------------
-- Table structure for `notice_status`
-- ----------------------------
DROP TABLE IF EXISTS `notice_status`;
CREATE TABLE `notice_status` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `USER_ID` int(10) unsigned default NULL,
  `NOTICE_CODE` varchar(64) default NULL,
  `UPTIME` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `USERID` USING HASH (`USER_ID`,`NOTICE_CODE`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of notice_status
-- ----------------------------
INSERT INTO `notice_status` VALUES ('24', '2', 'B537600DB4DCC45411CB9DE802834195', '2014-11-21 15:11:23');
INSERT INTO `notice_status` VALUES ('25', '2', 'F8D085E183370B01C18FEE7C6A615289', '2014-11-21 15:11:34');
INSERT INTO `notice_status` VALUES ('26', '2', '7E955EE7D38584924E9A59965D0106BC', '2014-11-21 15:11:40');
INSERT INTO `notice_status` VALUES ('27', '2', '4E81FA60BB6721A84675FCA56D15C238', '2014-11-21 15:22:38');
INSERT INTO `notice_status` VALUES ('28', '2', '9B5CA64C3A2769052FF859EB983BBF14', '2014-11-21 15:28:26');

-- ----------------------------
-- Table structure for `smart_center`
-- ----------------------------
DROP TABLE IF EXISTS `smart_center`;
CREATE TABLE `smart_center` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `SN` varchar(128) default NULL,
  `PASS` varchar(128) default '888888',
  `PUSH_CLIENTID` varchar(128) default NULL,
  `STATE` tinyint(4) default '0',
  `UPTIME` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `SN` USING HASH (`SN`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of smart_center
-- ----------------------------
INSERT INTO `smart_center` VALUES ('2', '351792055028994', '888888', 'c4b4085674e0cba5a0f01aea4da73b1c', '1', '2014-11-21 15:17:16');
INSERT INTO `smart_center` VALUES ('3', 'A1000033328559', '888888', null, '0', '2014-11-13 16:03:58');

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `SMARTCENTER_SN` varchar(128) default NULL,
  `NAME` varchar(128) default NULL,
  `PASS` varchar(128) default NULL,
  `PHONE` varchar(128) default NULL,
  `STATUS` tinyint(4) default NULL,
  `PUSH_CLIENTID` varchar(128) default NULL,
  `UPTIME` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `NAME` USING HASH (`NAME`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '351792055028994', 'luoie', 'luoie', null, null, 'bb245d9a7d940eb0cba62eb85aec2e56', '2014-11-21 14:56:34');
INSERT INTO `user` VALUES ('2', '351792055028994', 'wxm', 'wxm', '', null, 'afc815c984ea849b9c68a35319fb85b5', '2014-11-20 18:13:28');

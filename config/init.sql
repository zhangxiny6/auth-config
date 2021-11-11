CREATE DATABASE IF NOT EXISTS auth_manage_db DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50709
 Source Host           : localhost:3306
 Source Schema         : auth_manage_db

 Target Server Type    : MySQL
 Target Server Version : 50709
 File Encoding         : 65001

 Date: 25/08/2021 09:53:48
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for am_auth_class
-- ----------------------------
DROP TABLE IF EXISTS `auth_manage_db`.`am_auth_class`;
CREATE TABLE `auth_manage_db`.`am_auth_class`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表主键',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '类名或方法名',
  `parent_class_id` int(11) NULL DEFAULT 0 COMMENT '父类id',
  `level` tinyint(11) NULL DEFAULT 0 COMMENT '0为控制器 1为方法',
  `auth_name` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '类说明',
  `author` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '类创建人',
  `module` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '类别',
  `createtime` int(11) NULL DEFAULT NULL COMMENT '记录生成时间',
  `auth_config_id` int(11) NULL DEFAULT 0 COMMENT '对 yjy_auth_config 主键',
  `class_createtime` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '类或方法创建时间',
  `url` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '菜单地址',
  `line` int(11) NULL DEFAULT 0 COMMENT '方法位置行数【自动获取】',
  `auth_status` tinyint(4) NULL DEFAULT 0 COMMENT '是否排除权限',
  `modifier` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '函数修饰符',
  `application_module` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所属模块',
  `qq_number` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'QQ号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for am_auth_config
-- ----------------------------
DROP TABLE IF EXISTS `auth_manage_db`.`am_auth_config`;
CREATE TABLE `auth_manage_db`.`am_auth_config`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表主键',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '配置名称',
  `file_path` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '控制器文件夹路径',
  `module` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '自定义类别',
  `sort` int(255) NULL DEFAULT 0 COMMENT '排序',
  `createtime` int(11) NULL DEFAULT NULL COMMENT '记录生成时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for am_manage_users
-- ----------------------------
DROP TABLE IF EXISTS `auth_manage_db`.`am_manage_users`;
CREATE TABLE `auth_manage_db`.`am_manage_users`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增长主键',
  `real_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '姓名',
  `user_name` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '登录名',
  `password` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '登录密码(md5加密)',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
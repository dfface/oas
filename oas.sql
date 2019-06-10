/*
 Navicat Premium Data Transfer

 Source Server         : macOS_MySQL
 Source Server Type    : MySQL
 Source Server Version : 80016
 Source Host           : localhost:3306
 Source Schema         : oas

 Target Server Type    : MySQL
 Target Server Version : 80016
 File Encoding         : 65001

 Date: 10/06/2019 10:14:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for carousel
-- ----------------------------
DROP TABLE IF EXISTS `carousel`;
CREATE TABLE `carousel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10006 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of carousel
-- ----------------------------
BEGIN;
INSERT INTO `carousel` VALUES (10002, '/images/20190608/996984d80b0ee7002d97d57bf376d9b7.jpg', '2019-06-08 20:01:49', '2019-06-08 20:01:49', NULL);
INSERT INTO `carousel` VALUES (10003, '/images/20190608/7edbda3507197272cd2655eeaec99fa9.jpg', '2019-06-08 20:01:55', '2019-06-08 20:01:55', NULL);
INSERT INTO `carousel` VALUES (10004, 'http://www.ustb.edu.cn/images/content/2018-10/20181022125207409236.jpg', '2019-06-08 20:02:06', '2019-06-08 20:02:06', NULL);
INSERT INTO `carousel` VALUES (10005, '/images/20190608/73a093ae070081841cee2ad39270873f.jpg', '2019-06-08 20:32:24', '2019-06-08 20:32:24', NULL);
COMMIT;

-- ----------------------------
-- Table structure for course
-- ----------------------------
DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tea_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `semester` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `year` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int(11) DEFAULT '1',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_teach` (`tea_id`),
  CONSTRAINT `FK_teach` FOREIGN KEY (`tea_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of course
-- ----------------------------
BEGIN;
INSERT INTO `course` VALUES ('C00000', 'B00000', '计算机网络A', '春', '2015', 0, '2019-05-21 17:29:22', '2019-05-21 20:57:11', NULL);
COMMIT;

-- ----------------------------
-- Table structure for file
-- ----------------------------
DROP TABLE IF EXISTS `file`;
CREATE TABLE `file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `use_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `url` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `desc` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `size` int(20) DEFAULT NULL,
  `download_count` int(11) DEFAULT '0',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_upload` (`use_id`),
  CONSTRAINT `FK_upload` FOREIGN KEY (`use_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10013 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of file
-- ----------------------------
BEGIN;
INSERT INTO `file` VALUES (10000, '41624539', 'php3D1hHR', '/images/20190528/fb0410a6411864cce9c13ec05ec72491.png', '2019 05 28 提问时上传的图片', 79000, 0, '2019-05-28 00:18:47', '2019-05-28 00:18:47', NULL);
INSERT INTO `file` VALUES (10001, '41624539', 'phpGiI4wA', '/images/20190528/82da60c48b2078ebe1fc16e7a21c721e.png', '2019年05月28日 提问时上传的图片', 80395, 0, '2019-05-28 00:23:48', '2019-05-28 00:23:48', NULL);
INSERT INTO `file` VALUES (10002, '41624539', 'phpnti6GN', '/images/20190528/14a19ca5a4c8f1881085679b9aa423ff.png', '2019年05月28日 提问时上传的图片', 79395, 0, '2019-05-28 00:32:15', '2019-05-28 00:32:15', NULL);
INSERT INTO `file` VALUES (10003, '41624539', 'phpkHbanS', '/images/20190601/d544ead45ab469a2a0c57165d0eae40a.png', '2019年06月01日 上传的图片', 133465, 0, '2019-06-01 13:13:26', '2019-06-01 13:13:26', NULL);
INSERT INTO `file` VALUES (10004, '41624539', 'phpeFD0S0', '/images/20190601/06773a53f9efcf8d1b97dc88dbd49ee8.jpg', '2019年06月01日 上传的图片', 264335, 0, '2019-06-01 13:15:19', '2019-06-01 13:15:19', NULL);
INSERT INTO `file` VALUES (10005, '41624539', 'php8vNhKQ', '/images/20190601/9141f263376dd9cdf9e3cc4bd19e3400.jpg', '2019年06月01日 修改资料时上传的头像', 9114, 0, '2019-06-01 16:29:44', '2019-06-01 16:29:44', NULL);
INSERT INTO `file` VALUES (10006, '41624539', 'phpJwPQcX', '/images/20190601/209f6ef094349c3506107258cb1faf39.png', '2019年06月01日 上传的图片', 227066, 0, '2019-06-01 16:50:38', '2019-06-01 16:50:38', NULL);
INSERT INTO `file` VALUES (10007, '41624539', 'phps7euiT', '/images/20190601/22476b95941b943bcb547628eb52ab10.png', '2019年06月01日 上传的图片', 183833, 0, '2019-06-01 16:53:06', '2019-06-01 16:53:06', NULL);
INSERT INTO `file` VALUES (10008, '41624539', 'phpN62SsW', '/images/20190601/d8d29518eb59c7bdb1dcb60bb08b3bc3.png', '2019年06月01日 上传的图片', 183748, 0, '2019-06-01 16:54:16', '2019-06-01 16:54:16', NULL);
INSERT INTO `file` VALUES (10009, 'A00000', '轮播图 10001', '/images/20190608/996984d80b0ee7002d97d57bf376d9b7.jpg', '2019年06月08日 上传的轮播图', 304271, 0, '2019-06-08 19:59:56', '2019-06-08 19:59:56', NULL);
INSERT INTO `file` VALUES (10010, 'A00000', '轮播图 10002', '/images/20190608/996984d80b0ee7002d97d57bf376d9b7.jpg', '2019年06月08日 上传的轮播图', 219008, 0, '2019-06-08 20:01:49', '2019-06-08 20:01:49', NULL);
INSERT INTO `file` VALUES (10011, 'A00000', '轮播图 10003', '/images/20190608/7edbda3507197272cd2655eeaec99fa9.jpg', '2019年06月08日 上传的轮播图', 540566, 0, '2019-06-08 20:01:55', '2019-06-08 20:01:55', NULL);
INSERT INTO `file` VALUES (10012, 'A00000', '轮播图 10005', '/images/20190608/73a093ae070081841cee2ad39270873f.jpg', '2019年06月08日 上传的轮播图', 216459, 0, '2019-06-08 20:32:24', '2019-06-08 20:32:24', NULL);
COMMIT;

-- ----------------------------
-- Table structure for question
-- ----------------------------
DROP TABLE IF EXISTS `question`;
CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stu_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `course_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `title` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `read_count` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ask` (`stu_id`),
  KEY `FK_course` (`course_id`),
  CONSTRAINT `FK_ask` FOREIGN KEY (`stu_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_course` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10017 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of question
-- ----------------------------
BEGIN;
INSERT INTO `question` VALUES (10001, '41624539', 'C00000', '等风来', '啦啦', 11, 0, '2019-05-22 10:12:38', '2019-06-08 18:29:49', NULL);
INSERT INTO `question` VALUES (10002, '41624539', 'C00000', '很好', '## 爱你', 1, 0, '2019-05-22 10:13:20', '2019-06-01 17:20:10', NULL);
INSERT INTO `question` VALUES (10003, '41624539', 'C00000', '测试', '# 你好\n\n### 这是测试', 5, 0, '2019-05-22 10:19:22', '2019-06-08 18:39:46', NULL);
INSERT INTO `question` VALUES (10004, '41624539', 'C00000', '仍然在测试', '# 你好\n\n### 这是测试\n\n### 六', 461, 1, '2019-05-22 10:19:49', '2019-06-09 23:34:34', NULL);
INSERT INTO `question` VALUES (10005, '41624539', 'C00000', '测试2', '的', 3, 0, '2019-05-22 16:46:53', '2019-06-01 18:37:39', '2019-06-01 18:37:39');
INSERT INTO `question` VALUES (10006, '41624539', 'C00000', '测试3', '个', 0, 0, '2019-05-22 16:47:04', '2019-06-01 17:49:19', '2019-06-01 17:49:19');
INSERT INTO `question` VALUES (10007, '41624539', 'C00000', '测试4', '我', 0, 0, '2019-05-23 18:05:25', '2019-06-01 18:23:16', '2019-06-01 18:23:16');
INSERT INTO `question` VALUES (10008, '41624539', 'C00000', '这个系统的图片提交后加了水印', '### 再次询问水印大小问题\n\n![](/images/20190528/82da60c48b2078ebe1fc16e7a21c721e.png)', 6, 0, '2019-05-28 00:24:05', '2019-06-08 21:29:41', NULL);
INSERT INTO `question` VALUES (10009, '41624539', 'C00000', '图像大小如何写入数据库', '### 这一次对图像大小的测试成功了\n\n具体是要用  filesize() 函数，其中传入相对路径，别是url。\n![](/images/20190528/14a19ca5a4c8f1881085679b9aa423ff.png)', 4, 0, '2019-05-28 00:34:19', '2019-06-05 19:30:21', NULL);
INSERT INTO `question` VALUES (10011, '41624539', 'C00000', '请问计算机网络指的是什么？', '> 计算机网络是指将地理位置不同的具有独立功能的多台计算机及其外部设备，通过通信线路连接起来，在网络操作系统，网络管理软件及网络通信协议的管理和协调下，实现资源共享和信息传递的计算机系统。\n\n这句话对吗？\n\n```c\n#include <stdio.h>\n \nint main()\n{\n    /* 我的第一个 C 程序 */\n    printf(\"Hello, World! \\n\");\n \n    return 0;\n}\n```\n', 5, 0, '2019-06-01 11:26:07', '2019-06-08 22:23:53', NULL);
INSERT INTO `question` VALUES (10012, '41624539', 'C00000', '对于问题的提出，使用markdown语法好吗？', '### markdown\n\n> Markdown是一种可以使用普通文本编辑器编写的标记语言，通过简单的标记语法，它可以使普通文本内容具有一定的格式。\nMarkdown具有一系列衍生版本，用于扩展Markdown的功能（如表格、脚注、内嵌HTML等等），这些功能原初的Markdown尚不具备，它们能让Markdown转换成更多的格式，例如LaTeX，Docbook。Markdown增强版中比较有名的有Markdown Extra、MultiMarkdown、 Maruku等。这些衍生版本要么基于工具，如Pandoc；要么基于网站，如GitHub和Wikipedia，在语法上基本兼容，但在一些语法和渲染效果上有改动。\n\n这个好像还行啊。', 4, 0, '2019-06-01 11:27:46', '2019-06-08 18:39:42', NULL);
INSERT INTO `question` VALUES (10013, '41624539', 'C00000', '项目开发记录 六一', '## 这是六月一日的测试\n\n```c\n#include <stdio.h>\n```\n\n![课表](/images/20190601/d544ead45ab469a2a0c57165d0eae40a.png \"课表\")', 41, 1, '2019-06-01 13:13:43', '2019-06-10 00:59:02', NULL);
INSERT INTO `question` VALUES (10014, '41624539', 'C00000', '图片logo水印测试', '### 左上角logo水印测试 和右下角文字水印测试\n\n![](/images/20190601/209f6ef094349c3506107258cb1faf39.png)\n\n![](/images/20190601/22476b95941b943bcb547628eb52ab10.png)\n\n最后一张还不错：\n![](/images/20190601/d8d29518eb59c7bdb1dcb60bb08b3bc3.png)', 14, 0, '2019-06-01 16:54:48', '2019-06-08 18:39:48', NULL);
INSERT INTO `question` VALUES (10015, '41624539', 'C00000', '删除测试', '### 删除测试', 1, 0, '2019-06-01 17:49:42', '2019-06-01 17:49:50', '2019-06-01 17:49:50');
INSERT INTO `question` VALUES (10016, '41624539', 'C00000', '2019年粽子节提问！', '### 忙忙忙！\n\n调用修改测试！\n测试下关闭窗口！\n\n再测试！\n\n测试一秒！\n\n再次！', 8, 0, '2019-06-08 23:28:59', '2019-06-10 00:50:00', NULL);
COMMIT;

-- ----------------------------
-- Table structure for reply
-- ----------------------------
DROP TABLE IF EXISTS `reply`;
CREATE TABLE `reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `que_id` int(11) NOT NULL,
  `use_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `like_count` int(11) NOT NULL DEFAULT '0',
  `dislike_count` int(11) NOT NULL DEFAULT '0',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_rep_use` (`use_id`),
  KEY `FK_rep_que` (`que_id`),
  CONSTRAINT `FK_rep_que` FOREIGN KEY (`que_id`) REFERENCES `question` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_rep_use` FOREIGN KEY (`use_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10026 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of reply
-- ----------------------------
BEGIN;
INSERT INTO `reply` VALUES (10000, 10004, 'B00000', '这道题你问的非常好，我虽然不会解，但还是尽力去想办法。', 1, 23, 0, '2019-05-24 23:11:35', '2019-05-31 19:16:06', NULL);
INSERT INTO `reply` VALUES (10001, 10004, 'B00000', '我虽然不会解，但还是尽力去想办法。', 1, 4, 1, '2019-05-28 14:00:52', '2019-05-31 23:34:32', NULL);
INSERT INTO `reply` VALUES (10002, 10004, 'B00000', '这是测试回答0', 1, 1, 4, '2019-05-28 14:02:06', '2019-05-31 22:56:44', NULL);
INSERT INTO `reply` VALUES (10003, 10004, 'B00000', '这是测试回答1', 1, 0, 0, '2019-05-28 14:02:06', '2019-06-01 18:57:25', NULL);
INSERT INTO `reply` VALUES (10004, 10004, 'B00000', '这是测试回答2', 1, 0, 0, '2019-05-28 14:02:06', '2019-06-01 18:56:56', NULL);
INSERT INTO `reply` VALUES (10005, 10004, 'B00000', '这是测试回答3', 1, 0, 0, '2019-05-28 14:02:06', '2019-06-01 13:18:38', NULL);
INSERT INTO `reply` VALUES (10006, 10004, 'B00000', '这是测试回答4', 0, 0, 0, '2019-05-28 14:02:06', '2019-05-31 19:09:30', NULL);
INSERT INTO `reply` VALUES (10007, 10004, 'B00000', '这是测试回答5', 1, 0, 0, '2019-05-28 14:02:06', '2019-06-01 18:46:27', NULL);
INSERT INTO `reply` VALUES (10008, 10004, 'B00000', '这是测试回答6', 1, 0, 0, '2019-05-28 14:02:06', '2019-06-01 13:18:42', NULL);
INSERT INTO `reply` VALUES (10009, 10004, 'B00000', '这是测试回答7', 1, 0, 0, '2019-05-28 14:02:06', '2019-06-01 18:56:49', NULL);
INSERT INTO `reply` VALUES (10010, 10004, 'B00000', '这是测试回答8', 0, 0, 0, '2019-05-28 14:02:06', '2019-05-28 14:02:06', NULL);
INSERT INTO `reply` VALUES (10011, 10004, 'B00000', '这是测试回答9', 0, 0, 0, '2019-05-28 14:02:06', '2019-05-28 14:02:06', NULL);
INSERT INTO `reply` VALUES (10012, 10004, 'B00000', '这是测试回答10', 0, 0, 0, '2019-05-28 14:02:06', '2019-05-28 14:02:06', NULL);
INSERT INTO `reply` VALUES (10013, 10004, 'B00000', '这是测试回答11', 0, 0, 0, '2019-05-28 14:02:06', '2019-05-28 14:02:06', NULL);
INSERT INTO `reply` VALUES (10014, 10004, 'B00000', '这是测试回答12', 0, 1, 0, '2019-05-28 14:02:06', '2019-06-03 13:32:22', NULL);
INSERT INTO `reply` VALUES (10015, 10004, 'B00000', '这是测试回答13', 0, 0, 0, '2019-05-28 14:02:06', '2019-05-28 14:02:06', NULL);
INSERT INTO `reply` VALUES (10016, 10004, 'B00000', '这是测试回答14', 0, 1, 0, '2019-05-28 14:02:06', '2019-06-08 17:03:18', NULL);
INSERT INTO `reply` VALUES (10017, 10004, 'B00000', '这是测试回答15', 0, 0, 0, '2019-05-28 14:02:06', '2019-05-28 14:02:06', NULL);
INSERT INTO `reply` VALUES (10018, 10004, 'B00000', '这是测试回答16', 0, 0, 0, '2019-05-28 14:02:06', '2019-05-31 19:11:22', NULL);
INSERT INTO `reply` VALUES (10019, 10004, 'B00000', '这是测试回答17', 0, 0, 0, '2019-05-28 14:02:06', '2019-05-31 19:11:27', NULL);
INSERT INTO `reply` VALUES (10020, 10004, 'B00000', '这是测试回答18', 0, 1, 0, '2019-05-28 14:02:06', '2019-06-09 23:34:48', NULL);
INSERT INTO `reply` VALUES (10021, 10004, 'B00000', '这是测试回答19', 0, 3, 2, '2019-05-28 14:02:06', '2019-06-09 23:34:44', NULL);
INSERT INTO `reply` VALUES (10022, 10004, '41624539', '#### 自己给自己回答一个\n\n```php\n<?php\n	echo \"hello\";\n?>\n```\n2019-05-31 20:40:32 星期五', 1, 3, 3, '2019-05-31 20:41:53', '2019-06-03 17:00:29', NULL);
INSERT INTO `reply` VALUES (10023, 10013, '41624539', '六一，自己回答自己！\n\n学生接口测试！\n\n![功能图](/images/20190601/06773a53f9efcf8d1b97dc88dbd49ee8.jpg \"功能图\")', 1, 3, 1, '2019-06-01 13:15:42', '2019-06-10 00:58:58', NULL);
INSERT INTO `reply` VALUES (10024, 10008, 'B00000', '你好，这是一个测试回答，我是教师，我给你回答之后会自动发送一封邮件提醒你。', 0, 0, 0, '2019-06-08 21:23:53', '2019-06-08 21:23:53', NULL);
INSERT INTO `reply` VALUES (10025, 10008, 'B00000', '再次测试！', 0, 0, 0, '2019-06-08 21:29:39', '2019-06-08 21:29:39', NULL);
COMMIT;

-- ----------------------------
-- Table structure for take
-- ----------------------------
DROP TABLE IF EXISTS `take`;
CREATE TABLE `take` (
  `stu_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cou_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`stu_id`,`cou_id`),
  KEY `FK_tak_cou` (`cou_id`),
  CONSTRAINT `FK_stu_tak` FOREIGN KEY (`stu_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_tak_cou` FOREIGN KEY (`cou_id`) REFERENCES `course` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of take
-- ----------------------------
BEGIN;
INSERT INTO `take` VALUES ('41624539', 'C00000', '2019-05-21 17:36:04', '2019-05-21 17:36:04', NULL);
COMMIT;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` tinyint(1) NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `avatar` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES ('3', '班晓娟', '$2y$10$uoyjNXTR3ORqr2iaJSipxOrnHq4lM2w1riAtRUefTtfGKKu.qRhsC', 1, '1234567891@qq.com', 'httpsss0.bdstatic.com70cFvHSh_Q1YnxGkpoWK1HF6hhyitu=2779942948,1955954400&fm=26&gp=0.jpg', '我是教师', NULL, NULL, NULL, NULL);
INSERT INTO `user` VALUES ('4', '包宏', '$2y$10$0JZwMBW3xobpqeMVuf9WIOJK7fvasu/vQOH2oU7gVSFgxwksoTfjq', 1, '1234567892@qq.com', 'httpsss0.bdstatic.com70cFvHSh_Q1YnxGkpoWK1HF6hhyitu=2779942948,1955954400&fm=26&gp=0.jpg', '我是教师', NULL, NULL, NULL, NULL);
INSERT INTO `user` VALUES ('41624539', '东方寒', '$2y$10$5vrpYKxy0IEgd1yeP/5pKeqWXBf6m5BP6y/wf7oqN2ofo4QG57rhq', 0, '1106741606@qq.com', '/images/20190601/9141f263376dd9cdf9e3cc4bd19e3400.jpg', '一蓑烟雨任平生', '2019-06-10 00:23:46', '2019-05-21 17:02:29', '2019-06-10 00:23:47', NULL);
INSERT INTO `user` VALUES ('5', '陈月云', '$2y$10$2xZdk0L70RyIFYXY.N9BEuOorDSE4A2KXlRq.kts74HSNRQ41itoS', 1, '1234567893@qq.com', 'httpsss0.bdstatic.com70cFvHSh_Q1YnxGkpoWK1HF6hhyitu=2779942948,1955954400&fm=26&gp=0.jpg', '我是教师', NULL, NULL, NULL, NULL);
INSERT INTO `user` VALUES ('A00000', '教务管理员小六', '$2y$10$R6aZGY5xxA.OwD.A7/dh9e5ahRc1znNSjC8MsW46rnlN/qK0StZ9i', 3, 'admin@dfface.com', 'https://upload.jianshu.io/users/upload_avatars/13213889/7314c5cc-ca7f-4542-b914-2c8dffaf324d.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/240/h/240', '一只有限写作的无限猴子~', '2019-06-10 02:23:24', '2019-05-21 17:07:28', '2019-06-10 02:23:25', NULL);
INSERT INTO `user` VALUES ('B00000', '王洪泊', '$2y$10$336.4NkSsFqruCWXkFc28uCLgMWcvQxCzgKLFheq6dlM5n4oG18Vi', 1, 'handy1998@qq.com', 'http://scce.ustb.edu.cn/attach/file/shiziduiwu/jiaoshixinxi/2018-04-12/cae0262ae07e6b085ebf1e7893313296.jpg', '王洪泊, 男，工学博士，硕士生导师，北京科技大学计算机与通信工程学院计算机系教师。主持国家自然科学基金项目(61572074)；作为骨干研究人员，参与国家自然科学基金项目(60375038)、国家“十五”重点科技攻关项目(2004BA616A-11)、北京市自然科学基金项目(4072018)、国家科技支撑计划(2006BAJ04B07)、国家863项目（2009AA01Z119）；主持横向课题一项。在国内外重要学术刊物及国际重要学术会议上发表论文30多篇，获省科技进步二等奖2项，专著或教材8部，国家授权发明专利1项、计算机软件著作权12项。', '2019-06-09 20:12:38', '2019-05-21 17:05:09', '2019-06-09 20:12:38', NULL);
INSERT INTO `user` VALUES ('B00001', '阿孜古丽', '$2y$10$UfFRawpAx9zrAETllGn3c.YCQWDsJhghOOcdQ0KE9eG0EfQy5fvqq', 1, 'aziguli@ustb.edu.cn', 'http://scce.ustb.edu.cn/attach/file/shiziduiwu/jiaoshixinxi/2018-04-09/4318670a11150628045019fa86f68920.jpg', '阿孜古丽教授现任职北京科技大学计算机与通信工程学院教授，材料领域知识工程北京市重点实验室副主任，研究方向主要为知识工程、大数据技术、人工智能、互联网应用、软件体系架构等，作为项目负责人及主要研发人员承担过国家863、973、国家科技支撑及北京市省部级课题等三十余项国家级和北京市的重点科技研究项目，科研项目涉及政府、高科技、矿业、医疗等行业领域，具备全面的IT专业服务能力，能够针对客户的需求提供解决方案。', NULL, '2019-06-01 22:43:03', '2019-06-01 22:43:03', NULL);
INSERT INTO `user` VALUES ('B00002', '陈红松', '$2y$10$5SAja.kPTcRb2I/k39XJxuIiZmoxaGUX9J5xLpTRE8OvSFRZulQgO', 1, 'chenhongsong@ustb.edu.cn', 'http://scce.ustb.edu.cn/attach/file/shiziduiwu/jiaoshixinxi/2018-12-19/de760e256ec28ec0a7e6e6392e086f67.jpg', ' 陈红松，目前，在北京科技大学计算机系工作，系副主任职务，副教授职称，硕士生导师，主要从事网络信息安全方面的教学和科研工作，陈红松目前是IEEE ComSoc Radio Communication 技术委员，Elsevier 出版的国际期刊《Information Sciences》特邀审稿人，Springer出版的国际期刊《Wireless Personal Communications》特邀审稿人，《北京邮电大学学报》审稿人、《应用科学学报》审稿人，中国计算机学会会员，中国密码学会会员，北京市通信协会IPv6 专委会委员。荣获2004年度哈工大计算机学院正在进行的优秀博士论文奖； 2005年度获得哈工大优秀博士生奖学金；2006年度被评为黑龙江省优秀博士毕业生；2007年获得北京科技大学优秀青年教师科研论文奖；2009年获得北京科技大学优秀科研论文奖；2010年获得北京科技大学第24届教育教学成果奖一等奖（排名第1）。2013年9月-2014年9月在美国普渡大学计算机科学系信息安全研究中心进行国家公派学术访问，主要从事网络内容安全、无线Ad hoc及传感器网络安全协议、云安全、大数据访问控制及行业应用、物联网安全等方向的研究。可以招收计算机科学与技术、软件工程、计算机技术等专业的硕士研究生。', NULL, '2019-06-01 22:44:04', '2019-06-01 22:44:04', NULL);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;

-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2011 年 07 月 15 日 06:07
-- 服务器版本: 5.5.11
-- PHP 版本: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `starrysky`
--

-- --------------------------------------------------------

--
-- 表的结构 `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '评论id，系统分配，自增、主键',
  `tweet_id` int(11) NOT NULL COMMENT '被评论者发布的信息的id',
  `c_user_id` int(11) NOT NULL COMMENT '评论用户id，user表外键',
  `bc_user_id` int(11) NOT NULL COMMENT '被评论用户id',
  `comment_datetime` datetime NOT NULL COMMENT '评论时间',
  `content` varchar(300) NOT NULL COMMENT '评论内容',
  PRIMARY KEY (`comment_id`),
  UNIQUE KEY `comment_id_UNIQUE` (`comment_id`),
  KEY `c_user_id` (`c_user_id`),
  KEY `tweet_id` (`tweet_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;



-- --------------------------------------------------------

--
-- 表的结构 `follower`
--

CREATE TABLE IF NOT EXISTS `follower` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '当前用户id',
  `follower_id` int(11) NOT NULL COMMENT '被当前用户关注的人的id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='被关注者表:即存储当前用户被谁关注' AUTO_INCREMENT=6 ;



-- --------------------------------------------------------

--
-- 表的结构 `following`
--

CREATE TABLE IF NOT EXISTS `following` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '当前用户id',
  `following_id` int(11) NOT NULL COMMENT '关注当期用户的用户id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='关注者表:即存储当前用户关注了谁' AUTO_INCREMENT=19 ;



-- --------------------------------------------------------

--
-- 表的结构 `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `userid1` int(11) NOT NULL,
  `userid2` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=67 ;


-- --------------------------------------------------------

--
-- 表的结构 `record`
--

CREATE TABLE IF NOT EXISTS `record` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '聊天记录id',
  `record_time` datetime NOT NULL COMMENT '聊天时间',
  `sender_id` int(11) NOT NULL COMMENT '发送方用户id',
  `receiver_id` int(11) NOT NULL COMMENT '接收方用户id',
  `content` text NOT NULL COMMENT '聊天内容',
  PRIMARY KEY (`record_id`),
  UNIQUE KEY `record_id_UNIQUE` (`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='聊天记录表(在本系统中，聊天等于私信),存储于服务器端' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `record`
--


-- --------------------------------------------------------

--
-- 表的结构 `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '兴趣爱好id',
  `user_tag` varchar(100) NOT NULL COMMENT '存储用户的兴趣、爱好',
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户兴趣、爱好表' AUTO_INCREMENT=24 ;



-- --------------------------------------------------------

--
-- 表的结构 `tb_onlineuser`
--

CREATE TABLE IF NOT EXISTS `tb_onlineuser` (
  `N_OnlineUserId` int(11) NOT NULL,
  `D_LoginTime` int(11) NOT NULL,
  `N_OnlineID` int(11) NOT NULL,
  PRIMARY KEY (`N_OnlineUserId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `tb_onlineuser`
--


-- --------------------------------------------------------

--
-- 表的结构 `tb_onlineusercount`
--

CREATE TABLE IF NOT EXISTS `tb_onlineusercount` (
  `N_OnlineID` int(11) NOT NULL,
  `N_OnlineUserId` int(11) NOT NULL,
  `D_LoginDate` date NOT NULL,
  `D_LoginTime` int(11) NOT NULL,
  `D_OverDate` date NOT NULL,
  `D_OverTime` int(11) NOT NULL,
  PRIMARY KEY (`N_OnlineID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- 表的结构 `tweet`
--

CREATE TABLE IF NOT EXISTS `tweet` (
  `tweet_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '信息编号',
  `user_id` int(11) NOT NULL COMMENT '发布该消息的用户id',
  `release_datetime` datetime NOT NULL COMMENT '发布消息的时间',
  `content` varchar(300) NOT NULL COMMENT '消息内容',
  PRIMARY KEY (`tweet_id`),
  UNIQUE KEY `tweet_id_UNIQUE` (`tweet_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='信息表:存储签名的更新等，并将其显示给关注该用户的人' AUTO_INCREMENT=13 ;

--
-- 转存表中的数据 `tweet`
--



-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '系统分配的用户id，唯一、自增',
  `email` varchar(100) NOT NULL COMMENT '邮箱（登录名）',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `nick_name` varchar(45) NOT NULL COMMENT '昵称',
  `birthday` date NOT NULL COMMENT '生日',
  `gender` varchar(10) NOT NULL COMMENT '性别',
  `introduction` varchar(300) DEFAULT NULL COMMENT '自我介绍（个性签名）',
  `image` varchar(100) DEFAULT NULL COMMENT '用户头像',
  `position` point NOT NULL COMMENT '用户所在位置的经度和纬度坐标。用POINT(11, 12)的形式表示',
  `address` varchar(50) DEFAULT NULL COMMENT '用户所在位置',
  `register_datetime` datetime NOT NULL COMMENT '系统分配的注册时间',
  `logon_time` datetime NOT NULL COMMENT '用户登录时间',
  `ip` varchar(45) NOT NULL COMMENT '登录时的ip地址',
  `online` tinyint(1) NOT NULL COMMENT '用户是否在线，online=1时就表示在线，0时表示离线',
  `height` int(11) NOT NULL COMMENT '用户身高，填的时候这样填：\n比如171cm，填171就ok了。',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id_UNIQUE` (`user_id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;



--
-- 表的结构 `user_to_tag`
--

CREATE TABLE IF NOT EXISTS `user_to_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_id` (`user_id`),
  KEY `fk_tag_id` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;


--
-- 限制导出的表
--

--
-- 限制表 `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- 限制表 `user_to_tag`
--
ALTER TABLE `user_to_tag`
  ADD CONSTRAINT `fk_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

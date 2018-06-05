#mysql数据库表结构

-- create database blog;

-- DROP TABLE IF EXISTS `blog_article`;
-- CREATE TABLE `blog_article`
-- (
--   `article_id` SMALLINT(4) NOT NULL AUTO_INCREMENT COMMENT '主键',
--   `title` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '文章标题',
--   `desc` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '文章描述',
--   `content` TEXT NOT NULL COMMENT '文章内容',
--   `create_time` VARCHAR(50) NOT NULL default '' COMMENT '添加时间',
--   `update_time` VARCHAR(50) NOT NULL default '' COMMENT '修改时间',
--   `cate_id` SMALLINT(4) NOT NULL COMMENT'文章分类',
--   PRIMARY KEY (`article_id`)
-- ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章表';
-- ALTER TABLE blog_article ADD thumb_pic VARCHAR(100) NOT NULL DEFAULT '' COMMENT '缩略图';

CREATE TABLE `t_cfg`(
    `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增id', 
    `host` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '数据库地址',
    `user` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '数据库用户名',
    `password` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '数据库密码',
    `databaseName` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '数据库名',
    `tableName` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '数据库表名',
    `fields` VARCHAR(2000) NOT NULL DEFAULT '' COMMENT '所有字段名和对应属性',
    `nav_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '页面导航名称',
    `nav_cat_id` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '页面所属类别',
    `create_time` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '创建时间',
    `update_time` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '修改时间',
    `create_user` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '创建人',
    `update_user` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '修改人',
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='页面配置表';
alter table t_cfg modify fields varchar(2000) not null default '' COMMENT '所有字段名和对应属性';

CREATE TABLE `t_category`(
    `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `cat_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '分类名称',
    `create_time` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '创建时间',
    `update_time` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '修改时间',
    `create_user` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '创建人',
    `update_user` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '修改人',
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='页面分类表';

#插几条假数据
INSERT INTO t_category
    (cat_name, create_time)
VALUES
    ('van系统页面', NOW());
INSERT INTO t_category
    (cat_name, create_time)
VALUES
    ('sam系统页面', NOW())

CREATE TABLE IF NOT EXISTS `t_log` (
  `id` int(11) NOT NULL auto_increment COMMENT '自增ID',
  `addtime` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT '操作时间',
  `username` varchar(20) NOT NULL COMMENT '操作人',
  `oper` enum('add','edit','delete','other') NOT NULL default 'other' COMMENT '操作类型：delete|add|edit|other',
  `ip` varchar(20) NOT NULL COMMENT '访问ip',
  `browser` varchar(30) NOT NULL COMMENT '操作浏览器',
  `operid` varchar(24) NOT NULL COMMENT '操作唯一标识',
  `opertable` varchar(40) NOT NULL COMMENT '操作表名称',
  `url` varchar(200) NOT NULL COMMENT '操作url',
  `oldinfo` mediumtext NOT NULL COMMENT '操作前表原信息',
  `newinfo` mediumtext NOT NULL COMMENT '操作后表新信息',
  PRIMARY KEY  (`id`),
  KEY `operid` (`operid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='操作日志表';

CREATE TABLE `t_test`(
    `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '文章id',
    `title` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '标题',
    `description` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '描述',
    `content` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '内容',
    `create_time` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '创建时间',
    `update_time` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='页面配置表';

#插入几条假数据
INSERT INTO t_test
    (title, description, content, create_time)
VALUES
    ('测试title0', '这是测试description', '这是测试测试content', NOW());
INSERT INTO t_test
    (title, description, content, create_time)
VALUES
    ('测试title1', '这是测试description', '这是测试测试content', NOW());
INSERT INTO t_test
    (title, description, content, create_time)
VALUES
    ('测试title2', '这是测试description', '这是测试测试content', NOW());
INSERT INTO t_test
    (title, description, content, create_time)
VALUES
    ('测试title3', '这是测试description', '这是测试测试content', NOW());
INSERT INTO t_test
    (title, description, content, create_time)
VALUES
    ('测试title4', '这是测试description', '这是测试测试content', NOW());
INSERT INTO t_test
    (title, description, content, create_time)
VALUES
    ('测试title5', '这是测试description', '这是测试测试content', NOW());











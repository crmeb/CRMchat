
ALTER TABLE `eb_chat_service` ADD `is_app` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否为APP登陆' AFTER `client_id`;

ALTER TABLE `eb_chat_user` ADD `remark_nickname` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '备注昵称' AFTER `nickname`;

CREATE TABLE `eb_chat_auto_reply` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '内容',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属用户',
  `appid` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '所属appid',
  `sort` INT(10) NOT NULL DEFAULT '0' COMMENT '排序,越靠前,越是能被自会回复到',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='自动回复';

ALTER TABLE `eb_chat_service` ADD `auto_reply` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '自动回复' AFTER `client_id`;
ALTER TABLE `eb_chat_service` ADD `is_backstage` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1=前台运行;0=后台运行' AFTER `auto_reply`;
ALTER TABLE `eb_chat_service` ADD `welcome_words` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '欢迎语' AFTER `auto_reply`;
ALTER TABLE `eb_chat_service_dialogue_record` ADD `is_send` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否发送' AFTER `other`;

-- 2021/09/17新增
ALTER TABLE `eb_chat_service` ADD `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间' AFTER `welcome_words`;
ALTER TABLE `eb_chat_service` ADD `ip` VARCHAR(50) NOT NULL DEFAULT '' COMMENT 'ip' AFTER `welcome_words`;


-- 2021/09/23新增
ALTER TABLE `eb_auxiliary` ADD `appid` VARCHAR(35) NOT NULL AFTER `binding_id`;

-- 2021/09/29新增
ALTER TABLE `eb_chat_user` ADD `online` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1=在线,0=离线' AFTER `sex`;
ALTER TABLE `eb_chat_user` ADD `version` varchar(30) NOT NULL DEFAULT '' COMMENT '版本号' AFTER `online`;

-- 2021/09/30新增
ALTER TABLE `eb_chat_service_record` ADD INDEX(`online`);
UPDATE `eb_chat_user` SET `online` = 1 WHERE `id` = (SELECT `user_id` FROM `eb_chat_service_record` WHERE `online` = 1);


-- 2021/10/21新增
ALTER TABLE `eb_chat_service_record` ADD `delete_time` INT(10) NULL DEFAULT NULL COMMENT '删除字段' AFTER `update_time`;

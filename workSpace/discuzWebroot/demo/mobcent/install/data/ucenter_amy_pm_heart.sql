CREATE TABLE `{replaceStr}ucenter_amy_pm_heart` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id', 
	`plid` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会话列表ID',
	`uid` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户id',
	`from_uid` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对话用户id',
	`last_received` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最近一次客户端接收',
	PRIMARY KEY (`id`)
) ENGINE=MYISAM;
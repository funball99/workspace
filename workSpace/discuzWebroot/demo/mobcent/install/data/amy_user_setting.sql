CREATE TABLE `{replaceStr}amy_user_setting` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id', 
    `uid` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户id',
    `ukey` CHAR(20) NOT NULL DEFAULT '' COMMENT '用户设置键名',
    `uvalue` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '用户设置值',
    PRIMARY KEY (`id`)
) ENGINE=MYISAM;
CREATE TABLE `{replaceStr}home_access` (`user_access_id` int(11) NOT NULL AUTO_INCREMENT,`user_access_token` varchar(36) DEFAULT NULL,`user_access_secret` varchar(36) DEFAULT NULL,`user_id` int(11) DEFAULT NULL,`create_time` datetime DEFAULT NULL,PRIMARY KEY (`user_access_id`),UNIQUE KEY `user_access_token` (`user_access_token`,`user_access_secret`),UNIQUE KEY `user_id` (`user_id`)) TYPE=MyISAM;
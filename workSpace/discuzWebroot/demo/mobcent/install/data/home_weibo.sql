CREATE TABLE `{replaceStr}home_weibo` (`uid` mediumint(8) unsigned NOT NULL,`username` varchar(15) NOT NULL default '',`sina_uid` bigint(20) unsigned NOT NULL default '0',`sina_username` varchar(30) NOT NULL default '',`token` varchar(32) NOT NULL default '',`remind_in` int(10) unsigned NOT NULL default '0',`expires_in` int(10) unsigned NOT NULL default '0',`thread` tinyint(1) unsigned NOT NULL default '0',`reply` tinyint(1) unsigned NOT NULL default '0',`follow` tinyint(1) unsigned NOT NULL default '0',`blog` tinyint(1) unsigned NOT NULL default '0',`doing` tinyint(1) unsigned NOT NULL default '0',`share` tinyint(1) unsigned NOT NULL default '0',`article` tinyint(1) unsigned NOT NULL default '0',`dateline` int(10) unsigned NOT NULL default '0',`update` int(10) unsigned NOT NULL default '0',PRIMARY KEY  (`uid`)) TYPE=MyISAM;
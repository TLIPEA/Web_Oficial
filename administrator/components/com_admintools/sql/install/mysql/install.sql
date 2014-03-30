CREATE TABLE IF NOT EXISTS `#__admintools_acl` (
	`user_id` bigint(20) unsigned NOT NULL,
	`permissions` mediumtext,
	PRIMARY KEY (`user_id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__admintools_adminiplist` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`ip` varchar(255) DEFAULT NULL,
	`description` varchar(255) DEFAULT NULL,
	UNIQUE KEY `id` (`id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;

CREATE TABLE  IF NOT EXISTS `#__admintools_badwords` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`word` varchar(255) DEFAULT NULL,
	UNIQUE KEY `id` (`id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;

CREATE TABLE  IF NOT EXISTS `#__admintools_customperms` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`path` varchar(255) NOT NULL,
	`perms` varchar(4) DEFAULT '0644',
	UNIQUE KEY `id` (`id`),
	KEY `path` (`path`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__admintools_filescache` (
	`admintools_filescache_id` bigint(20) NOT NULL AUTO_INCREMENT,
	`path` varchar(2048) NOT NULL,
	`filedate` int(11) NOT NULL DEFAULT '0',
	`filesize` int(11) NOT NULL DEFAULT '0',
	`data` blob,
	`checksum` varchar(32) NOT NULL DEFAULT '',
	PRIMARY KEY (`admintools_filescache_id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__admintools_ipautoban` (
	`ip` varchar(255) NOT NULL,
	`reason` varchar(255) DEFAULT 'other',
	`until` datetime DEFAULT NULL,
	UNIQUE KEY `ip` (`ip`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__admintools_ipblock` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`ip` varchar(255) DEFAULT NULL,
	`description` varchar(255) DEFAULT NULL,
	UNIQUE KEY `id` (`id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;

CREATE TABLE  IF NOT EXISTS `#__admintools_log` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`logdate` datetime NOT NULL,
	`ip` varchar(40) DEFAULT NULL,
	`url` varchar(255) DEFAULT NULL,
	`reason` varchar(255) DEFAULT 'other',
	`extradata` mediumtext,
	UNIQUE KEY `id` (`id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__admintools_redirects` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`source` varchar(255) DEFAULT NULL,
	`dest` varchar(255) DEFAULT NULL,
	`ordering` bigint(20) NOT NULL DEFAULT '0',
	`published` tinyint(1) NOT NULL DEFAULT '1',
	`keepurlparams` tinyint(1) NOT NULL DEFAULT '1',
	UNIQUE KEY `id` (`id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__admintools_scanalerts` (
	`admintools_scanalert_id` bigint(20) NOT NULL AUTO_INCREMENT,
	`path` varchar(2048) NOT NULL,
	`scan_id` bigint(20) NOT NULL DEFAULT '0',
	`diff` mediumtext,
	`threat_score` int(11) NOT NULL DEFAULT '0',
	`acknowledged` tinyint(4) NOT NULL DEFAULT '0',
	PRIMARY KEY (`admintools_scanalert_id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__admintools_scans` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`description` varchar(255) NOT NULL,
	`comment` longtext,
	`backupstart` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`backupend` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`status` enum('run','fail','complete') NOT NULL DEFAULT 'run',
	`origin` varchar(30) NOT NULL DEFAULT 'backend',
	`type` varchar(30) NOT NULL DEFAULT 'full',
	`profile_id` bigint(20) NOT NULL DEFAULT '1',
	`archivename` longtext,
	`absolute_path` longtext,
	`multipart` int(11) NOT NULL DEFAULT '0',
	`tag` varchar(255) DEFAULT NULL,
	`filesexist` tinyint(3) NOT NULL DEFAULT '1',
	`remote_filename` varchar(1000) DEFAULT NULL,
	`total_size` bigint(20) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_fullstatus` (`filesexist`,`status`),
	KEY `idx_stale` (`status`,`origin`)
) DEFAULT CHARACTER SET utf8;


CREATE TABLE IF NOT EXISTS `#__admintools_storage` (
	`key` varchar(255) NOT NULL,
	`value` longtext NOT NULL,
	PRIMARY KEY (`key`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__admintools_wafexceptions` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`option` varchar(255) DEFAULT NULL,
	`view` varchar(255) DEFAULT NULL,
	`query` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__admintools_profiles` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`description` varchar(255) NOT NULL,
	`configuration` longtext,
	`filters` longtext,
	PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

INSERT IGNORE INTO `#__admintools_profiles`
(`id`,`description`, `configuration`, `filters`) VALUES
(1,'Default PHP Change Scanner Profile','','');
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
CREATE TABLE IF NOT EXISTS #__rokcandy (
  id int(11) unsigned NOT NULL auto_increment,
  catid int(11) NOT NULL,
  macro text NOT NULL,
  html text NOT NULL,
  published tinyint(1) NOT NULL,
  checked_out int(11) NOT NULL,
  checked_out_time datetime NOT NULL,
  ordering int(11) NOT NULL,
  params text NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
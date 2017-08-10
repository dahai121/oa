DROP TABLE IF EXISTS `phpcms_oacarbook`;
CREATE TABLE IF NOT EXISTS `phpcms_oacarbook` (
  `bid` smallint(5) NOT NULL AUTO_INCREMENT,
  `siteid` smallint(4) NOT NULL DEFAULT 1,
  `carname` varchar(30) NOT NULL,
  `userid` smallint(4) NOT NULL,
  `groupid` smallint(4) NOT NULL,
  `bdate` date NOT NULL DEFAULT '0000-00-00',
  `btime` varchar(10) DEFAULT NULL,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `flag` tinyint(1) NOT NULL DEFAULT 1,
  `comment` varchar(100) DEFAULT ' ',
  PRIMARY KEY (`bid`)
) TYPE=MyISAM;

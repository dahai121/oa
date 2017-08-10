DROP TABLE IF EXISTS `phpcms_oacarinfo`;
CREATE TABLE IF NOT EXISTS `phpcms_oacarinfo` (
  `carid` smallint(4) NOT NULL AUTO_INCREMENT,
  `siteid` smallint(4) NOT NULL,
  `carname` varchar(30) NOT NULL,
  `cartype` varchar(30) DEFAULT NULL,
  `cardate` datetime DEFAULT NULL,
  `comment` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`carid`)
) TYPE=MyISAM;

INSERT INTO `phpcms_oacarinfo` VALUES ('1', '1', '京EM7908', '京EM7908', '2017-05-26 16:16:45', '程小千驾驶');
INSERT INTO `phpcms_oacarinfo` VALUES ('2', '1', '京QM7908', '京QM7908', '2017-06-01 10:25:19', '自驾');

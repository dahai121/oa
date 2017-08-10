DROP TABLE IF EXISTS `phpcms_oattendance`;
CREATE TABLE `phpcms_oattendance` (
  `attid` int(8) NOT NULL AUTO_INCREMENT,
  `siteid` smallint(1) NOT NULL,
  `userid` smallint(4) NOT NULL,
  `groupid` smallint(4) NOT NULL,
  `attype` smallint(1) NOT NULL COMMENT '1:出勤 绿色\r\n2:请假 黄色\r\n3:迟到 红色\r\n4:公差 蓝色\r\n5:公休 蓝色\r\n6:缺勤 黑色',
  `attdate` date NOT NULL,
  `addtime` datetime NOT NULL,
  `flag` smallint(1) DEFAULT NULL,
  `loginIP` varchar(20) DEFAULT NULL,
  `comment` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`attid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=gbk;

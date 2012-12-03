CREATE TABLE `messages` (
  `messageid` int(11) NOT NULL AUTO_INCREMENT,
  `sender` int(11) NOT NULL DEFAULT '0',
  `recipient` int(11) NOT NULL DEFAULT '0',
  `message` varchar(255) NOT NULL DEFAULT '',
  `isread` tinyint(1) NOT NULL DEFAULT '0',
  `created` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`messageid`)
);
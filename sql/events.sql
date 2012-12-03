CREATE TABLE `events` (
  `eventid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `description` varchar(100) NOT NULL DEFAULT '',
  `eventdate` date NOT NULL DEFAULT '0000-00-00',
  `recurring` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eventid`)
);
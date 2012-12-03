CREATE TABLE `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `fullname` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `comment` text,
  `email_msgs` tinyint(1) NOT NULL DEFAULT '0',
  `list_stamp` datetime DEFAULT NULL,
  `initialfamilyid` int(11) DEFAULT NULL,
  PRIMARY KEY (`userid`)
);
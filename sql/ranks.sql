CREATE TABLE `ranks` (
  `ranking` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `rendered` varchar(255) NOT NULL DEFAULT '',
  `rankorder` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ranking`)
);
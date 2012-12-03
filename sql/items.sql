CREATE TABLE `items` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(7,2) DEFAULT NULL,
  `source` varchar(255) NOT NULL DEFAULT '',
  `ranking` int(11) NOT NULL DEFAULT '0',
  `url` varchar(255) DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `comment` text,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `image_filename` varchar(255) DEFAULT NULL,
  `addedByUserId` int(11) DEFAULT NULL,
  `visibleToOwner` int(11) DEFAULT NULL,
  `received` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`itemid`)
);
CREATE TABLE `itemsources` (
  `sourceid` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL DEFAULT '0',
  `source` text NOT NULL,
  `sourceurl` varchar(255) DEFAULT NULL,
  `sourceprice` decimal(10,2) DEFAULT NULL,
  `sourcecomments` text,
  `addedByUserId` int(11) NOT NULL,
  `visibleToOwner` int(11) DEFAULT NULL,
  PRIMARY KEY (`sourceid`)
);
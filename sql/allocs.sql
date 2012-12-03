# Dump of table allocs
# ------------------------------------------------------------

CREATE TABLE `allocs` (
  `itemid` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `bought` tinyint(1) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemid`,`userid`,`bought`)
);
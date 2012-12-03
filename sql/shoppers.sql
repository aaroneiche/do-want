CREATE TABLE `shoppers` (
  `shopper` int(11) NOT NULL DEFAULT '0',
  `mayshopfor` int(11) NOT NULL DEFAULT '0',
  `pending` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shopper`,`mayshopfor`)
);
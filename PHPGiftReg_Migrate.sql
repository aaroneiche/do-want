# Dump of table itemimages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `itemimages`;

CREATE TABLE `itemimages` (
  `imageid` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`imageid`)
);

# Dump of table itemsources
# ------------------------------------------------------------

DROP TABLE IF EXISTS `itemsources`;

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


# Add columns to items
# -------------------------------------------------------------
alter table items add column `addedByUserId` INT(11);
alter table items  add column `received` INT(1) DEFAULT 0;
alter table items  add column `visibleToOwner` INT(1);

# Add Sources table and copy data in.
# -------------------------------------------------------------
insert into `itemsources` (itemid, source, sourceurl, sourceprice)
select itemid, `source`, url, price from items;

# Add Images table and copy data in
# -------------------------------------------------------------
insert into `itemimages` (itemid, filename)
select itemid, image_filename from items where image_filename is not null;
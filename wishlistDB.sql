# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.15)
# Database: wishlist2
# Generation Time: 2012-03-06 07:49:39 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table allocs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `allocs`;

CREATE TABLE `allocs` (
  `itemid` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `bought` tinyint(1) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemid`,`userid`,`bought`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `categoryid` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`categoryid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;

INSERT INTO `categories` (`categoryid`, `category`)
VALUES
	(1,'Books'),
	(2,'Music'),
	(3,'Video Games'),
	(4,'Clothing'),
	(5,'Movies/DVD'),
	(6,'Gift Certificates'),
	(7,'Hobbies'),
	(8,'Household'),
	(9,'Electronics'),
	(10,'Ornaments/Figurines'),
	(11,'Automotive'),
	(12,'Toys'),
	(13,'Jewellery'),
	(14,'Computer'),
	(15,'Games'),
	(16,'Tools');

/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table events
# ------------------------------------------------------------

DROP TABLE IF EXISTS `events`;

CREATE TABLE `events` (
  `eventid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `description` varchar(100) NOT NULL DEFAULT '',
  `eventdate` date NOT NULL DEFAULT '0000-00-00',
  `recurring` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eventid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;

INSERT INTO `events` (`eventid`, `userid`, `description`, `eventdate`, `recurring`)
VALUES
	(1,NULL,'Christmas','2000-12-25',1);

/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table families
# ------------------------------------------------------------

DROP TABLE IF EXISTS `families`;

CREATE TABLE `families` (
  `familyid` int(11) NOT NULL AUTO_INCREMENT,
  `familyname` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`familyid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `families` WRITE;
/*!40000 ALTER TABLE `families` DISABLE KEYS */;

INSERT INTO `families` (`familyid`, `familyname`)
VALUES
	(1,'Jones');

/*!40000 ALTER TABLE `families` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table itemimages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `itemimages`;

CREATE TABLE `itemimages` (
  `imageid` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`imageid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `items`;

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
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;

INSERT INTO `items` (`itemid`, `userid`, `description`, `price`, `source`, `ranking`, `url`, `category`, `comment`, `quantity`, `image_filename`, `addedByUserId`, `visibleToOwner`)
VALUES
	(111,3,'Test Item 1',NULL,'',3,NULL,2,'This is a great thing that I really really really really want.',1,NULL,NULL,NULL),
	(112,4,'Test Item 1',NULL,'',3,NULL,2,'This is a great thing that I really really really really want.',1,NULL,NULL,NULL);

/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;


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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `itemsources` WRITE;
/*!40000 ALTER TABLE `itemsources` DISABLE KEYS */;

INSERT INTO `itemsources` (`sourceid`, `itemid`, `source`, `sourceurl`, `sourceprice`, `sourcecomments`, `addedByUserId`, `visibleToOwner`)
VALUES
	(1,1,'Best Buy\n','http://bestbuy.com',19.95,'This is a really great awesome thing.',1,1);

/*!40000 ALTER TABLE `itemsources` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table memberships
# ------------------------------------------------------------

DROP TABLE IF EXISTS `memberships`;

CREATE TABLE `memberships` (
  `userid` int(11) NOT NULL DEFAULT '0',
  `familyid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`,`familyid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `memberships` WRITE;
/*!40000 ALTER TABLE `memberships` DISABLE KEYS */;

INSERT INTO `memberships` (`userid`, `familyid`)
VALUES
	(3,1),
	(4,1),
	(5,1);

/*!40000 ALTER TABLE `memberships` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `messageid` int(11) NOT NULL AUTO_INCREMENT,
  `sender` int(11) NOT NULL DEFAULT '0',
  `recipient` int(11) NOT NULL DEFAULT '0',
  `message` varchar(255) NOT NULL DEFAULT '',
  `isread` tinyint(1) NOT NULL DEFAULT '0',
  `created` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`messageid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table ranks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ranks`;

CREATE TABLE `ranks` (
  `ranking` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `rendered` varchar(255) NOT NULL DEFAULT '',
  `rankorder` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ranking`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `ranks` WRITE;
/*!40000 ALTER TABLE `ranks` DISABLE KEYS */;

INSERT INTO `ranks` (`ranking`, `title`, `rendered`, `rankorder`)
VALUES
	(1,'1 - Wouldn\'t mind it','<img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_off.gif\" alt=\"\"><img src=\"images/star_off.gif\" alt=\"\"><img src=\"images/star_off.gif\" alt=\"\"><img src=\"images/star_off.gif\" alt=\"\">',1),
	(2,'2 - Would be nice to have','<img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_off.gif\" alt=\"\"><img src=\"images/star_off.gif\" alt=\"\"><img src=\"images/star_off.gif\" alt=\"\">',2),
	(3,'3 - Would make me happy','<img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_off.gif\" alt=\"\"><img src=\"images/star_off.gif\" alt=\"\">',3),
	(4,'4 - I would really, really like this','<img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_off.gif\" alt=\"\">',4),
	(5,'5 - I\'d love to get this','<img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_on.gif\" alt=\"*\"><img src=\"images/star_on.gif\" alt=\"*\">',5);

/*!40000 ALTER TABLE `ranks` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table shoppers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shoppers`;

CREATE TABLE `shoppers` (
  `shopper` int(11) NOT NULL DEFAULT '0',
  `mayshopfor` int(11) NOT NULL DEFAULT '0',
  `pending` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shopper`,`mayshopfor`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `shoppers` WRITE;
/*!40000 ALTER TABLE `shoppers` DISABLE KEYS */;

INSERT INTO `shoppers` (`shopper`, `mayshopfor`, `pending`)
VALUES
	(3,4,0),
	(4,5,0),
	(4,3,0),
	(3,5,0),
	(5,3,0);

/*!40000 ALTER TABLE `shoppers` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`userid`, `username`, `password`, `fullname`, `email`, `approved`, `admin`, `comment`, `email_msgs`, `list_stamp`, `initialfamilyid`)
VALUES
	(3,'admin','5f4dcc3b5aa765d61d8327deb882cf99','User one','email@email.com',1,1,NULL,1,NULL,NULL),
	(4,'newuser','5f4dcc3b5aa765d61d8327deb882cf99','user two','email@email2.com',1,0,NULL,1,NULL,NULL),
	(5,'newuser2','5f4dcc3b5aa765d61d8327deb882cf99','user three','email@aaroneiche.com',1,0,NULL,1,NULL,NULL);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

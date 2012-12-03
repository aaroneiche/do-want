CREATE TABLE `memberships` (
  `userid` int(11) NOT NULL DEFAULT '0',
  `familyid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`,`familyid`)
);
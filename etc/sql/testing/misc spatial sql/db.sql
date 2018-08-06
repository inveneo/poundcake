-- **************************************************************************************
-- Master table
-- **************************************************************************************

DROP TABLE IF EXISTS `sites`;
CREATE TABLE `sites` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `lat` decimal(17,14) DEFAULT NULL,
  `lon` decimal(17,14) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- **************************************************************************************
-- Slave table
-- **************************************************************************************

DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `location` point NOT NULL,
  PRIMARY KEY (`id`),
  SPATIAL INDEX(location)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;


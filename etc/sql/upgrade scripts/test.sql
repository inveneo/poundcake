DROP TABLE IF EXISTS `network_interface_type_switch_types`;
CREATE TABLE `network_interface_type_switch_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `network_interface_type_id` int(10) NOT NULL,
  `switch_type_id` int(10) NOT NULL,
  `number` int(10),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
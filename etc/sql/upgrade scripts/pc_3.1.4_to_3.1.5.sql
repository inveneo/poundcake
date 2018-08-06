DROP TABLE IF EXISTS `build_items_projects`;
CREATE TABLE `build_items_projects` (
  `build_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL,
  PRIMARY KEY (`build_item_id`,`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- make all existing build items for HRBN, which has a project id of 1
INSERT INTO build_items_projects (build_item_id,project_id)
  SELECT build_items.id,1 FROM build_items;
  
alter table network_radios add router_port int(10) after switch_port;
alter table router_types add ports int(10) after id;
update router_types set ports=5; -- 5 seems like as sensible default

alter table network_radios add network_router_id int(10) after network_switch_id;

DROP TABLE IF EXISTS `router_type_network_interface_types`;
CREATE TABLE `router_type_network_interface_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `network_interface_type_id` int(10) NOT NULL,
  `router_type_id` int(10) NOT NULL,
  `number` int(10),
  PRIMARY KEY (`id`,`network_interface_type_id`,`router_type_id`,`number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
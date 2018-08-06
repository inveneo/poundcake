-- alter table radio_types drop watts;
-- alter table switch_types drop watts;
-- alter table router_types drop watts;

alter table sites add elevation int(10) after lon;
alter table sites add elevation_source varchar(255) after elevation;

alter table radio_types add watts decimal(5,2);
alter table switch_types add watts decimal(5,2);
alter table router_types add watts decimal(5,2);
update radio_types set watts=8.5;
update switch_types set watts=15.0 where name like '8-Port%';
update switch_types set watts=16.0 where name like '16-Port%';
update switch_types set watts=17.0 where name like '24-Port%';
update router_types set watts=6.0 where name like '%RB750G%';
update router_types set watts=12.0 where name like '%1100%';

alter table radio_types add value decimal(5,2);
alter table switch_types add value decimal(5,2);
alter table router_types add value decimal(5,2);
update radio_types set value='229.00' where id=1;
update radio_types set value='78.00' where id=2;
update radio_types set value='86.00' where id=3;
update radio_types set value='87.00' where id=4;
update radio_types set value='79.00' where id=5;
update radio_types set value='238.00' where id=6;
update radio_types set value='245.00' where id=7;
update radio_types set value='219.00' where id=8;
update radio_types set value='154.00' where id=9;

update switch_types set value='500.00';
update router_types set value='44.00' where id=2;
update router_types set value='166.00' where id=3;
update router_types set value='489.00' where id=4;

DROP TABLE IF EXISTS `network_interface_types`;
CREATE TABLE `network_interface_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

insert into network_interface_types(name) values ('eth');
insert into network_interface_types(name) values ('ath');
insert into network_interface_types(name) values ('wifi');
insert into network_interface_types(name) values ('br');
insert into network_interface_types(name) values ('vlan');
insert into network_interface_types(name) values ('lo');

alter table ip_spaces drop primary_ip;
alter table ip_spaces add gateway_id int(10);

-- PC-543

alter table network_radios drop ip_space_id;
alter table network_routers drop ip_space_id;
alter table network_switches drop ip_space_id;
-- alter table network_radios add network_interface_ip_space_id int(10) after ip_address;

DROP TABLE IF EXISTS `radio_type_network_interface_types`;
CREATE TABLE `radio_type_network_interface_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `network_interface_type_id` int(10) NOT NULL,
  `radio_type_id` int(10) NOT NULL,
  `number` int(10),
  PRIMARY KEY (`id`,`network_interface_type_id`,`radio_type_id`,`number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `network_interface_ip_spaces`;
CREATE TABLE `network_interface_ip_spaces` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  
  `network_interface_type_id` int(10) NOT NULL,
  `if_number` int(10) NOT NULL,
  `network_radio_id` int(10),
  `network_router_id` int(10),
  `ip_space_id` int(10),
  `if_primary` tinyint(1),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


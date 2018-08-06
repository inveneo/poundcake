alter table projects add secure_password varchar(255) after default_lon;
alter table projects add insecure_password varchar(255) after secure_password;
alter table network_radios add configuration_template_id int(10) after snmp_community_name;

alter table projects add dns1 int(10) unsigned;
alter table projects add dns2 int(10) unsigned;

DROP TABLE IF EXISTS `configuration_templates`;
CREATE TABLE `configuration_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `configuration_templates_projects`;
CREATE TABLE `configuration_templates_projects` (
  `configuration_template_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL,
  PRIMARY KEY (`configuration_template_id`,`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


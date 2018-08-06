alter table projects add snmp_contact varchar(255) after snmp_community_name;
alter table configuration_templates add type TINYINT after name;

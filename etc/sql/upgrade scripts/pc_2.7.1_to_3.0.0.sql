alter table network_radios add p2mp tinyint(1) default 0 after ssid;
alter table network_radios add tmp tinyint(1) default 0; -- temporary column

DROP TRIGGER IF EXISTS tr_network_radio_insert;
DROP TRIGGER IF EXISTS tr_network_radio_update;
DROP TRIGGER IF EXISTS tr_network_radio_delete;
truncate table radios_radios;

update network_radios set ssid='CBRIT_MP1' where id=198; -- missing SSID
update network_radios set p2mp=1,sector=0 where id=198;

update network_radios set p2mp=1,sector=0 where id=263;
update network_radios set p2mp=1,sector=0 where id=264;

-- source /Users/clarkritchie/Desktop/poundcake/etc/sql/poundcake/tr_multipoint-links-SSID-key.sql
source /var/www/towerdb.inveneo.org/poundcake/etc/sql/poundcake/tr_multipoint-links-SSID-key.sql

update network_radios set tmp=1; -- cause the trigger to fire
alter table network_radios drop tmp; -- remove our column

-- select id,name,p2mp from network_radios where name like '%_MP%';
-- select id,name,p2mp,sector,ssid from network_radios where id = 198 or id = 263 or id = 264;

DROP TABLE IF EXISTS `ip_spaces`;

CREATE TABLE ip_spaces (
    id INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_id INTEGER(10) DEFAULT NULL,
    lft INTEGER(10) DEFAULT NULL,
    rght INTEGER(10) DEFAULT NULL,
    name VARCHAR(255) DEFAULT '',
    ip_address int unsigned default 0,
    cidr int,
    project_id INTEGER(10) DEFAULT NULL,
    PRIMARY KEY  (id)
);


DROP TABLE IF EXISTS `ip_addresses`;
CREATE TABLE ip_addresses (
    id INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    ip_address int unsigned default 0,
    project_id INTEGER(10) NOT NULL,
    PRIMARY KEY  (id)
);

alter table network_radios add ip_space_id int(10) after ip_address;
alter table network_switches add ip_space_id int(10) after ip_address;
alter table network_routers add ip_space_id int(10) after ip_address;
alter table users add ip_address int unsigned default 0;

CREATE TABLE site_state_icons LIKE site_states;
desc site_state_icons;
INSERT site_state_icons SELECT * FROM site_states;
alter table site_state_icons drop project_id;
alter table site_state_icons drop name;
alter table site_state_icons drop sequence;

alter table site_states add site_state_icon_id int(10);
alter table site_states drop img_name;
alter table site_states drop img_type;
alter table site_states drop img_size;
alter table site_states drop img_data;

UPDATE site_states SET site_state_icon_id=2 WHERE id=15;
UPDATE site_states SET site_state_icon_id=2 WHERE id=40;
UPDATE site_states SET site_state_icon_id=2 WHERE id=43;
UPDATE site_states SET site_state_icon_id=2 WHERE id=49;
UPDATE site_states SET site_state_icon_id=2 WHERE id=52;
UPDATE site_states SET site_state_icon_id=4 WHERE id=28;
UPDATE site_states SET site_state_icon_id=4 WHERE id=46;
UPDATE site_states SET site_state_icon_id=3 WHERE id=36;
UPDATE site_states SET site_state_icon_id=3 WHERE id=41;
UPDATE site_states SET site_state_icon_id=4 WHERE id=27;
UPDATE site_states SET site_state_icon_id=3 WHERE id=18;
UPDATE site_states SET site_state_icon_id=3 WHERE id=20;
UPDATE site_states SET site_state_icon_id=3 WHERE id=31;
UPDATE site_states SET site_state_icon_id=3 WHERE id=44;
UPDATE site_states SET site_state_icon_id=7 WHERE id=24;
UPDATE site_states SET site_state_icon_id=7 WHERE id=45;
UPDATE site_states SET site_state_icon_id=4 WHERE id=33;
UPDATE site_states SET site_state_icon_id=4 WHERE id=34;
UPDATE site_states SET site_state_icon_id=6 WHERE id=21;
UPDATE site_states SET site_state_icon_id=6 WHERE id=30;
UPDATE site_states SET site_state_icon_id=6 WHERE id=32;
UPDATE site_states SET site_state_icon_id=6 WHERE id=42;
UPDATE site_states SET site_state_icon_id=5 WHERE id=16;
UPDATE site_states SET site_state_icon_id=5 WHERE id=25;
UPDATE site_states SET site_state_icon_id=5 WHERE id=51;
UPDATE site_states SET site_state_icon_id=5 WHERE id=47;
UPDATE site_states SET site_state_icon_id=5 WHERE id=37;
UPDATE site_states SET site_state_icon_id=1 WHERE id=23;
UPDATE site_states SET site_state_icon_id=1 WHERE id=29;
UPDATE site_states SET site_state_icon_id=1 WHERE id=53;
UPDATE site_states SET site_state_icon_id=7 WHERE id=50;
UPDATE site_states SET site_state_icon_id=4 WHERE id=38;
UPDATE site_states SET site_state_icon_id=6 WHERE id=48;
UPDATE site_states SET site_state_icon_id=4 WHERE id=26;


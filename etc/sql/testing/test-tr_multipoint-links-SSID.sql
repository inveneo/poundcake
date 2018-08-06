use poundcake

DROP TABLE IF EXISTS `temp1`;
CREATE TABLE `temp1` (
  `id` int(10)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

source /Users/clarkritchie/Desktop/poundcake/etc/sql/poundcake/tr_multipoint-links-SSID.sql

truncate table radios_radios;
delete from network_radios where name like 'radio%';
insert into network_radios(name,ssid) values ( "radioA","superduper");
insert into network_radios(name,ssid) values ( "radioB","superduper");
insert into network_radios(name,ssid) values ( "radioC","superduper");
-- insert into network_radios(name,ssid) values ( "radioD","superduper");
-- insert into network_radios(name,ssid) values ( "radioE","wowzers");
-- insert into network_radios(name,ssid) values ( "radioF","wowzers");

select id,name from network_radios where ssid="superduper";
select * from radios_radios;

SELECT id into @tID FROM network_radios WHERE name = "radioA";
-- select @tID;
update network_radios set name='radioA-UPDATED' where id=@tID;
select * from radios_radios;
select id,name from network_radios where ssid="superduper";
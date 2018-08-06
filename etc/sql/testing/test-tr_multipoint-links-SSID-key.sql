use poundcake

DROP TABLE IF EXISTS `temp1`;
CREATE TABLE `temp1` (
  `id` int(10),
  `comment` varchar(255)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

source /Users/clarkritchie/Desktop/poundcake/etc/sql/poundcake/tr_multipoint-links-SSID-key.sql

truncate table radios_radios;
delete from network_radios where name like 'radio%';
-- **** INSERT test cases
insert into network_radios(name,ssid,p2mp) values ( "radioA","superduper",0);
insert into network_radios(name,ssid,p2mp) values ( "radioB","superduper",0);
-- insert into network_radios(name,ssid,p2mp) values ( "radioC","superduper",0);
-- insert into network_radios(name,ssid,p2mp) values ( "radioD","superduper",0);
-- insert into network_radios(name,ssid) values ( "radioD","superduper");
-- insert into network_radios(name,ssid) values ( "radioE","wowzers");
-- insert into network_radios(name,ssid) values ( "radioF","wowzers");

select id,name from network_radios where ssid="superduper";
select * from radios_radios;
select * from temp1;

-- **** UPDATE test cases
SELECT id into @tID FROM network_radios WHERE name = "radioB";
select @tID;
update network_radios set name='radioB-UPDATED' where id=@tID;
select id,name from network_radios where ssid="superduper";
select * from radios_radios;
select * from temp1;
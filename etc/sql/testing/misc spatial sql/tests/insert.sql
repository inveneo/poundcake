truncate table items;
truncate table locations;

-- mysql -uroot -proot -v spatialtest < insert.sql

insert into items values (1,'1.001','1.002');
insert into items values (2,NULL,NULL);
insert into items values (3,'','');
insert into items values (4,4.001,'');
insert into items values (5,'',5.002);
insert into items values (6,6.001,'');
insert into items values (7,7.001,'');
insert into items values (8,8.001,8.002);

select * from items;
select id,X(location),Y(location) from locations;
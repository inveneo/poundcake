

-- delete from network_radios;
-- delete from radios_radios;

-- insert into network_radios(name) values ('FOO-BAR');
-- insert into network_radios(name) values ('BAR-FOO');

-- insert into network_radios(name) values ('AA-BB'); -- id 1
-- insert into network_radios(name) values ('BB-MP'); -- id 2
-- insert into network_radios(name) values ('CC-BB'); -- id 3
-- insert into network_radios(name) values ('DD-EE'); -- id 4
-- insert into network_radios(name) values ('EE-DD'); -- id 5

-- Link #1:
-- HILARE-MP1
-- PSOND-HILARE-MP1

-- Link #2:
-- HILARE-MP1
-- LIANC-HILARE-MP1

-- Link #3:
-- HILARE-MP1
-- PRIVA-HILARE-MP1

-- Link #4:
-- HILARE-STMRC
-- STMRC-HILARE

-- Link #5:
-- HILARE-OBLEO
-- OBLEO-HILARE

truncate table network_radios;
truncate table radios_radios;
truncate table temp;
insert into network_radios(name) values ('HILARE_MP'); -- 1
insert into network_radios(name) values ('PSOND-HILARE_MP'); -- 2
insert into network_radios(name) values ('HILARE-STMRC'); -- 3
insert into network_radios(name) values ('STMRC-HILARE'); -- 4
insert into network_radios(name) values ('PRIVA-HILARE_MP'); -- 5
insert into network_radios(name) values ('FOO-BAR'); -- 6
select id,name from network_radios;
select src_radio_id, dest_radio_id from radios_radios;

update network_radios set name = 'LESTR-FOO' where id = 5;
update network_radios set name = 'FOO-LESTR' where id = 3;
update network_radios set name = 'FOO-HILARE_MP' where id = 6;
select id,name from network_radios;
select src_radio_id, dest_radio_id from radios_radios;

delete from network_radios where id = 2;
delete from network_radios where id = 6;
select id,name from network_radios;
select src_radio_id, dest_radio_id from radios_radios;


-- select * from temp;

-- insert into network_radios(name) values ('PRIVA-HILARE-MP1');

-- insert into network_radios(name) values ('HILARE-OBLEO');
-- insert into network_radios(name) values ('OBLEO-HILARE');

-- insert into network_radios(name) values ('LIANC-HILARE-MP1');
